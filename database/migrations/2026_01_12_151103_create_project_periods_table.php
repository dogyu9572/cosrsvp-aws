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
        Schema::create('project_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operating_institution_id')->constrained('operating_institutions')->onDelete('cascade')->comment('운영기관 ID');
            $table->string('name_ko', 100)->comment('프로젝트기간명 (국문)');
            $table->string('name_en', 100)->nullable()->comment('프로젝트기간명 (영문)');
            $table->integer('display_order')->default(0)->comment('정렬 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            $table->softDeletes();

            // 인덱스
            $table->index('operating_institution_id');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_periods');
    }
};
