<?php

namespace App\Repositories;

use App\Models\Matches;

class MatchRepository extends Repository
{
    public function getModel()
    {
        return new Matches();
    }

    public function getListPK($club_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matches.*')
            ->join('challenge_clubs', 'challenge_clubs.match_id', '=', "$tableName.id")
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->where('challenge_clubs.club_id', $club_id)
            ->groupBy('matches.id')
            ->get();
    }

    public function getAll()
    {
        return $this->getModel()
            ->select('matches.*')
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
            ->select('matches.*')
            ->selectRaw("DATE_ADD(CONCAT(match_date, ' ', match_time), INTERVAL duration_minutes MINUTE) AS match_end_date")
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('challenge_clubs')
            ->where('matches.creator_member_id', $user_id)
            ->get();
    }

    public function checkMatchWithClub($club_id, $match_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matches.*')
            ->join('challenge_clubs', 'challenge_clubs.match_id', '=', "$tableName.id")
            ->where('challenge_clubs.club_id', $club_id)
            ->where('matches.id', $match_id)
            ->groupBy('matches.id')
            ->exists();
    }
}
