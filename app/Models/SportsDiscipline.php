<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsDiscipline extends Model
{
    use HasFactory;
    protected $table = 'sports_disciplines';
    public $timestamps = false;
    protected $hidden = ['created_at', 'updated_at'];
}
