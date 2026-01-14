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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('문의 회원 ID');
            
            // 문의 기본 정보
            $table->string('title')->comment('제목');
            $table->text('content')->comment('내용');
            $table->json('attachments')->nullable()->comment('첨부파일');
            
            // 프로젝트 기수
            $table->unsignedBigInteger('project_term_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('operating_institution_id')->nullable();
            $table->unsignedBigInteger('project_period_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            
            // 답변 정보
            $table->text('reply_content')->nullable()->comment('답변 내용');
            $table->json('reply_attachments')->nullable()->comment('답변 첨부파일');
            $table->enum('reply_status', ['pending', 'completed'])->default('pending')->comment('답변여부');
            $table->timestamp('replied_at')->nullable()->comment('답변일');
            $table->unsignedBigInteger('replied_by')->nullable()->comment('답변 작성자 ID');
            
            $table->timestamps();
            $table->softDeletes();
            
            // 인덱스
            $table->index('user_id');
            $table->index('reply_status');
            $table->index('replied_at');
            $table->index('created_at');
            $table->index(['project_term_id', 'course_id', 'operating_institution_id', 'project_period_id', 'country_id'], 'inquiries_project_term_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
