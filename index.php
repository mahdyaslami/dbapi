<?php

require_once(__DIR__ . '/configs/env.php');
require_once(__DIR__ . '/configs/routes.php');
require_once(__DIR__ . '/includes/global-variables.php');
require_once(__DIR__ . '/includes/func-router.php');
require_once('vendor/autoload.php');

//
// Database
//
use Medoo\Medoo;

$db = new Medoo($env['connection']);

//
// Execute project.
//

try {
    routeRequest($routes, $request);
} catch (Throwable $e) {
    http_response_code($e->getCode());
    throw $e;
}
