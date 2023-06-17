<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\MiddlewareHandler;
use App\Contracts\AuthInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware extends MiddlewareHandler
{
    public function __construct(private readonly AuthInterface $auth)
    {
        //
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $user = $this->auth->user();
        if (!$user) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // parent::process($request, $handler);
        return $response;
    }
}