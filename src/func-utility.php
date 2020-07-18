<?php

function jsonResult($statusCode, $errorMessage, $developerMessage, $data)
{
    header('Content-Type: application/json');
    echo json_encode([
        'statusCode' => $statusCode,
        'errorMessage' => $errorMessage,
        'developerMessage' => $developerMessage,
        'data' => $data
    ]);
}
