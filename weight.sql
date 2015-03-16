CREATE DATABASE weight_app;
GRANT ALL ON weight_app.* TO dbuser@localhost identified by 'vKei7H3p4s';
USE weight_app;

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


// ユーザー情報更新
UPDATE users
SET name = :name,
	email = :email,
	password = :password,
	height = :height,
	modified = now()
WHERE id = $me['id'];

// 自動連番のリセット
ALTER TABLE users AUTO_INCREMENT = 1;