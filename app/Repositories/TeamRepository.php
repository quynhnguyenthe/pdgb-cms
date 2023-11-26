<?php

namespace App\Repositories;

use App\Models\Team;

class TeamRepository extends Repository
{
    public function getModel()
    {
        return new Team();
    }
}
