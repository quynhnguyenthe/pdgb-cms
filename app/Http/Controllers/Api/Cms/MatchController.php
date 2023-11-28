<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Repositories\ChallengeClubRepository;
use App\Repositories\ClubMemberRepository;
use App\Repositories\MatchRepository;
use App\Repositories\TeamMatchRepository;
use Illuminate\Http\Request;

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

    public function list()
    {
        $matches = $this->matchRepository->getAll();

        return response()->json(['message' => 'success', 'data' => $matches], 200);
    }
}
