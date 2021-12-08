<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Franchise extends Model
{
    use CrudTrait;

    protected $table = 'franchises';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];
}
