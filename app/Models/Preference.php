<?php

namespace App\Models;

use App\Traits\HasUuid;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Preference extends Model
{
    use HasFactory;
    use HasUuid;
    use HasSlug;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug')
                          ->skipGenerateWhen(fn () => !Schema::hasColumn('preferences', 'slug'));
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, UserPreference::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, ArticlePreference::class);
    }

    public function relevancePoints()
    {
        return $this->hasMany(RelevancePoint::class, 'preference_id', 'id');
    }

    public function related()
    {
        return $this->belongsToMany(Preference::class, 'related_preferences', 'preference_id', 'related_id');
    }
}
