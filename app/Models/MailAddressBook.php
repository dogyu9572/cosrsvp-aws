<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailAddressBook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'created_by',
    ];

    /**
     * 생성자와의 관계
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 연락처와의 관계 (Many-to-Many)
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(MailContact::class, 'mail_address_book_contacts', 'address_book_id', 'contact_id')
            ->withTimestamps();
    }

    /**
     * 회원과의 관계 (Many-to-Many)
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'mail_address_book_members', 'address_book_id', 'member_id')
            ->withTimestamps();
    }

    /**
     * 메일 선택과의 관계
     */
    public function mailSelections(): HasMany
    {
        return $this->hasMany(MailAddressBookSelection::class, 'address_book_id');
    }

    /**
     * 등록인원 수 계산 (연락처 + 회원)
     */
    public function getTotalRecipientsCountAttribute(): int
    {
        return $this->contacts()->count() + $this->members()->count();
    }
}
