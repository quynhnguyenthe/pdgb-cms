<?php

namespace App\Repositories;

use App\Models\ClubMember;

class ClubMemberRepository extends Repository
{
    public function getModel()
    {
        new ClubMember();
    }
}
