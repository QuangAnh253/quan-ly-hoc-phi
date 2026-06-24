<?php

namespace App\Http\Controllers;

use App\Models\{SinhVien, Khoa};
use App\Services\HocPhiService;
use Illuminate\Http\Request;

/**
 * SinhVienController — CRUD sinh viên (Admin / Kế toán)
 * Phân công: Lộc
 */
class SinhVienController extends Controller
{
    public function __construct(private HocPhiService $hocPhiService) {}

    // ── Danh sách ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = SinhVien::with('khoa')->active();

        if ($request->filled('khoa_id')) {
            $query->where('khoa_id', $request->khoa_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ma_sv',  'like', "%{$request->search}%")
                  ->orWhere('ho_ten','like', "%{$request->search}%");
            });
        }
        if ($request->filled('con_no')) {
            $query->conNo();
        }

        $sinhViens = $query->orderBy('ma_sv')->paginate(20)->withQueryString();
        $khoas     = Khoa::active()->orderBy('ten_khoa')->get();

        return view('sinh-vien.index', compact('sinhViens', 'khoas'));
    }

    // ── Chi tiết + lịch sử học phí ──────────────────────────────
    public function show(SinhVien $sinhVien)
    {
        $sinhVien->load(['khoa', 'hocPhis.dotThu', 'hocPhis.thanhToans', 'mienGiams']);
        $conNo = $this->hocPhiService->traXuatCongNoSinhVien($sinhVien);
        return view('sinh-vien.show', compact('sinhVien', 'conNo'));
    }

    // ── Tạo mới ──────────────────────────────────────────────────
    public function create()
    {
        $khoas = Khoa::active()->orderBy('ten_khoa')->get();
        return view('sinh-vien.create', compact('khoas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'khoa_id'       => 'required|exists:khoas,id',
            'ma_sv'         => 'required|unique:sinh_viens|max:20',
            'ho_ten'        => 'required|max:100',
            'ngay_sinh'     => 'nullable|date',
            'gioi_tinh'     => 'required|in:nam,nu,khac',
            'lop'           => 'required|max:20',
            'nien_khoa'     => 'required|digits:4',
            'he_dao_tao'    => 'required|in:chinh_quy,lien_thong,tu_xa',
            'dien_mien_giam'=> 'required',
            'so_dien_thoai' => 'nullable|max:15',
            'email'         => 'nullable|email|max:100',
            'dia_chi'       => 'nullable|max:500',
        ]);

        SinhVien::create($data);

        return redirect()->route('sinh-vien.index')
                         ->with('success', "Đã thêm sinh viên {$data['ho_ten']} thành công.");
    }

    // ── Cập nhật ─────────────────────────────────────────────────
    public function edit(SinhVien $sinhVien)
    {
        $khoas = Khoa::active()->orderBy('ten_khoa')->get();
        return view('sinh-vien.edit', compact('sinhVien', 'khoas'));
    }

    public function update(Request $request, SinhVien $sinhVien)
    {
        $data = $request->validate([
            'khoa_id'       => 'required|exists:khoas,id',
            'ho_ten'        => 'required|max:100',
            'ngay_sinh'     => 'nullable|date',
            'gioi_tinh'     => 'required|in:nam,nu,khac',
            'lop'           => 'required|max:20',
            'dien_mien_giam'=> 'required',
            'so_dien_thoai' => 'nullable|max:15',
            'email'         => 'nullable|email|max:100',
            'dia_chi'       => 'nullable|max:500',
        ]);

        $sinhVien->update($data);

        return redirect()->route('sinh-vien.show', $sinhVien)
                         ->with('success', 'Đã cập nhật thông tin sinh viên.');
    }

    // ── Xóa mềm ──────────────────────────────────────────────────
    public function destroy(SinhVien $sinhVien)
    {
        $sinhVien->update(['active' => false]);
        return redirect()->route('sinh-vien.index')
                         ->with('success', "Đã vô hiệu hóa sinh viên {$sinhVien->ho_ten}.");
    }
}
