<?php

use App\Controller\TableAndViewController;

$app->get('/{table}', TableAndViewController::class . ':getAll');
$app->get('/{table}/{id:[0-9]+}', TableAndViewController::class . ':getById');
$app->put('/{table}', TableAndViewController::class . ':insert');