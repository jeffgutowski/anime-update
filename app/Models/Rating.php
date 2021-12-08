<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'game_rating';
    protected $fillable = [
        'game_id',
        'user_id',
        'rating',
        'difficulty',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id", "id")->select("id", "name");
    }

}
