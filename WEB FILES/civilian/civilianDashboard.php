<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();

$user_discordid = $_SESSION['user_discordid'];
$characterID = $_SESSION["characterID"];
$_SESSION['redirect'] = "/civilian/civilianDashboard.php";

if ($_SESSION['civilianperms'] != 1)
{
	header('Location: ../index.php?notCivilian');
}

if(isset($_GET['duplicateName']))
{
  $actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Someone has already taken that name, please choose a different one!</div>';
}

if(isset($_GET['characterLimit']))
{
  $actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have reached the character limit of '.MAX_CHARACTERS.'!</div>';
}

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$resetstatus = $pdo->query("UPDATE users SET currstatus='10-7', currdivision='None', currdept='None' WHERE discordid='$user_discordid'");

$result = $pdo->query("SELECT * FROM characters WHERE discordid='$user_discordid'");
$totalCharacters = $pdo->query("SELECT * FROM characters WHERE discordid='$user_discordid'");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo SERVER_SHORT_NAME; ?> CAD | Civilian</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="horizontal-menu">
        <!-- HEADER -->
        <?php include "../includes/header.inc.php"; ?>
        <!-- NAVBAR -->
        <?php include "../includes/navbar.inc.php"; ?>
      </div>

      <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
          <div class="content-wrapper">
            <?php if($actionMessage){echo $actionMessage;} ?>
          	<div class="row">
				      <div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Create Character</h3>
	                    <?php
	                    	if (sizeof($totalCharacters->fetchAll()) <= MAX_CHARACTERS)
	                    	{
	                    ?>
						    <form class="forms-sample" action="../actions/civ_functions.php"  method="post" enctype="multipart/form-data">
						        <div class="form-group">
						            <label for="character_name">Name</label>
						            <input type="text" class="form-control p-input" id="character_name" name="character_name" placeholder="Eg. John Doe" required>
						        </div>
						        <div class="form-group">
						            <label for="character_dob">Date Of Birth</label>
						            <input type="date" max="<?php echo date('Y-m-d'); ?>" class="form-control p-input" id="character_dob" name="character_dob" required>
						        </div>
						        <div class="form-group">
						            <label for="character_haircolor">Hair Color</label>
						            <input type="text" class="form-control p-input" id="character_haircolor" name="character_haircolor" placeholder="Eg. Brown" required>
						        </div>
						        <div class="form-group">
						            <label for="character_address">Address</label>
						            <input type="text" class="form-control p-input" id="character_address" name="character_address" placeholder="Eg. 55 Forum Dr." required>
						        </div>
						        <div class="form-group">
						            <label for="character_image">Character Image (Optional)</label>
						            <input type="file" class="form-control p-input" name="character_image">
						        </div>
						        <div class="form-group">
						            <label for="character_gender">Gender</label>
						            <select class="form-control p-input" id="character_gender" name="character_gender" style="color: white;" required>
						            	<option value="Male">Male</option>
						            	<option value="Female">Female</option>
						            	<option value="Other">Other</option>
						            </select>
						        </div>
						        <div class="form-group">
						            <label for="character_race">Race</label>
						            <select class="form-control p-input" id="character_race" name="character_race" style="color: white;" required>
						            	<option value="White">White</option>
						            	<option value="Black / African American">Black / African American</option>
						            	<option value="Hispanic">Hispanic</option>
						            	<option value="Pacific Islander">Pacific Islander</option>
						            	<option value="Asian">Asian</option>
						            	<option value="Indian">Indian</option>
						            </select>
						        </div>
						        <div class="form-group">
						            <label for="character_build">Build</label>
						            <select class="form-control p-input" id="character_build" name="character_build" style="color: white;" required>
						            	<option value="Average">Average</option>
						            	<option value="Fit">Fit</option>
						            	<option value="Muscular">Muscular</option>
						            	<option value="Overweight">Overweight</option>
						            	<option value="Skinny">Skinny</option>
						            	<option value="Thin">Thin</option>
						            </select>
						        </div>
						        <button type="submit" name="create_character_btn" class="btn btn-outline-info">Create</button>
						        <br><br>
						    </form> 
						    <?php
						    	}
						    	else {
						    		echo '<p class="text-primary text-center pt-4">You have reached the character limit of '.MAX_CHARACTERS.'!</p>';
						    	}
						    ?>  
	                  </div>
	                </div>
	            </div>

          		<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body text-center">
	                    <h3 class="mb-3 text-center pb-4">Select Character</h3>
	                    	<?php
	                    		foreach ($result as $row)
	                    		{
	                    			$ID = $row['ID'];
	                    			$name = $row['name'];
	                    			$dob = $row['dob'];
	                    			$dead = $row['dead'];
	                    			$newdob = date("d-m-Y", strtotime($dob));

	                    			if ($dead == 1)
	                    			{
	                    	?>
									<div class="mb-4">
                    					<button onClick="location.href='civilianDetails.php?ID=<?php echo $ID; ?>'" type="button" class="btn btn-outline-danger btn-rounded btn-fw" style="width: 500px; padding: 13px;">[DEAD] - <?php echo $name; ?> - <?php echo $newdob; ?></button>
                    				</div>     	
	                    	<?php
	                    			} else {
	                    	?>
									<div class="mb-4">
                    					<button onClick="location.href='civilianDetails.php?ID=<?php echo $ID; ?>'" type="button" class="btn btn-outline-info btn-rounded btn-fw" style="width: 500px; padding: 13px;"><?php echo $name; ?> - <?php echo $newdob; ?></button>
                    				</div>
	                    	<?php
	                    			}
	                    		}

	                    		if (empty($ID))
	                    		{
	                    			echo '<p class="text-primary">Create your first character!</p>';
	                    		}
	                    	?>
	                  </div>
	                </div>
	            </div>
          	</div>  
          	<br><br>        	
          <!-- FOOTER -->
          <?php include "../includes/footer.inc.php"; ?>
          </div>
        </div>
      </div>
    </div>
    <!-- JS -->
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../assets/vendors/chart.js/Chart.min.js"></script>
    <script src="../assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/misc.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/dashboard.js"></script>

	<script>
		document.getElementById("character_name").addEventListener("keypress", function(evt){
			var words = this.value.split(/\s+/);
			var numWords = words.length;
			var maxWords = <?php echo CHARACTER_NAME_WORDS_LIMIT; ?>;

			if(numWords > maxWords){
			evt.preventDefault();
			}
		});
	</script>
  </body>
</html>