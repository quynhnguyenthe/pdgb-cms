<?php

namespace App\Repositories;

use App\Models\SportsDiscipline;

class SportsDisciplineRepository extends Repository
{
    public function getModel()
    {
        return new SportsDiscipline();
    }
}
