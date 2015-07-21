<?php
/*
 * David Zarza Luna - HackLabs
 * Start Modifying - 2014/9/8
*/

// Include required configuration files
require_once ('../../config.php');
require_once ('../../authenticate.php');
require_once ('../../functions.php');

// Session handler is database
if (USE_DATABASE_FOR_SESSIONS == "true") {
	session_set_save_handler ( 'sess_open', 'sess_close', 'sess_read', 'sess_write', 'sess_destroy', 'sess_gc' );
}

// Start the session
session_set_cookie_params ( 0, '/', '', isset ( $_SERVER ["HTTPS"] ), true );
session_start ( 'SimpleRisk' );

// Check for session timeout or renegotiation
session_check ();

if (! isset ( $_SESSION ["access"] ) || $_SESSION ["access"] != "granted") {
	//header ( "Location: ../index.php" );
	$risk_id = 1000;
	$message = "An AJAX request to get projects and clients from company fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	exit ( 0 );
}
if (isset ($_SESSION ["type"]) && $_SESSION ["type"] == 1 && isset($_GET['company']) && is_int ((int)$_GET['company']))
	$company = (int) $_GET ["company"];

$return = new stdClass();

//company_existwithinuser($company, $client)  --> Should be checked if the currently user session is allow to see the company/projects

if (isset($company) && existNameByValue('company', $company)){
		
		$tablecontent = get_projects_and_risks_table($company);
		
		$return->message = $tablecontent; 
		$return->success = true;
		
		// Audit log
		$risk_id = 1000;
		$message = "An existing company is able to be found in order to show the projects associated by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
}
else {

	$return->success = false;
	$return->message = "<p class='redalert'>Please try again.</p>";

	// Audit log
	$risk_id = 1000;
	$message = "An AJAX request to get the projects and risks associated fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
}
echo json_encode($return);