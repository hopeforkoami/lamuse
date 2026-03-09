<?php

namespace App\Controllers;

use App\Models\Song;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ArtistController
{
    public function getDashboardStats(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('token')['user'];
        $artistId = $user->id;

        $totalSongs = Song::where('artist_id', $artistId)->count();
        $publishedSongs = Song::where('artist_id', $artistId)->where('status', 'published')->count();
        
        // Mock sales for now
        $totalSales = 125000; 
        $recentSongs = Song::where('artist_id', $artistId)->orderBy('created_at', 'desc')->limit(5)->get();

        $data = [
            'total_songs' => $totalSongs,
            'published_songs' => $publishedSongs,
            'total_sales' => $totalSales,
            'recent_songs' => $recentSongs
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function listSongs(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('token')['user'];
        $songs = Song::where('artist_id', $user->id)->get();
        
        $response->getBody()->write(json_encode($songs));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function uploadSong(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('token')['user'];
        $body = $request->getParsedBody();

        $song = Song::create([
            'title' => $body['title'],
            'artist_id' => $user->id,
            'status' => 'draft',
            'price' => $body['price'] ?? 0,
            'currency_code' => $body['currency_code'] ?? 'XOF',
            'genre' => $body['genre'] ?? 'Unknown',
            's3_key_main' => $body['s3_key_main'] ?? null,
            's3_key_teaser' => $body['s3_key_teaser'] ?? null
        ]);

        $response->getBody()->write(json_encode($song));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function updateProfile(Request $request, Response $response): Response
    {
        $userToken = $request->getAttribute('token')['user'];
        $user = User::find($userToken->id);
        $body = $request->getParsedBody();

        $user->name = $body['name'] ?? $user->name;
        $user->country = $body['country'] ?? $user->country;
        $user->save();

        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
