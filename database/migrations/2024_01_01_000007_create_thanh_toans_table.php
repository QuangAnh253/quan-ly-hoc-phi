<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thanh_toans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_phi_id')->constrained('hoc_phis')->cascadeOnDelete();
            $table->foreignId('nguoi_thu_id')->constrained('users');
            $table->string('ma_giao_dich', 50)->unique()->comment('TT-20240601-001');
            $table->decimal('so_tien', 14, 2);
            $table->enum('hinh_thuc', ['tien_mat', 'chuyen_khoan', 'the_ngan_hang', 'vi_dien_tu'])
                  ->default('tien_mat');
            $table->string('ngan_hang', 50)->nullable();
            $table->string('so_tham_chieu', 100)->nullable()->comment('Mã CK/biên lai ngân hàng');
            $table->text('ghi_chu')->nullable();
            $table->timestamp('thoi_gian_thu')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('thanh_toans'); }
};
