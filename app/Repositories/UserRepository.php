<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    public function getModel()
    {
        return new User();
    }
}
