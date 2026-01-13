<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'name_ko',
        'name_en',
        'start_date',
        'end_date',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * 이 일정이 속한 국가
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * 활성화된 일정만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
