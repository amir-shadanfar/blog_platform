<?php

declare(strict_types=1);

namespace App;

use App\Contracts\RouterInterface;
use App\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

class Router implements RouterInterface, RequestHandlerInterface
{
    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @param Container $container
     */
    public function __construct(private readonly Container $container)
    {
        //
    }

    /**
     * @param string $requestMethod
     * @param string $route
     * @param callable|array $action
     * @return $this
     */
    public function register(string $requestMethod, string $route, callable|array $action): self
    {
        $this->routes[] = [
            'method' => $requestMethod,
            'route' => $route,
            'action' => $action,
            'middleware' => null,
        ];

        return $this;
    }

    /**
     * use for adding middleware
     *
     * @param $key
     * @return $this
     */
    public function middleware($key): self
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    /**
     * @param string $route
     * @param callable|array $action
     * @return $this
     */
    public function get(string $route, callable|array $action): self
    {
        return $this->register('get', $route, $action);
    }

    /**
     * @param string $route
     * @param callable|array $action
     * @return $this
     */
    public function post(string $route, callable|array $action): self
    {
        return $this->register('post', $route, $action);
    }

    /**
     * @param string $route
     * @param callable|array $action
     * @return $this
     */
    public function put(string $route, callable|array $action): self
    {
        return $this->register('put', $route, $action);
    }

    /**
     * @param string $route
     * @param callable|array $action
     * @return $this
     */
    public function delete(string $route, callable|array $action): self
    {
        return $this->register('delete', $route, $action);
    }

    /**
     * @return array
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exceptions\ContainerException
     * @throws ReflectionException
     * @throws RouteNotFoundException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->container->get(ResponseInterface::class);

        $requestUri = $request->getUri();
        $requestMethod = strtolower($request->getMethod());

        $action = null;
        foreach ($this->routes as $route) {
            if ($route['route'] == $requestUri->getPath() && $route['method'] == $requestMethod) {
                $action = $route['action'];
            }
        }

        if (is_null($action)) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_array($action)) {
            [$class, $method] = $action;
            if (class_exists($class)) {
                $class = $this->container->get($class);
                if (method_exists($class, $method)) {
                    return call_user_func([$class, $method], $request, $response);
                }
            }
        }

        throw new RouteNotFoundException();
    }
}