<?php

require_once('config.php');
require_once('functions.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	// CSRF対策
	setToken();
} else {
	checkToken();
	
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$height = $_POST['height'];
	
	$dbh = connectDb();
	
	$error = array();
	$success = false;
	
	// 名前が空？
	if ($name == '') {
		$error['name'] = 'お名前を入力してください';
	}
	// メールアドレスが正しい？
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error['email'] = 'メールアドレスの形式が正しくありません';
	}
	if (emailExists($email, $dbh)) {
		$error['email'] = 'このメールアドレスは既に登録されています';
	}
	// メールアドレスが空？
	if ($email == '') {
		$error['email'] = 'メールアドレスを入力してください';
	}
	// パスワードが空？
	if ($password == '') {
		$error['password'] = 'パスワードを入力してください';
	}
	// 身長が数字以外？
	if (!is_numeric($height) && $height != "") {
		$error['height'] = '身長は数字で入力してください';
	}
	
	if (empty($error)) {
		// 登録処理
		$sql = "insert into users
				(name, email, password, height, created, modified)
				values
				(:name, :email, :password, :height, now(), now())";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":name" => $name,
			":email" => $email,
			":password" => password_hash($password, PASSWORD_DEFAULT),
			":height" => $height === "" ? NULL : $height,
		);
		$stmt->execute($params);
		
		$success = true;
	}
	
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="favicon.gif" >
<link rel="stylesheet" type="text/css" href="weight_app.css">
<link rel="stylesheet" type="text/css" href="lib/sweet-alert.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="lib/sweet-alert.min.js"></script>
<title>新規ユーザー登録 -体重管理アプリ-</title>
</head>
<body>
<div id="container">
	<h1>体重管理アプリ</h1>
	<h2>新規ユーザー登録</h2>
	<form action="" method="POST">
		<p>お名前*：<input type="text" name="name" value="<?php echo h($name); ?>"></p>
		<span class="warning"><?php echo h($error['name']); ?></span>
		<p>メールアドレス*:<input type="text" name="email" size="30" value="<?php echo h($email); ?>"></p>
		<span class="warning"><?php echo h($error['email']); ?></span>
		<p>パスワード*：<input type="password" name="password" value=""></p>
		<span class="warning"><?php echo h($error['password']); ?></span>
		<p>身長：<input type="text" name="height" value="<?php echo h($height); ?>"></p>
		<span class="warning"><?php echo h($error['height']); ?></span>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p id="notice">*は必須項目です。</p>
	<p><input type="submit" value="新規登録！">
	</form>
	<a href="index.php">戻る</a></p>
	<div id="footer">Copyright 2015 Masayoshi Iwatani All rights reserved.</div><!-- /footer -->
</div><!-- /container -->
<?php
	if ($success) {
	
		echo '<script>swal("ユーザー登録が完了しました。", "", "success",
			function() { location.href="login.php"; 
		});</script>';
	/*
		echo '<script>alert("ユーザー登録が完了しました。");
			  location.href="login.php";</script>';
	}
	*/
?>
</body>
</html>
