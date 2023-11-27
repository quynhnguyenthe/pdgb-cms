<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMatch extends Model
{
    use HasFactory;

    const Team_One = 1;
    const Team_Two = 2;
    protected $guarded = [];
    public $timestamps = false;
}
