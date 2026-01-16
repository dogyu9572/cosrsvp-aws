<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailContact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    /**
     * 주소록과의 관계 (Many-to-Many)
     */
    public function addressBooks(): BelongsToMany
    {
        return $this->belongsToMany(MailAddressBook::class, 'mail_address_book_contacts', 'contact_id', 'address_book_id')
            ->withTimestamps();
    }
}
