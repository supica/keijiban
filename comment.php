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

  $board = '';
  $reg_sts = '';
  $comment = '';
  $comment_error = '';
  $comment_rec = '';
  $title_disp = '';
  $disp_sts = '';
  $comment_list = '';
  $user_name = '';
  $regist_change = '';
  $error_message = '';
  $login_massege = '';

  // ログイン中の表示
  if(login_check() == true){
    $user_name = $_COOKIE['user_name'];      
    $login_message = '<img src="./images/icon_pink.png" /><a href="logout.php">ログアウト</a>　　
                      <img src="./images/icon_brown.png" /><a href="resign.php" class="resign">退会</a><br /><br />'.
                     '<img src="./images/icon_red.png" />
                      今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中<br />';
    $regist_change = '<img src="./images/icon_org.png" /><a href="regist_change.php">パスワード変更</a>　　';
  }else {
    /*$login_message =  '<img src="./images/icon_green.png" /><a href="regist.php">ユーザー登録</a>　　'.
                      '<font color = "red">※ログインして下さい　</font>';*/

    $error_message = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.
                     '<link href="style.css" rel="stylesheet" type="text/css">'.
                     '<div id="wrapper">'.
                     '<a href="index.php"><h1 id="hitokoto"><span>ひとこと掲示板</span></h1></a>'.
                     '<p class="disp_msg">※ログインして下さい<br /><br /></p>'.
                     '<p><a href="index.php">HOMEに戻る</a></p>'.
                     '</div>';
    echo $error_message;
  exit;
  }

  //セッションを開始する
  session_start();

  // 選択したタイトルのコメントを表示／クエリ(検索条件)を送信する
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
    
  // 選択したタイトル名を表示  
  if(isset($_GET['board-id'])){
    $board = $_GET['board-id'];
  }elseif(isset($_SESSION['board-id'])){
    $board = $_SESSION['board-id'];
  }else{
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
          <link href="style.css" rel="stylesheet" type="text/css"></head>'.
         '<div id="wrapper">'.
         '<a href="index.php"><h1 id="hitokoto"><span>ひとこと掲示板</span></h1></a>'.
         '<br /><p><a href="index.php">HOMEに戻る</a></p>'.
         '</div>';
  exit;
  }

  while ($row = mysql_fetch_assoc($result)) {
    if($board == $row['id']) {
      $title_disp = '【タイトル：'.'<tr><td>'.$row['title'] . '】'. '</td></tr>';
    }
  }

  //コメント一覧の表示／データの取り出し()
  $sql = "SELECT * FROM comment";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  while ($row = mysql_fetch_assoc($result)) {
      
    if($board == $row['board_id']){
      $comment_list .= "<tr><td>".
                      $row['contents'].
                      "</td>".
                      '<td class="delate">'.
                      $row['user_name'].
                      "</td>".
                      '<td class="delate">';
      if($user_name == $row['user_name']){
      $comment_list .= '<form method="post" action="edit.php">'.
                       '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
                       '<input type="hidden" value="'.$board.'" name="board-id" />'.
                       '<input type="submit" value="編集・削除" name="delete_submit" class="submit" />'.
                       '</form>'.
                       "</tr></td>\n";
      }
      else {
      $comment_list .= '---';  
      }
    }
  }

  if(isset($_SESSION['reg_sts'])){  
  $reg_sts = $_SESSION['reg_sts'];  

    switch ($reg_sts){
        case -1:
            $disp_sts = '<p class="disp_msg">※登録できませんでした。</p>';
            break;
        case 1:
            $disp_sts = '<p class="disp_msg2">※コメントを登録しました。</p>';
            break;
        case 2:
            $disp_sts = '<p class="disp_msg">※コメントを入力してください。</p>';
            break;
        case 3:
            $disp_sts = '<p class="disp_msg">※コメントは全角150文字以内で入力してください。</p>';
            break;
        case 0:
            $disp_sts = '';
            break;
        default:
            $disp_sts = '<font color = "red">※!?<br /></font>';
            break;
    }
  }

  //投稿コメントが150文字以上の時、コメントを150文字まで表示
  //(半角英数字はカナ変換→チェック→半角英数字に変換)
  if($reg_sts == 3){
    if(isset($_SESSION["comment"])){
     
      $comment = $_SESSION["comment"];
      $comment_str = mb_strlen($comment,'utf-8'); //文字数をカウント
      $comment_error = mb_substr($comment,0,150,'utf-8'); //文字数で丸め
      $comment_chars = htmlspecialchars($comment,ENT_QUOTES); //htmlタグを無効化
      $comment = nl2br($comment_chars); //<br />タグを追加
    }
    //$_SESSION['comment_rec'] = $comment_rec;
  }
  session_unset();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="style.css" rel="stylesheet" type="text/css">
  <title>ひとこと掲示板</title>
</head>

<body>
<div id="wrapper">
  <a href="index.php"><h1 id="hitokoto"><span>ひとこと掲示板</span></h1></a>

<div class="disp_msg">
  <?php
    echo $regist_change;
    echo $login_message;
    echo $error_message;
    echo $disp_sts;
   ?>
</div><!-- _END -->
  <!-- 選択したコメント -->
<div class="space15px"> </div>
<?php echo $title_disp; ?>
<div class="space05px"> </div>

<div class="title-lay">
  <!-- コメント一覧 -->
  <!-- word-break:break-all = 行末で改行 （単語の途中であっても改行させる）-->
  <table  class="lay" style="word-break:break-all;" border="1" width="425" cellspacing="0" cellpadding="5">
  <tr>
    <th width="">コメント一覧</th>
    <th width="60">name</th>
    <th width="60">編集</th>
  </tr>
<?php echo $comment_list; ?>
</table>
</div><!-- title-lay_END -->

<div class="comment-post">
  <!-- コメント投稿フォーム -->
  <form method="post" action="store_comment.php">
    <?php echo $row['title']; ?>
      <label>コメント投稿：　<全角150文字以内></label><br />
      <textarea id="comment" name="comment" cols="50" rows="5" class="comment-lay"><?php echo $comment_error; ?></textarea><br />
      <input type="hidden" value="<?php echo $board; ?>" name="board-id">
      <input type="hidden" value="<?php echo $user_name; ?>" name="user_name">
      <input type="submit" value="コメント送信" name="submit" class="submit" /><br /><br />
  </form>
  <!-- コメント投稿フォーム_END -->
</div>

<p class="home"><img src="./images/icon_l-blue.png" /><a href="index.php">HOMEに戻る</a></p>
</div><!-- wrapper_END -->
<footer>
  <div class="footer">
  </div><!-- footer-bottom_END -->
</footer>
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