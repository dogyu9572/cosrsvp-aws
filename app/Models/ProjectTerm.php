<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTerm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * 이 기수에 속한 과정들
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class)->orderBy('display_order');
    }

    /**
     * 활성화된 기수만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
