<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Repositories\SportsDisciplineRepository;
use Illuminate\Http\Request;

class SportsDisciplineController extends Controller
{
    protected $sportsDisciplineRepository;

    public function __construct(
        SportsDisciplineRepository $sportsDisciplineRepository,
    )
    {
//        $this->middleware('auth:api');
        $this->sportsDisciplineRepository = $sportsDisciplineRepository;
    }

    public function list(Request $request)
    {
        $query = $this->sportsDisciplineRepository;
        if ($request->get('status')) {

        }
        $sportsDisciplines = $query->get();

        return response()->json(['message' => 'success', 'data' => $sportsDisciplines], 200);
    }
}
