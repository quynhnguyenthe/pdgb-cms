<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubRequest;
use App\Repositories\ClubRepository;
use App\Repositories\ClubRequestRepository;
use App\Repositories\ClubRequestSportsDisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ClubRequestController extends Controller
{
    private $clubRequestRepository;
    private $clubRepository;
    private $clubRequestSportsDisciplineRepository;


    /**
     * Create a new ClubRequestController instance.
     *
     * @return void
     */
    public function __construct(
        ClubRequestRepository             $clubRequestRepository,
        ClubRepository                    $clubRepository,
        ClubRequestSportsDisciplineRepository $clubRequestSportsDisciplineRepository,
    )
    {
//        $this->middleware('auth:api');
        $this->clubRequestRepository = $clubRequestRepository;
        $this->clubRepository = $clubRepository;
        $this->clubRequestSportsDisciplineRepository = $clubRequestSportsDisciplineRepository;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'club_name' => 'required',
            'manager_id' => 'required|exists:members,id',
            'number_of_members' => 'required',
            'sports_discipline_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $clubRequestData = [
            'club_name' => $request->get('club_name'),
            'manager_id' => $request->get('manager_id'),
            'number_of_members' => $request->get('number_of_members'),
            'description' => $request->get('description'),
            'type' => ClubRequest::TYPE['create'],
            'status' => ClubRequest::NEW,
        ];
        try {
            $clubRequest = $this->clubRequestRepository->create($clubRequestData);
            foreach ($request->get('sports_discipline_ids') as $sports_discipline_id) {
                $sportsDisciplines = [
                    'club_request_id' => $clubRequest->id,
                    'sports_discipline_id' => $sports_discipline_id,
                    'number_of_members' => 0,
                ];
                $this->clubRequestSportsDisciplineRepository->create($sportsDisciplines);
//                $clubRequest['sports_disciplines'][] = $sportsDisciplines;
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }

        return response()->json(['message' => 'success', 'data' => $clubRequest], 200);
    }
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'club_id' => 'required',
            'manager_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $clubId = $request->get('club_id');
        $club = $this->clubRepository->find($clubId);
        dd($club);
        $clubRequest = [
            'manager_id' => $request->get('manager_id'),
            'club_id' => $clubId,
            'description' => $request->get('description'),
            'type' => ClubRequest::TYPE['delete'],
            'status' => ClubRequest::NEW,
        ];
        try {
            $clubRequestId = $this->clubRequestRepository->create($clubRequest);
            $clubRequest['id'] = $clubRequestId;
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }

        return response()->json(['message' => 'success', 'data' => $clubRequest], 200);
    }
}
