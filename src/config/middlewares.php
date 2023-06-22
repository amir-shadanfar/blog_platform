<?php

declare(strict_types=1);

use App\MiddlewareDispatcher;
use App\Middlewares\RouteMiddleware;
use App\Middlewares\StartSessionMiddleware;
use App\Middlewares\ValidationErrorsMiddleware;
use App\Middlewares\ValidationExceptionMiddleware;

return function (MiddlewareDispatcher $middlewareDispatcher) {
    $middlewareDispatcher->addMiddleware(StartSessionMiddleware::class);
    $middlewareDispatcher->addMiddleware(RouteMiddleware::class);
    $middlewareDispatcher->addMiddleware(ValidationErrorsMiddleware::class);
    $middlewareDispatcher->addMiddleware(ValidationExceptionMiddleware::class);
};