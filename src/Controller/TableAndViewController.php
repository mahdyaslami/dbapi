<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

class TableAndViewController 
{
    public function getAll(Request $request, Response $response, $args) {
        global $database;
        
        $data = $database->select($args['table'], '*');

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function getById (Request $request, Response $response, $args) {
        global $database;
        
        $data = $database->select($args['table'], '*', [
            'id' => $args['id']
        ]);
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function insert(Request $request, Response $response, $args) {
        global $database;
        
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['id'])) {
            // TODO در صورتی که این آیدی موجود بود بروزرسانی کند و سطر جدیدی اضافه نکند
        } 
    
        $database->insert($args['table'], $parsedBody);
            
        $id = $database->id();
    
        $response->getBody()->write(json_encode(['id' => $id]));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
