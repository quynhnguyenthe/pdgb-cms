<?php

namespace App\Repositories;

use App\Models\ChallengeClub;

class ChallengeClubRepository extends Repository
{
    public function getModel()
    {
        return new ChallengeClub();
    }

    public function getChallengeWithMatchAndClub(int $club_id, int $match_id)
    {
        return $this->getModel()->where('club_id', $club_id)->where('match_id', $match_id)->first();
    }

    public function getOtherChallengeMatchWithClub($club_id, int $match_id)
    {
        return $this->getModel()->where('club_id', '!=', $club_id)->where('match_id', $match_id)->get();
    }

    public function getRejectWithMatch(int $match_id)
    {
        return $this->getModel()
            ->where('match_id', $match_id)
            ->whereIn('status', [ChallengeClub::NEW, ChallengeClub::APPROVE])
            ->count();
    }

    public function getChallengeWithMatch(int $match_id)
    {
        return $this->getModel()
            ->where('match_id', $match_id)
            ->get();
    }

    public function getPKWithMatchAndClub(int $match_id, int $club_id)
    {
        return $this->getModel()
            ->where('match_id', $match_id)
            ->where('club_id', $club_id)
            ->first();
    }
}
