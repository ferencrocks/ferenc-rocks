<?php
namespace App;

use Psr\Container\ContainerInterface;

use DI\Container;
use Slim\Views\PhpRenderer;
use Dotenv;

// session
session_cache_limiter(false);
session_start();

//.env
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

// DI
$container = new Container([
    PHPRenderer::class => function (ContainerInterface $container) {
        $renderer = new PhpRenderer(__DIR__ . '/views');
        $renderer->setLayout('layouts/default.php');

        return $renderer;
    },
]);

// app
$app = \DI\Bridge\Slim\Bridge::create($container);
makeRoutes($app);
$app->run();