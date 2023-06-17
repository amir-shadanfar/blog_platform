<?php

declare(strict_types=1);

use App\Middlewares\AuthMiddleware;
use App\Middlewares\GuestMiddleware;
use App\View;
use App\Router;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\CommentController;

return function (Router $router) {
    // blog
    $router->get('/', [BlogController::class, 'index']);
    $router->get('/blog', [BlogController::class, 'show']);
    $router->get('/blog/create', [BlogController::class, 'create'])->middleware(AuthMiddleware::class);
    $router->post('/blog', [BlogController::class, 'store']);
    // auth
    $router->get('/login', [AuthController::class, 'loginPage'])->middleware(GuestMiddleware::class);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'registerPage'])->middleware(GuestMiddleware::class);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout'])->middleware(AuthMiddleware::class);
    // comment
    $router->post('/comment', [CommentController::class, 'store'])->middleware(AuthMiddleware::class);
    $router->delete('/comment', [BlogController::class, 'delete'])->middleware(AuthMiddleware::class);

    // closure routeing without rendering with twig
    $router->get('/imprint', function () {
        return View::make('imprint');
    });
};