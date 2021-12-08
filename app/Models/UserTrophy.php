<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTrophy extends Model
{
    protected $table = 'user_trophies';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'type',
        'region',
        'platform_id',
        'count',
        'trophy_id',
        'next_trophy_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function trophy()
    {
        return $this->belongsTo('App\Models\Trophy', 'trophy_id', 'id');
    }

    public function nextTrophy()
    {
        return $this->belongsTo('App\Models\Trophy', 'next_trophy_id', 'id');
    }

    public function platform()
    {
        return $this->belongsTo('App\Models\Platform');
    }
}
