<?php
require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Middleware\JsonBodyParserMiddleware;

/**
 * Database configuration.
 */
require __DIR__ . '/db.php';

/**
 * |---------------------------------------------------------------------------
 * |---------- Basic Configuration
 * |---------------------------------------------------------------------------
 */

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
 * |---------------------------------------------------------------------------
 * |---------- Error handling
 * |---------------------------------------------------------------------------
 */

/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();

/**
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 * 
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

/**
 * Add routs.
 */
require __DIR__ . '/routs.php';

/**
 * |---------------------------------------------------------------------------
 * |---------- CORS
 * |---------------------------------------------------------------------------
 */
$app->options('/{routes:.+}', function (Request $request, Response $response, $args) {
    return $response;
});

$app->add(function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

/**
 * run.
 */
$app->run();
