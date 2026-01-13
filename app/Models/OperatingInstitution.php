<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingInstitution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'name_ko',
        'name_en',
        'cosmojin_manager_name',
        'cosmojin_manager_phone',
        'cosmojin_manager_email',
        'kofhi_manager_name',
        'kofhi_manager_phone',
        'kofhi_manager_email',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * 이 운영기관이 속한 과정
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * 이 운영기관에 속한 프로젝트기간들
     */
    public function projectPeriods(): HasMany
    {
        return $this->hasMany(ProjectPeriod::class)->orderBy('display_order');
    }

    /**
     * 활성화된 운영기관만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
