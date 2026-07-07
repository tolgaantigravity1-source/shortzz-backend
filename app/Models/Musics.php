<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musics extends Model
{
    use HasFactory;
    public $table = "tbl_sound";

    public function category()
    {
        return $this->hasOne(MusicCategories::class, 'id', 'category_id');
    }

    public function user (){
        return $this->hasOne(Users::class, 'id', 'user_id');
    }


}
