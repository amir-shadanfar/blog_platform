<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Contracts\AuthInterface;
use App\Contracts\SessionInterface;
use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    /**
     * @param SessionInterface $session
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
        $response = null;
        try {
            $response = $handler->handle($request);
            return $response;
        } catch (ValidationException $e) {
            // add errors as a flush message
            $this->session->flash('errors', $e->errors);
            $this->session->flash('old', $request->getParsedBody());
            // redirect back
            $referer = $request->getServerParams()['HTTP_REFERER'];
            return $response->withHeader('Location', $referer)->withStatus(302);
        }
    }
}