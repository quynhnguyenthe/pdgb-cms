<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ChallengeClub;
use App\Models\ClubMember;
use App\Models\Match;
use App\Models\Matches;
use App\Models\TeamMatch;
use App\Repositories\ChallengeClubRepository;
use App\Repositories\ClubMemberRepository;
use App\Repositories\MatchRepository;
use App\Repositories\TeamMatchRepository;
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
     * @var TeamMatchRepository
     */
    private $teamMatchRepository;
    /**
     * @var TeamMemberRepository
     */
    private $teamMemberRepository;

    public function __construct(
        MatchRepository         $matchRepository,
        ClubMemberRepository    $clubMemberRepository,
        ChallengeClubRepository $challengeClubRepository,
        TeamMatchRepository     $teamMatchRepository,
    )
    {
        $this->matchRepository = $matchRepository;
        $this->clubMemberRepository = $clubMemberRepository;
        $this->challengeClubRepository = $challengeClubRepository;
        $this->teamMatchRepository = $teamMatchRepository;
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
            'member_club_id' => 'required|',
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
            $match_end_date = (strtotime($request->get('match_date') . ' '. $request->get('match_time') * $request->get('duration_minutes')*60*1000));
            $match = [
                'sports_discipline_id' => $request->get('sports_discipline_id'),
                'creator_member_id' => $user->id,
                'creator_club_id' => $clubMember['club_id'],
                'match_date' => $request->get('match_date'),
                'match_time' => $request->get('match_time'),
                'duration_minutes' => $request->get('duration_minutes'),
                'match_end_date' => $match_end_date,
                'venue' => $request->get('venue'),
                'coin' => $request->get('coin'),
                'type' => $request->get('type'),
                'description' => $request->get('description'),
                'status' => Matches::STATUS_NEW
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
                'match_id' => $match['id'],
                'member_id' => $user->id,
                'type' => TeamMatch::Team_One,
            ];
            $this->teamMatchRepository->create($teamOne);
            $memberInClubs = $request->get('member_club_id');
            foreach ($memberInClubs as $member_id) {
                $teamOne = [
                    'match_id' => $match['id'],
                    'member_id' => $member_id,
                    'type' => TeamMatch::Team_One,
                ];
                $this->teamMatchRepository->create($teamOne);
            }
            DB::commit();

            return response()->json(['message' => 'success', 'data' => $match], 200);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
            DB::rollBack();
        }
    }

    public function listPK()
    {
        $user = Auth::guard('google-member')->user();
        $clubMember = $this->clubMemberRepository->getClubByMember($user->id);
        if ($clubMember) {
            $listPK = $this->matchRepository->getListPK($clubMember['club_id']);

            return response()->json(['message' => 'success', 'data' => $listPK], 200);
        }

        return response()->json(['message' => 'success', 'data' => []], 200);
    }

    public function listMatch()
    {
        $user = Auth::guard('google-member')->user();
        $matches = $this->matchRepository->getListMatch($user->id);

        return response()->json(['message' => 'success', 'data' => $matches], 200);
    }

    public function replyPK(Request $request, int $match_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $match = $this->matchRepository->getById($match_id);
        if (!$match || $match['status'] != Matches::STATUS_NEW) {
            return response()->json(['error' => 'Trận đấu không tồn tại hoặc đã diễn ra'], 422);
        }
        $user = Auth::guard('google-member')->user();
        $clubMember = $this->clubMemberRepository->getClubByMember($user->id);
        $checkMatch = $this->matchRepository->checkMatchWithClub($clubMember['club_id'], $match_id);
        if (!$checkMatch) {
            return response()->json(['error' => 'CLB của bạn không có vé mời tham dự trận đấu này'], 422);
        }

        $listMemberIds = $request->get('member_ids') ?? [];
        $statusReply = $request->get('status');
        DB::beginTransaction();
        try {
            if ($statusReply == ChallengeClub::APPROVE) {
                $teamOne = [
                    'match_id' => $match_id,
                    'member_id' => $user->id,
                    'type' => TeamMatch::Team_Two,
                ];
                $this->teamMatchRepository->create($teamOne);
                foreach ($listMemberIds as $member_id) {
                    $teamOne = [
                        'match_id' => $match_id,
                        'member_id' => $member_id,
                        'type' => TeamMatch::Team_Two,
                    ];
                    $this->teamMatchRepository->create($teamOne);
                }
                $this->matchRepository->update($match, ['status' => Matches::STATUS_ACCEPTED]);
                $challengeClub = $this->challengeClubRepository->getChallengeWithMatchAndClub($clubMember['club_id'], $match_id);
                $this->challengeClubRepository->update($challengeClub, ['status' => ChallengeClub::APPROVE]);
                $otherChallengeClubs = $this->challengeClubRepository->getOtherChallengeMatchWithClub($clubMember['club_id'], $match_id);
                foreach ($otherChallengeClubs as $otherChallengeClub) {
                    $this->challengeClubRepository->update($otherChallengeClub, ['status' => ChallengeClub::REJECT]);
                }
            } else {
                $challengeClub = $this->challengeClubRepository->getChallengeWithMatchAndClub($clubMember['club_id'], $match_id);
                $this->challengeClubRepository->update($challengeClub, ['status' => ChallengeClub::REJECT]);
                $checkRejectMatch = $this->challengeClubRepository->getRejectWithMatch($match_id);
                if ($checkRejectMatch == 0) {
                    $this->matchRepository->update($match, ['status' => Matches::STATUS_REJECT]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }

    public function getInDue() {
        $user = Auth::guard('google-member')->user();
        $match = $this->matchRepository->getInDueWithUser($user->id);

        return response()->json(['message' => 'success', 'data' => $match], 200);
    }
}
