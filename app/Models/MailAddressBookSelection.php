<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailAddressBookSelection extends Model
{
    protected $fillable = [
        'mail_id',
        'address_book_id',
    ];

    /**
     * 메일과의 관계
     */
    public function mail(): BelongsTo
    {
        return $this->belongsTo(Mail::class);
    }

    /**
     * 주소록과의 관계
     */
    public function addressBook(): BelongsTo
    {
        return $this->belongsTo(MailAddressBook::class);
    }
}
