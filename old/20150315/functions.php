<?php

function connectDb() {
	try {
		return new PDO(DSN, DB_USER, DB_PASSWORD);
	} catch (PDOException $e) {
		echo $e->getMessage();
		exit;
	}
}

function h($s) {
	return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function setToken() {
	if (!isset($_SESSION['token'])) {
		$_SESSION['token'] = sha1(uniqid(mt_rand(), true));
	}
}

function checkToken() {
	if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
		echo "不正な処理です！";
		exit;
	}
}

function emailExists($email, $dbh) {
	$sql = "select * from users where email = :email limit 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(":email" => $email));
	$user = $stmt->fetch();
	return $user ? true : false;
}

function getSha1Password($s) {
	return (sha1(PASSWORD_KEY.$s));
}