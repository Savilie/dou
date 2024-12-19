<?php 
require_once(__DIR__ . "/../actions/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();
checkBan();
$user_discordid = $_SESSION['user_discordid'];
$_SESSION['redirect'] = "/leo/leoDashboard.php";
$_SESSION['plateq'] = "";
$_SESSION['nameq'] = "";
$_SESSION['weaponq'] = "";

if ($_SESSION['leoperms'] != 1)
{
	header('Location: ../index.php?notAuthorisedDepartment');
}

try{
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(PDOException $ex)
{
	echo json_encode(array("response" => "400", "message" => "Missing Parameters"));
}

$result = $pdo->query("SELECT * FROM users WHERE discordid='$user_discordid'");

$divisions = $pdo->query("SELECT * FROM divisions WHERE type='LEO'");

$setdept = $pdo->query("UPDATE users SET currdept='LEO' WHERE discordid='$user_discordid'");

$activeunits = $pdo->query("SELECT * FROM users WHERE currstatus!='10-7'");

foreach ($result as $row)
{
	$leo_identifier = $row['identifier'];
	$leo_currstatus = $row['currstatus'];
	$leo_currpanic = $row['currpanic'];
	$leo_currdivision = $row['currdivision'];
	$leo_notepad = $row['notepad'];
	$showsupervisor = $row['showsupervisor'];
	$department = $row['department'];
	$identifier_check = $row['identifier_check'];
}

// ACTION NOTIFICATIONS
if(isset($_GET['notActive']))
{
	$actionMessage = '<div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You cannot do this as you are currently 10-7!</div>';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo SERVER_SHORT_NAME; ?> CAD | LEO</title>
	<!-- CSS -->
	<link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
	<link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
	<link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">

	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" href="../assets/vendors/quill/quill.snow.css">
	<link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'>
	<!-- FAVICON -->
	<link rel="shortcut icon" href="<?php echo SERVER_LOGO; ?>" />
</head>
<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/timer.js"></script>
<script src="../assets/js/sweetalert.js"></script>
<body id="modalCheck">
	<div class="container-scroller">
		<div class="horizontal-menu">
			<!-- HEADER -->
			<?php include "../includes/header.inc.php"; ?>
			<!-- NAVBAR -->
			<?php include "../includes/navbar.inc.php"; ?>
		</div>
		
		<?php
		if ($_SESSION['supervisor'] == 1) {
			?>
			<div id="panel" class="panel">
				<a style="text-decoration: none; color: white; font-size: 1.5em;" href="#"><span>&times;</span></a>
				<h4 class="text-center"><u>Active Units</u></h4>
				<br>
				<div style="text-align: center;">
					<?php
					foreach ($activeunits as $row)
					{
						echo "<p>".$row['identifier']." | <span style='color: ".ACCENT_COLOR.";'><i>".$row['currstatus']."</i></span><p>";
					}
					?>
				</div>
			</div>
			<?php
		}
		?>

		<div class="container-fluid page-body-wrapper">
			<div class="main-panel">
				<div class="content-wrapper">
					<!-- ACTION DISPLAY -->
					<?php if($actionMessage){echo $actionMessage;} ?>
					<div class="row">
						<div class="col-xl-9">
							<div class="card">
								<script>
									fetch('../actions/getzulu.php').then(function (resp) {
										resp.text().then(function (text) {
											$('#getzulu').html(text);
										});
									});
									var auto_refresh = setInterval( function () {
										fetch('../actions/getzulu.php').then(function (resp) {
											resp.text().then(function (text) {
												$('#getzulu').html(text);
											});
										});
									}, 1000);
								</script>
								<div style="font-family: 'Orbitron'; font-size: 30px; z-index: 2; position: absolute; padding: 30px 0px 0px 50px;" class="text-muted" id="getzulu"></div>
								<div class="card-body text-center" style="padding-top: 55px; padding-bottom: 66px;">
									<h3 class="mb-2 text-center">LEO Control Panel</h3><br>
									<h5 class="text-center">Identifier: <span class="text-muted"><?php echo $leo_identifier; ?> <?php if ($_SESSION['supervisor'] == 1 & $showsupervisor == 1) {echo "(Supervisor)";} ?></span></h5><br>
									<?php
									if (($department != "") && ($department != "None"))
									{
										?>
										<h5 class="text-center">Department: <span class="text-muted"><?php echo $department; ?></span></h5><br>
										<?php
									}
									?>
										<h5 class="text-center">Status: <span class="text-muted" id="changestatus"><?php echo $leo_currstatus; ?></span></h5>
									<?php
									if ($leo_currdivision != "None")
									{
										?>
										<br><h5 class="text-center">Subdivision: <span class="text-muted"><?php echo $leo_currdivision; ?></span></h5>
										<?php
									}
									?>
									<div id="getpanic">
										<script type="text/javascript">
											$('#getpanic').load('../actions/getpanic.php');
											var auto_refresh = setInterval( function () {
												$('#getpanic').load('../actions/getpanic.php');
											}, 10000);
										</script>
									</div>
									<div id="getsignal">
										<script type="text/javascript">
											$('#getsignal').load('../actions/getsignal.php');
											var auto_refresh = setInterval( function () {
												$('#getsignal').load('../actions/getsignal.php');
											}, 5000);
										</script>     				
									</div>
									<div id="getping">
										<script type="text/javascript">
											$('#getping').load('../actions/getping.php');
											var auto_refresh = setInterval( function () {
												$('#getping').load('../actions/getping.php');
											}, 5000);
										</script>     				
									</div>
									<br>
									<button type="button" onclick="changeStatus('10-8')" class="btn btn-outline-success btn-rounded btn-fw p-3 m-2">10-8 | In Service</button>
									<button type="button" onclick="changeStatus('10-7')" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">10-7 | Out of Service</button>
									<?php 
									if ($leo_currpanic == 0)
									{
										echo '<p data-toggle="modal" data-target="#panicModal" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Panic Button</p>';
									}
									else
									{
										?>
										<button type="button" onclick="disablePanic()" class="btn btn-outline-danger btn-rounded btn-fw p-3 m-2">Disable Panic</button>
										<?php
									}
									?>
									<br>
									<?php
									foreach ($LEO_STATUSES as $status => $short)
									{
										?>
										<button type="button" onclick="changeStatus('<?php echo $short; ?>')" class="btn btn-outline-info btn-rounded btn-fw p-3 m-2"><?php echo $status; ?></button>
										<?php
									}
									?>
									<br>
									<p data-toggle="modal" data-target="#Code10" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">10 Codes</p>
									<?php
									if (PENAL_CODE_LINK != "#")
									{
										?>
										<a href="<?php echo PENAL_CODE_LINK; ?>" target="_blank" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Penal Code</a>
										<?php 
									}
									?>
									<p data-toggle="modal" data-target="#noteModal" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Notepad</p>
									<a href="../warrants/warrantDashboard.php" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Warrants Dashboard</a>
									<p data-toggle="modal" data-target="#subdivisionModal" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Select Subdivision</p>
									<p data-toggle="modal" data-target="#createBolo" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Create Bolo</p>
									<?php
									if ($_SESSION['supervisor'] == 1) {
										?>
										<a href="#panel" class="btn btn-outline-warning btn-rounded btn-fw p-3 m-2">Show Active Units</a>
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<div class="col-xl-3">
							<div class="card">
								<div class="card-body text-center">
									<h3 class="mt-3 text-center">Livechat</h3><br>
									<link rel="stylesheet" href="../assets/css/livechat.css">
									<div id="wrapper">
										<div id="chatbox" style="margin-left: 20px;">
											<?php
											if(file_exists("../actions/log.html") && filesize("../actions/log.html") > 0){
												$contents = file_get_contents("../actions/log.html");          
												echo $contents;
											}
											?>
										</div>
										<form action="">
											<input placeholder="Start typing..." name="usermsg" class="livechat-input p-input" type="text" id="usermsg" style="width: 20em;" />
											<input name="submitmsg" type="submit" id="submitmsg" value="Send" class="btn btn-outline-info"/>
											<br><br>
										</form>
									</div>
									<script type="text/javascript">
										$(document).ready(function () {
											$("#submitmsg").click(function () {
												var clientmsg = $("#usermsg").val();
												$.post("../actions/sendchat.php", { text: clientmsg });
												$("#usermsg").val("");
												return false;
											});

											function loadLog() {
												var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20;
												$.ajax({
													url: "../actions/log.html",
													cache: false,
													success: function (html) {
														$("#chatbox").html(html);

					                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
					                            if(newscrollHeight > oldscrollHeight){
					                            	$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
					                            }   
					                        }
					                    });
											}
											setInterval (loadLog, 1000);
										});
									</script>
								</div>
							</div>
						</div>
					</div>
					<br>
					<br>
					<div class="row">
						<div class="col-xl-6">
							<div class="card">
								<div class="card-body text-center">
									<h3 class="mt-2 mb-2 text-center">Your Calls</h3><br>
									<div id="getcalls">
										<script type="text/javascript">
											var modalChecking;
											$('#getcalls').load('../actions/getcalls.php');
											var auto_refresh = setInterval( function () {
												modalChecking = $('#modalCheck').hasClass('modal-open');
												if (modalChecking == false)
												{
													$('#getcalls').load('../actions/getcalls.php');
												}
											}, 3000);
										</script>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-6">
							<div class="card">
								<div class="card-body text-center">
									<h3 class="mt-2 mb-2 text-center">Active Bolos</h3><span id="bolosuccess"></span><br>
									<div class="table-responsive" id="vehiclesection">
										<table class="table text-center" id="getbolo">
											<script type="text/javascript">
												$('#getbolo').load(`../actions/getbolo.php`);
												var auto_refresh = setInterval( function () {
													$('#getbolo').load(`../actions/getbolo.php`);
												}, 3000);
											</script>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<br>
					<br>
					<div class="row">
						<div class="col-xl-4">
							<div class="card" id="searched">
								<div class="card-body text-center">
									<h3 class="mt-2 mb-1 text-center">Name Check</h3><br>
									<form class="forms-sample" autocomplete="off" enctype="multipart/form-data">
										<div class="form-group">
											<input type="text" class="form-control p-input" placeholder="Search Name or SSN" onkeyup="showResult(this.value, 'NAME')" style="color: white;">
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
									$character_discordid = $row['discordid'];
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

									if (checkOnline('discord:'.$character_discordid) == true) {
										$player_ingame = true;
									}
								}

								$regvehicle = $pdo->query("SELECT * FROM vehicles WHERE charid='$character_id' ORDER BY ID DESC");

								$warnings = $pdo->query("SELECT * FROM warnings WHERE civid='$character_id' ORDER BY ID DESC");

								$citations = $pdo->query("SELECT * FROM citations WHERE civid='$character_id' ORDER BY ID DESC");

								$arrests = $pdo->query("SELECT * FROM arrests WHERE civid='$character_id' ORDER BY ID DESC");

								$warrants = $pdo->query("SELECT * FROM warrants WHERE civid='$character_id' ORDER BY ID DESC");
								$warrantscount = $pdo->query("SELECT * FROM warrants WHERE civid='$character_id' ORDER BY ID DESC");
								?>
								<div class="card">
									<div class="card-body">
										<div style="font-size: 20px; text-align: right;">
											<a style="text-decoration: none; color: white;" href="leoDashboard.php"><span>&times;</span></a>
										</div><br>
										<?php if (sizeof($warrantscount->fetchAll()) != null) {?><h4 style="color: red; text-align: center;">!! WANTED !!</h4><br><?php }?>
										<div class="text-center">
											<img src="<?php echo $character_image; ?>" alt="Image" style="border-radius: 100px; width: 20%; <?php if ($player_ingame == true) {echo "border: 3px solid #3ba55d;";} else {echo "border: 3px solid #747f8d;";} ?>">
										</div>
										<br>
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
												<h5 class="pb-2">Suspend Licenses</h5><span class="text-success" id="licensesuccess"></span>
												<div class="row">
													<div class="col-md-10">
														<div class="form-group">
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
													<div class="form-group">
														<select class="form-control p-input" id="license_status"style="color: white;" hidden>
															<option value="Suspended">Suspended</option>
														</select>
													</div>
												</div>
												<button type="submit" onclick="updateSearchedLicense(<?php echo $character_id; ?>)" class="btn btn-outline-danger">Suspend</button>
											</div>
										</div>
										<br>
										<h5>Points: <span class="text-muted"><?php echo $driverspoints; ?></span></h5>
										<div class="form-group" style="width: 25%;">
											<input type="number" class="form-control p-input" id="add_points" name="add_points">
										</div>
										<button type="submit" onclick="addDriversPoints(<?php echo $character_id; ?>)" class="btn btn-outline-info">Add</button>
										<br>
										<br>
										<h5>Registered Vehicles Plate:</h5>
										<span class="text-muted">=====================================</span><br>
										<?php
										foreach ($regvehicle as $row)
										{
											$regvehicle_plate = $row['plate'];
											?>
											<span class="text-muted"><a style="color: <?php echo ACCENT_COLOR; ?>" href="?nameq=<?php echo $nameq; ?>&plateq=<?php echo $row['ID']; ?>#searched"><?php echo $regvehicle_plate; ?></a></span><br>
											<?php
										}

										if ($regvehicle_plate == "")
										{
											echo '<span class="text-muted">None</span><br>';
										}
										?>
										<span class="text-muted">=====================================</span><br>
										<br>
										<h5>Warnings: <p data-toggle="modal" data-target="#addWarningModal" class="btn btn-outline-info m-2">Add</p></h5>
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
											<span class="text-muted"><b>Offence:</b> <?php echo $warnings_offences; ?></span><br>
											<span class="text-muted"><b>Note:</b> <?php echo $warnings_note; ?></span><br>
											<span class="text-muted"><b>Date & Time:</b> <?php echo $warnings_date . " | " . $warnings_time; ?></span><br>
											<span class="text-muted"><b>Signing Unit:</b> <?php echo $warnings_unit_identifier; ?></span><br>
											<span class="text-muted">=====================================</span><br>
											<?php
										}

										if ($warnings_unit_identifier == "")
										{
											echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
										}
										?>
										<br>
										<h5>Citations: <p data-toggle="modal" data-target="#addCitationModal" class="btn btn-outline-info m-2">Add</p></h5>
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
											<span class="text-muted"><b>Offence:</b> <?php echo $citation_offences; ?></span><br>
											<span class="text-muted"><b>Fine:</b> $<?php echo $citation_fine; ?></span><br>
											<span class="text-muted"><b>Note:</b> <?php echo $citation_note; ?></span><br>
											<span class="text-muted"><b>Date & Time:</b> <?php echo $citation_date . " | " . $citation_time; ?></span><br>
											<span class="text-muted"><b>Signing Unit:</b> <?php echo $citation_unit_identifier; ?></span><br>
											<span class="text-muted">=====================================</span><br>
											<?php
										}

										if ($citation_unit_identifier == "")
										{
											echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
										}

										?>
										<br>
										<h5>Arrests: <p data-toggle="modal" data-target="#addArrestsModal" class="btn btn-outline-info m-2">Add</p></h5>
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
											<span class="text-muted"><b>Type:</b> <?php echo $arrests_type; ?></span><br>
											<span class="text-muted"><b>Reason:</b> <?php echo $arrests_reason; ?></span><br>
											<span class="text-muted"><b>Fine:</b> $<?php echo $arrests_fine; ?></span><br>
											<span class="text-muted"><b>Jail Time:</b> <?php echo $arrests_jailtime; ?> Seconds</span><br>
											<span class="text-muted"><b>Note:</b> <?php echo $arrests_note; ?></span><br>
											<span class="text-muted"><b>Date & Time:</b> <?php echo $arrests_date . " | " . $arrests_time; ?></span><br>
											<span class="text-muted"><b>Signing Unit:</b> <?php echo $arrests_unit_identifier; ?></span><br>
											<span class="text-muted">=====================================</span><br>
											<?php
										}

										if ($arrests_unit_identifier == "")
										{
											echo '<span class="text-muted">None</span><br><span class="text-muted">=====================================</span><br>';
										}

										?>
										<br>
										<h5>Warrants: <?php if ($_SESSION['supervisor'] == 1 && ALLOW_LEO_SUPERVISOR_ADD_WARRANT == true) {?><p data-toggle="modal" data-target="#addWarrantModal" class="btn btn-outline-info m-2">Add</p><?php } ?><span class="text-success pl-3" id="warrantsuccess"></span></h5>
										<span class="text-muted">=====================================</span><br>
										<?php
										foreach ($warrants as $row)
										{
											$warrants_unit_identifier = $row['unitidentifier'];
											$warrants_date = $row['date'];
											$warrants_time = $row['time'];
											$warrants_details = $row['details'];
											?>
											<span class="text-muted"><b>Details:</b> <?php echo $warrants_details; ?></span><br>
											<span class="text-muted"><b>Date & Time:</b> <?php echo $warrants_date . " | " . $warrants_time; ?></span><br>
											<span class="text-muted"><b>Signing Unit:</b> <?php echo $warrants_unit_identifier; ?></span><br>
											<span class="text-muted">=====================================</span><br>
											<?php
										}

										if ($warrants_unit_identifier == "")
										{
											echo '<span class="text-muted text-success">None</span><br><span class="text-muted">=====================================</span><br>';
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
													<div class="form-group">
														<label>Signing Off</label>
														<input type="text" class="form-control p-input" value="<?php echo $leo_identifier; ?>" readonly>
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
													<div class="form-group">
														<label>Signing Off</label>
														<input type="text" class="form-control p-input" value="<?php echo $leo_identifier; ?>" readonly>
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
													<div class="form-group">
														<label>Signing Off</label>
														<input type="text" class="form-control p-input" value="<?php echo $leo_identifier; ?>" readonly>
													</div>
													<button type="submit" name="add_arrest_btn" class="btn btn-outline-info">Add</button>
												</form>
											</div>
										</div>
									</div>
								</div>
								<?php if (ALLOW_LEO_SUPERVISOR_ADD_WARRANT == true) { ?>
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
											<input type="text" class="form-control p-input" placeholder="Search Plate or VIN" onkeyup="showResult(this.value, 'PLATE')" style="color: white;">
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
											<a style="text-decoration: none; color: white; right: 0;" href="leoDashboard.php"><span>&times;</span></a><br><br>
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
											<a style="text-decoration: none; color: white; right: 0;" href="leoDashboard.php"><span>&times;</span></a><br><br>
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
					<br>
					<br>
					<!-- FOOTER -->
					<?php include "../includes/footer.inc.php"; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Subdivision Modal -->
	<div class="modal fade" id="subdivisionModal" tabindex="-1" role="dialog" aria-labelledby="subdivisionModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="subdivisionModal">Subdivision</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" action="../actions/department_functions.php" method="POST">
						<div class="form-group">
							<label for="update_subdivision">Update Subdivision</label>
							<select class="form-control p-input" id="update_subdivision" name="update_subdivision" style="color: white;" required>
								<option value="None">None</option>
								<?php
								foreach ($divisions as $row)
								{
									?>
									<option value="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></option>
									<?php 
								}
								?>
							</select>
						</div>
						<button type="submit" name="update_subdivision_btn" class="btn btn-outline-info">Update</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Create Bolo Modal -->
	<div class="modal fade" id="createBolo" tabindex="-1" role="dialog" aria-labelledby="createBolo" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="createBolo">Create Bolo</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" action="../actions/department_functions.php" method="POST">
						<div class="form-group">
							<label for="bolo_type">Bolo Type</label>
							<select  onchange="checkVehicle(this);" class="form-control p-input" id="bolo_type" name="bolo_type" style="color: white;" required>
								<option value="Person">Person</option>
								<option value="Vehicle">Vehicle</option>
								<option value="Other">Other</option>
							</select>
						</div>
						<div class="form-group" id="ifVeh" style="display: none;">
							<label for="bolo_plate">Plate</label>
							<input type="text" class="form-control p-input" id="bolo_plate" name="bolo_plate">
						</div>
						<div class="form-group">
							<label for="bolo_details">Details</label>
							<textarea type="text" class="form-control p-input" id="bolo_details" name="bolo_details" required></textarea>
						</div>
						<div class="form-group">
							<label>Date & Time</label>
							<input type="text" class="form-control p-input" value="<?php echo date('Y-m-d | H:i:s'); ?>" readonly>
						</div>
						<button type="submit" name="add_bolo_btn" class="btn btn-outline-info">Add</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Identifier Check Modal -->
	<div class="modal fade" id="identifierCheckModal" tabindex="-1" role="dialog" aria-labelledby="identifierCheckModal" aria-hidden="true" style="background: rgba(5, 5, 5, .9);">
	  <div class="modal-dialog" style="margin-top: 300px; max-width: 800px;" role="document">
	    <div class="modal-content">
	      <div class="modal-body">
	      	<h4 style="text-align: center;">PLEASE MAKE SURE THE FOLLOWING ARE CORRECT!</h4>
	      	<br>
	        <form class="forms-sample row" action="<?php echo BASE_URL; ?>/actions/civ_functions.php" method="POST">
	            <div class="form-group col-md-6">
	                <label for="user_identifier">Current Identifier</label>
	                <input type="text" class="form-control" id="user_identifier" name="user_identifier" placeholder="Enter Identifier" value="<?php echo $user_identifier; ?>">
	            </div>
	            <div class="form-group col-md-6">
	              <label for="user_department">Current Department</label>
	              <select class="form-control p-input" id="user_department" name="user_department" style="color: white;" required>
	                <?php
	                if (!empty($user_department)) {
	                  echo '<option value="'.$user_department.'">'.$user_department.'</option>';
	                }
	                foreach ($_SESSION['departments_identifiers'] as $identifier)
	                {
	                  if ($identifier != NULL) {
	                    echo '<option value="'.$identifier.'">'.$identifier.'</option>';
	                  }
	                }
	                ?>
	                <option value="None">None</option>
	              </select>
	            </div>
	            <div style="text-align: center; margin: auto;">
	                <button type="submit" name="editprofile_btn" class="btn btn-lg btn-outline-success">All Good!</button>
	            </div>
	        </form>
	      </div>
	    </div>
	  </div>
	</div>

	<?php
		if ($identifier_check != "1" && IDENTIFIER_CHECK == true) {
	?>
	<script>
		var element = document.getElementById("identifierCheckModal");
  		element.classList.add("show");
  		element.style.display = "block";
	</script>
	<?php
		}

		$checkdone = $pdo->query("UPDATE users SET identifier_check='1' WHERE discordid='$user_discordid'");
	?>

	<!-- 10 Codes Modal -->
	<div class="modal fade" id="Code10" tabindex="-1" role="dialog" aria-labelledby="Code10" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document" style="margin-top: 50px;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="Code10">10 Codes</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
							<?php
							foreach ($CODES10 as $code => $color) {
								echo '<div style="color: '.$color.'">'.$code.'</div>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Panic Modal -->
	<div class="modal fade" id="panicModal" tabindex="-1" role="dialog" aria-labelledby="panicModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="panicModal">Press Panic Button</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<span id="panicsuccess"></span>
					<div class="form-group">
						<label>Your Location</label>
						<input type="text" class="form-control p-input" id="panic_location">
					</div>
					<button type="submit" onclick="startPanic()" class="btn btn-outline-danger">Press</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Note Modal -->
	<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModal" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="noteModal">Your Notepad</h5><span class="text-success pl-3" id="notesuccess"></span>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" action="../actions/department_functions.php" method="POST">
						<div class="form-group">
							<div class="row grid-margin">
								<div class="col-lg-12">
									<div class="card">
										<div class="card-body">
											<div style="color: white !important;" id="quillExample1" class="quill-container">
												<?php echo $leo_notepad; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<input type="text" id="note_content" name="note_content" hidden>
					</form>
					<div class="pt-2">
						<button type="submit" onclick="saveNote()" class="btn btn-outline-info btn-fw">Save & Close</button>
					</div>
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
				return;
			}
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			} else {
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

		}

		function saveNote () {
			var myEditor = document.querySelector('#quillExample1');
			var html = myEditor.children[0].innerHTML;
			document.getElementById("note_content").value = html;
			document.getElementById("notesuccess").innerHTML = "SUCCESS";

			$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { note_content : document.getElementById("note_content").value },
				success: function(res){  
							// success
							$('#noteModal').modal('hide');
						}
    		});
		}

		function changeStatus(status) {
			statusnospace = status.replace(/\s+/g, '+');
			$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { leostatus : statusnospace },
				success: function(res){  
							// success
							$('#changestatus').html(res); 
						}
    		});
		}

		function startPanic() {
			var panic_location = document.getElementById("panic_location").value;
			$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { panic : "1", paniclocation : panic_location },
				success: function(res){  
							// success
							$('#panicsuccess').html(res); 
							location.reload();
						}
    		});
		}

		function deleteBolo(id) {
			$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { boloid : id },
				success: function(res){  
							// success
							$('#bolosuccess').html(res); 
							location.reload();
						}
    		});
		}

		function disablePanic() {
			var paniclocation = "";
			$.ajax({
				type : "POST",
				url  : "../actions/department_functions.php",
				data : { panic : "0", paniclocation : paniclocation },
				success: function(res){  
							// success
							$('#panicsuccess').html(res); 
							location.reload();
						}
    		});
		}

		function updateSearchedLicense(id) {
			var licensetype = document.getElementById("license_type").value;
			var licensestatus = document.getElementById("license_status").value;
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

		function addDriversPoints(id) {
			var addpoints = document.getElementById("add_points").value;

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

		function checkVehicle(that) {
			if (that.value == "Vehicle") {
				document.getElementById("ifVeh").style.display = "block";
			} else {
				document.getElementById("ifVeh").style.display = "none";
			}
		}	

		<?php 
		if ($_SESSION['supervisor'] == 1)
		{
			?>
			window.addEventListener('click', function(e){   
				if (document.getElementById('panel').contains(e.target)){
			    // Clicked in box
			} else {
				window.location.href = "#";
			}
		});
			<?php
		}

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

		var dutyTime = localStorage.getItem("dutyTimeMinutes");
		if (dutyTime != "0")
		{
			if (dutyTime != "1")
			{
				restart3();
			}
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
	<script src="../assets/vendors/quill/quill.min.js"></script>
	<script src="../assets/js/editorDemo.js"></script>
</body>
</html>