<?php
require('../../database.php');
ini_set('display_errors', 1);

$cupId = $_POST["cup_id"];

$solv_id = $_POST["solv_id"];



try {
    $pdo->beginTransaction();
    $sql = $pdo->query("DELETE FROM `races` WHERE solv_id = $solv_id and cupId = $cupId");
    $sql = $pdo->query("DELETE FROM `results` WHERE raceId = $solv_id");
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php?id=$cupId");
?>
