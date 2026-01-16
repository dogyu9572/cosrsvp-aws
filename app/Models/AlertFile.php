<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertFile extends Model
{
    protected $table = 'member_alert_files';

    protected $fillable = [
        'alert_id',
        'file_path',
        'file_name',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * 알림과의 관계
     */
    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
