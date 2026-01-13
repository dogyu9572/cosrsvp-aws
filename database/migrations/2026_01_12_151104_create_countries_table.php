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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_period_id')->constrained('project_periods')->onDelete('cascade')->comment('프로젝트기간 ID');
            $table->string('name_ko', 100)->comment('국가명 (국문)');
            $table->string('name_en', 100)->nullable()->comment('국가명 (영문)');
            $table->unsignedBigInteger('reference_material_id')->nullable()->comment('참고자료 ID (추후 reference_materials 테이블 생성 후 외래키 추가 예정)');
            $table->integer('display_order')->default(0)->comment('정렬 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            $table->softDeletes();

            // 인덱스
            $table->index('project_period_id');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
