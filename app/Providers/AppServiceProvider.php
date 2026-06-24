<?php

namespace App\Providers;

use App\Models\DotThu;
use App\Observers\DotThuObserver;
use App\Services\{HocPhiService, ThanhToanService, BaoCaoService, ThongBaoService};
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HocPhiService::class);
        $this->app->singleton(ThanhToanService::class);
        $this->app->singleton(BaoCaoService::class);
        $this->app->singleton(ThongBaoService::class);
    }

    public function boot(): void
    {
        DotThu::observe(DotThuObserver::class);

        // Khắc phục triệt để: Nếu đang chạy trên domain thật thì bắt buộc 100% dùng HTTPS
        if (str_contains(request()->getHost(), 'lequanganh.id.vn')) {
            URL::forceScheme('https');
        }
    }
}