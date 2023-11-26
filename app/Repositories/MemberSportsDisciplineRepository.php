<?php

namespace App\Repositories;

use App\Models\MemberSportsDiscipline;

class MemberSportsDisciplineRepository extends Repository
{
    public function getModel()
    {
        return new MemberSportsDiscipline();
    }

    public function create(array $data)
    {
        $checkExists = $this->getModel()
            ->where('member_id', $data['member_id'])
            ->where('sports_discipline_id', $data['sports_discipline_id'])
            ->exists();
        if ($checkExists) {
            return [];
        }

        return parent::create($data);
    }
}
