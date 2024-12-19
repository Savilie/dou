<?php
require_once(__DIR__ . "/discord_functions.php");
require_once(__DIR__ . "/../config.php");
session_start();

if (isset($_POST['create_character_btn']))
{
    createCharacter();
}

if (isset($_POST['update_character_btn']))
{
    updateCharacter();
}

if (isset($_POST['markdead_btn']))
{
    markDead();
}

if (isset($_POST['delete_character_btn']))
{
    deleteCharacter();
}

if (isset($_POST['update_license_btn']))
{
    updateLicense();
}

if (isset($_POST['licensetype']))
{
    updateLicense();
}

if (isset($_POST['add_vehicle_btn']))
{
    addVehicle();
}

if (isset($_POST['editprofile_btn']))
{
    editProfile();
}

if (isset($_GET['vehicleID']))
{
    removeVehicle();
}

if (isset($_POST['add_weapon_btn']))
{
    addWeapon();
}

if (isset($_GET['weaponID']))
{
    removeWeapon();
}

if (isset($_POST['update_medical_btn']))
{
    updateMedical();
}

if (isset($_POST['editvehicle']))
{
    updateVehicle();
}

function editProfile()
{
	$user_discordid = $_SESSION['user_discordid'];
	$redirect = $_SESSION['redirect'];
	$user_identifier = htmlspecialchars($_POST['user_identifier']);
	$user_department = htmlspecialchars($_POST['user_department']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// INSERT INTO DATABASE
	$stmt = $pdo->prepare("UPDATE users SET identifier=?, department=? WHERE discordid=?");
    $stmt->execute([$user_identifier, $user_department, $user_discordid]);

    // LOG IT
	$log = new richEmbed("IDENTIFIER UPDATED", "By <@{$user_discordid}>");
	$log->addField("Identifier:", $user_identifier, false);
	$log->addField("Department:", $user_department, false);
	$log = $log->build();
	sendLog($log, MISC_LOGS);

	if ($redirect == "")
	{
		header('Location: ../index.php');
	} else {
		header('Location: ../'.$redirect);
	}
}

function createCharacter()
{
	$user_discordid = $_SESSION['user_discordid'];
	$character_name = htmlspecialchars($_POST['character_name']);
	$character_dob = htmlspecialchars($_POST['character_dob']);
	$character_haircolor = htmlspecialchars($_POST['character_haircolor']);
	$character_address = htmlspecialchars($_POST['character_address']);
	$character_gender = htmlspecialchars($_POST['character_gender']);
	$character_race = htmlspecialchars($_POST['character_race']);
	$character_build = htmlspecialchars($_POST['character_build']);

	$errors = [];
	$fileExtensionsAllowed = ['jpeg','jpg','png'];
	$fileName = $_FILES['character_image']['name'];
    $fileSize = $_FILES['character_image']['size'];
    $fileTmpName  = $_FILES['character_image']['tmp_name'];
    $fileType = $_FILES['character_image']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));

	$uploadPath = "../assets/images/characters/".$user_discordid."_".preg_replace('/\s+/', '', $character_name)."_".$fileName;

	if( $fileName != "" ) {
		if (! in_array($fileExtension,$fileExtensionsAllowed)) {
			$errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";
		  }
	
		  if ($fileSize > 4000000) {
			$errors[] = "File exceeds maximum size (4MB)";
		  }
	
		  if (empty($errors)) {
			$didUpload = move_uploaded_file($fileTmpName, $uploadPath);
	
			if (!$didUpload) {
			  die("An error occurred. Please contact the administrator.");
			}
		  } else {
			foreach ($errors as $error) {
			  die($error . "These are the errors" . "\n");
			}
		  }
	} else {
		$uploadPath = "../assets/images/characters/default.png";
	}

    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $character_ssn = '';
    for ($i = 0; $i < 9; $i++) {
        $character_ssn .= $characters[rand(0, $charactersLength - 1)];
    }
	
	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	$stmt = $pdo->prepare("SELECT * FROM characters WHERE discordid=?");
    $stmt->execute([$user_discordid]);
    $totalCharacters = $stmt->fetchAll();

	// CHECK IF REACHED CHARACTER LIMIT
	if (sizeof($totalCharacters) <= MAX_CHARACTERS)
	{
		// CHECK FOR DUPLICATE NAME
		if (ALLOW_DUPLICATE_CHARACTER_NAMES == 0)
		{
			$stmt = $pdo->prepare("SELECT * FROM characters WHERE name=?");
			$stmt->execute([$character_name]);
			$result2 = $stmt->fetchAll();

			if (sizeof($result2) > 0)
			{
				header('Location: ../civilian/civilianDashboard.php?duplicateName');
			}
			else
			{
				// INSERT INTO DATABASE
				$stmt = $pdo->prepare("INSERT INTO characters (discordid, name, dob, haircolor, address, gender, race, build, ssn, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$result = $stmt->execute(array($user_discordid, $character_name, $character_dob, $character_haircolor, $character_address, $character_gender, $character_race, $character_build, $character_ssn, $uploadPath));

				// LOG IT
				$log = new richEmbed("NEW CHARACTER", "By <@{$user_discordid}>");
				$log->addField("Name:", $character_name, false);
				$log->addField("DOB:", $character_dob, false);
				$log->addField("Hair Color:", $character_haircolor, false);
				$log->addField("Address:", $character_address, false);
				$log->addField("Gender:", $character_gender, false);
				$log->addField("Race:", $character_race, false);
				$log->addField("Build:", $character_build, false);
				$log->addField("SSN:", $character_ssn, false);
				$log->addField("Image:", $uploadPath, false);
				$log = $log->build();
				sendLog($log, CHARACTER_LOGS);

				header('Location: ../civilian/civilianDashboard.php');
			}
		} 
		else 
		{
			// INSERT INTO DATABASE
			$stmt = $pdo->prepare("INSERT INTO characters (discordid, name, dob, haircolor, address, gender, race, build, ssn, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute(array($user_discordid, $character_name, $character_dob, $character_haircolor, $character_address, $character_gender, $character_race, $character_build, $character_ssn, $uploadPath));

			// LOG IT
			$log = new richEmbed("NEW CHARACTER", "By <@{$user_discordid}>");
			$log->addField("Name:", $character_name, false);
			$log->addField("DOB:", $character_dob, false);
			$log->addField("Hair Color:", $character_haircolor, false);
			$log->addField("Address:", $character_address, false);
			$log->addField("Gender:", $character_gender, false);
			$log->addField("Race:", $character_race, false);
			$log->addField("Build:", $character_build, false);
			$log->addField("SSN:", $character_ssn, false);
			$log->addField("Image:", $uploadPath, false);
			$log = $log->build();
			sendLog($log, CHARACTER_LOGS);

			header('Location: ../civilian/civilianDashboard.php');
		}
	} else {
		header('Location: ../civilian/civilianDashboard.php?characterLimit');
	}
}

function updateCharacter()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$character_name = htmlspecialchars($_POST['character_name']);
	$character_haircolor = htmlspecialchars($_POST['character_haircolor']);
	$character_address = htmlspecialchars($_POST['character_address']);
	$character_gender = htmlspecialchars($_POST['character_gender']);
	$character_build = htmlspecialchars($_POST['character_build']);
	$character_occupation = htmlspecialchars($_POST['character_occupation']);
	if (CIV_PHONE_NUMBERS == false) {
		$character_number = "";
	} else {
		$character_number = htmlspecialchars($_POST['character_number']);
	}
	
	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	$stmt = $pdo->prepare("SELECT * FROM characters WHERE ID=? AND discordid=?");
	$stmt->execute([$characterID, $user_discordid]);
	$result = $stmt->fetchAll();

	foreach ($result as $row)
	{
		$old_image = $row['image'];
	}

	$errors = [];
	$fileExtensionsAllowed = ['jpeg','jpg','png'];
	$fileName = $_FILES['character_image']['name'];
    $fileSize = $_FILES['character_image']['size'];
    $fileTmpName  = $_FILES['character_image']['tmp_name'];
    $fileType = $_FILES['character_image']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));

	$uploadPath = "../assets/images/characters/".$user_discordid."_".preg_replace('/\s+/', '', $character_name)."_".$fileName;

	if( $fileName != "" ) {
		if (! in_array($fileExtension,$fileExtensionsAllowed)) {
			$errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";
		  }
	
		  if ($fileSize > 4000000) {
			$errors[] = "File exceeds maximum size (4MB)";
		  }
	
		  if (empty($errors)) {
			$didUpload = move_uploaded_file($fileTmpName, $uploadPath);
	
			if ($old_image != "") {
				if ($old_image != "../assets/images/characters/default.png") {
					unlink($old_image);
				}
			}

			if (!$didUpload) {
			  die("An error occurred. Please contact the administrator.");
			}
		  } else {
			foreach ($errors as $error) {
			  die($error . "These are the errors" . "\n");
			}
		  }
	}  else if ($old_image != "") {
		$uploadPath = $old_image;
	} else {
		$uploadPath = "../assets/images/characters/default.png";
	}

    // LOG IT
	$log = new richEmbed("CHARACTER UPDATED: $character_name", "By <@{$user_discordid}>");
	$log->addField("Hair Color:", $character_haircolor, false);
	$log->addField("Address:", $character_address, false);
	$log->addField("Gender:", $character_gender, false);
	$log->addField("Number:", $character_number, false);
	$log->addField("Build:", $character_build, false);
	$log->addField("Occupation:", $character_occupation, false);
	$log->addField("Image:", $uploadPath, false);
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	// UPDATE DATABASE
    $stmt = $pdo->prepare("UPDATE characters SET haircolor=?, address=?, gender=?, number=?, build=?, occupation=?, image=? WHERE discordid='$user_discordid' AND ID='$characterID'");
    $stmt->execute([$character_haircolor, $character_address, $character_gender, $character_number, $character_build, $character_occupation, $uploadPath]);

	header('Location: ../civilian/civilianDetails.php?ID='.$characterID);
}

function deleteCharacter()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	$stmt = $pdo->prepare("SELECT * FROM characters WHERE ID=? AND discordid=?");
	$stmt->execute([$characterID, $user_discordid]);
	$result = $stmt->fetchAll();

	foreach ($result as $row)
	{
		$old_image = $row['image'];
	}

	if ($old_image != "") {
		if ($old_image != "../assets/images/characters/default.png") {
			unlink($old_image);
		}
	}

	// DELETE FROM DATABASE
	$stmt = $pdo->prepare("DELETE FROM characters WHERE ID=? AND discordid=?");
	$stmt->execute([$characterID, $user_discordid]);

    // LOG IT
	$log = new richEmbed("CHARACTER DELETED", "By <@{$user_discordid}>");
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	header('Location: ../civilian/civilianDashboard.php');
}

function markDead()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

    // LOG IT
	$log = new richEmbed("CHARACTER MARKED DEAD", "By <@{$user_discordid}>");
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	// UPDATE DATABASE
    $stmt = $pdo->prepare("UPDATE characters SET dead=? WHERE discordid='$user_discordid' AND ID='$characterID'");
    $stmt->execute([1]);

	header('Location: ../civilian/civilianDetails.php?ID='.$characterID);
}

function updateLicense()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$license_type = htmlspecialchars($_POST['licensetype']);
	$license_status = htmlspecialchars($_POST['licensestatus']);

	if (DISABLE_CIV_LICENSE_EDIT == 1 && ($license_type == "drivers" || $license_type == "commercial" || $license_type == "boating" || $license_type == "aviation"))
	{
		echo '<span class="text-danger">NICE TRY</span>';
	} else if ($license_status == "-") {
		echo '<span class="text-danger">NOT VALID STATUS</span>';
	} else if ($license_type == "-") {
		echo '<span class="text-danger">NOT VALID TYPE</span>';
	} else {
		if ($license_status == "Unobtained" || $license_status == "Valid" || $license_status == "Invalid" || $license_status == "Revoked"|| $license_status == "Suspended")
		{
			try{
				$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			} catch(PDOException $ex)
			{
				echo "Could not connect -> ".$ex->getMessage();
				die();
			}

			$stmt = $pdo->prepare("SELECT * FROM arrests WHERE civid=?");
			$stmt->execute([$characterID]);
			$getarrest = $stmt->fetchAll();

			foreach ($getarrest as $row)
			{
				$arresttype = $row['arresttype'];
			}
			if ($arresttype == "Felony" & $license_type == "weapons") {
				echo '<span class="text-danger">NOT ALLOWED TO ALTER WEAPON STATUS AS YOU HAVE A FELONY ARREST</span>';
			} else {
	
				// LOG IT
				$log = new richEmbed("CHARACTER LICENSE UPDATED", "By <@{$user_discordid}>");
				$log->addField("License Type:", $license_type, false);
				$log->addField("License Status:", $license_status, false);
				$log = $log->build();
				sendLog($log, CHARACTER_LOGS);
	
				// UPDATE DATABASE
				$stmt = $pdo->prepare("UPDATE characters SET $license_type=? WHERE discordid='$user_discordid' AND ID='$characterID'");
				$stmt->execute([$license_status]);
	
				//header('Location: ../civilian/civilianDetails.php?ID='.$characterID);
				echo '<span class="text-success">SUCCESS</span>';
			}
		}
	}
}

function addVehicle()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$vehicle_plate = htmlspecialchars($_POST['vehicle_plate']);
	$vehicle_model = htmlspecialchars($_POST['vehicle_model']);
	$vehicle_color = htmlspecialchars($_POST['vehicle_color']);
	$vehicle_insurance = htmlspecialchars($_POST['vehicle_insurance']);
	$vehicle_regstate = htmlspecialchars($_POST['vehicle_regstate']);
	$vehicle_flags = htmlspecialchars($_POST['vehicle_flags']);

    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $vehicle_vin = '';
    for ($i = 0; $i < 17; $i++) {
        $vehicle_vin .= $characters[rand(0, $charactersLength - 1)];
    }

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// CHECK IF IT ALREADY EXISTS
	$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE plate=?");
	$stmt->execute([$vehicle_plate]);
	$existscheck = $stmt->fetchAll();

	if (sizeof($existscheck) > 0)
	{
		header('Location: ../civilian/civilianDetails.php?ID='.$characterID.'&duplicatePlate');
	}
	else 
	{
		// INSERT INTO DATABASE
	    $stmt = $pdo->prepare("INSERT INTO vehicles (discordid, charid, plate, makemodel, color, insurance, regstate, flags, vin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	    $result = $stmt->execute(array($user_discordid, $characterID, $vehicle_plate, $vehicle_model, $vehicle_color, $vehicle_insurance, $vehicle_regstate, $vehicle_flags, $vehicle_vin));

	    // LOG IT
		$log = new richEmbed("NEW VEHICLE", "By <@{$user_discordid}>");
		$log->addField("Plate:", $vehicle_plate, false);
		$log->addField("Make & Model:", $vehicle_model, false);
		$log->addField("Color:", $vehicle_color, false);
		$log->addField("Insurance:", $vehicle_insurance, false);
		$log->addField("Registered State:", $vehicle_regstate, false);
		$log->addField("Flags:", $vehicle_flags, false);
		$log->addField("VIN:", $vehicle_vin, false);
		$log = $log->build();
		sendLog($log, CHARACTER_LOGS);

		header('Location: ../civilian/civilianDetails.php?ID='.$characterID.'#vehiclesection');
	}
}

function removeVehicle()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$vehicleID = htmlspecialchars($_GET['vehicleID']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// REMOVE FROM DATABASE
	$stmt = $pdo->prepare("DELETE FROM vehicles WHERE ID=? AND charid=? AND discordid=?");
	$stmt->execute([$vehicleID, $characterID, $user_discordid]);

    // LOG IT
	$log = new richEmbed("VEHICLE DELETED", "By <@{$user_discordid}>");
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	header('Location: ../civilian/civilianDetails.php?ID='.$characterID.'#vehiclesection');
}

function addWeapon()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$weapon_type = htmlspecialchars($_POST['weapon_type']);
	$weapon_name = htmlspecialchars($_POST['weapon_name']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $weapon_serialnumber = '';
    for ($i = 0; $i < 10; $i++) {
        $weapon_serialnumber .= $characters[rand(0, $charactersLength - 1)];
    }

	// INSERT INTO DATABASE
    $stmt = $pdo->prepare("INSERT INTO weapons (discordid, charid, type, name, serialnumber) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute(array($user_discordid, $characterID, $weapon_type, $weapon_name, $weapon_serialnumber));

    // LOG IT
	$log = new richEmbed("NEW WEAPON", "By <@{$user_discordid}>");
	$log->addField("Type:", $weapon_type, false);
	$log->addField("Name:", $weapon_name, false);
	$log->addField("Serial Number:", $weapon_serialnumber, false);
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	header('Location: ../civilian/civilianDetails.php?ID='.$characterID.'#weaponsection');
}

function removeWeapon()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$weaponID = htmlspecialchars($_GET['weaponID']);

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	// REMOVE FROM DATABASE
	$stmt = $pdo->prepare("DELETE FROM weapons WHERE ID=? AND charid=? AND discordid=?");
	$stmt->execute([$weaponID, $characterID, $user_discordid]);

    // LOG IT
	$log = new richEmbed("WEAPON DELETED", "By <@{$user_discordid}>");
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	header('Location: ../civilian/civilianDetails.php?ID='.$characterID.'#weaponsection');
}

function updateMedical()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$medical_bloodtype = htmlspecialchars($_POST['medical_bloodtype']);
	$medical_emergency_contact = htmlspecialchars($_POST['medical_emergency_contact']);
	$medical_allergies = htmlspecialchars($_POST['medical_allergies']);
	$medical_medication = htmlspecialchars($_POST['medical_medication']);
	$medical_organdonor = htmlspecialchars($_POST['medical_organdonor']);

	if ($medical_bloodtype == "Unknown" || $medical_bloodtype == "A Positive" || $medical_bloodtype == "A Negative" || $medical_bloodtype == "A Unknown" || $medical_bloodtype == "B Positive" || $medical_bloodtype == "B Negative" || $medical_bloodtype == "B Unknown" || $medical_bloodtype == "AB Positive" || $medical_bloodtype == "AB Negative" || $medical_bloodtype == "AB Unknown" || $medical_bloodtype == "O Positive" || $medical_bloodtype == "O Negative" || $medical_bloodtype == "O Unknown")
	{
		try{
			$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch(PDOException $ex)
		{
			echo "Could not connect -> ".$ex->getMessage();
			die();
		}
	
		// LOG IT
		$log = new richEmbed("UPDATED CHARACTER MEDICAL", "By <@{$user_discordid}>");
		$log->addField("Blood Types:", $medical_bloodtype, false);
		$log->addField("Emergency Contact:", $medical_emergency_contact, false);
		$log->addField("Allergies:", $medical_allergies, false);
		$log->addField("Medication:", $medical_medication, false);
		$log->addField("Organ Donor:", $medical_organdonor, false);
		$log = $log->build();
		sendLog($log, CHARACTER_LOGS);
	
		// UPDATE DATABASE
		$stmt = $pdo->prepare("UPDATE characters SET bloodtype=?, emergcontact=?, allergies=?, medication=?, organdonor=? WHERE discordid='$user_discordid' AND ID='$characterID'");
		$stmt->execute([$medical_bloodtype, $medical_emergency_contact, $medical_allergies, $medical_medication, $medical_organdonor]);
	}

	header('Location: ../civilian/civilianDetails.php?ID='.$characterID.'#medicalsection');
}

function updateVehicle()
{
	$user_discordid = $_SESSION['user_discordid'];
	$characterID = $_SESSION["characterID"];
	$editvehicle = htmlspecialchars($_POST['editvehicle']);
	$insurance = htmlspecialchars($_POST['insurance']);
	$flag = htmlspecialchars(str_replace('-', ' ', $_POST['flag']));
	$color = htmlspecialchars(str_replace('-', ' ', $_POST['color']));
	$plate = htmlspecialchars(str_replace('-', ' ', $_POST['plate']));

	try{
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	} catch(PDOException $ex)
	{
		echo "Could not connect -> ".$ex->getMessage();
		die();
	}

	if (ALLOW_CIV_EDIT_VEHICLE_PLATE == false) {
		$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE ID=? AND charid=?");
		$stmt->execute([$editvehicle, $characterID]);
		$result2 = $stmt->fetchAll();

		foreach ($result2 as $row)
		{
			$plate = $row['plate'];
		}
	}

	// UPDATE INTO DATABASE
	$stmt = $pdo->prepare("UPDATE vehicles SET color=?, insurance=?, flags=?, plate=? WHERE ID=? AND charid=?");
	$stmt->execute([$color, $insurance, $flag, $plate, $editvehicle, $characterID]);

    // LOG IT
	$log = new richEmbed("VEHICLE UPDATED", "By <@{$user_discordid}>");
	$log->addField("Color:", $color, false);
	$log->addField("Flags:", $flag, false);
	$log->addField("Insurance:", $insurance, false);
	$log = $log->build();
	sendLog($log, CHARACTER_LOGS);

	echo '<span class="text-success">SUCCESS</span>';

}
?>