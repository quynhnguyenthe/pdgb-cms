<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubRequest;
use App\Repositories\ClubRepository;
use App\Repositories\ClubRequestRepository;
use App\Repositories\ClubSportsDisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ClubRequestController extends Controller
{
    private $clubRequestRepository;
    private $clubRepository;
    private $clubSportsDisciplineRepository;

    /**
     * Create a new ClubRequestController instance.
     *
     * @return void
     */
    public function __construct(
        ClubRequestRepository          $clubRequestRepository,
        ClubRepository                 $clubRepository,
        ClubSportsDisciplineRepository $clubSportsDisciplineRepository
    )
    {
        $this->middleware('auth:api');
        $this->clubRequestRepository = $clubRequestRepository;
        $this->clubRepository = $clubRepository;
        $this->clubSportsDisciplineRepository = $clubSportsDisciplineRepository;
    }

    public function list(Request $request)
    {
        $query = $this->clubRequestRepository;
        if ($request->get('status')) {

        }
        $clubRequests = $query->get();

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
        if ($clubRequest) {
            if ($clubRequest['status'] == ClubRequest::NEW) {
                $clubRequestStatus = $request->get('status');
                $clubRequest['status'] = $clubRequestStatus;
                if ($clubRequestStatus == ClubRequest::APPROVE) {
                    $club = [
                        'name' => $clubRequest['club_name'],
                        'manager_id' => $clubRequest['manager_id'],
                        'number_of_members' => $clubRequest['number_of_members'],
                        'description' => $clubRequest['description'],
                        'status' => Club::ACTIVE,
                    ];
                    $club = $this->clubRepository->create($club);
                    $clubRequest['club_id'] = $club['id'];
                }

                $this->clubRequestRepository->update($clubRequest, $clubRequest->toArray());
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
