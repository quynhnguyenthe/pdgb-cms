<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeClub extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    const APPROVE = 1;
    const REJECT = 2;
    const NEW = 0;

    public function matchs(){
        return $this->hasOne(Matchs::class, 'id', 'match_id');
    }
}
