<?php
require_once(__DIR__ . "/../config.php");
session_start();

$user_discordid = $_SESSION['user_discordid'];

try{
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
    echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM 911call ORDER BY ID DESC");
$new911calls = $pdo->query("SELECT * FROM 911call");

$newCallSize = sizeof($new911calls->fetchAll());
if ($_SESSION['dispatchperms'] == 1 || $_SESSION['adminperms'] == 1) {
if ($newCallSize > 0) {

    foreach ($result as $row) {
?>       
<div class="alert alert-danger" role="alert">
  <a type="button" class="close" href="../actions/department_functions.php?delete911=<?php echo $row['ID']; ?>" style="color: white;" title="Delete"><span aria-hidden="true">&times;</span></a>
  <?php echo $row['info']; ?>
</div>
<?php
    }

} else {
  echo "<p class='text-success text-center'>No Current 911's</p>";
}
}

?>