<?php
ini_set('display_errors', 1);
require('../../database.php');

$solv_id = $_POST["solv_id"];
$name = $_POST["name"];
$club = $_POST["club"];
$cupId = $_POST["cupId"];
$date = $_POST["date"];
$calculation = $_POST["calculation"];

try {
    $pdo->beginTransaction();
    $sql = $pdo->query("INSERT INTO `races` (`solv_id`, `name`, `club`, `date`, `cupId`, `calculation`) VALUES ('$solv_id', '$name', '$club', '$date', $cupId, '$calculation')");

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php?id={$cupId}");