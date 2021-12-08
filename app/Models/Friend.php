<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $table = 'friends';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function friend()
    {
        return $this->belongsTo('App\Models\User' ,'friend_id', 'id');
    }
}
