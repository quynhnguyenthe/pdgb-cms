<?php

namespace App\Repositories;

use App\Models\ClubRequestSportsDiscipline;

class ClubRequestSportsDisciplineRepository extends Repository
{
    public function getModel()
    {
        return new ClubRequestSportsDiscipline();
    }

}
