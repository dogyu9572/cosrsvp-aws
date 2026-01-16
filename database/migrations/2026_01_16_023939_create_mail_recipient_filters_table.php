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
        Schema::create('mail_recipient_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_id')->constrained('mails')->cascadeOnDelete()->comment('메일 ID');
            $table->foreignId('project_term_id')->nullable()->constrained('project_terms')->nullOnDelete()->comment('기수');
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete()->comment('과정');
            $table->foreignId('operating_institution_id')->nullable()->constrained('operating_institutions')->nullOnDelete()->comment('운영기관');
            $table->foreignId('project_period_id')->nullable()->constrained('project_periods')->nullOnDelete()->comment('프로젝트기간');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->comment('국가');
            $table->timestamps();
            
            // 인덱스
            $table->index('mail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_recipient_filters');
    }
};
