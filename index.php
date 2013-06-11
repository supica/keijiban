<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>トレーニング01</title>
</head>

<body>

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


  //結果セットの行数を取得する
 // $rows = mysql_num_rows($result);

  //フォームのデータが送信された場合に行う処理
  if(isset($_POST['submit']) && $_POST['submit']=='送信'){
    
    $title = $_POST['title'];
    
	  if($title != "") {
		$query = mysql_query("INSERT INTO training01.board(title) VALUES('$title')", $link);
	  } elseif($title == "") {
	  	echo "入力してください";
   }
  }
?>

<?php
  //削除ボタンの実行

  $delete_id = '';

  if(isset($_POST['submit']) && $_POST['delete_submit'] == '削除'){
  $delete_id = $_POST['delete_id'];

  $sql = "DELETE FROM board WHERE id = $delete_id";
  $result = mysql_query($db,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
  }
  
  //mysql_close($db);
?>

<!-- タイトル：登録フォーム -->
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
  <label for="title">タイトル：</label><br />
  <textarea id="title" name="title" cols="50"></textarea><br />
  <input type="submit" value="送信" name="submit" />
  </form>
<!-- タイトル：登録フォーム_END -->

<!-- タイトル一覧 -->
  <table border="1" width="400" cellspacing="0" cellpadding="5">
  <tr>
    <th width="400">タイトル</th>
    <th width="100">削除</th>
  </tr>

<!-- タイトルを選ぶ -->
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
	
  <input type="submit" value="選択" name="select_submit" />
  </select>
  </form>
  

  <?php
  // クエリ(検索条件)を送信する
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  //データの取り出し
  $board = '';
  $post_id = '';

  while ($row = mysql_fetch_assoc($result)) {

  if(isset($_POST['select_submit'])=='選択'){

    $board = $_POST['board-id'];

    if($board ==  $row['id']) {
      echo "<tr><td>";
      echo $row['title'];
      echo "</td><td>";
      echo '';
      echo '<form>'.
           '<input type="hidden" value="'.$post_id.'" name="delete_id" />'.
           '<input type="submit" value="削除" name="delete_submit" />'.
           '</form>';
      echo "</td></tr>";
    }
    
  }
   
   else {
  echo "<tr><td>";
  echo $row['title'];
  echo "</td><td>";
  echo '';
  
  //$delete_id = $POST['delete_id'];
  echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
       '<input type="hidden" value="'.$post_id.'" name="delete_id" />'.
       '<input type="submit" value="削除" name="delete_submit" />'.
       '</form>';
  echo "</td></tr>";
  }
}
 
  ?>
 
  </table>
<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>">HOMEに戻る</a></p>
</body>
</html>
