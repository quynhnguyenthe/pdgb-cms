<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ClubMember;
use App\Models\Match;
use App\Repositories\ChallengeClubRepository;
use App\Repositories\ClubMemberRepository;
use App\Repositories\MatchRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    /**
     * @var MatchRepository
     */
    private $matchRepository;
    /**
     * @var ClubMemberRepository
     */
    private $clubMemberRepository;
    /**
     * @var ChallengeClubRepository
     */
    private $challengeClubRepository;

    public function __construct(
        MatchRepository $matchRepository,
        ClubMemberRepository $clubMemberRepository,
        ChallengeClubRepository $challengeClubRepository,
    )
    {
        $this->matchRepository = $matchRepository;
        $this->clubMemberRepository = $clubMemberRepository;
        $this->challengeClubRepository = $challengeClubRepository;
    }

    public function create(Request $request)
    {
        $user = Auth::guard('google-member')->user();
        $validator = Validator::make($request->all(), [
            'sports_discipline_id' => 'required|exists:sports_disciplines,id',
            'match_date' => 'required|',
            'match_time' => 'required|',
            'duration_minutes' => 'required|',
            'venue' => 'required|',
            'coin' => 'required|',
            'type' => 'required|',
            'challenge_club' => 'required|',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $clubMember = $this->clubMemberRepository->getClubByMember($user->id);
            $match = [
                'sports_discipline_id' => $request->get('sports_discipline_id'),
                'creator_member_id' => $user->id,
                'creator_club_id' => $clubMember['club_id'],
                'match_date' => $request->get('match_date'),
                'match_time' => $request->get('match_time'),
                'duration_minutes' => $request->get('duration_minutes'),
                'venue' => $request->get('venue'),
                'coin' => $request->get('coin'),
                'type' => $request->get('type'),
                'status' => Match::STATUS_NEW,
            ];
            $match = $this->matchRepository->create($match);
            $challengeClubIds = $request->get('challenge_club');
            foreach ($challengeClubIds as $challengeClubId) {
                $challengeClub = [
                    'match_id' => $match['id'],
                    'club_id' => $challengeClubId,
                ];
            }
            $this->challengeClubRepository->create($challengeClub);

        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }
}
