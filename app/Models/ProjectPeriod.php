<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectPeriod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operating_institution_id',
        'name_ko',
        'name_en',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * 이 프로젝트기간이 속한 운영기관
     */
    public function operatingInstitution(): BelongsTo
    {
        return $this->belongsTo(OperatingInstitution::class);
    }

    /**
     * 이 프로젝트기간에 속한 국가들
     */
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class)->orderBy('display_order');
    }

    /**
     * 활성화된 프로젝트기간만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
