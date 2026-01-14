<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class InquirySeeder extends Seeder
{
    /**
     * 문의 관리 테스트 데이터를 시드합니다.
     */
    public function run(): void
    {
        // 기존 데이터 삭제
        Inquiry::query()->delete();

        // 사용자 가져오기 (없으면 null로 처리)
        $users = User::limit(5)->get();
        $userIds = $users->pluck('id')->toArray();
        if (empty($userIds)) {
            $userIds = [null];
        }

        // 프로젝트 기수 데이터 가져오기
        $projectTerm = ProjectTerm::active()->first();
        $course = $projectTerm ? Course::where('project_term_id', $projectTerm->id)->first() : null;
        $institution = $course ? OperatingInstitution::where('course_id', $course->id)->first() : null;
        $period = $institution ? ProjectPeriod::where('operating_institution_id', $institution->id)->first() : null;
        $country = $period ? Country::where('project_period_id', $period->id)->first() : null;

        // 관리자 사용자 가져오기 (답변 작성자용)
        $adminUser = User::where('role', 'admin')->orWhere('role', 'super_admin')->first();

        // 샘플 문의 데이터
        $inquiries = [
            [
                'user_id' => $userIds[0] ?? null,
                'title' => '프로젝트 참여 신청 문의',
                'content' => '안녕하세요. 프로젝트 참여를 신청하고 싶은데, 어떤 절차를 따라야 하는지 문의드립니다. 자세한 안내 부탁드립니다.',
                'attachments' => null,
                'project_term_id' => $projectTerm?->id,
                'course_id' => $course?->id,
                'operating_institution_id' => $institution?->id,
                'project_period_id' => $period?->id,
                'country_id' => $country?->id,
                'reply_content' => '<p>안녕하세요. 프로젝트 참여 신청에 관심을 가져주셔서 감사합니다.</p><p>프로젝트 참여 신청 절차는 다음과 같습니다:</p><ol><li>온라인 신청서 작성</li><li>서류 제출</li><li>면접 진행</li><li>최종 선발</li></ol><p>자세한 내용은 공지사항을 참고해주시기 바랍니다.</p>',
                'reply_attachments' => null,
                'reply_status' => 'completed',
                'replied_at' => now()->subDays(2),
                'replied_by' => $adminUser?->id,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $userIds[1 % count($userIds)] ?? null,
                'title' => '비자 발급 관련 문의',
                'content' => '비자 발급에 필요한 서류와 절차를 알려주실 수 있나요? 특히 서류 준비 기간이 얼마나 걸리는지 궁금합니다.',
                'attachments' => null,
                'project_term_id' => $projectTerm?->id,
                'course_id' => $course?->id,
                'operating_institution_id' => $institution?->id,
                'project_period_id' => $period?->id,
                'country_id' => $country?->id,
                'reply_content' => '<p>비자 발급 관련 문의 감사합니다.</p><p>필요한 서류는 다음과 같습니다:</p><ul><li>여권 사본</li><li>입학 허가서</li><li>재정 증명서</li><li>건강 진단서</li></ul><p>서류 준비 기간은 약 2-3주 정도 소요됩니다.</p>',
                'reply_attachments' => null,
                'reply_status' => 'completed',
                'replied_at' => now()->subDays(1),
                'replied_by' => $adminUser?->id,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $userIds[2 % count($userIds)] ?? null,
                'title' => '숙소 배정 문의',
                'content' => '프로젝트 참여 시 숙소는 어떻게 배정되나요? 개인 숙소를 원할 경우 가능한지 문의드립니다.',
                'attachments' => null,
                'project_term_id' => $projectTerm?->id,
                'course_id' => $course?->id,
                'operating_institution_id' => $institution?->id,
                'project_period_id' => $period?->id,
                'country_id' => $country?->id,
                'reply_content' => null,
                'reply_attachments' => null,
                'reply_status' => 'pending',
                'replied_at' => null,
                'replied_by' => null,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => $userIds[3 % count($userIds)] ?? null,
                'title' => '장학금 신청 방법',
                'content' => '장학금 신청 기간과 방법을 알려주세요. 신청 자격 요건도 함께 안내해주시면 감사하겠습니다.',
                'attachments' => null,
                'project_term_id' => $projectTerm?->id,
                'course_id' => $course?->id,
                'operating_institution_id' => $institution?->id,
                'project_period_id' => $period?->id,
                'country_id' => $country?->id,
                'reply_content' => null,
                'reply_attachments' => null,
                'reply_status' => 'pending',
                'replied_at' => null,
                'replied_by' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $userIds[4 % count($userIds)] ?? null,
                'title' => '프로그램 일정 문의',
                'content' => '프로그램 일정표를 받고 싶습니다. 언제부터 시작하는지, 주요 일정은 어떻게 되는지 알려주세요.',
                'attachments' => null,
                'project_term_id' => $projectTerm?->id,
                'course_id' => $course?->id,
                'operating_institution_id' => $institution?->id,
                'project_period_id' => $period?->id,
                'country_id' => $country?->id,
                'reply_content' => '<p>프로그램 일정 관련 문의 감사합니다.</p><p>프로그램은 3월부터 시작되며, 주요 일정은 다음과 같습니다:</p><ul><li>3월: 오리엔테이션</li><li>4-6월: 교육 과정</li><li>7월: 현장 실습</li><li>8월: 최종 평가</li></ul><p>상세 일정표는 추후 공지사항을 통해 안내드리겠습니다.</p>',
                'reply_attachments' => null,
                'reply_status' => 'completed',
                'replied_at' => now()->subHours(12),
                'replied_by' => $adminUser?->id,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(12),
            ],
        ];

        foreach ($inquiries as $inquiry) {
            Inquiry::create($inquiry);
        }

        $this->command->info('문의 관리 테스트 데이터가 생성되었습니다.');
    }
}
