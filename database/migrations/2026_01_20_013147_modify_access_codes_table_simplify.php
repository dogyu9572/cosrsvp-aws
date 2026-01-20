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
        Schema::table('access_codes', function (Blueprint $table) {
            $table->dropColumn(['description', 'expires_at', 'used_count', 'last_used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_codes', function (Blueprint $table) {
            $table->string('description')->nullable()->comment('코드 설명');
            $table->timestamp('expires_at')->nullable()->comment('만료일');
            $table->unsignedInteger('used_count')->default(0)->comment('사용 횟수');
            $table->timestamp('last_used_at')->nullable()->comment('마지막 사용일');
        });
    }
};
