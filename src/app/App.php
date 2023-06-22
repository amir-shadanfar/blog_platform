<?php

declare(strict_types=1);

namespace App;

use App\Contracts\MiddlewareDispatcherInterface;
use App\Contracts\ResponseEmitterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Contracts\RouterInterface;
use Psr\Container\ContainerInterface;
use App\Exceptions\RouteNotFoundException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App
{
    /**
     * @var DB
     */
    private static DB $db;

    /**
     * @param ContainerInterface $container
     * @param ResponseEmitterInterface $responseEmitter
     * @param RouterInterface|null $router
     * @param ServerRequestInterface|null $request
     * @param MiddlewareDispatcherInterface|null $middlewareDispatcher
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private ContainerInterface $container,
        private ResponseEmitterInterface $responseEmitter,
        private ?RouterInterface $router = null,
        private ?ServerRequestInterface $request = null,
        private ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ) {
        // initial db
        static::$db = $container->get(DB::class);
    }

    /**
     * @return DB
     */
    public static function db(): DB
    {
        return static::$db;
    }

    /**
     * @return void
     */
    public function run()
    {
        $response = $this->middlewareDispatcher->handle($this->request);
        $this->responseEmitter->emit($response);
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewareDispatcher->addMiddleware($middleware);
        return $this;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @return Router|null
     */
    public function getRouter(): ?Router
    {
        return $this->router;
    }

    /**
     * @param Router $router
     * @return void
     */
    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @return MiddlewareDispatcherInterface|null
     */
    public function getMiddlewareDispatcher(): ?MiddlewareDispatcherInterface
    {
        return $this->middlewareDispatcher;
    }

    /**
     * @param MiddlewareDispatcherInterface $middlewareDispatcher
     */
    public function setMiddlewareDispatcher(MiddlewareDispatcherInterface $middlewareDispatcher): void
    {
        $this->middlewareDispatcher = $middlewareDispatcher;
    }
}