<?php

namespace App\Repositories;

use App\Models\MemberRequestsSportsDiscipline;

class MemberRequestsSportsDisciplineRepository extends Repository
{
    public function getModel()
    {
        return new MemberRequestsSportsDiscipline();
    }

    public function getByRequest(int $request_id)
    {
        return $this->getModel()->where('member_request_id', $request_id)->get();
    }
}
