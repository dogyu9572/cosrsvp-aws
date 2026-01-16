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
        Schema::create('mail_address_book_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_book_id')->constrained('mail_address_books')->cascadeOnDelete()->comment('주소록 ID');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('회원 ID');
            $table->timestamps();
            
            // 유니크 제약조건
            $table->unique(['address_book_id', 'member_id']);
            
            // 인덱스
            $table->index('address_book_id');
            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_address_book_members');
    }
};
