<?php

declare(strict_types=1);

use App\App;
use App\Contracts\ResponseEmitterInterface;
use Dotenv\Dotenv;

require __DIR__ . '/config/path_constants.php';
require ROOT_PATH . '/vendor/autoload.php';

// load environment file
$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// initial container
$container = require CONFIG_PATH . '/container.php';
$responseEmitter = $container->get(ResponseEmitterInterface::class);

return new App($container, $responseEmitter);
