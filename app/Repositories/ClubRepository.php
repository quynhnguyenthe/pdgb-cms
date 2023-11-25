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

    public function getAll(int $status = null)
    {
        $club = $this->getModel()
            ->with('manager')
            ->withCount('sports_disciplines')
            ->withCount('members')
            ->withCount('teams');
        if ($status) {
            $club->where('status', $status);
        }

        return $club->get();
    }

    public function getClubByManagerID(int $manager_id)
    {
        $club = $this->getModel()
            ->with('sports_disciplines')
            ->with('members')
            ->with('teams')
            ->where('status', Club::ACTIVE)
            ->where('manager_id', $manager_id);

        return $club->get();
    }

    public function getById($id, array $options = [])
    {
        return $this->getModel()->withCount('members')->firstOrFail($id);
    }

    public function getOtherClub(int $id)
    {
        $club = $this->getModel()
            ->withCount('sports_disciplines')
            ->withCount('members')
            ->withCount('teams')
            ->where('status', Club::ACTIVE)
            ->where('manager_id', '!=', $id);

        return $club->get();
    }
}
