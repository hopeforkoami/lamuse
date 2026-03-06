<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // super_admin, artist, client
        'country',
        'star_ranking',
    ];

    protected $hidden = [
        'password',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class, 'artist_id');
    }
}
