<?php

namespace App\Repositories;

use App\Models\MatchResult;

class MatchResultRepository extends Repository
{
    public function getModel()
    {
        return new MatchResult();
    }
}
