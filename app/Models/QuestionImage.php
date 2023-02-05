<?php

namespace App\Models;

use App\Traits\HasQuestionId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionImage extends Model
{
    use HasFactory;
    use HasQuestionId;

    public $timestamps = false;

    protected $guarded = ['id'];
}
