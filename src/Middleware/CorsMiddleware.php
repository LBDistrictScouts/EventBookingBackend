<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Add CORS headers to the response
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'https://greenway.lbdscouts.org.uk')
            ->withHeader('Access-Control-Allow-Origin', 'https://localhost:5173')
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8765')
            ->withHeader('Access-Control-Allow-Origin', '*') // Adjust the '*' to restrict domains
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-Token');
    }
}
