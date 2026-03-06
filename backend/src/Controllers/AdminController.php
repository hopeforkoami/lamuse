<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\StarPricingRule;
use App\Models\PaymentHealth;
use App\Models\Song;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController
{
    // Artist Management
    public function listArtists(Request $request, Response $response): Response
    {
        $artists = User::where('role', 'artist')->get();
        $response->getBody()->write($artists->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateArtistStar(Request $request, Response $response, $id): Response
    {
        $data = $request->getParsedBody();
        $star = $data['star_ranking'] ?? 0;

        $artist = User::where('role', 'artist')->findOrFail($id);
        $artist->star_ranking = $star;
        $artist->save();

        $response->getBody()->write($artist->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Pricing Rules
    public function listPricingRules(Request $request, Response $response): Response
    {
        $rules = StarPricingRule::all();
        $response->getBody()->write($rules->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function upsertPricingRule(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $rule = StarPricingRule::updateOrCreate(
            [
                'star_level' => $data['star_level'],
                'currency_code' => $data['currency_code']
            ],
            [
                'min_price' => $data['min_price'],
                'max_price' => $data['max_price']
            ]
        );

        $response->getBody()->write($rule->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Payment Health
    public function getPaymentHealth(Request $request, Response $response): Response
    {
        // For now, get the latest health status for each provider
        $health = PaymentHealth::orderBy('last_check_at', 'desc')->get()->groupBy('provider')->map(function ($group) {
            return $group->first();
        })->values();

        $response->getBody()->write($health->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Dashboard Statistics
    public function getDashboardStats(Request $request, Response $response): Response
    {
        $totalArtists = User::where('role', 'artist')->count();
        $totalSongs = Song::count();
        $recentArtists = User::where('role', 'artist')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'created_at']);
        $recentSongs = Song::with('artist:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_artists' => $totalArtists,
            'total_songs' => $totalSongs,
            'recent_artists' => $recentArtists,
            'recent_songs' => $recentSongs,
        ];

        $response->getBody()->write(json_encode($stats));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
