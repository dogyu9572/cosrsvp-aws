<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 활성 코드만 조회하는 스코프
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 코드 검증 (대소문자 구분 없음)
     */
    public static function validateCode(string $code): ?self
    {
        return static::active()
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();
    }
}