<?php
/*
 * David Zarza Luna - HackLabs
 * Start Modifying - 2014/9/5
*/

// Include required configuration files
require_once ('../../config.php');
require_once ('../../authenticate.php');
require_once ('../../functions.php');
require_once ('../../reporting.php');

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
if (isset ($_SESSION ["type"]) && $_SESSION ["type"] == 2 && isset ($_SESSION ["company"]) && $_SESSION ["company"] != 0 && is_int ((int) $_SESSION ["company"]))
	$company = (int) $_SESSION ["company"];
elseif (isset($_GET['company']) && is_int ((int)$_GET['company']))
$company = (int) $_GET['company'];

if (isset($_GET['project']) && is_int ((int)$_GET['project']))
	$project = (int) $_GET['project'];

$return = new stdClass();

//company_existwithinuser($company, $client)  --> Should be checked if the currently user session is allow to see the company/projects

if (isset($company) && existNameByValue('company', $company) && isset($project) && existNameByValue('project_version', $project)){
	if (!projectversion_exist_in_company($project, $company)){
		$return->success = false;
		$return->message = "<p class='redalert'>The application cannot find this version project in the company requested.</p>";
		
		// Audit log
		$risk_id = 1000;
		$message = "An existing project is not be able to be found in order to show the required dashboard by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
	}
	else {
		$return->message = new stdClass();
		$return->message->n_openrisks = "(".get_open_risks($project).")";
		$return->message->open_risk_level_pie = open_risk_level_pie($project);
		$return->message->open_risk_status_pie = open_risk_status_pie($project);
		//$return->message->open_risk_location_pie = open_risk_location_pie($project);
		//$return->message->open_risk_category_pie = open_risk_category_pie($project);
		//$return->message->open_risk_team_pie = open_risk_team_pie($project);
		//$return->message->open_risk_technology_pie = open_risk_technology_pie($project);
		$return->message->open_risk_owner_pie = open_risk_owner_pie($project);
		$return->message->open_risk_owners_manager_pie = open_risk_owners_manager_pie($project);
		//$return->message->open_risk_scoring_method_pie = open_risk_scoring_method_pie($project);
		$return->message->n_closed_risks = "(".get_closed_risks($project).")";
		$return->message->closed_risk_reason_pie = closed_risk_reason_pie($project);
				
		$return->success = true;
	
		// Audit log
		$risk_id = 1000;
		$message = "An existing project is able to be found in order to show the required dashboard by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
	}
}
else {

	$return->success = false;
	$return->message = "<p class='redalert'>Please try again.</p>";

	// Audit log
	$risk_id = 1000;
	$message = "An AJAX request to get the dashboard fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
}
echo json_encode($return);