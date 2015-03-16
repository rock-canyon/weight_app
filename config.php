<?php

define('DSN', 'mysql:host=localhost;dbname=weight_app');
define('DB_USER', 'dbuser');
define('DB_PASSWORD', 'vKei7H3p4s');

define('SITE_URL', 'http://localhost/weight_app/');

error_reporting(E_ALL & ~E_NOTICE);

session_set_cookie_params(0, '/weight_app/');