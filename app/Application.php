<?php

namespace Ramro;

use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use League\Container\ReflectionContainer;
use League\Route\RouteCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramro\Providers\HttpMessageServiceProvider;
use Zend\Diactoros\Response\EmitterInterface;

class Application implements ContainerAwareInterface
{
    /**
     * @var \League\Container\ContainerInterface
     */
    protected $container;

    public function __construct()
    {
        $this->boot();
    }

    /**
     * Set a container
     *
     * @param \League\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get the container
     *
     * @return \League\Container\ContainerInterface
     */
    public function getContainer()
    {
        if (isset($this->container)) {
            return $this->container;
        }

        $container = new Container;
        $container->delegate(new ReflectionContainer());
        $this->setContainer($container);

        return $container;
    }

    private function boot()
    {
        $container = $this->getContainer();
        $container->addServiceProvider(HttpMessageServiceProvider::class);
    }

    public function run()
    {
        try {
            $route = new RouteCollection($this->container);

            $route->map('GET', '/', function (ServerRequestInterface $request, ResponseInterface $response) {
                $response->getBody()->write('<h1>Hello, World!</h1>');

                return $response;
            });

            $response = $route->dispatch($this->container->get(ServerRequestInterface::class), $this->container->get(ResponseInterface::class));

            return $this->container->get(EmitterInterface::class)->emit($response);

        }catch (\Exception $exception) {
            // register exception handler which returns PSR-7 Response
            throw $exception;
        }
    }
}