<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDocument extends Model
{
    protected $fillable = [
        'member_id',
        'document_name',
        'file_path',
        'submission_deadline',
        'submitted_at',
        'status',
        'supplement_request_content',
    ];

    protected $casts = [
        'submission_deadline' => 'date',
        'submitted_at' => 'datetime',
    ];

    /**
     * 회원과의 관계
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 상태별 필터링
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
