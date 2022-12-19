<?php
require('../../database.php');
ini_set('display_errors', 1);

$cupId = $_POST["cup_id"];

// delete cup
$sql = $pdo->query("DELETE FROM `cups` WHERE id = $cupId");

header("Location: index.php?id=$cupId");
?>
