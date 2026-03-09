<?php

use Slim\Factory\AppFactory;
use App\Handlers\HttpErrorHandler;
use App\Handlers\ShutdownHandler;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Middleware\ErrorMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Initialize Database
\App\Config\Database::init();

// Instantiate App
$app = AppFactory::create();

// Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Set base path if needed
$app->setBasePath('/api');

// Set the strategy for route callbacks
$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy(new RequestResponseArgs());

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    $_ENV['APP_DEBUG'] === 'true',
    true,
    true
);

// JWT Middleware
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => ["/api"],
    "ignore" => ["/api/login", "/api/register", "/api/songs", "/api/checkout", "/api/payments/webhook", "/api/health"],
    "secret" => $_ENV['JWT_SECRET'],
    "attribute" => "token",
    "secure" => false,
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));

// Define API routes
$app->post('/login', [\App\Controllers\AuthController::class, 'login']);
$app->post('/register', [\App\Controllers\AuthController::class, 'register']);

// Storefront Routes (Public)
$app->get('/songs', [\App\Controllers\StoreController::class, 'listSongs']);
$app->get('/songs/{id}', [\App\Controllers\StoreController::class, 'getSong']);

// Payment Routes (Handle both public webhooks and potentially guest checkouts)
$app->post('/checkout', [\App\Controllers\OrderController::class, 'createOrder']);
$app->post('/payments/webhook/{provider}', [\App\Controllers\OrderController::class, 'handleWebhook']);

// Admin Routes
$app->group('/admin', function (\Slim\Routing\RouteCollectorProxy $group) {
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
$app->group('/artist', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/dashboard-stats', [\App\Controllers\ArtistController::class, 'getDashboardStats']);
    $group->get('/songs', [\App\Controllers\ArtistController::class, 'listSongs']);
    $group->post('/songs', [\App\Controllers\ArtistController::class, 'uploadSong']);
    $group->post('/profile', [\App\Controllers\ArtistController::class, 'updateProfile']);
    $group->get('/export-sales', [\App\Controllers\ArtistController::class, 'exportSalesReport']);
})->add(new \App\Middleware\RoleMiddleware(['artist']));

// Buyer Routes
$app->group('/buyer', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/library', [\App\Controllers\BuyerController::class, 'getLibrary']);
    $group->get('/download/{id}', [\App\Controllers\BuyerController::class, 'downloadSong']);
})->add(new \App\Middleware\RoleMiddleware(['client']));

$app->get('/health', function ($request, $response) {
    $response->getBody()->write(json_encode(['status' => 'UP']));
    return $response->withHeader('Content-Type', 'application/json');
});

// Run app
$app->run();
