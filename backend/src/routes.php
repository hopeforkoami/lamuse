<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/v1', function (RouteCollectorProxy $v1) {
        // Auth Routes
        $v1->post('/login', [\App\Controllers\AuthController::class, 'login']);
        $v1->post('/register', [\App\Controllers\AuthController::class, 'register']);

        // Storefront Routes (Public)
        $v1->get('/songs', [\App\Controllers\StoreController::class, 'listSongs']);
        $v1->get('/songs/{id}', [\App\Controllers\StoreController::class, 'getSong']);

        // Payment Routes (Handle both public webhooks and potentially guest checkouts)
        $v1->post('/checkout', [\App\Controllers\OrderController::class, 'createOrder']);
        $v1->post('/payments/webhook/{provider}', [\App\Controllers\OrderController::class, 'handleWebhook']);

        // Admin Routes
        $v1->group('/admin', function (RouteCollectorProxy $group) {
            $group->get('/artists', [\App\Controllers\AdminController::class, 'listArtists']);
            $group->post('/artists/{id}/star', [\App\Controllers\AdminController::class, 'updateArtistStar']);
            $group->get('/pricing-rules', [\App\Controllers\AdminController::class, 'listPricingRules']);
            $group->post('/pricing-rules', [\App\Controllers\AdminController::class, 'upsertPricingRule']);
            $group->get('/payment-health', [\App\Controllers\AdminController::class, 'getPaymentHealth']);
            $group->get('/dashboard-stats', [\App\Controllers\AdminController::class, 'getDashboardStats']);
            $group->get('/songs', [\App\Controllers\AdminController::class, 'listSongs']);
            $group->post('/songs/{id}/status', [\App\Controllers\AdminController::class, 'updateSongStatus']);
        })->add(new \App\Middleware\RoleMiddleware(['super_admin']));

        // Artist Routes
        $v1->group('/artist', function (RouteCollectorProxy $group) {
            $group->get('/dashboard-stats', [\App\Controllers\ArtistController::class, 'getDashboardStats']);
            $group->get('/songs', [\App\Controllers\ArtistController::class, 'listSongs']);
            $group->post('/songs', [\App\Controllers\ArtistController::class, 'uploadSong']);
            $group->post('/profile', [\App\Controllers\ArtistController::class, 'updateProfile']);
            $group->get('/export-sales', [\App\Controllers\ArtistController::class, 'exportSalesReport']);
        })->add(new \App\Middleware\RoleMiddleware(['artist']));

        // Buyer Routes
        $v1->group('/buyer', function (RouteCollectorProxy $group) {
            $group->get('/library', [\App\Controllers\BuyerController::class, 'getLibrary']);
            $group->get('/download/{id}', [\App\Controllers\BuyerController::class, 'downloadSong']);
        })->add(new \App\Middleware\RoleMiddleware(['client']));

        // Health Check
        $v1->get('/health', function ($request, $response) {
            $response->getBody()->write(json_encode(['status' => 'UP']));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });
};
