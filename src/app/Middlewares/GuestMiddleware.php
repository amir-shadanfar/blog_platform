<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Contracts\AuthInterface;
use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(private AuthInterface $auth)
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
        if ($user) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $response;
    }
}