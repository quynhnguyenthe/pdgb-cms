<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    protected $table = 'matches';
    use HasFactory;

    protected $guarded = [];

    const STATUS_NEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_IN_DUE = 3;
    const STATUS_DONE = 4;
    const STATUS_REJECT = 5;

    const STATUS_NAME = [
        1 => 'Mới',
        2 => 'Đang thi đấu',
        3 => 'Đã xong',
        4 => 'Huỷ',
    ];
    protected $hidden = ['team_one', 'team_two'];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute() {
        return self::STATUS_NAME[$this->attributes['status']];
    }

    public function sports_discipline(){
        return $this->hasOne(SportsDiscipline::class, 'id', 'sports_discipline_id');
    }
    public function creator_member(){
        return $this->hasOne(Member::class, 'id', 'creator_member_id');
    }
    public function recipient_member(){
        return $this->hasOne(Member::class, 'id', 'recipient_member_id');
    }

    public function team_ones(){
        return $this->belongsToMany(Member::class, 'team_matches', 'match_id', 'member_id')->where('type', TeamMatch::Team_One);
    }
    public function team_twos(){
        return $this->belongsToMany(Member::class, 'team_matches', 'match_id', 'member_id')->where('type', TeamMatch::Team_Two);
    }

    public function challenge_clubs() {
        return $this->belongsToMany(Club::class, 'challenge_clubs', 'match_id', 'club_id');
    }
}
