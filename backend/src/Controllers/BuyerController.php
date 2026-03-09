<?php

namespace App\Controllers;

use App\Models\Song;
use App\Models\Order;
use App\Models\Entitlement;
use App\Services\StorageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BuyerController
{
    protected $storage;

    public function __construct()
    {
        $this->storage = new StorageService();
    }

    public function getLibrary(Request $request, Response $response): Response
    {
        $token = $request->getAttribute("token");
        $userId = $token['uid'];

        $songs = Song::whereHas('entitlements', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('artist')->get();

        $response->getBody()->write(json_encode($songs));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function downloadSong(Request $request, Response $response, array $args): Response
    {
        $songId = $args['id'];
        $token = $request->getAttribute("token");
        $userId = $token['uid'];

        $entitled = Entitlement::where('user_id', $userId)
            ->where('song_id', $songId)
            ->exists();

        if (!$entitled) {
            $response->getBody()->write(json_encode(['error' => 'Not entitled to download this song']));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        $song = Song::find($songId);
        
        $signedUrl = $this->storage->getPresignedUrl($song->main_audio_s3_key, '+2 hours');

        $response->getBody()->write(json_encode(['download_url' => $signedUrl]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
