<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;

class RoleMiddleware implements MiddlewareInterface
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getAttribute('token');
        
        if (!$token || !isset($token['role']) || !in_array($token['role'], $this->allowedRoles)) {
            throw new HttpForbiddenException($request, "Insufficient permissions.");
        }

        return $handler->handle($request);
    }
}
