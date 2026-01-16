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
        Schema::create('member_modification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('회원 ID');
            $table->foreignId('modified_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자');
            $table->string('modification_type', 100)->comment('수정 유형');
            $table->text('description')->comment('수정 내용');
            $table->timestamps();
            
            // 인덱스
            $table->index('member_id');
            $table->index('modified_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_modification_logs');
    }
};
