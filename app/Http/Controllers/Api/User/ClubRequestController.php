<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubRequest;
use App\Models\MemberRequest;
use App\Repositories\ClubMemberRepository;
use App\Repositories\ClubRepository;
use App\Repositories\ClubRequestRepository;
use App\Repositories\ClubRequestSportsDisciplineRepository;
use App\Repositories\MemberRequestsRepository;
use App\Repositories\MemberSportsDisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class ClubRequestController extends Controller
{
    private $clubRequestRepository;
    private $clubRepository;
    private $clubRequestSportsDisciplineRepository;
    private $clubMemberRepository;
    private $memberSportsDisciplineRepository;
    private $memberRequestRepository;


    /**
     * Create a new ClubRequestController instance.
     *
     * @return void
     */
    public function __construct(
        ClubRequestRepository             $clubRequestRepository,
        ClubRepository                    $clubRepository,
        ClubRequestSportsDisciplineRepository $clubRequestSportsDisciplineRepository,
        ClubMemberRepository $clubMemberRepository,
        MemberSportsDisciplineRepository $memberSportsDisciplineRepository,
        MemberRequestsRepository $memberRequestRepository,
    )
    {
        $this->clubRequestRepository = $clubRequestRepository;
        $this->clubRepository = $clubRepository;
        $this->clubRequestSportsDisciplineRepository = $clubRequestSportsDisciplineRepository;
        $this->clubMemberRepository = $clubMemberRepository;
        $this->memberSportsDisciplineRepository = $memberSportsDisciplineRepository;
        $this->memberRequestRepository = $memberRequestRepository;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'club_name' => 'required',
            'number_of_members' => 'required',
            'sports_discipline_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = Auth::guard('google-member')->user();
        $userId = $user['id'];
        $clubMember = $this->clubMemberRepository->checkExistsClubWithMember($userId);
        if ($clubMember) {
            return response()->json(['error' => 'Bạn đã ở trong 1 club khác, không thể tạo club'], 422);
        }

        $clubRequestOld = $this->clubRequestRepository->getClubRquestWithMember($userId);
        if ($clubRequestOld) {
            return response()->json(['error' => 'Bạn đã có 1 đơn chờ duyệt'], 422);
        }


        $clubRequestData = [
            'club_name' => $request->get('club_name'),
            'manager_id' => $user['id'],
            'number_of_members' => $request->get('number_of_members'),
            'description' => $request->get('description'),
            'type' => ClubRequest::TYPE['create'],
            'status' => ClubRequest::NEW,
        ];
        DB::beginTransaction();
        try {
            $memberRequests = $this->memberRequestRepository->getMemberRequest($userId);
            foreach ($memberRequests as $memberRequest) {
                $this->memberRequestRepository->update($memberRequest, ['status' => MemberRequest::CANCEL]);
            }
            $clubRequest = $this->clubRequestRepository->create($clubRequestData);
            foreach ($request->get('sports_discipline_ids') as $sports_discipline_id) {
                $sportsDisciplines = [
                    'club_request_id' => $clubRequest->id,
                    'sports_discipline_id' => $sports_discipline_id,
                    'number_of_members' => 0,
                ];
                $this->clubRequestSportsDisciplineRepository->create($sportsDisciplines);
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 400);
        }

        return response()->json(['message' => 'success', 'data' => $clubRequest], 200);
    }
    public function delete(Request $request)
    {
        $user = Auth::guard('google-member')->user();
        $userId = $user['id'];
        $club = $this->clubRepository->getClubByManagerID($userId)->toArray();
        if (empty($club)) {
            return response()->json(['error' => 'Bạn chưa phải người tạo clb'], 422);
        }

        $clubRequest = [
            'manager_id' => $userId,
            'club_id' => $club[0]['id'],
            'club_name' => $club[0]['name'],
            'number_of_members' => $club[0]['number_of_members'],
            'description' => $request->get('description') ?? '',
            'type' => ClubRequest::TYPE['delete'],
            'status' => ClubRequest::NEW,
        ];
        try {
            $clubRequestId = $this->clubRequestRepository->create($clubRequest);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }

        return response()->json(['message' => 'success', 'data' => $clubRequest], 200);
    }
}
