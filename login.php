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
  $error_message = '';
  
  //フォームのデータが送信された場合
  if(isset($_COOKIE['user_name'])){
   $error_message =  '今は ' . '('.$_COOKIE['user_name'].')'.' さんがログインしています。';
  }

  //echo 'チェック！！';
    if(isset($_POST['submit'])){
      $user_name = $_POST['user_name'];
      $password = sha1($_POST['password']);      
      $pw = $_POST['password'];

      //ユーザー名とパスワードがどちらも入力されていたら
      if($user_name != "" && $pw !=""){       
        $sql = "SELECT * FROM users WHERE user_name = '$user_name' AND password = '$password'";
        $result = mysql_query($sql, $link);
          //該当する結果が1行だったら
          if(mysql_num_rows($result) == 1){
            $row = mysql_fetch_array($result);
            //$sql = "SELECT * FROM users WHERE user_name = '$user_name' AND password = '$password'";
            //$result = mysql_query($sql, $link);
            //クッキーの送信
            setcookie('user_name',$row['user_name']);
            //header('Location:index.php');
            $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
            header('Location: '.$url);
            exit;
          }
       else{
         $error_message = '<font color = "red">※ユーザーID、または、パスワードが間違っています。</font>';
         //echo '<font color = "red">ユーザーID、または、パスワードが間違っています。</font>';
       }
    }
    else{
      $error_message = '<font color = "red">※ユーザーIDとパスワードが入力されていません。</font>';
      //echo '<font color = "red">※入力内容が正しくありません。</font>';
    }
  }
  
  //パスワードが一致しているかどうか
  //どちらも一致していたらデータベースから取り出し
  //COOKIEで保持する
  //ログアウト


?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板</title>
</head>

<body>
  <h1><a href="index.php">ひとこと掲示板</a></h1>
  <h2>ログイン</h2>
  <!--<p><a href="">ログアウト</a></p>-->
<div>
  <p><?php echo $error_message; ?></p>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <label for="user_name">ユーザー名：</label><br />
    <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>"/><br />
    <label for="password">パスワード：</label><br />
    <input type="password" id="password" name="password" value=""/><br />
    <input type="submit" value="送信" name="submit" />
  </form>
</div>
<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
