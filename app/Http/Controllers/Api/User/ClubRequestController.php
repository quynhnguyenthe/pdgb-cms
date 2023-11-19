<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubRequest;
use App\Repositories\ClubRepository;
use App\Repositories\ClubRequestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ClubRequestController extends Controller
{
    private $clubRequestRepository;
    private $clubRepository;

    /**
     * Create a new ClubRequestController instance.
     *
     * @return void
     */
    public function __construct(
        ClubRequestRepository $clubRequestRepository,
        ClubRepository        $clubRepository,
    )
    {
//        $this->middleware('auth:api');
        $this->clubRequestRepository = $clubRequestRepository;
        $this->clubRepository = $clubRepository;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manager_id' => 'required',
            'number_of_members' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $type = $request->get('type');
        if ($type == ClubRequest::TYPE['delete']) {
            $validator = Validator::make($request->all(), [
                'club_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'club_name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
        }

        $clubRequest = [
            'manager_id' => $request->get('manager_id'),
            'number_of_members' => $request->get('number_of_members'),
            'description' => $request->get('description'),
            'type' => $type,
            'status' => ClubRequest::NEW,
        ];
        if ($type == ClubRequest::TYPE['delete']) {
            $clubRequest['club_id'] = $request->get('club_id');
            $club = $this->clubRepository->getById($clubRequest['club_id']);
            if ($club) {
                $clubRequest['club_name'] = $club['name'];
            }
        } else {
            $clubRequest['club_name'] = $request->get('club_name');
        }
        try {
            $clubRequest = $this->clubRequestRepository->create($clubRequest);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }

        return response()->json(['message' => 'success', 'data' => $clubRequest], 200);
    }
}
