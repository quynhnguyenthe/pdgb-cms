<?php

namespace App\Repositories;

use App\Models\ChallengeClub;

class ChallengeClubRepository extends Repository
{
    public function getModel()
    {
        return new ChallengeClub();
    }
}
