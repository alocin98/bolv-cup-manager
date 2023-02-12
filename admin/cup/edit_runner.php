<?php
  //include connection file 
  include_once("connection.php");
  
  //define index of column
  $columns = array(
    0 =>'runnerName', 
    1 => 'runnerClub',
    2 => 'runnerYear',
    3 => 'runnerCanton',
    4 => 'runnerPoints',
  );
  $error = true;
  $colVal = '';
  $colIndex = $rowId = 0;
  
  $msg = array('status' => !$error, 'msg' => 'Failed! updation in mysql');
  if(isset($_POST)){
    if(isset($_POST['val']) && !empty($_POST['val']) && $error) {
      $colVal = $_POST['val'];
      $error = false;
      
    } else {
      $error = true;
    }
    if(isset($_POST['index']) && $_POST['index'] >= 0 &&  $error) {
      $colIndex = $_POST['index'];
      $error = false;
    } else {
      $error = true;
    }
    if(isset($_POST['id']) && $_POST['id'] > 0 && $error) {
      $rowId = $_POST['id'];
      $error = false;
    } else {
      $error = true;
    }
  
    if(!$error) {
        //update data in mysql database
        $sql = "UPDATE results SET ".$columns[$colIndex]." = '".$colVal."' WHERE runnerId='".$rowId."'";
        $status = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
        $msg = array('status' => !$error, 'msg' => 'Success! updation in mysql');
    }
  }
  
  // send data as json format
  echo json_encode($msg);
?>