<?php

/**
 * An Route Item:
 *  [
 *      'method' => GET, POST, DELETE, ... have to be uppercase.
 *      'path' => 
 *              Path of url and have to start with `/` like `/users`.
 *              Parameters have to be in `{}` like `/users/{id}`
 *              Parameters name have to be in snake case format like `start-date`.
 *      'params' => 
 *              Array of parameters that used in path. see following array:
 *                  [
 *          'id' => '\d+',
 *          'param-name' => 'regex-exp without parenthesis `()`'
 *      ]
 *      'callbacks' => 
 *              Array of functions that you want to invoke after each other.
 *              We always send two paramters to functions $request, $args.
 *                  $request: Contain current request.
 *                  $args: Contain path paramters value for example if path is
 *                      `/users/10` and pattern is `/users/{id}` you can get id 
 *                      with handle `$args['id']`.
 *  ]
 * An Route Group:
 *  [
 *      'path' => 
 *              Path of url and have to start with `/` like `/users`.
 *              Parameters have to be in `{}` like `/users/{id}`
 *              Parameters name have to be in snake case format like `start-date`.
 *      'params' => 
 *              Array of parameters that used in path. avoid repeating 
 *              parameters in children. see following array:
 *                  [
 *          'id' => '\d+',
 *          'param-name' => 'regex-exp without parenthesis `()`'
 *      ]
 *      'beforeCallbacks' => Execute before children callbacks.
 *      'afterCallbacks' => Execute after children callbacks.
 *              Array of functions that you want to invoke after each other.
 *              We always send two paramters to functions $request, $args.
 *                  $request: Contain current request.
 *                  $args: Contain path paramters value for example if path is
 *                      `/users/10` and pattern is `/users/{id}` you can get id 
 *                      with handle `$args['id']`.
 *       'children' =>
 *              Contain route items with current route group prefix.
 *  ]
 */
require_once(__DIR__ . '/../src/func-utility.php');

$routes = [
    [
        'method' => 'GET',
        'path' => '/{table}',
        'params' => [
            'table' => '\w+'
        ],
        'callbacks' => [
            function ($request, $args) {
                global $db;
                $table = $args['table'];
                
                require_once(__DIR__ . '/../src/get-table.php');
            }
        ]
    ]
];
