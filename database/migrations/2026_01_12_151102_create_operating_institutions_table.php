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
        Schema::create('operating_institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade')->comment('과정 ID');
            $table->string('name_ko', 100)->comment('운영기관명 (국문)');
            $table->string('name_en', 100)->nullable()->comment('운영기관명 (영문)');
            
            // 코스모진 담당자 정보
            $table->string('cosmojin_manager_name', 100)->nullable()->comment('코스모진 담당자 이름');
            $table->string('cosmojin_manager_phone', 50)->nullable()->comment('코스모진 담당자 전화번호');
            $table->string('cosmojin_manager_email', 100)->nullable()->comment('코스모진 담당자 이메일');
            
            // KOFHI 담당자 정보
            $table->string('kofhi_manager_name', 100)->nullable()->comment('KOFHI 담당자 이름');
            $table->string('kofhi_manager_phone', 50)->nullable()->comment('KOFHI 담당자 전화번호');
            $table->string('kofhi_manager_email', 100)->nullable()->comment('KOFHI 담당자 이메일');
            
            $table->integer('display_order')->default(0)->comment('정렬 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            $table->softDeletes();

            // 인덱스
            $table->index('course_id');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_institutions');
    }
};
