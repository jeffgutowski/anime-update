<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Trophy extends Model
{
    use CrudTrait;

    protected $table = 'trophies';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'threshold',
        'tier',
        'type',
        'platform_id',
        'region',
    ];

    public function platform()
    {
        $this->belongsTo('App\Models\Platform');
    }
}
