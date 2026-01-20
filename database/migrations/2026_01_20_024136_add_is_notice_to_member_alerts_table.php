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
        Schema::table('member_alerts', function (Blueprint $table) {
            $table->boolean('is_notice')->default(false)->after('member_id')->comment('공지사항 여부');
            $table->index(['is_notice', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_alerts', function (Blueprint $table) {
            $table->dropIndex(['is_notice', 'created_at']);
            $table->dropColumn('is_notice');
        });
    }
};
