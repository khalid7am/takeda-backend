<?php

namespace App\Models;

use App\Traits\HasUserId;
use App\Traits\HasPreferenceId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;
    use HasUserId;
    use HasPreferenceId;

    public $timestamps = false;
}
