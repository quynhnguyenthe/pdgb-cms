<?php

namespace App\Repositories;

use App\Models\Match;

class MatchRepository extends Repository
{
    public function getModel()
    {
        return new Match();
    }
}
