<?php

namespace App\Models;

use App\Traits\HasUserId;
use App\Traits\HasArticleId;
use App\Traits\HasPreferenceId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelevancePoint extends Model
{
    use HasFactory;
    use HasPreferenceId;
    use HasUserId;


    protected $table = 'relevance_points';

    protected $fillable = [
        'user_id',
        'preference_id',
        'point',
    ];

    public function scopeUserId($query, $userId)
    {
        $query->where('user_id', $userId);
    }

    public function scopePreferenceId($query, $preferenceId)
    {
        $query->where('preference_id', $preferenceId);
    }
}
