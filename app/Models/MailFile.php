<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailFile extends Model
{
    protected $fillable = [
        'mail_id',
        'file_path',
        'file_name',
        'file_size',
    ];

    /**
     * 메일과의 관계
     */
    public function mail(): BelongsTo
    {
        return $this->belongsTo(Mail::class);
    }
}
