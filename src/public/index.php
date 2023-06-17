<?php

declare(strict_types=1);

use App\App;
use App\Contracts\MiddlewareDispatcherInterface;
use App\Contracts\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

echo '<pre>';

/** @var App $app * */
$app = require __DIR__ . '/../bootstrap.php';

// container
$container = $app->getContainer();

// set routes
$router = $container->get(RouterInterface::class);
/** @var Closure $routes * */
$routes = require CONFIG_PATH . '/routes.php';
$routes($router);
$app->setRouter($router);

// request
$request = $container->get(ServerRequestInterface::class);
$app->setRequest($request);

$middlewareDispatcher = $container->get(MiddlewareDispatcherInterface::class);
/** @var Closure $middlewares * */
$middlewares = require CONFIG_PATH . '/middlewares.php';
$middlewares($middlewareDispatcher);
$app->setMiddlewareDispatcher($middlewareDispatcher);

/**
 * Run the application
 */
$app->run();