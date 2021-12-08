<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HaveList extends Model
{
    protected $table = 'game_have_lists';
    protected $primaryKey = 'id';
    protected $fillable = [
        'game_id',
        'user_id',
        'region',
        'quantity',
        'box',
        'case',
        'manual',
        'disc',
        'case_art',
        'cartridge',
        'cartridge_holder',
        'clamshell',
        'box_or_case',
        'art_or_holder',
        'case_sticker',
        'insert',
        'styrofoam',
        'complete',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'game_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
