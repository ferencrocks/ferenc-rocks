<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

use App\Controllers\ContactController;

function makeRoutes(Slim\App $app) {
    $app->get('/', function (Request $request, Response $response, PHPRenderer $renderer) {
        return $renderer->render($response, 'home.php', [
            'title' => 'Manifesto'
        ]);
    });

    $app->get('/curriculum-vitae', function (Request $request, Response $response, PHPRenderer $renderer) {
        return $renderer->render($response, 'cv.php', [
            'title' => 'Curriculum Vitae',
        ]);
    });

    $app->get('/contact', [ContactController::class, 'show']);
    $app->post('/contact', [ContactController::class, 'post']);
}