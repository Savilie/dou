<?php 
// DOCUMENTATION: https://docs.hamz.dev/
// Please look at documentation before you create a ticket.
// Add your domain to whitelist @ https://license.hamzcad.com/

// CAD URL \\
define('BASE_URL', 'https://cad.domain.tld'); // Make sure no / at the end. Same Format as Example!

// SQL DATABASE CONNECTION \\
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hamzcad');

// DISCORD OAUTH2 - Get information from Discord Dev Portal \\
define('TOKEN', 'INSERT_HERE');
define('GUILD_ID', 'INSERT_HERE');
define('OAUTH2_CLIENT_ID', 'INSERT_HERE');
define('OAUTH2_CLIENT_SECRET', 'INSERT_HERE');
 
// DISCORD ADMIN PERMISSIONS \\
// This will allow an admin to login and setup the cad permissions in the Admin Settings.
$ADMINROLES = [
	"INSERT_ROLEID_HERE",
	"INSERT_ROLEID_HERE",
];

// DISCORD LOGS \\
define('LOGS_COLOR', '#00B2FF');
define('LOGS_IMAGE', 'https://imgur.com/yaHpliD.png');

define('MISC_LOGS', 'INSERT_CHANNELID_HERE');
define('CHARACTER_LOGS', 'INSERT_CHANNELID_HERE');
define('LAW_BREAKING_LOGS', 'INSERT_CHANNELID_HERE');
define('DEPARTMENT_LOGS', 'INSERT_CHANNELID_HERE');
define('ADMIN_LOGS', 'INSERT_CHANNELID_HERE');
define('DISPATCH_LOGS', 'INSERT_CHANNELID_HERE');
define('STATUS_LOGS', 'INSERT_CHANNELID_HERE');

// GENERAL SETTINGS \\
define('SERVER_SHORT_NAME', 'HAMZ');
define('SERVER_LOGO', 'https://imgur.com/yaHpliD.png');
define('PENAL_CODE_LINK', '#'); // # to Disable
define('LIVEMAP_LINK', '#'); // # to Disable
define('MAX_CHARACTERS', '10');
define('ALLOW_DUPLICATE_CHARACTER_NAMES', '1'); // 0 to Disable | 1 to Allow
define('GALLERY', '1'); // 0 to Disable | 1 to Enable
define('PRELOADER', '0'); // 0 to Disable | 1 to Enable
define('CALLPANEL911', '0'); // 0 to Disable | 1 to Enable
define('CIVILIAN_PERM_PUBLIC', '1'); // 0 to Disable | 1 to Enable 
define('DMV_SYSTEM', '1'); // 0 to Disable | 1 to Enable 
define('DISABLE_CIV_LICENSE_EDIT', '0'); // 0 to Disable | 1 to Enable // Disable if DMV is 0
define('CIV_PHONE_NUMBERS', true); // true to Enable | false to Disable
define('POINTS_FOR_DRIVING_SUSPENSION', '6');
define('UPDATE_IDENTIFIER_ON_LOGIN', true); // Update the Identifier with Discord Nickname, Each time a user logs in.
define('IDENTIFIER_CHECK', true); // Displays pop up asking user to verify if they have the correct identifier & department before allowing to access department dashboard.
define('ALLOW_EMS_MARK_CHARACTER_DEAD', true); // If true, allows ems to mark characters as dead or alive.
define('CHARACTER_NAME_WORDS_LIMIT', 4); // Number of words a character name can have. Eg. If 3: John Bob Doe | If 2: John Doe
define('ALLOW_LEO_SUPERVISOR_ADD_WARRANT', true); // If true, allows leo supervisors to add warrants to characters.
define('ALLOW_CIV_EDIT_VEHICLE_PLATE', true); // If true, allows civilians to edit their vehicle plate.

$CODES10 = [
	"Signal 100 | HOLD ALL BUT EMERGENCY TRAFFIC" => "#FF4747",
	"Signal 60 | Drugs" => "white",
	"Signal 11 | Running Radar" => "white",
	"Code Zero | Game Crash" => "white",
	"Code 4 | Under Control" => "white",
	"Code 5 | Felony Stop / High Risk Stop" => "#FF4747",
	"10-0 | Disappeared" => "white",
	"10-1 | Frequency Change" => "white",
	"10-3 | Stop Transmitting " => "white",
	"10-4 | Affirmative" => "white",
	"10-5 | Meal Break Burger Shots Etc." => "white",
	"10-6 | Busy" => "white",
	"10-7 | Out of Service" => "white",
	"10-8 | In Service" => "white",
	"10-9 | Repeat" => "white",
	"10-10 | Fight In Progress" => "yellow",
	"10-11 | Traffic Stop" => "white",
	"10-12 | Standby" => "white",
	"10-13 | Shots Fired" => "#FF4747",
	"10-15 | Subject in custody en route to Station" => "white",
	"10-16 | Stolen Vehicle" => "yellow",
	"10-17 | Suspicious Person" => "yellow",
	"10-19 | Active Ride Along" => "white",
	"10-20 | Location" => "white",
	"10-22 | Disregard" => "white",
	"10-23 | Arrived on Scene" => "white",
	"10-25 | Domestic Dispute" => "white",
	"10-26 | ETA" => "white",
	"10-27 | Drivers License check for valid" => "white",
	"10-28 | Vehicle License Plate Check" => "white",
	"10-29 | NCIC Warrant Check" => "white",
	"10-30 | Wanted Person" => "#FF4747",
	"10-31 | Not Wanted No Warrants" => "white",
	"10-32 | Request Backup" => "yellow",
	"10-35 | Wrap The Scene Up" => "white",
	"10-38 | Suspicious Vehicle" => "yellow",
	"10-41 | Beginning Tour of Duty" => "white",
	"10-42 | Ending Tour of Duty" => "white",
	"10-43 | Information" => "white",
	"10-49 | Homocide" => "white",
	"10-50 | Vehicle Accident" => "white",
	"10-51 | Requesting Towing Service" => "white",
	"10-52 | Request EMS" => "white",
	"10-53 | Request Fire Department" => "white",
	"10-55 | Intoxicated Driver" => "white",
	"10-56 | Intoxicated Pedestrian " => "white",
	"10-60 | Armed with a Gun" => "#FF4747",
	"10-61 | Armed with a Knife" => "#FF4747",
	"10-62 | Kidnapping" => "white",
	"10-64 | Sexual Assault" => "white",
	"10-65 | Escorting Prisoner" => "white",
	"10-66 | Reckless Driver" => "white",
	"10-67 | Fire" => "#FF4747",
	"10-68 | Armed Robbery" => "#FF4747",
	"10-70 | Foot Pursuit" => "white",
	"10-71 | Request Supervisor at Scene" => "white",
	"10-73 | Advise Status" => "white",
	"10-80 | Vehicle Pursuit" => "white",
	"10-90 | In Game Warning" => "white",
	"10-93 | Removed from Game" => "white",
	"10-97 | In Route" => "white",
	"10-99 | Officer in Distress EXTREME EMERGENCY ONLY" => "#FF4747",
	"11-44 | Person Deceased" => "white",
	"Code Blue | Lost Pulse (Start CPR)" => "lightblue",
	"Code Red | Activate Trauma Code (5 minute timer)" => "#FF4747",
	"Code 1 | Low Priority Transport (Routine)" => "white",
	"Code 2 | Medium Priority Transport" => "white",
	"Code 3 | Highest Priority Transport" => "white",
];

$FIREEMS_APPARATUS = [
	"Engine",
	"Tower",
	"Medic",
];

// EDIT STATUS'S
$LEO_STATUSES = [
	"10-6 | Busy" => "10-6",
	"10-11 | Traffic Stop" => "10-11",
	"10-15 | En-Route to Station" => "10-15",
	"10-23 | Arrived on Scene" => "10-23",
	"10-97 | In Route" => "10-97",
	"10-99 | In Distress" => "10-99",
];

$FIREEMS_STATUSES = [
	"Roaming" => "Roaming",
	"10-6 | Busy" => "10-6",
	"10-15 | En-Route to Station" => "10-15",
	"10-23 | Arrived on Scene" => "10-23",
	"10-97 | In Route" => "10-97",
	"10-99 | In Distress" => "10-99",
];

// Don't add servers which are offline, it could cause your cad to be slow.
$SERVERS = array(
	[
		'server_ip' => '149.202.88.5',
		'server_port' => '30120'
	],
	// [
	// 	'server_ip' => '127.0.0.1',
	// 	'server_port' => '30121'
	// ]
);


define('ACCENT_COLOR', '#00B9FF');
define('BACKGROUND_COLOR', '#000000');
define('CARD_COLOR', '#191c24');
define('TEXT_COLOR', '#ffffff');
define('INPUTBOX_COLOR', '#2A3038');
define('SCROLLBAR_COLOR', '#6c7293');

define('SECRET_KEY', 'apitest123');  // Used in some API's
?>