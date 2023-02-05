<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasArticleId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    use HasUuid;
    use HasArticleId;

    protected $guarded = ['id'];

    protected $with = ['choices', 'reasons', 'images'];

    public $timestamps = false;

    public function choices()
    {
        return $this->hasMany(QuestionChoice::class, 'question_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answers::class, 'question_id', 'id');
    }

    public function reasons()
    {
        return $this->hasMany(QuestionReason::class, 'question_id', 'id')->orderBy("order");
    }

    public function images()
    {
        return $this->hasMany(QuestionImage::class, 'question_id', 'id')->orderBy("order");
    }

    public function getImages()
    {
        $urls = [];

        foreach ($this->images as $key => $image) {
            $urls[$key]['path'] = config('app.url') . \Storage::url($image['path']);
        }
        
        return $urls;
    }
}
