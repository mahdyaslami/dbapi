<?php
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . './vendor/autoload.php';
require __DIR__ . './vendor/catfan/medoo/src/medoo.php';
require __DIR__ . './db.php';

$app = AppFactory::create();
$app->setBasePath('/dbapi');
$app->add(function (Request $request, RequestHandler $handler) {
    $contentType = $request->getHeaderLine('Content-Type');

    if (strstr($contentType, 'application/json')) {
        $contents = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $request = $request->withParsedBody($contents);
        }
    }

    return $handler->handle($request);
});

require __DIR__ . './routs.php';

$app->run();
