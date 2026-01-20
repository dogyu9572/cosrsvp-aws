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
        Schema::create('access_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('접근 코드');
            $table->string('description')->nullable()->comment('코드 설명');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamp('expires_at')->nullable()->comment('만료일');
            $table->unsignedInteger('used_count')->default(0)->comment('사용 횟수');
            $table->timestamp('last_used_at')->nullable()->comment('마지막 사용일');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_codes');
    }
};
