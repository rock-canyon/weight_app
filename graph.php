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
	$day = str_replace("-", "/", substr($row['created'], 0, 10));
	$graphData .= "['" . $day . "', " . $row['weight'] . "], ";
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
	<script type="text/javascript">
	
	// Visualization API と折れ線グラフ用のパッケージのロード
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
		var options = { title: '<?php echo h($me['name']); ?>さんの体重グラフ',
						colors: ['red'],
						fontName: "メイリオ",
						chartArea:{width:'78%'},
						/* hAxis: {
							showTextEvery: 10
						} */
		};
		
		// LineChart のオブジェクトの作成
		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
		
		// データテーブルとオプションを渡して、グラフを描画
		chart.draw(data, options);
	}
	</script>
<title>グラフ表示 -体重管理アプリ-</title>
</head>
<body>
<div id="container">
	<h1>体重管理アプリ</h1>
	<h2>グラフ表示</h2>
	<div id="menu">
		<ul>
			<li><a href="index.php">体重登録</a></li>
			<li><a href="calc_bmi.php">BMI計算</a></li>
			<li id="selected">グラフ表示</li>
			<li><a href="userconfig.php">設定変更</a></li>
			<li><a href="logout.php">ログアウト</a></li>
		</ul>
	</div><!-- /menu -->
	<p><?php echo h($me['name']); ?>さん ログイン中です。</p>
	<!-- グラフを描く div 要素 -->
	<div id="chart_div" style="width: 600px; height: 400px;"></div><!-- /cart_div -->
	<div id="footer">Copyright 2015 Masayoshi Iwatani All rights reserved.</div><!-- /footer -->
</div><!-- /container -->
</body>
</html>