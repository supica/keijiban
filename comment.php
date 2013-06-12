<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>コメント投稿</title>
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
?>


<?php
  // 選択したタイトルのコメントを表示／クエリ(検索条件)を送信する
  $sql = "SELECT * FROM comment";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  //データの取り出し(タイトルを表示)
  $board = '';
  $comment = '';

<?php
  //削除ボタンが押された場合に行う処理
  $delete_id = '';

  if(isset($_POST['delete_submit']) && $_POST['delete_submit'] == '削除'){
    $delete_id = $_POST['delete_id'];
 
    $sql = "DELETE FROM comment WHERE id = $delete_id";
    $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
  }
?>


  $board = $_POST['board-id'];    

  while ($row = mysql_fetch_assoc($result)) {

    if($board ==  $row['id']) {
      echo "タイトル：";
      echo "<tr><td>";
      echo $row['title'];
      echo $board;
      echo "</td><td>";
      echo '';  
      echo "</td></tr>";
    }

    //選択したタイトルにコメントが投稿された場合の処理
    if(isset($_POST['comment_submit'])=='送信'){

      $comment = $_POST['comment'];
      $sql = "SELECT * FROM comment WHERE contents = '$board','$comment';";
      $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
 
      while ($row = mysql_fetch_assoc($result)) {

        if($board ==  $row['id'] && $comment != "") {
          $sql = "INSERT INTO $db.comment(board_id,contents) VALUES('$board','$comment')";
          $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
          echo '投稿内容・・・';
          echo "<tr><td>";
          echo "</td><td>";
          echo '「';
          echo "<tr><td>";
          echo $comment;
          echo '」';
          echo "</td><td>";
          //echo '';  
          echo "</td></tr>";
        }
        elseif($comment == "") {
          echo "入力してください";
        }
      }
    }
  }

?>


  <div>
  <!-- コメント投稿フォーム -->
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <?php echo $row['title']; ?><br /><br />
      <label>コメント投稿：</label><br />
      <textarea id="comment" name="comment" cols="50" rows="6"></textarea><br />
      <input type="submit" value="送信" name="comment_submit" /><br /><br />
      <input type="hidden" value="<?php echo $board; ?>" name="board-id">
  </form>
  <!-- コメント投稿フォーム_END -->
  </div>

  <!-- コメント一覧 -->
  <table border="1" width="400" cellspacing="0">
  <tr>
    <th width="400">コメント</th>
    <th width="100">削除</th>
  </tr>

<?php
  //if(isset($_POST['comment_submit'])=='送信'){
  $sql = "SELECT * FROM comment";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  //データの取り出し()
  while ($row = mysql_fetch_assoc($result)) {
 
    if($board == $row['board_id']){
      echo "<tr><td>";
      echo " <br />".$row['board_id']."|".$row['contents'];
      echo "</td>";
      echo "<td>";
      echo '<form>'.
           '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
           '<input type="submit" value="削除" name="delete_submit" />'.
           '</form>';
      echo "</tr></td>\n";
    }
  }
?>

</table>
<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
