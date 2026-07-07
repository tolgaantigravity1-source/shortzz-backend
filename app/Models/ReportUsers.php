<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUsers extends Model
{
    use HasFactory;
    public $table = "report_user";

    public function by_user()
    {
        return $this->hasOne(Users::class, 'id', 'by_user_id');
    }
    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }
}
