<?php

namespace App\Providers;

use App\Models\DotThu;
use App\Observers\DotThuObserver;
use App\Services\{HocPhiService, ThanhToanService, BaoCaoService, ThongBaoService};
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- 1. Bổ sung thư viện URL ở đây

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Đăng ký Services vào IoC Container — dùng Singleton
        // (Laravel chỉ tạo 1 instance duy nhất trong 1 request)
        $this->app->singleton(HocPhiService::class);
        $this->app->singleton(ThanhToanService::class);
        $this->app->singleton(BaoCaoService::class);
        $this->app->singleton(ThongBaoService::class);
    }

    public function boot(): void
    {
        // Đăng ký Observer — DotThu sẽ tự kích hoạt Observer
        DotThu::observe(DotThuObserver::class);

        // <-- 2. Thêm đoạn code ép HTTPS vào đây (khi chạy trên mạng)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}