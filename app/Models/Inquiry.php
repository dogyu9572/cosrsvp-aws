<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;

    /**
     * 답변 상태 상수
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'attachments',
        'project_term_id',
        'course_id',
        'operating_institution_id',
        'project_period_id',
        'country_id',
        'reply_content',
        'reply_attachments',
        'reply_status',
        'replied_at',
        'replied_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attachments' => 'array',
        'reply_attachments' => 'array',
        'replied_at' => 'datetime',
    ];

    /**
     * 문의를 남긴 회원
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 프로젝트 기수
     */
    public function projectTerm()
    {
        return $this->belongsTo(ProjectTerm::class, 'project_term_id');
    }

    /**
     * 과정
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * 운영기관
     */
    public function operatingInstitution()
    {
        return $this->belongsTo(OperatingInstitution::class, 'operating_institution_id');
    }

    /**
     * 프로젝트기간
     */
    public function projectPeriod()
    {
        return $this->belongsTo(ProjectPeriod::class, 'project_period_id');
    }

    /**
     * 국가
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * 답변 작성자
     */
    public function repliedByUser()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * 답변 대기 중인 문의만 조회
     */
    public function scopePending($query)
    {
        return $query->where('reply_status', self::STATUS_PENDING);
    }

    /**
     * 답변 완료된 문의만 조회
     */
    public function scopeCompleted($query)
    {
        return $query->where('reply_status', self::STATUS_COMPLETED);
    }

    /**
     * 답변 여부 확인
     */
    public function isPending(): bool
    {
        return $this->reply_status === self::STATUS_PENDING;
    }

    /**
     * 답변 완료 여부 확인
     */
    public function isCompleted(): bool
    {
        return $this->reply_status === self::STATUS_COMPLETED;
    }
}
