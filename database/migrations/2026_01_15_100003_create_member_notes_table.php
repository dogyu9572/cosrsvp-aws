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
        Schema::create('member_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('회원 ID');
            $table->enum('status', ['normal', 'urgent', 'caution'])->default('normal')->comment('상태 (일반, 긴급, 주의)');
            $table->string('korean_title', 255)->comment('국문 제목');
            $table->string('english_title', 255)->comment('영문 제목');
            $table->text('korean_content')->comment('국문 내용');
            $table->text('english_content')->comment('영문 내용');
            $table->boolean('share_with_member')->default(false)->comment('회원 공유');
            $table->boolean('share_with_kofhi')->default(false)->comment('KOFHI 공유');
            $table->boolean('share_with_operator')->default(false)->comment('운영기관 담당자 공유');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자');
            $table->timestamps();
            $table->softDeletes();
            
            // 인덱스
            $table->index('member_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_notes');
    }
};
