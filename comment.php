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
  // クエリ(検索条件)を送信する
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  //データの取り出し
  $board = '';
  $comment = '';

  while ($row = mysql_fetch_assoc($result)) {

  if($board ==  $row['id']) {
    echo "タイトル：";
    echo "<tr><td>";
    echo $row['title'];
    echo "</td><td>";
    echo '';  
    echo "</td></tr>";
  }

    $board = $_POST['board-id'];    

    if(isset($_POST['comment_submit'])=='送信'){

    $comment = $_POST['comment'];

    $query = "SELECT * FROM comment WHERE contents = '$board','$comment';";
 
    while ($row = mysql_fetch_assoc($result)) {

      if($board ==  $row['id'] && $comment != "") {
      $query = mysql_query("INSERT INTO training01.comment(board_id,contents) VALUES('$board','$comment')", $link);
  
        echo '投稿内容・・・';
        echo "<tr><td>";
        //echo $row['id'];
        echo "</td><td>";
        echo '「';
        echo "<tr><td>";
        echo $comment;
        echo '」';
        echo "</td><td>";
        

        
        //echo '';  
        echo "</td></tr>";
      } elseif($comment == "") {
        echo "入力してください";
      }
    
  }
  }
  
  } 
  
?>


<div>
  <!-- コメント欄 -->
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <?php echo $row['title']; ?><br />  
  <br /><label>コメントを書き込む：</label><br />
  <textarea id="comment" name="comment" cols="50" rows="6"></textarea><br />
  <input type="submit" value="送信" name="comment_submit" /><br /><br />
  <input type="hidden" value="<?php echo $board; ?>" name="board-id">
  </form>
  <!-- コメント欄_END -->
</div>


<?php

?>


<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
