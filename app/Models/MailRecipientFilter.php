<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailRecipientFilter extends Model
{
    protected $fillable = [
        'mail_id',
        'project_term_id',
        'course_id',
        'operating_institution_id',
        'project_period_id',
        'country_id',
    ];

    /**
     * 메일과의 관계
     */
    public function mail(): BelongsTo
    {
        return $this->belongsTo(Mail::class);
    }

    /**
     * 프로젝트 기수와의 관계
     */
    public function projectTerm(): BelongsTo
    {
        return $this->belongsTo(ProjectTerm::class);
    }

    /**
     * 과정과의 관계
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * 운영기관과의 관계
     */
    public function operatingInstitution(): BelongsTo
    {
        return $this->belongsTo(OperatingInstitution::class);
    }

    /**
     * 프로젝트기간과의 관계
     */
    public function projectPeriod(): BelongsTo
    {
        return $this->belongsTo(ProjectPeriod::class);
    }

    /**
     * 국가와의 관계
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
