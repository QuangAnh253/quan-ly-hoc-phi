<?php

namespace App\Http\Controllers;

use App\Models\DotThu;
use App\Services\BaoCaoService;
use Illuminate\Http\Request;

/**
 * BaoCaoController — Báo cáo thống kê (Admin / Kế toán)
 * Phân công: Thùy Trang
 */
class BaoCaoController extends Controller
{
    public function __construct(private BaoCaoService $baoCaoService) {}

    // ── Báo cáo tổng hợp một đợt ────────────────────────────────
    public function dotThu(DotThu $dotThu)
    {
        $baoCao = $this->baoCaoService->baoCaoTongHopDotThu($dotThu);
        $quaHan = $this->baoCaoService->danhSachQuaHan($dotThu);
        return view('bao-cao.dot-thu', compact('dotThu', 'baoCao', 'quaHan'));
    }

    // ── Báo cáo năm học ─────────────────────────────────────────
    public function namHoc(Request $request)
    {
        $namHoc  = $request->get('nam_hoc', date('Y') . '-' . (date('Y') + 1));
        $baoCao  = $this->baoCaoService->baoCaoNamHoc($namHoc);
        $dotThus = DotThu::select('nam_hoc')->distinct()->pluck('nam_hoc');
        return view('bao-cao.nam-hoc', compact('namHoc', 'baoCao', 'dotThus'));
    }

    // ── Xuất Excel danh sách công nợ ────────────────────────────
    public function xuatExcel(DotThu $dotThu)
    {
        $quaHan = $this->baoCaoService->danhSachQuaHan($dotThu);

        // Headers CSV đơn giản — nếu cần Excel đẹp dùng PhpSpreadsheet
        $filename = "cong-no-{$dotThu->id}-" . now()->format('Ymd') . ".csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($quaHan) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 để Excel hiển thị tiếng Việt đúng
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Mã SV','Họ tên','Khoa','Lớp','Phải đóng','Đã đóng','Còn nợ','Phí phạt','Ngày quá hạn','Email']);
            foreach ($quaHan as $row) {
                fputcsv($handle, [
                    $row['ma_sv'], $row['ho_ten'], $row['khoa'], $row['lop'],
                    $row['phai_dong'], $row['da_dong'], $row['con_no'],
                    $row['phi_phat'], $row['so_ngay_qua_han'], $row['email'],
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
