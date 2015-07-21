<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

// Include required functions file
require_once ('includes/functions.php');
require_once ('includes/authenticate.php');

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

// Start session
session_set_cookie_params ( 0, '/', '', isset ( $_SERVER ["HTTPS"] ), true );
session_start ( 'SimpleRisk' );

// Include the language file
require_once (language_file ());

// Default is no alert
$alert = false;
$reset_response = new stdClass();
// Check if a password reset email was requested
if (isset ( $_POST ['send_reset_email'] )) {
	$reset_response = new stdClass();
	$username = $_POST ['user'];
	
	// Try to generate a password reset token
	password_reset_by_username ( $username );
	
	// Send an alert message
	$alert = "good";
	$reset_response->message = "If the user exists in the system, then a password reset e-mail should be on it's way.";
}

// Check if a password reset was requested
if (isset ( $_POST ['password_reset'] ) && $_POST ['key'] ) {
	$username = $_POST ['user'];
	$token = $_POST ['token'];
	$password = $_POST ['password'];
	$repeat_password = $_POST ['repeat_password'];
	$key = $_POST ['key'];
	
	$reset_response = new stdClass();
	$reset_response->valid = false;
	$reset_response->message = "There was a problem with your password reset request.  Please try again.";
	
	$reset_response = password_reset_by_token ( $username, $token, $password, $repeat_password, $key );
	
	// If a password reset was submitted
	if ($reset_response->valid)	$alert = "good";
	else 
		$alert = "bad";
	
}

?>

<!doctype html>
<html>

<head>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<title>R2MS: Risk & Reporting Management Service</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap-responsive.css">
</head>

<body>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.css">
	<link rel="stylesheet" href="css/divshot-util.css">
	<link rel="stylesheet" href="css/divshot-canvas.css">
	<link rel="stylesheet" href="css/display.css">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" target="_blank" href="http://www.hacklabs.com/"></a>
				<div class="navbar-content">
					<ul class="nav">
						<li class="active"><a href="index.php"><?php echo $lang['Home']; ?></a>
						</li>
						<li><a href="management/index.php"><?php echo $lang['RiskManagement']; ?></a>
						</li>
						<li><a href="reports/index.php"><?php echo $lang['Reporting']; ?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php
if ($alert == "good") {
	echo "<div id=\"alert\" class=\"container-fluid\">\n";
	echo "<div class=\"row-fluid\">\n";
	echo "<div class=\"span12 greenalert\">" . $reset_response->message . "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "<br />\n";
} else if ($alert == "bad") {
	echo "<div id=\"alert\" class=\"container-fluid\">\n";
	echo "<div class=\"row-fluid\">\n";
	echo "<div class=\"span12 redalert\">" . $reset_response->message . "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "<br />\n";
}
?>
    <div class="container-fluid">
		<div class="row-fluid">
			<div class="span9">
				<div class="well">
					<p>
						<label><u><?php echo $lang['SendPasswordResetEmail']; ?></u></label>
					</p>
					<form name="send_reset_email" method="post" action="">
            <?php echo $lang['Username']; ?>: <input
							class="input-medium" name="user" id="user" type="text"
							maxlength="20" />
						<div class="form-actions">
							<button type="submit" name="send_reset_email"
								class="btn btn-primary"><?php echo $lang['Send']; ?></button>
							<input class="btn" value="<?php echo $lang['Reset']; ?>"
								type="reset">
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span9">
				<div class="well">
					<p>
						<label><u><?php echo $lang['PasswordReset']; ?></u></label>
					</p>
					<form name="password_reset" method="post" action="">
            <?php echo $lang['Username']; ?>: <input required
							class="input-medium" name="user" id="user" type="text"
							maxlength="20" /><br />
            <?php echo $lang['ResetToken']; ?>: <input required
							class="input-medium" name="token" id="token" type="password"
							maxlength="20" /><br />
            <?php echo $lang['Password']; ?>: <input required
							class="input-medium" name="password" id="password"
							type="password" maxlength="50" autocomplete="off" pattern=".{8,}" title="8 minimum length with a combination of lower and upper case characters, digits and special characters" maxlength="50"/><br />
            <?php echo $lang['RepeatPassword']; ?>: <input required
							class="input-medium" name="repeat_password" id="repeat_password"
							type="password" maxlength="50" autocomplete="off" pattern=".{8,}" title="8 minimum length with a combination of lower and upper case characters, digits and special characters" maxlength="50"/> <br />
			YubiKey : <input required class="input-medium" autocomplete="off" type="password" name="key" placeholder="YubiKey" />
						<div class="form-actions">
							<button type="submit" name="password_reset"
								class="btn btn-primary"><?php echo $lang['Submit']; ?></button>
							<input class="btn" value="<?php echo $lang['Reset']; ?>"
								type="reset">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
