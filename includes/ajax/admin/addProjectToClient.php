<?php

/*
 * David Zarza Luna - HackLabs
* Start Modifying - 2014/7/13
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

$company = (int) $_GET['company'];
$client = (int) $_GET['client'];
$project = (int) $_GET['project'];
$return = new stdClass();

if (isset($company) && is_int ($company) && existNameByValue('company', $company) && isset($project) && is_int ($project) && is_int ($client) && company_existwithinuser($company, $client)){
	
	if(!project_client_exist($client, $project)) {
		$return->success = true;
		$return->message = "An existing project has been associated the client/s successfully.";
		if (addUserAndProjectToUserProject_client($client, $project)){
		
			// Audit log
			$risk_id = 1000;
			$message = "An existing project was associated to the client/s by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
		}
		else {
			$return->success = false;
			$return->message = "Please try again.";
			
			$risk_id = 1000;
			$message = "An AJAX request to add project to a client fail by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
		}
	}
	else {
		$return->success = true;
		$return->message = "The client has been updated and associated to the project.";
		if (activeUserAndProjectToUserProject_client($client, $project)){
			// Audit log
			$risk_id = 1000;
			$message = "An existing project was updated and associated to the client/s by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
		}
		else {
			$return->success = false;
			$return->message = "Please try again.";
			
			// Audit log
			$risk_id = 1000;
			$message = "An AJAX request to add project to a client fail by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
		}
	}
	
}
else {
	
	$return->success = false;
	$return->message = "Please try again.";
	
	// Audit log
	$risk_id = 1000;
	$message = "An AJAX request to add project to a client fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
}

echo json_encode($return);