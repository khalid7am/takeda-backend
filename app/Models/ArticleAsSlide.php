<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleAsSlide extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'article_as_slides';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'article_id',
        'path',
        'ppt_name',
        'pdf_name',
        'created_by_id',
        'unique_identifier',
    ];



    public function scopePptName($query, $pptName)
    {
        $query->where('ppt_name', $pptName);
    }
    
    public function scopeIdentifier($query, $identifier)
    {
        $query->where('unique_identifier', $identifier);
    }

    public function scopeCreatedById($query, $createdById)
    {
        $query->where('created_by_id', $createdById);
    }


    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function slideImages()
    {
        return $this->hasMany(ArticleAsSlideImage::class)->orderBy('order');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by_id = auth()->id();
        });
    }
}
