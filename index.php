<?php
  // セッション時の処理 
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    //header("HTTP/1.1 303 See Other");
    header("HTTP/1.1 301 Moved Premanently");
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }
?>

<?php
  // データベース設定
  require_once('dbsettings.php');

  // MySQLへ接続する
  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");  
  // データベースを選択する
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  
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
  //ログイン：判定
  function login_check(){
    if(isset($_COOKIE['user_name'])){
      return true;
    }else{
      return false;
    }
  }
  login_check();
?>

<?php
  $user_name = '';
  $login_message = '';

  // ログイン時：「～さんでログイン中」表示
  if(login_check($_COOKIE,'user_name') == true){
    $user_name = $_COOKIE['user_name'];      
    echo '<a href="logout.php">ログアウト</a><br /><br />';
    $login_message =  '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中';
    echo $login_message;
  }else {
    $login_message =  '<font color = "red">※ログインして下さい</font>';
    echo $login_message;
  }

  // ログイン時：タイトル投稿欄 表示
  function login_display(){
    if(login_check() == true){
      echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
      '<label for="title">タイトルを作る：　＜20文字以内＞</label><br />'.
      '<textarea id="title" name="title" cols="60"></textarea><br />'.
      '<input type="submit" value="送信" name="submit" /><br /><br />'.
      '</form>';
    }
  }
?>

<br /><br />
  
<?php  
  // タイトル送信時：タイトルの追加
  if(isset($_POST['submit']) && $_POST['submit']=='送信'){   
    if(login_check() == true){
    $title = $_POST['title'];
    //$title = strlen($_POST['title']);
    
    $str = $title;
    $str_mb = mb_strlen($str,'UTF-8');
    //$title_error = '';
    
      if($title != ""){
        if($str_mb <= 20){
        $sql = "INSERT INTO training01.board(title,user_name) VALUES('$title','$user_name')";
        $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
        header('Location:'.$_SERVER['PHP_SELF']);
      exit();
      }else{
        //$title_error = mb_strimwidth($str,0,40,'','utf-8');
	    echo '<font color = "red">※20文字以内で入力してください　　　</font>' . '<a href="index.php">HOMEに戻る</a><br /><br />';
      }
      }
	  elseif($title == "") {
	    $login_message = '<font color = "red">※登録するタイトルを入力してください　　　</font>' . '<a href="index.php">HOMEに戻る</a><br /><br />';
        echo $login_message;
      }
    }
    else {
	  $login_message = '<font color = "red">※ログインしてください　　　</font>' . '<a href="index.php">HOMEに戻る</a><br /><br />';
      echo $login_message;
    }
    
  }

  //削除ボタン：押された時
  $delete_id = '';
  
  if(login_check() == true){
    if(isset($_POST['delete_submit']) && $_POST['delete_submit'] == '削除'){
    $delete_id = $_POST['delete_id'];
    $sql = "DELETE FROM board WHERE id = $delete_id";
    $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
    }
  }
?>

  <!-- タイトル一覧 -->
  <?php
  login_display();
  ?>

  <table border="1" width="425" cellspacing="0" cellpadding="0">
  <tr>
    <th width="800">タイトル一覧</th>
    <th width="100">削除</th>
  </tr>

  <!-- 一覧からタイトルを選ぶ  -->
<?php
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  //タイトル:選択
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
?>
  

<?php
  //session_start();

  // タイトルの表示・＜クエリ(検索条件)を送信する＞
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  $board = '';

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
        echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
             '</form>';
       if($user_name == $row['user_name']){
         echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
              '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
              '<input type="submit" value="削除" name="delete_submit" />'.
              '</form>';  
         echo "</td></tr>\n";
       }else {
         echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
              '---'.
              '</form>';  
       }
   }
  }
?>

 
  </table>
<!-- <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>">HOMEに戻る</a></p> -->
</body>
</html>
