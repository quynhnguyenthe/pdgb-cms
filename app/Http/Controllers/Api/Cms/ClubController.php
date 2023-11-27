<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Repositories\ClubSportsDisciplineRepository;
use Illuminate\Http\Request;
use App\Repositories\ClubRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class ClubController extends Controller
{
    private $clubRepository;
    private $clubSportsDisciplineRepository;
    /**
     * Create a new ClubController instance.
     *
     * @return void
     */
    public function __construct(
        ClubRepository $clubRepository,
        ClubSportsDisciplineRepository $clubSportsDisciplineRepository
    )
    {
        $this->middleware('auth:api');
        $this->clubRepository = $clubRepository;
        $this->clubSportsDisciplineRepository = $clubSportsDisciplineRepository;
    }

    public function getClubs(Request $request)
    {
        $status = null;
        if ($request->get('status')) {
            $status = $request->get('status');
        }
        $clubs = $this->clubRepository->getAll($status);

        return response()->json(['message' => 'success', 'data' => $clubs], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'manager_id' => 'required|integer|exists:members,id',
            'number_of_members'=> 'required|integer|',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $status = $request->get('status') ?? Club::INACTIVE;
        $club = [
            'name' => $request->get('name'),
            'manager_id' => $request->get('manager_id'),
            'number_of_members' => $request->get('number_of_members'),
            'status' => $status
        ];
        try {
            $club = $this->clubRepository->create($club);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }


        return response()->json(['message' => 'success', 'data' => $club], 200);
    }

    public function detail(int $club_id)
    {
        $clubs = $this->clubRepository->getClubByID($club_id);

        return response()->json(['message' => 'success', 'data' => $clubs], 200);
    }
}
