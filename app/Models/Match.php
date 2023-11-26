<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATUS_NEW = 0;
    const STATUS_IN_DUE = 1;
    const STATUS_DONE = 2;
    const STATUS_REJECT = 3;
}
