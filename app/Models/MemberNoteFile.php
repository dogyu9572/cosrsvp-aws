<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberNoteFile extends Model
{
    protected $fillable = [
        'member_note_id',
        'file_path',
        'file_name',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * 회원비고와의 관계
     */
    public function memberNote(): BelongsTo
    {
        return $this->belongsTo(MemberNote::class);
    }
}
