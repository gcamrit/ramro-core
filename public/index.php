<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


require_once __DIR__.'/../vendor/autoload.php';

$app = new \Ramro\Application;

$app->run();