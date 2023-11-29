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

    public function getByMember($merber_id)
    {
        return $this->getModel()->where('member_id', $merber_id)->get();
    }

    public function getMemberInClubWithSports($sportsDisciplineId, $listMemberIdsInClub)
    {
        return $this->getModel()
            ->with('members')
            ->where('sports_discipline_id', $sportsDisciplineId)
            ->whereIn('member_id', $listMemberIdsInClub)
            ->get();
    }
}
