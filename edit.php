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
  
  
  $user_name = '';
  $login_message = '';
  $delete_id = '';
  $delete_comment = '';
  $board = '';
  $comment = '';
  $comment_str = '';
  $edit_message = '';
  $comment_edit = '';
  $comment_over = '';


  // ログイン中の表示
  if(login_check() == true){
    $user_name = $_COOKIE['user_name'];      
    $login_message =  '<a href="logout.php">ログアウト</a><br /><br />'.
                      '今は ' . '('.$_COOKIE['user_name'].')'.' さんでログイン中';
    $regist_change = '<a href="regist_change.php">パスワード変更</a>　　　';
  }else {
    $login_message =  '<a href="regist.php">ユーザー登録</a>　　　'.'<font color = "red">※ログインして下さい<br /><br /></font>';
  }


  // 選択したコメントを表示／クエリ(検索条件)を送信する
  $sql = "SELECT * FROM board";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
  
  if(!isset($_POST['board-id'])){
    $error_message = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.
                     '<font color = "red">※コメント編集は、トップページの"タイトルを選ぶ"を選択後、
                     <br />　コメント一覧の「編集・削除ボタン」よりお進みください</font>'.
                     '<p><a href="index.php">HOMEに戻る</a></p>';
    echo $error_message;
  exit;
  }

  //タイトルを表示（データの取り出し）
  $board = $_POST['board-id'];
  
  while ($row = mysql_fetch_assoc($result)) {  
    if($board ==  $row['id']) {
      $title_disp = '【タイトル：'.'<tr><td>'.$row['title'].'】'.'</td><td>'.''.'</td></tr>';
    }
  }

  //削除ボタンが押された場合に行う処理
  if(isset($_POST['delete_submit']) && $_POST['delete_submit'] == '削除'){
    $delete_id = $_POST['delete_id'];
    $delete_comment = '<font color="red"><br /><br />※コメントを削除しました。</font>';
 
    $sql = "DELETE FROM comment WHERE id = $delete_id";
    $result = mysql_query($sql,$link) or die('ERROR!(削除):MySQLサーバーへの接続に失敗しました。');
  }


  //選択したコメントが編集された場合の処理
  if(isset($_POST['submit']) && $_POST['submit'] =='編集内容を保存'){

    $comment = trim($_POST['comment']);
    $delete_id = $_POST['delete_id'];
    $comment_str = mb_strlen($comment,'utf-8'); //文字数をカウント
    $comment_chars = htmlspecialchars($comment,ENT_QUOTES);
    $comment_br = nl2br($comment_chars);

    if($comment != ""){
    
      if($comment_str <= 150){
        $sql = "UPDATE comment SET contents = '$comment_br' WHERE id = $delete_id";
        $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
          $edit_message = '<font color = "blue"><br /><br />※コメントを編集しました</font>';
      }else{
        $comment_error = mb_substr($comment,0,150,'utf-8'); //文字数で丸め
          $edit_message = '<font color = "red"><br /><br />※コメントは150字以内で編集してください</font>';
      }
    }    
    elseif($comment == "") {
        $edit_message = '<font color = "red"><br /><br />※コメントを入力してください</font>';
    }
  }

  $sql = "SELECT * FROM comment";
  $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

  //コメントの取り出し
  $rec_cnt = 0;
  $rec_comment = '';
  while ($row = mysql_fetch_assoc($result)) {

    if($board == $row['board_id'] && $_POST['delete_id'] == $row['id']){
      $comment_edit = "<tr><td>".
                      " ".$row['contents'].
                      "</td>".
                      "<td>";
      $rec = $row['contents'];
      $rec_comment = str_replace(array('<br />','<br>'), "", $rec);
      
      $comment_id = '';
      
        if($user_name == $row['user_name']){
          $comment_edit .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
                           '<input type="hidden" value="'.$row['id'].'" name="delete_id" />'.
                           '<input type="hidden" value="'.$board.'" name="board-id" />'.
                           '<input type="submit" value="削除" name="delete_submit" />'.
                           '</form>';
          $comment_id = $row['id'];
          $rec_cnt++;
          $comment_edit .= "</td></tr>\n";

          $comment_over = '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
                          '<br /><br />'.
                          '<label>'.
                          '<b>コメントを編集する：</b>'.
                          '</label>'.
                          '<br />'.
                          '<textarea id="comment" name="comment" cols="50" rows="5">';
                           if($comment_str > 150){
                            $comment_over .= $comment_error;
                            }else{
                            $comment_over .= $rec_comment;
                            }
          $comment_over .= '</textarea><br />'.
                           '<input type="hidden" value="'.$comment_id.'" name="delete_id" />'.
                           '<input type="hidden" value="'.$board.'" name="board-id">'.
                           '<input type="hidden" value="'.$user_name.'" name="user_name">'.
                           '<input type="submit" value="編集内容を保存" name="submit" /><br /><br />'.
                           '</form>';
        }
    }
  }
              if(0 == $rec_cnt){
                $comment_edit .=  '<td colspan="2">'.
                                  'データはありません。'.
                                  '</td>';
              } 

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>ひとこと掲示板</title>
</head>

<body>
  <h1><a href="index.php">ひとこと掲示板</a></h1>
  <!--<a href="login.php">ログイン</a>　　　-->
  <!--<a href="regist.php">ユーザー登録</a>　　　-->

<?php 
  echo $regist_change;
  echo $login_message;
  echo $edit_message;
  echo $delete_comment;
 ?>

  <!-- 選択したコメントの表示 -->
<br /><br />

<?php echo $title_disp; ?>

<table style="word-break:break-all;" border="1" width="425" cellspacing="0" cellpadding="5">
  <tr>
    <th width="">コメント内容</th>
    <th width="60">削除</th>
  </tr>
  <?php echo $comment_edit; ?>
</table>

  <div>
  <!-- コメント編集フォーム -->
  <?php echo $comment_over; ?>
  </div>
  
<p><a href="index.php">HOMEに戻る</a></p>

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

