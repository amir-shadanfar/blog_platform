<?php

declare(strict_types=1);

use App\App;
use App\Contracts\MiddlewareDispatcherInterface;
use App\Contracts\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var App $app * */
$app = require __DIR__ . '/../bootstrap.php';
// container
$container = $app->getContainer();
// routes
$app->setRouter($container->get(RouterInterface::class));
// request
$app->setRequest($container->get(ServerRequestInterface::class));
// middleware
$app->setMiddlewareDispatcher($container->get(MiddlewareDispatcherInterface::class));
/**
 * Run the application
 */
$app->run();