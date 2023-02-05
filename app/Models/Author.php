<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'alkalmazott_tag',
        'reszvenytulajdon',
        'eloadoi_dij',
        'testuleti_reszvetel',
        'konzultacios_szerzodes',
        'tovabbkepzesi_hozzajarulas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
