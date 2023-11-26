<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchs extends Model
{
    protected $table = 'matchs';
    use HasFactory;

    protected $guarded = [];

    const STATUS_NEW = 0;
    const STATUS_IN_DUE = 1;
    const STATUS_DONE = 2;
    const STATUS_REJECT = 3;

    const STATUS_NAME = [
        0 => 'Mới',
        1 => 'Đang thi đấu',
        2 => 'Đã xong',
        3 => 'Bị từ chối',
    ];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute() {
        return self::STATUS_NAME[$this->attributes['status']];
    }
}
