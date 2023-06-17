<?php

declare(strict_types=1);

namespace App;

use App\Contracts\MiddlewareDispatcherInterface;
use App\Exceptions\MiddlewareException;
use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareDispatcher implements MiddlewareDispatcherInterface
{
    /**
     * @param RequestHandlerInterface $requestHandler
     * @param ContainerInterface $container
     */
    public function __construct(
        private requestHandlerInterface $requestHandler,
        private readonly ContainerInterface $container
    ) {
        //
    }

    /**
     * @param string|MiddlewareInterface|callable $middleware
     * @return MiddlewareInterface
     * @throws ContainerExceptionInterface
     * @throws MiddlewareException
     * @throws NotFoundExceptionInterface
     */
    private function resolve(string|MiddlewareInterface|callable $middleware): MiddlewareInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        if ($middleware instanceof Closure) {
            /** @var Closure $middleware */
            $middleware = $middleware->bindTo($this->container);
        }

        if (is_string($middleware)) {
            $resolvedMiddleware = $this->container->get($middleware);
            if ($resolvedMiddleware instanceof MiddlewareInterface) {
                return $resolvedMiddleware;
            }
        }

        throw new MiddlewareException(sprintf('%s is not instance if MiddlewareInterface', $middleware));
    }

    /**
     * @param string|MiddlewareInterface|callable $middleware
     * @return MiddlewareDispatcherInterface
     * @throws ContainerExceptionInterface
     * @throws MiddlewareException
     * @throws NotFoundExceptionInterface
     */
    public function addMiddleware(string|MiddlewareInterface|callable $middleware): MiddlewareDispatcherInterface
    {
        $middleware = $this->resolve($middleware);
        $next = $this->requestHandler;

        $this->requestHandler = new class ($middleware, $next) implements RequestHandlerInterface {
            public function __construct(
                private readonly MiddlewareInterface $middleware,
                private readonly RequestHandlerInterface $next
            ) {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->next);
            }
        };

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->requestHandler->handle($request);
    }
}