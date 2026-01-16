<?php

namespace App\Services\Backoffice;

use App\Models\MailAddressBook;
use App\Models\MailContact;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MailAddressBookService
{
    /**
     * 검색/필터링된 주소록 목록 조회
     */
    public function getAddressBooksWithFilters(\Illuminate\Http\Request $request)
    {
        $query = MailAddressBook::with(['creator', 'contacts', 'members']);

        // 주소록명 검색
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        // 목록 개수 설정
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * 주소록 생성
     */
    public function createAddressBook(array $data, array $contacts = [], $excelFile = null): MailAddressBook
    {
        return DB::transaction(function () use ($data, $contacts, $excelFile) {
            $data['created_by'] = Auth::id();
            $addressBook = MailAddressBook::create($data);

            // 직접 입력된 연락처 추가
            if (!empty($contacts)) {
                foreach ($contacts as $contactData) {
                    // 빈 값 제외
                    if (empty($contactData['name']) || empty($contactData['email'])) {
                        continue;
                    }
                    
                    $contact = MailContact::create([
                        'name' => $contactData['name'],
                        'email' => $contactData['email'],
                        'phone' => $contactData['phone'] ?? null,
                    ]);
                    $addressBook->contacts()->attach($contact->id);
                }
            }

            // 엑셀 파일 처리 (있을 경우)
            if ($excelFile) {
                $this->importFromExcel($addressBook, $excelFile);
            }

            return $addressBook;
        });
    }

    /**
     * 주소록 수정
     */
    public function updateAddressBook(MailAddressBook $addressBook, array $data, array $contacts = [], $excelFile = null): bool
    {
        return DB::transaction(function () use ($addressBook, $data, $contacts, $excelFile) {
            $addressBook->update($data);

            // 직접 입력된 신규 연락처 추가
            if (!empty($contacts)) {
                foreach ($contacts as $contactData) {
                    // 빈 값 제외
                    if (empty($contactData['name']) || empty($contactData['email'])) {
                        continue;
                    }
                    
                    $contact = MailContact::create([
                        'name' => $contactData['name'],
                        'email' => $contactData['email'],
                        'phone' => $contactData['phone'] ?? null,
                    ]);
                    $addressBook->contacts()->attach($contact->id);
                }
            }

            // 엑셀 파일 처리 (있을 경우)
            if ($excelFile) {
                $this->importFromExcel($addressBook, $excelFile);
            }

            return true;
        });
    }

    /**
     * 주소록 삭제
     */
    public function deleteAddressBook(MailAddressBook $addressBook): bool
    {
        return $addressBook->delete();
    }

    /**
     * 연락처 추가
     */
    public function addContact(MailAddressBook $addressBook, array $data): MailContact
    {
        $contact = MailContact::create($data);
        $addressBook->contacts()->attach($contact->id);
        return $contact;
    }

    /**
     * 연락처 수정
     */
    public function updateContact(MailContact $contact, array $data): bool
    {
        return $contact->update($data);
    }

    /**
     * 연락처 삭제
     */
    public function deleteContact(MailAddressBook $addressBook, MailContact $contact): bool
    {
        $addressBook->contacts()->detach($contact->id);
        return $contact->delete();
    }

    /**
     * 회원 추가
     */
    public function addMember(MailAddressBook $addressBook, int $memberId): void
    {
        $addressBook->members()->syncWithoutDetaching([$memberId]);
    }

    /**
     * 회원 제거
     */
    public function removeMember(MailAddressBook $addressBook, int $memberId): void
    {
        $addressBook->members()->detach($memberId);
    }

    /**
     * 엑셀 일괄 등록
     */
    public function importFromExcel(MailAddressBook $addressBook, $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $importedCount = 0;
            $skippedCount = 0;
            
            // 첫 번째 행은 헤더이므로 제외
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // 빈 행 건너뛰기
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }
                
                $name = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');
                $phone = trim($row[2] ?? '');
                
                // 이름과 이메일이 필수
                if (empty($name) || empty($email)) {
                    $skippedCount++;
                    continue;
                }
                
                // 이메일 형식 검증
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skippedCount++;
                    continue;
                }
                
                // 기존 연락처 확인 (이메일 기준)
                $existingContact = MailContact::where('email', $email)->first();
                
                if ($existingContact) {
                    // 기존 연락처가 있으면 주소록에 연결 (중복 제거)
                    $addressBook->contacts()->syncWithoutDetaching([$existingContact->id]);
                } else {
                    // 새 연락처 생성
                    $contact = MailContact::create([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone ?: null,
                    ]);
                    $addressBook->contacts()->attach($contact->id);
                }
                
                $importedCount++;
            }
            
            return [
                'success' => true,
                'message' => "총 {$importedCount}개의 연락처가 등록되었습니다." . ($skippedCount > 0 ? " ({$skippedCount}개 건너뜀)" : ''),
                'imported_count' => $importedCount,
                'skipped_count' => $skippedCount,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '엑셀 파일 처리 중 오류가 발생했습니다: ' . $e->getMessage(),
            ];
        }
    }
}
