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
$error = array();

$dbh = connectDB();

$sql = "SELECT height, weight, weight_log.created FROM users, weight_log
		WHERE users.id = :id AND users.id = weight_log.id
		ORDER BY weight_log.created DESC LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute(array(":id" => $me['id']));
$bodyData = $stmt->fetch();
// エラー処理
if (!$bodyData) {
	$error['weight'] = "体重が一度も登録されていません。<br>
						体重を登録してからお試しください。<br>";
}

if ($bodyData['height'] == NULL) {
	$error['height'] = "身長が登録されていません。<br>
						身長を登録してからお試しください。<br>";
}

if(empty($error)) {
	$bmi = number_format(round($bodyData['weight'] / pow($bodyData['height'] / 100, 2), 2), 1);

	$bmiDate = date("Y/m/d（D）",strtotime($bodyData['created']));
	
	$fatness = checkFatness($bmi);
}


function checkFatness($bmi) {
	if ($bmi >= 40.0) {
		return "肥満（4度）";
	} else if ($bmi >= 35.0) {
		return "肥満（3度）";
	} else if ($bmi >= 30.0) {
		return "肥満（2度）";
	} else if ($bmi >= 25.0) {
		return "肥満（1度）";
	} else if ($bmi >= 18.0) {
		return "普通体重";
	} else {
		return "低体重（痩せ型）";
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="weight_app.css">
<link rel="shortcut icon" href="favicon.gif" >
<title>体重管理アプリ</title>
</head>
<body>
	<h1>体重管理アプリ</h1>
	<h2>BMI計算</h2>
	<div id="menu">
		<ul>
			<li><a href="logout.php">ログアウト</a>
			<li><a href="graph.php">グラフ表示</a>
		</ul>
	</div>
	<p><?php echo $me['name']; ?>さん ログイン中です。</p>
	<p><?php if(empty($error)) {
		echo $bmiDate . "時点のBMIは " . $bmi . "です。<br>";
		echo $fatness . "です。";
	} else {
		echo "<span class=\"warning\">" . $error['weight'];
		echo $error['height'] . "</span>";
	}
	?></p>
	<a href="index.php">戻る</a>
</body>
</html>