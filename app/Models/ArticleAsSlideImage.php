<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class ArticleAsSlideImage extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'article_as_slide_images';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'article_as_slide_id',
        'article_id',
        'path',
        'order',
        'created_by_id',
        'order_question',
    ];


    public function getImageUrlAttribute()
    {
        return Storage::disk('ppt_files')->url($this->path);
    }

    public function articleAsSlide()
    {
        return $this->belongsTo(ArticleAsSlide::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by_id = auth()->id();
        });
    }
}
