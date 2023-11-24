<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRequestSportsDiscipline extends Model
{
    use HasFactory;
    protected $table = 'club_request_sports_discipline';

    protected $fillable = ['club_request_id', 'sports_discipline_id'];
    public $timestamps = false;
}
