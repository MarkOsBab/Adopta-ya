<?php
$Panel_Title = 'Adopta YA! | Panel administrativo';
$Title = 'Adopta YA!';
session_start();
/* DATABASE CONFIGURATION */
define('DB_SERVER', 'mysql.secureserver.net');
define('DB_USERNAME', 'slkahduwiqiu2322');
define('DB_PASSWORD', 'sad!sjw7362S');
define('DB_DATABASE', 'adopta_ya');
define("BASE_URL", "http://adoptaya.com/");


function getDB() 
{
	$dbhost=DB_SERVER;
	$dbuser=DB_USERNAME;
	$dbpass=DB_PASSWORD;
	$dbname=DB_DATABASE;
	try {
		$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
		$dbConnection->exec("set names utf8");
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbConnection;
	}
	catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
}

?>