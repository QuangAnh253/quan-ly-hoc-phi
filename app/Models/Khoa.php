<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Khoa extends Model
{
    protected $table = 'khoas';

    protected $fillable = ['ma_khoa', 'ten_khoa', 'truong_khoa', 'active'];

    public function sinhViens(): HasMany
    {
        return $this->hasMany(SinhVien::class, 'khoa_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
