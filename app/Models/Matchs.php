<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchs extends Model
{
    protected $table = 'matchs';
    use HasFactory;

    protected $guarded = [];

    const STATUS_NEW = 1;
    const STATUS_IN_DUE = 2;
    const STATUS_DONE = 3;
    const STATUS_REJECT = 4;

    const STATUS_NAME = [
        1 => 'Mới',
        2 => 'Đang thi đấu',
        3 => 'Đã xong',
        4 => 'Bị từ chối',
    ];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute() {
        return self::STATUS_NAME[$this->attributes['status']];
    }
}
