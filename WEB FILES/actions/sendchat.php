<?php
require_once(__DIR__ . "/../config.php");
session_start();
$user_discordid = $_SESSION['user_discordid'];

if ($user_discordid != "")
{
	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	  } catch(PDOException $ex)
	  {
		echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
	  }
	 
	  $stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	  $stmt->execute([$user_discordid]);
	  $identifier = $stmt->fetchAll();
	  
	  foreach ($identifier as $row)
	  {
		$user_identifier = $row['identifier'];
	  }
	  
	  if ($_SESSION['leoperms'] == 1 || $_SESSION['fireemsperms'] == 1 || $_SESSION['dispatchperms'] == 1)
	  {
		  if(isset($_SESSION['user_name'])){
			  $text = $_POST['text'];
	  
			  if ($text != "")
			  {
				  $text_message = "<div class='msgln'><span class='chat-time'>".gmdate("g:i A")."</span> <b class='user-name'>".$user_identifier."</b> ".stripslashes(htmlspecialchars($text))."<br></div>";
				  file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
			  }
		  }
	  }
}
?>