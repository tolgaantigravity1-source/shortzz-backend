<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followers extends Model
{
    use HasFactory;
    public $table = "tbl_followers";

    public function from_user()
    {
        return $this->hasOne(Users::class, 'id', 'from_user_id');
    }
    public function to_user()
    {
        return $this->hasOne(Users::class, 'id', 'to_user_id');
    }

}
