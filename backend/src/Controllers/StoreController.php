<?php

namespace App\Controllers;

use App\Models\Song;
use App\Services\StorageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StoreController
{
    protected $storage;

    public function __construct()
    {
        $this->storage = new StorageService();
    }

    public function listSongs(Request $request, Response $response): Response
    {
        $songs = Song::where('status', 'published')
            ->with(['artist' => function ($query) {
                $query->select('id', 'name', 'country', 'star_ranking');
            }])
            ->get();

        $response->getBody()->write(json_encode($songs));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getSong(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $song = Song::where('status', 'published')
            ->with(['artist' => function ($query) {
                $query->select('id', 'name', 'country', 'star_ranking');
            }])
            ->find($id);

        if (!$song) {
            $response->getBody()->write(json_encode(['error' => 'Song not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        if ($song->teaser_audio_s3_key) {
            $song->teaser_url = $this->storage->getPresignedUrl($song->teaser_audio_s3_key, '+10 minutes');
        }

        if ($song->cover_s3_key) {
            $song->cover_url = $this->storage->getPresignedUrl($song->cover_s3_key, '+1 hour');
        }

        $response->getBody()->write(json_encode($song));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
