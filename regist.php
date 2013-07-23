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
     
     //ユーザー名とパスワードがどちらも入力されていたら
     if($user_name != "" && $pw != ""){
     $sql = "SELECT * FROM users WHERE user_name = '$user_name' AND password = '$password'";
     $result = mysql_query($sql,$link);
     
       //入力チェック：4-8文字の英数字
       if(preg_match("/^[a-zA-Z0-9]{4,8}$/",$user_name)){
         //ユーザー名とパスワードで問い合わせる
         if(mysql_num_rows($result) == 0){
           $sql = "INSERT INTO training01.users(id,user_name,password) VALUES(NULL,'$user_name','$password')";
           $sql_c = "INSERT INTO training01.comment(user_name) VALUES('$user_name')";         
           $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
           setcookie('user_name','$user_name');
           header('Location: regist02.php');
         exit;
         }else{
           $error_message = '<font color = "red">※このユーザー名は使用されています。他のユーザー名を指定してください。</font>'.'<br /><br /><a href="">戻る</a>';
         }
       }else{
         $error_message ='<font color = "red">※ユーザー名は、4～8文字の英数字で登録してください。</font>';
       }
     }else{
     $error_message = '<font color = "red">※入力内容が正しくありません。</font>';
     }
   mysql_close($link);
  }
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板</title>
</head>

<body>
  <h1><a href="index.php">ひとこと掲示板</a></h1>
  
<?php echo $error_message; ?>

  <h2>ユーザー登録</h2>
<div>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <label for="user_name">ユーザー名：</label><br />
    <input type="text" id="user_name" name="user_name" value=""/><br />
    <label for="password">パスワード：</label><br />
    <input type="password" id="password" name="password" value=""/><br />
    <input type="submit" value="送信" name="submit" />
  </form>
</div><!-- post_comment_form_END -->
<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
