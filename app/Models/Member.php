<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function memberRequests()
    {
        return $this->hasMany(MemberRequest::class, 'member_id');
    }
}
