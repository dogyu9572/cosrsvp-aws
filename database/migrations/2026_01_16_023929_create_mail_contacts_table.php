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
        Schema::create('mail_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('이름');
            $table->string('email', 255)->comment('이메일');
            $table->string('phone', 50)->nullable()->comment('연락처');
            $table->timestamps();
            $table->softDeletes();
            
            // 인덱스
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_contacts');
    }
};
