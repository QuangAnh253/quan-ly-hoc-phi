<?php

namespace App\Http\Controllers;

use App\Models\{DotThu, SinhVien};
use App\Services\{HocPhiService, BaoCaoService};
use App\Jobs\GuiEmailThongBaoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DotThuController — Quản lý đợt thu học phí (Admin)
 * Phân công: Duy Thành
 */
class DotThuController extends Controller
{
    public function __construct(
        private HocPhiService $hocPhiService,
        private BaoCaoService $baoCaoService,
    ) {}

    public function index()
    {
        $dotThus = DotThu::with('createdBy')
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('dot-thu.index', compact('dotThus'));
    }

    public function create()
    {
        return view('dot-thu.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ten_dot'         => 'required|max:100',
            'hoc_ky'          => 'required|in:1,2',
            'nam_hoc'         => 'required|regex:/^\d{4}-\d{4}$/',
            'don_gia_tin_chi' => 'required|numeric|min:1000',
            'ngay_bat_dau'    => 'required|date',
            'han_dong'        => 'required|date|after:ngay_bat_dau',
            'phi_phat_ngay'   => 'nullable|numeric|min:0',
            'ghi_chu'         => 'nullable|max:500',
        ]);

        $data['created_by'] = Auth::id();
        $data['trang_thai'] = 'sap_mo';
        $data['phi_phat_ngay'] = $data['phi_phat_ngay'] ?? 0;

        // Observer sẽ tự kích hoạt sau khi DotThu::create()
        $dotThu = DotThu::create($data);

        return redirect()->route('admin.dot-thu.show', $dotThu)
                         ->with('success', "Đã tạo đợt thu \"{$dotThu->ten_dot}\" thành công.");
    }

    public function show(DotThu $dotThu)
    {
        $baoCao = $this->baoCaoService->baoCaoTongHopDotThu($dotThu);
        $dotThu->load('createdBy');
        return view('dot-thu.show', compact('dotThu', 'baoCao'));
    }

    // ── Mở đợt thu — trigger Observer gửi email toàn bộ SV ──────
    public function moDot(DotThu $dotThu)
    {
        abort_if($dotThu->trang_thai !== 'sap_mo', 400, 'Đợt thu không ở trạng thái sắp mở.');

        // Tính học phí cho tất cả SV (cần có soTinChiMap từ file import)
        // Ở đây dùng default 18 TC để demo — thực tế lấy từ bảng đăng ký môn học
        $sinhViens   = SinhVien::active()->get();
        $soTinChiMap = $sinhViens->pluck('id')->mapWithKeys(fn($id) => [$id => 18])->toArray();

        $ketQua = $this->hocPhiService->tinhHocPhiCaLop($dotThu, $soTinChiMap);

        // Đổi trạng thái → Observer tự gửi thông báo email
        $dotThu->update([
            'trang_thai' => 'dang_thu'
        ]);

        foreach ($dotThu->hocPhis as $hocPhi)
        {
            GuiEmailThongBaoJob::dispatch(
                'mo_dot',
                [
                    'dotThu'=>$dotThu,
                    'sinhVien'=>$hocPhi->sinhVien,
                    'soTienPhaiDong'=>$hocPhi->tong_phai_dong
                ]
            );
        }

        return redirect()->route('admin.dot-thu.show', $dotThu)
                         ->with('success', "Đã mở đợt thu. Tính HP cho {$ketQua['thanhCong']} SV. Thông báo email đã được gửi.");
    }

    // ── Đóng đợt thu ─────────────────────────────────────────────
    public function dongDot(DotThu $dotThu)
    {
        abort_if($dotThu->trang_thai !== 'dang_thu', 400, 'Đợt thu chưa mở.');
        $dotThu->update(['trang_thai' => 'da_dong']);

        return redirect()->route('admin.dot-thu.show', $dotThu)
                         ->with('success', 'Đã đóng đợt thu.');
    }

    // ── Gửi nhắc nợ cho SV còn chưa đóng đủ ────────────────────
    public function guiNhacNo(DotThu $dotThu)
    {
        $dotThu->load('hocPhis.sinhVien');

        $count = 0;
        foreach ($dotThu->hocPhis as $hocPhi) {
            $conNo = $hocPhi->tong_phai_dong + $hocPhi->phi_phat - $hocPhi->da_dong;
            if ($conNo <= 0) continue;

            $sv = $hocPhi->sinhVien;
            if (!$sv?->email) continue;

            GuiEmailThongBaoJob::dispatch('nhac_no', [
                'dotThu'      => $dotThu,
                'sinhVien'    => $sv,
                'soTienConNo' => $conNo,
            ])->onQueue('emails');

            $count++;
        }

        return back()->with('success', "Đã gửi nhắc nợ đến {$count} sinh viên.");
    }

    // ── Báo cáo tổng hợp đợt ────────────────────────────────────
    public function baoCao(DotThu $dotThu)
    {
        $baoCao    = $this->baoCaoService->baoCaoTongHopDotThu($dotThu);
        $quaHan    = $this->baoCaoService->danhSachQuaHan($dotThu);
        return view('dot-thu.bao-cao', compact('dotThu', 'baoCao', 'quaHan'));
    }
}
