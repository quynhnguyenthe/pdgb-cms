<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRequestsSportsDiscipline extends Model
{
    protected $table = 'member_request_sports_discipline';
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
}
