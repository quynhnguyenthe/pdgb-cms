<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Repositories\ClubRequestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * @var ClubRequestRepository
     */
    private $clubRequestRepository;

    public function __construct(
        ClubRequestRepository $clubRequestRepository
    )
    {
        $this->clubRequestRepository = $clubRequestRepository;
    }

    public function userInfo()
    {
        $user = Auth::guard('google-member')->user();
        $user['request_club'] = $this->clubRequestRepository->getClubRquestWithMember($user->id);

        return response()->json(['message' => 'success', 'data' => $user], 200);
    }
}
