<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->group('/table', function (RouteCollectorProxy $group) {
    $group->get('/{table}', function (Request $request, Response $response, $args) {
        global $database;
        
        $data = $database->select($args['table'], '*');
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json');
    });

    $group->get('/{table}/{id:[0-9]+}', function (Request $request, Response $response, $args) {
        global $database;
        
        $data = $database->select($args['table'], '*', [
            'id' => $args['id']
        ]);
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json');
    });

    $group->put('/{table}', function (Request $request, Response $response, $args) {
        global $database;
        
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['id'])) {
            
        }

        $database->insert($args['table'], $parsedBody);
         
        $id = $database->id();

        $response->getBody()->write(json_encode(['id' => $id]));
        return $response
            ->withHeader('Content-Type', 'application/json');
    });
});
