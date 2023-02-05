<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasQuestionId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionChoice extends Model
{
    use HasFactory;
    use HasUuid;
    use HasQuestionId;

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        "is_correct" => "bool",
    ];

    public function answers()
    {
        return $this->hasMany(Answers::class, "choice_id", "id");
    }
}
