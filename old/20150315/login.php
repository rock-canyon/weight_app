<?php

require_once('config.php');
require_once('functions.php');

session_start();

if (!empty($_SESSION['me'])) {
	header('Location: '.SITE_URL);
}

function getUser($email, $password, $dbh) {
	$sql = "select * from users where email = :email and password = :password limit 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(":email"=>$email, ":password"=>getSha1Password($password)));
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	return $user ? $user : false;
}


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	// CSRF対策
	setToken();
} else {
	// CSRF対策
	checkToken();
	
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	$dbh = connectDb();
	
	$error = array();
	
	// メールアドレスが登録されていない
	if (!emailExists($email, $dbh)) {
		$error['email'] = 'このメールアドレスは登録されていません';
	}
	// メールアドレスの形式が不正
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error['email'] = 'メールアドレスの形式が正しくありません';
	}
	// メールアドレスが空？
	if ($email == '') {
		$error['email'] = 'メールアドレスを入力してください';
	}
	// メールアドレスとパスワードが正しくない
	if (!$me = getUser($email, $password, $dbh)) {
		$error['password'] = 'パスワードとメールアドレスが正しくありません';
	}
	// パスワードが空？
	if ($password == '') {
		$error['password'] = 'パスワードを入力してください';
	}
	
	// 名前が空？
	
	if (empty($error)) {
		// セッションハイジャック対策
		session_regenerate_id(true);
		$_SESSION['me'] = $me;
		header('Location: '.SITE_URL);
		exit;
	}
	
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="weight_app.css">
<link rel="shortcut icon" href="favicon.gif" >
<title>ログイン -体重管理アプリ-</title>
</head>
<body>
<div id="container">
	<h1>体重管理アプリ</h1>
	<h2>ログイン</h2>
	<form action="" method="POST">
		<p>メールアドレス：<input type="text" name="email" size="30" value="<?php echo h($email); ?>"></p>
		<span class="warning"><?php echo h($error['email']); ?></span>
		<p>パスワード：<input type="password" name="password" value=""></p>
		<span class="warning"><?php echo h($error['password']); ?></span>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p><input type="submit" value="ログイン"></p>
		<p><a href="signup.php">新規登録はこちら！</a></p>
	</form>
	<div id="footer">Copyright 2015 Masayoshi Iwatani All rights reserved.</div><!-- /footer -->
</div><!-- /container -->
</body>
</html>