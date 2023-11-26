<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubRequest;
use App\Repositories\ClubMemberRepository;
use App\Repositories\ClubRepository;
use App\Repositories\ClubRequestRepository;
use App\Repositories\ClubSportsDisciplineRepository;
use App\Repositories\MemberSportsDisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ClubRequestController extends Controller
{
    /**
     * @var MemberSportsDisciplineRepository
     */
    private $memberSportsDisciplineRepository;
    /**
     * @var ClubRequestRepository
     */
    private $clubRequestRepository;
    /**
     * @var ClubRepository
     */
    private $clubRepository;
    /**
     * @var ClubSportsDisciplineRepository
     */
    private $clubSportsDisciplineRepository;
    /**
     * @var ClubMemberRepository
     */
    private $clubMemberRepository;

    /**
     * Create a new ClubRequestController instance.
     *
     * @return void
     */
    public function __construct(
        ClubRequestRepository          $clubRequestRepository,
        ClubRepository                 $clubRepository,
        ClubSportsDisciplineRepository $clubSportsDisciplineRepository,
        ClubMemberRepository $clubMemberRepository,
        MemberSportsDisciplineRepository $memberSportsDisciplineRepository,
    )
    {
        $this->clubRequestRepository = $clubRequestRepository;
        $this->clubRepository = $clubRepository;
        $this->clubSportsDisciplineRepository = $clubSportsDisciplineRepository;
        $this->clubMemberRepository = $clubMemberRepository;
        $this->memberSportsDisciplineRepository = $memberSportsDisciplineRepository;
    }

    public function list(Request $request)
    {
        $status = null;
        if ($request->get('status')) {
            $status = $request->get('status');
        }
        $clubRequests = $this->clubRequestRepository->getAll($status);

        return response()->json(['message' => 'success', 'data' => $clubRequests], 200);
    }

    public function reviewRegistration(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $clubRequest = $this->clubRequestRepository->getById($id);
        $dataUpdate = [];
        $club = [];
        if ($clubRequest) {
            if ($clubRequest['status'] == ClubRequest::NEW) {
                DB::beginTransaction();
                try {
                    $clubRequestStatus = $request->get('status');
                    $dataUpdate['status'] = $clubRequestStatus;
                    if ($clubRequestStatus == ClubRequest::APPROVE) {
                        $club = [
                            'name' => $clubRequest['club_name'],
                            'manager_id' => $clubRequest['manager_id'],
                            'number_of_members' => $clubRequest['number_of_members'],
                            'description' => $clubRequest['description'],
                            'status' => Club::ACTIVE,
                        ];
                        $club = $this->clubRepository->create($club);
                        $clubMember = [
                            'club_id' => $club['id'],
                            'member_id' => $club['manager_id']
                        ];
                        $this->clubMemberRepository->create($clubMember);
                        foreach ($clubRequest['sports_disciplines'] as $sportsDiscipline) {
                            $clubSportsDisciplines = [
                                'club_id' => $club['id'],
                                'sports_discipline_id' => $sportsDiscipline['id'],
                            ];
                            $this->clubSportsDisciplineRepository->create($clubSportsDisciplines);
                            $memberSportsDiscipline = [
                                'member_id' => $club['manager_id'],
                                'sports_discipline_id' => $sportsDiscipline['id'],
                            ];
                            $this->memberSportsDisciplineRepository->create($memberSportsDiscipline);
                        }
                    }
                    $this->clubRequestRepository->update($clubRequest, $dataUpdate);
                    DB::commit();
                } catch (\Exception $ex) {
                    DB::rollBack();
                    dd($ex);
                }
            } else {
                return response()->json(['error' => 'The request has been processed'], 403);
            }
        } else {
            return response()->json(['error' => 'club request not found'], 404);
        }

        return response()->json(['message' => 'success', 'data' => $club], 200);
    }

    public function reviewDeletion(Request $request, int $id)
    {
        $clubRequest = $this->clubRequestRepository->getById($id);
        if ($clubRequest) {
            if ($clubRequest['status'] == ClubRequest::NEW) {
                $club = $this->clubRepository->getById($clubRequest['club_id']);
                DB::beginTransaction();
                try {
                    $this->clubSportsDisciplineRepository->getModel()->where('club', $id)->delete();
                    $club->delete();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['error' => $e->getMessage()], 400);
                }
            } else {
                return response()->json(['error' => 'The request has been processed'], 403);
            }
        } else {
            return response()->json(['error' => 'club request not found'], 404);
        }


        return response()->json(['message' => 'success'], 200);
    }
}
