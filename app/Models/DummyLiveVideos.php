<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DummyLiveVideos extends Model
{
    use HasFactory;
    public $table = "dummy_live_videos";

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

}
