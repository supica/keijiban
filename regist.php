<?php
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  $url = "localhost";
  $user = "root";
  $pass = "";
  $db = "training01";

  // MySQLへ接続する
  $link = mysql_connect($url,$user,$pass) or die("MySQLへの接続に失敗しました。");

  // データベースを選択する
  $sdb = mysql_select_db($db,$link) or die("データベースの選択に失敗しました。");

  $user_name = '';
  
  if(isset($_POST['submit'])){
   $user_name = $_POST['user_name'];
    $password = sha1($_POST['password']);

     if($user_name != "" && $password != ""){
       if(mysql_num_rows($result) == 0){
         $sql = "INSERT INTO $db.users(id,user_name,password) VALUES(NULL,'$user_name','$password')";
         $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
         setcookie('user_name','$user_name');
         header('Location: regist02.php');
         exit;
       }
     }
  }

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板</title>
</head>

<body>
  <h1>ひとこと掲示板</h1>
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
