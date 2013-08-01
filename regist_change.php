<?php

  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  //データベース設定
  require_once('dbsettings.php');

  // MySQLへ接続する
  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");
  //mysql_query($link,"SET NAMES utf8");
  
  // データベースを選択する
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  

  $url = '';
  $pw = '';
  $pw_n = '';
  $error_message = '';
  $password = '';
  $new_password = '';
  $new_pass = '';
  $new_pass_2 = '';
  
  //フォームのデータが送信された場合
  if(isset($_COOKIE['user_name'])){
   $error_message = '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中<br /><br />';
  }
    //変更ボタンが押された場合に行う処理
    if(isset($_POST['submit'])){
      $user_name = $_COOKIE['user_name'];
      $password = sha1($_POST['password']);
      $new_password = sha1($_POST['new_pass']);
      $pw = $_POST['password'];
      $pw_n = $_POST['new_pass'];
      $pw_n2 = $_POST['new_pass_2'];

      //パスワードが入力されたら
      if($pw !="" && $pw_n !="" && $pw_n2 !="" && $pw_n == $pw_n2){
        $sql = "SELECT * FROM users WHERE user_name = '$user_name' AND password = '$password'";
        $result = mysql_query($sql, $link);

          //該当する結果が1行だったら
          if(mysql_num_rows($result) == 1){
            $row = mysql_fetch_array($result);

                $sql = "UPDATE training01.users SET password = '$new_password' WHERE user_name = $user_name";
                $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
                header('Location:  regist_change02.php');
          }
          else{
            $error_message = '<p class="disp_msg">※パスワードを確認してください。</p>';
          }
      }else{
        $error_message = '<p class="disp_msg">※パスワードを入力してください。</p>';
      }
  }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="style.css" rel="stylesheet" type="text/css">
  <title>ひとこと掲示板</title>
</head>

<body>
<div id="wrapper">
  <a href="index.php"><h1 id="hitokoto"><span>ひとこと掲示板</span></h1></a>

  <?php echo $error_message; ?>
  <!-- パスワード変更フォーム -->
<div class="log-lay">
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
  <dl>
  <dt>
    <label for="password" class="question">現在のパスワード：</label>
    <input type="password" id="password" name="password" value="" class="log-form" />
  </dt>
  <dt>
    <label for="new_pass" class="question">新パスワード：</label>
    <input type="password" id="new_pass" name="new_pass" value="" class="log-form" />
  </dt>
  <dt>
    <label for="new_pass_2" class="question">新パスワード(確認)：</label>
    <input type="password" id="new_pass_2" name="new_pass_2" value="" class="log-form" />
  </dt>
    <img src="./images/icon_org.png" />
    <input type="submit" value="パスワードを変更する" name="submit" class="submit"/>
  </dl>
</form>
</div><!-- log-lay_END -->

<p><a href="index.php">HOMEに戻る</a></p>
</div><!-- wrapper_END -->
</body>
</html>

<?php
  //ログイン：判定
  function login_check(){
    if(isset($_COOKIE['user_name'])){
      return true;
    }else{
      return false;
    }
  }
?>