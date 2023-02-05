<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordReset extends Model
{
    use HasFactory;
    protected $table = "password_resets";

    protected $guarded = [];

    public $timestamps = ["created_at"];
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'id');
    }

    public function scopeValid($query)
    {
        $query->where('created_at', '>=', Carbon::now()->subMinutes(config('auth.passwords.users.expire')))->latest('created_at');
    }
}
