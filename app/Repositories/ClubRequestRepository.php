<?php

namespace App\Repositories;

use App\Models\ClubRequest;

class ClubRequestRepository extends Repository
{
    public function getModel()
    {
        return new ClubRequest();
    }

    public function getAll($status)
    {
        return $this->getModel()
            ->with('sports_disciplines')
            ->with('manager')
            ->get();
    }

    public function getById($id, array $options = [])
    {
        return $this->getModel()->with('sports_disciplines')->find($id);
    }

    public function getClubRquestWithMember($userId)
    {
        return $this->getModel()
            ->where("manager_id", $userId)
            ->where('status', '=', ClubRequest::NEW)
            ->exists();
    }
}
