<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Contracts\AuthInterface;
use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment;

class ValidationErrorsMiddleware implements MiddlewareInterface
{
    /**
     * @param SessionInterface $session
     * @param Environment $twig
     */
    public function __construct(private readonly SessionInterface $session)
    {
        //
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($errors = $this->session->getFlash('errors')) {
            // $this->twig->addGlobal('errors', $errors);
        }

        return $handler->handle($request);
    }
}