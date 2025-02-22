<?php
  if(!isset($_SESSION['level'])){
    header("location: login.php");
  }


  $level = $_SESSION['level'];

  switch($level){
    case "wks_1" : 
        include('wks/wks_1.php');
        break;
    case "wks_2" :
        include('wks/wks_2.php');
        break;
    case "wks_3" :
        include('wks/wks_3.php');
        break;
    case "wks_4" :
      include('wks/wks_4.php');
      break;
  }
?>