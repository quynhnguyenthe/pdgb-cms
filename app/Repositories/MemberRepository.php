<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository extends Repository
{
    public function getModel()
    {
        return new Member();
    }

    public function getAll()
    {
        return $this->getModel()->get();
    }
}
