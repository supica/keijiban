<?php
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  require_once('dbsettings.php');

  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  

  $user_name = '';
  $error_message = '';

  // 登録：ユーザー名とパスワードが送信された時
  if(isset($_POST['submit'])){
   $user_name = $_POST['user_name'];
   $password = sha1($_POST['password']);
   $pw = $_POST['password'];
   $pw_2 = $_POST['password_2'];
     
     //ユーザー名、パスワード、確認用パスワードが入力されていたら＆パスワードと確認用パスワードが同一だったら
     if($user_name != "" && $pw != "" && $pw_2 != "" && $pw == $pw_2){
     $sql = "SELECT * FROM users WHERE user_name = '$user_name'";
     $result = mysql_query($sql,$link);
     
       //入力チェック：4-8文字の英数字
       if(preg_match("/^[a-zA-Z0-9]{4,8}$/",$user_name)){
         //ユーザー名とパスワードで問い合わせる
         if(mysql_num_rows($result) == 0){
           $sql = "INSERT INTO training01.users(id,user_name,password) VALUES(NULL,'$user_name','$password')";
           //$sql_c = "INSERT INTO training01.comment(user_name) VALUES('$user_name')";         
           $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
           setcookie('user_name','$user_name');
           header('Location: regist02.php');
         exit;
         }else{
           $error_message = '<p class="disp_msg">※このユーザー名は使用されています。<br />　他のユーザー名を指定してください。</p>';
         }
       }else{
         $error_message = '<p class="disp_msg">※ユーザー名は、4～8文字の英数字で登録してください。</p>';
       }
     }else{
     $error_message = '<p class="disp_msg">※入力内容が正しくありません</p>';
     }
   mysql_close($link);
  }
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href="style.css" rel="stylesheet" type="text/css">
  <title>ひとこと掲示板</title>
</head>

<body>
<div id="wrapper">
  <a href="index.php"><h1 id="hitokoto"><span>ひとこと掲示板</span></h1></a>
  
  <h2>ユーザー登録</h2>
<div class= "disp_msg"><?php echo $error_message; ?></div>
<div class="space10px"> </div>

<div class="log-lay">
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
  <dl>
  <dt class="log-lay-regi">
    <label for="user_name" class="question">ユーザー名：</label>
    <input type="text" id="user_name" name="user_name" value="" class="log-form" />
  </dt>
  <dt class="log-lay-regi">
    <label for="password" class="question">パスワード：</label>
    <input type="password" id="password" name="password" value="" class="log-form" />
  </dt>
  <dt class="log-lay-regi">
    <label for="password_2" class="question">パスワード(確認)：</label>
    <input type="password" id="password_2" name="password_2" value=""  class="log-form" />
  </dt>
    <img src="./images/icon_green.png" />
    <input type="submit" value="ユーザー登録する" name="submit" class="submit_b" />
  </dl>
</form>
</div><!-- post_comment_form_END -->
<p class="home"><img src="./images/icon_l-blue.png" /><a href="index.php">HOMEに戻る</a></p>
</div><!-- wrapper_END -->
<footer>
  <div class="footer">
  </div><!-- footer-bottom_END -->
</footer>
</body>
</html>
