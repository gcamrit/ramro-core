<?php

namespace Ramro;

use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use League\Container\ReflectionContainer;

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
        $this->getContainer();
        $this->container->share('response', \Zend\Diactoros\Response::class);
        $this->container->share('request', function () {
            return \Zend\Diactoros\ServerRequestFactory::fromGlobals(
                $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
            );
        });

        $this->container->share('emitter', \Zend\Diactoros\Response\SapiEmitter::class);
    }
}