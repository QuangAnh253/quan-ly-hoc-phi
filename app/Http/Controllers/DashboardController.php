<?php

namespace App\Http\Controllers;

use App\Services\{HocPhiService, ThanhToanService};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private HocPhiService    $hocPhiService,
        private ThanhToanService $thanhToanService,
    ) {}

    // ── Admin ────────────────────────────────────────────────────
    public function adminDashboard()
    {
        $thongKe = $this->hocPhiService->thongKeDashboard();
        return view('dashboard.admin', compact('thongKe'));
    }

    // ── Kế toán ──────────────────────────────────────────────────
    public function ketoanDashboard()
    {
        $thongKe = $this->hocPhiService->thongKeDashboard();
        return view('dashboard.ketoan', compact('thongKe'));
    }

    // ── Sinh viên — FIX: load khoa + lịch sử GD ─────────────────
    public function sinhVienDashboard()
    {
        $sv    = Auth::user()->sinhVien->load('khoa');   // ← thêm load('khoa')
        $conNo = $this->hocPhiService->traXuatCongNoSinhVien($sv);

        // ← thêm lịch sử 6 GD gần nhất cho sidebar
        $lichSu = $this->thanhToanService->lichSuTheoSinhVien($sv)->take(6);

        return view('dashboard.sinhvien', compact('sv', 'conNo', 'lichSu'));
    }
}
