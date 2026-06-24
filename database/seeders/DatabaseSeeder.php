<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\{Khoa, User, SinhVien, DotThu, MienGiam, HocPhi, ThanhToan};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ThanhToan::truncate();
        HocPhi::truncate();
        MienGiam::truncate();
        SinhVien::truncate();
        DotThu::truncate();
        User::truncate();
        Khoa::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $khoaCNTT = Khoa::create([
            'ma_khoa'     => 'CNTT',
            'ten_khoa'    => 'Công nghệ Thông tin',
            'truong_khoa' => 'TS. Lê Chí Luận',
            'active'      => 1,
        ]);

        $khoaKinhTe = Khoa::create([
            'ma_khoa'     => 'KTVT',
            'ten_khoa'    => 'Kinh tế vận tải',
            'truong_khoa' => 'TS. Hoàng Thị Hồng Lê',
            'active'      => 1,
        ]);

        $khoaQT = Khoa::create([
            'ma_khoa'     => 'QT',
            'ten_khoa'    => 'Quản trị',
            'truong_khoa' => 'TS. Nguyễn Hùng Cường',
            'active'      => 1,
        ]);

        $admin = User::create([
            'name'     => 'Quản trị UTT',
            'email'    => 'admin@utt.edu.vn',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $ketoan = User::create([
            'name'     => 'Phòng Kế toán UTT',
            'email'    => 'ketoan@utt.edu.vn',
            'password' => Hash::make('password'),
            'role'     => 'ketoan',
        ]);

        $studentUsers = [
            ['name' => 'Lê Quang Anh', 'email' => 'quanganhle253@gmail.com'],
            ['name' => 'Nguyễn Thị Hồng',   'email' => 'nguyenthihong26092005@gmail.com'],
            ['name' => 'Nguyễn Văn Lộc',     'email' => 'nguyenvanloc24092005@gmail.com'],
            ['name' => 'Vũ Thị Thùy Trang',   'email' => 'vut91744@gmail.com'],
            ['name' => 'Nguyễn Bá Lộc',     'email' => 'nguyenbaloc294@gmail.com'],
            ['name' => 'Nguyễn Duy Thành',     'email' => 'nguyendthanh11052005@gmail.com'],
            ['name' => 'Hoàng Gia Huy',   'email' => 'contact.quanganh253@gmail.com'],
        ];

        $danhSachDien = [
            'binh_thuong',
            'ho_ngheo',
            'ho_can_ngheo',
            'chinh_sach',
            'khuyet_tat',
            'thuong_binh',
            'binh_thuong',
        ];

        $studentUsersByEmail = [];
        foreach ($studentUsers as $studentUser) {
            $studentUsersByEmail[$studentUser['email']] = User::create([
                'name'     => $studentUser['name'],
                'email'    => $studentUser['email'],
                'password' => Hash::make('password'),
                'role'     => 'sinhvien',
            ]);
        }

        $khoaList   = [$khoaCNTT, $khoaCNTT, $khoaKinhTe, $khoaQT, $khoaCNTT, $khoaKinhTe, $khoaQT];
        $maSvList   = ['74DCHT21175','74DCTT21284','74DCKT22244','74DCQT22166','74DCCN21020','74DCCQ22236','74DCQT22001'];
        $hoTenList  = ['Lê Quang Anh','Nguyễn Thị Hồng','Nguyễn Văn Lộc','Vũ Thị Thùy Trang','Nguyễn Bá Lộc','Nguyễn Duy Thành','Hoàng Gia Huy'];
        $ngaySinhList = ['2005-03-25','2005-09-26','2005-09-24','2005-04-24','2005-04-29','2005-05-11','2005-07-09'];
        $gioiTinhList = ['nam','nu','nam','nu','nam','nu','nam'];
        $cccdList   = ['001205000001','034305000002','040205000003','036305000004','001205000005','040205000006','001205012345'];
        $lopList    = ['74DCHT22','74DCTT23','74DCKT21','74DCQT24','74DCCN21','74DCKN22','74DCHT24'];
        $sdtList    = ['0833250305','0374154947','0567003946','0382917406','0936227858','0374223006','0912345681'];
        $diaChiList = ['Hoàn Kiếm, Hà Nội','Thái Bình','Nghệ An','Ý Yên, Nam Định','Ứng Hòa, Hà Nội','Cửa Lò, Nghệ An','Đống Đa, Hà Nội'];
        $emailList  = ['quanganhle253@gmail.com','nguyenthihong26092005@gmail.com','nguyenvanloc24092005@gmail.com','vut91744@gmail.com','nguyenbaloc294@gmail.com','nguyendthanh11052005@gmail.com','contact.quanganh253@gmail.com'];

        $sinhViens = [];
        foreach ($studentUsers as $i => $su) {
            $sinhViens[] = SinhVien::create([
                'user_id'        => $studentUsersByEmail[$emailList[$i]]->id,
                'khoa_id'        => $khoaList[$i]->id,
                'ma_sv'          => $maSvList[$i],
                'ho_ten'         => $hoTenList[$i],
                'ngay_sinh'      => $ngaySinhList[$i],
                'gioi_tinh'      => $gioiTinhList[$i],
                'cccd'           => $cccdList[$i],
                'lop'            => $lopList[$i],
                'nien_khoa'      => 2022,
                'he_dao_tao'     => 'chinh_quy',
                'dien_mien_giam' => $danhSachDien[$i],
                'so_dien_thoai'  => $sdtList[$i],
                'email'          => $emailList[$i],
                'dia_chi'        => $diaChiList[$i],
                'active'         => true,
            ]);
        }

        // ── 4. Miễn giảm ─────────────────────────────────────

        /*
        |--------------------------------------------------------------------------
        | MIỄN GIẢM
        |--------------------------------------------------------------------------
        */

        $mienGiamMap = [
            'ho_ngheo'    => 100,
            'ho_can_ngheo' => 50,
            'chinh_sach'  => 50,
            'khuyet_tat'  => 100,
            'thuong_binh' => 70,
        ];

        $mgSoQd = 1;
        foreach ($sinhViens as $sv) {
            if (!isset($mienGiamMap[$sv->dien_mien_giam])) {
                continue;
            }
            MienGiam::create([
                'sinh_vien_id'       => $sv->id,
                'loai'               => $sv->dien_mien_giam,
                'phan_tram_giam'     => $mienGiamMap[$sv->dien_mien_giam],
                'so_tien_giam_co_dinh' => 0,
                'so_quyet_dinh'      => 'UTT-MG-2026-' . str_pad($mgSoQd++, 3, '0', STR_PAD_LEFT),
                'nam_ap_dung'        => 2026,
                'active'             => true,
            ]);
        }

        // ── 5. Đợt thu ───────────────────────────────────────

        /*
        |--------------------------------------------------------------------------
        | CÁC ĐỢT THU
        |--------------------------------------------------------------------------
        */

        $dotThus = [

            // 2023-2024
            [
                'ten_dot'   => 'HK1 2023-2024',
                'hoc_ky'    => 1,
                'nam_hoc'   => '2023-2024',
                'trang_thai' => 'da_dong',
            ],

            [
                'ten_dot'   => 'HK2 2023-2024',
                'hoc_ky'    => 2,
                'nam_hoc'   => '2023-2024',
                'trang_thai' => 'da_dong',
            ],

            // 2024-2025
            [
                'ten_dot'   => 'HK1 2024-2025',
                'hoc_ky'    => 1,
                'nam_hoc'   => '2024-2025',
                'trang_thai' => 'da_dong',
            ],

            [
                'ten_dot'   => 'HK2 2024-2025',
                'hoc_ky'    => 2,
                'nam_hoc'   => '2024-2025',
                'trang_thai' => 'da_dong',
            ],

            // 2025-2026
            [
                'ten_dot'   => 'HK1 2025-2026',
                'hoc_ky'    => 1,
                'nam_hoc'   => '2025-2026',
                'trang_thai' => 'da_dong',
            ],

            [
                'ten_dot'   => 'HK2 Đợt 1 2025-2026',
                'hoc_ky'    => 2,
                'nam_hoc'   => '2025-2026',
                'trang_thai' => 'da_dong',
            ],

            [
                'ten_dot'   => 'HK2 Đợt 2 2025-2026',
                'hoc_ky'    => 2,
                'nam_hoc'   => '2025-2026',
                'trang_thai' => 'dang_thu',
            ],

            [
                'ten_dot'   => 'Kỳ phụ 86',
                'hoc_ky'    => 2,
                'nam_hoc'   => '2025-2026',
                'trang_thai' => 'sap_mo',
            ],
        ];

        $dotThuDates = [
            ['ngay_bat_dau' => '2023-08-15', 'han_dong' => '2023-09-20'],
            ['ngay_bat_dau' => '2024-01-10', 'han_dong' => '2024-02-20'],
            ['ngay_bat_dau' => '2024-08-15', 'han_dong' => '2024-09-20'],
            ['ngay_bat_dau' => '2025-01-10', 'han_dong' => '2025-02-20'],
            ['ngay_bat_dau' => '2025-08-15', 'han_dong' => '2025-09-20'],
            ['ngay_bat_dau' => '2026-01-10', 'han_dong' => '2026-02-20'],
            ['ngay_bat_dau' => '2026-03-01', 'han_dong' => '2026-04-10'],
            ['ngay_bat_dau' => '2026-07-01', 'han_dong' => '2026-07-31'],
        ];

        $dotThuObjects = [];
        foreach ($dotThus as $i => $dt) {
            $dotThuObjects[] = DotThu::create([
                'ten_dot'       => $dt['ten_dot'],
                'hoc_ky'        => $dt['hoc_ky'],
                'nam_hoc'       => $dt['nam_hoc'],
                'don_gia_tin_chi' => 520000,
                'ngay_bat_dau'  => $dotThuDates[$i]['ngay_bat_dau'],
                'han_dong'      => $dotThuDates[$i]['han_dong'],
                'phi_phat_ngay' => 8000,
                'trang_thai'    => $dt['trang_thai'],
                'ghi_chu'       => null,
                'created_by'    => $admin->id,
            ]);
        }

        $donGiaTinChi = 520000;

        foreach ($dotThuObjects as $dotThu) {

            foreach ($sinhViens as $sv) {

                $phanTramGiam = $mienGiamMap[$sv->dien_mien_giam] ?? 0;

                $soTinChi = rand(15, 20);

                $tongTruocGiam = $soTinChi * $donGiaTinChi;

                $soTienGiam = $tongTruocGiam * $phanTramGiam / 100;

                $tongPhaiDong = $tongTruocGiam - $soTienGiam;

                $daDong    = $tongPhaiDong;
                $trangThai = 'da_dong_du';

                if (
                    $dotThu->ten_dot == 'HK1 2025-2026'
                    && $sv->ma_sv == '74DCQT22001'
                ) {
                    $daDong    = $tongPhaiDong / 2;
                    $trangThai = 'dong_mot_phan';
                }

                if (
                    $dotThu->ten_dot == 'HK2 Đợt 2 2025-2026'
                    && in_array($sv->ma_sv, [
                        '74DCHT21175',
                        '74DCCQ22236',
                        '74DCQT22001',
                    ])
                ) {
                    $daDong    = 0;
                    $trangThai = 'chua_dong';
                }

                if ($dotThu->ten_dot == 'Kỳ phụ 86') {
                    continue;
                }

                $hocPhi = HocPhi::create([
                    'sinh_vien_id'   => $sv->id,
                    'dot_thu_id'     => $dotThu->id,
                    'so_tin_chi'     => $soTinChi,
                    'don_gia_tin_chi' => $donGiaTinChi,
                    'phan_tram_giam' => $phanTramGiam,
                    'so_tien_giam'   => $soTienGiam,
                    'tong_phai_dong' => $tongPhaiDong,
                    'da_dong'        => $daDong,
                    'phi_phat'       => 0,
                    'trang_thai'     => $trangThai,
                ]);

                if ($daDong > 0) {

                    ThanhToan::create([
                        'hoc_phi_id'    => $hocPhi->id,
                        'nguoi_thu_id'  => $ketoan->id,
                        'ma_giao_dich'  => 'TT-' . now()->format('YmdHis') . rand(100, 999),
                        'so_tien'       => $daDong,
                        'hinh_thuc'     => 'chuyen_khoan',
                        'ngan_hang'     => 'Vietcombank',
                        'so_tham_chieu' => fake()->uuid(),
                        'ghi_chu'       => 'Thanh toán học phí',
                        'thoi_gian_thu' => fake()->dateTimeBetween('-2 years'),
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeder đã hoàn thành!');
        $this->command->info('   admin@utt.edu.vn / password');
        $this->command->info('   ketoan@utt.edu.vn / password');
        $this->command->info('   quanganhle253@gmail.com / password');
    }
}
