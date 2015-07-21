<?php

/*
 * David Zarza Luna - HackLabs
* Start Modifying - 2014/7/16
*/

// Include required configuration files
require_once ('../config.php');
require_once ('../authenticate.php');
require_once ('../functions.php');

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
	$message = "An AJAX request to get clients from project-client list fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	exit ( 0 );
}
$company = (int) $_GET['company'];
$project = (int) $_GET['project'];
$return = new stdClass();

if (isset($company) && is_int ($company) && existNameByValue('company', $company) && isset($project) && is_int ($project) ){
	$response = getClientsFromProjectClient($project);
	echo json_encode($response);
}
else {
	// Audit log
	$risk_id = 1000;
	$message = "An AJAX request to get clients from project-client list fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	echo "error";
}

/**
 * ***************************************
 * FUNCTION: CLIENTS FROM PROJECT-CLIENT *
 * ***************************************
 */
function getClientsFromProjectClient($project) {
	// Open the database connection
	$db = db_open ();

	// Find the user
	$stmt = $db->prepare ( "SELECT CONCAT (name, ' - ', email) as nameemail FROM user INNER JOIN project_client ON project_client.user = user.value WHERE project_client.project = :project AND user.type = 2" );
	/*Notice that project_client.active = 1
	/*$stmt = $db->prepare ( "SELECT CONCAT (name, ' - ', email) as nameemail FROM user INNER JOIN project_client ON project_client.user = user.value WHERE project_client.active = 1 AND project_client.project = :project AND user.type = 2" );
	 * 
	 */
	$stmt->bindParam ( ":project", $project, PDO::PARAM_INT);

	$stmt->execute ();

	$return = new stdClass();
	
	// Fetch the array
	$arrayClients = $stmt->fetchAll (PDO::FETCH_ASSOC);
	
	// If the array is empty
	if (empty ( $arrayClients )) {
		$return->valid = false;
		$return->data = null; 
	} else {
		$return->valid = true;
		$return->data = $arrayClients;
		
	}

	// Close the database connection
	db_close ( $db );

	return $return;
}
