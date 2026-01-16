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
        Schema::create('member_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('회원 ID');
            $table->string('document_name', 255)->comment('서류명');
            $table->string('file_path', 500)->nullable()->comment('파일 경로');
            $table->date('submission_deadline')->nullable()->comment('제출마감일');
            $table->dateTime('submitted_at')->nullable()->comment('제출일');
            $table->enum('status', ['submitted', 'not_submitted', 'supplement_requested', 'resubmitted'])->default('not_submitted')->comment('상태');
            $table->text('supplement_request_content')->nullable()->comment('보완요청 내용');
            $table->timestamps();
            
            // 인덱스
            $table->index('member_id');
            $table->index('status');
            $table->index('submission_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_documents');
    }
};
