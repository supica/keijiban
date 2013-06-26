<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板:コメント投稿</title>
</head>

<body>
  <h1><a href="index.php">ひとこと掲示板</a></h1>

<?php
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header("HTTP/1.1 301 Moved Premanently");
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }

  //データベース設定
  require_once('dbsettings.php');

  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  
  
  
  //クッキーがセットされたフォームのデータが送信された場合にしようする変数の初期化
  $user_name = '';
  $login_message = '';
  
  //フォームのデータが送信された場合
  if(isset($_COOKIE['user_name'])){
    $user_name = $_COOKIE['user_name'];
    $login_message =  '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中　　' . '<a href="logout.php">ログアウト</a><br /><br />';
    echo $login_message;
    //echo ;
   }
   

  //削除ボタンが押された場合に行う処理
  $delete_id = '';

  if(isset($_POST['delete_submit']) && $_POST['delete_submit'] == '削除'){
    $delete_id = $_POST['delete_id'];
 
    $sql = "DELETE FROM comment WHERE id = $delete_id";
    $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
  }

?>

<?php
  // 選択したタイトルのコメントを表示／クエリ(検索条件)を送信する
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  //データの取り出し(タイトルを表示)

  $board = '';
  $comment = '';

  $board = $_POST['board-id'];

  while ($row = mysql_fetch_assoc($result)) {
    if($board ==  $row['id']) {
      echo "【タイトル：";
      echo "<tr><td>";
      //echo $row['title'];
      echo $row['title'] . '】';
      //echo $board;
      //echo "</td><td>";
      //echo '';  
      echo "</td></tr>";
    }
  }

//コメント投稿
?>

  <!-- コメント一覧 -->
  <table border="1" width="400" cellspacing="0">
  <tr>
    <th width="400">コメント一覧</th>
    <th width="100">name</th>
    <th width="100">編集</th>
  </tr>

<?php
  $sql = "SELECT * FROM comment";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  //データの取り出し()
  while ($row = mysql_fetch_assoc($result)) {
 
    if($board == $row['board_id']){
      echo "<tr><td>";
      echo $row['contents'];
      echo "</td>";
      
      echo "<td>";
      echo $row['user_name'];
      echo "</td>";
 
      echo "<td>";
      if($user_name == $row['user_name']){
        //echo '<a href="edit.php">'.'編集・削除';      
        echo '<form method="post" action="edit.php">'.
             '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
             '<input type="hidden" value="'.$board.'" name="board-id" />'.
             '<input type="submit" value="編集・削除" name="delete_submit" />'.
             '</form>';
        echo "</tr></td>\n";
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

  <div>
  <!-- コメント投稿フォーム -->
  <form method="post" action="store_comment.php">
    <?php echo $row['title']; ?><br /><br />
      <label>コメント投稿：</label><br />
      <textarea id="comment" name="comment" cols="50" rows="6"></textarea><br />
      <input type="hidden" value="<?php echo $board; ?>" name="board-id">
      <input type="hidden" value="<?php echo $user_name; ?>" name="user_name">
      <input type="submit" value="コメント送信" name="submit" /><br /><br />
  </form>
  <!-- コメント投稿フォーム_END -->
  </div>


<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
