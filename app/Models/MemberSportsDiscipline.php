<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSportsDiscipline extends Model
{
    protected $table = 'member_sports_discipline';
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
}
