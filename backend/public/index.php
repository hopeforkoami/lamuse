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
    "path" => ["/api/v1"],
    "ignore" => ["/api/v1/login", "/api/v1/register", "/api/v1/songs", "/api/v1/checkout", "/api/v1/payments/webhook", "/api/v1/health"],
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

// Register API routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

// Run app
$app->run();
