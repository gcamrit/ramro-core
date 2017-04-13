<?php

namespace Ramro;

use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use League\Container\ReflectionContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramro\Providers\HttpMessageServiceProvider;
use Ramro\Providers\RouteServiceProvider;
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

    protected function boot()
    {
        $container = $this->getContainer();
        $container->addServiceProvider(HttpMessageServiceProvider::class);
        $container->addServiceProvider(RouteServiceProvider::class);
    }

    public function run()
    {
        try {
            $response = $this->container->get('route')->dispatch(
                $this->container->get(ServerRequestInterface::class),
                $this->container->get(ResponseInterface::class)
            );

            return $this->container->get(EmitterInterface::class)->emit($response);

        } catch (\Exception $exception) {
            // register exception handler which returns PSR-7 Response
            throw $exception;
        }
    }
}
