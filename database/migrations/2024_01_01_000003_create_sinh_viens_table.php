<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinh_viens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('khoa_id')->constrained('khoas');
            $table->string('ma_sv', 20)->unique()->comment('VD: SV001, B21DCCN001');
            $table->string('ho_ten', 100);
            $table->date('ngay_sinh')->nullable();
            $table->enum('gioi_tinh', ['nam', 'nu', 'khac'])->default('nam');
            $table->string('cccd', 12)->nullable()->unique();
            $table->string('lop', 20);
            $table->year('nien_khoa');
            $table->enum('he_dao_tao', ['chinh_quy', 'lien_thong', 'tu_xa'])->default('chinh_quy');
            $table->enum('dien_mien_giam', [
                'binh_thuong',
                'ho_ngheo',
                'ho_can_ngheo',
                'chinh_sach',
                'thuong_binh',
                'mo_coi',
                'khuyet_tat',
            ])->default('binh_thuong');
            $table->string('so_dien_thoai', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('dia_chi')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('sinh_viens'); }
};
