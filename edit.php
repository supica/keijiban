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
      echo '【タイトル：';
      echo '<tr><td>';
      //echo $row['title'];
      echo $row['title'];
      //echo $row['board_id'];
      echo '】';
      //echo $board;
      echo '</td><td>';
      echo '';  
      echo '</td></tr>';
    }
  }


  //選択したタイトルにコメントが投稿された場合の処理
  if(isset($_POST['submit']) && $_POST['submit'] =='編集保存'){

    $comment = $_POST['comment'];
    //echo $comment;

    if($comment != ""){
      $sql = "UPDATE comment SET contents = $comment WHERE id = delete_id ";      
      $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

      echo '<br /><br />編集内容・・・';
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
          echo '<font color = "red"><br /><br />※コメントを入力してください</font>';
     }
  }

?>

  <!-- 選択したコメント -->
<table border="1" width="400" cellspacing="0">
  <tr>
    <th width="400">コメント内容</th>
    <!--<th width="100">編集</th>-->
    <th width="100">削除</th>
  </tr>

<?php
  $sql = "SELECT * FROM comment";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  //データの取り出し()
  while ($row = mysql_fetch_assoc($result)) {
 
    if($board == $row['board_id'] && $_POST['delete_id'] == $row['id']){
      echo "<tr><td>";
      echo " <br />".$row['contents'];
      //echo " <br />".$row['board_id']."|".$row['contents'];
      echo "</td>";
      echo "<td>";
        if($user_name == $row['user_name']){
          echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
               '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
               '<input type="hidden" value="'.$board.'" name="board-id" />'.
               '<input type="submit" value="削除" name="delete_submit" />'.
               '</form>';
          //echo '削除しました';
          echo "</td></tr>\n";
        }
    }
  }
?>
</table>

  <div>
  <!-- コメント編集フォーム -->
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <?php echo $row['title']; ?><br /><br />
      <label><b>コメントを編集する：</b></label><br />
      <textarea id="comment" name="comment" cols="50" rows="6"></textarea><br />
      <input type="hidden" value="<?php echo $board; ?>" name="board-id">
      <input type="hidden" value="<?php echo $user_name; ?>" name="user_name">
      <input type="submit" value="編集内容を保存" name="submit" /><br /><br />
  </form>
  <!-- コメント投稿フォーム_END -->
  </div>
  
<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>