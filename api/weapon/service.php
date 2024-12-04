<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/server.php';

function verifyWeapon($weapon): bool {
    $type = trim($weapon['type']);
    if (strlen($type) < 3) {
        returnError(412, 'Type must be at least 3 characters long');
    }

    $damage = intval($weapon['damage']);
    if ($damage < 1) {
        returnError(412, 'Damage must be a positive and non-zero number');
    }
    return true;
}
