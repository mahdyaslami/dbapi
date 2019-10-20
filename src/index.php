<?php
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Middleware\JsonBodyParserMiddleware;

require __DIR__ . './../vendor/autoload.php';

/**
 * Database configuration.
 */
require __DIR__ . './db.php';

/**
 * Create app.
 */
$app = AppFactory::create();

/**
 * If api site is in the subdirectory we have to set subdirectories as base
 * path here.
 */
$app->setBasePath('/dbapi');

/**
 * Add middleware for manage body when content type is 'application/json'.
 */
$app->add(new JsonBodyParserMiddleware());

/**
 * Add routs.
 */
require __DIR__ . './routs.php';

/**
 * run.
 */
$app->run();
