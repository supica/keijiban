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

            //クッキーの送信
            setcookie('user_name',$row['user_name']);
            //header('Location:index.php');
            $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
            header('Location: '.$url);
            exit;
          }
       else{
         $error_message = '<font color = "red">※ユーザーID、または、パスワードが間違っています。</font>';
       }
    }
    else{
      $error_message = '<font color = "red">※ユーザーIDとパスワードが入力されていません。</font>';
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
  <h1><a href="index.php">ひとこと掲示板</a></h1>
  <h2>ログイン</h2>
  <!--<p><a href="">ログアウト</a></p>-->
<div class="log-lay">
  <p><?php echo $error_message; ?></p>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <dl>
    <dt>
    <label for="user_name" class="question">ユーザー名：</label>
    <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>" class="log-form" />
    </dt>
    <dt>
    <label for="password" class="question">パスワード：</label>
    <input type="password" id="password" name="password" value="" class="log-form"/>
    </dt>
    <input type="submit" value="送信" name="submit" class="submit" />
    </dl>
  </form>
</div>
<p><a href="index.php">HOMEに戻る</a></p>
</div><!-- wrapper_END -->
</body>
</html>
