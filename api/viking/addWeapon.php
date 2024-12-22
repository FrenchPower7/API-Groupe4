<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/viking.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/weapon.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/server.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    returnError(405, 'Method not allowed');
    exit;
}

$data = getBody();

if (!validateMandatoryParams($data, ['viking_id', 'weapon_id'])) {
    returnError(400, 'Missing parameters: viking_id, weapon_id');
    exit;
}

$viking = findOneViking($data['viking_id']);
if (!$viking) {
    returnError(404, 'Viking not found');
    exit;
}

$weapon = findWeaponById($data['weapon_id']);
if (!$weapon) {
    returnError(404, 'Weapon not found');
    exit;
}

try {
    $db = getDatabaseConnection();
    $sql = "UPDATE viking SET weaponId = :weapon_id WHERE id = :viking_id";  
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'weapon_id' => $data['weapon_id'],
        'viking_id' => $data['viking_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'message' => 'Weapon assigned successfully',
            'links' => [
                'self' => "/viking/findOne.php?id={$data['viking_id']}",
                'weapon' => "/weapon/findOne.php?id={$data['weapon_id']}"
            ]
        ]);
        http_response_code(200);
    } else {
        returnError(500, 'No changes were made or weapon already assigned');
    }
} catch (Exception $e) {
    returnError(500, 'An error occurred: ' . $e->getMessage());
}





