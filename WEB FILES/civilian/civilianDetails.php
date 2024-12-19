<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();

$_SESSION["characterID"] = $_GET["ID"];
$characterID = $_SESSION["characterID"];
$user_discordid = $_SESSION['user_discordid'];
$_SESSION['redirect'] = "/civilian/civilianDetails.php";

if ($_SESSION['civilianperms'] != 1)
{
	header('Location: ../index.php?notCivilian');
}

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

if (is_numeric($characterID))
{
	$result = $pdo->query("SELECT * FROM characters WHERE ID='$characterID'");
}

foreach ($result as $row)
{
	$character_discordid = $row['discordid'];

	$character_name = $row['name'];
	$character_dob = $row['dob'];
	$character_haircolor = $row['haircolor'];
	$character_address = $row['address'];
	$character_gender = $row['gender'];
	$character_number = $row['number'];
	$character_race = $row['race'];
	$character_build = $row['build'];
	$character_occupation = $row['occupation'];
	$character_image = $row['image'];
	$dead = $row['dead'];

	$character_height = $row['height'];
	$character_ssn = $row['ssn'];

	$drivers = $row['drivers'];
	$weapons = $row['weapons'];
	$hunting = $row['hunting'];
	$fishing = $row['fishing'];
	$commercial = $row['commercial'];
	$boating = $row['boating'];
	$aviation = $row['aviation'];
	$driverspoints = $row['driverspoints'];

	$bloodtype = $row['bloodtype'];
	$emergcontact = $row['emergcontact'];
	$allergies = $row['allergies'];
	$medication = $row['medication'];
	$organdonor = $row['organdonor'];
}

if ($user_discordid != $character_discordid)
{
	header('Location: civilianDashboard.php?notYourCharacter');
}

// ACTION NOTIFICATIONS
if(isset($_GET['duplicatePlate']))
{
  $actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Someone has already taken that plate, please choose a different one!</div>';
}

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

if (is_numeric($characterID))
{
	$result2 = $pdo->query("SELECT * FROM vehicles WHERE charid='$characterID'");

	$result3 = $pdo->query("SELECT * FROM weapons WHERE charid='$characterID'");

	$result4 = $pdo->query("SELECT * FROM warnings WHERE civid='$characterID'");

	$result5 = $pdo->query("SELECT * FROM citations WHERE civid='$characterID'");

	$result6 = $pdo->query("SELECT * FROM arrests WHERE civid='$characterID'");

	$result7 = $pdo->query("SELECT * FROM warrants WHERE civid='$characterID'");

	$result8 = $pdo->query("SELECT * FROM medicalrecords WHERE civid='$characterID'");
}

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
  <script type="text/javascript" src="../assets/js/jquery.min.js"></script>
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
        <!-- ACTION DISPLAY -->
        <?php if($actionMessage){echo $actionMessage;} ?>
          	<div class="row">
				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-2 text-center">Character Details</h3><br>
	                    	<div class="text-center mb-4">
	                    		<img src="<?php echo $character_image; ?>" alt="Image" style="border-radius: 100px; width: 20%;">
	                		</div>
 						    <form class="forms-sample" action="../actions/civ_functions.php"  method="post" enctype="multipart/form-data">
 						    	<div class="row">
							        <div class="form-group col-md-4">
							            <label for="character_name">Name</label>
							            <input type="text" class="form-control p-input" id="character_name" name="character_name" value="<?php echo $character_name; ?>" readonly>
							        </div>
							        <div class="form-group col-md-4">
							            <label for="character_dob">Date Of Birth</label>
							            <input type="date" class="form-control p-input" id="character_dob" name="character_dob" value="<?php echo $character_dob; ?>" disabled>
							        </div>
							        <div class="form-group col-md-4">
							            <label for="character_snn">SSN</label>
							            <input type="text" class="form-control p-input" id="character_snn" name="character_snn" value="<?php echo $character_ssn; ?>" disabled>
							        </div>
						    	</div>
						    	<div class="row">
							        <div class="form-group col-md-4">
							            <label for="character_haircolor">Hair Color</label>
							            <input type="text" class="form-control p-input" id="character_haircolor" name="character_haircolor" value="<?php echo $character_haircolor; ?>" required>
							        </div>
							        <div class="form-group col-md-4">
							            <label for="character_address">Address</label>
							            <input type="text" class="form-control p-input" id="character_address" name="character_address" value="<?php echo $character_address; ?>" required>
							        </div>
							        <div class="form-group col-md-4">
							            <label for="character_occupation">Occupation</label>
							            <input type="text" class="form-control p-input" id="character_occupation" name="character_occupation" value="<?php echo $character_occupation; ?>">
							        </div>
							    </div>
							    <div class="row">
							        <div class="form-group col-md">
							            <label for="character_image">Character Image</label>
							            <input type="file" class="form-control p-input" name="character_image">
							        </div>
									<?php
									if (CIV_PHONE_NUMBERS == true) {
									?>
									<div class="form-group col-md">
							            <label>Ph. Number</label>
							            <input type="text" class="form-control p-input" id="character_number" name="character_number" value="<?php echo $character_number; ?>" autocomplete="off">
							        </div>
									<?php
									}
									?>
							        <div class="form-group col-md">
							            <label for="character_gender">Gender</label>
							            <select class="form-control p-input" id="character_gender" name="character_gender" style="color: white;" required>
							            	<?php
							            		if ($character_gender == "Male")
							            		{
							            			echo '<option value="Male">Male</option>
							            				<option value="Female">Female</option>
							            				<option value="Other">Other</option>';
							            		} 
							            		else if ($character_gender == "Female") 
							            		{

							            			echo '<option value="Female">Female</option>
							            				<option value="Male">Male</option>
							            				<option value="Other">Other</option>';
							            		}
							            		else if ($character_gender == "Other") 
							            		{

							            			echo '<option value="Other">Other</option>
							            				<option value="Male">Male</option>
							            				<option value="Female">Female</option>';
							            		}
							            	?>
							            </select>
							        </div>
							    </div>
							    <div class="row">
							        <div class="form-group col-md-6">
							            <label for="character_race">Race</label>
							            <select class="form-control p-input" id="character_race" name="character_race" style="color: white;" disabled>
							            	<option value="<?php echo $character_race; ?>"><?php echo $character_race; ?></option>
							            </select>
							        </div>
							        <div class="form-group col-md-6">
							            <label for="character_build">Build</label>
							            <select class="form-control p-input" id="character_build" name="character_build" style="color: white;" required>
							            	<option value="<?php echo $character_build; ?>"><?php echo $character_build; ?></option>
							            	<option value="Average">Average</option>
							            	<option value="Fit">Fit</option>
							            	<option value="Muscular">Muscular</option>
							            	<option value="Overweight">Overweight</option>
							            	<option value="Skinny">Skinny</option>
							            	<option value="Thin">Thin</option>
							            </select>
							        </div>
							    </div>
							    <div class="text-center">
						        <?php
						        if ($dead != 1)
						        {
						        ?>
						        <button type="submit" name="update_character_btn" class="btn btn-outline-info m-2">Update</button>
						        <p class="btn btn-outline-danger m-2" data-toggle="modal" data-target="#markDeadModal">Mark Dead</p>
						        <?php 
						        } else {
						        ?>
						        <p class="btn btn-outline-danger m-2">CHARACTER IS DEAD</p>
						        <?php
						    	}
						        ?>
						        <p class="btn btn-outline-danger m-2" data-toggle="modal" data-target="#deleteCharacterModal">Delete Character</p>
						    	</div>
						    </form>
	                  </div>
	                </div>
	            </div>

				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">License Details</h3>
							<span id="licensesuccess"></span><br><br>
							    <div class="row">
							        <div class="form-group col-md-6">
							            <label for="license_type">License Type</label>
							            <select class="form-control p-input" id="license_type" name="license_type" style="color: white;" required>
							            	<option value="-">-</option>
							            	<option value="drivers" <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo "disabled";}?>>Drivers Permit <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo " - Contact DMV";}?></option>
							            	<option value="weapons">Weapons Permit</option>
							            	<option value="hunting">Hunting Permit</option>
							            	<option value="fishing">Fishing Permit</option>
							            	<option value="commercial" <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo "disabled";}?>>Commercial Permit <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo " - Contact DMV";}?></option>
							            	<option value="boating" <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo "disabled";}?>>Boating Permit <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo " - Contact DMV";}?></option>
							            	<option value="aviation" <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo "disabled";}?>>Aviation Permit <?php if (DISABLE_CIV_LICENSE_EDIT == 1) {echo " - Contact DMV";}?></option>
							            </select>
							        </div>
							        <div class="form-group col-md-6">
							            <label for="license_status">License Status</label>
							            <select class="form-control p-input" id="license_status" name="license_status" style="color: white;" required>
							            	<option value="-">-</option>
							            	<option value="Unobtained">Unobtained</option>
							            	<option value="Valid">Valid</option>
							            	<option value="Invalid">Invalid</option>
							            	<option value="Revoked">Revoked</option>
							            	<option value="Suspended">Suspended</option>
							            </select>
							        </div>
							    </div>
							    <div class="text-center">
						        <button type="submit" onclick="updateLicense()" class="btn btn-outline-info m-2">Update</button>
						    	</div>
						    <br>
						    <div class="table-responsive" id="vehiclesection">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th title="Points: <?php echo $driverspoints; ?>">Drivers</th>
		                              <th>Weapons</th>
		                              <th>Hunting</th>
		                              <th>Fishing</th>
		                              <th>Commercial</th>
		                              <th>Boating</th>
		                              <th>Aviation</th>
		                            </tr>
		                          </thead>
		                          <tbody id="getlicense">
	                	          <script type="text/javascript">
	        		                  	$('#getlicense').load('getlicense.php');
						          	var auto_refresh = setInterval( function () {
						        		$('#getlicense').load('getlicense.php');
						        		document.getElementById("licensesuccess").innerHTML = '';
							      	}, 1000);
								  </script>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>
          	</div>
          	<br>
          	<br>
          	<div class="row">
				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-2 text-center">Add Vehicle</h3><br>
 						    <form class="forms-sample" action="../actions/civ_functions.php"  method="post">
 						    	<div class="row">
							        <div class="form-group col-md-6">
							            <label for="vehicle_plate">Plate</label>
							            <input type="text" class="form-control p-input" id="vehicle_plate" name="vehicle_plate" required>
							        </div>
							        <div class="form-group col-md-6">
							            <label for="vehicle_model">Make & Model</label>
							            <input type="text" class="form-control p-input" id="vehicle_model" name="vehicle_model" required>
							        </div>
						    	</div>
						    	<div class="row">
							        <div class="form-group col-md-6">
							            <label for="vehicle_color">Color</label>
							            <input type="text" class="form-control p-input" id="vehicle_color" name="vehicle_color" required>
							        </div>
							        <div class="form-group col-md-6">
							            <label for="vehicle_insurance">Insurance</label>
							            <select class="form-control p-input" id="vehicle_insurance" name="vehicle_insurance" style="color: white;" required>
							            	<option value="Valid">Valid</option>
							            	<option value="Expired">Expired</option>
							            	<option value="Canceled">Canceled</option>
							            	<option value="Suspended">Suspended</option>
							            	<option value="Unknown">Unknown</option>
							            </select>
							        </div>
							    </div>
							    <div class="row">
							        <div class="form-group col-md-6">
							            <label for="vehicle_regstate">Registered State</label>
							            <select class="form-control p-input" id="vehicle_regstate" name="vehicle_regstate" style="color: white;" required>
							            	<option value="Los Santos">Los Santos</option>
							            	<option value="Blaine County">Blaine County</option>
							            	<option value="San Andreas">San Andreas</option>
							            </select>
							        </div>
							        <div class="form-group col-md-6">
							            <label for="vehicle_flags">Flags</label>
							            <select class="form-control p-input" id="vehicle_flags" name="vehicle_flags" style="color: white;" required>
							            	<option value="None">None</option>
							            	<option value="Stolen">Stolen</option>
							            	<option value="Wanted">Wanted</option>
							            	<option value="Suspended Reg">Suspended Reg</option>
							            	<option value="Canceled Reg">Cancelled Reg</option>
							            	<option value="Expired Reg">Expired Reg</option>
							            	<option value="Insurance Reg">Insurance Flag</option>
							            	<option value="Driver Flag">Driver Flag</option>
							            	<option value="No Insurance">No Insurance</option>
							            </select>
							        </div>
							    </div>
							    <div class="text-center">
						        	<button type="submit" name="add_vehicle_btn" class="btn btn-outline-info mb-4 mt-3">Add</button>
						    	</div>
						    </form>
	                  </div>
	                </div>
	            </div>

				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Vehicle Details</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>Plate</th>
		                              <th>Make & Model</th>
		                              <th>Color</th>
		                              <th>Insurance</th>
		                              <th>Registered State</th>
		                              <th>Flags</th>
		                              <th></th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result2 as $row)
		                          		{
							              $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					                      $charactersLength = strlen($characters);
					                      $modalstr = '';
					                      for ($i = 0; $i < 10; $i++) {
					                          $modalstr .= $characters[rand(0, $charactersLength - 1)];
					                          $idstr .= $characters[rand(0, $charactersLength - 1)];
					                          $idstr2 .= $characters[rand(0, $charactersLength - 1)];
					                          $idstr3 .= $characters[rand(0, $charactersLength - 1)];
											  $idstr4 .= $characters[rand(0, $charactersLength - 1)];
					                      }
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['plate']; ?></td>
	                            			    <td><?php echo $row['makemodel']; ?></td>
	                            			    <td><?php echo $row['color']; ?></td>
	                            			    <td style="color: <?php echo  getColor($row['insurance']); ?>"><?php echo $row['insurance']; ?></td>
	                            			    <td><?php echo $row['regstate']; ?></td>
	                            			    <td><?php echo $row['flags']; ?></td>
	                            			    <td><p class="btn btn-outline-info m-2" data-toggle="modal" data-target="#<?php echo $modalstr; ?>">Edit</p></td>
	                            			 </tr>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="<?php echo $modalstr; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Vehicle Information - <code style="color: grey;"><?php echo $row['vin']; ?></code></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <div id="vehiclesuccess"></div>
        <?php 
        echo '<b>Insurance:</b><br>';
        echo '<div style="margin-bottom: 10px;"></div>';
        echo '<select class="form-control p-input" id="'.$idstr.'" style="color: white;" required>
          	    <option value="'.$row['insurance'].'">'.$row['insurance'].'</option>
            	<option value="Valid">Valid</option>
            	<option value="Expired">Expired</option>
            	<option value="Canceled">Canceled</option>
            	<option value="Suspended">Suspended</option>
            	<option value="Unknown">Unknown</option>
              </select>';

        echo '<br><b>Flags:</b><br>';
        echo '<div style="margin-bottom: 10px;"></div>';
        echo '<select class="form-control p-input" id="'.$idstr2.'" style="color: white;" required>
          	    <option value="'.$row['flags'].'">'.$row['flags'].'</option>
            	<option value="None">None</option>
            	<option value="Stolen">Stolen</option>
            	<option value="Wanted">Wanted</option>
            	<option value="Suspended Reg">Suspended Reg</option>
            	<option value="Canceled Reg">Cancelled Reg</option>
            	<option value="Expired Reg">Expired Reg</option>
            	<option value="Insurance Reg">Insurance Flag</option>
            	<option value="Driver Flag">Driver Flag</option>
            	<option value="No Insurance">No Insurance</option>
              </select>';

		echo '<div class="row">';
		if (ALLOW_CIV_EDIT_VEHICLE_PLATE == true) {
			echo '<div class="form-group col-md-6">';
			echo '<br><b>Vehicle Plate:</b><br>';
			echo '<div style="margin-bottom: 10px;"></div>';
			echo '<input type="text" style="color: white;" class="form-control p-input" id="'.$idstr4.'" value="'.$row['plate'].'"></input>';
			echo '</div>';
		} else {
			echo '<input type="text" style="color: white;" class="form-control p-input" id="'.$idstr4.'" value="'.$row['plate'].'" hidden></input>';
		}
		echo '<div class="form-group col-md-6">';
        echo '<br><b>Vehicle Color:</b><br>';
        echo '<div style="margin-bottom: 10px;"></div>';
        echo '<input type="text" style="color: white;" class="form-control p-input" id="'.$idstr3.'" value="'.$row['color'].'"></input>';
		echo '</div>';
		echo '</div>';

        ?>
        <br><br>
          <button type="button" onclick="updateVehicle(<?php echo $row['ID']; ?>, '<?php echo $idstr; ?>', '<?php echo $idstr2; ?>', '<?php echo $idstr3; ?>', '<?php echo $idstr4; ?>')" class="btn btn-outline-info">Update</button>
          <span class="p-2"></span>
          <span onClick="location.href='<?php echo BASE_URL; ?>/actions/civ_functions.php?vehicleID=<?php echo $row['ID'] ?>'" class="btn btn-outline-danger">Delete</span>
      </div>
    </div>
  </div>
</div>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>
          	</div>
          	<br>
          	<br>
          	<div class="row">
				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-2 text-center">Add Weapon</h3><br>
 						    <form class="forms-sample" action="../actions/civ_functions.php"  method="post">
 						    	<div class="row">
							        <div class="form-group col-md-6">
							            <label for="weapon_type">Weapon Type</label>
							            <input type="text" class="form-control p-input" id="weapon_type" name="weapon_type" placeholder="Eg. Handgun" required>
							        </div>
							        <div class="form-group col-md-6">
							            <label for="weapon_name">Weapon Name</label>
							            <input type="text" class="form-control p-input" id="weapon_name" name="weapon_name" placeholder="Eg. Glock 19" required>
							        </div>
						    	</div>
						    	<!-- <div class="row">
							        <div class="form-group col-md-12">
							            <label>Serial Number</label>
							            <input type="text" class="form-control p-input" placeholder="AUTO GENERATED" disabled>
							        </div>
							    </div> -->
							    <div class="text-center">
						        	<button type="submit" name="add_weapon_btn" class="btn btn-outline-info mb-4 mt-3">Add</button>
						    	</div>
						    </form>
	                  </div>
	                </div>
	            </div>

				<div class="col-xl-6">
	                <div class="card" id="weaponsection">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Weapon Details</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>Weapon Type</th>
		                              <th>Weapon Name</th>
		                              <th>Serial Number</th>
		                              <th></th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result3 as $row)
		                          		{
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['type']; ?></td>
	                            			    <td><?php echo $row['name']; ?></td>
	                            			    <td><?php echo $row['serialnumber']; ?></td>
	                            			    <td><span onClick="location.href='<?php echo BASE_URL; ?>/actions/civ_functions.php?weaponID=<?php echo $row['ID'] ?>'" class="btn btn-outline-danger">Delete</span></td>
	                            			  </tr>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>
          	</div>
          	<br>
          	<br>
          	<div class="row">
				<div class="col-xl-6">
	                <div class="card" id="medicalsection">
	                  <div class="card-body">
	                    <h3 class="mb-2 text-center">Update Medical Information</h3><br>
 						    <form class="forms-sample" action="../actions/civ_functions.php"  method="post">
 						    	<div class="row">
							        <div class="form-group col-md-4">
							            <label for="medical_bloodtype">Blood Type</label>
							            <select class="form-control p-input" id="medical_bloodtype" name="medical_bloodtype" style="color: white;" required>
							            	<option value="<?php echo $bloodtype; ?>"><?php echo $bloodtype; ?></option>
										    <option value="A Positive">A Positive</option>
										    <option value="A Negative">A Negative</option>
										    <option value="A Unknown">A Unknown</option>
										    <option value="B Positive">B Positive</option>
										    <option value="B Negative">B Negative</option>
										    <option value="B Unknown">B Unknown</option>
										    <option value="AB Positive">AB Positive</option>
										    <option value="AB Negative">AB Negative</option>
										    <option value="AB Unknown">AB Unknown</option>
										    <option value="O Positive">O Positive</option>
										    <option value="O Negative">O Negative</option>
										    <option value="O Unknown">O Unknown</option>
										    <option value="Unknown">Unknown</option>
							            </select>
							        </div>
							        <div class="form-group col-md-4">
							            <label for="medical_organdonor">Organ Donor?</label>
							            <select class="form-control p-input" id="medical_organdonor" name="medical_organdonor" style="color: white;" required>
							            	<option value="<?php echo $organdonor; ?>"><?php if ($organdonor == "1") {echo "Yes";} else {echo "No";} ?></option>
										    <option value="1">Yes</option>
										    <option value="0">No</option>
							            </select>
							        </div>
							        <div class="form-group col-md-4">
							            <label for="medical_emergency_contact">Emergency Contact</label>
							            <input type="text" class="form-control p-input" id="medical_emergency_contact" name="medical_emergency_contact" value="<?php echo $emergcontact; ?>" required>
							        </div>
						    	</div>
						    	<div class="row">
							        <div class="form-group col-md-6">
							            <label for="medical_allergies">Allergies</label>
							            <input type="text" class="form-control p-input" id="medical_allergies" name="medical_allergies" value="<?php echo $allergies; ?>">
							        </div>
							        <div class="form-group col-md-6">
							            <label for="medical_medication">Medication</label>
							            <input type="text" class="form-control p-input" id="medical_medication" name="medical_medication" value="<?php echo $medication; ?>">
							        </div>
							    </div>
							    <div class="text-center">
						        	<button type="submit" name="update_medical_btn" class="btn btn-outline-info mb-4 mt-3">Update</button>
						    	</div>
						    </form>
	                  </div>
	                </div>
	            </div>

				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Medical Reports</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>#</th>
		                              <th>Details</th>
		                              <th>Written By</th>
		                              <th>Date & Time</th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result8 as $row)
		                          		{
		                          			$datetime = $row['date'] . " | " . $row['time'];
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['ID']; ?></td>
	                            			    <td><?php echo $row['details']; ?></td>
	                            			    <td><?php echo $row['unitidentifier']; ?></td>
	                            			    <td><?php echo $datetime; ?></td>
	                            			</tr>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>
          	</div>
          	<br>
          	<br>
          	<div class="row">
				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Warnings</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>#</th>
		                              <th>Details</th>
		                              <th>Date & Time</th>
		                              <th>Unit</th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result4 as $row)
		                          		{
		                          			$datetime = $row['date'] . " | " . $row['time'];
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['ID']; ?></td>
	                            			    <td><?php echo $row['offences']; ?></td>
	                            			    <td><?php echo $datetime; ?></td>
	                            			    <td><?php echo $row['unitidentifier']; ?></td>
	                            			</tr>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>

				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Citations</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>#</th>
		                              <th>Details</th>
		                              <th>Fine</th>
		                              <th>Date & Time</th>
		                              <th>Unit</th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result5 as $row)
		                          		{
		                          			$datetime = $row['date'] . " | " . $row['time'];
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['ID']; ?></td>
	                            			    <td><?php echo $row['offences']; ?></td>
	                            			    <td><?php echo $row['fine']; ?></td>
	                            			    <td><?php echo $datetime; ?></td>
	                            			    <td><?php echo $row['unitidentifier']; ?></td>
	                            			</tr>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>
          	</div>
          	<br>
          	<br>
          	<div class="row mb-5">
				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Arrests</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>#</th>
		                              <th>Details</th>
		                              <th>Fine</th>
		                              <th>Jail Time</th>
		                              <th>Date & Time</th>
		                              <th>Unit</th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result6 as $row)
		                          		{
		                          			$datetime = $row['date'] . " | " . $row['time'];
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['ID']; ?></td>
	                            			    <td><?php echo $row['reason']; ?></td>
	                            			    <td><?php echo $row['fine']; ?></td>
	                            			    <td><?php echo $row['jailtime']; ?> Seconds</td>
	                            			    <td><?php echo $datetime; ?></td>
	                            			    <td><?php echo $row['unitidentifier']; ?></td>
	                            			</tr>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>

				<div class="col-xl-6">
	                <div class="card">
	                  <div class="card-body">
	                    <h3 class="mb-3 text-center">Your Warrants</h3>
						    <br>
						    <div class="table-responsive">
		                        <table class="table text-center">
		                          <thead>
		                            <tr>
		                              <th>#</th>
		                              <th>Details</th>
		                              <th>Date & Time</th>
		                              <th>Unit</th>
		                            </tr>
		                          </thead>
		                          <tbody style="color: white;">
		                          	<?php
		                          		foreach ($result7 as $row)
		                          		{
		                          			$datetime = $row['date'] . " | " . $row['time'];
		                          	?>
	                          				<tr>
	                            			    <td><?php echo $row['ID']; ?></td>
	                            			    <td><?php echo $row['details']; ?></td>
	                            			    <td><?php echo $datetime; ?></td>
	                            			    <td><?php echo $row['unitidentifier']; ?></td>
	                            			</tr>
		                            <?php
		                          		}
		                          	?>
		                          </tbody>
		                        </table>
	                    	</div>
	                  </div>
	                </div>
	            </div>
          	</div>

          <!-- FOOTER -->
          <?php include "../includes/footer.inc.php"; ?>
          </div>
        </div>
      </div>
    </div>

<!-- Mark Dead Modal -->
<div class="modal fade" id="markDeadModal" tabindex="-1" role="dialog" aria-labelledby="markDeadModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="markDeadModal">Mark Dead</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" action="../actions/civ_functions.php" method="POST">
            <div class="form-group">
                <label for="character_id">Are you sure? (This cannot be undone!)</label>
            </div>
            <button type="submit" name="markdead_btn" class="btn btn-outline-danger">Mark Dead</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Character Modal -->
<div class="modal fade" id="deleteCharacterModal" tabindex="-1" role="dialog" aria-labelledby="deleteCharacterModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteCharacterModal">Delete Character</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" action="../actions/civ_functions.php" method="POST">
            <div class="form-group">
                <label for="character_id">Are you sure? (This cannot be undone!)</label>
            </div>
            <button type="submit" name="delete_character_btn" class="btn btn-outline-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
	function updateLicense() {
		var license_type = document.getElementById("license_type");
		var licensetype = license_type.value;
		var license_status = document.getElementById("license_status");
		var licensestatus = license_status.value;

		$.ajax({
			type : "POST",
			url  : "../actions/civ_functions.php",
			data : { licensetype : licensetype, licensestatus : licensestatus },
			success: function(res){  
						// success
						$('#licensesuccess').html(res); 
					}
		});

	}

	function updateVehicle(vehicleid, insuranceid, flagid, colorid, plate) {
		var insurance_edit = document.getElementById(insuranceid);
		var insuranceedit = insurance_edit.value;
		var flag_edit = document.getElementById(flagid);
		var flagedit = flag_edit.value;
		flagnospace = flagedit.replace(/\s+/g, '-');
		var color_edit = document.getElementById(colorid);
		var coloredit = color_edit.value;
		colornospace = coloredit.replace(/\s+/g, '-');
		var plate_edit = document.getElementById(plate);
		var plateedit = plate_edit.value;
		platenospace = plateedit.replace(/\s+/g, '-');

		$.ajax({
			type : "POST",
			url  : "../actions/civ_functions.php",
			data : { editvehicle : vehicleid, insurance : insuranceedit, flag : flagnospace, color : colornospace, plate : platenospace },
			success: function(res){  
						// success
						$('#vehiclesuccess').html(res); 
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