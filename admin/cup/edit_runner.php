<?php
  require('../../database.php');

  //define index of column
  $columns = array(
    0 =>'runnerName', 
    1 => 'runnerClub',
    2 => 'runnerYear',
    3 => 'runnerCanton',
    4 => 'runnerPoints',
  );

  $value = $_POST['val'];
  $runnerId = $_POST['id'];
  $colIndex = $_POST['index'];

  $raceId = $_POST['raceId'];
  $cupId = $_POST['cupId'];

  $msg = "OK";

  if($colIndex == 4) {
    $sql = $pdo->query("UPDATE `results` SET `points` = $value WHERE `runnerId` = $runnerId AND `raceId` = $raceId AND cupId = $cupId");
  }

  // send data as json format
  echo json_encode($msg);
?>