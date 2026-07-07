<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPosts extends Model
{
    use HasFactory;
    public $table = "report_posts";

    public function by_user()
    {
        return $this->hasOne(Users::class, 'id', 'by_user_id');
    }
    public function post()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
}
