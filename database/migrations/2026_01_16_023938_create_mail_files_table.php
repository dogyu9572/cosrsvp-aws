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
        Schema::create('mail_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_id')->constrained('mails')->cascadeOnDelete()->comment('메일 ID');
            $table->string('file_path', 500)->comment('파일 경로');
            $table->string('file_name', 255)->comment('파일명');
            $table->unsignedBigInteger('file_size')->default(0)->comment('파일 크기 (bytes)');
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
        Schema::dropIfExists('mail_files');
    }
};
