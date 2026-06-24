<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('khoas', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khoa', 20)->unique()->comment('Mã khoa: CNTT, KTKT...');
            $table->string('ten_khoa', 100);
            $table->string('truong_khoa', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('khoas'); }
};
