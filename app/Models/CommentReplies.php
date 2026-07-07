<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReplies extends Model
{
    use HasFactory;
    public $table = "comment_replies";

    public function comment()
    {
        return $this->hasOne(PostComments::class, 'id', 'comment_id');
    }
    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }
}
