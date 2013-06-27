<?php

  //セッション時の処理
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header("HTTP/1.1 303 See Other");
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  define('Charset', 'UTF-8');
  //データベース設定
  require_once('dbsettings.php');
  
  // MySQLへ接続する
  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");
  
  // データベースを選択する
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  


  //選択したタイトルにコメントが投稿された場合の処理
  if(isset($_POST['submit']) && $_POST['submit'] =='コメント送信'){
  //define('Charset', 'UTF-8');
    $comment = '';
    $user_name = '';
    $board = '';
    
    $comment = $_POST['comment'];
    $user_name = $_COOKIE['user_name'];
    $board = $_POST['board-id'];

    if($comment != ""){
      $sql = "INSERT INTO comment(board_id,contents,user_name) VALUES('$board','$comment','$user_name')";
      $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
    }
      $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/comment.php'.'?board-id='.$board;
      header("HTTP/1.1 301 Moved Permanently");
      header('Location: '.$url);
      exit;
  }
?>
