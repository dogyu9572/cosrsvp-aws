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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            
            // 기본 정보
            $table->string('login_id', 100)->unique()->comment('로그인 아이디');
            $table->string('password')->comment('비밀번호');
            $table->string('name', 100)->comment('성명');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('성별');
            $table->string('email', 255)->nullable()->comment('이메일');
            $table->string('phone_kr', 50)->nullable()->comment('한국 전화번호');
            $table->string('phone_local', 50)->nullable()->comment('현지 전화번호');
            $table->date('birth_date')->nullable()->comment('생년월일');
            
            // 신분 정보
            $table->string('passport_number', 50)->nullable()->comment('여권번호');
            $table->date('passport_expiry')->nullable()->comment('여권유효기간');
            $table->string('alien_registration_number', 50)->nullable()->comment('외국인등록번호');
            $table->date('alien_registration_expiry')->nullable()->comment('외국인등록증 유효기간');
            
            // 프로젝트 연결
            $table->foreignId('project_term_id')->nullable()->constrained('project_terms')->nullOnDelete()->comment('프로젝트 기수');
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete()->comment('과정');
            $table->foreignId('operating_institution_id')->nullable()->constrained('operating_institutions')->nullOnDelete()->comment('운영기관');
            $table->foreignId('project_period_id')->nullable()->constrained('project_periods')->nullOnDelete()->comment('프로젝트기간');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->comment('국가');
            
            // 연수 정보 (관리자 작성)
            $table->string('hotel_name', 255)->nullable()->comment('호텔명');
            $table->string('hotel_address', 500)->nullable()->comment('호텔주소');
            $table->string('hotel_address_detail', 255)->nullable()->comment('호텔 상세주소');
            $table->string('training_period', 100)->nullable()->comment('연수기간');
            $table->string('visa_type', 50)->nullable()->comment('비자종류');
            $table->text('cultural_experience')->nullable()->comment('문화체험');
            $table->string('account_info', 255)->nullable()->comment('계좌번호');
            $table->string('insurance_status', 50)->nullable()->comment('보험가입여부');
            
            // 회원 작성 정보
            $table->string('occupation', 100)->nullable()->comment('직업');
            $table->string('major', 100)->nullable()->comment('전공');
            $table->string('affiliation', 255)->nullable()->comment('소속');
            $table->string('department', 100)->nullable()->comment('부서');
            $table->string('position', 100)->nullable()->comment('직위');
            $table->string('clothing_size', 10)->nullable()->comment('옷 사이즈');
            $table->text('dietary_restrictions')->nullable()->comment('특이식성');
            $table->text('special_requests')->nullable()->comment('특이사항 및 요청사항');
            
            // 입출국 정보
            $table->string('ticket_file', 255)->nullable()->comment('항공권 파일');
            $table->string('departure_location', 100)->nullable()->comment('출발지');
            $table->string('arrival_location', 100)->nullable()->comment('도착지');
            $table->date('entry_date')->nullable()->comment('입국일자');
            $table->date('exit_date')->nullable()->comment('출국일자');
            $table->string('entry_flight', 255)->nullable()->comment('입국 항공편');
            $table->string('exit_flight', 255)->nullable()->comment('출국 항공편');
            
            // 기타
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            $table->softDeletes();
            
            // 인덱스
            $table->index('login_id');
            $table->index('project_term_id');
            $table->index('course_id');
            $table->index('country_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
