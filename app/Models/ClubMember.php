<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;
    protected $table = 'club_member';
    protected $guarded = [];
    public $timestamps = false;
}
