<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/viking.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/server.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/viking/service.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    returnError(405, 'Method not allowed');
    exit;
}

$data = getBody();

if (!validateMandatoryParams($data, ['name', 'health', 'attack', 'defense'])) {
    returnError(412, 'Mandatory parameters: name, health, attack, defense');
    exit;
}

try {
    verifyViking($data);
} catch (Exception $e) {
    returnError(400, $e->getMessage());
    exit;
}

$defaultWeapon = getDefaultWeapon();
if (!$defaultWeapon) {
    returnError(500, 'No default weapon available');
    exit;
}

$newVikingId = createViking($data['name'], $data['health'], $data['attack'], $data['defense'], $defaultWeapon['id']);
if (!$newVikingId) {
    returnError(500, 'Could not create the viking');
    exit;
}

echo json_encode([
    'id' => $newVikingId,
    'links' => [
        'self' => "/viking/findOne.php?id={$newVikingId}",
        'assign_weapon' => "/viking/addWeapon.php?viking_id={$newVikingId}",
        'delete' => "/viking/delete.php?id={$newVikingId}",
        'all_vikings' => "/viking/find.php"
    ]
]);
http_response_code(201);


function getDefaultWeapon() {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT * FROM weapon LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
