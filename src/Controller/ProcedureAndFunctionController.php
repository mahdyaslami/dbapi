<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

class ProcedureAndFunctionController
{
    private $parametersSequence;

    private $parameterArray;

    public function getAll(Request $request, Response $response, $args)
    {
        global $database;
        $result = [];
        $error = false;

        $procedure = htmlspecialchars($args['procedure'], ENT_QUOTES);
        $this->prepareParameters();

        $data = $database->query(
            'CALL ' . $procedure . '(' . $this->parametersSequence . ');',
            $this->parameterArray
        )->fetchAll();

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

    /**
     * This function will return parameters with comma separate
     * for using inside call store procedure query.
     */
    private function prepareParameters()
    {
        $i = 1;
        $prefix = 'p';
        
        $this->parameterArray = array();
        
        $name = $prefix . $i;
        if (isset($_GET[$name])) {
            $this->parametersSequence = ':' . $name;
            $this->parameterArray[':' . $name] = $this->getConvertedValue($_GET[$name]);
            $i++;
            $name = $prefix . $i;
        }

        while (isset($_GET[$name])) {
            $this->parametersSequence .= ', :' . $name;
            $this->parameterArray[':' . $name] = $this->getConvertedValue($_GET[$name]);
            $i++;
            $name = $prefix . $i;
        }
    }

    private function getConvertedValue($str) {
        $str = trim($str);

        if (is_numeric($str)) {
            return floatval($str);
        } elseif ($str === 'true' || $str === 'false') {
            return $str === 'true';
        } else {
            return $str;
        }
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
