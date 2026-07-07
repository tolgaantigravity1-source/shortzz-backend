<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBlocks extends Model
{
    use HasFactory;
    public $table = "tbl_user_block";

    public function from_user()
    {
        return $this->hasOne(Users::class, 'id', 'from_user_id');
    }
    public function to_user()
    {
        return $this->hasOne(Users::class, 'id', 'to_user_id');
    }
}
