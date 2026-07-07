<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    public $table = "tbl_post";

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }
    public function music()
    {
        return $this->hasOne(Musics::class, 'id', 'sound_id');
    }
    public function images()
    {
        return $this->hasMany(PostImages::class, 'post_id', 'id');
    }
    public function comments()
    {
        return $this->hasMany(PostComments::class, 'post_id', 'id');
    }



}
