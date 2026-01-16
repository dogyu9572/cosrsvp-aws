<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_address_book_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_book_id')->constrained('mail_address_books')->cascadeOnDelete()->comment('주소록 ID');
            $table->foreignId('contact_id')->constrained('mail_contacts')->cascadeOnDelete()->comment('연락처 ID');
            $table->timestamps();
            
            // 유니크 제약조건
            $table->unique(['address_book_id', 'contact_id']);
            
            // 인덱스
            $table->index('address_book_id');
            $table->index('contact_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_address_book_contacts');
    }
};
