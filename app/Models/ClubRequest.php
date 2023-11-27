<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    use HasFactory;
    const NEW = 1;
    const APPROVE = 2;
    const REJECT = 3;
    const TYPE = [
        'create' => 1,
        'delete' => 2,
    ];
    const STATUS_NAME = [
        1 => 'Mới',
        2 => 'Đã duyệt',
        3 => 'Từ chối'
    ];

    const TYPE_NAME = [
        1 => 'Thêm mới',
        2 => 'Xoá',
    ];
    protected $guarded = [];

    protected $appends = ['status_name', 'type_name'];

    public function getStatusNameAttribute()
    {
        return self::STATUS_NAME[$this->attributes['status']];
    }

    public function getTypeNameAttribute()
    {
        return self::TYPE_NAME[$this->attributes['type']];
    }

    public function sports_disciplines()
    {
        return $this->belongsToMany(SportsDiscipline::class);
    }

    public function manager()
    {
        return $this->belongsTo(Member::class, 'manager_id');
    }
}
