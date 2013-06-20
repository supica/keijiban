<?php

  if(isset($_COOKIE['user_name'])){
    setcookie('user_name',"",time()-3600);
    //setcookie('user_name',$_COOKIE['user_name'],time()-3600);
  }

  $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/logout02.php';
  header('Location: '.$url);
  exit;

?>