<?php

declare(strict_types=1);

namespace App;

use App\Contracts\MiddlewareDispatcherInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Contracts\RouterInterface;
use Psr\Container\ContainerInterface;
use App\Exceptions\RouteNotFoundException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{
    /**
     * @var DB
     */
    private static DB $db;

    /**
     * @param ContainerInterface $container
     * @param RouterInterface|null $router
     * @param ServerRequestInterface|null $request
     * @param MiddlewareDispatcherInterface|null $middlewareDispatcher
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private ContainerInterface $container,
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
    public function run(): void
    {
        try {

            // $response = $this->handle($this->request);
            // @todo refactor with responseFactory [view|json]

            // resolve route & handle middleware
            echo $this->router->resolve($this->request, $this->middlewareDispatcher);
        } catch (RouteNotFoundException) {
            http_response_code(404);

            echo View::make('errors/404');
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response =  $this->middlewareDispatcher->handle($request);
        return $response;
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