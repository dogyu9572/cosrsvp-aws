<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'status',
        'korean_title',
        'english_title',
        'korean_content',
        'english_content',
        'share_with_member',
        'share_with_kofhi',
        'share_with_operator',
        'created_by',
    ];

    protected $casts = [
        'share_with_member' => 'boolean',
        'share_with_kofhi' => 'boolean',
        'share_with_operator' => 'boolean',
    ];

    /**
     * 회원과의 관계
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 생성자와의 관계
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 첨부파일과의 관계
     */
    public function files(): HasMany
    {
        return $this->hasMany(MemberNoteFile::class);
    }

    /**
     * 상태별 필터링
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
