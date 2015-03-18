<?php

require_once('config.php');
require_once('functions.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	// CSRF対策
	setToken();
	
	// 変更前の値をDBから取得
	$dbh = connectDb();
	$sql = "SELECT * FROM users WHERE id = :id";
	$stmt = $dbh->prepare($sql);
	$params = array(":id" => $_SESSION['me']['id']);
	$stmt->execute($params);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	
	// DBから取得した値をフォームに表示するため、変数に格納
	$name = $user['name'];
	$email = $user['email'];
	$height = $user['height'];
	
} else {
	// CSRF対策
	checkToken();
	
	$name = $_POST['name'];
	$email = $_POST['email'];
	$height = $_POST['height'];
	
	$me = $_SESSION['me'];
	
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
/*	if (emailExists($email, $dbh)) {
		$error['email'] = 'このメールアドレスは既に登録されています';
	} */
	// メールアドレスが空？
	if ($email == '') {
		$error['email'] = 'メールアドレスを入力してください';
	}
	// 身長が数字以外？
	if (!is_numeric($height) && $height != "") {
		$error['height'] = '身長は数字で入力してください';
	}
	
	if (empty($error)) {
		// ユーザー情報更新処理
		$sql = "UPDATE users
				SET name = :name,
					email = :email,
					height = :height,
					modified = now()
				WHERE id = :id";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":name" => $name,
			":email" => $email,
			":height" => $height === "" ? NULL : $height,
			":id" => $me['id']
		);
		$stmt->execute($params);
/*		
		$sql = "SELECT * FROM users WHERE id = :id";
		$stmt = $dbh->prepare($sql);
		$stmt->execute($me['id']);
		$_SESSION['me'] = $stmt->fetch(PDO::FETCH_ASSOC);
*/		
		// echo '<script language="javascript">alert("変更が完了しました。");';
		// echo 'location.href = "index.php"</script>';
		// header('Location: '.SITE_URL);
		// exit;
		
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="lib/sweet-alert.css">
<script src="lib/sweet-alert.min.js"></script>
<title>設定 -体重管理アプリ-</title>
</head>
<body>
<div id="container">
	<h1>体重管理アプリ</h1>
	<h2>設定変更</h2>
		<div id="menu">
		<ul>
			<li><a href="index.php">体重登録</a></li>
			<li><a href="calc_bmi.php">BMI計算</a></li>
			<li><a href="graph.php">グラフ表示</a></li>
			<li id="selected">設定変更</a></li>
			<li><a href="logout.php">ログアウト</a></li>
		</ul>
	</div><!-- /menu -->
		<form action="" method="POST">
		<p><label for="name">お名前*：</label><input id="name" type="text" name="name" value="<?php echo h($name); ?>"></p>
		<span class="warning"><?php echo h($error['name']); ?></span>
		<p><label for="email">メールアドレス*:</label><input id="email" type="text" name="email" size="30" value="<?php echo h($email); ?>"></label></p>
		<span class="warning"><?php echo h($error['email']); ?></span>
		<p><label for="height">身長：<input id="height" type="text" name="height" value="<?php echo h($height); ?>"></label></p>
		<span class="warning"><?php echo h($error['height']); ?></span>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p id="notice">*は必須項目です。</p>
	<p><input type="submit" value="変更"></p>
	</form>
	<div id="footer">Copyright 2015 Masayoshi Iwatani All rights reserved.</div><!-- /footer -->
</div><!-- container -->
<?php
	if ($success) {
		echo '<script>swal("設定変更が完了しました。", "", "success",
			function() { location.href="login.php"; 
		});</script>';
	/*
		echo '<script>alert("設定変更が完了しました。");
			  location.href="index.php";</script>';
	*/
	}
?>
</body>
</html>
