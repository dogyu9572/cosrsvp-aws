<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Model
{
    use SoftDeletes;

    protected $table = 'member_alerts';

    protected $fillable = [
        'member_id',
        'is_notice',
        'korean_title',
        'english_title',
        'korean_content',
        'english_content',
        'created_by',
    ];

    protected $casts = [
        'is_notice' => 'boolean',
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
        return $this->hasMany(AlertFile::class);
    }
}
