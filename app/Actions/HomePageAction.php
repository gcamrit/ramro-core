<?php

namespace Ramro\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomePageAction {

    function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write('<h1>Hello, World!</h1>');

        return $response;
    }
}
