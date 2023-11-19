<?php

namespace App\Repositories;

use App\Models\ClubSportsDiscipline;

class ClubSportsDisciplineRepository extends Repository
{
    public function getModel()
    {
        return new ClubSportsDiscipline();
    }
}
