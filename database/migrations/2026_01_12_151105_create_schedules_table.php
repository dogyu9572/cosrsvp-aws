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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade')->comment('국가 ID');
            $table->string('name_ko', 100)->comment('일정명 (국문)');
            $table->string('name_en', 100)->nullable()->comment('일정명 (영문)');
            $table->date('start_date')->nullable()->comment('일정 시작일');
            $table->date('end_date')->nullable()->comment('일정 종료일');
            $table->integer('display_order')->default(0)->comment('정렬 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            $table->softDeletes();

            // 인덱스
            $table->index('country_id');
            $table->index('display_order');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
