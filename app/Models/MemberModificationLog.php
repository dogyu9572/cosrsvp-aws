<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberModificationLog extends Model
{
    protected $fillable = [
        'member_id',
        'modified_by',
        'modification_type',
        'description',
    ];

    /**
     * 회원과의 관계
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 수정자와의 관계
     */
    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
