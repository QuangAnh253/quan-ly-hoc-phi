<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dot_thus', function (Blueprint $table) {
            $table->id();
            $table->string('ten_dot', 100)->comment('VD: Đợt 1 - HK1 2024-2025');
            $table->tinyInteger('hoc_ky')->comment('1 hoặc 2');
            $table->string('nam_hoc', 9)->comment('VD: 2024-2025');
            $table->decimal('don_gia_tin_chi', 12, 2)->comment('Đơn giá 1 tín chỉ (VNĐ)');
            $table->date('ngay_bat_dau');
            $table->date('han_dong')->comment('Hạn đóng học phí');
            $table->decimal('phi_phat_ngay', 10, 2)->default(0)->comment('Phí phạt mỗi ngày quá hạn');
            $table->enum('trang_thai', ['sap_mo', 'dang_thu', 'da_dong'])->default('sap_mo');
            $table->text('ghi_chu')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('dot_thus'); }
};
