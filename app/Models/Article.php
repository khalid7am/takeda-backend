<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Types\ArticleType;
use App\Traits\HasAuthorId;
use Spatie\Sluggable\HasSlug;
use App\Traits\BelongsToArticle;
use DB;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    use BelongsToArticle;
    use HasUuid;
    use HasAuthorId;
    use HasSlug;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'content' => 'array'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->generateSlugsFrom('title')
                          ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    public function media()
    {
        //TODO: Ha tiszta lesz
        switch ($this->type) {
            case ArticleType::AUDIO:
                if (substr($this->media, 0, 4 ) === "http") {
                    return $this->media;
                }
                return config('app.url') . \Storage::url($this->media);
            case ArticleType::LEGO:
                break;
            case ArticleType::DEMO:
                break;
            case ArticleType::VIDEO:
                return json_decode($this->media);
        }
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, "author_id", "id");
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, "lecturer_id", "id");
    }

    public function getPreferencePointByUserAttribute()
    {
        $userId = auth()->id();
        // TESTING
        //$userId = 2;

        $point = 0;
        // GET PREFERENCE IDS
        $preferenceIds = $this->preferences->pluck('id');

        $point += RelevancePoint::userId($userId)->whereIn('preference_id', $preferenceIds)->sum('point') ?? 0;

        return $point;
    }

    public function preferences()
    {
        return $this->belongsToMany(Preference::class, ArticlePreference::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, "article_id", "id");
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, "article_id", "id");
    }

    public function questions()
    {
        return $this->hasMany(Question::class, "article_id", "id");
    }

    public function articleAsSlides()
    {
        return $this->hasMany(ArticleAsSlide::class);
    }

    public function slideImages()
    {
        return $this->hasMany(ArticleAsSlideImage::class)->orderBy('order');
    }

    public function answers()
    {
        return $this->hasManyThrough(Answers::class, Question::class);
    }

    public function views()
    {
        return $this->hasMany(ArticleView::class);
    }

    public function downloads()
    {
        return $this->hasMany(ArticleDownload::class);
    }

    public function articleUserAnswered()
    {
        return $this->hasMany(ArticleUserAnswered::class);
    }

    public function loggedInUserArticleAnswer()
    {
        return $this->hasOne(ArticleUserAnswered::class)->where('user_id', auth()->id());
    }

    public function scopeDemo($query)
    {
        return $query->where('type', ArticleType::DEMO);
    }

    public function scopeLego($query)
    {
        return $query->where('type', ArticleType::LEGO);
    }

    public function scopeAudio($query)
    {
        return $query->where('type', ArticleType::AUDIO);
    }

    public function scopeVideo($query)
    {
        return $query->where('type', ArticleType::VIDEO);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeNotPublished($query)
    {
        return $query->whereNull('published_at');
    }

    public function scopeAuthorIs($query, User $author)
    {
        return $query->where('author_id', $author->getKey());
    }

    public function scopeType($query, $type)
    {
        $type = strtoupper($type);
        return $query->where('type', $type); // ArticleType::$type
    }

    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? null;
    }

    public function getReviewAverageAttribute()
    {
        return $this->getAverageRating();
    }

    public function getThumbnailUrl()
    {
        $url = '';
        if (isset(json_decode($this->content, true)[0])) {
            $content = json_decode($this->content, true)[0];
            if (!isset($content['postThumbnail']) || empty($content['postThumbnail'])) {
                return null;
            }

            $url = config('app.url') . \Storage::url($content['postThumbnail']);
        }

        return $url;
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($query) use ($searchTerm) {
            return $query->where('title', 'LIKE', "%$searchTerm%")
                         ->orWhere('excerpt', 'LIKE', "%$searchTerm%");
        });
    }

    public function viewed()
    {
        $this->views()->updateOrCreate(
            ['user_id' => auth()->id()]
        )->increment('count_views');
    }

    public function finished()
    {
        $this->views()
            ->where('user_id', auth()->id())
            ->firstOrFail()
            ->update(['is_finished' => true]);
    }

    public function downloaded()
    {
        $this->downloads()->updateOrCreate(
            ['user_id' => auth()->id()]
        )->increment('count_downloads');
    }

    public function count_views()
    {
        return $this->views->sum('count_views');
    }
}
