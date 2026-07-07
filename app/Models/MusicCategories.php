<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicCategories extends Model
{
    use HasFactory;
    public $table = "tbl_sound_category";

    public function musics()
    {
        return $this->hasMany(Musics::class, 'category_id', 'id');
    }

}
