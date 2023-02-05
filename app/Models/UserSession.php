<?php

namespace App\Models;

use App\Traits\HasUserId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;
    use HasUserId;

    public $timestamps = false;

    protected $dates = [
        'login_at',
        'logout_at'
    ];

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
    ];

    public function is_online()
    {
        return (strtotime($this->login_at) >= strtotime("-4 hours")) && is_null($this->logout_at);
    }
}
