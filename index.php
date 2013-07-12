<?php
  // セッション時の処理 
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header("HTTP/1.1 301 Moved Premanently");
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  // データベース設定
  require_once('dbsettings.php');
  // MySQLへ接続する
  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");  
  // データベースを選択する
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  

  $user_name = '';
  $login_message = '';
  $str_mb = '';
  $title_20 = '';
  $delete_id = '';
  $board = '';

  login_check(); //ログイン認証
  
  // ログイン中の表示
  if(login_check() == true){
    $user_name = $_COOKIE['user_name'];      
    $login_message =  '<a href="logout.php">ログアウト</a><br /><br />'.
                      '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中<br /><br />';
  }else {
    $login_message =  '<font color = "red">※ログインして下さい<br /><br /></font>';
  }

  // 「タイトルを作る」から送信ボタンが押された時
  if(isset($_POST['submit']) && $_POST['submit']=='送信'){

      $title = $_POST['title'];
      $title_chars = htmlspecialchars($title,ENT_QUOTES); //htmlタグを無効化
      $str_mb = mb_strlen($title,'UTF-8'); //文字数をカウント

      if($title != ""){
          if($str_mb <= 20){
            $sql = "INSERT INTO training01.board(title,user_name) VALUES('$title_chars','$user_name')";
            $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
          header('Location:'.$_SERVER['PHP_SELF']);
          exit();
          }else{
            $title_20 = mb_substr($title,0,20,'utf-8');  //20文字で丸める
   	        $login_message = '<font color = "red">※20文字以内で入力してください　　　</font>'.
   	                         '<a href="index.php"><br /><br />HOMEに戻る</a><br /><br />';
          }
      }elseif($title == ""){
	    $login_message = '<font color = "red">※登録するタイトルを入力してください　　　</font>'.
	                     '<a href="index.php"><br /><br />HOMEに戻る</a><br /><br />';
  }else {
	$login_message = '<font color = "red">※ログインしてください　　　</font>'.
	                 '<a href="index.php"><br /><br />HOMEに戻る</a><br /><br />';
    }
  }

  //「タイトル一覧」から削除ボタンが押された時
    if(isset($_POST['delete_submit']) && $_POST['delete_submit'] == '削除'){
      $delete_id = $_POST['delete_id'];
      $sql = "DELETE FROM board WHERE id = $delete_id";
      $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
    }
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板</title>
</head>

<body>
  <h1>ひとこと掲示板</h1>
  <a href="login.php">ログイン</a>　　　
  <a href="regist.php">ユーザー登録</a>　　　

  <?php
    echo $login_message;
   ?>

  <table border="1" width="425" cellspacing="0" cellpadding="0">
  <tr>
    <th width="800">タイトル一覧</th>
    <th width="100">削除</th>
  </tr>

<?php
  //「タイトルを作る」の文字数判定
  if(login_check() == true){
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
    echo '<label for="title">タイトルを作る：　＜20文字以内＞</label><br />';
    echo '<textarea id="title" name="title" cols="60" >';
      if($str_mb > 20){
        echo $title_20;
      }
    echo '</textarea><br />';
    echo '<input type="submit" value="送信" name="submit" /><br /><br />';
    echo '</form>';
  }

  //「タイトルを選ぶ」から選択ボタンが押された時
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  if(login_check() == true){
    echo '<form method="get" action="comment.php" >タイトルを選ぶ：';
    echo '<select name="board-id">';    
  
    while ($row = mysql_fetch_assoc($result)) {
      echo "<option value=\"";
      echo $row['id'];
      echo "\">";
      echo $row['title'];
      echo "</option>";
    }
    echo '<input type="submit" value="選択" name="select_submit" /><br /><br />';
    echo '</select>';
    echo '</form>';
  }

  // 「タイトル一覧」の表示
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  while ($row = mysql_fetch_assoc($result)) {
    if(isset($_POST['select_submit'])=='選択'){
      $board = $_GET['board-id'];
      if($board ==  $row['id']) {
        echo "<tr><td>";
        echo $row['title'];
        echo "</td><td>";
        echo '';
        echo "</td></tr>";
      }   
    }else{
        echo "<tr><td>";
        echo $row['title'];
        echo "</td><td>";
        echo '';
        echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
        echo '</form>';
       if($user_name == $row['user_name']){
         echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
         echo '<input type="hidden" value="'.$row['id'].'" name="delete_id" />';
         echo '<input type="submit" value="削除" name="delete_submit" />';
         echo '</form>';  
         echo "</td></tr>\n";
       }else {
         echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
         echo '---';
         echo '</form>';
       }
    }
  }
?>
  </table>
<!-- <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>">HOMEに戻る</a></p> -->
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

