<?php

namespace App\Services;

use App\Factories\MienGiamFactory;
use App\Models\{DotThu, HocPhi, MienGiam, SinhVien, ThanhToan};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║  FACADE PATTERN — HocPhiService                             ║
 * ║                                                              ║
 * ║  Cung cấp một giao diện đơn giản duy nhất cho toàn bộ       ║
 * ║  nghiệp vụ học phí. Controllers chỉ cần biết class này —    ║
 * ║  không cần biết Strategy, Factory, Repository bên trong.    ║
 * ║                                                              ║
 * ║  Các phương thức public (giao diện Facade):                  ║
 * ║    tinhHocPhiChoSinhVien()   — tính HP 1 SV trong 1 đợt      ║
 * ║    tinhHocPhiCaLop()         — tính HP hàng loạt cả đợt      ║
 * ║    ghiNhanThanhToan()        — thu tiền, cập nhật công nợ    ║
 * ║    traXuatCongNo()           — công nợ theo SV / đợt         ║
 * ║    tinhPhiPhat()             — tính phí phạt quá hạn         ║
 * ╚══════════════════════════════════════════════════════════════╝
 */
class HocPhiService
{
    // ═══════════════════════════════════════════════════════════
    // 1. TÍNH HỌC PHÍ
    // ═══════════════════════════════════════════════════════════

    /**
     * Tính và lưu học phí cho MỘT sinh viên trong một đợt thu.
     *
     * Luồng xử lý:
     *   SinhVien + DotThu
     *     → lấy MienGiam hiện tại
     *     → MienGiamFactory::create() → chọn đúng Strategy
     *     → strategy->tinh()          → ra kết quả tài chính
     *     → upsert vào bảng hoc_phis
     *
     * @throws \RuntimeException Nếu SV hoặc đợt thu không hợp lệ
     */
    public function tinhHocPhiChoSinhVien(
        SinhVien $sinhVien,
        DotThu   $dotThu,
        int      $soTinChi
    ): HocPhi {
        // Lấy diện miễn giảm đang áp dụng (nếu có)
        $mienGiam = $sinhVien->mienGiams()
            ->where('nam_ap_dung', now()->year)
            ->where('active', true)
            ->first();

        // Factory chọn đúng Strategy dựa vào diện
        $strategy = MienGiamFactory::create(
            $sinhVien->dien_mien_giam,
            $mienGiam
        );

        // Strategy tính toán
        $ketQua = $strategy->tinh($soTinChi, $dotThu->don_gia_tin_chi);

        // Upsert — nếu đã có thì cập nhật, chưa có thì tạo mới
        $hocPhi = HocPhi::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVien->id,
                'dot_thu_id'   => $dotThu->id,
            ],
            [
                'so_tin_chi'      => $soTinChi,
                'don_gia_tin_chi' => $dotThu->don_gia_tin_chi,
                'phan_tram_giam'  => $ketQua['phan_tram_giam'],
                'so_tien_giam'    => $ketQua['so_tien_giam'],
                'tong_phai_dong'  => $ketQua['tong_phai_dong'],
                'trang_thai'      => $ketQua['tong_phai_dong'] == 0
                    ? 'mien_hoan_toan'
                    : 'chua_dong',
            ]
        );

        Log::info("Tính HP: SV#{$sinhVien->ma_sv} | Đợt#{$dotThu->id} | Diện:{$strategy->getTenDien()} | Phải đóng:" . number_format($ketQua['tong_phai_dong']));

        return $hocPhi;
    }

    /**
     * Tính học phí HÀNG LOẠT cho toàn bộ sinh viên trong một đợt thu.
     * Dùng transaction để đảm bảo toàn vẹn dữ liệu.
     *
     * @param  DotThu                   $dotThu
     * @param  array<int, int>          $soTinChiMap  [sinh_vien_id => so_tin_chi]
     * @return array{thanh_cong: int, that_bai: int, chi_tiet: array}
     */
    public function tinhHocPhiCaLop(DotThu $dotThu, array $soTinChiMap): array
    {
        $thanhCong = 0;
        $thatBai   = 0;
        $chiTiet   = [];

        DB::transaction(function () use ($dotThu, $soTinChiMap, &$thanhCong, &$thatBai, &$chiTiet) {
            $sinhViens = SinhVien::whereIn('id', array_keys($soTinChiMap))
                ->with(['mienGiams' => fn($q) => $q->where('active', true)->where('nam_ap_dung', now()->year)])
                ->get();

            foreach ($sinhViens as $sv) {
                try {
                    $soTinChi = $soTinChiMap[$sv->id] ?? 0;
                    if ($soTinChi <= 0) continue;

                    $hocPhi = $this->tinhHocPhiChoSinhVien($sv, $dotThu, $soTinChi);

                    $chiTiet[] = [
                        'ma_sv'        => $sv->ma_sv,
                        'ho_ten'       => $sv->ho_ten,
                        'so_tin_chi'   => $soTinChi,
                        'tong_phai_dong' => $hocPhi->tong_phai_dong,
                        'trang_thai'   => $hocPhi->trang_thai,
                    ];
                    $thanhCong++;
                } catch (\Throwable $e) {
                    $thatBai++;
                    Log::error("Lỗi tính HP SV#{$sv->id}: " . $e->getMessage());
                }
            }
        });

        return compact('thanhCong', 'thatBai', 'chiTiet');
    }

    // ═══════════════════════════════════════════════════════════
    // 2. GHI NHẬN THANH TOÁN
    // ═══════════════════════════════════════════════════════════

    /**
     * Ghi nhận một giao dịch thanh toán học phí.
     *
     * Luồng:
     *   Validate số tiền → tạo ThanhToan → cộng vào hoc_phi.da_dong
     *   → capNhatTrangThai() → trả về ThanhToan + HocPhi đã cập nhật
     *
     * @param  HocPhi $hocPhi
     * @param  float  $soTien
     * @param  string $hinhThuc   'tien_mat' | 'chuyen_khoan' | 'the_ngan_hang' | 'vi_dien_tu'
     * @param  int    $nguoiThuId user_id của kế toán đang thu
     * @param  array  $extra      Các trường tuỳ chọn: ngan_hang, so_tham_chieu, ghi_chu
     * @return array{thanh_toan: ThanhToan, hoc_phi: HocPhi, con_no_sau: float}
     *
     * @throws \InvalidArgumentException Nếu số tiền không hợp lệ
     * @throws \OverflowException        Nếu thanh toán vượt quá công nợ
     */
    public function ghiNhanThanhToan(
        HocPhi $hocPhi,
        float  $soTien,
        string $hinhThuc,
        int    $nguoiThuId,
        array  $extra = []
    ): array {
        // Validate
        if ($soTien <= 0) {
            throw new \InvalidArgumentException('Số tiền thanh toán phải lớn hơn 0.');
        }

        $conNo = $hocPhi->tong_phai_dong - $hocPhi->da_dong + $hocPhi->phi_phat;

        if ($soTien > $conNo + 1) { // +1 để tránh lỗi làm tròn float
            throw new \OverflowException(
                "Số tiền ({$soTien}) vượt quá công nợ hiện tại (" . number_format($conNo) . "đ)."
            );
        }

        return DB::transaction(function () use ($hocPhi, $soTien, $hinhThuc, $nguoiThuId, $extra): array {
            // Tạo giao dịch
            $thanhToan = ThanhToan::create([
                'hoc_phi_id'     => $hocPhi->id,
                'nguoi_thu_id'   => $nguoiThuId,
                'ma_giao_dich'   => ThanhToan::generateMaGiaoDich(),
                'so_tien'        => $soTien,
                'hinh_thuc'      => $hinhThuc,
                'ngan_hang'      => $extra['ngan_hang']      ?? null,
                'so_tham_chieu'  => $extra['so_tham_chieu']  ?? null,
                'ghi_chu'        => $extra['ghi_chu']        ?? null,
                'thoi_gian_thu'  => now(),
            ]);

            // Cập nhật số tiền đã đóng
            $hocPhi->increment('da_dong', $soTien);
            $hocPhi->refresh();

            // Tự động cập nhật trạng thái (chua_dong → dong_mot_phan → da_dong_du)
            $hocPhi->capNhatTrangThai();

            $conNoSau = max(0, $hocPhi->tong_phai_dong - $hocPhi->da_dong + $hocPhi->phi_phat);

            Log::info("Thu HP: Mã GD {$thanhToan->ma_giao_dich} | {$soTien}đ | Còn nợ: {$conNoSau}đ");

            return [
                'thanh_toan' => $thanhToan,
                'hoc_phi'    => $hocPhi,
                'con_no_sau' => $conNoSau,
            ];
        });
    }

    // ═══════════════════════════════════════════════════════════
    // 3. TRA CỨU CÔNG NỢ
    // ═══════════════════════════════════════════════════════════

    /**
     * Công nợ của MỘT sinh viên — toàn bộ các đợt chưa đóng đủ.
     *
     * @return array{
     *     sinh_vien: SinhVien,
     *     tong_con_no: float,
     *     chi_tiet: Collection<HocPhi>
     * }
     */
    public function traXuatCongNoSinhVien(SinhVien $sinhVien): array
    {
        $hocPhis = HocPhi::with('dotThu')
            ->where('sinh_vien_id', $sinhVien->id)
            ->whereIn('trang_thai', ['chua_dong', 'dong_mot_phan'])
            ->orderBy('created_at', 'desc')
            ->get();

        $tongConNo = $hocPhis->sum(fn($hp) => $hp->tong_phai_dong - $hp->da_dong + $hp->phi_phat);

        return [
            'sinh_vien'  => $sinhVien,
            'tong_con_no' => max(0, $tongConNo),
            'chi_tiet'   => $hocPhis,
        ];
    }

    /**
     * Danh sách công nợ của cả một đợt thu (dùng cho trang báo cáo kế toán).
     *
     * @return array{
     *     dot_thu: DotThu,
     *     tong_phai_thu: float,
     *     tong_da_thu: float,
     *     tong_con_no: float,
     *     ty_le_thu: float,
     *     danh_sach_con_no: Collection
     * }
     */
    public function traXuatCongNoDotThu(DotThu $dotThu): array
    {
        $hocPhis = HocPhi::with(['sinhVien.khoa', 'thanhToans'])
            ->where('dot_thu_id', $dotThu->id)
            ->get();

        $tongPhaiThu = $hocPhis->sum('tong_phai_dong');
        $tongDaThu   = $hocPhis->sum('da_dong');
        $tongConNo   = $hocPhis->sum(fn($hp) => max(0, $hp->tong_phai_dong - $hp->da_dong + $hp->phi_phat));

        // Chỉ lấy những SV còn nợ
        $danhSachConNo = $hocPhis
            ->filter(fn($hp) => in_array($hp->trang_thai, ['chua_dong', 'dong_mot_phan']))
            ->sortByDesc(fn($hp) => $hp->tong_phai_dong - $hp->da_dong)
            ->values();

        return [
            'dot_thu'          => $dotThu,
            'tong_phai_thu'    => $tongPhaiThu,
            'tong_da_thu'      => $tongDaThu,
            'tong_con_no'      => $tongConNo,
            'ty_le_thu'        => $tongPhaiThu > 0
                ? round($tongDaThu / $tongPhaiThu * 100, 2)
                : 0,
            'danh_sach_con_no' => $danhSachConNo,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // 4. PHÍ PHẠT QUÁ HẠN
    // ═══════════════════════════════════════════════════════════

    /**
     * Tính và cập nhật phí phạt quá hạn cho tất cả HocPhi của một đợt thu.
     * Nên chạy bằng Scheduler (daily): php artisan schedule:run
     *
     * Công thức: phi_phat += so_ngay_qua_han × dot_thu.phi_phat_ngay
     *
     * @return array{cap_nhat: int, tong_phi_phat: float}
     */
    public function tinhPhiPhatQuaHan(DotThu $dotThu): array
    {
        if (!$dotThu->isQuaHan()) {
            return ['cap_nhat' => 0, 'tong_phi_phat' => 0];
        }

        $soNgayQuaHan = $dotThu->so_ngay_qua_han;

        $hocPhisChuaDong = HocPhi::where('dot_thu_id', $dotThu->id)
            ->whereIn('trang_thai', ['chua_dong', 'dong_mot_phan'])
            ->get();

        $tongPhiPhat = 0;
        $capNhat     = 0;

        DB::transaction(function () use ($hocPhisChuaDong, $dotThu, $soNgayQuaHan, &$tongPhiPhat, &$capNhat) {
            foreach ($hocPhisChuaDong as $hp) {
                $conNo      = $hp->tong_phai_dong - $hp->da_dong;
                $phiPhatMoi = $conNo * ($dotThu->phi_phat_ngay / 100) * $soNgayQuaHan;
                $phiPhatMoi = max(0, $phiPhatMoi);

                $hp->update(['phi_phat' => $phiPhatMoi]);
                $tongPhiPhat += $phiPhatMoi;
                $capNhat++;
            }
        });

        Log::info("Cập nhật phí phạt đợt#{$dotThu->id}: {$capNhat} SV | Tổng phí phạt: " . number_format($tongPhiPhat) . "đ");

        return ['cap_nhat' => $capNhat, 'tong_phi_phat' => $tongPhiPhat];
    }

    // ═══════════════════════════════════════════════════════════
    // 5. THỐNG KÊ NHANH (dùng cho Dashboard)
    // ═══════════════════════════════════════════════════════════

    /**
     * Số liệu tổng hợp cho widget dashboard admin / kế toán.
     *
     * @return array{
     *     tong_sv: int,
     *     tong_sv_con_no: int,
     *     tong_con_no: float,
     *     tong_thu_thang_nay: float,
     *     dot_thu_dang_mo: int
     * }
     */
    public function thongKeDashboard(): array
    {
        return [
            'tong_sv'           => SinhVien::where('active', true)->count(),

            'tong_sv_con_no'    => HocPhi::whereIn('trang_thai', ['chua_dong', 'dong_mot_phan'])
                ->distinct('sinh_vien_id')
                ->count('sinh_vien_id'),

            'tong_con_no'       => HocPhi::whereIn('trang_thai', ['chua_dong', 'dong_mot_phan'])
                ->selectRaw('SUM(tong_phai_dong - da_dong + phi_phat) as tong')
                ->value('tong') ?? 0,

            'tong_thu_thang_nay' => ThanhToan::whereMonth('created_at', now()->month)
                ->whereYear('created_at',  now()->year)
                ->sum('so_tien'),

            'dot_thu_dang_mo'   => DotThu::where('trang_thai', 'dang_thu')->count(),
        ];
    }
}
