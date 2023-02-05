<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Preference;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasPreferenceId
{
    public function preference(): BelongsTo
    {
        return $this->belongsTo(Preference::class, 'preference_id', 'id');
    }
}