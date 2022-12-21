<?php
require('../database.php');
ini_set('display_errors', 1);

$name = $_POST["name"];
$season= $_POST["season"];
$categories = $_POST["categories"];
$clubs = $_POST["clubs"];

try {
    $pdo->beginTransaction();
    $sql = $pdo->query("INSERT INTO `cups` (`name`, `season`) VALUES ('$name', '$season')");
    $id = $pdo->lastInsertId();
    foreach($categories as $category) {
        $sql = $pdo->query("INSERT INTO `cups_categories` (`cup_id`, `name`) VALUES ('$id', '$category')");
    }
    foreach($clubs as $club) {
        $sql = $pdo->query("INSERT INTO `cups_clubs` (`cupId`, `clubId`) VALUES ('$id', '$club')");
    }
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php");
?>
