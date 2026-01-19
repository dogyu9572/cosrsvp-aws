<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MemberDocumentController extends Controller
{
    /**
     * 문서 제출 처리
     */
    public function store(Request $request)
    {
        $member = session('member');
        if (!$member || !isset($member['id'])) {
            return response()->json([
                'success' => false,
                'message' => '로그인이 필요합니다.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'document_id' => 'nullable|exists:member_documents,id',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png',
        ], [
            'document_id.exists' => '존재하지 않는 문서입니다.',
            'file.required' => '파일을 선택해주세요.',
            'file.file' => '올바른 파일을 선택해주세요.',
            'file.max' => '파일 크기는 10MB 이하여야 합니다.',
            'file.mimes' => 'pdf, doc, docx, zip, rar, jpg, jpeg, png 파일만 업로드 가능합니다.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // 파일 업로드
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('member_documents', $fileName, 'public');

                // document_id가 있으면 기존 문서 업데이트, 없으면 새 문서 생성
                if ($request->document_id) {
                    $document = MemberDocument::findOrFail($request->document_id);

                    // 회원 본인의 문서인지 확인
                    if ($document->member_id != $member['id']) {
                        return response()->json([
                            'success' => false,
                            'message' => '권한이 없습니다.'
                        ], 403);
                    }

                    // 기존 파일이 있으면 삭제
                    if ($document->file_path) {
                        Storage::disk('public')->delete($document->file_path);
                    }

                    // 문서 상태 업데이트
                    $document->file_path = $filePath;
                    $document->submitted_at = now();
                    
                    // 이전 상태가 보완요청이었으면 재제출완료, 아니면 제출완료
                    if ($document->status == 'supplement_requested') {
                        $document->status = 'resubmitted';
                    } else {
                        $document->status = 'submitted';
                    }

                    $document->save();
                } else {
                    // 새 문서 생성
                    $document = MemberDocument::create([
                        'member_id' => $member['id'],
                        'document_name' => 'Self-introduction',
                        'file_path' => $filePath,
                        'submitted_at' => now(),
                        'status' => 'submitted',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => '문서가 성공적으로 제출되었습니다.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => '파일 업로드에 실패했습니다.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('문서 제출 오류: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '문서 제출 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
