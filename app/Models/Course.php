<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_term_id',
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
     * 이 과정이 속한 프로젝트 기수
     */
    public function projectTerm(): BelongsTo
    {
        return $this->belongsTo(ProjectTerm::class);
    }

    /**
     * 이 과정에 속한 운영기관들
     */
    public function operatingInstitutions(): HasMany
    {
        return $this->hasMany(OperatingInstitution::class)->orderBy('display_order');
    }

    /**
     * 활성화된 과정만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
