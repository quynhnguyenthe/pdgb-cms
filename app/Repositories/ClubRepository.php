<?php

namespace App\Repositories;

use App\Models\Club;
use App\Models\MemberRequest;
use Illuminate\Support\Facades\DB;

class ClubRepository extends Repository
{
    public function getModel()
    {
        return new Club();
    }

    public function getAll(int $status = null)
    {
        $club = $this->getModel()
            ->with('manager')
            ->withCount('sports_disciplines')
            ->withCount('members')
            ->withCount('teams');
        if ($status) {
            $club->where('status', $status);
        }

        return $club->get();
    }

    public function getClubByManagerID(int $user_id)
    {
        $table = $this->getModel()->getTable();
        $club = $this->getModel()
            ->select('clubs.*')
            ->join('club_member', "$table.id", '=', 'club_member.club_id')
            ->with('manager')
            ->with('sports_disciplines')
            ->with(['members' => function ($query) use ($user_id) {
                $query->where('members.id', '!=', $user_id);
            }])
            ->with('teams')
            ->where('status', Club::ACTIVE)
            ->where('manager_id', $user_id)
            ->orwhere('club_member.member_id', $user_id)
            ->groupBy('clubs.id');

        return $club->get();
    }

    public function getById($id, array $options = [])
    {
        return $this->getModel()->select('clubs.*')->withCount('members')->find($id);
    }

    public function getOtherClub(int $id)
    {
        $tableName = $this->getModel()->getTable();
        $club = $this->getModel()
            ->select("$tableName.*", 'member_requests.status as request_join_status', 'member_requests.id as request_id')
            ->with('sports_disciplines')
            ->with('members')
            ->with('teams')
            ->leftJoin('member_requests', function ($join) use ($tableName, $id) {
                $join->on("$tableName.id", '=', 'member_requests.club_id')
                    ->where('member_requests.member_id', '=', $id)
                    ->whereIn('member_requests.status', [MemberRequest::NEW, MemberRequest::APPROVE]);
            })
            ->where("$tableName.status", Club::ACTIVE)
            ->where('manager_id', '!=', $id);

        return $club->get();
    }

    public function checkClub($member_id)
    {
        $table = $this->getModel()->getTable();
        return $this->getModel()
            ->join('club_member', "$table.id", '=', 'club_member.club_id')
            ->where('club_member.member_id', $member_id)
            ->orWhere("$table.manager_id", $member_id)
            ->exists();
    }
}
