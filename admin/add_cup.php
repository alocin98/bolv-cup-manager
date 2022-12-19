<?php
require('../database.php');
ini_set('display_errors', 1);

$name = $_POST["name"];
$season= $_POST["season"];
$categories = $_POST["categories"];

try {
    $pdo->beginTransaction();
    $sql = $pdo->query("INSERT INTO `cups` (`name`, `season`) VALUES ('$name', '$season')");
    $id = $pdo->lastInsertId();
    foreach($categories as $category) {
        $sql = $pdo->query("INSERT INTO `cups_categories` (`cup_id`, `name`) VALUES ('$id', '$category')");
    }
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php");
?>
