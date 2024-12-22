<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/viking.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/server.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/viking/service.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    returnError(405, 'Method not allowed');
    return;
}

$data = getBody();

if (!isset($_GET['id'])) {
    returnError(400, 'Missing parameter: id');
    return;
}

$id = intval($_GET['id']);

if (validateMandatoryParams($data, ['name', 'health', 'attack', 'defense'])) {
    verifyViking($data);

    $updated = updateViking($id, $data['name'], $data['health'], $data['attack'], $data['defense']);
    
    if ($updated == 1) {
        echo json_encode([
            'message' => 'Viking updated successfully',
            'viking' => [
                'id' => $id,
                'name' => $data['name'],
                'health' => $data['health'],
                'attack' => $data['attack'],
                'defense' => $data['defense']
            ]
        ]);
        http_response_code(200); // OK
    } elseif ($updated == 0) {
        returnError(404, 'Viking not found');
    } else {
        returnError(500, 'Could not update the viking');
    }
} else {
    returnError(412, 'Mandatory parameters: name, health, attack, defense');
}
