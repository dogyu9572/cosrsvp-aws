<?php

namespace App\Services\Backoffice;

use App\Models\Member;
use Illuminate\Support\Collection;

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
}
