<?php

namespace App\Http\Controllers;

use App\Jobs\GuiEmailThongBaoJob;
use App\Models\{HocPhi, SinhVien, DotThu, ThanhToan};
use App\Services\{HocPhiService, ThanhToanService, ThongBaoService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ThanhToanController — Ghi nhận thanh toán, biên lai (Kế toán)
 * Phân công: Thùy Trang
 */
class ThanhToanController extends Controller
{
    public function __construct(
        private HocPhiService $hocPhiService,
        private ThanhToanService $thanhToanService,
        private ThongBaoService $thongBaoService,
    ) {}

    // ── Form thu tiền ────────────────────────────────────────────
    public function create(Request $request)
    {
        $sinhVien = null;
        $conNo = null;

        if ($request->filled('ma_sv')) {
            $sinhVien = SinhVien::where('ma_sv', $request->ma_sv)->first();

            if ($sinhVien) {
                $conNo = $this->hocPhiService->traXuatCongNoSinhVien($sinhVien);
            }
        }

        $dotThus = DotThu::dangThu()
            ->orderByDesc('created_at')
            ->get();

        return view('thanh-toan.create', compact(
            'sinhVien',
            'conNo',
            'dotThus'
        ));
    }

    // ── Ghi nhận thanh toán ──────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'hoc_phi_id'    => 'required|exists:hoc_phis,id',
            'so_tien'       => 'required|numeric|min:1000',
            'hinh_thuc'     => 'required|in:tien_mat,chuyen_khoan,the_ngan_hang,vi_dien_tu',
            'ngan_hang'     => 'nullable|max:50',
            'so_tham_chieu' => 'nullable|max:100',
            'ghi_chu'       => 'nullable|max:300',
        ]);

        $hocPhi = HocPhi::findOrFail($data['hoc_phi_id']);

        try {

            $ketQua = $this->hocPhiService->ghiNhanThanhToan(
                hocPhi: $hocPhi,
                soTien: (float)$data['so_tien'],
                hinhThuc: $data['hinh_thuc'],
                nguoiThuId: Auth::id(),
                extra: [
                    'ngan_hang'     => $data['ngan_hang'] ?? null,
                    'so_tham_chieu' => $data['so_tham_chieu'] ?? null,
                    'ghi_chu'       => $data['ghi_chu'] ?? null,
                ]
            );

            // Gửi email xác nhận thanh toán
            GuiEmailThongBaoJob::dispatch(
                'xac_nhan_thanh_toan',
                [
                    'thanhToan' => $ketQua['thanh_toan']
                ]
            );

            return redirect()
                ->route(
                    'ketoan.thanh-toan.bien-lai',
                    $ketQua['thanh_toan']->ma_giao_dich
                )
                ->with(
                    'success',
                    "Thu thành công "
                    . number_format($data['so_tien'], 0, ',', '.')
                    . "đ. Còn nợ: "
                    . number_format($ketQua['con_no_sau'], 0, ',', '.')
                    . "đ"
                );

        } catch (\InvalidArgumentException $e) {

            return back()
                ->withErrors([
                    'so_tien' => $e->getMessage()
                ]);

        } catch (\OverflowException $e) {

            return back()
                ->withErrors([
                    'so_tien' => $e->getMessage()
                ]);
        }
    }

    // ── Biên lai thanh toán ──────────────────────────────────────
    public function bienLai(string $maGiaoDich)
    {
        $data = $this->thanhToanService->layDuLieuBienLai($maGiaoDich);

        return view('thanh-toan.bien-lai', $data);
    }

    // ── Lịch sử giao dịch theo SV ───────────────────────────────
    public function lichSuSinhVien(SinhVien $sinhVien)
    {
        $lichSu = $this->thanhToanService
            ->lichSuTheoSinhVien($sinhVien);

        return view(
            'thanh-toan.lich-su',
            compact('sinhVien', 'lichSu')
        );
    }

    // ── Danh sách công nợ đợt ───────────────────────────────────
    public function congNoDotThu(DotThu $dotThu)
    {
        $congNo = $this->hocPhiService
            ->traXuatCongNoDotThu($dotThu);

        return view(
            'thanh-toan.cong-no',
            compact('dotThu', 'congNo')
        );
    }

    // ── Hủy giao dịch (Admin only) ──────────────────────────────
    public function huyGiaoDich(
        Request $request,
        ThanhToan $thanhToan
    ) {
        $request->validate([
            'ly_do' => 'required|max:200'
        ]);

        $this->thanhToanService
            ->huyGiaoDich(
                $thanhToan,
                $request->ly_do
            );

        return back()->with(
            'success',
            "Đã hủy giao dịch {$thanhToan->ma_giao_dich}."
        );
    }
}
