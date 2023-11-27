<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeClub extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    const NEW = 1;
    const APPROVE = 2;
    const REJECT = 3;

    public function matchs(){
        return $this->hasOne(Matchs::class, 'id', 'match_id');
    }
}
