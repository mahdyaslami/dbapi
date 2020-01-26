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
        $result = [];
        $error = false;

        $data = $database->select($args['table'], '*');

        $error = $this->getError();

        if ($error !== false) {
            /**
             * Change response status to 400 because an error has thrown
             * if the table was empty $data isn't false and api will return
             * emtpy array.
             */
            $response = $response->withStatus(400);

            $result = $error;
        } elseif ($data === false) {
            $response = $response->withStatus(204);
            $result = false;
        } elseif (empty($data)) {
            $response = $response->withStatus(200);
            $result = [];
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
        $error = false;

        $data = $database->select($args['table'], '*', [
            'id' => $args['id']
        ]);

        $error = $this->getError();

        if ($error !== false) {
            /**
             * Change response status to 400 because an error has thrown
             * if the table was empty $data isn't false and api will return
             * emtpy array.
             */
            $response = $response->withStatus(400);

            $result = $error;
        } elseif ($data === false) {
            $response = $response->withStatus(204);
            $result = false;
        } elseif (empty($data)) {
            $response = $response->withStatus(200);
            $result = [];
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
        $error = false;
        $id = null;
        $isUpdate = false;

        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['id'])) {
            $id = $parsedBody['id'];
            unset($parsedBody['id']);

            $data = $database->update($args['table'], $parsedBody, [
                'id' => $id
            ]);

            $error = $this->getError();

            $isUpdate = true;
        } else {
            $data = $database->insert($args['table'], $parsedBody);

            $error = $this->getError();
        }

        $affectedRows = $data->rowCount();
        if ($error !== false) {
            /**
             * Change response status to 400 because an error has thrown
             * if the table was empty $data isn't false and api will return
             * emtpy array.
             */
            $response = $response->withStatus(400);

            $result = $error;
        } elseif ($affectedRows > 0 && $isUpdate == true) {
            /**
             * If PUT request has updated existing rows so we will
             * add affected rows to the response for making difference
             * between update and insert response.
             */
            $result = [
                'id' => $id,
                'affectedRows' => $affectedRows
            ];
        } elseif ($affectedRows > 0 && $isUpdate == false) {
            /**
             * New row is created so response status have to change to
             * (201 - Created) status.
             */
            $response = $response->withStatus(201);
            $result = ['id' => $database->id()];
        } elseif ($affectedRows === 0) {
            $response = $response->withStatus(200);
            $result = [
                'affectedRows' => $affectedRows
            ];
        } elseif ($data === false) {
            $response = $response->withStatus(204);
            $result = false;
        } elseif (empty($data)) {
            $response = $response->withStatus(200);
            $result = [];
        } else {
            $result = $data;
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args)
    {
        global $database;
        $result = null;
        $error = false;
        $id = $args['id'];

        $parsedBody = $request->getParsedBody();

        $data = $database->update($args['table'], $parsedBody, [
            'id' => $id
        ]);

        $error = $this->getError();

        $affectedRows = $data->rowCount();
        if ($error !== false) {
            /**
             * Change response status to 400 because an error has thrown
             * if the table was empty $data isn't false and api will return
             * emtpy array.
             */
            $response = $response->withStatus(400);

            $result = $error;
        } elseif ($affectedRows > 0) {
            $result = [
                'id' => $id,
                'affectedRows' => $affectedRows
            ];
        } elseif ($affectedRows === 0) {
            $response = $response->withStatus(200);
            $result = [
                'affectedRows' => $affectedRows
            ];
        } elseif ($data === false) {
            $response = $response->withStatus(204);
            $result = false;
        } elseif (empty($data)) {
            $response = $response->withStatus(200);
            $result = [];
        } else {
            $result = $data;
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args)
    {
        global $database;
        $result = null;
        $error = false;
        $id = $args['id'];

        $data = $database->delete($args['table'], [
            'id' => $id
        ]);

        $error = $this->getError();

        $affectedRows = $data->rowCount();
        if ($error !== false) {
            /**
             * Change response status to 400 because an error has thrown
             * if the table was empty $data isn't false and api will return
             * emtpy array.
             */
            $response = $response->withStatus(400);

            $result = $error;
        } elseif ($affectedRows > 0) {
            $result = [
                'affectedRows' => $affectedRows
            ];
        } elseif ($affectedRows === 0) {
            $response = $response->withStatus(200);
            $result = [
                'affectedRows' => $affectedRows
            ];
        } elseif ($data === false) {
            $response = $response->withStatus(204);
            $result = false;
        } elseif (empty($data)) {
            $response = $response->withStatus(200);
            $result = [];
        } else {
            $result = $data;
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
            return false;
        }
    }
}
