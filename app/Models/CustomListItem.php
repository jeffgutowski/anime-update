<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomListItem extends Model
{
    protected $table = 'custom_list_game';
    protected $primaryKey = 'id';
    protected $fillable = [
        'custom_list_id',
        'game_id',
        'description',
        'order_number',
        'thumbnail',
        'created_at',
        'updated_at',
    ];

    function product() {
        return $this->belongsTo('App\Models\Product','game_id','id');
    }
    function list() {
        return $this->belongsTo('App\Models\CustomList','custom_list_id','id');
    }
}
