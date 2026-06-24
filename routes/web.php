<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DotThuController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\ThanhToanController;
use App\Services\ThanhToanService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
// AUTH
// ══════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ══════════════════════════════════════════════════════════════
// ADMIN
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('dashboard');

        Route::resource('dot-thu', DotThuController::class)
            ->except(['edit', 'update', 'destroy']);

        Route::patch('dot-thu/{dotThu}/mo',   [DotThuController::class, 'moDot'])
            ->name('dot-thu.mo');
        Route::patch('dot-thu/{dotThu}/dong', [DotThuController::class, 'dongDot'])
            ->name('dot-thu.dong');
        Route::post('dot-thu/{dotThu}/nhac-no', [DotThuController::class, 'guiNhacNo'])
            ->name('dot-thu.nhac-no');

        Route::patch('thanh-toan/{thanhToan}/huy', [ThanhToanController::class, 'huyGiaoDich'])
            ->name('thanh-toan.huy');

        Route::get('bao-cao/nam-hoc', [BaoCaoController::class, 'namHoc'])
            ->name('bao-cao.nam-hoc');
    });

// ══════════════════════════════════════════════════════════════
// KẾ TOÁN
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin,ketoan'])
    ->prefix('ketoan')
    ->name('ketoan.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'ketoanDashboard'])
            ->name('dashboard');

        Route::resource('sinh-vien', SinhVienController::class)
            ->except(['destroy']);

        Route::get('thanh-toan/thu',          [ThanhToanController::class, 'create'])->name('thanh-toan.create');
        Route::post('thanh-toan',             [ThanhToanController::class, 'store'])->name('thanh-toan.store');
        Route::get('thanh-toan/bien-lai/{maGiaoDich}', [ThanhToanController::class, 'bienLai'])->name('thanh-toan.bien-lai');
        Route::get('thanh-toan/lich-su/{sinhVien}',    [ThanhToanController::class, 'lichSuSinhVien'])->name('thanh-toan.lich-su');
        Route::get('thanh-toan/cong-no/{dotThu}',      [ThanhToanController::class, 'congNoDotThu'])->name('thanh-toan.cong-no');

        Route::get('bao-cao/dot-thu/{dotThu}',   [BaoCaoController::class, 'dotThu'])->name('bao-cao.dot-thu');
        Route::get('bao-cao/xuat-excel/{dotThu}',[BaoCaoController::class, 'xuatExcel'])->name('bao-cao.xuat-excel');
    });

// ══════════════════════════════════════════════════════════════
// SINH VIÊN
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:sinhvien'])
    ->prefix('sv')
    ->name('sinhvien.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'sinhVienDashboard'])
            ->name('dashboard');

        Route::get('/hoc-phi', function () {
            $sinhVien = Auth::user()->sinhVien->load('khoa');
            $lichSu   = app(ThanhToanService::class)->lichSuTheoSinhVien($sinhVien);
            return view('thanh-toan.lich-su', compact('sinhVien', 'lichSu'));
        })->name('hoc-phi');
    });

// ══════════════════════════════════════════════════════════════
// DEFAULT REDIRECT
// ══════════════════════════════════════════════════════════════
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return redirect()->route(match (Auth::user()->role) {
        'admin'  => 'admin.dashboard',
        'ketoan' => 'ketoan.dashboard',
        default  => 'sinhvien.dashboard',
    });
});
