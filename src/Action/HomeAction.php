<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['hello' => 'world']));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function SendHello(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['hello']));

        return $response->withHeader('Content-Type', 'application/json');
    }
}