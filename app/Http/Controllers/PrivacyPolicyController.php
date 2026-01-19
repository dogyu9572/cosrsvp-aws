<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    /**
     * 개인정보처리방침 페이지
     */
    public function index()
    {
        $gNum = null;
        $gName = "Privacy Policy";
        
        return view('privacy-policy.index', compact('gNum', 'gName'));
    }
}
