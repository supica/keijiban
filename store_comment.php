<?php

  // セッション時の処理
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header("HTTP/1.1 303 See Other");
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  //define('Charset', 'UTF-8');
  //データベース設定
  require_once('dbsettings.php');
  
  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  

  //セッションの作成・開始
  session_start();
    
  $_SESSION["comment"] = $_POST["comment"];

  if(isset($_POST["board-id"])){
  $_SESSION["board-id"] = $_POST["board-id"];
  }

  //選択したタイトルにコメントが投稿された場合の処理
  if(isset($_POST['submit']) && $_POST['submit'] =='コメント送信'){
    $comment = '';
    $user_name = '';
    $board = '';
    $reg_sts = '';
    $str = '';
    $str_mb = '';
    $comment_chars = '';
    
    
    $comment = trim($_SESSION['comment']);
    $user_name = $_COOKIE['user_name'];
    $board = $_SESSION['board-id'];
    
    //文字数チェック：30文字
    $str = mb_convert_kana($comment,"A","utf-8");
    $str_mb = mb_strlen($str,'UTF-8');
    $_SESSION['str_mb'] = $str_mb;
    $comment_chars = htmlspecialchars($comment,ENT_QUOTES);

    if($comment != ""){
      if($str_mb <= 30){
      $comment = mb_convert_kana($comment,"AKV","utf-8");
      $sql = "INSERT INTO comment(board_id,contents,user_name) VALUES('$board','$comment_chars','$user_name')";
      $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
      
        if($result == true){
        $reg_sts = 1;
        }
        else{
        $reg_sts = -1;
        }
      }else{
        $reg_sts = 3;
      }
    }else{
      $reg_sts = 2;
    }
    $_SESSION['reg_sts'] = $reg_sts;
    $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/comment.php';
    header("HTTP/1.1 301 Moved Permanently");
    header('Location: '.$url);
    exit;
  }
?>
