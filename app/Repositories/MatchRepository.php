<?php

namespace App\Repositories;

use App\Models\Matchs;

class MatchRepository extends Repository
{
    public function getModel()
    {
        return new Matchs();
    }

    public function getChallenges($club_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matchs.*')
            ->join('challenge_clubs', 'challenge_clubs.match_id', '=', "$tableName.id")
            ->where('challenge_clubs.club_id', $club_id)
            ->get();
    }
}
