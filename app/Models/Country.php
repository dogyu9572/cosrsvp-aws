<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_period_id',
        'name_ko',
        'name_en',
        'reference_material_id',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * 이 국가가 속한 프로젝트기간
     */
    public function projectPeriod(): BelongsTo
    {
        return $this->belongsTo(ProjectPeriod::class);
    }

    /**
     * 이 국가에 속한 일정들
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class)->orderBy('display_order');
    }

    /**
     * 활성화된 국가만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
