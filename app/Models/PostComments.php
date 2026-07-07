<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComments extends Model
{
    use HasFactory;
    public $table = "tbl_comments";

    public function post()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }
    public function likes()
    {
        return $this->hasMany(CommentLikes::class, 'comment_id', 'id');
    }
    public function replies()
    {
        return $this->hasMany(CommentReplies::class, 'comment_id', 'id');
    }
}
