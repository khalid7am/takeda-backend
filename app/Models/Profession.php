<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function users()
    {
        $this->hasMany(User::class, "profession_id", "id");
    }
}
