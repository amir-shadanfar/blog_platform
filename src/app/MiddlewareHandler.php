<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareHandler implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private RequestHandlerInterface $nextHandler;

    /**
     * @param RequestHandlerInterface $handler
     * @return RequestHandlerInterface
     */
    public function setNext(RequestHandlerInterface $handler): RequestHandlerInterface
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->nextHandler->handle($request);
    }
}