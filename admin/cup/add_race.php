<?php
require('../../database.php');
ini_set('display_errors', 1);

$cupId = $_POST["cup_id"];

$solv_id = $_POST["solv_id"];
$name = $_POST["name"];
$club = $_POST["club"];
$date = $_POST["date"];
$season = $_POST["season"];
$calculation = $_POST["calculation"];


try {
    $pdo->beginTransaction();
    $sql = $pdo->query("INSERT INTO `races` (`solv_id`, `name`, `club`, `date`, cupId, `calculation`) VALUES ('$solv_id', '$name', '$club', '$date', $cupId, '$calculation')");

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php?id=$cupId");
?>
