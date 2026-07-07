<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostImages extends Model
{
    use HasFactory;
    public $table = "post_images";

    public function post()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
}
