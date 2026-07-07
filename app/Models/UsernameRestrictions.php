<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsernameRestrictions extends Model
{
    use HasFactory;
    public $table = "restriction_username";
}
