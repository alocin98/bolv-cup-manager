<?php
require('../../database.php');
ini_set('display_errors', 1);

$cupId = $_POST["cup_id"];
$name = $_POST["runner_name"];
$club = $_POST["runner_club"];
$year = $_POST["runner_year"];
$canton = $_POST["runner_canton"];
$category = $_POST["runner_category"];
$points = $_POST["runner_points"];
$raceId = $_POST["race_id"];


try {
    $pdo->beginTransaction();
    $sql = $pdo->query("INSERT INTO `runners` (`name`, `club`, `year`, `canton`, `category`, `cupId`) VALUES ('$name', '$club', '$year', '$canton', '$category', '$cupId')");
    // add result
    $runnerId = $pdo->lastInsertId();
    $sql = $pdo->query("INSERT INTO `results` (`runnerId`, `raceId`, `points`, `cupId`) VALUES ($runnerId, $raceId, $points, $cupId)");
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}


header("Location: index.php?id=$cupId");
?>
