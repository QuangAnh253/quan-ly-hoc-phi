<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mien_giams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->cascadeOnDelete();
            $table->enum('loai', [
                'ho_ngheo',         // miễn 100%
                'ho_can_ngheo',     // giảm 50%
                'chinh_sach',       // giảm 50%
                'thuong_binh',      // giảm 70%
                'mo_coi',           // miễn 100%
                'khuyet_tat',       // miễn 100%
                'hoc_bong_kk',      // giảm theo số tiền cố định
                'hoc_bong_toan_phan', // miễn 100%
            ]);
            $table->decimal('phan_tram_giam', 5, 2)->default(0)->comment('0-100%');
            $table->decimal('so_tien_giam_co_dinh', 12, 2)->default(0)->comment('Nếu miễn cố định');
            $table->string('so_quyet_dinh', 50)->nullable();
            $table->string('chung_tu', 255)->nullable()->comment('Path file chứng từ');
            $table->year('nam_ap_dung');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('mien_giams'); }
};
