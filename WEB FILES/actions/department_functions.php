<?php
require_once(__DIR__ . "/discord_functions.php");
require_once(__DIR__ . "/../config.php");

session_start();

if (isset($_POST['leostatus']))
{
	setLEOStatus();
}

if (isset($_POST['panic']))
{
	setPanic();
}

if (isset($_GET['panic2']))
{
	disableUnitPanic();
}

if (isset($_POST['unitping']))
{
	unitPing();
}

if (isset($_GET['nc']))
{
	searchName();
}

if (isset($_GET['pc']))
{
	searchPlate();
}

if (isset($_GET['wc']))
{
	searchWeapon();
}

if (isset($_GET['cs']))
{
	searchCall();
}

if (isset($_POST['ban_user_btn']))
{
	banUser();
}

if (isset($_POST['add_gallery_image']))
{
	addGallery();
}

if (isset($_POST['add_penalcode']))
{
	addPenalcode();
}

if (isset($_POST['update_subdivision_btn']))
{
	updateSubdivision();
}

if (isset($_POST['update_apparatus_btn']))
{
	updateApparatus();
}

if (isset($_POST['update_unitsidentifier_btn']))
{
	updateIdentifier();
}

if (isset($_POST['update_unitsdivision_btn']))
{
	updateDivision();
}

if (isset($_POST['note_content']))
{
	updateNote();
}

if (isset($_POST['add_written_warning_btn']))
{
	addWrittenWarning();
}

if (isset($_POST['add_citation_btn']))
{
	addCitation();
}

if (isset($_POST['add_arrest_btn']))
{
	addArrest();
}

if (isset($_POST['add_warrant_btn']))
{
	addWarrant();
}

if (isset($_POST['fireemsstatus']))
{
	setFIREEMSStatus();
}

if (isset($_POST['add_medical_record_btn']))
{
	addMedicalRecord();
}

if (isset($_POST['dispatchstatus']))
{
	setDispatchStatus();
}

if (isset($_GET['clearchat']))
{
	clearChat();
}

if (isset($_GET['activatesignal']))
{
	activateSignal();
}

if (isset($_GET['disablesignal']))
{
	disableSignal();
}

if (isset($_GET['playtone']))
{
	playSignalTone();
}

if (isset($_POST['playfiretone']))
{
	playFireTone();
}

if (isset($_POST['add_bolo_btn']))
{
	addBolo();
}

if (isset($_POST['add_call_btn']))
{
	addCall();
}

if (isset($_POST['mark10-7_btn']))
{
	markOffDuty();
}

if (isset($_POST['boloid']))
{
	deleteBolo();
}

if (isset($_POST['narrative']))
{
	updateNarrative();
}

if (isset($_POST['deletecallid']))
{
	deleteCall();
}

if (isset($_POST['deletewarning']))
{
	deleteWarning();
}

if (isset($_POST['deletecitation']))
{
	deleteCitation();
}

if (isset($_POST['deletearrest']))
{
	deleteArrest();
}

if (isset($_POST['deletewarrant']))
{
	deleteWarrant();
}

if (isset($_POST['deletemedical']))
{
	deleteMedical();
}

if (isset($_POST['roleid']))
{
	addPermission();
}

if (isset($_POST['deletepermission']))
{
	deletePermission();
}

if (isset($_POST['divisionname']))
{
	addDivision();
}

if (isset($_POST['deletedivision']))
{
	deleteDivision();
}

if (isset($_POST['deleteban']))
{
	deleteBan();
}

if (isset($_POST['deleteimage']))
{
	deleteImage();
}

if (isset($_POST['deletepenal']))
{
	deletePenal();
}

if (isset($_POST['courtlicenseid']))
{
	updateLicenseCourt();
}

if (isset($_GET['ems_markdead']))
{
	markDead();
}

if (isset($_GET['ems_markalive']))
{
	markAlive();
}

if (isset($_POST['addpoints']))
{
	addPoints();
}

if (isset($_POST['removepoints']))
{
	removePoints();
}

if (isset($_GET['showsupervisor']))
{
	showSupervisor();
}

if (isset($_GET['delete911']))
{
	delete911();
}

if (isset($_GET['assignunitcall']))
{
	assignUnits();
}

if (isset($_GET['changeunitstatus']))
{
	changeUnitStatus();
}

if (isset($_GET['changeunitdivision']))
{
	changeUnitDivision();
}

if (isset($_GET['changeunitapparatus']))
{
	changeUnitApparatus();
}

if (isset($_GET['deletechar']))
{
	deleteChar();
}

if (isset($_GET['deletechardataid']))
{
	deleteCharData();
}

function updateSubdivision()
{
	$user_discordid = $_SESSION['user_discordid'];
	$update_subdivision = htmlspecialchars($_POST['update_subdivision']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['leoperms'] == 1 || $_SESSION['fireemsperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// INSERT INTO DATABASE
		$result = $pdo->query("UPDATE users SET currdivision='$update_subdivision' WHERE discordid='$user_discordid'");

	    // LOG IT
		$log = new richEmbed("SUBDIVISION UPDATED", "By <@{$user_discordid}>");
		$log->addField("Subdivision:", $update_subdivision, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ..'.$redirect);
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function updateApparatus()
{
	$user_discordid = $_SESSION['user_discordid'];
	$update_apparatus = htmlspecialchars($_POST['update_apparatus']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['fireemsperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// INSERT INTO DATABASE
		$result = $pdo->query("UPDATE users SET currapparatus='$update_apparatus' WHERE discordid='$user_discordid'");

	    // LOG IT
		$log = new richEmbed("APPARATUS UPDATED", "By <@{$user_discordid}>");
		$log->addField("Apparatus:", $update_apparatus, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ..'.$redirect);
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function updateIdentifier()
{
	$user_discordid = $_SESSION['user_discordid'];
	$change_unit = htmlspecialchars($_POST['change_unit']);
	$unit_identifier = htmlspecialchars($_POST['unit_identifier']);
	$unit_department = htmlspecialchars($_POST['unit_department']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UPDATE DATABASE
		if ($unit_department != "No Change") {
			$result = $pdo->query("UPDATE users SET identifier='$unit_identifier', department='$unit_department' WHERE ID='$change_unit'");
		} else {
			$result = $pdo->query("UPDATE users SET identifier='$unit_identifier' WHERE ID='$change_unit'");
		}

	    // LOG IT
		$log = new richEmbed("DISPATCH UPDATED UNIT IDENTIFIER", "By <@{$user_discordid}>");
		$log->addField("Identifier:", $unit_identifier, false);
		$log->addField("Department:", $unit_department, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

		header('Location: ..'.$redirect);
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function updateDivision()
{
	$user_discordid = $_SESSION['user_discordid'];
	$change_unit = htmlspecialchars($_POST['change_unit']);
	$change_division = htmlspecialchars($_POST['change_division']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// INSERT INTO DATABASE
		$result = $pdo->query("UPDATE users SET currdivision='$change_division' WHERE ID='$change_unit'");

	    // LOG IT
		$log = new richEmbed("DISPATCH UPDATE UNIT DIVISION", "By <@{$user_discordid}>");
		$log->addField("Division:", $change_division, false);
		$log->addField("Unit ID:", $change_unit, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

		header('Location: ..'.$redirect);
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function updateNote()
{
	$user_discordid = $_SESSION['user_discordid'];
	$note_content = $_POST['note_content'];
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// INSERT INTO DATABASE
	$result = $pdo->query("UPDATE users SET notepad='$note_content' WHERE discordid='$user_discordid'");

    // LOG IT
	$log = new richEmbed("NOTEPAD UPDATED", "By <@{$user_discordid}>");
	$log->addField("Note:", $note_content, false);
	$log = $log->build();
	sendLog($log, DEPARTMENT_LOGS);

	header('Location: ..'.$redirect);
}

function setLEOStatus()
{
	global $LEO_STATUSES;
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];
	$leostatus = htmlspecialchars(str_replace('+', ' ', $_POST['leostatus']));

	if ($_SESSION['leoperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		if ($leostatus == "10-8")
		{
			echo '<script type="text/javascript">start3();</script>';
		}
		if ($leostatus == "10-7")
		{
			echo '<script type="text/javascript">stop3();</script>';
		}

		$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
		$stmt->execute([$user_discordid]);
		$result = $stmt->fetchAll();

		foreach ($result as $row)
		{
			$unit_identifier = $row['identifier'];
		}

		if (empty($unit_identifier))
		{
			echo "ERROR: Identifier Empty.";
		} else {
			if (in_array($leostatus, $LEO_STATUSES) || $leostatus == "10-8" || $leostatus == "10-7") {
				// UDPATE DATABASE
				$result2 = $pdo->query("UPDATE users SET currstatus='$leostatus', currdept='LEO' WHERE discordid='$user_discordid'");
				echo $leostatus;

				// LOG IT
				$log = new richEmbed("LEO STATUS UPDATED", "By <@{$user_discordid}>");
				$log->addField("Status:", $leostatus, false);
				$log = $log->build();
				sendLog($log, STATUS_LOGS);

				//header('Location: ..'.$redirect);
			}
		}
	}
}

function setPanic()
{
	$user_discordid = $_SESSION['user_discordid'];
	$panic = htmlspecialchars($_POST['panic']);
	$paniclocation = htmlspecialchars($_POST['paniclocation']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['leoperms'] == 1 || $_SESSION['fireemsperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
		$stmt->execute([$user_discordid]);
		$getstatus = $stmt->fetchAll();

		foreach ($getstatus as $row)
		{
			$currstatus = $row['currstatus'];
		}

		if ($currstatus == "10-7")
		{
			//header('Location: ..'.$redirect.'?notActive');
			echo '<span class="text-danger">You are currently 10-7</span><br><br>';
		} else {

			// UDPATE DATABASE
			$stmt = $pdo->prepare("UPDATE users SET currpanic=?, currpaniclocation=? WHERE discordid=?");
			$stmt->execute([$panic, $paniclocation, $user_discordid]);

			if ($panic == 1)
			{
				$panicStatus = "On";
			} else {
				$panicStatus = "Off";
			}

		    // LOG IT
			$log = new richEmbed("DEPARTMENT PANIC UPDATED", "By <@{$user_discordid}>");
			$log->addField("Status:", $panicStatus, false);
			$log = $log->build();
			sendLog($log, DEPARTMENT_LOGS);

			//header('Location: ..'.$redirect);
			echo '<span class="text-success">SUCCESS</span><br><br>';
		}
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}

}

function disableUnitPanic()
{
	$user_discordid = $_SESSION['user_discordid'];
	$panic = htmlspecialchars($_GET['panic2']);
	$unitdiscordid = htmlspecialchars($_GET['unitdiscordid']);
	$paniclocation = htmlspecialchars(str_replace('-', ' ', $_GET['paniclocation']));
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['supervisor'] == 1 || $_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currpanic=?, currpaniclocation=? WHERE discordid=?");
		$stmt->execute([$panic, $paniclocation, $unitdiscordid]);

		if ($panic == 1)
		{
			$panicStatus = "On";
		} else {
			$panicStatus = "Off";
		}

	    // LOG IT
		$log = new richEmbed("PANIC DISABLED", "By <@{$user_discordid}> For <@{$unitdiscordid}>");
		$log->addField("Status:", $panicStatus, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ..'.$redirect);
		//echo '<span class="text-success">SUCCESS</span><br><br>';

	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}

}

function unitPing()
{
	$user_discordid = $_SESSION['user_discordid'];
	$unitping = htmlspecialchars($_POST['unitping']);

	if ($_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
		$stmt->execute([$user_discordid]);
		$getstatus = $stmt->fetchAll();

		foreach ($getstatus as $row)
		{
			$currstatus = $row['currstatus'];
		}

		if ($currstatus == "10-7")
		{
			//header('Location: ..'.$redirect.'?notActive');
			echo '<br><span class="text-danger">You are currently 10-7</span>';
		} else {

			// UDPATE DATABASE
			$stmt = $pdo->prepare("UPDATE users SET currping='1' WHERE ID=?");
			$stmt->execute([$unitping]);

		    // LOG IT
			$log = new richEmbed("DISPATCH PINGED A UNIT", "By <@{$user_discordid}>");
			$log = $log->build();
			sendLog($log, DEPARTMENT_LOGS);

			//header('Location: ..'.$redirect);
			echo '<br><span class="text-success">SUCCESSULLY PINGED UNIT</span>';
		}
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}

}

function clearChat()
{
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
		$stmt->execute([$user_discordid]);
		$getstatus = $stmt->fetchAll();

		foreach ($getstatus as $row)
		{
			$currstatus = $row['currstatus'];
		}

		if ($currstatus == "10-7")
		{
			header('Location: ..'.$redirect.'?notActive');
		} else {

			// DELETE AND CREATE NEW FILE
			$my_file = 'log.html';
			unlink($my_file);

			$my_file = 'log.html';
			$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file

		    // LOG IT
			$log = new richEmbed("LIVECHAT CLEARED", "By <@{$user_discordid}>");
			$log = $log->build();
			sendLog($log, DISPATCH_LOGS);

			header('Location: ..'.$redirect);
		}
	}
}

function activateSignal()
{
	$user_discordid = $_SESSION['user_discordid'];
	$activatesignal = htmlspecialchars($_GET['activatesignal']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
		$stmt->execute([$user_discordid]);
		$getstatus = $stmt->fetchAll();

		foreach ($getstatus as $row)
		{
			$currstatus = $row['currstatus'];
		}

		if ($currstatus == "10-7")
		{
			header('Location: ..'.$redirect.'?notActive');
		} else {

			// UDPATE DATABASE
			$stmt = $pdo->prepare("UPDATE users SET currsignal=? WHERE discordid=?");
			$stmt->execute([$activatesignal, $user_discordid]);

		    // LOG IT
			$log = new richEmbed("DISPATCH SIGNAL 100 STARTED", "By <@{$user_discordid}>");
			$log = $log->build();
			sendLog($log, DEPARTMENT_LOGS);

			header('Location: ..'.$redirect);
		}
	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}

}

function disableSignal()
{
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$result = $pdo->query("UPDATE users SET currsignal='0'");

	    // LOG IT
		$log = new richEmbed("DISPATCH SIGNAL 100 ENDED", "By <@{$user_discordid}>");
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ..'.$redirect);

	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}

}

function playSignalTone()
{
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currsound='1' WHERE discordid=?");
		$stmt->execute([$user_discordid]);

	    // LOG IT
		$log = new richEmbed("DISPATCH SIGNAL 100 TONE PLAYED", "By <@{$user_discordid}>");
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ..'.$redirect);

	}
	else 
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}

}

function playFireTone()
{
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];
	$playfiretone = htmlspecialchars($_POST['playfiretone']);

	if ($_SESSION['dispatchperms'] == 1 || $_SESSION['fireemsperms'] == 1)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currfiretone=? WHERE discordid=?");
		$stmt->execute([$playfiretone, $user_discordid]);

	    // LOG IT
		$log = new richEmbed("DISPATCH FIRE TONE PLAYED", "By <@{$user_discordid}>");
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		echo '<span class="text-success">SUCCESS</span>';
	}

}

function searchName()
{
	$nc = htmlspecialchars($_GET['nc']);
	$plateq = $_SESSION['plateq'];
	$weaponq = $_SESSION['weaponq'];

	if (strlen($nc) > 0)
	{
		if ($_SESSION['dispatchperms'] == 1 || $_SESSION['leoperms'] == 1 || $_SESSION['fireemsperms'] == 1 || $_SESSION['courtperms'] == 1 || $_SESSION['dmvperms'] == 1 || $_SESSION['adminperms'] == 1)
		{
			try{
				$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			} catch(PDOException $ex)
			{
				echo "Could not connect -> ".$ex->getMessage();
				die();
			}
	
			// SEARCH DATABASE
			$result = $pdo->query("SELECT * FROM characters WHERE name LIKE '%$nc%' OR ssn LIKE '%$nc%'");
			$charamount = $pdo->query("SELECT * FROM characters WHERE name LIKE '%$nc%' OR ssn LIKE '%$nc%'");
	
			if (sizeof($charamount->fetchAll()) > 0) {
	
	
				if ($plateq != "" & $weaponq != "")
				{
					foreach ($result as $row)
					{
						$char_date = date('d-m-Y', strtotime($row['dob']));
						echo "<p class='text-success'><a style='text-decoration: none;' href=?nameq=" . $row['ID'] . "&plateq=" . $plateq . "&weaponq=".$weaponq."#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
					}
				} else if ($plateq != "") {
					foreach ($result as $row)
					{
						$char_date = date('d-m-Y', strtotime($row['dob']));
						echo "<p class='text-success'><a style='text-decoration: none;' href=?nameq=" . $row['ID'] . "&plateq=" . $plateq . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
					}
				} else if ($weaponq != "") {
					foreach ($result as $row)
					{
						$char_date = date('d-m-Y', strtotime($row['dob']));
						echo "<p class='text-success'><a style='text-decoration: none;' href=?nameq=" . $row['ID'] . "&weaponq=" . $weaponq . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
					}
				} else {
					foreach ($result as $row)
					{
						$char_date = date('d-m-Y', strtotime($row['dob']));
						echo "<p class='text-success'><a style='text-decoration: none;' href=?nameq=" . $row['ID'] . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
					}			
				}
	
			} else {
				echo '<p class="text-success">No Search Results</p>';
			}
		}

	} else {
		echo '<p class="text-success">No Search Results</p>';
	}
}

function deleteChar()
{
	$deletechar = htmlspecialchars($_GET['deletechar']);

	if (strlen($deletechar) > 0)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// SEARCH DATABASE
		$result = $pdo->query("SELECT * FROM characters WHERE name LIKE '%$deletechar%' OR discordid LIKE '%$deletechar%'");
		$charamount = $pdo->query("SELECT * FROM characters WHERE name LIKE '%$deletechar%' OR discordid LIKE '%$deletechar%'");

		if (sizeof($charamount->fetchAll()) > 0) {

			foreach ($result as $row)
			{
				$char_date = date('d-m-Y', strtotime($row['dob']));
				echo "<p>" . $row['name'] . " - " . $char_date ." | <a class='btn btn-outline-danger' href='../actions/department_functions.php?deletechardataid=".$row['ID']."'>Delete Character & All Data!</a></p><hr>";
			}			

		} else {
			echo '<p class="text-success">No Search Results</p>';
		}

	} else {
		echo '<p class="text-success">No Search Results</p>';
	}
}

function deleteCharData() {
	$user_discordid = $_SESSION['user_discordid'];
	$deletechardataid = htmlspecialchars($_GET['deletechardataid']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['adminperms'] == 1) {
		// GET DATA
		$stmt = $pdo->prepare("SELECT * FROM characters WHERE ID=?");
		$stmt->execute([$deletechardataid]);
		$result = $stmt->fetchAll();

		foreach ($result as $row)
		{
			$char_name = $row['name'];
			$char_discordid = $row['discordid'];
		}

		// DELETE DATA
		$result2 = $pdo->query("DELETE FROM arrests WHERE civid='$deletechardataid'");
		$result3 = $pdo->query("DELETE FROM citations WHERE civid='$deletechardataid'");
		$result4 = $pdo->query("DELETE FROM medicalrecords WHERE civid='$deletechardataid'");
		$result5 = $pdo->query("DELETE FROM vehicles WHERE charid='$deletechardataid'");
		$result6 = $pdo->query("DELETE FROM warnings WHERE civid='$deletechardataid'");
		$result7 = $pdo->query("DELETE FROM warrants WHERE civid='$deletechardataid'");
		$result8 = $pdo->query("DELETE FROM weapons WHERE charid='$deletechardataid'");
		$result9 = $pdo->query("DELETE FROM characters WHERE ID='$deletechardataid'");

	    // LOG IT
		$log = new richEmbed("ADMIN | CHARACTER DATA WIPED", "By <@{$user_discordid}>");
		$log->addField("Character Name:", $char_name, false);
		$log->addField("Character Discord ID:", $char_discordid, false);
		$log = $log->build();
		sendLog($log, ADMIN_LOGS);

		header('Location: ../admin/adminDashboard.php#deletecharacter');
	} else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function searchPlate()
{
	$pc = htmlspecialchars($_GET['pc']);
	$nameq = $_SESSION['nameq'];
	$weaponq = $_SESSION['weaponq'];

	if (strlen($pc) > 0)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// SEARCH DATABASE
		$result = $pdo->query("SELECT * FROM vehicles WHERE plate LIKE '%$pc%' OR vin LIKE '%$pc%'");
		$vehamount = $pdo->query("SELECT * FROM vehicles WHERE plate LIKE '%$pc%' OR vin LIKE '%$pc%'");

		if (sizeof($vehamount->fetchAll()) > 0) {

			if ($nameq != "" & $weaponq != "")
			{
				foreach ($result as $row)
				{
					echo "<p class='text-success'><a style='text-decoration: none;' href=?plateq=" . $row['ID'] . "&nameq=" . $nameq . "&weaponq=".$weaponq."#searched>" . $row['plate'] ."</a></p>";
				}
			} else if ($nameq != "") {
				foreach ($result as $row)
				{
					echo "<p class='text-success'><a style='text-decoration: none;' href=?plateq=" . $row['ID'] . "&nameq=" . $nameq ."#searched>" . $row['plate'] ."</a></p>";
				}
			} else if ($weaponq != "") {
				foreach ($result as $row)
				{
					echo "<p class='text-success'><a style='text-decoration: none;' href=?plateq=" . $row['ID'] . "&weaponq=" . $weaponq ."#searched>" . $row['plate'] ."</a></p>";
				}
			} else {
				foreach ($result as $row)
				{
					echo "<p class='text-success'><a style='text-decoration: none;' href=?plateq=" . $row['ID'] . "#searched>" . $row['plate'] ."</a></p>";
				}			
			}

		} else {
			echo '<p class="text-success">No Search Results</p>';
		}

	} else {
		echo '<p class="text-success">No Search Results</p>';
	}
}

function searchWeapon()
{
	$wc = htmlspecialchars($_GET['wc']);
	$nameq = $_SESSION['nameq'];
	$plateq = $_SESSION['plateq'];

	if (strlen($wc) > 0)
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// SEARCH DATABASE
		$result = $pdo->query("SELECT * FROM characters WHERE name LIKE '%$wc%'");
		$charamount = $pdo->query("SELECT * FROM characters WHERE name LIKE '%$wc%'");

		if (sizeof($charamount->fetchAll()) > 0) {

			if ($plateq != "" & $nameq != "")
			{
				foreach ($result as $row)
				{
					$char_date = date('d-m-Y', strtotime($row['dob']));
					echo "<p class='text-success'><a style='text-decoration: none;' href=?weaponq=" . $row['ID'] . "&plateq=" . $plateq . "&nameq=" . $nameq . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
				}
			} else if ($plateq != "") {
				foreach ($result as $row)
				{
					$char_date = date('d-m-Y', strtotime($row['dob']));
					echo "<p class='text-success'><a style='text-decoration: none;' href=?weaponq=" . $row['ID'] . "&plateq=" . $plateq . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
				}
			} else if ($nameq != "") {
				foreach ($result as $row)
				{
					$char_date = date('d-m-Y', strtotime($row['dob']));
					echo "<p class='text-success'><a style='text-decoration: none;' href=?weaponq=" . $row['ID'] . "&nameq=" . $nameq . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
				}
			} else {
				foreach ($result as $row)
				{
					$char_date = date('d-m-Y', strtotime($row['dob']));
					echo "<p class='text-success'><a style='text-decoration: none;' href=?weaponq=" . $row['ID'] . "#searched>" . $row['name'] . " - " . $char_date ."</a></p>";
				}			
			}

		} else {
			echo '<p class="text-success">No Search Results</p>';
		}

	} else {
		echo '<p class="text-success">No Search Results</p>';
	}
}

function searchCall()
{
	$cs = htmlspecialchars($_GET['cs']);

	if ($_SESSION['courtperms'] == 1 || $_SESSION['adminperms'] == 1) {
		if (strlen($cs) > 0)
		{
			try{
				$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			} catch(PDOException $ex)
			{
				echo "Could not connect -> ".$ex->getMessage();
				die();
			}
			$searchlen = strlen($cs);
			// SEARCH DATABASE
			if ($searchlen > 16) {
				$result = $pdo->query("SELECT * FROM activecalls WHERE calltype LIKE '%$cs%' OR attachedunits LIKE '%$cs%' ORDER BY ID DESC");
				$charamount = $pdo->query("SELECT * FROM activecalls WHERE calltype LIKE '%$cs%' OR attachedunits LIKE '%$cs%' ORDER BY ID DESC");
			} else {
				$result = $pdo->query("SELECT * FROM activecalls WHERE calltype LIKE '%$cs%' OR ID = '$cs' ORDER BY ID DESC");
				$charamount = $pdo->query("SELECT * FROM activecalls WHERE calltype LIKE '%$cs%' OR ID = '$cs' ORDER BY ID DESC");
			}

			if (sizeof($charamount->fetchAll()) > 0) {

				foreach ($result as $row)
				{
					echo "<p class='text-success'><a style='text-decoration: none;' href=?callq=" . $row['ID'] . "> <b>ID:</b> " . $row['ID'] . " <br><b>Type:</b> " . $row['calltype'] . " <br><b>Date & Time:</b> " . $row['date'] . " | " . $row['time'] . " </a></p><span class='text-muted'>=====================================</span><br>";
				}			

			} else {
				echo '<p class="text-success">No Search Results</p>';
			}

		} else {
			echo '<p class="text-success">No Search Results</p>';
		}
	}
}


function addWrittenWarning()
{
	$user_discordid = $_SESSION['user_discordid'];
	$warning_name = htmlspecialchars($_POST['warning_name']);
	$warning_id = htmlspecialchars($_POST['warning_id']);
	$warning_offence = htmlspecialchars($_POST['warning_offence']);
	$warning_notes = htmlspecialchars($_POST['warning_notes']);
	$warning_date = date('Y-m-d');
	$warning_time = date('H:i:s');
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$warning_signing_off = $row['identifier'];
		$currstatus = $row['currstatus'];
	}

	if ($currstatus == "10-7")
	{
		header('Location: ..'.$redirect.'?notActive');
	} 
	else if ($_SESSION['leoperms'] == 1 || $_SESSION['courtperms'] == 1) {
		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO warnings (civid, civname, unitidentifier, unitdiscordid, date, time, offences, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($warning_id, $warning_name, $warning_signing_off, $user_discordid, $warning_date, $warning_time, $warning_offence, $warning_notes));

	    // LOG IT
		$log = new richEmbed("NEW WARNING", "By <@{$user_discordid}>");
		$log->addField("Issued To:", $warning_name, false);
		$log->addField("Offences:", $warning_offence, false);
		$log->addField("Notes:", $warning_notes, false);
		$log->addField("Date:", $warning_date, false);
		$log->addField("Time:", $warning_time, false);
		$log->addField("Signed Off:", $warning_signing_off, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		header('Location: ..'.$redirect.'?nameq='.$warning_id.'#searched');
	}
	else {

		header('Location: ../index.php?notAuthorisedDepartment');

	}
}

function addCitation()
{
	$user_discordid = $_SESSION['user_discordid'];
	$citation_name = htmlspecialchars($_POST['citation_name']);
	$citation_id = htmlspecialchars($_POST['citation_id']);
	$citation_offence = htmlspecialchars($_POST['citation_offence']);
	$citation_fine = htmlspecialchars($_POST['citation_fine']);
	$citation_notes = htmlspecialchars($_POST['citation_notes']);
	$citation_date = date('Y-m-d');
	$citation_time = date('H:i:s');
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$citation_signing_off = $row['identifier'];
		$currstatus = $row['currstatus'];
	}

	if ($currstatus == "10-7")
	{
		header('Location: ..'.$redirect.'?notActive');
	}

	if ($_SESSION['leoperms'] == 1 || $_SESSION['courtperms'] == 1) {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO citations (civid, civname, unitidentifier, unitdiscordid, date, time, offences, fine, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($citation_id, $citation_name, $citation_signing_off, $user_discordid, $citation_date, $citation_time, $citation_offence, $citation_fine, $citation_notes));

	    // LOG IT
		$log = new richEmbed("NEW CITATION", "By <@{$user_discordid}>");
		$log->addField("Issued To:", $citation_name, false);
		$log->addField("Offences:", $citation_offence, false);
		$log->addField("Fine:", $citation_fine, false);
		$log->addField("Notes:", $citation_notes, false);
		$log->addField("Date:", $citation_date, false);
		$log->addField("Time:", $citation_time, false);
		$log->addField("Signed Off:", $citation_signing_off, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		header('Location: ..'.$redirect.'?nameq='.$citation_id.'#searched');
	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}


function addArrest()
{
	$user_discordid = $_SESSION['user_discordid'];
	$arrest_name = htmlspecialchars($_POST['arrest_name']);
	$arrest_id = htmlspecialchars($_POST['arrest_id']);
	$arrest_type = htmlspecialchars($_POST['arrest_type']);
	$arrest_reason = htmlspecialchars($_POST['arrest_reason']);
	$arrest_fine = htmlspecialchars($_POST['arrest_fine']);
	$arrest_jailtime = htmlspecialchars($_POST['arrest_jailtime']);
	$arrest_notes = htmlspecialchars($_POST['arrest_notes']);
	$arrest_date = date('Y-m-d');
	$arrest_time = date('H:i:s');
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$arrest_signing_off = $row['identifier'];
		$currstatus = $row['currstatus'];
	}

	if ($currstatus == "10-7")
	{
		header('Location: ..'.$redirect.'?notActive');
	}

	if ($_SESSION['leoperms'] == 1 || $_SESSION['courtperms'] == 1) {
		// SUSPEND LICENSE IF FELONY
		if ($arrest_type == "Felony")
		{
			$stmt = $pdo->prepare("UPDATE characters SET weapons='Revoked' WHERE ID=?");
			$stmt->execute([$arrest_id]);
		}

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO arrests (civid, civname, unitidentifier, unitdiscordid, date, time, arresttype, reason, fine, jailtime, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($arrest_id, $arrest_name, $arrest_signing_off, $user_discordid, $arrest_date, $arrest_time, $arrest_type, $arrest_reason, $arrest_fine, $arrest_jailtime, $arrest_notes));

	    // LOG IT
		$log = new richEmbed("NEW ARREST", "By <@{$user_discordid}>");
		$log->addField("Issued To:", $arrest_name, false);
		$log->addField("Type:", $arrest_type, false);
		$log->addField("Reason:", $arrest_reason, false);
		$log->addField("Fine:", $arrest_fine, false);
		$log->addField("Jail Time:", $arrest_jailtime, false);
		$log->addField("Notes:", $arrest_notes, false);
		$log->addField("Date:", $arrest_date, false);
		$log->addField("Time:", $arrest_time, false);
		$log->addField("Signed Off:", $arrest_signing_off, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		header('Location: ..'.$redirect.'?nameq='.$arrest_id.'#searched');
	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}


function addWarrant()
{
	$user_discordid = $_SESSION['user_discordid'];
	$warrant_details = htmlspecialchars($_POST['warrant_details']);
	$warrant_requestingunit = htmlspecialchars($_POST['warrant_requestingunit']);
	$warrant_id = htmlspecialchars($_POST['warrant_id']);
	$warrant_date = date('Y-m-d');
	$warrant_time = date('H:i:s');
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$warrant_signing_off = $row['identifier'];
	}

	if ($_SESSION['courtperms'] == 1 || $_SESSION['supervisor'] == 1) {
		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO warrants (civid, unitidentifier, unitdiscordid, date, time, details, requestingunit) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($warrant_id, $warrant_signing_off, $user_discordid, $warrant_date, $warrant_time, $warrant_details, $warrant_requestingunit));

	    // LOG IT
		$log = new richEmbed("NEW WARRANT", "By <@{$user_discordid}>");
		$log->addField("Details:", $warrant_details, false);
		$log->addField("Date:", $warrant_date, false);
		$log->addField("Time:", $warrant_time, false);
		$log->addField("Requesting Unit:", $warrant_requestingunit, false);
		$log->addField("Signed Off:", $warrant_signing_off, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		header('Location: ..'.$redirect.'?nameq='.$warrant_id.'#searched');
	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}


function setFIREEMSStatus()
{
	global $FIREEMS_STATUSES;
	$user_discordid = $_SESSION['user_discordid'];
	$fireemsstatus = htmlspecialchars(str_replace('+', ' ', $_POST['fireemsstatus']));
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['fireemsperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		echo $fireemsstatus;
		if ($fireemsstatus == "10-8")
		{
			echo '<script type="text/javascript">start3();</script>';
		}
		if ($fireemsstatus == "10-7")
		{
			echo '<script type="text/javascript">stop3();</script>';
		}

		if (in_array($fireemsstatus, $FIREEMS_STATUSES) || $fireemsstatus == "10-8" || $fireemsstatus == "10-7") {
			// UDPATE DATABASE
			$stmt = $pdo->prepare("UPDATE users SET currstatus=?, currdept='FIREEMS' WHERE discordid=?");
			$stmt->execute([$fireemsstatus, $user_discordid]);

			// LOG IT
			$log = new richEmbed("FIRE/EMS STATUS UPDATED", "By <@{$user_discordid}>");
			$log->addField("Status:", $fireemsstatus, false);
			$log = $log->build();
			sendLog($log, STATUS_LOGS);

			//header('Location: ..'.$redirect);
		}
	}
}

function addMedicalRecord()
{
	$user_discordid = $_SESSION['user_discordid'];
	$medical_name = htmlspecialchars($_POST['medical_name']);
	$medical_id = htmlspecialchars($_POST['medical_id']);
	$medical_details = htmlspecialchars($_POST['medical_details']);
	$medical_date = date('Y-m-d');
	$medical_time = date('H:i:s');

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$medical_signing_off = $row['identifier'];
		$currstatus = $row['currstatus'];
	}

	if ($_SESSION['fireemsperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else if ($currstatus == "10-7")
	{
		header('Location: ../fireems/fireemsDashboard.php?notActive');
	}
	else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO medicalrecords (civid, civname, unitidentifier, unitdiscordid, date, time, details) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($medical_id, $medical_name, $medical_signing_off, $user_discordid, $medical_date, $medical_time, $medical_details));

	    // LOG IT
		$log = new richEmbed("NEW MEDICAL RECORD", "By <@{$user_discordid}>");
		$log->addField("Issued To:", $medical_name, false);
		$log->addField("Details:", $medical_details, false);
		$log->addField("Date:", $medical_date, false);
		$log->addField("Time:", $medical_time, false);
		$log->addField("Signed Off:", $medical_signing_off, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ../fireems/fireemsDashboard.php?nameq='.$medical_id.'#searched');

	}
}


function setDispatchStatus()
{
	$user_discordid = $_SESSION['user_discordid'];
	$dispatchstatus = htmlspecialchars($_POST['dispatchstatus']);
	$redirect = $_SESSION['redirect'];

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		echo $dispatchstatus;
		if ($dispatchstatus == "10-8")
		{
			echo '<script type="text/javascript">start3();</script>';
		}
		if ($dispatchstatus == "10-7")
		{
			echo '<script type="text/javascript">stop3();</script>';
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currstatus=?, currdept='DISPATCH' WHERE discordid=?");
		$stmt->execute([$dispatchstatus, $user_discordid]);

	    // LOG IT
		$log = new richEmbed("DISPATCH STATUS UPDATED", "By <@{$user_discordid}>");
		$log->addField("Status:", $dispatchstatus, false);
		$log = $log->build();
		sendLog($log, STATUS_LOGS);

		//header('Location: ..'.$redirect);
	}
}


function deleteBolo()
{
	$user_discordid = $_SESSION['user_discordid'];
	$boloid = htmlspecialchars($_POST['boloid']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$currstatus = $row['currstatus'];
	}

	if ($currstatus == "10-7") {
		echo '<span class="text-danger">You are currently 10-7</span>';
	}
	else if ($_SESSION['dispatchperms'] == 1 || $_SESSION['supervisor'] == 1) {
		// DELETE INTO DATABASE
		$stmt = $pdo->prepare("DELETE FROM bolos WHERE ID=? ");
		$stmt->execute([$boloid]);

	    // LOG IT
		$log = new richEmbed("BOLO DELETED", "By <@{$user_discordid}>");
		$log->addField("ID:", $boloid, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		echo '<span class="text-success">SUCCESS</span>';
	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function updateLicenseCourt()
{
	$user_discordid = $_SESSION['user_discordid'];
	$courtlicenseid = htmlspecialchars($_POST['courtlicenseid']);
	$courtlicensetype = htmlspecialchars($_POST['courtlicensetype']);
	$courtlicensestatus = htmlspecialchars($_POST['courtlicensestatus']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($courtlicensestatus == "-")
	{
		echo '<span class="text-danger">NOT VALID STATUS</span>';
	} else if ($courtlicensetype == "-") {
		echo '<span class="text-danger">NOT VALID STATUS</span>';
	} else if ($_SESSION['courtperms'] == 1 || $_SESSION['leoperms'] == 1 || $_SESSION['dmvperms'] == 1) {

		// UPDATE DATABASE
		$stmt = $pdo->prepare("UPDATE characters SET $courtlicensetype=? WHERE ID='$courtlicenseid'");
		$stmt->execute([$courtlicensestatus]);

	    // LOG IT
		$log = new richEmbed("LICENSE UPDATED BY COURT/LEO/DMV", "By <@{$user_discordid}>");
		$log->addField("CIV ID:", $courtlicenseid, false);
		$log->addField("License Type:", $courtlicensetype, false);
		$log->addField("License Status:", $courtlicensestatus, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		echo 'SUCCESS';

	} else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function addPoints()
{
	$user_discordid = $_SESSION['user_discordid'];
	$addpoints = htmlspecialchars($_POST['addpoints']);
	$driversid = htmlspecialchars($_POST['driversid']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['courtperms'] == 1 || $_SESSION['leoperms'] == 1) {

		$stmt = $pdo->prepare("SELECT * FROM characters WHERE ID=?");
		$stmt->execute([$driversid]);
		$result = $stmt->fetchAll();
		foreach ($result as $row)
		{
			$driverspoints = $row['driverspoints'];
		}

		if ($driverspoints+$addpoints >= POINTS_FOR_DRIVING_SUSPENSION)
		{
			$suspenddrivers = $pdo->query("UPDATE characters SET drivers='Suspended' WHERE ID='$driversid'");
		}

		// UPDATE DATABASE
		$result2 = $pdo->query("UPDATE characters SET driverspoints=driverspoints+$addpoints WHERE ID='$driversid'");

	    // LOG IT
		$log = new richEmbed("DRIVERS POINTS ADDED BY LEO/COURT", "By <@{$user_discordid}>");
		$log->addField("CIV ID:", $driversid, false);
		$log->addField("Points:", $addpoints, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

	} else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}	
}

function removePoints()
{
	$user_discordid = $_SESSION['user_discordid'];
	$removepoints = htmlspecialchars($_POST['removepoints']);
	$driversid = htmlspecialchars($_POST['driversid']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['courtperms'] == 1 || $_SESSION['leoperms'] == 1) {

		// UPDATE DATABASE
		$result = $pdo->query("UPDATE characters SET driverspoints=driverspoints-$removepoints WHERE ID='$driversid'");

	    // LOG IT
		$log = new richEmbed("DRIVERS POINTS REMOVED BY COURT", "By <@{$user_discordid}>");
		$log->addField("CIV ID:", $driversid, false);
		$log->addField("Points:", $removepoints, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

	} else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}	
}

function showSupervisor()
{
	$user_discordid = $_SESSION['user_discordid'];
	$showsupervisor = htmlspecialchars($_GET['showsupervisor']);
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['supervisor'] != 1)
	{
		header('Location: ..'.$redirect);
	} else {

		// UPDATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET showsupervisor=? WHERE discordid='$user_discordid'");
		$stmt->execute([$showsupervisor]);

		if ($showsupervisor == 1)
		{
			$supervisorStatus = "Show";
		} else {
			$supervisorStatus = "Hide";
		}

	    // LOG IT
		$log = new richEmbed("SUPERVISOR STATUS UPDATED", "By <@{$user_discordid}>");
		$log->addField("Status:", $supervisorStatus, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		header('Location: ..'.$redirect);

	}
}

function delete911()
{
	$user_discordid = $_SESSION['user_discordid'];
	$delete911 = htmlspecialchars($_GET['delete911']);
	$redirect = $_SESSION['redirect'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	} else {
		$stmt = $pdo->prepare("DELETE FROM 911call WHERE ID=? ");
		$stmt->execute([$delete911]);

		header('Location: ..'.$redirect);
	}
}

function deleteWarning()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletewarning = htmlspecialchars($_POST['deletewarning']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['courtperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("DELETE FROM warnings WHERE ID=? ");
		$stmt->execute([$deletewarning]);


	    // LOG IT
		$log = new richEmbed("WARNING DELETED", "By <@{$user_discordid}>");
		$log->addField("ID:", $deletewarning, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		echo 'SUCCESS';
	}
}

function deleteCitation()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletecitation = htmlspecialchars($_POST['deletecitation']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['courtperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("DELETE FROM citations WHERE ID=? ");
		$stmt->execute([$deletecitation]);


	    // LOG IT
		$log = new richEmbed("CITATION DELETED", "By <@{$user_discordid}>");
		$log->addField("ID:", $deletecitation, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		echo 'SUCCESS';
	}
}

function deleteArrest()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletearrest = htmlspecialchars($_POST['deletearrest']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['courtperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("DELETE FROM arrests WHERE ID=? ");
		$stmt->execute([$deletearrest]);


	    // LOG IT
		$log = new richEmbed("ARREST DELETED", "By <@{$user_discordid}>");
		$log->addField("ID:", $deletearrest, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		echo 'SUCCESS';
	}
}

function deleteWarrant()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletewarrant = htmlspecialchars($_POST['deletewarrant']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['courtperms'] == 1 || $_SESSION['supervisor'] == 1) {
		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("DELETE FROM warrants WHERE ID=? ");
		$stmt->execute([$deletewarrant]);


	    // LOG IT
		$log = new richEmbed("WARRANT DELETED", "By <@{$user_discordid}>");
		$log->addField("ID:", $deletewarrant, false);
		$log = $log->build();
		sendLog($log, LAW_BREAKING_LOGS);

		echo 'SUCCESS';
	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function deleteMedical()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletemedical = htmlspecialchars($_POST['deletemedical']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['fireemsperms'] == 1 || $_SESSION['courtperms'] == 1) {

		// DELETE INTO DATABASE
		$stmt = $pdo->prepare("DELETE FROM medicalrecords WHERE ID=? ");
		$stmt->execute([$deletemedical]);


	    // LOG IT
		$log = new richEmbed("MEDICAL RECORD DELETED", "By <@{$user_discordid}>");
		$log->addField("ID:", $deletemedical, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		echo 'SUCCESS';

	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}	

}

function deleteCall()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletecallid = htmlspecialchars($_POST['deletecallid']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$currstatus = $row['currstatus'];
	}

	if ($_SESSION['dispatchperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else if ($currstatus == "10-7")
	{
		//header('Location: ../dispatch/dispatchDashboard.php?notActive');
		echo '<span class="text-danger">You are currently 10-7</span>';
	}
	else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("UPDATE activecalls SET status='1' WHERE ID=?");
		$stmt->execute([$deletecallid]);

	    // LOG IT
		$log = new richEmbed("CALL ENDED", "By <@{$user_discordid}>");
		$log->addField("ID:", $deletecallid, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		echo '<span class="text-success">SUCCESS</span>';
	}
}

function updateNarrative()
{
	$user_discordid = $_SESSION['user_discordid'];
	$narrative = htmlspecialchars($_POST['narrative']);
	$calltype = htmlspecialchars($_POST['calltype']);
	$callid = htmlspecialchars($_POST['callid']);
	$location = htmlspecialchars($_POST['location']);
	$postal = htmlspecialchars($_POST['postal']);
	$other = htmlspecialchars($_POST['other']);

	if (!empty($other)) {
		$calltype = $other;
	}

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['dispatchperms'] != 1) {
		echo '<span class="text-danger">NOT ALLOWED</span>';
	}
	else {

		// UPDATE INTO DATABASE
		$stmt = $pdo->prepare("UPDATE activecalls SET narrative=?, calltype=?, location=?, postal=? WHERE ID=?");
		$stmt->execute([$narrative, $calltype, $location, $postal, $callid]);


	    // LOG IT
		$log = new richEmbed("CALL UPDATED", "By <@{$user_discordid}>");
		$log->addField("Narative:", $narrative, false);
		$log->addField("Call Type:", $calltype, false);
		$log->addField("Location:", $location, false);
		$log->addField("Postal:", $postal, false);
		$log->addField("Call #:", $callid, false);
		$log = $log->build();
		sendLog($log, DEPARTMENT_LOGS);

		echo '<span class="text-success">SUCCESS</span>';
	}
}

function addBolo()
{
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];
	$bolo_type = htmlspecialchars($_POST['bolo_type']);
	$bolo_details = htmlspecialchars($_POST['bolo_details']);
	$bolo_plate = htmlspecialchars($_POST['bolo_plate']);
	$bolo_date = date('Y-m-d');
	$bolo_time = date('H:i:s');

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$bolo_identifier = $row['identifier'];
		$currstatus = $row['currstatus'];
	}

	if ($currstatus == "10-7")
	{
		header('Location: ..'.$redirect.'?notActive');
	}
	else if ($_SESSION['dispatchperms'] == 1 || $_SESSION['leoperms'] == 1) {

		if ($bolo_plate == "")
		{
			$bolo_plate = "N/A";
		}

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO bolos (unitidentifier, unitdiscordid, date, time, type, details, plate) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($bolo_identifier, $user_discordid, $bolo_date, $bolo_time, $bolo_type, $bolo_details, $bolo_plate));

	    // LOG IT
		$log = new richEmbed("NEW BOLO", "By <@{$user_discordid}>");
		$log->addField("Type:", $bolo_type, false);
		$log->addField("Details:", $bolo_details, false);
		$log->addField("Plate:", $bolo_plate, false);
		$log->addField("Date:", $bolo_date, false);
		$log->addField("Time:", $bolo_time, false);
		$log->addField("Identifier:", $bolo_identifier, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

		header('Location: ..'.$redirect);
	}
	else {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
}

function addCall()
{
	$user_discordid = $_SESSION['user_discordid'];
	$call_type = htmlspecialchars($_POST['call_type']);
	$call_location = htmlspecialchars($_POST['call_location']);
	$call_postal = htmlspecialchars($_POST['call_postal']);
	$call_narrative = htmlspecialchars($_POST['call_narrative']);
	$call_initiatingunit = htmlspecialchars($_POST['call_initiatingunit']);
	$call_type_other = htmlspecialchars($_POST['call_type_other']);
	$call_date = date('Y-m-d');
	$call_time = date('H:i:s');

	if ($call_type == "Other") {
		$call_type = $call_type_other;
	}

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// GET UNIT IDENTIFIER
	$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
	$stmt->execute([$user_discordid]);
	$unit = $stmt->fetchAll();
	foreach ($unit as $row)
	{
		$currstatus = $row['currstatus'];
	}

	if ($_SESSION['dispatchperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else if ($currstatus == "10-7")
	{
		header('Location: ../dispatch/dispatchDashboard.php?notActive');
	}
	else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO activecalls (date, time, calltype, location, postal, narrative, attachedunits) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$result = $stmt->execute(array($call_date, $call_time, $call_type, $call_location, $call_postal, $call_narrative, $call_initiatingunit));

	    // LOG IT
		$log = new richEmbed("NEW CALL", "By <@{$user_discordid}>");
		$log->addField("Type:", $call_type, false);
		$log->addField("Location:", $call_location, false);
		$log->addField("Postal:", $call_postal, false);
		$log->addField("Initiating Unit:", $call_initiatingunit, false);
		$log->addField("Date:", $call_date, false);
		$log->addField("Time:", $call_time, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

		header('Location: ../dispatch/dispatchDashboard.php');

	}
}

function addPermission()
{
	$user_discordid = $_SESSION['user_discordid'];
	$roleid = htmlspecialchars($_POST['roleid']);
	$permissiontype = htmlspecialchars($_POST['permissiontype']);
	$permissionidentifier = htmlspecialchars(str_replace('-', ' ', $_POST['permissionidentifier']));

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}
	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {

			// INSERT INTO DATABASE
			$stmt = $pdo->prepare("INSERT INTO permissions (roleid, permission, identifier) VALUES (?, ?, ?)");
			$result = $stmt->execute(array($roleid, $permissiontype, $permissionidentifier));

			// LOG IT
			$log = new richEmbed("ADDED PERMISSION", "By <@{$user_discordid}>");
			$log->addField("Role ID:", $roleid, false);
			$log->addField("Permission Type:", $permissiontype, false);
			$log->addField("Permission Identifier:", $permissionidentifier, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function deletePermission()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletepermission = htmlspecialchars($_POST['deletepermission']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}
	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {
			$stmt = $pdo->prepare("SELECT * FROM permissions WHERE ID=?");
			$stmt->execute([$deletepermission]);
			$result = $stmt->fetchAll();
			foreach ($result as $row) {
				$roleid = $row['roleid'];
				$permission = $row['permission'];
			}
			
			// UPDATE INTO DATABASE
			$stmt = $pdo->prepare("DELETE FROM permissions WHERE ID=? ");
			$stmt->execute([$deletepermission]);

			// LOG IT
			$log = new richEmbed("DELETED PERMISSION", "By <@{$user_discordid}>");
			$log->addField("Role ID:", $roleid, false);
			$log->addField("Permission Type:", $permission, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function addDivision()
{
	$user_discordid = $_SESSION['user_discordid'];
	$divisiontype = htmlspecialchars($_POST['divisiontype']);
	$divisionname = htmlspecialchars(str_replace('-', ' ', $_POST['divisionname']));

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}
	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {

			// INSERT INTO DATABASE
			$stmt = $pdo->prepare("INSERT INTO divisions (name, type) VALUES (?, ?)");
			$result = $stmt->execute(array($divisionname, $divisiontype));

			// LOG IT
			$log = new richEmbed("ADDED SUBDIVISION", "By <@{$user_discordid}>");
			$log->addField("Name:", $divisionname, false);
			$log->addField("Type:", $divisiontype, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function deleteDivision()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletedivision = htmlspecialchars($_POST['deletedivision']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {
			$stmt = $pdo->prepare("SELECT * FROM divisions WHERE ID=?");
			$stmt->execute([$deletedivision]);
			$result = $stmt->fetchAll();
			foreach ($result as $row) {
				$name = $row['name'];
				$type = $row['type'];
			}
			
			// UPDATE INTO DATABASE
			$stmt = $pdo->prepare("DELETE FROM divisions WHERE ID=? ");
			$stmt->execute([$deletedivision]);

			// LOG IT
			$log = new richEmbed("DELETED SUBDIVISION", "By <@{$user_discordid}>");
			$log->addField("Name:", $name, false);
			$log->addField("Type:", $type, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function deleteBan()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deleteban = htmlspecialchars($_POST['deleteban']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {
			$stmt = $pdo->prepare("SELECT * FROM bans WHERE ID=?");
			$stmt->execute([$deleteban]);
			$result = $stmt->fetchAll();
			foreach ($result as $row) {
				$name = $row['name'];
				$discordid = $row['discordid'];
			}
			
			// UPDATE INTO DATABASE
			$stmt = $pdo->prepare("DELETE FROM bans WHERE ID=? ");
			$stmt->execute([$deleteban]);

			// LOG IT
			$log = new richEmbed("DELETED BAN", "By <@{$user_discordid}>");
			$log->addField("Name:", $name, false);
			$log->addField("Discord ID:", $discordid, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function deletePenal()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deletepenal = htmlspecialchars($_POST['deletepenal']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {
			$stmt = $pdo->prepare("SELECT * FROM penalcode WHERE ID=?");
			$stmt->execute([$deletepenal]);
			$result = $stmt->fetchAll();
			foreach ($result as $row) {
				$title = $row['title'];
			}
			
			// UPDATE INTO DATABASE
			$stmt = $pdo->prepare("DELETE FROM penalcode WHERE ID=? ");
			$stmt->execute([$deletepenal]);

			// LOG IT
			$log = new richEmbed("DELETED PENALCODE", "By <@{$user_discordid}>");
			$log->addField("Title:", $title, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function deleteImage()
{
	$user_discordid = $_SESSION['user_discordid'];
	$deleteimage = htmlspecialchars($_POST['deleteimage']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if (!empty($_POST['token']) || hash_equals($_SESSION['token'], $_POST['token'])) {
		if ($_SESSION['adminperms'] != 1) {
			header('Location: ../index.php?notAuthorisedDepartment');
		}
		else {
			$stmt = $pdo->prepare("SELECT * FROM gallery WHERE ID=?");
			$stmt->execute([$deleteimage]);
			$result = $stmt->fetchAll();
			foreach ($result as $row) {
				$link = $row['link'];
			}
			
			// UPDATE INTO DATABASE
			$stmt = $pdo->prepare("DELETE FROM gallery WHERE ID=? ");
			$stmt->execute([$deleteimage]);

			// LOG IT
			$log = new richEmbed("DELETED GALLERY IMAGE", "By <@{$user_discordid}>");
			$log->addField("Link:", $link, false);
			$log = $log->build();
			sendLog($log, ADMIN_LOGS);

			echo 'SUCCESS';

		}
	}
}

function banUser()
{
	$user_discordid = $_SESSION['user_discordid'];
	$ban_discordid = htmlspecialchars($_POST['ban_discordid']);
	$ban_name = htmlspecialchars($_POST['ban_name']);
	$ban_reason = htmlspecialchars($_POST['ban_reason']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['adminperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	} else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO bans (discordid, name, reason) VALUES (?, ?, ?)");
		$result = $stmt->execute(array($ban_discordid, $ban_name, $ban_reason));

	    // LOG IT
		$log = new richEmbed("NEW BAN", "By <@{$user_discordid}>");
		$log->addField("Discord ID:", $ban_discordid, false);
		$log->addField("Name:", $ban_name, false);
		$log->addField("Reason:", $ban_reason, false);
		$log = $log->build();
		sendLog($log, ADMIN_LOGS);

		header('Location: ../admin/adminDashboard.php');

	}
}

function addPenalcode()
{
	$user_discordid = $_SESSION['user_discordid'];
	$penal_title = htmlspecialchars($_POST['penal_title']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['adminperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	} else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO penalcode (title) VALUES (?)");
		$result = $stmt->execute(array($penal_title));

	    // LOG IT
		$log = new richEmbed("NEW PENAL CODE", "By <@{$user_discordid}>");
		$log->addField("Title:", $penal_title, false);
		$log = $log->build();
		sendLog($log, ADMIN_LOGS);

		header('Location: ../admin/adminDashboard.php#penalcode');

	}
}

function addGallery()
{
	$user_discordid = $_SESSION['user_discordid'];
	$gallery_link = htmlspecialchars($_POST['gallery_link']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['adminperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	} else {

		// INSERT INTO DATABASE
		$stmt = $pdo->prepare("INSERT INTO gallery (link) VALUES (?)");
		$result = $stmt->execute(array($gallery_link));

	    // LOG IT
		$log = new richEmbed("NEW GALLERY IMAGE", "By <@{$user_discordid}>");
		$log->addField("Link:", $gallery_link, false);
		$log = $log->build();
		sendLog($log, ADMIN_LOGS);

		header('Location: ../admin/adminDashboard.php#gallery');

	}
}

function markOffDuty() 
{
	$user_discordid = $_SESSION['user_discordid'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if ($_SESSION['dispatchperms'] != 1) {
		header('Location: ../index.php?notAuthorisedDepartment');
	} else {

		// UPDATE DATABASE
		$result = $pdo->query("UPDATE users SET currstatus='10-7'");

	    // LOG IT
		$log = new richEmbed("ALL UNITS MARKED 10-7", "By <@{$user_discordid}>");
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

		header('Location: ../dispatch/dispatchDashboard.php');

	}
}

function assignUnits()
{
	$user_discordid = $_SESSION['user_discordid'];
	$su = $_GET['su'];
	$cid = $_GET['cid'];

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		if ($cid != "None")
		{
			$result2 = $pdo->query("SELECT * FROM activecalls WHERE ID='$cid'");
		} else {
			$result2 = $pdo->query("SELECT * FROM activecalls WHERE attachedunits LIKE '%$su%' AND status='0'");
		}

		foreach ($result2 as $row)
		{
			$currentactive = $row['attachedunits'];
			$currentid = $row['ID'];
		}

		if ($cid != "None")
		{
			$updatedunits = $currentactive.', '.$su;
		} else {
			$updatedunits = str_replace($su, '', $currentactive);
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE activecalls SET attachedunits=? WHERE ID=?");
		$stmt->execute([$updatedunits, $currentid]);

		$stmt = $pdo->prepare("SELECT * FROM users WHERE discordid=?");
		$stmt->execute([$su]);
		$unitname = $stmt->fetchAll();
		foreach ($unitname as $row)
		{
			$su_name = $row['identifier'];
		}

	    // LOG IT
		$log = new richEmbed("DISPATCH ATTACHED/REMOVED UNIT FROM CALL", "By <@{$user_discordid}>");
		$log->addField("Unit:", $su_name, false);
		$log->addField("Call #:", $cid, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

	}
}

function changeUnitStatus()
{
	$user_discordid = $_SESSION['user_discordid'];
	$su = $_GET['su'];
	$ss = $_GET['ss'];

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currstatus=? WHERE ID=?");
		$stmt->execute([$ss, $su]);

		$stmt = $pdo->prepare("SELECT * FROM users WHERE ID=?");
		$stmt->execute([$su]);
		$unitname = $stmt->fetchAll();
		foreach ($unitname as $row)
		{
			$su_name = $row['identifier'];
		}

	    // LOG IT
		$log = new richEmbed("DISPATCH CHANGED UNIT STATUS", "By <@{$user_discordid}>");
		$log->addField("Unit:", $su_name, false);
		$log->addField("Status:", $ss, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

	}
}

function changeUnitDivision()
{
	$user_discordid = $_SESSION['user_discordid'];
	$su = $_GET['su'];
	$sdiv = $_GET['sdiv'];

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currdivision=? WHERE ID=?");
		$stmt->execute([$sdiv, $su]);

		$stmt = $pdo->prepare("SELECT * FROM users WHERE ID=?");
		$stmt->execute([$su]);
		$unitname = $stmt->fetchAll();
		foreach ($unitname as $row)
		{
			$su_name = $row['identifier'];
		}

	    // LOG IT
		$log = new richEmbed("DISPATCH CHANGED UNIT DIVISION", "By <@{$user_discordid}>");
		$log->addField("Unit:", $su_name, false);
		$log->addField("Division:", $sdiv, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

	}
}

function changeUnitApparatus()
{
	$user_discordid = $_SESSION['user_discordid'];
	$su = $_GET['su'];
	$sdiv = $_GET['sdiv'];

	if ($_SESSION['dispatchperms'] != 1)
	{
		header('Location: ../index.php?notAuthorisedDepartment');
	}
	else 
	{

		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}

		// UDPATE DATABASE
		$stmt = $pdo->prepare("UPDATE users SET currapparatus=? WHERE ID=?");
		$stmt->execute([$sdiv, $su]);

		$stmt = $pdo->prepare("SELECT * FROM users WHERE ID=?");
		$stmt->execute([$su]);
		$unitname = $stmt->fetchAll();
		foreach ($unitname as $row)
		{
			$su_name = $row['identifier'];
		}

	    // LOG IT
		$log = new richEmbed("DISPATCH CHANGED UNIT APPARATUS", "By <@{$user_discordid}>");
		$log->addField("Unit:", $su_name, false);
		$log->addField("Apparatus:", $sdiv, false);
		$log = $log->build();
		sendLog($log, DISPATCH_LOGS);

	}
}

function markDead()
{
	$redirect = $_SESSION['redirect'];
	$nameq = $_SESSION['nameq'];
	$user_discordid = $_SESSION['user_discordid'];
	$ems_markdead = $_GET['ems_markdead'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

    // LOG IT
	$log = new richEmbed("EMS MARKED CHARACTER DEAD", "By <@{$user_discordid}>");
	$log = $log->build();
	sendLog($log, DEPARTMENT_LOGS);

	// UPDATE DATABASE
    $stmt = $pdo->prepare("UPDATE characters SET dead=? WHERE ID='$ems_markdead'");
    $stmt->execute([1]);

	header('Location: ..'.$redirect.'?nameq='.$nameq);
}

function markAlive()
{
	$redirect = $_SESSION['redirect'];
	$nameq = $_SESSION['nameq'];
	$user_discordid = $_SESSION['user_discordid'];
	$ems_markalive = $_GET['ems_markalive'];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

    // LOG IT
	$log = new richEmbed("EMS MARKED CHARACTER ALIVE", "By <@{$user_discordid}>");
	$log = $log->build();
	sendLog($log, DEPARTMENT_LOGS);

	// UPDATE DATABASE
    $stmt = $pdo->prepare("UPDATE characters SET dead=? WHERE ID='$ems_markalive'");
    $stmt->execute([0]);

	header('Location: ..'.$redirect.'?nameq='.$nameq);
}
?>