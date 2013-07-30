<?php
  if(isset($_COOKIE['user_name'])){
  setcookie('user_name',"",time()-3600);
  }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href="style.css" rel="stylesheet" type="text/css">
  <title>ひとこと掲示板</title>
</head>
<body>
<div id="wrapper">
  <h1><a href="index.php">ひとこと掲示板</a></h1>
  <h2>ユーザー登録が完了しました</h2>
<p><a href="index.php">HOMEに戻る</a></p>
</div><!-- wrapper_END -->
</body>
</html>
