<?php
  if(isset($_SESSION['post_proc']) == true){
    $_SESSION['post_proc'] = false;
    header("HTTP/1.1 301 Moved Premanently");
    header('Location:'.$_SERVER['PHP_SELF']);
  exit();
  }
?>

<?php
  // データベース設定
  require_once('dbsettings.php');

  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("MySQLへの接続に失敗しました。");
  $sdb = mysql_select_db(DB_NAME,$link) or die("データベースの選択に失敗しました。");  
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板:コメント投稿</title>
</head>

<body>
  <h1><a href="index.php">ひとこと掲示板</a></h1>

<?php
  // ログイン：判定
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
  // ログイン時：「～さんでログイン中」表示
  $user_name = '';
  $login_message = '';
  
  if(login_check() == true){
    $user_name = $_COOKIE['user_name'];      
    $login_message =  '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中　　';
      echo $login_message;
      echo '<a href="logout.php">ログアウト</a><br /><br />';
  }else {
    $login_message =  '<font color = "red">※ログインして下さい</font>';
      echo $login_message;
  }
  //セッションを開始する
  session_start();

  // 選択したタイトルのコメントを表示／クエリ(検索条件)を送信する
  //データの取り出し(タイトルを表示)
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
    
  // 選択したタイトル名を表示
  $board = '';
  
  if(isset($_GET['board-id'])){
    $board = $_GET['board-id'];
  }elseif(isset($_SESSION['board-id'])){
    $board = $_SESSION['board-id'];
  }else{
    echo '<br /><p><a href="index.php">HOMEに戻る</a></p>';
  exit;
  }
 

  while ($row = mysql_fetch_assoc($result)) {
    if($board == $row['id']) {
      echo "【タイトル：";
      echo "<tr><td>";
      echo $row['title'] . '】';
      echo "</td></tr>";
    }
  }


  $reg_sts = '';

  if(isset($_SESSION['reg_sts'])){
  
  $reg_sts = $_SESSION['reg_sts'];  
  //$board = $_SESSION['board-id'];

  switch ($reg_sts){
      case -1:
          $disp_sts = '<font color = "red">※登録できませんでした。<br /></font>';
          break;
      case 1:
          $disp_sts = '<font color = "blue">※コメントを登録しました。<br /></font>';
          break;
      case 2:
          $disp_sts = '<font color = "red">※コメントを入力してください。<br /></font>';
          break;
      case 3:
         $disp_sts = '<font color = "red">※コメントは全角30文字以内で入力してください。<br /></font>';
          break;
      case 0:
          $disp_sts = '';
          break;
      default:
          $disp_sts = '<font color = "red">※!?<br /></font>';
          break;
  }
  echo $disp_sts.'<br />';
  }

  $comment = '';
  $comment_error = '';

  //投稿コメントが30文字以上の時、コメントを30文字まで表示
  if($reg_sts == 3){
    if(isset($_SESSION["comment"])){
      $comment = $_SESSION["comment"];
      $board = $_SESSION['board-id'];
      $comment_error = mb_strimwidth($comment,0,60,'','utf-8');
    }
  }
  
  session_unset();
?>

  <!-- コメント一覧 -->
  <table border="1" width="425" cellspacing="0">
  <tr>
    <th width="800">コメント一覧</th>
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
      <label>コメント投稿：　<全角30文字以内></label><br />
      <textarea id="comment" name="comment" cols="60" rows="2"><?php echo $comment_error; ?></textarea><br />
      <input type="hidden" value="<?php echo $board; ?>" name="board-id">
      <input type="hidden" value="<?php echo $user_name; ?>" name="user_name">
      <input type="submit" value="コメント送信" name="submit" /><br /><br />
  </form>
  <!-- コメント投稿フォーム_END -->
  </div>


<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
