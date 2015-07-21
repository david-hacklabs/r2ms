<?php

/*
 * David Zarza Luna - HackLabs
* Start Modifying - 2014/7/8
*/

// Include required configuration files
require_once ('../config.php');
require_once ('../authenticate.php');

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
	$message = "An AJAX request to get projects from company fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	exit ( 0 );
}

if (isset ($_SESSION ["type"]) && $_SESSION ["type"] == 2 && isset ($_SESSION ["company"]) && $_SESSION ["company"] != 0 && is_int ((int) $_SESSION ["company"]))
	$company = (int) $_SESSION ["company"];
elseif (isset($_GET['company']) && is_int ((int)$_GET['company']))
	 $company = (int) $_GET['company'];

if (isset($company)){
	$response = getProjectsCompany($company);
	echo json_encode($response);
}
else {
	// Audit log
	$risk_id = 1000;
	$message = "An AJAX request to get projects from company fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	echo "error";
}

/**
 * ******************************
 * FUNCTION: PROJECTS'S COMPANY *
 * ******************************
 */
function getProjectsCompany($name) {
	// Open the database connection
	$db = db_open ();

	// Find the user
	$stmt = $db->prepare ( "SELECT project.value, project.name, project.company_id, project.category_id, category.name as category, CONCAT(YEAR(project.creation),'/',MONTHNAME(project.creation), '/', DAY(project.creation)) as creation FROM project INNER JOIN category ON category_id = category.value WHERE company_id=:company_id" );
	$stmt->bindParam ( ":company_id", $name, PDO::PARAM_INT);

	$stmt->execute ();

	$return = new stdClass();
	
	// Fetch the array
	$array = $stmt->fetchAll (PDO::FETCH_ASSOC);

	// If the array is empty
	if (empty ( $array )) {
		$return->valid = false;
		$return->data = null; 
	} else {
		$return->valid = true;
		$return->data = $array;
	}

	// Close the database connection
	db_close ( $db );

	return $return;
}