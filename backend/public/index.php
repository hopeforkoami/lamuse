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
    "ignore" => ["/api/login", "/api/health"],
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

// Admin Routes
$app->group('/admin', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/artists', [\App\Controllers\AdminController::class, 'listArtists']);
    $group->post('/artists/{id}/star', [\App\Controllers\AdminController::class, 'updateArtistStar']);
    $group->get('/pricing-rules', [\App\Controllers\AdminController::class, 'listPricingRules']);
    $group->post('/pricing-rules', [\App\Controllers\AdminController::class, 'upsertPricingRule']);
    $group->get('/payment-health', [\App\Controllers\AdminController::class, 'getPaymentHealth']);
    $group->get('/dashboard-stats', [\App\Controllers\AdminController::class, 'getDashboardStats']);
})->add(new \App\Middleware\RoleMiddleware(['super_admin']));

$app->get('/health', function ($request, $response) {
    $response->getBody()->write(json_encode(['status' => 'UP']));
    return $response->withHeader('Content-Type', 'application/json');
});

// Run app
$app->run();
