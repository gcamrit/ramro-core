<?php

namespace Ramro\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

class HttpMessageServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        ResponseInterface::class,
        ServerRequestInterface::class,
        EmitterInterface::class,
    ];

    public function register()
    {
        $this->container->share(ResponseInterface::class, function () {
            return new \Zend\Diactoros\Response;
        });

        $this->container->share(ServerRequestInterface::class, function () {
            return ServerRequestFactory::fromGlobals(
                $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
            );
        });

        $this->container->share(EmitterInterface::class, function () {
            return new SapiEmitter;
        });
    }
}
