<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;
    public $table = "notification_users";

    public function from_user()
    {
        return $this->hasOne(Users::class, 'id', 'from_user_id');
    }
    public function to_user()
    {
        return $this->hasOne(Users::class, 'id', 'to_user_id');
    }

}
