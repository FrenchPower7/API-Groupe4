<?php

function methodIsAllowed(string $action): bool {
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($action) {
        case 'update':
        case 'create':
            return $method == 'PUT';
        case 'read':
            return $method == 'GET';
        case 'delete':
            return $method == 'DELETE';
        default:
            return false;
    }
}

function getBody() {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        returnError(400, 'Invalid JSON');
        exit;
    }
    return $data;
}



function returnError (int $code, string $message) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}

function validateMandatoryParams($data, $requiredParams) {
    foreach ($requiredParams as $param) {
        if (!isset($data[$param])) {
            return false; 
        }
    }

}