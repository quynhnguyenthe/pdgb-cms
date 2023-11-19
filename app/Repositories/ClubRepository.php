<?php

namespace App\Repositories;

use App\Models\Club;
use Illuminate\Support\Facades\DB;

class ClubRepository extends Repository
{
    public function getModel()
    {
        return new Club();
    }

    public function getAllClubs(int $status = null)
    {
        $club = $this->getModel();
        if ($status) {
            $club->where('status', $status);
        }

        return $club->get();
    }
}
