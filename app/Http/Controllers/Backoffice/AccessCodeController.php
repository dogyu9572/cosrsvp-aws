<?php

namespace App\Http\Controllers\Backoffice;

use App\Models\AccessCode;
use App\Services\Backoffice\AccessCodeService;
use Illuminate\Http\Request;

class AccessCodeController extends BaseController
{
    public function __construct(
        private AccessCodeService $accessCodeService
    ) {}

    /**
     * 코드 목록 조회 (AJAX)
     */
    public function index()
    {
        $codes = $this->accessCodeService->getCodes();
        return response()->json([
            'success' => true,
            'data' => $codes
        ]);
    }

    /**
     * 코드 생성
     */
    public function store(Request $request)
    {
        try {
            $code = $this->accessCodeService->createCode($request);
            return response()->json([
                'success' => true,
                'message' => '코드가 등록되었습니다.',
                'data' => $code
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '입력값을 확인해주세요.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '코드 등록에 실패했습니다.'
            ], 500);
        }
    }


    /**
     * 코드 수정
     */
    public function update(Request $request, AccessCode $accessCode)
    {
        try {
            $result = $this->accessCodeService->updateCode($accessCode, $request);
            
            if ($result) {
                $accessCode->refresh();
                return response()->json([
                    'success' => true,
                    'message' => '코드가 수정되었습니다.',
                    'data' => $accessCode
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => '코드 수정에 실패했습니다.'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '입력값을 확인해주세요.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '코드 수정에 실패했습니다.'
            ], 500);
        }
    }
}