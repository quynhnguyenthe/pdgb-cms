<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRequest extends Model
{
    use HasFactory;
    const NEW = 1;
    const APPROVE = 2;
    const REJECT = 3;
    const CANCEL = 4;
    const STATUS_NAME = [
        1 => 'Mới',
        2 => 'Đã duyệt',
        3 => 'Từ chối',
        4 => 'Huỷ'
    ];
    protected $hidden = ['updated_at'];
    protected $guarded = [];
    protected $appends = ['status_name'];

    public function getStatusNameAttribute()
    {
        return self::STATUS_NAME[$this->attributes['status']];
    }

    public function members()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function sports_disciplines()
    {
        return $this->belongsToMany(SportsDiscipline::class);
    }

}
