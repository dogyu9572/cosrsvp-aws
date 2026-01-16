<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMailAddressBookRequest;
use App\Http\Requests\UpdateMailAddressBookRequest;
use App\Services\Backoffice\MailAddressBookService;
use App\Models\MailAddressBook;
use App\Models\MailContact;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MailAddressBookController extends Controller
{
    protected $mailAddressBookService;

    public function __construct(MailAddressBookService $mailAddressBookService)
    {
        $this->mailAddressBookService = $mailAddressBookService;
    }

    /**
     * 주소록 목록 표시
     */
    public function index(Request $request)
    {
        $addressBooks = $this->mailAddressBookService->getAddressBooksWithFilters($request);
        return view('backoffice.mail-address-books.index', compact('addressBooks'));
    }

    /**
     * 주소록 등록 폼 표시
     */
    public function create()
    {
        return view('backoffice.mail-address-books.create');
    }

    /**
     * 주소록 저장
     */
    public function store(StoreMailAddressBookRequest $request)
    {
        $data = $request->validated();
        
        // 연락처 배열 추출 (빈 값 제거)
        $contacts = [];
        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contact) {
                // 이름과 이메일이 모두 있는 경우만 추가
                if (!empty($contact['name']) && !empty($contact['email'])) {
                    $contacts[] = [
                        'name' => $contact['name'],
                        'email' => $contact['email'],
                        'phone' => $contact['phone'] ?? null,
                    ];
                }
            }
        }
        
        // 엑셀 파일 추출
        $excelFile = $request->hasFile('excel_file') ? $request->file('excel_file') : null;
        
        // 주소록명만 추출 (contacts, excel_file 제외)
        $addressBookData = ['name' => $data['name']];
        
        $this->mailAddressBookService->createAddressBook($addressBookData, $contacts, $excelFile);

        return redirect()->route('backoffice.mail-address-books.index')
            ->with('success', '주소록이 등록되었습니다.');
    }

    /**
     * 주소록 수정 폼 표시 (상세 페이지)
     */
    public function edit($id)
    {
        $addressBook = MailAddressBook::with(['contacts', 'members'])->findOrFail($id);
        $members = Member::active()->orderBy('name')->get(['id', 'name', 'email']);
        
        return view('backoffice.mail-address-books.edit', compact('addressBook', 'members'));
    }

    /**
     * 주소록 업데이트
     */
    public function update(UpdateMailAddressBookRequest $request, $id)
    {
        $addressBook = MailAddressBook::findOrFail($id);
        $data = $request->validated();
        
        // 연락처 배열 추출 (빈 값 제거)
        $contacts = [];
        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contact) {
                // 이름과 이메일이 모두 있는 경우만 추가
                if (!empty($contact['name']) && !empty($contact['email'])) {
                    $contacts[] = [
                        'name' => $contact['name'],
                        'email' => $contact['email'],
                        'phone' => $contact['phone'] ?? null,
                    ];
                }
            }
        }
        
        // 엑셀 파일 추출
        $excelFile = $request->hasFile('excel_file') ? $request->file('excel_file') : null;
        
        // 주소록명만 추출 (contacts, excel_file 제외)
        $addressBookData = ['name' => $data['name']];
        
        $this->mailAddressBookService->updateAddressBook($addressBook, $addressBookData, $contacts, $excelFile);

        return redirect()->route('backoffice.mail-address-books.index')
            ->with('success', '주소록이 수정되었습니다.');
    }

    /**
     * 주소록 삭제
     */
    public function destroy($id)
    {
        $addressBook = MailAddressBook::findOrFail($id);
        $this->mailAddressBookService->deleteAddressBook($addressBook);

        return redirect()->route('backoffice.mail-address-books.index')
            ->with('success', '주소록이 삭제되었습니다.');
    }

    /**
     * 연락처 추가 (AJAX)
     */
    public function addContact(Request $request, $addressBook)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $addressBook = MailAddressBook::findOrFail($addressBook);
        $contact = $this->mailAddressBookService->addContact($addressBook, $request->only(['name', 'email', 'phone']));

        return response()->json([
            'success' => true,
            'contact' => $contact,
            'message' => '연락처가 추가되었습니다.',
        ]);
    }

    /**
     * 연락처 수정 (AJAX)
     */
    public function updateContact(Request $request, $addressBook, $contact)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $addressBook = MailAddressBook::findOrFail($addressBook);
        $contact = MailContact::findOrFail($contact);
        
        $this->mailAddressBookService->updateContact($contact, $request->only(['name', 'email', 'phone']));

        return response()->json([
            'success' => true,
            'contact' => $contact->fresh(),
            'message' => '연락처가 수정되었습니다.',
        ]);
    }

    /**
     * 연락처 삭제 (AJAX)
     */
    public function deleteContact($addressBook, $contact)
    {
        $addressBook = MailAddressBook::findOrFail($addressBook);
        $contact = MailContact::findOrFail($contact);
        
        $this->mailAddressBookService->deleteContact($addressBook, $contact);

        return response()->json([
            'success' => true,
            'message' => '연락처가 삭제되었습니다.',
        ]);
    }

    /**
     * 회원 추가 (AJAX)
     */
    public function addMember(Request $request, $addressBook)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $addressBook = MailAddressBook::findOrFail($addressBook);
        $this->mailAddressBookService->addMember($addressBook, $request->member_id);

        return response()->json([
            'success' => true,
            'message' => '회원이 추가되었습니다.',
        ]);
    }

    /**
     * 회원 제거 (AJAX)
     */
    public function removeMember($addressBook, $member)
    {
        $addressBook = MailAddressBook::findOrFail($addressBook);
        $this->mailAddressBookService->removeMember($addressBook, $member);

        return response()->json([
            'success' => true,
            'message' => '회원이 제거되었습니다.',
        ]);
    }

    /**
     * 엑셀 샘플 다운로드
     */
    public function downloadExcelSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // 헤더 설정
        $sheet->setCellValue('A1', '이름');
        $sheet->setCellValue('B1', '이메일');
        $sheet->setCellValue('C1', '연락처');
        
        // 샘플 데이터
        $sampleData = [
            ['홍길동', 'hong@example.com', '010-1234-5678'],
            ['김철수', 'kim@example.com', '010-2345-6789'],
        ];
        
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->setCellValue('C' . $row, $data[2]);
            $row++;
        }
        
        // 컬럼 너비 자동 조정
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        
        // 헤더 스타일
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        
        // 파일 다운로드
        $writer = new Xls($spreadsheet);
        $filename = '주소록_샘플.xls';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * 엑셀 일괄 등록
     */
    public function importExcel(Request $request, $addressBook)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,xlsx,xls|max:5120', // 최대 5MB
        ]);

        $addressBook = MailAddressBook::findOrFail($addressBook);
        $result = $this->mailAddressBookService->importFromExcel($addressBook, $request->file('excel_file'));

        return response()->json($result);
    }
}
