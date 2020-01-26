<?php

use App\Middleware\JsonBodyParserMiddleware;

/**
 * Add middleware for manage body when content type is 'application/json'.
 */
$app->add(new JsonBodyParserMiddleware());