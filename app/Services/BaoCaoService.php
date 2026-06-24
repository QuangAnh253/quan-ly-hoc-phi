<?php

namespace App\Services;

use App\Models\{DotThu, HocPhi, ThanhToan, SinhVien, Khoa};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * BaoCaoService — Tổng hợp số liệu báo cáo
 * Dùng cho trang báo cáo của Admin / Kế toán.
 * Xuất Excel dùng PhpSpreadsheet (Thùy Trang implement tiếp).
 */
class BaoCaoService
{
    // ── 1. Báo cáo tổng hợp theo đợt thu ────────────────────────

    /**
     * Số liệu tổng hợp một đợt: tổng thu, tỷ lệ, breakdown theo khoa.
     */
    public function baoCaoTongHopDotThu(DotThu $dotThu): array
    {
        // Tổng quan đợt
        $tongQuan = HocPhi::where('dot_thu_id', $dotThu->id)
            ->selectRaw('
                COUNT(*)                                    AS tong_sv,
                SUM(tong_phai_dong)                         AS tong_phai_thu,
                SUM(da_dong)                                AS tong_da_thu,
                SUM(tong_phai_dong - da_dong + phi_phat)    AS tong_con_no,
                SUM(CASE WHEN trang_thai = "da_dong_du"     THEN 1 ELSE 0 END) AS sv_da_dong,
                SUM(CASE WHEN trang_thai = "dong_mot_phan"  THEN 1 ELSE 0 END) AS sv_dong_mot_phan,
                SUM(CASE WHEN trang_thai = "chua_dong"      THEN 1 ELSE 0 END) AS sv_chua_dong,
                SUM(CASE WHEN trang_thai = "mien_hoan_toan" THEN 1 ELSE 0 END) AS sv_mien
            ')
            ->first();

        // Breakdown theo khoa
        $theoKhoa = HocPhi::where('dot_thu_id', $dotThu->id)
            ->join('sinh_viens', 'hoc_phis.sinh_vien_id', '=', 'sinh_viens.id')
            ->join('khoas', 'sinh_viens.khoa_id', '=', 'khoas.id')
            ->selectRaw('
                khoas.ten_khoa,
                khoas.ma_khoa,
                COUNT(*)            AS tong_sv,
                SUM(tong_phai_dong) AS phai_thu,
                SUM(da_dong)        AS da_thu,
                SUM(tong_phai_dong - da_dong) AS con_no
            ')
            ->groupBy('khoas.id', 'khoas.ten_khoa', 'khoas.ma_khoa')
            ->orderByDesc('phai_thu')
            ->get();

        // Tính tỷ lệ cho từng khoa
        $theoKhoa->transform(function ($row) {
            $row->ty_le_thu = $row->phai_thu > 0
                ? round($row->da_thu / $row->phai_thu * 100, 1)
                : 0;
            return $row;
        });

        return [
            'dot_thu'   => $dotThu,
            'tong_quan' => $tongQuan,
            'theo_khoa' => $theoKhoa,
            'ty_le_tong' => $tongQuan->tong_phai_thu > 0
                ? round($tongQuan->tong_da_thu / $tongQuan->tong_phai_thu * 100, 1)
                : 0,
        ];
    }

    // ── 2. Danh sách SV quá hạn chưa đóng ───────────────────────

    /**
     * Danh sách SV quá hạn — dùng cho trang nhắc nợ / xuất file gửi khoa.
     */
    public function danhSachQuaHan(DotThu $dotThu): Collection
    {
        return HocPhi::with(['sinhVien.khoa'])
            ->where('dot_thu_id', $dotThu->id)
            ->whereIn('trang_thai', ['chua_dong', 'dong_mot_phan'])
            ->orderByDesc(DB::raw('tong_phai_dong - da_dong'))
            ->get()
            ->map(function ($hp) use ($dotThu) {
                return [
                    'ma_sv'       => $hp->sinhVien->ma_sv,
                    'ho_ten'      => $hp->sinhVien->ho_ten,
                    'khoa'        => $hp->sinhVien->khoa->ten_khoa,
                    'lop'         => $hp->sinhVien->lop,
                    'phai_dong'   => $hp->tong_phai_dong,
                    'da_dong'     => $hp->da_dong,
                    'con_no'      => $hp->tong_phai_dong - $hp->da_dong,
                    'phi_phat'    => $hp->phi_phat,
                    'so_ngay_qua_han' => $dotThu->so_ngay_qua_han,
                    'email'       => $hp->sinhVien->email,
                ];
            });
    }

    // ── 3. Báo cáo theo năm học ──────────────────────────────────

    /**
     * Tổng thu học phí theo từng đợt trong năm học — dùng vẽ biểu đồ.
     */
    public function baoCaoNamHoc(string $namHoc): Collection
    {
        return DotThu::where('nam_hoc', $namHoc)
            ->withSum('hocPhis as tong_phai_thu', 'tong_phai_dong')
            ->withSum('hocPhis as tong_da_thu',   'da_dong')
            ->orderBy('hoc_ky')
            ->get()
            ->map(function ($dot) {
                $dot->ty_le = $dot->tong_phai_thu > 0
                    ? round($dot->tong_da_thu / $dot->tong_phai_thu * 100, 1)
                    : 0;
                return $dot;
            });
    }
}
