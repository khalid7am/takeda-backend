<?php

namespace App\Models;

use App\Traits\HasUserId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoleChangeRequest extends Model
{
    use HasFactory;
    use HasUserId;

    protected $guarded = ['id'];

    protected $casts = [
        "is_accepted" => "bool",
    ];

    public function from()
    {
        return $this->belongsTo(Role::class, 'from_role_id');
    }

    public function to()
    {
        return $this->belongsTo(Role::class, 'to_role_id');
    }

    public function decidedBy(){
        return $this->belongsTo(User::class, 'decided_by');
    }
}
