<?php

namespace App\Repositories;

use App\Models\ClubMember;

class ClubMemberRepository extends Repository
{
    public function getModel()
    {
        return new ClubMember();
    }

    public function checkExistsClubWithMember($userId)
    {
        return $this->getModel()->where("member_id", $userId)->exists();
    }

    public function getClubByMember($userId)
    {
        return $this->getModel()->where("member_id", $userId)->first();
    }

    public function getByClub($clubId)
    {
        return $this->getModel()->where("club_id", $clubId)->get();
    }
}
