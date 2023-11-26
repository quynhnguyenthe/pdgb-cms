<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct()
    {

    }

    public function userInfo()
    {
        $user = Auth::guard('google-member')->user();

        return response()->json(['message' => 'success', 'data' => $user], 200);
    }
}
