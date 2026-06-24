<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hoc_phis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->cascadeOnDelete();
            $table->foreignId('dot_thu_id')->constrained('dot_thus')->cascadeOnDelete();
            $table->integer('so_tin_chi')->comment('Số tín chỉ đăng ký kỳ này');
            $table->decimal('don_gia_tin_chi', 12, 2)->comment('Snapshot đơn giá tại thời điểm tạo');
            $table->decimal('tong_truoc_giam', 14, 2)->storedAs('so_tin_chi * don_gia_tin_chi');
            $table->decimal('phan_tram_giam', 5, 2)->default(0);
            $table->decimal('so_tien_giam', 12, 2)->default(0);
            $table->decimal('tong_phai_dong', 14, 2);
            $table->decimal('da_dong', 14, 2)->default(0);
            $table->decimal('phi_phat', 12, 2)->default(0)->comment('Phí phạt quá hạn tích lũy');
            $table->enum('trang_thai', ['chua_dong', 'dong_mot_phan', 'da_dong_du', 'mien_hoan_toan'])
                  ->default('chua_dong');
            $table->timestamps();

            $table->unique(['sinh_vien_id', 'dot_thu_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('hoc_phis'); }
};
