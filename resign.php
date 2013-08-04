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
  // データベースを選択する
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  

  $user_name = '';
  $error_message = '';
  $pw = '';
  $pw_2 = '';
  $password = '';
  
  login_check(); //ログイン認証

  // ログイン中の表示
  if(login_check() == true){
    $user_name = $_COOKIE['user_name'];      
    $login_message = '<img src="./images/icon_red.png" />
                      今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中<br />';
  }else {
    $error_message = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.
                     '<link href="style.css" rel="stylesheet" type="text/css">'.
                     '<div id="wrapper">'.
                     '<a href="index.php"><h1 id="hitokoto"><span>ひとこと掲示板</span></h1></a>'.
                     '<p class="disp_msg">※ログインして下さい<br /><br /></p>'.
                     '<p><a href="index.php">HOMEに戻る</a></p>'.
                     '</div>';
    echo $error_message;
  exit;

  }

  // フォームのデータが送信された場合  
  if(isset($_POST['submit']) && $_POST['submit'] == '退会する'){
    $password = sha1($_POST['password']);
    $pw = $_POST['password'];
    $pw_2 = $_POST['password_2'];

    //ユーザー名とパスワード、確認用パスワードが入力されていたら＆パスワードと確認用パスワードが同一だったら
    if($user_name != "" && $pw !="" && $pw_2 != "" && $pw == $pw_2){
      $sql = "SELECT * FROM users WHERE user_name = '$user_name' AND password = '$password'";
      $result = mysql_query($sql,$link);

        //ユーザー名とパスワードで問い合わせる
        if(mysql_num_rows($result) == 1){
          $sql = "DELETE FROM users WHERE user_name = '$user_name' AND password = '$password'";
          $result = mysql_query($sql, $link);
          header('Location: resign02.php');
          exit;
        }else{
           $error_message = '<p class="disp_msg">※入力内容が間違っています</p>';
        }
    }else{
      $error_message = '<p class="disp_msg">※パスワードを入力して下さい</p>';
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
  <h2>退会画面</h2>

<div class="disp_msg">
  <?php echo $login_message; ?>
  <?php echo $error_message; ?>
</div>
<div class="space10px"> </div>

<div class="log-lay">
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <dl>
    <dt class="log-lay-in">
    <label for="user_name" class="question">ユーザー名：</label>
    <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>" class="log-form" />
    </dt>
    <dt class="log-lay-in">
    <label for="password" class="question">パスワード：</label>
    <input type="password" id="password" name="password" value="" class="log-form"/>
    </dt>
    <dt class="log-lay-regi">
      <label for="password_2" class="question">パスワード(確認)：</label>
      <input type="password" id="password_2" name="password_2" value=""  class="log-form" />
    </dt>
    <img src="./images/icon_brown.png" />
    <input type="submit" value="退会する" name="submit" class="submit_b" />
  </form>
</div>
<p class="home"><img src="./images/icon_l-blue.png" /><a href="index.php">HOMEに戻る</a></p>
</div><!-- wrapper_END -->
<footer>
  <div class="footer">
  </div><!-- footer-bottom_END -->
</footer>
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
