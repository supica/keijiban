<?php
  if(isset($_COOKIE['user_name'])){
  setcookie('user_name',"",time()-3600);
  }
?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ひとこと掲示板</title>
</head>
<body>
  <h1><a href="index.php">ひとこと掲示板</a></h1>
  <h2>ユーザー登録が完了しました</h2>
<p><a href="index.php">HOMEに戻る</a></p>
</body>
</html>
