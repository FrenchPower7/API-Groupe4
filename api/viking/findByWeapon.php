<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/viking.php'; 

header('Content-Type: application/json');

if (!isset($_GET['weapon_id'])) {
    returnError(400, 'Missing parameter: weapon_id');
    return;
}

$weapon_id = intval($_GET['weapon_id']);

try {
    $vikings = findVikingsByWeapon($weapon_id);

    if (empty($vikings)) {
        returnError(404, 'No vikings found for this weapon');
        return;
    }

    $response = [];
    foreach ($vikings as $viking) {
        $response[] = [
            'id' => $viking['id'],
            'name' => $viking['name'],
            'health' => $viking['health'],
            'attack' => $viking['attack'],
            'defense' => $viking['defense'],
            'weapon' => [
                'name' => $viking['weapon_name'],
                'link' => "http://localhost/api/weapon/$viking[weapon_id]"
            ]
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    returnError(500, 'Database connection error: ' . $e->getMessage());
}
?>
