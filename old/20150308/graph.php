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

$sql = "SELECT created, weight FROM weight_log
		WHERE id = :id ORDER BY created";
$stmt = $dbh->prepare($sql);
$stmt->execute(array(":id" => $me['id']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$graphData = "['日付', '体重'], ";
foreach ($rows as $row) {
	$graphData .= "['" . $row['created'] . "', " . $row['weight'] . "], ";
}

// エラー処理

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="weight_app.css">
<link rel="shortcut icon" href="favicon.gif" >
	<!-- AJAX API のロード -->
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript"> // Visualization API と折れ線グラフ用のパッケージのロード
	google.load("visualization", "1", {packages:["corechart"]});
	
	// Google Visualization API ロード時のコールバック関数の設定
	google.setOnLoadCallback(drawChart);
	
	// グラフ作成用のコールバック関数
	function drawChart() {
		// データテーブルの作成
		var data = google.visualization.arrayToDataTable([
		<?php echo $graphData; ?>
		]);
		
		// グラフのオプションを設定
		var options = { title: '体重グラフ' };
		
		// LineChart のオブジェクトの作成
		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
		
		// データテーブルとオプションを渡して、グラフを描画
		chart.draw(data, options);
	}
	</script>
<title>体重管理アプリ</title>
</head>
<body>
	<h1>体重管理アプリ</h1>
	<h2>グラフ表示</h2>
	<div id="menu">
		<ul>
			<li><a href="logout.php">ログアウト</a>
			<li><a href="calc_bmi.php">BMI計算</a>
		</ul>
	</div>
	<p><?php echo $me['name']; ?>さん ログイン中です。</p>
	<!-- グラフを描く div 要素 -->
	<div id="chart_div" style="width: 80%; height: 400px;"></div>
	<a href="index.php">戻る</a>
</body>
</html>