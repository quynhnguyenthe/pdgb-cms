<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ChallengeClub;
use App\Models\ClubMember;
use App\Models\Match;
use App\Models\Matchs;
use App\Repositories\ChallengeClubRepository;
use App\Repositories\ClubMemberRepository;
use App\Repositories\MatchRepository;
use App\Repositories\TeamMemberRepository;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

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
    /**
     * @var TeamRepository
     */
    private $teamRepository;
    /**
     * @var TeamMemberRepository
     */
    private $teamMemberRepository;

    public function __construct(
        MatchRepository         $matchRepository,
        ClubMemberRepository    $clubMemberRepository,
        ChallengeClubRepository $challengeClubRepository,
        TeamRepository          $teamRepository,
        TeamMemberRepository $teamMemberRepository
    )
    {
        $this->matchRepository = $matchRepository;
        $this->clubMemberRepository = $clubMemberRepository;
        $this->challengeClubRepository = $challengeClubRepository;
        $this->teamRepository = $teamRepository;
        $this->teamMemberRepository = $teamMemberRepository;
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
            if (empty($clubMember)) {
                return response()->json(['error' => 'Bạn chưa tham gia clb'], 422);
            }
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
                'status' => Matchs::STATUS_NEW
            ];
            $match = $this->matchRepository->create($match);
            $challengeClubIds = $request->get('challenge_club');
            foreach ($challengeClubIds as $challengeClubId) {
                $challengeClub = [
                    'match_id' => $match['id'],
                    'club_id' => $challengeClubId,
                    'status' => ChallengeClub::NEW
                ];
                $this->challengeClubRepository->create($challengeClub);
            }

            $teamOne = [
                'name' => $user->name,
                'creator_member_id' => $user->id,
                'club_id' => $clubMember['club_id'],
            ];
            $teamOne = $this->teamRepository->create($teamOne);
            $teamMember = [
                'team_id' => $teamOne['id'],
                'member_id' => $user->id
            ];
            $this->teamMemberRepository->create($teamMember);
            DB::commit();

            return response()->json(['message' => 'success', 'data' => $match], 200);
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollBack();
        }
    }

    public function listChallenge() {
        $user = Auth::guard('google-member')->user();
        $clubMember = $this->clubMemberRepository->getClubByMember($user->id);
        $challenge = $this->matchRepository->getChallenges($clubMember['club_id']);

        return response()->json(['message' => 'success', 'data' => $challenge], 200);
    }
}
