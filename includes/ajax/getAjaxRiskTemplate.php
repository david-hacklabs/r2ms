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
	$message = "An AJAX request to get risk templates fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	exit ( 0 );
}

if (isset($_GET['risk_template']) && $_GET['risk_template'] != 0 && is_int ((int) $_GET['risk_template'])){
	$risk_template = $_GET['risk_template']; 
}
else $risk_template = null;

if (isset($risk_template)){
	$response = getRiskTemplateDetails($risk_template);
	echo json_encode($response);
}
else {
	// Audit log
	$risk_id = 1000;
	$message = "An AJAX request to get risk templates fail by the \"" . $_SESSION ['user'] . "\" user.";
	write_log ( $risk_id, $_SESSION ['uid'], $message );
	echo "error";
}

/**
 * ******************************
 * FUNCTION: GET RISK TEMPLATE DETAILS *
 * ******************************
 */
function getRiskTemplateDetails($value) {
	// Open the database connection
	$db = db_open ();

	// Find the user
	$stmt = $db->prepare ( "SELECT risk_template.value, risk_template.name, risk_template.description, risk_template.impact, risk_template.detail, risk_template.recommendation FROM risk_template WHERE value=:value" );
	$stmt->bindParam ( ":value", $value, PDO::PARAM_INT);

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