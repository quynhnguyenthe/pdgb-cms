<?php

namespace App\Repositories;

use App\Models\ClubRequest;

class ClubRequestRepository extends Repository
{
    public function getModel()
    {
        return new ClubRequest();
    }
}
