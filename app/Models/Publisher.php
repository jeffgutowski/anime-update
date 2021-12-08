<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use CrudTrait, SoftDeletes;

    protected $table = 'publishers';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function games()
    {
        return $this->belongsToMany("App\Models\Game");
    }
}
