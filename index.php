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
  $comment = '';

  // MySQLへ接続する
  $link = mysql_connect($url,$user,$pass) or die("MySQLへの接続に失敗しました。");

  // データベースを選択する
  $sdb = mysql_select_db($db,$link) or die("データベースの選択に失敗しました。");


  //結果セットの行数を取得する
 // $rows = mysql_num_rows($result);

  //フォームのデータが送信された場合に行う処理
  if(isset($_POST['submit']) && $_POST['submit']=='送信'){
    
    $id = $_POST['id'];
    $title = $_POST['title'];
	$comment = $_POST['comment'];
    
	  if($id != "" && $title != "") {
		$query = mysql_query("INSERT INTO training01.board(id,title) VALUES('$id', '$title')", $link);
	  } elseif($id == "" || $title == "") {
	  	echo "入力してください";
   }
  }
?>

<!-- コメント投稿フォーム -->
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
  <label for="id">id：</label><br />
  <input type="text" id="id" name="id" /><br />
  <label for="title">タイトル：</label><br />
  <textarea id="title" name="title" cols="50"></textarea><br />
  <label>コメント：</label><br />
  <textarea id="comment" name="comment" cols="50" rows="6"></textarea>
  <input type="submit" value="送信" name="submit" />
  </form>
<!-- コメント投稿フォーム_END -->

  <table border="1" width="400" cellspacing="0" cellpadding="5">
  <tr>
    <th width="100">id</th>
    <th width="400">タイトル</th>
    <th width="100">削除</th>
  </tr>

  <?php
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  ?>
  
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >タイトル：
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
  
  while ($row = mysql_fetch_assoc($result)) {

  if(isset($_POST['select_submit'])=='選択'){

    $board = $_POST['board-id'];	

    if($board ==  $row['id']) {
      echo "<tr><td>";
      echo $row['id'];
      echo "</td><td>";
      echo $row['title'];  
      echo "</td><td>";
      echo '';  
      echo "</td></tr>";
    } 
   }
  else {
  echo "<tr><td>";
  echo $row['id'];
  echo "</td><td>";
  echo $row['title'];  
  echo "</td><td>";
  echo '';  
  echo "</td></tr>";	    
  }
}
  
  ?>

  </table>

</body>
</html>

