CREATE DATABASE weight_app;
GRANT ALL ON wapp.* TO dbu@mysql.hostinger.jp identified by 'Bzbcri0h7w';
USE wapp;

CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	email VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	height DOUBLE,
	created DATETIME,
	modified DATETIME
);

CREATE TABLE weight_log (
	id INT,
	weight DOUBLE,
	created DATETIME,
	modified DATETIME
);

// BMI計算のため、身長と最新の体重を取得するSQL文
SELECT height, weight, weight_log.created FROM users, weight_log
WHERE users.id = :id AND users.id = weight_log.id
ORDER BY weight_log.created DESC LIMIT 1

// 自動連番のリセット
ALTER TABLE users AUTO_INCREMENT = 1;