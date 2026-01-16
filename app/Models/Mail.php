<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'dispatch_subject',
        'content',
        'recipient_type',
        'dispatch_status',
        'scheduled_at',
        'test_email',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

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
        return $this->hasMany(MailFile::class);
    }

    /**
     * 기수별 발송 필터와의 관계
     */
    public function recipientFilters(): HasMany
    {
        return $this->hasMany(MailRecipientFilter::class);
    }

    /**
     * 주소록 선택과의 관계
     */
    public function addressBookSelections(): HasMany
    {
        return $this->hasMany(MailAddressBookSelection::class);
    }

    /**
     * 주소록과의 관계 (Many-to-Many via selections)
     */
    public function addressBooks(): BelongsToMany
    {
        return $this->belongsToMany(MailAddressBook::class, 'mail_address_book_selections', 'mail_id', 'address_book_id')
            ->withTimestamps();
    }
}
