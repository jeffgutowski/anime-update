<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomList extends Model
{
    public static function boot()
    {
        parent::boot();
        static::creating(function (CustomList $custom_list) {
            if (!isset($custom_list->order_number) && auth()->check()) {
                $custom_list->order_number = CustomList::where("user_id", auth()->id())->max('order_number') + 1;;
            }
        });
    }
    protected $table = 'custom_lists';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'youtube_id',
        'thumbnail',
        'order_by',
        'custom_item_thumbnails',
        'show_order_number',
        'public',
        'order_number'
    ];

    public function items() {
        return $this->belongsToMany('App\Models\Product','custom_list_game','custom_list_id','game_id')->withPivot('description', 'order_number', 'thumbnail', 'id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
