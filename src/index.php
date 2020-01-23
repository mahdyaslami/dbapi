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
require __DIR__ . './routs.php';

/**
 * run.
 */
$app->run();
