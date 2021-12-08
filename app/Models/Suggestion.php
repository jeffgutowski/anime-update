<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Suggestion extends Model
{
    use CrudTrait;

    protected $table = 'suggestions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'reviewed_at',
        'product_id',
        'user_id',
        'name',
        'type',
        'company',
        'model_number',
        'description',
        'platform_id',
        'ntsc_u',
        'ntsc_j',
        'pal',
        'cover_us',
        'cover_jp',
        'cover_eu',
        'name_us',
        'name_jp',
        'name_eu',
        'upc_us',
        'upc_jp',
        'upc_eu',
        'catalog_number_us',
        'catalog_number_jp',
        'catalog_number_eu',
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
        'publishers_us',
        'publishers_eu',
        'publishers_jp',
        'developers',
        'genres',
        'comments',
        'review_comments',
    ];

    public function platform()
    {
        return $this->belongsTo('App\Models\Platform');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getReviewButton()
    {
        if (is_null($this->reviewed_at)) {
            return '<a style="color:black;" href="/suggestion/review/'.$this->id.'"><button>Review</button></a>';
        }
        return '';
    }
}
