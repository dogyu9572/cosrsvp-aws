<?php

namespace App\Services\Backoffice;

use App\Models\Member;
use App\Models\MemberModificationLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberService
{
    /**
     * 프로젝트 기수 조건으로 회원 필터링
     * 
     * @param array $filters
     * @return Collection
     */
    public function getMembersByProjectTerm(array $filters): Collection
    {
        $query = Member::query();
        
        // 프로젝트 기수 ID로 필터링
        if (!empty($filters['project_term_id'])) {
            $query->where('project_term_id', $filters['project_term_id']);
        }
        
        // 과정 ID로 필터링
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        
        // 운영기관 ID로 필터링
        if (!empty($filters['operating_institution_id'])) {
            $query->where('operating_institution_id', $filters['operating_institution_id']);
        }
        
        // 프로젝트기간 ID로 필터링
        if (!empty($filters['project_period_id'])) {
            $query->where('project_period_id', $filters['project_period_id']);
        }
        
        // 국가 ID로 필터링
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }
        
        // 활성화된 회원만 조회 (is_active 컬럼이 있다면)
        if (in_array('is_active', (new Member())->getFillable())) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('name')->get(['id', 'name']);
    }

    /**
     * 검색/필터링된 회원 목록 조회
     */
    public function getMembersWithFilters(\Illuminate\Http\Request $request)
    {
        $query = Member::with(['projectTerm', 'course', 'operatingInstitution', 'projectPeriod', 'country']);

        // 프로젝트 기수 필터
        if ($request->filled('filter_project_term_id')) {
            $query->where('project_term_id', $request->filter_project_term_id);
        }

        // 과정 필터
        if ($request->filled('filter_course_id')) {
            $query->where('course_id', $request->filter_course_id);
        }

        // 운영기관 필터
        if ($request->filled('filter_operating_institution_id')) {
            $query->where('operating_institution_id', $request->filter_operating_institution_id);
        }

        // 프로젝트기간 필터
        if ($request->filled('filter_project_period_id')) {
            $query->where('project_period_id', $request->filter_project_period_id);
        }

        // 국가 필터
        if ($request->filled('filter_country_id')) {
            $query->where('country_id', $request->filter_country_id);
        }

        // 가입일 필터
        if ($request->filled('join_date_from')) {
            $query->whereDate('created_at', '>=', $request->join_date_from);
        }
        if ($request->filled('join_date_to')) {
            $query->whereDate('created_at', '<=', $request->join_date_to);
        }

        // 검색 필터
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $searchType = $request->get('search_type', '');
            
            if (empty($searchType)) {
                // 전체 검색
                $query->where(function($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('email', 'like', '%' . $keyword . '%')
                      ->orWhere('phone_kr', 'like', '%' . $keyword . '%')
                      ->orWhere('phone_local', 'like', '%' . $keyword . '%')
                      ->orWhere('passport_number', 'like', '%' . $keyword . '%')
                      ->orWhere('alien_registration_number', 'like', '%' . $keyword . '%');
                });
            } else {
                // 특정 필드 검색
                switch ($searchType) {
                    case 'name':
                        $query->where('name', 'like', '%' . $keyword . '%');
                        break;
                    case 'email':
                        $query->where('email', 'like', '%' . $keyword . '%');
                        break;
                    case 'phone':
                        $query->where(function($q) use ($keyword) {
                            $q->where('phone_kr', 'like', '%' . $keyword . '%')
                              ->orWhere('phone_local', 'like', '%' . $keyword . '%');
                        });
                        break;
                    case 'passport':
                        $query->where('passport_number', 'like', '%' . $keyword . '%');
                        break;
                    case 'alien_registration':
                        $query->where('alien_registration_number', 'like', '%' . $keyword . '%');
                        break;
                }
            }
        }

        // 목록 개수 설정
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * 회원 생성
     */
    public function createMember(array $data): Member
    {
        return Member::create($data);
    }

    /**
     * 회원 정보 수정
     */
    public function updateMember(Member $member, array $data, $modifiedBy = null): bool
    {
        // 기존 데이터 저장 (비교용) - 관계 포함
        $member->load(['projectTerm', 'course', 'operatingInstitution', 'projectPeriod', 'country']);
        $oldData = $member->getAttributes();
        $oldRelations = [
            'project_term_id' => $member->projectTerm ? ($member->projectTerm->name_ko ?? $member->projectTerm->name ?? null) : null,
            'course_id' => $member->course ? ($member->course->name_ko ?? $member->course->name ?? null) : null,
            'operating_institution_id' => $member->operatingInstitution ? ($member->operatingInstitution->name_ko ?? $member->operatingInstitution->name ?? null) : null,
            'project_period_id' => $member->projectPeriod ? ($member->projectPeriod->name_ko ?? $member->projectPeriod->name ?? null) : null,
            'country_id' => $member->country ? ($member->country->name_ko ?? $member->country->name ?? null) : null,
        ];
        
        // 데이터 업데이트
        $result = $member->update($data);
        
        // 업데이트 실패 시 바로 반환
        if (!$result) {
            return false;
        }
        
        // 새로고침하여 변경된 데이터 가져오기
        $member->refresh();
        $member->load(['projectTerm', 'course', 'operatingInstitution', 'projectPeriod', 'country']);
        $newData = $member->getAttributes();
        $newRelations = [
            'project_term_id' => $member->projectTerm ? ($member->projectTerm->name_ko ?? $member->projectTerm->name ?? null) : null,
            'course_id' => $member->course ? ($member->course->name_ko ?? $member->course->name ?? null) : null,
            'operating_institution_id' => $member->operatingInstitution ? ($member->operatingInstitution->name_ko ?? $member->operatingInstitution->name ?? null) : null,
            'project_period_id' => $member->projectPeriod ? ($member->projectPeriod->name_ko ?? $member->projectPeriod->name ?? null) : null,
            'country_id' => $member->country ? ($member->country->name_ko ?? $member->country->name ?? null) : null,
        ];

        // 수정 로그 기록 (업데이트 성공 후에만)
        if ($modifiedBy) {
            try {
                $changes = $this->getChangedFields($oldData, $newData, $oldRelations, $newRelations);
                if (!empty($changes)) {
                    $description = implode(', ', $changes);
                    $this->logModification($member->id, $modifiedBy, 'update', $description);
                }
                // 변경사항이 없으면 로그를 기록하지 않음
            } catch (\Exception $e) {
                // 로그 기록 실패해도 수정은 성공한 것으로 처리
                \Log::error('회원 수정 로그 기록 실패: ' . $e->getMessage());
            }
        }

        return true;
    }
    
    /**
     * 변경된 필드 목록 생성
     */
    private function getChangedFields(array $oldData, array $newData, array $oldRelations, array $newRelations): array
    {
        $fieldLabels = [
            'login_id' => '아이디',
            'name' => '성명',
            'gender' => '성별',
            'email' => '이메일',
            'phone_kr' => '한국 전화번호',
            'phone_local' => '현지 전화번호',
            'birth_date' => '생년월일',
            'occupation' => '직업',
            'major' => '전공',
            'affiliation' => '소속',
            'department' => '부서',
            'position' => '직위',
            'passport_number' => '여권번호',
            'passport_expiry' => '여권유효기간',
            'alien_registration_number' => '외국인등록번호',
            'alien_registration_expiry' => '외국인등록증 유효기간',
            'project_term_id' => '기수',
            'course_id' => '과정',
            'operating_institution_id' => '운영기관',
            'project_period_id' => '프로젝트기간',
            'country_id' => '국가',
            'hotel_name' => '호텔명',
            'hotel_address' => '호텔주소',
            'hotel_address_detail' => '호텔 상세주소',
            'training_period' => '연수기간',
            'visa_type' => '비자종류',
            'cultural_experience' => '문화체험',
            'account_info' => '계좌번호',
            'insurance_status' => '보험가입여부',
            'clothing_size' => '옷 사이즈',
            'dietary_restrictions' => '특이식성',
            'special_requests' => '특이사항 및 요청사항',
            'departure_location' => '출발지',
            'arrival_location' => '도착지',
            'entry_date' => '입국일자',
            'exit_date' => '출국일자',
            'entry_flight' => '입국 항공편',
            'exit_flight' => '출국 항공편',
            'ticket_file' => '항공권 파일',
            'is_active' => '활성화 여부',
        ];
        
        $changes = [];
        $ignoreFields = ['password', 'updated_at', 'created_at', 'deleted_at'];
        
        foreach ($fieldLabels as $field => $label) {
            if (in_array($field, $ignoreFields)) {
                continue;
            }
            
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newData[$field] ?? null;
            
            // 관계 필드 처리
            if (in_array($field, ['project_term_id', 'course_id', 'operating_institution_id', 'project_period_id', 'country_id'])) {
                $oldValue = $oldRelations[$field] ?? null;
                $newValue = $newRelations[$field] ?? null;
            }
            
            // 날짜 필드 포맷팅
            if (in_array($field, ['birth_date', 'entry_date', 'exit_date'])) {
                try {
                    $oldValue = $oldValue ? (is_string($oldValue) ? $oldValue : (is_object($oldValue) && method_exists($oldValue, 'format') ? $oldValue->format('Y-m-d') : (string)$oldValue)) : null;
                    $newValue = $newValue ? (is_string($newValue) ? $newValue : (is_object($newValue) && method_exists($newValue, 'format') ? $newValue->format('Y-m-d') : (string)$newValue)) : null;
                } catch (\Exception $e) {
                    // 날짜 포맷팅 실패 시 원본 값 유지
                }
            }
            
            // 월 필드 포맷팅 (passport_expiry, alien_registration_expiry)
            if (in_array($field, ['passport_expiry', 'alien_registration_expiry'])) {
                try {
                    $oldValue = $oldValue ? (is_string($oldValue) ? $oldValue : (is_object($oldValue) && method_exists($oldValue, 'format') ? $oldValue->format('Y-m') : (string)$oldValue)) : null;
                    $newValue = $newValue ? (is_string($newValue) ? $newValue : (is_object($newValue) && method_exists($newValue, 'format') ? $newValue->format('Y-m') : (string)$newValue)) : null;
                } catch (\Exception $e) {
                    // 날짜 포맷팅 실패 시 원본 값 유지
                }
            }
            
            // 성별 필드 처리
            if ($field === 'gender') {
                $oldValue = $oldValue === 'male' ? '남자' : ($oldValue === 'female' ? '여자' : null);
                $newValue = $newValue === 'male' ? '남자' : ($newValue === 'female' ? '여자' : null);
            }
            
            // 보험가입여부 처리
            if ($field === 'insurance_status') {
                $oldValue = $oldValue === 'yes' ? '예' : ($oldValue === 'no' ? '아니오' : null);
                $newValue = $newValue === 'yes' ? '예' : ($newValue === 'no' ? '아니오' : null);
            }
            
            // 활성화 여부 처리
            if ($field === 'is_active') {
                $oldValue = $oldValue ? '활성' : '비활성';
                $newValue = $newValue ? '활성' : '비활성';
            }
            
            // 항공권 파일 처리 (파일명만 표시)
            if ($field === 'ticket_file') {
                if ($oldValue) {
                    $oldFileName = basename($oldValue);
                    $oldValue = preg_replace('/_\d+\./', '.', $oldFileName);
                }
                if ($newValue) {
                    $newFileName = basename($newValue);
                    $newValue = preg_replace('/_\d+\./', '.', $newFileName);
                }
            }
            
            // 값 비교 (null과 빈 문자열을 동일하게 처리)
            $oldValue = $oldValue === null || $oldValue === '' ? null : $oldValue;
            $newValue = $newValue === null || $newValue === '' ? null : $newValue;
            
            if ($oldValue != $newValue) {
                $oldDisplay = $oldValue ?? '(없음)';
                $newDisplay = $newValue ?? '(없음)';
                $changes[] = "{$label}: {$oldDisplay} → {$newDisplay}";
            }
        }
        
        return $changes;
    }

    /**
     * 회원 삭제
     */
    public function deleteMember(Member $member): bool
    {
        return $member->delete();
    }

    /**
     * 비밀번호 초기화
     */
    public function resetPassword(Member $member, $modifiedBy = null): bool
    {
        $defaultPassword = 'COS1234';
        $member->password = $defaultPassword;
        $result = $member->save();

        // 수정 로그 기록
        if ($result && $modifiedBy) {
            $this->logModification($member->id, $modifiedBy, 'password_reset', '비밀번호가 초기화되었습니다.');
        }

        return $result;
    }

    /**
     * 회원 상세 정보 조회
     */
    public function getMemberDetail(int $id): Member
    {
        return Member::with([
            'projectTerm',
            'course',
            'operatingInstitution',
            'projectPeriod',
            'country',
            'documents',
            'modificationLogs.modifier'
        ])->findOrFail($id);
    }

    /**
     * 선택 회원에게 메일 발송 (구조만 준비)
     */
    public function sendEmailToMembers(array $memberIds, $mailListId): bool
    {
        // TODO: 메일 발송 로직 구현
        return true;
    }

    /**
     * 수정 로그 기록
     */
    private function logModification(int $memberId, int $modifiedBy, string $type, string $description): void
    {
        MemberModificationLog::create([
            'member_id' => $memberId,
            'modified_by' => $modifiedBy,
            'modification_type' => $type,
            'description' => $description,
        ]);
    }
}
