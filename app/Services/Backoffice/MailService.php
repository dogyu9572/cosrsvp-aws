<?php

namespace App\Services\Backoffice;

use App\Models\Mail;
use App\Models\MailFile;
use App\Models\MailRecipientFilter;
use App\Models\MailAddressBookSelection;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MailService
{
    /**
     * 검색/필터링된 메일 목록 조회
     */
    public function getMailsWithFilters(\Illuminate\Http\Request $request)
    {
        $query = Mail::with(['creator', 'files', 'recipientFilters', 'addressBookSelections.addressBook']);

        // 발송대상 필터
        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }

        // 검색 필터
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('dispatch_subject', 'like', '%' . $keyword . '%')
                  ->orWhere('content', 'like', '%' . $keyword . '%');
            });
        }

        // 목록 개수 설정
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * 메일 생성
     */
    public function createMail(array $data, array $files = []): Mail
    {
        $data['created_by'] = Auth::id();
        
        // 발송여부가 일반저장이면 scheduled_at은 null
        if ($data['dispatch_status'] === 'saved') {
            $data['scheduled_at'] = null;
        }
        
        // 발송대상 타입에 따라 처리
        $recipientType = $data['recipient_type'];
        unset($data['recipient_type']);
        
        $mail = Mail::create($data);
        
        // 기수별 발송 필터 저장
        if ($recipientType === 'project_term' && isset($data['recipient_filters'])) {
            foreach ($data['recipient_filters'] as $filter) {
                $filter['mail_id'] = $mail->id;
                MailRecipientFilter::create($filter);
            }
        }
        
        // 주소록 선택 저장
        if ($recipientType === 'address_book' && isset($data['address_book_ids'])) {
            foreach ($data['address_book_ids'] as $addressBookId) {
                MailAddressBookSelection::create([
                    'mail_id' => $mail->id,
                    'address_book_id' => $addressBookId,
                ]);
            }
        }
        
        // 파일 업로드 처리
        if (!empty($files)) {
            $this->uploadFiles($mail, $files);
        }
        
        // TODO: SMTP 전송 로직은 나중에 구현
        // if ($data['dispatch_status'] === 'scheduled') {
        //     $this->scheduleMail($mail);
        // }

        return $mail;
    }

    /**
     * 메일 수정
     */
    public function updateMail(Mail $mail, array $data, array $files = [], array $deletedFileIds = []): bool
    {
        // 발송여부가 일반저장이면 scheduled_at은 null
        if ($data['dispatch_status'] === 'saved') {
            $data['scheduled_at'] = null;
        }
        
        // 발송대상 타입에 따라 처리
        $recipientType = $data['recipient_type'];
        unset($data['recipient_type']);
        
        $result = $mail->update($data);
        
        // 기존 필터/선택 삭제 후 재생성
        $mail->recipientFilters()->delete();
        $mail->addressBookSelections()->delete();
        
        // 기수별 발송 필터 저장
        if ($recipientType === 'project_term' && isset($data['recipient_filters'])) {
            foreach ($data['recipient_filters'] as $filter) {
                $filter['mail_id'] = $mail->id;
                MailRecipientFilter::create($filter);
            }
        }
        
        // 주소록 선택 저장
        if ($recipientType === 'address_book' && isset($data['address_book_ids'])) {
            foreach ($data['address_book_ids'] as $addressBookId) {
                MailAddressBookSelection::create([
                    'mail_id' => $mail->id,
                    'address_book_id' => $addressBookId,
                ]);
            }
        }
        
        // 파일 삭제
        if (!empty($deletedFileIds)) {
            $this->deleteFiles($deletedFileIds);
        }

        // 새 파일 업로드
        if (!empty($files)) {
            $this->uploadFiles($mail, $files);
        }
        
        // TODO: SMTP 전송 로직은 나중에 구현

        return $result;
    }

    /**
     * 메일 삭제
     */
    public function deleteMail(Mail $mail): bool
    {
        // 첨부파일 삭제
        foreach ($mail->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        return $mail->delete();
    }

    /**
     * 기수별 필터로 회원 조회 (미리보기용)
     */
    public function getRecipientsByProjectTerm(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Member::active();
        
        foreach ($filters as $filter) {
            $query->where(function($q) use ($filter) {
                if (!empty($filter['project_term_id'])) {
                    $q->where('project_term_id', $filter['project_term_id']);
                }
                if (!empty($filter['course_id'])) {
                    $q->where('course_id', $filter['course_id']);
                }
                if (!empty($filter['operating_institution_id'])) {
                    $q->where('operating_institution_id', $filter['operating_institution_id']);
                }
                if (!empty($filter['project_period_id'])) {
                    $q->where('project_period_id', $filter['project_period_id']);
                }
                if (!empty($filter['country_id'])) {
                    $q->where('country_id', $filter['country_id']);
                }
            });
        }
        
        return $query->get(['id', 'name', 'email']);
    }

    /**
     * 주소록으로 회원/연락처 조회 (미리보기용)
     */
    public function getRecipientsByAddressBook(array $addressBookIds): array
    {
        $members = collect();
        $contacts = collect();
        
        foreach ($addressBookIds as $addressBookId) {
            $addressBook = \App\Models\MailAddressBook::with(['members', 'contacts'])->find($addressBookId);
            if ($addressBook) {
                $members = $members->merge($addressBook->members);
                $contacts = $contacts->merge($addressBook->contacts);
            }
        }
        
        return [
            'members' => $members->unique('id')->values(),
            'contacts' => $contacts->unique('id')->values(),
        ];
    }

    /**
     * 파일 업로드 처리
     */
    private function uploadFiles(Mail $mail, array $files): void
    {
        foreach ($files as $file) {
            if ($file->isValid()) {
                $path = $file->store('mails', 'public');
                
                MailFile::create([
                    'mail_id' => $mail->id,
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
        $files = MailFile::whereIn('id', $fileIds)->get();
        
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }
    }
}
