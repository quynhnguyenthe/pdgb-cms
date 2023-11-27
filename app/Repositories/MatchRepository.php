<?php

namespace App\Repositories;

use App\Models\Matchs;

class MatchRepository extends Repository
{
    public function getModel()
    {
        return new Matchs();
    }

    public function getListPK($club_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matchs.*')
            ->join('challenge_clubs', 'challenge_clubs.match_id', '=', "$tableName.id")
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->where('challenge_clubs.club_id', $club_id)
            ->groupBy('matchs.id')
            ->get();
    }

    public function getAll()
    {
        return $this->getModel()
            ->select('matchs.*')
            ->selectRaw("DATE_ADD(CONCAT(match_date, ' ', match_time), INTERVAL duration_minutes MINUTE) AS match_end_date")
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->get();
    }
    public function getListMatch($user_id)
    {
        return $this->getModel()
            ->select('matchs.*')
            ->selectRaw("DATE_ADD(CONCAT(match_date, ' ', match_time), INTERVAL duration_minutes MINUTE) AS match_end_date")
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('challenge_clubs')
            ->where('matchs.creator_member_id', $user_id)
            ->get();
    }
}
