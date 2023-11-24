<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Repositories\MemberRepository;
use Illuminate\Http\Request;
use Validator;

class MemberController extends Controller
{
    protected $memberRepository;
    public function __construct(
        MemberRepository $memberRepository
    )
    {
        $this->memberRepository = $memberRepository;
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:members,email',
            'phone'=> 'required|unique:members,phone',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $member = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'address' => $request->get('address') ?? '',
        ];
        try {
            $member = $this->memberRepository->create($member);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }


        return response()->json(['message' => 'success', 'data' => $member], 200);
    }

    public function list(Request $request)
    {
        $members = $this->memberRepository->getAll();

        return response()->json(['message' => 'success', 'data' => $members], 200);
    }

    public function detail(Request $request, int $id)
    {
        $member = $this->memberRepository->getById($id);

        return response()->json(['message' => 'success', 'data' => $member], 200);
    }
}
