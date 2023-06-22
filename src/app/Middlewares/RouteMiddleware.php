<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Contracts\RouterInterface;
use App\Exceptions\ContainerException;
use App\Exceptions\RouteNotFoundException;
use App\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @param RouterInterface $router
     */
    public function __construct(private readonly RouterInterface $router)
    {
        //
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerException
     * @throws ReflectionException
     * @throws RouteNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->router->handle($request);

        return $handler->handle($request);
    }
}