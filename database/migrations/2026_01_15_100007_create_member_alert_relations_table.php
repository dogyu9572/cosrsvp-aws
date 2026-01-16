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
        Schema::create('member_alert_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('member_alerts')->cascadeOnDelete()->comment('알림 ID');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('회원 ID');
            $table->dateTime('push_sent_at')->nullable()->comment('푸시 발송일시');
            $table->timestamps();
            
            // 인덱스
            $table->index('alert_id');
            $table->index('member_id');
            $table->unique(['alert_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_alert_relations');
    }
};
