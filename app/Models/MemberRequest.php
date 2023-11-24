<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRequest extends Model
{
    use HasFactory;
    const APPROVE = 1;
    const REJECT = 2;
    const NEW = 0;
    const STATUS_NAME = [
        0 => 'Mới',
        1 => 'Đã duyệt',
        2 => 'Từ chối'
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

}
