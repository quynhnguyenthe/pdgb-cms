<?php

namespace App\Repositories;

use App\Models\TeamMember;

class TeamMemberRepository extends Repository
{
    public function getModel()
    {
        return new TeamMember();
    }
}
