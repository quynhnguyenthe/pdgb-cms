<?php

namespace App\Repositories;

use App\Models\TeamMatch;

class TeamMatchRepository extends Repository
{
    public function getModel()
    {
        return new TeamMatch();
    }
}
