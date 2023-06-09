<?php

declare(strict_types=1);

namespace App\Exceptions;

class RouteNotFoundException extends \Exception
{
    /**
     * @var string
     */
    protected $message = '404 Not Found!';
}