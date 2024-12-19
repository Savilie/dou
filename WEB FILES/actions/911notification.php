<?php
require_once(__DIR__ . "/../config.php");
$get911 = htmlspecialchars($_GET['get911']);

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo "Could not connect -> ".$ex->getMessage();
	die();
}

$result = $pdo->query("SELECT * FROM users WHERE currdept='DISPATCH' AND currstatus!='10-7'");

if (sizeof($result->fetchAll()) > 0)
{
	// INSERT INTO DATABASE
	$stmt = $pdo->prepare("INSERT INTO 911call (info) VALUES (?)");
	$result2 = $stmt->execute(array($get911));
}
?>