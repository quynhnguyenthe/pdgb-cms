<?php

namespace App\Repositories;

use App\Models\ClubSportsDiscipline;

class ClubSportsDisciplineRepository extends Repository
{
    public function getModel()
    {
        return new ClubSportsDiscipline();
    }

    public function checkExists($club_id, $sportDisciplineId)
    {
        return $this->getModel()
            ->where('sports_discipline_id', $sportDisciplineId)
            ->where('club_id', $club_id)
            ->exists();
    }
}
