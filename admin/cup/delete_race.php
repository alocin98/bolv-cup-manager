<?php
require('../../database.php');
ini_set('display_errors', 1);

$cupId = $_POST["cup_id"];
$raceId = $_POST["race_id"];



try {
    $pdo->beginTransaction();
    $sql = $pdo->query("DELETE FROM `races` WHERE id = $raceId and cupId = $cupId");
    $sql = $pdo->query("DELETE FROM `results` WHERE raceId = $raceId");
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php?id=$cupId");
?>
