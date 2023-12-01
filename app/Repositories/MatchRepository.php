<?php

namespace App\Repositories;

use App\Models\ChallengeClub;
use App\Models\Matches;

class MatchRepository extends Repository
{
    public function getModel()
    {
        return new Matches();
    }

    public function getById($id, array $options = [])
    {
        return $this->getModel()
            ->with('members')
            ->where('id', $id)
            ->first();
    }

    public function getListPK($club_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matches.*', 'challenge_clubs.status as challenge_status')
            ->join('challenge_clubs', 'challenge_clubs.match_id', '=', "$tableName.id")
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('result')
            ->where('challenge_clubs.club_id', $club_id)
            ->orderBy('matches.id', 'DESC')
            ->get();
    }

    public function getAll()
    {
        return $this->getModel()
            ->select('matches.*')
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('result')
            ->with('team_twos')
            ->orderBy('matches.id', 'DESC')
            ->get();
    }
    public function getListMatch($user_id)
    {
        return $this->getModel()
            ->select('matches.*')
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('challenge_clubs')
            ->with('result')
            ->where('matches.creator_member_id', $user_id)
            ->orWhere('team_ones.member_id', $user_id)
            ->orderBy('matches.id', 'DESC')
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
            ->orderBy('matches.id', 'DESC')
            ->exists();
    }

    public function getInDueWithUser($user_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matches.*')
            ->join('team_matches', 'team_matches.match_id', '=', "$tableName.id")
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('result')
            ->where('team_matches.member_id', $user_id)
            ->where('matches.status', Matches::STATUS_IN_DUE)
            ->groupBy('matches.id')
            ->orderBy('matches.id', 'DESC')
            ->first();
    }

    public function getWaitForResult($user_id)
    {
        $tableName = $this->getModel()->getTable();
        return $this->getModel()
            ->select('matches.*')
            ->join('team_matches', 'team_matches.match_id', '=', "$tableName.id")
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('result')
            ->where('team_matches.member_id', $user_id)
            ->whereIn('matches.status', [Matches::WAIT_RESULT, Matches::STATUS_DONE])
            ->groupBy('matches.id')
            ->orderBy('matches.id', 'DESC')
            ->get();
    }

    public function getListAllMatch($user_id, $otherUserId)
    {

        $qb = $this->getModel()
            ->select('matches.*')
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('result')
            ->join('team_matches', 'team_matches.match_id', 'matches.id');
        if ($otherUserId > 0) {
            $user_id = $otherUserId;
            $qb->where('matches.type', Matches::PUBLIC);
        }
        $qb->where('matches.creator_member_id', $user_id)
            ->orWhere('team_matches.member_id', $user_id);
        $qb->whereIn('matches.status', [Matches::STATUS_ACCEPTED, Matches::STATUS_IN_DUE,Matches::WAIT_RESULT,Matches::STATUS_DONE]);
        $qb->orderBy('matches.id', 'DESC');
        $qb->groupBy('matches.id');

        return $qb->get();
    }

    public function detail($match_id)
    {
        return $this->getModel()
            ->select('matches.*')
            ->with('sports_discipline')
            ->with('creator_member')
            ->with('recipient_member')
            ->with('team_ones')
            ->with('team_twos')
            ->with('challenge_clubs')
            ->with('result')
            ->where('matches.id', $match_id)
            ->orderBy('matches.id', 'DESC')
            ->first();
    }
}
