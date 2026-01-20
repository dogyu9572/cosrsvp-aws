<?php

namespace App\Services\Backoffice;

use App\Models\AccessCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessCodeService
{
    /**
     * 코드 목록 조회
     */
    public function getCodes()
    {
        return AccessCode::orderBy('created_at', 'desc')->get();
    }

    /**
     * 코드 생성
     */
    public function createCode(Request $request): AccessCode
    {
        $data = $request->validate([
            'code' => 'required|string|unique:access_codes,code',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;

        return AccessCode::create($data);
    }

    /**
     * 코드 수정
     */
    public function updateCode(AccessCode $accessCode, Request $request): bool
    {
        $data = $request->validate([
            'code' => 'required|string|unique:access_codes,code,' . $accessCode->id,
            'is_active' => 'boolean',
        ]);

        try {
            $accessCode->update($data);
            return true;
        } catch (\Exception $e) {
            Log::error('코드 수정 실패', [
                'code_id' => $accessCode->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

}