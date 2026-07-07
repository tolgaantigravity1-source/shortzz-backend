<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSaves extends Model
{
    use HasFactory;
    public $table = "post_saves";

    public function post()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }
}
