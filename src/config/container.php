<?php

declare(strict_types=1);

use App\App;
use App\Auth;
use App\Config;
use App\Container;
use App\Contracts\AuthInterface;
use App\Contracts\MiddlewareDispatcherInterface;
use App\Contracts\RouterInterface;
use App\Contracts\SessionInterface;
use App\Contracts\UserInterface;
use App\Contracts\UserRepositoryInterface;
use App\DB;
use App\MiddlewareDispatcher;
use App\MiddlewareHandler;
use App\Migrations\Migration;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Router;
use App\Session;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


$container = new Container();

// Application
$container->bind(App::class, function (ContainerInterface $container) {
    return new App($container);
});

// Config
$container->bind(Config::class, fn() => new Config($_ENV));

// DB
$container->bind(DB::class, function (ContainerInterface $container) {
    $config = $container->get(Config::class);
    return new DB($config->db);
});

// Migration
$container->bind(Migration::class, function (ContainerInterface $container) {
    $db = $container->get(DB::class);
    return new Migration($db);It  bedcustom
});

// Session
$container->bind(SessionInterface::class, Session::class);

// Auth
$container->bind(AuthInterface::class, Auth::class);

// model & repositories
$container->bind(UserInterface::class, fn() => new User());
$container->bind(UserRepositoryInterface::class, UserRepository::class);

// Router
$container->bind(RouterInterface::class, Router::class);

$container->bind(
    ServerRequestInterface::class,
    fn() => new ServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])
);

$container->bind(RequestHandlerInterface::class, function (ContainerInterface $container) {
    return $container->get(RouterInterface::class);
});

$container->bind(MiddlewareDispatcherInterface::class, function (ContainerInterface $container) {
    $requestHandler = $container->get(RequestHandlerInterface::class);
    return new MiddlewareDispatcher($requestHandler, $container);
});

// Twig
//$container->bind(Environment::class, function (ContainerInterface $container) {
//    $config = $container->get(Config::class);
//
//    $twig =  new Environment(new FilesystemLoader(VIEW_PATH), [
//        'cache' => STORAGE_PATH . '/cache/templates',
//        'auto_reload' => $config->environment == AppEnvironment::Development->value,
//    ]);
//});

return $container;