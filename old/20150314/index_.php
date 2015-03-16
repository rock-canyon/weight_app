<?php

require_once('config.php');
require_once('functions.php');

session_start();

// ログインしてなければログイン画面へ
if (empty($_SESSION['me'])) {
	header('Location: '.SITE_URL.'login.php');
}

// ログイン情報を変数に格納
$me = $_SESSION['me'];

if ($_SERVER['REQUEST_METHOD'] != "POST") {
	// 投稿前
	
	// CSRF対策
	setToken();
	
} else {
	// 投稿後
	
	// CSRF対策
	checkToken();
	
	$weight = $_POST['weight'];
	
	// 入力値のエラー処理
	$error = array();
	
	if (!is_numeric($weight)) {
		$error['weight'] = "数字を入力してください。";
	}
	
	if ($weight == "") {
		$error['weight'] = "体重が空欄です。";
	}
	
	// データベースに登録
	if (empty($error)) {
		$dbh = connectDB();
		
		$sql = "insert into weight_log (id, weight, created, modified)
				values (:id, :weight, ADDTIME(NOW(), '09:00:00'), ADDTIME(NOW(), '09:00:00'))";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":id" => $me['id'],
			":weight" => $weight
		);
		$stmt->execute($params);
		
		echo '<script language="javascript">alert("体重登録が完了しました。");</script>';
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="weight_app.css">
<link rel="shortcut icon" href="favicon.gif" >
<title>体重登録 -体重管理アプリ-</title>
</head>
<body>
<div id="container">
	<h1>体重管理アプリ</h1>
	<h2>体重登録</h2>
	<div id="menu">
		<ul>
			<li id="selected">体重登録</li>
			<li><a href="calc_bmi.php">BMI計算</a></li>
			<li><a href="graph.php">グラフ表示</a></li>
			<li><a href="userconfig.php">設定変更</a></li>
			<li><a href="logout.php">ログアウト</a></li>
		</ul>
	</div>
	<p><?php echo $me['name']; ?>さん ログイン中です。</p>

	<p><?php echo date('Y/m/d(D)'); ?></p>
	<p>あなたの今日の体重は？</p>
	<form action="index.php" method="post">
		<p id=weight_box><input type="text" name="weight">kg</p>
		<span class="warning"><?php if($error['weight']) echo h($error['weight']); ?></span>
		<p><input type="submit" value="登録"></p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
	</form>
	
</div>
</body>
</html>