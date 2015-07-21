<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

// Include required functions file
require_once ('../includes/functions.php');
require_once ('../includes/authenticate.php');

// Add various security headers
header ( "X-Frame-Options: DENY" );
header ( "X-XSS-Protection: 1; mode=block" );

// If we want to enable the Content Security Policy (CSP) - This may break Chrome
if (CSP_ENABLED == "true") {
	// Add the Content-Security-Policy header
	header ( "Content-Security-Policy: default-src 'self'; script-src 'unsafe-inline'; style-src 'unsafe-inline'" );
}

// Session handler is database
if (USE_DATABASE_FOR_SESSIONS == "true") {
	session_set_save_handler ( 'sess_open', 'sess_close', 'sess_read', 'sess_write', 'sess_destroy', 'sess_gc' );
}

// Start the session
session_set_cookie_params ( 0, '/', '', isset ( $_SERVER ["HTTPS"] ), true );
session_start ( 'SimpleRisk' );

// Include the language file
require_once (language_file ());

require_once ('../includes/csrf-magic/csrf-magic.php');

// Check for session timeout or renegotiation
session_check ();

// Check if access is authorized
if (! isset ( $_SESSION ["access"] ) || $_SESSION ["access"] != "granted") {
	header ( "Location: ../index.php" );
	exit ( 0 );
}

// Check if a risk ID was sent
if (isset ( $_GET ['id'] ) || isset ( $_POST ['id'] )) {
	if (isset ( $_GET ['id'] )) {
		$id = htmlentities ( $_GET ['id'], ENT_QUOTES, 'UTF-8' );
	} else if (isset ( $_POST ['id'] )) {
		$id = htmlentities ( $_POST ['id'], ENT_QUOTES, 'UTF-8' );
	}
	
	// Reopen the risk
	if (reopen_risk ( $id )){
	
		// Audit log
		$risk_id = $id;
		$message = "Risk ID \"" . $risk_id . "\" was reopened by username \"" . $_SESSION ['user'] . "\".";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
		
		// Check that the id is a numeric value
		if (is_numeric ( $id )) {
			// Create the redirection location
			$url = "view.php?id=" . $id . "&reopened=true";
			
			// Redirect to plan mitigations page
			header ( "Location: " . $url );
		}
	}
	else {
		$alert = "bad";
		$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
	}
} else
	header ( 'Location: reports/closed.php' );
?>
