<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();
checkBan();
$user_discordid = $_SESSION['user_discordid'];
$_SESSION['redirect'] = "/court/courtDashboard.php";
$_SESSION['plateq'] = "";
$_SESSION['nameq'] = "";
$_SESSION['weaponq'] = "";

if ($_SESSION['courtperms'] != 1)
{
	header('Location: ../index.php?notAuthorisedDepartment');
}

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$resetstatus = $pdo->query("UPDATE users SET currstatus='10-8', currdivision='None', currdept='None' WHERE discordid='$user_discordid'");

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
    <title><?php echo SERVER_SHORT_NAME; ?> CAD | Court</title>
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
				<div class="col-xl-4">
	                <div class="card" id="searched">
	                  <div class="card-body text-center">
	                    <h3 class="mt-2 mb-1 text-center">Name Check</h3><br>
 						    <form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
						        <div class="form-group">
						            <input type="text" class="form-control p-input" placeholder="Search Name" onkeyup="showResult(this.value, 'NAME')" style="color: white;">
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
								if ($dead == 1)
								{
									$deadstatus = "Dead";
									$deadcolor = "red";
								} else {
									$deadstatus = "Alive";
									$deadcolor = "green";
								}

								$character_ssn = $row['ssn'];

	                          	$bloodtype = $row['bloodtype'];
	                          	$emergcontact = $row['emergcontact'];
	                          	$allergies = $row['allergies'];
	                          	$medication = $row['medication'];
	                          	$organdonor = $row['organdonor'];

								$drivers = $row['drivers'];
								$weapons = $row['weapons'];
								$hunting = $row['hunting'];
								$fishing = $row['fishing'];
								$commercial = $row['commercial'];
								$boating = $row['boating'];
								$aviation = $row['aviation'];
								$driverspoints = $row['driverspoints'];

								$character_discordid = $row['discordid'];

								if (checkOnline('discord:'.$character_discordid) == true) {
									$player_ingame = true;
								}
							}

							$regvehicle = $pdo->query("SELECT * FROM vehicles WHERE charid='$character_id' ORDER BY ID DESC");

							$warnings = $pdo->query("SELECT * FROM warnings WHERE civid='$character_id' ORDER BY ID DESC");

							$citations = $pdo->query("SELECT * FROM citations WHERE civid='$character_id' ORDER BY ID DESC");

							$arrests = $pdo->query("SELECT * FROM arrests WHERE civid='$character_id' ORDER BY ID DESC");

							$warrants = $pdo->query("SELECT * FROM warrants WHERE civid='$character_id' ORDER BY ID DESC");

							$medicalrecords = $pdo->query("SELECT * FROM medicalrecords WHERE civid='$character_id' ORDER BY ID DESC");
					?>
	                <div class="card">
	                  <div class="card-body">
	                  	<div style="font-size: 20px; text-align: right;">
	                  		<a style="text-decoration: none; color: white;" href="courtDashboard.php"><span>&times;</span></a>
	                  	</div><br>
	                  	<div class="text-center">
	                  		<img src="<?php echo $character_image; ?>" alt="Image" style="border-radius: 100px; width: 20%; <?php if ($player_ingame == true) {echo "border: 3px solid #3ba55d;";} else {echo "border: 3px solid #747f8d;";} ?>">
	                  	</div>
	                  	<h5>Name: <span class="text-muted"><?php echo $character_name; ?></span></h5>
	                  	<h5>DOB: <span class="text-muted"><?php echo $character_dob; ?></span></h5>
	                  	<h5>SSN: <span class="text-muted"><?php echo $character_ssn; ?></span></h5>
	                  	<h5>Hair Color: <span class="text-muted"><?php echo $character_haircolor; ?></span></h5>
	                  	<h5>Address: <span class="text-muted"><?php echo $character_address; ?></span></h5>
	                  	<h5>Gender: <span class="text-muted"><?php echo $character_gender; ?></span></h5>
	                  	<h5>Race: <span class="text-muted"><?php echo $character_race; ?></span></h5>
	                  	<h5>Build: <span class="text-muted"><?php echo $character_build; ?></span></h5>
	                  	<h5>Occupation: <span class="text-muted"><?php echo $character_occupation; ?></span></h5>
						<?php
						if (CIV_PHONE_NUMBERS == true) { echo '<h5>Phone Number: <span class="text-muted">'.$character_number.'</span></h5>'; }
						?>
	                  	<h5 style="color: <?php echo $deadcolor; ?>;"><?php echo $deadstatus; ?></h5>
                      <br>
                      <h5>Medical Details:</h5>
                      <span>Blood Type: <span class="text-muted"><?php echo $bloodtype; ?></span></span><br>
                      <span>Emergency Contact: <span class="text-muted"><?php echo $emergcontact; ?></span></span><br>
                      <span>Allergies: <span class="text-muted"><?php echo $allergies; ?></span></span><br>
                      <span>Medication: <span class="text-muted"><?php echo $medication; ?></span></span><br>
	                  	<?php
	                  	if ($organdonor == "1")
	                  	{
	                  	?>
	                  	<h5 style="color: #f55f82;">DONOR</h5>
	                  	<?php
	                  	}
	                  	?>
	                  	<br>
	                  	<div class="row">
	                  		<div class="col-md-5">
			                  	<h5>Licenses:</h5>
			                  	<span class="text-muted">Drivers: <span style="color: <?php echo  getColor($drivers); ?>;"><?php echo $drivers; ?></span></span><br>
			                  	<span class="text-muted">Weapons: <span style="color: <?php echo  getColor($weapons); ?>;"><?php echo $weapons; ?></span></span><br>
			                  	<span class="text-muted">Hunting: <span style="color: <?php echo  getColor($hunting); ?>;"><?php echo $hunting; ?></span></span><br>
			                  	<span class="text-muted">Fishing: <span style="color: <?php echo  getColor($fishing); ?>;"><?php echo $fishing; ?></span></span><br>
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
						            	<option value="weapons">Weapons Permit</option>
						            	<option value="hunting">Hunting Permit</option>
						            	<option value="fishing">Fishing Permit</option>
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
	                  	<br>
	                  	<h5>Points: <span class="text-muted"><?php echo $driverspoints; ?></span></h5>
						<div class="form-group" style="width: 25%;">
				            <input type="number" class="form-control p-input" id="add_points" name="add_points">
						</div>
						<button type="submit" onclick="addDriversPoints(<?php echo $character_id; ?>)" class="btn btn-outline-info">Add</button>
						<button type="submit" onclick="removeDriversPoints(<?php echo $character_id; ?>)" class="btn btn-outline-danger ml-3">Remove</button>
	                  	<br>
	                  	<br>
	                  	<h5>Registered Vehicles Plate:</h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
							foreach ($regvehicle as $row)
							{
								$regvehicle_plate = $row['plate'];
	                  	?>
								<span class="text-muted"><a style="color: <?php echo ACCENT_COLOR; ?>;" href="?nameq=<?php echo $nameq; ?>&plateq=<?php echo $row['ID']; ?>#searched"><?php echo $regvehicle_plate; ?></a></span><br>
	                  	<?php
							}

							if ($regvehicle_plate == "")
							{
								echo '<span class="text-muted">None</span><br>';
							}
	                  	?>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<br>
	                  	<h5>Warnings: <p data-toggle="modal" data-target="#addWarningModal" class="btn btn-outline-info m-2">Add</p><span class="text-success pl-3" id="warningsuccess"></span></h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
							foreach ($warnings as $row)
							{
								$warnings_unit_identifier = $row['unitidentifier'];
								$warnings_date = $row['date'];
								$warnings_time = $row['time'];
								$warnings_offences = $row['offences'];
								$warnings_note = $row['note'];
	                  	?>
	                  	<div class="row">
	                  		<div class="col-md-6">
							<span class="text-muted"><b>Offence:</b> <?php echo $warnings_offences; ?></span><br>
							<span class="text-muted"><b>Note:</b> <?php echo $warnings_note; ?></span><br>
							<span class="text-muted"><b>Date & Time:</b> <?php echo $warnings_date . " | " . $warnings_time; ?></span><br>
							<span class="text-muted"><b>Signing Unit:</b> <?php echo $warnings_unit_identifier; ?></span><br>
							</div>
							<div class="col-md-6" style="top: 35px;">
							<button type="button" onclick="deleteWarning(<?php echo $row['ID']; ?>)" class="btn btn-outline-danger m-2">Delete</button><br>
							</div>
						</div>
	                  		<span class="text-muted">=====================================</span><br>
	                  	<?php
							}

							if ($warnings_unit_identifier == "")
							{
								echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
							}
	                  	?>
	                  	<br>
	                  	<h5>Citations: <p data-toggle="modal" data-target="#addCitationModal" class="btn btn-outline-info m-2">Add</p><span class="text-success pl-3" id="citationsuccess"></span></h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
							foreach ($citations as $row)
							{
								$citation_unit_identifier = $row['unitidentifier'];
								$citation_date = $row['date'];
								$citation_time = $row['time'];
								$citation_offences = $row['offences'];
								$citation_fine = $row['fine'];
								$citation_note = $row['note'];
	                  	?>
	                  	<div class="row">
	                  		<div class="col-md-6">
							<span class="text-muted"><b>Offence:</b> <?php echo $citation_offences; ?></span><br>
							<span class="text-muted"><b>Fine:</b> $<?php echo $citation_fine; ?></span><br>
							<span class="text-muted"><b>Note:</b> <?php echo $citation_note; ?></span><br>
							<span class="text-muted"><b>Date & Time:</b> <?php echo $citation_date . " | " . $citation_time; ?></span><br>
							<span class="text-muted"><b>Signing Unit:</b> <?php echo $citation_unit_identifier; ?></span><br>
							</div>
							<div class="col-md-6" style="top: 35px;">
							<button type="button" onclick="deleteCitation(<?php echo $row['ID']; ?>)" class="btn btn-outline-danger m-2">Delete</button><br>
							</div>
						</div>
	                  		<span class="text-muted">=====================================</span><br>
	                  	<?php
							}

							if ($citation_unit_identifier == "")
							{
								echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
							}

	                  	?>
	                  	<br>
	                  	<h5>Arrests: <p data-toggle="modal" data-target="#addArrestsModal" class="btn btn-outline-info m-2">Add</p><span class="text-success pl-3" id="arrestsuccess"></span></h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
							foreach ($arrests as $row)
							{
								$arrests_unit_identifier = $row['unitidentifier'];
								$arrests_date = $row['date'];
								$arrests_time = $row['time'];
								$arrests_type = $row['arresttype'];
								$arrests_reason = $row['reason'];
								$arrests_fine = $row['fine'];
								$arrests_jailtime = $row['jailtime'];
								$arrests_note = $row['note'];
	                  	?>
	                  	<div class="row">
	                  		<div class="col-md-6">
	                  		<span class="text-muted"><b>Type:</b> <?php echo $arrests_type; ?></span><br>
							<span class="text-muted"><b>Reason:</b> <?php echo $arrests_reason; ?></span><br>
							<span class="text-muted"><b>Fine:</b> $<?php echo $arrests_fine; ?></span><br>
							<span class="text-muted"><b>Jail Time:</b> <?php echo $arrests_jailtime; ?> Seconds</span><br>
							<span class="text-muted"><b>Note:</b> <?php echo $arrests_note; ?></span><br>
							<span class="text-muted"><b>Date & Time:</b> <?php echo $arrests_date . " | " . $arrests_time; ?></span><br>
							<span class="text-muted"><b>Signing Unit:</b> <?php echo $arrests_unit_identifier; ?></span><br>
							</div>
							<div class="col-md-6" style="top: 35px;">
							<button type="button" onclick="deleteArrest(<?php echo $row['ID']; ?>)" class="btn btn-outline-danger m-2">Delete</button><br>
							</div>
						</div>
	                  		<span class="text-muted">=====================================</span><br>
	                  	<?php
							}

							if ($arrests_unit_identifier == "")
							{
								echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
							}

	                  	?>
	                  	<br>
	                  	<h5>Warrants: <p data-toggle="modal" data-target="#addWarrantModal" class="btn btn-outline-info m-2">Add</p><span class="text-success pl-3" id="warrantsuccess"></span></h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
							foreach ($warrants as $row)
							{
								$warrants_unit_identifier = $row['unitidentifier'];
								$warrants_date = $row['date'];
								$warrants_time = $row['time'];
								$warrants_details = $row['details'];
								$warrants_requestingunit = $row['requestingunit'];
	                  	?>
	                  	<div class="row">
	                  		<div class="col-md-6">
							<span class="text-muted"><b>Details:</b> <?php echo $warrants_details; ?></span><br>
							<span class="text-muted"><b>Date & Time:</b> <?php echo $warrants_date . " | " . $warrants_time; ?></span><br>
							<span class="text-muted"><b>Requesting Unit:</b> <?php echo $warrants_requestingunit; ?></span><br>
							<span class="text-muted"><b>Signing Unit:</b> <?php echo $warrants_unit_identifier; ?></span><br>
							</div>
							<div class="col-md-6" style="top: 35px;">
							<button type="button" onclick="deleteWarrant(<?php echo $row['ID']; ?>)" class="btn btn-outline-danger m-2">Delete</button><br>
							</div>
						</div>
	                  		<span class="text-muted">=====================================</span><br>
	                  	<?php
							}

							if ($warrants_unit_identifier == "")
							{
								echo '<span class="text-muted text-success">None</span><br><span class="text-muted">=====================================</span><br>';
							}

	                  	?>
                      <br>
                      <h5>Medical Record:<span class="text-success pl-3" id="medicalsuccess"></span></h5>
                      <span class="text-muted">=====================================</span><br>
                      <?php
                      foreach ($medicalrecords as $row)
                      {
                        $medicalrecord_unit_identifier = $row['unitidentifier'];
                        $medicalrecord_date = $row['date'];
                        $medicalrecord_time = $row['time'];
                        $medicalrecord_details = $row['details'];
                              ?>
                      <div class="row">
                      <div class="col-md-6">
                      <span class="text-muted"><b>Details:</b> <?php echo $medicalrecord_details; ?></span><br>
                      <span class="text-muted"><b>Date & Time:</b> <?php echo $medicalrecord_date . " | " . $medicalrecord_time; ?></span><br>
                      <span class="text-muted"><b>Signing Unit:</b> <?php echo $medicalrecord_unit_identifier; ?></span><br>
                      </div>
                      <div class="col-md-6" style="top: 15px;">
                      <button type="button" onclick="deleteMedical(<?php echo $row['ID']; ?>)" class="btn btn-outline-danger">Delete</button><br>
                      </div>
                      </div>
                      <span class="text-muted">=====================================</span><br>
                      <?php
                      }
                      if ($medicalrecord_unit_identifier == "")
                      {
                        echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
                      }
                      ?>
	                  </div>
	                </div>

<!-- Warning Modal -->
<div class="modal fade" id="addWarningModal" tabindex="-1" role="dialog" aria-labelledby="addWarningModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addWarningModal">Add Written Warning</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" action="../actions/department_functions.php" method="POST">
            <div class="form-group">
            	<label for="warning_name">Violaters Name</label>
				<input type="text" class="form-control p-input" id="warning_name" name="warning_name" value="<?php echo $character_name; ?>" readonly>
				<input type="text" class="form-control p-input" id="warning_id" name="warning_id" value="<?php echo $character_id; ?>" hidden>
			</div>
			<div class="form-group">
				<label>Violaters DOB</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_dob; ?>" readonly>
			</div>
			<div class="form-group">
				<label>Violaters Address</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_address; ?>" readonly>
			</div>
			<div class="form-group">
				<label for="warning_offence">Offence(s)</label>
				<input type="text" class="form-control p-input" id="warning_offence" name="warning_offence" autocomplete="off" onKeyUp="showResults(this, 'penal_result1')" required>
				<div id="penal_result1" class="penal_result"></div>
			</div>
			<div class="form-group">
				<label for="warning_notes">Notes</label>
				<textarea type="text" class="form-control p-input" id="warning_notes" name="warning_notes" required></textarea>
			</div>
			<div class="form-group">
                <label>Date & Time</label>
				<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
			</div>
            <button type="submit" name="add_written_warning_btn" class="btn btn-outline-info">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Citations Modal -->
<div class="modal fade" id="addCitationModal" tabindex="-1" role="dialog" aria-labelledby="addCitationModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCitationModal">Add Citation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" action="../actions/department_functions.php" method="POST">
            <div class="form-group">
            	<label for="citation_name">Violaters Name</label>
				<input type="text" class="form-control p-input" id="citation_name" name="citation_name" value="<?php echo $character_name; ?>" readonly>
				<input type="text" class="form-control p-input" id="citation_id" name="citation_id" value="<?php echo $character_id; ?>" hidden>
			</div>
			<div class="form-group">
				<label>Violaters DOB</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_dob; ?>" readonly>
			</div>
			<div class="form-group">
				<label>Violaters Address</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_address; ?>" readonly>
			</div>
			<div class="form-group">
				<label for="citation_offence">Offence(s)</label>
				<input type="text" class="form-control p-input" id="citation_offence" name="citation_offence" autocomplete="off" onKeyUp="showResults(this, 'penal_result2')" required>
				<div id="penal_result2" class="penal_result"></div>
			</div>
			<div class="form-group">
				<label for="citation_fine">Fine</label>
				<input type="number" class="form-control p-input" id="citation_fine" name="citation_fine" required>
			</div>
			<div class="form-group">
				<label for="citation_notes">Notes</label>
				<textarea type="text" class="form-control p-input" id="citation_notes" name="citation_notes" required></textarea>
			</div>
			<div class="form-group">
                <label>Date & Time</label>
				<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
			</div>
            <button type="submit" name="add_citation_btn" class="btn btn-outline-info">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Arrests Modal -->
<div class="modal fade" id="addArrestsModal" tabindex="-1" role="dialog" aria-labelledby="addArrestsModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addArrestsModal">Add Arrest</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" action="../actions/department_functions.php" method="POST">
            <div class="form-group">
            	<label for="arrest_name">Violaters Name</label>
				<input type="text" class="form-control p-input" id="arrest_name" name="arrest_name" value="<?php echo $character_name; ?>" readonly>
				<input type="text" class="form-control p-input" id="arrest_id" name="arrest_id" value="<?php echo $character_id; ?>" hidden>
			</div>
			<div class="form-group">
				<label>Violaters DOB</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_dob; ?>" readonly>
			</div>
			<div class="form-group">
				<label>Violaters Address</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_address; ?>" readonly>
			</div>
			<div class="form-group">
				<label for="arrest_type">Arrest Type</label>
	            <select class="form-control p-input" id="arrest_type" name="arrest_type" style="color: white;" required>
	            	<option value="Felony">Felony</option>
	            	<option value="Misdemeanor">Misdemeanor</option>
	            	<option value="Other">Other</option>
	            </select>
			</div>
			<div class="form-group">
				<label for="arrest_reason">Reason</label>
				<input type="text" class="form-control p-input" id="arrest_reason" name="arrest_reason" autocomplete="off" onKeyUp="showResults(this, 'penal_result3')" required>
				<div id="penal_result3" class="penal_result"></div>
			</div>
			<div class="form-group">
				<label for="arrest_fine">Fine</label>
				<input type="number" class="form-control p-input" id="arrest_fine" name="arrest_fine" required>
			</div>
			<div class="form-group">
				<label for="arrest_jailtime">Jail Time (Seconds)</label>
				<input type="number" class="form-control p-input" id="arrest_jailtime" name="arrest_jailtime" required>
			</div>
			<div class="form-group">
				<label for="arrest_notes">Notes</label>
				<textarea type="text" class="form-control p-input" id="arrest_notes" name="arrest_notes" required></textarea>
			</div>
			<div class="form-group">
                <label>Date & Time</label>
				<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
			</div>
            <button type="submit" name="add_arrest_btn" class="btn btn-outline-info">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Warrant Modal -->
<div class="modal fade" id="addWarrantModal" tabindex="-1" role="dialog" aria-labelledby="addArrestsModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addArrestsModal">Add Warrant</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" action="../actions/department_functions.php" method="POST">
            <div class="form-group">
            	<label>Violaters Name</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_name; ?>" readonly>
				<input type="text" class="form-control p-input" id="warrant_id" name="warrant_id" value="<?php echo $character_id; ?>" hidden>
			</div>
			<div class="form-group">
				<label>Violaters DOB</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_dob; ?>" readonly>
			</div>
			<div class="form-group">
				<label>Violaters Address</label>
				<input type="text" class="form-control p-input" value="<?php echo $character_address; ?>" readonly>
			</div>
			<div class="form-group">
				<label for="warrant_details">Details</label>
				<textarea type="text" class="form-control p-input" id="warrant_details" name="warrant_details" required></textarea>
			</div>
			<div class="form-group">
				<label for="warrant_requestingunit">Requesting Unit</label>
				<input type="text" class="form-control p-input" id="warrant_requestingunit" name="warrant_requestingunit" required>
			</div>
			<div class="form-group">
                <label>Date & Time</label>
				<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
			</div>
            <button type="submit" name="add_warrant_btn" class="btn btn-outline-info">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>


					<?php
						}
	                ?>
	            </div>
				<div class="col-xl-4">
                	<div class="card">
	              		<div class="card-body text-center">
	                		<h3 class="mt-2 mb-2 text-center">Plate Check</h3><br>
 						    <form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
						        <div class="form-group">
						            <input type="text" class="form-control p-input" placeholder="Search Plate" onkeyup="showResult(this.value, 'PLATE')" style="color: white;">
						        </div>
						        <div id="platesearch"></div>
						    </form>
	                    </div>
	            	</div>
	                <br>
	                <?php
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
								$vehicle_vin = $row['vin'];

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
	                  	<h5>Registered Owner: <span class="text-muted"><a style="color: <?php echo ACCENT_COLOR; ?>;" href="?nameq=<?php echo $vehicle_charid; ?>&plateq=<?php echo $plateq; ?>#searched"><?php echo $owner_name; ?></a></span></h5>
	                  	<h5>Plate: <span class="text-muted"><?php echo $vehicle_plate; ?></span></h5>
	                  	<h5>Make & Model: <span class="text-muted"><?php echo $vehicle_makemodel; ?></span></h5>
	                  	<h5>Color: <span class="text-muted"><?php echo $vehicle_color; ?></span></h5>
	                  	<h5>Insurance: <span style="color: <?php echo  getColor($vehicle_insurance); ?>;"><?php echo $vehicle_insurance; ?></span></h5>
	                  	<h5>Registered State: <span class="text-muted"><?php echo $vehicle_regstate; ?></span></h5>
	                  	<h5>Flags: <span class="<?php if($vehicle_flags=="None"){echo"text-muted";}else{echo"text-danger";} ?>"><?php echo $vehicle_flags; ?></span></h5>
	                  	<?php if ($vehicle_vin != null) {?><h5>VIN: <span class="text-muted"><?php echo $vehicle_vin; ?></span></h5><?php }?>
	                  </div>
	                </div>
					<?php
						}
	                ?>
	            </div>
	            <div class="col-xl-4">
                	<div class="card">
	              		<div class="card-body text-center">
	                		<h3 class="mt-2 mb-2 text-center">Weapon Check</h3><br>
 						    <form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
						        <div class="form-group">
						            <input type="text" class="form-control p-input" placeholder="Search Name" onkeyup="showResult(this.value, 'WEAPON')" style="color: white;">
						        </div>
						        <div id="weaponsearch"></div>
						    </form>
	                    </div>
	            	</div>
	                <br>
	                <?php
						if (isset($_GET['weaponq']))
						{
						    $weaponq = htmlspecialchars($_GET['weaponq']);
						    $_SESSION['weaponq'] = $weaponq;
						    $weapons = $pdo->query("SELECT * FROM weapons WHERE charid='$weaponq'");
					?>
	                <div class="card">
	                  <div class="card-body">
	                  	<div style="font-size: 20px; text-align: right;">
	                  		<a style="text-decoration: none; color: white; right: 0;" href="courtDashboard.php"><span>&times;</span></a><br><br>
	                  	</div>
	                  	<h5>Weapons:</h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php 
						    foreach ($weapons as $row)
							{
								$weapon_type = $row['type'];
								$weapon_name = $row['name'];
								$weapon_serialnumber = $row['serialnumber'];

	                  	?>
	                  	<h5>Type: <span class="text-muted"><?php echo $weapon_type; ?></span></h5>
	                  	<h5>Name: <span class="text-muted"><?php echo $weapon_name; ?></span></h5>
	                  	<h5>Serial Number: <span class="text-muted"><?php echo $weapon_serialnumber; ?></span></h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<?php
	                  		}

							if ($weapon_serialnumber == "")
							{
								echo '<span class="text-muted text-success">None</span><br><span class="text-muted">=====================================</span><br>';
							}
	                  	?>
	                  </div>
	                </div>
					<?php
						}
	                ?>
	            </div>
          	</div>
          	<br><br>
          	<div class="row">
	            <div class="col-xl-6">
                	<div class="card">
	              		<div class="card-body text-center">
	                		<h3 class="mt-2 mb-2 text-center">Search Call History</h3><br>
 						    <form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
						        <div class="form-group">
						            <input type="text" class="form-control p-input" placeholder="Search By Call ID, Units Discord ID or Call Type" onkeyup="showResult(this.value, 'CALL')" style="color: white;">
						        </div>
						        <div id="callsearch"></div>
						    </form>
	                    </div>
	            	</div>
	            </div>
	            <div class="col-xl-6">
	                <?php
						if (isset($_GET['callq']))
						{
						    $callq = htmlspecialchars($_GET['callq']);
						    $call = $pdo->query("SELECT * FROM activecalls WHERE ID='$callq'");

						    foreach ($call as $row)
							{
								$call_ID = $row['ID'];
								$call_type = $row['calltype'];
								$call_date = $row['date'];
								$call_time = $row['time'];
								$call_location = $row['location'];
								$call_postal = $row['postal'];
								$call_narrative = $row['narrative'];
								$call_attachedunits = $row['attachedunits'];
								$seperatedunits = explode(",",$call_attachedunits);
								$call_status = $row['status'];
							}
	                  	?>
	                <div class="card">
	                  <div class="card-body">
	                  	<div style="font-size: 20px; text-align: right;">
	                  		<a style="text-decoration: none; color: white; right: 0;" href="courtDashboard.php"><span>&times;</span></a><br><br>
	                  	</div>
	                  	<h5>Call History - <?php echo $call_ID; ?></h5>
	                  	<span class="text-muted">=====================================</span><br>
	                  	<h5>Call Type: <span class="text-muted font-weight-normal"><?php echo $call_type; ?></span></h5>
	                  	<h5>Location: <span class="text-muted font-weight-normal"><?php echo $call_location; ?></span></h5>
	                  	<h5>Postal: <span class="text-muted font-weight-normal"><?php echo $call_postal; ?></span></h5>
	                  	<h5>Date: <span class="text-muted font-weight-normal"><?php echo $call_date; ?></span></h5>
	                  	<h5>Time: <span class="text-muted font-weight-normal"><?php echo $call_time; ?></span></h5>
	                  	<h5>Status: <span class="text-muted font-weight-normal"><?php if ($call_status = "0") {echo "Open";} else {echo "Ended";} ?></span></h5><br>
	                  	<h5>Narrative: </h5>
	                  	<span class="text-muted"><?php echo $call_narrative; ?></span><br><br>
	                  	<h5>Attached Units: </h5>
	                  	<?php
				        foreach ($seperatedunits as $value)
				        {
				          $value = str_replace(' ', '', $value);
				          $getidentifier = $pdo->query("SELECT * FROM users WHERE discordid='$value'");
				          foreach ($getidentifier as $row)
				          {
				            echo "<span class='text-muted'>".$row['identifier']. " : " .$row['discordid']."</span><br>";
				          }
				        }
				        ?>
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
			if (type == 'PLATE')
			{
				document.getElementById("platesearch").innerHTML="";
			}
			if (type == 'WEAPON')
			{
				document.getElementById("weaponsearch").innerHTML="";
			}
			if (type == 'CALL')
			{
				document.getElementById("callsearch").innerHTML="";
			}
			return;
		}
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {  // code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (this.readyState == 4 && this.status == 200) {
				if (type == 'NAME')
				{
					document.getElementById("namesearch").innerHTML = this.responseText;
				}
				if (type == 'PLATE')
				{
					document.getElementById("platesearch").innerHTML = this.responseText;
				}
				if (type == 'WEAPON')
				{
					document.getElementById("weaponsearch").innerHTML = this.responseText;
				}
				if (type == 'CALL')
				{
					document.getElementById("callsearch").innerHTML = this.responseText;
				}
			}
		}

		if (type == 'NAME')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?nc=" + str, true);
			xmlhttp.send();
		}
		if (type == 'PLATE')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?pc=" + str, true);
			xmlhttp.send();
		}
		if (type == 'WEAPON')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?wc=" + str, true);
			xmlhttp.send();
		}
		if (type == 'CALL')
		{
			xmlhttp.open("GET", "../actions/department_functions.php?cs=" + str, true);
			xmlhttp.send();
		}

		}

		function deleteWarning(id) {
			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { deletewarning : id },
					success: function(res){  
								// success
								$('#warningsuccess').text(res); 
								location.reload();
							}
        	});

		}	
		function deleteCitation(id) {
			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { deletecitation : id },
					success: function(res){  
								// success
								$('#citationsuccess').text(res); 
								location.reload();
							}
        	});

		}	
		function deleteArrest(id) {
			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { deletearrest : id },
					success: function(res){  
								// success
								$('#arrestsuccess').text(res); 
								location.reload();
							}
        	});

		}	
		function deleteWarrant(id) {
			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { deletewarrant : id },
					success: function(res){  
								// success
								$('#warrantsuccess').text(res); 
								location.reload();
							}
        	});

		}

		function updateSearchedLicense(id) {
			var license_type = document.getElementById("license_type");
			var licensetype = license_type.value;
			var license_status = document.getElementById("license_status");
			var licensestatus = license_status.value;

			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { courtlicenseid : id, courtlicensetype : licensetype, courtlicensestatus : licensestatus },
					success: function(res){  
								// success
								$('#licensesuccess').text(res); 
								location.reload();
							}
        	});

		}

		function addDriversPoints(id) {
			var add_points = document.getElementById("add_points");
			var addpoints = add_points.value;

			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { addpoints : addpoints, driversid : id },
					success: function(res){  
								// success
								location.reload();
							}
        	});

		}

		function removeDriversPoints(id) {
			var add_points = document.getElementById("add_points");
			var addpoints = add_points.value;

			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { removepoints : addpoints, driversid : id },
					success: function(res){  
								// success
								location.reload();
							}
        	});
		}

	    function deleteMedical(id) {
			$.ajax({
					type : "POST",
					url  : "../actions/department_functions.php",
					data : { deletemedical : id },
					success: function(res){  
								// success
								$('#medicalsuccess').text(res); 
								location.reload();
							}
        	});

	    }	

	    <?php

		$penal_query = $pdo->query("SELECT * FROM penalcode");
		$PENALCODE = [];
		foreach ($penal_query as $row)
		{
			$PENALCODE[] = $row['title'];
		}

	    ?>

		var search_terms = <?php echo json_encode($PENALCODE); ?>;
		 
		function autocompleteMatch(input) {
		  if (input == '') {
		    return [];
		  }
		  var reg = new RegExp(input, 'i')
		  return search_terms.filter(function(term) {
			  if (term.match(reg)) {
		  	  return term;
			  }
		  });
		}

		function showResults(that, results) {
		  let val = that.value;
		  let id = that.id;
		  res = document.getElementById(results);
		  res.innerHTML = '';
		  let list = '';
		  let terms = autocompleteMatch(val);
		  for (i=0; i<terms.length; i++) {
		    list += '<li onclick="complete(\'' + terms[i] + '\',\''+id+'\',\''+results+'\')">' + terms[i] + '</li>';
		  }
		  res.innerHTML = '<ul>' + list + '</ul>';
		}

		function complete(strComplete, inputId, showResults) {
		    document.getElementById(inputId).value = strComplete;
		    document.getElementById(showResults).innerHTML = ""; // empty the suggestion div
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