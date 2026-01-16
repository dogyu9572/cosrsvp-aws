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
        Schema::create('member_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('회원 ID');
            $table->string('korean_title', 255)->comment('국문 제목');
            $table->string('english_title', 255)->comment('영문 제목');
            $table->text('korean_content')->comment('국문 내용');
            $table->text('english_content')->comment('영문 내용');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자');
            $table->timestamps();
            $table->softDeletes();
            
            // 인덱스
            $table->index('member_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_alerts');
    }
};
