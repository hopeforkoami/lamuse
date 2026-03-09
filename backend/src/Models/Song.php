<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'title',
        'artist_id',
        'status', // draft | published | upcoming | archived
        'price',
        'currency_code',
        'main_audio_s3_key',
        'teaser_audio_s3_key',
        'cover_s3_key',
        'genre',
        'duration',
        'release_date',
        'description',
    ];

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function entitlements()
    {
        return $this->hasMany(Entitlement::class);
    }
}
