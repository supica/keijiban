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
  if(isset($_COOKIE['user_name']))
  echo '<a href="logout.php">ログアウト</a>';
  ?>
  <br /><br />
    
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
  
  //クッキーがセットされたフォームのデータが送信された場合
  $user_name = '';
  $login_message = '';
  
  //ログインの判定：コメント表示
  if(isset($_COOKIE['user_name'])){
    $user_name = $_COOKIE['user_name'];
    $login_message =  '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中<br /><br />';
    echo $login_message;
   }

  
  //タイトルの追加：フォームのデータが送信された場合に行う処理
  if(isset($_POST['submit']) && $_POST['submit']=='送信'){   
    if(isset($_COOKIE['user_name'])){

    $title = $_POST['title'];
    
    if($title != "") {
      $sql = "INSERT INTO $db.board(title,user_name) VALUES('$title','$user_name')";
      $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
	}
	elseif($title == "") {
      echo '<font color = "red">※登録するタイトルを入力してください　　　</font>' . '<a href="index.php">HOMEに戻る</a><br /><br />';
    }

    }
    else {
    echo '<font color="red">※ログインしてください</font>';
    }
    
  }

  //削除ボタンが押された場合に行う処理
  $delete_id = '';

  if(isset($_POST['delete_submit']) && $_POST['delete_submit'] == '削除'){
    $delete_id = $_POST['delete_id'];
 
    $sql = "DELETE FROM board WHERE id = $delete_id";
    $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
  }
?>
  
  <!-- タイトル一覧 -->
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <label for="title">タイトル：</label><br />
    <textarea id="title" name="title" cols="50"></textarea><br />
    <input type="submit" value="送信" name="submit" /><br /><br />
  </form>

  <table border="1" width="400" cellspacing="0" cellpadding="5">
  <tr>
    <th width="400">タイトル一覧</th>
    <th width="100">削除</th>
  </tr>

  <!-- 一覧からタイトルを選ぶ  -->
<?php
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
?>
  
<form method="post" action="comment.php" >タイトルを選ぶ：
  <select name="board-id">
    <?php
      //データの取り出し：セレクトボード
      while ($row = mysql_fetch_assoc($result)) {
        echo "<option value=\"";
        echo $row['id'];
        echo "\">";
        echo $row['title'];
        echo "</option>";
      }
    ?>	
  <input type="submit" value="選択" name="select_submit" /><br /><br />
  </select>
</form>

<?php
  // タイトルの表示・＜クエリ(検索条件)を送信する＞
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  $board = '';

  while ($row = mysql_fetch_assoc($result)) {

    if(isset($_POST['select_submit'])=='選択'){

      $board = $_POST['board-id'];


      if($board ==  $row['id']) {
        echo "<tr><td>";
        echo $row['title'];
        echo "</td><td>";
        echo '';
        echo "</td></tr>";
      }   
    }
    else {
     echo "<tr><td>";
     echo $row['title'];
     echo "</td><td>";
     echo '';
     echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
          '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
          '</form>';
     if($user_name == $row['user_name']){
       echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
            '<input type="submit" value="削除" name="delete_submit" />'.
            '</form>';  
     echo "</td></tr>\n";
     }
     else {
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
