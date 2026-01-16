<?php

namespace App\Services\Backoffice;

use App\Models\MemberNote;
use App\Models\MemberNoteFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MemberNoteService
{
    /**
     * 검색/필터링된 회원비고 목록 조회
     */
    public function getMemberNotesWithFilters(\Illuminate\Http\Request $request)
    {
        $query = MemberNote::with(['member', 'creator', 'files']);

        // 회원 필터
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        // 상태 필터
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 등록일 필터
        if ($request->filled('registration_date_from')) {
            $query->whereDate('created_at', '>=', $request->registration_date_from);
        }
        if ($request->filled('registration_date_to')) {
            $query->whereDate('created_at', '<=', $request->registration_date_to);
        }

        // 검색 필터
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $searchType = $request->get('search_type', '');
            
            if (empty($searchType)) {
                // 전체 검색
                $query->where(function($q) use ($keyword) {
                    $q->where('korean_title', 'like', '%' . $keyword . '%')
                      ->orWhere('english_title', 'like', '%' . $keyword . '%')
                      ->orWhere('korean_content', 'like', '%' . $keyword . '%')
                      ->orWhere('english_content', 'like', '%' . $keyword . '%');
                });
            } else {
                // 특정 필드 검색
                switch ($searchType) {
                    case 'title':
                        $query->where(function($q) use ($keyword) {
                            $q->where('korean_title', 'like', '%' . $keyword . '%')
                              ->orWhere('english_title', 'like', '%' . $keyword . '%');
                        });
                        break;
                    case 'content':
                        $query->where(function($q) use ($keyword) {
                            $q->where('korean_content', 'like', '%' . $keyword . '%')
                              ->orWhere('english_content', 'like', '%' . $keyword . '%');
                        });
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
     * 회원비고 생성 및 파일 업로드
     */
    public function createMemberNote(array $data, array $files = []): MemberNote
    {
        $data['created_by'] = Auth::id();
        $memberNote = MemberNote::create($data);

        // 파일 업로드 처리
        if (!empty($files)) {
            $this->uploadFiles($memberNote, $files);
        }

        return $memberNote;
    }

    /**
     * 회원비고 수정
     */
    public function updateMemberNote(MemberNote $memberNote, array $data, array $files = [], array $deletedFileIds = []): bool
    {
        $result = $memberNote->update($data);

        // 파일 삭제
        if (!empty($deletedFileIds)) {
            $this->deleteFiles($deletedFileIds);
        }

        // 새 파일 업로드
        if (!empty($files)) {
            $this->uploadFiles($memberNote, $files);
        }

        return $result;
    }

    /**
     * 회원비고 삭제
     */
    public function deleteMemberNote(MemberNote $memberNote): bool
    {
        // 첨부파일 삭제
        foreach ($memberNote->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        return $memberNote->delete();
    }

    /**
     * 파일 업로드 처리
     */
    private function uploadFiles(MemberNote $memberNote, array $files): void
    {
        foreach ($files as $file) {
            if ($file->isValid()) {
                $path = $file->store('member_notes', 'public');
                
                MemberNoteFile::create([
                    'member_note_id' => $memberNote->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }

    /**
     * 파일 삭제 처리
     */
    private function deleteFiles(array $fileIds): void
    {
        $files = MemberNoteFile::whereIn('id', $fileIds)->get();
        
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }
    }
}
