<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();
checkBan();
$user_discordid = $_SESSION['user_discordid'];
$_SESSION['redirect'] = "/dmv/dmvDashboard.php";
$_SESSION['nameq'] = "";

if ($_SESSION['dmvperms'] != 1 || DMV_SYSTEM != 1)
{
	header('Location: ../index.php?notAuthorisedDepartment');
}

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$resetstatus = $pdo->query("UPDATE users SET currstatus='10-7', currdivision='None', currdept='None' WHERE discordid='$user_discordid'");

function getColor($status)
{
	if ($status == "Valid")
	{
	  return "green";
	} 
	else if ($status == "Unobtained")
	{
	  return "#F0BF48";
	}
	else if ($status == "Invalid")
	{
	  return "#F0BF48";
	}
	else if ($status == "Unknown")
	{
	  return "#F0BF48";
	}
	else
	{
	  return "red";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo SERVER_SHORT_NAME; ?> CAD | DMV</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
  </head>
  <script type="text/javascript" src="../assets/js/jquery.min.js"></script>
  <body id="modalCheck">
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
        <!-- ACTION DISPLAY -->
        <?php if($actionMessage){echo $actionMessage;} ?>
          	<div class="row">
          		<div class="col-xl-3"></div>
				<div class="col-xl-6">
	                <div class="card" id="searched">
	                  <div class="card-body text-center">
	                    <h3 class="mt-2 mb-1 text-center">Search License</h3><br>
 						    <form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
						        <div class="form-group">
						            <input type="text" class="form-control p-input" placeholder="Enter Name" onkeyup="showResult(this.value, 'NAME')" style="color: white;">
						        </div>
						        <div id="namesearch"></div>
						    </form>
	                  </div>
	                </div>
	                <br>
	                <?php
						if (isset($_GET['nameq']))
						{
						    $nameq = htmlspecialchars($_GET['nameq']);
						    $_SESSION['nameq'] = $nameq;
						    $character = $pdo->query("SELECT * FROM characters WHERE ID='$nameq'");

						    foreach ($character as $row)
							{
								$character_id = $row['ID'];
								$character_discordid = $row['discordid'];
								$character_name = $row['name'];
								$character_dob = $row['dob'];
								$character_address = $row['address'];
								$character_gender = $row['gender'];
								$character_image = $row['image'];

								$drivers = $row['drivers'];
								$commercial = $row['commercial'];
								$boating = $row['boating'];
								$aviation = $row['aviation'];
								$driverspoints = $row['driverspoints'];

								$organdonor = $row['organdonor'];
							}

							$regvehicle = $pdo->query("SELECT * FROM vehicles WHERE charid='$character_id' ORDER BY ID DESC");

							if ($character_discordid == $user_discordid) {
								echo "<p style='text-align: center; color: orange;'>You can't edit your own license!</p>";
							} else {
					?>
	                <div class="card">
	                  <div class="card-body">
	                  	<div style="font-size: 20px; text-align: right;">
	                  		<a style="text-decoration: none; color: white;" href="dmvDashboard.php"><span>&times;</span></a>
	                  	</div><br>
	                  	<div class="row">
	                  		<div class="col-md-6">
			                  	<h5>Name: <span class="text-muted"><?php echo $character_name; ?></span></h5>
			                  	<h5>DOB: <span class="text-muted"><?php echo $character_dob; ?></span></h5>
			                  	<h5>Address: <span class="text-muted"><?php echo $character_address; ?></span></h5>
			                  	<h5>Gender: <span class="text-muted"><?php echo $character_gender; ?></span></h5>
			                  	<?php
			                  	if ($organdonor == "1")
			                  	{
			                  	?>
			                  	<h5 style="color: #f55f82;">DONOR</h5>
			                  	<?php
			                  	}
			                  	?>
	                  		</div>
	                  		<div class="col-md-6">
			                  	<div class="text-center">
			                  		<img src="<?php echo $character_image; ?>" alt="Image" style="border-radius: 100px; width: 25%;">
			                  	</div>
	                  		</div>
	                  	</div>
	                  	<br>
	                  	<div class="row">
	                  		<div class="col-md-5">
			                  	<h5>Licenses:</h5>
			                  	<span class="text-muted">Drivers: <span style="color: <?php echo  getColor($drivers); ?>;"><?php echo $drivers; ?></span></span><br>
			                  	<span class="text-muted">Commercial: <span style="color: <?php echo  getColor($commercial); ?>;"><?php echo $commercial; ?></span></span><br>
			                  	<span class="text-muted">Boating: <span style="color: <?php echo  getColor($boating); ?>;"><?php echo $boating; ?></span></span><br>
			                  	<span class="text-muted">Aviation: <span style="color: <?php echo  getColor($aviation); ?>;"><?php echo $aviation; ?></span></span><br>
	                 	 	</div>
	                 	 	<div class="col-md-6 ml-2">
								<h5>Update Licenses</h5><span class="text-success" id="licensesuccess"></span>
	                 	 		<div class="row">
	                 	 		<div class="col-md-6">
								<div class="form-group">
									<span class="text-muted">Type</span>
						            <select class="form-control p-input" id="license_type"style="color: white;" required>
						            	<option value="-">-</option>
						            	<option value="drivers">Drivers Permit</option>
						            	<option value="commercial">Commercial Permit</option>
						            	<option value="boating">Boating Permit</option>
						            	<option value="aviation">Aviation Permit</option>
						            </select>
								</div>
								</div>
								<div class="col-md-6">
								<div class="form-group">
									<span class="text-muted">Status</span>
						            <select class="form-control p-input" id="license_status"style="color: white;" required>
						            	<option value="-">-</option>
						            	<option value="Unobtained">Unobtained</option>
						            	<option value="Valid">Valid</option>
						            	<option value="Invalid">Invalid</option>
						            	<option value="Revoked">Revoked</option>
						            	<option value="Suspended">Suspended</option>
						            </select>
								</div>
								</div>
								</div>
					            <button type="submit" onclick="updateSearchedLicense(<?php echo $character_id; ?>)" class="btn btn-outline-info">Update</button>
	                 	 	</div>
	                  	</div>
	                  	<h5>Drivers Points: <span class="text-muted"><?php echo $driverspoints; ?></span></h5>
	                  	<br>
	                  	<h5>Registered Vehicles:</h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
							foreach ($regvehicle as $row)
							{
								$regvehicle_plate = $row['plate'];
	                  	?>
								<span class="text-muted"> | <a style="color: #00B9FF;" href="?nameq=<?php echo $nameq; ?>&plateq=<?php echo $row['ID']; ?>#searched"><?php echo $regvehicle_plate; ?></a> | </span> 
	                  	<?php
							}

							if ($regvehicle_plate == "")
							{
								echo '<span class="text-muted">None</span><br>';
							} else {
								echo "<br>";
							}
	                  	?>
	                  	<span class="text-muted">=====================================</span>
	                  	<br>
	                  </div>
	                </div>
	                <br>
					<?php
							}
						}
	
						if (isset($_GET['plateq']))
						{
						    $plateq = htmlspecialchars($_GET['plateq']);
						    $_SESSION['plateq'] = $plateq;
						    $vehicles = $pdo->query("SELECT * FROM vehicles WHERE ID='$plateq'");

						    foreach ($vehicles as $row)
							{
								$vehicle_id = $row['ID'];
								$vehicle_charid = $row['charid'];
								$vehicle_plate = $row['plate'];
								$vehicle_makemodel = $row['makemodel'];
								$vehicle_color = $row['color'];
								$vehicle_insurance = $row['insurance'];
								$vehicle_regstate = $row['regstate'];
								$vehicle_flags = $row['flags'];

						    	$owner = $pdo->query("SELECT * FROM characters WHERE ID='$vehicle_charid'");

						    	foreach ($owner as $row2)
						    	{
						    		$owner_name = $row2['name'];
						    	}

							}
					?>
	                <div class="card">
	                  <div class="card-body">
	                  	<div style="font-size: 20px; text-align: right;">
	                  		<a style="text-decoration: none; color: white; right: 0;" href="courtDashboard.php"><span aria-label="Close">&times;</span></a><br><br>
	                  	</div>
	                  	<h5>Registered Owner: <span class="text-muted"><a style="color: #00B9FF;" href="?nameq=<?php echo $vehicle_charid; ?>&plateq=<?php echo $plateq; ?>#searched"><?php echo $owner_name; ?></a></span></h5>
	                  	<h5>Plate: <span class="text-muted"><?php echo $vehicle_plate; ?></span></h5>
	                  	<h5>Make & Model: <span class="text-muted"><?php echo $vehicle_makemodel; ?></span></h5>
	                  	<h5>Color: <span class="text-muted"><?php echo $vehicle_color; ?></span></h5>
	                  	<h5>Insurance: <span style="color: <?php echo  getColor($vehicle_insurance); ?>;"><?php echo $vehicle_insurance; ?></span></h5>
	                  	<h5>Registered State: <span class="text-muted"><?php echo $vehicle_regstate; ?></span></h5>
	                  	<h5>Flags: <span class="<?php if($vehicle_flags=="None"){echo"text-muted";}else{echo"text-danger";} ?>"><?php echo $vehicle_flags; ?></span></h5>
	                  </div>
	                </div>
					<?php
						}
	                ?>
	            </div>
          	</div>
          	<br>
          	<br>
          <!-- FOOTER -->
          <?php include "../includes/footer.inc.php"; ?>
          </div>
        </div>
      </div>
    </div>
	<script>
		function showResult(str, type) {
		if (str.length==0) {
			if (type == 'NAME')
			{
				document.getElementById("namesearch").innerHTML="";
			}
			return;
		}
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {  // code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (type == 'NAME')
				{
					document.getElementById("namesearch").innerHTML = this.responseText;
				}
			}
		}

		if (type == 'NAME')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?nc=" + str, true);
			xmlhttp.send();
		}

		}

		function updateSearchedLicense(id) {
			var license_type = document.getElementById("license_type");
			var licensetype = license_type.value;
			var license_status = document.getElementById("license_status");
			var licensestatus = license_status.value;
			$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { courtlicenseid : id, courtlicensetype : licensetype, courtlicensestatus : licensestatus},
				success: function(res){  
							// success
							$('#licensesuccess').html(res); 
							location.reload();
						}
			});
		}

	</script>
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
  </body>
</html>