<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    /**
     * 이용약관 페이지
     */
    public function index()
    {
        $gNum = null;
        $gName = "Terms Of Use";
        
        return view('terms.index', compact('gNum', 'gName'));
    }
}
