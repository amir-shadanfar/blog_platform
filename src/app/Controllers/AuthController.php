<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Contracts\AuthInterface;
use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

class AuthController extends AbstractController
{
    /**
     * @param AuthInterface $auth
     * @param Environment $twig
     */
    public function __construct(private readonly AuthInterface $auth, private readonly Environment $twig)
    {
        //
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function loginPage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $bodyContent = $this->twig->render('auth/login.twig');
        $response->getBody()->write($bodyContent);
        return $response;
    }

    /**
     * @return void
     */
    public function login()
    {
        $input = $_POST;
        try {
            $validator = new Validator($input);
            $validator->rule('required', ['email', 'password', 'password_confirmation']);
            $validator->rule('email', ['email']);
            $validator->rule('equals', 'password_confirmation', 'password');
            if (!$this->auth->attemptLogin($input)) {
                throw new ValidationException([
                    'password' => ['You have entered an invalid username or password']
                ]);
            }
            // redirect
            http_response_code(302);

            header('Location: /blogs');
            exit;
        } catch (\Throwable $exception) {
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function registerPage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $bodyContent = $this->twig->render('auth/register.twig');
        $response->getBody()->write($bodyContent);
        return $response;
    }

    /**
     * @return void
     */
    public function register()
    {
        $input = $_POST;
        try {
            $validator = new Validator($input);
            $validator->rule('required', ['email', 'password', 'password_confirmation']);
            $validator->rule('email', ['email']);
            $validator->rule('equals', 'password_confirmation', 'password');

            $user = $this->auth->register($input);

            // redirect
            http_response_code(302);

            header('Location: /blogs');
            exit;
        } catch (\Throwable $exception) {
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->auth->logOut();

        // redirect
        header('Location: /');
        exit();
    }
}