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
        Schema::create('mail_address_book_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_id')->constrained('mails')->cascadeOnDelete()->comment('메일 ID');
            $table->foreignId('address_book_id')->constrained('mail_address_books')->cascadeOnDelete()->comment('주소록 ID');
            $table->timestamps();
            
            // 인덱스
            $table->index('mail_id');
            $table->index('address_book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_address_book_selections');
    }
};
