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

        if ($data === false) {
            // 404 - Not found
            $response = $response->withStatus(404);

            $result = $this->getError();
        } else {
            $result = $data;
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, $args)
    {
        global $database;
        $result = null;

        $data = $database->select($args['table'], '*', [
            'id' => $args['id']
        ]);

        if ($data === false) {
            // 404 - Not found
            $response = $response->withStatus(404);

            $result = $this->getError();
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
        $id = null;
        $isUpdate = false;

        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['id'])) {
            $id = $parsedBody['id'];
            unset($parsedBody['id']);

            $data = $database->update($args['table'], $parsedBody, [
                'id' => $id
            ]);

            $isUpdate = true;
        } else {
            $data = $database->insert($args['table'], $parsedBody);
        }

        if ($data->rowCount() == 1 && $isUpdate == true) {
            $result = ['id' => $id];
        } elseif ($data->rowCount() == 1 && $isUpdate == false) {
            // 201 - Created
            $response = $response->withStatus(201);
            $result = ['id' => $database->id()];
        } else {
            $result = $this->getError();
            // TODO در صورتی که کد خطا لازم دارد باید اینجا هم برگردد
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args)
    {
        global $database;
        $result = null;
        $id = $args['id'];

        $parsedBody = $request->getParsedBody();

        $data = $database->update($args['table'], $parsedBody, [
            'id' => $id
        ]);

        if ($data->rowCount() == 1) {
            $result = ['id' => $id];
        } else {
            $result = $this->getError();
            // TODO در صورتی که کد خطا لازم دارد باید اینجا هم برگردد
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    private function getError()
    {
        global $database;

        $error = $database->error();
        if ($error != null && $error[0] != '00000') {
            return [
                'sqlStateErrorCode' => $error[0],
                'driverSpecificErrorCode' => $error[1],
                'message' => $error[2]
            ];
        } else {
            return [];
        }
    }
}
