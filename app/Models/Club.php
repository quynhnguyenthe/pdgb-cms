<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $guarded = [];
    const ACTIVE = 1;
    const INACTIVE = 0;


    public function sports_disciplines()
    {
        return $this->belongsToMany(SportsDiscipline::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class);
    }
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function manager()
    {
        return $this->belongsTo(Member::class, 'manager_id');
    }
}
