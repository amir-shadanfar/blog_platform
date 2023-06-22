<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ViewNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;
use App\View;

class BlogController extends AbstractController
{
    /**
     * @param Environment $twig
     */
    public function __construct(private readonly Environment $twig)
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
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $bodyContent = $body = $this->twig->render('blogs\index.twig', [
            'blogs' => [],
            'pages' => 1,
        ]);

        $response->getBody()->write($bodyContent);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!isset($_GET['id'])) {
            // redirect to show method
            header('Location: /blogs');
            exit;
        }
        $id = (int)$_GET['id'];

        $bodyContent = $this->twig->render('blogs/detail.twig', ['blog' => $this->blogRepo->find($id)]);
        $response->getBody()->write($bodyContent);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $bodyContent = $this->twig->render('blogs/form.twig');
        $response->getBody()->write($bodyContent);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    public function store(ServerRequestInterface $request, ResponseInterface $response)
    {
        $input = $_POST;
        try {
            $validator = new Validator($input);
            $validator->rule('required', ['title', 'description', 'image_url']);
            $validator->rule('url', 'image_url');
            // create blog

            // redirect to show method
            header('Location: /blogs');
            exit;
        } catch (\Throwable $exception) {
        }
    }
}