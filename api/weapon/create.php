<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/weapon.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/server.php';

header('Content-Type: application/json');

if (!methodIsAllowed('create')) {
    returnError(405, 'Method not allowed');
    return;
}

$data = getBody();
if (!validateMandatoryParams($data, ['type', 'damage'])) {
    returnError(400, 'Missing parameters: type, damage');
    exit;
}


$type = $data['type'];
$damage = $data['damage'];

$weaponId = createWeapon($type, $damage);

if ($weaponId) {
    echo json_encode([
        'message' => 'Weapon created successfully',
        'weaponId' => $weaponId
    ]);
} else {
    returnError(500, 'Failed to create weapon');
}

