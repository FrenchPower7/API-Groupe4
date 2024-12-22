
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils/database.php';

function findWeaponById(string $id) {
    $db = getDatabaseConnection();
    $sql = "SELECT id, type, damage FROM weapon WHERE id = :id"; 
    $stmt = $db->prepare($sql);
    $res = $stmt->execute(['id' => $id]);
    if ($res) {
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
    return null;
}


function findAllWeapons($type, $limit, $offset) {
    $db = getDatabaseConnection();
    
    $sql = "SELECT * FROM weapon";
    if (!empty($type)) {
        $sql .= " WHERE type = :type";
    }
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    
    if (!empty($type)) {
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    }
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function createWeapon(string $type, int $damage) {
    $db = getDatabaseConnection();
    
    $sql = "INSERT INTO weapon (type, damage) VALUES (:type, :damage)";
    $stmt = $db->prepare($sql);
    
    try {
        $res = $stmt->execute([
            'type' => $type,
            'damage' => $damage
        ]);
        
        if ($res) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return null;
    }
}




function findOneWeapon(int $id) {
    $db = getDatabaseConnection();
    $sql = "SELECT * FROM weapon WHERE id = :id"; 
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    return null; 
}
function deleteWeapon($weaponId) {
    $db = getDatabaseConnection();
    $sql = "DELETE FROM weapon WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $weaponId, PDO::PARAM_INT);
    
    return $stmt->execute(); 
}
