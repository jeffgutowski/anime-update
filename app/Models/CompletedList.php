<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedList extends Model
{
    protected $table = 'game_completed_lists';
    protected $primaryKey = 'id';
    protected $fillable = ['game_id','user_id','region'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'game_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
