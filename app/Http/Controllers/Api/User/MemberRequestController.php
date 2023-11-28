<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\MemberRequest;
use App\Repositories\ClubMemberRepository;
use App\Repositories\ClubRepository;
use App\Repositories\ClubSportsDisciplineRepository;
use App\Repositories\MemberRequestsRepository;
use App\Repositories\MemberRequestsSportsDisciplineRepository;
use App\Repositories\MemberSportsDisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class MemberRequestController extends Controller
{
    protected $clubRepository;
    protected $memberRequestsRepository;
    protected $clubMemberRepository;
    /**
     * @var MemberSportsDisciplineRepository
     */
    private $memberSportsDisciplineRepository;
    /**
     * @var MemberRequestsSportsDisciplineRepository
     */
    private $memberRequestsSportsDisciplineRepository;
    /**
     * @var ClubSportsDisciplineRepository
     */
    private $clubSportsDisciplineRepository;


    public function __construct(
        ClubRepository                           $clubRepository,
        MemberRequestsRepository                 $memberRequestsRepository,
        ClubMemberRepository                     $clubMemberRepository,
        MemberSportsDisciplineRepository         $memberSportsDisciplineRepository,
        MemberRequestsSportsDisciplineRepository $memberRequestsSportsDisciplineRepository,
        ClubSportsDisciplineRepository           $clubSportsDisciplineRepository,
    )
    {
        $this->clubRepository = $clubRepository;
        $this->memberRequestsRepository = $memberRequestsRepository;
        $this->clubMemberRepository = $clubMemberRepository;
        $this->memberSportsDisciplineRepository = $memberSportsDisciplineRepository;
        $this->memberRequestsSportsDisciplineRepository = $memberRequestsSportsDisciplineRepository;
        $this->clubSportsDisciplineRepository = $clubSportsDisciplineRepository;
    }

    public function requestJoin(Request $request)
    {
        $user = Auth::guard('google-member')->user();
        $validator = Validator::make($request->all(), [
            'club_id' => 'required|exists:clubs,id',
            'sports_discipline_id' => 'required|',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $clubMember = $this->clubMemberRepository->checkExistsClubWithMember($user->id);
        if ($clubMember) {
            return response()->json(['error' => 'Bạn đã tham gia vào 1 clb khác'], 422);
        }

        $exists = DB::table('member_requests')
            ->where('member_id', $user->id)
            ->where('club_id', $request->input('club_id'))
            ->where('status', '=', MemberRequest::NEW)
            ->exists();
        if ($exists) {
            return response()->json(['error' => 'Bạn đã có đơn xin vào clb này'], 422);
        }

        $sportsDisciplineIds = $request->get('sports_discipline_id');
        $checkInvalidSportsDiscipline = false;
        foreach ($sportsDisciplineIds as $sportsDisciplineId) {
            if ($this->clubSportsDisciplineRepository->checkExists($request->get('club_id'), $sportsDisciplineId)) {
                $checkInvalidSportsDiscipline = true;
                break;
            }
        }
        if (!$checkInvalidSportsDiscipline) {
            return response()->json(['error' => 'Bộ môn bạn chọn không phù hợp với clb này'], 422);
        }

        DB::beginTransaction();
        try {
            $memberRequest = [
                'member_id' => $user->id,
                'club_id' => $request->get('club_id'),
                'status' => MemberRequest::NEW
            ];
            $memberRequest = $this->memberRequestsRepository->create($memberRequest);
            foreach ($sportsDisciplineIds as $sportsDisciplineId) {
                $memberRequestSportsDiscipline = [
                    'member_request_id' => $memberRequest['id'],
                    'sports_discipline_id' => $sportsDisciplineId
                ];
                $this->memberRequestsSportsDisciplineRepository->create($memberRequestSportsDiscipline);
            }
            DB::commit();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
            DB::rollBack();
        }


        return response()->json(['message' => 'success', 'data' => $memberRequest], 200);
    }

    public function listRequestJoin(Request $request)
    {
        $user = Auth::guard('google-member')->user();
        $requests = $this->memberRequestsRepository->getRequestWithManager($user['id']);

        return response()->json(['message' => 'success', 'data' => $requests], 200);
    }

    public function reviewRequestJoin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:clubs,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $requestId = $request->get('request_id');
        $user = Auth::guard('google-member')->user();
        $requestJoin = $this->memberRequestsRepository->getById($requestId);
        $requestStatus = $request->get('status') ?? MemberRequest::APPROVE;
        if ($requestJoin && $requestJoin['status'] == MemberRequest::NEW) {
            $clubMember = $this->clubMemberRepository->checkExistsClubWithMember($requestJoin['member_id']);
            if ($clubMember) {
                return response()->json(['error' => 'Thành viên xin gia nhập đã tham gia vào 1 clb khác'], 422);
            }
            $club = $this->clubRepository->getById($requestJoin['club_id'])->toArray();
            if ($club['manager_id'] != $user->id) {
                return response()->json(['error' => 'Bạn không phải chủ club'], 422);
            }
            if ($club['members_count'] >= $club['number_of_members']) {
                $dataRequestJoin['status'] = $requestStatus;
                $this->memberRequestsRepository->update($requestJoin, $dataRequestJoin);
                return response()->json(['error' => 'Số lượng thành viên đã đạt giới hạn tối đa'], 422);
            }
            DB::beginTransaction();
            try {
                if ($requestStatus == MemberRequest::APPROVE) {
                    $clubMember = [
                        'club_id' => $club['id'],
                        'member_id' => $requestJoin['member_id']
                    ];
                    $this->clubMemberRepository->create($clubMember);
                    $memberRequestSportsDisciplines = $this->memberRequestsSportsDisciplineRepository->getByRequest($requestId);
                    foreach ($memberRequestSportsDisciplines as $memberRequestSportsDiscipline) {
                        $memberSportsDiscipline = [
                            'member_id' => $requestJoin['member_id'],
                            'sports_discipline_id' => $memberRequestSportsDiscipline['sports_discipline_id']
                        ];
                        $this->memberSportsDisciplineRepository->create($memberSportsDiscipline);
                    }
                }
                $dataRequestJoin['status'] = $requestStatus;
                $this->memberRequestsRepository->update($requestJoin, $dataRequestJoin);
                DB::commit();

                return response()->json(['message' => 'success'], 200);
            } catch (\Exception $ex) {
                DB::rollBack();
            }

        } else {
            return response()->json(['error' => 'Đơn không tồn tại hoặc đã được duyệt'], 400);
        }
    }

    public function cancelRequestJoin(Request $request, int $requestId)
    {
        $user = Auth::guard('google-member')->user();
        $requestJoin = $this->memberRequestsRepository->getById($requestId);
        if ($user->id != $requestJoin['member_id']) {
            return response()->json(['error' => 'Bạn không thể huỷ đơn gia nhập của người khác'], 422);
        }

        if ($requestJoin['status'] != MemberRequest::NEW) {
            return response()->json(['error' => 'Đơn của bạn đã được xử lý, không thể huỷ'], 422);
        }

        $data = ['status' => MemberRequest::CANCEL];
        $this->memberRequestsRepository->update($requestJoin, $data);

        return response()->json(['message' => 'success'], 200);
    }
}
