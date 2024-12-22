<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/database.php';


function findOneViking(string $id) {
    $db = getDatabaseConnection();
    $sql = "SELECT v.id, v.name, v.health, v.attack, v.defense, w.id AS weaponId, w.type AS weapon_type, w.damage AS weapon_damage  
            FROM viking AS v
            LEFT JOIN weapon w ON v.weaponId = w.id 
            WHERE v.id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id]);

    $viking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($viking) {
        return $viking;
    }

    return false;
}





function findAllVikings(string $name = "", int $limit = 10, int $offset = 0) {
    $db = getDatabaseConnection();
    $params = [];
    $sql = "SELECT v.id, v.name, v.health, v.attack, v.defense, w.id AS weapon_id, w.type AS weapon_type, w.damage AS weapon_damage  
            FROM viking AS v
            LEFT JOIN weapon w ON v.weaponId = w.id";
    
    $stmt = $db->prepare($sql);
    $res = $stmt->execute($params);
    if ($res) {
        $vikings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vikings as &$viking) {
            $viking['links'] = [
                'self' => "/viking/findOne.php?id={$viking['id']}",
                'assign_weapon' => "/viking/addWeapon.php?viking_id={$viking['id']}",
                'delete' => "/viking/delete.php?id={$viking['id']}"
            ];
        }

        return $vikings;
    }
    return null;
}



function createViking(string $name, int $health, int $attack, int $defense) {
    $db = getDatabaseConnection();
    $sql = "INSERT INTO viking (name, health, attack, defense) VALUES (:name, :health, :attack, :defense)";
    $stmt = $db->prepare($sql);
    $res = $stmt->execute(['name' => $name, 'health' => $health, 'attack' => $attack, 'defense' => $defense]);
    if ($res) {
        return $db->lastInsertId();
    }
    return null;
}

function updateViking(string $id, string $name, int $health, int $attack, int $defense) {
    $db = getDatabaseConnection();
    $sql = "UPDATE viking SET name = :name, health = :health, attack = :attack, defense = :defense WHERE id = :id";
    $stmt = $db->prepare($sql);
    $res = $stmt->execute(['id' => $id, 'name' => $name, 'health' => $health, 'attack' => $attack, 'defense' => $defense]);
    if ($res) {
        return $stmt->rowCount();
    }
    return null;
}

function deleteViking(string $id) {
    $db = getDatabaseConnection();
    $sql = "DELETE FROM viking WHERE id = :id";
    $stmt = $db->prepare($sql);
    $res = $stmt->execute(['id' => $id]);
    if ($res) {
        return $stmt->rowCount();
    }
    return null;
}
function findVikingsByWeapon($weapon_id) {
    global $pdo;

    $sql = "
        SELECT v.id, v.name, v.health, v.attack, v.defense, 
               w.name AS weapon_name, w.id AS weapon_id
        FROM vikings v
        LEFT JOIN weapons w ON v.weapon_id = w.id
        WHERE w.id = :weapon_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':weapon_id', $weapon_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
