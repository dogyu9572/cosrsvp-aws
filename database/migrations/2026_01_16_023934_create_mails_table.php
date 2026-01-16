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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('제목 (관리자용)');
            $table->string('dispatch_subject', 255)->comment('발송 제목');
            $table->text('content')->comment('내용');
            $table->enum('recipient_type', ['project_term', 'address_book', 'test'])->comment('발송대상 타입');
            $table->enum('dispatch_status', ['saved', 'scheduled'])->default('saved')->comment('발송여부');
            $table->dateTime('scheduled_at')->nullable()->comment('예약 발송일시');
            $table->string('test_email', 255)->nullable()->comment('테스트 이메일');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자');
            $table->timestamps();
            $table->softDeletes();
            
            // 인덱스
            $table->index('recipient_type');
            $table->index('dispatch_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
