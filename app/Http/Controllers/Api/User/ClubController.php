<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\MemberRequest;
use App\Repositories\ClubMemberRepository;
use App\Repositories\ClubRepository;
use App\Repositories\MemberRequestsRepository;
use Illuminate\Http\Request;
use Validator;

class ClubController extends Controller
{
    protected $clubRepository;
    protected $memberRequestsRepository;
    protected $clubMemberRepository;

    public function __construct(
        ClubRepository           $clubRepository,
        MemberRequestsRepository $memberRequestsRepository,
        ClubMemberRepository $clubMemberRepository,
    )
    {
        $this->clubRepository = $clubRepository;
        $this->memberRequestsRepository = $memberRequestsRepository;
        $this->clubMemberRepository = $clubMemberRepository;
    }

    public function list()
    {
        $id = 1;
        $clubs = $this->clubRepository->getClubByManagerID($id);

        return response()->json(['message' => 'success', 'data' => $clubs], 200);
    }

    public function requestJoin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'club_id' => 'required|exists:clubs,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $club = $this->clubRepository->find($request->get('club_id'));
        if ($club['manager_id'] == $request->get('member_id')) {
            return response()->json(['error' => 'Cannot join your own club'], 422);
        }

        $memberRequest = [
            'member_id' => $request->get('member_id'),
            'club_id' => $request->get('club_id'),
            'status' => MemberRequest::NEW
        ];
        try {
            $memberRequest = $this->memberRequestsRepository->create($memberRequest);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }


        return response()->json(['message' => 'success', 'data' => $memberRequest], 200);
    }

    public function listRequestJoin(Request $request, int $id)
    {
        $requests = $this->memberRequestsRepository->getWithClub($id);

        return response()->json(['message' => 'success', 'data' => $requests], 200);
    }

    public function reviewRequestJoin(Request $request, int $request_id)
    {
        $requestJoin = $this->memberRequestsRepository->getById($request_id);
        if ($requestJoin['status'] == MemberRequest::NEW) {
            $club = $this->clubRepository->getById($requestJoin['club_id'])->toArray();
            if ($club['members_count'] >= $club['number_of_members']) {
                $dataRequestJoin['status'] = MemberRequest::REJECT;
                $this->memberRequestsRepository->update($requestJoin, $dataRequestJoin);
                return response()->json(['error' => 'The number of members in the club has reached the maximum limit'], 422);
            }

            $clubMember = [
                'club_id' => $club['id'],
                'member_id' => $requestJoin['member_id']
            ];
            $this->clubMemberRepository->create($clubMember);
            $dataRequestJoin['status'] = MemberRequest::APPROVE;
            $this->memberRequestsRepository->update($requestJoin, $dataRequestJoin);

            return response()->json(['message' => 'success'], 200);
        } else {
            return response()->json(['error' => 'Đơn đã được duyệt'], 400);
        }
    }
}
