<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

class TableAndViewController 
{
    public function getAll(Request $request, Response $response, $args) 
    {
        global $database;
        $result = null;

        $data = $database->select($args['table'], '*');

        if ($data === false){
            // 404 - Not found
            $response = $response->withStatus(404);
            $result = [
                'message' => 'Table is not exists.'
            ];
        } else {
            $result = $data;
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function getById (Request $request, Response $response, $args) 
    {
        global $database;
        $result = null;

        $data = $database->select($args['table'], '*', [
            'id' => $args['id']
        ]);

        if ($data === false){
            // 404 - Not found
            $response = $response->withStatus(404);
            $result = [
                'message' => 'Table is not exists.'
            ];
        } else {
            $result = $data;
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function insert(Request $request, Response $response, $args) 
    {
        global $database;
        $result = null;

        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['id'])) {
            $id = $parsedBody['id'];
            unset($parsedBody['id']);

            $data = $database->update($args['table'], $parsedBody, [
                'id' => $id
            ]);

            if ($data->rowCount() == 1) {
                $result = json_encode(['id' => $id]);
            } else {
                // TODO در صورتی که کد خطا لازم دارد باید اینجا هم برگردد
            }
        } else {
            $data = $database->insert($args['table'], $parsedBody);
            
            if ($data->rowCount() == 1) {
                // 201 - Created
                $response = $response->withStatus(201);
                $result = json_encode(['id' => $database->id()]);
            } else {
                // TODO در صورتی که کد خطا لازم دارد باید اینجا هم برگردد
            }
        }
    
        $response->getBody()->write($result);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
