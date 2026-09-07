<?php

use api\config\Env;

$host = Env::get('DB_HOST', 'localhost');
$dbname = Env::get('DB_NAME', 'restaurante');
$username = Env::get('DB_USERNAME', 'root');
$password = Env::get('DB_PASSWORD', '');

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

try {
	$pdo = new \PDO($dsn, $username, $password, [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_EMULATE_PREPARES => false,
	]);
} catch (\PDOException $exception) {
	throw new \RuntimeException('Database connection failed.', 0, $exception);
}

