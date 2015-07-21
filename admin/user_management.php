<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * David Zarza Luna - HackLabs
 * Start Modyfing 2014/5/21
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

// Default is no alert
$alert = false;

// Check if access is authorized
if (! isset ( $_SESSION ["admin"] ) || $_SESSION ["admin"] != "1") {
	header ( "Location: ../index.php" );
	exit ( 0 );
}

// Check if a new user was submitted
if (isset ( $_POST ['add_user'] )) {
	$type = $_POST ['typeuser'];
	$company_id = addslashes ( $_POST ['company'] );
	$name = addslashes ( $_POST ['name'] );
	$email = addslashes ( $_POST ['email'] );
	$user = addslashes ( $_POST ['new_user'] );
	$pass = $_POST ['password'];
	$repeat_pass = $_POST ['repeat_password'];
	$teams = $_POST ['team'];
	$admin = isset ( $_POST ['admin'] ) ? '1' : '0';
	$submit_risks = isset ( $_POST ['submit_risks'] ) ? '1' : '0';
	$modify_risks = isset ( $_POST ['modify_risks'] ) ? '1' : '0';
	$close_risks = isset ( $_POST ['close_risks'] ) ? '1' : '0';
	$plan_mitigations = isset ( $_POST ['plan_mitigations'] ) ? '1' : '0';
	$review_critical = isset ( $_POST ['review_critical'] ) ? '1' : '0';
	$review_high = isset ( $_POST ['review_high'] ) ? '1' : '0';
	$review_medium = isset ( $_POST ['review_medium'] ) ? '1' : '0';
	$review_low = isset ( $_POST ['review_low'] ) ? '1' : '0';
	$multi_factor = ( int ) $_POST ['multi_factor'];
	
	// If the type is HackLabs Staff and HackLabs Client
	if ($type != "1" && $type != "2") $type = "INVALID";
	/*
	if ($type == "1" && $type == "2") {
		$type = "simplerisk";
	} 	// If the type is 2
	else if ($type == "2") {
		$type = "ldap";
	} else
		$type = "INVALID";
	*/	
	// Verify that the two passwords are the same
	if ("$pass" == "$repeat_pass") {
		// Verify that the password meets the requirements
		if (is_strong_password($pass)) {
			// Verify that the user does not exist
			if (! user_exist ( $user )) {
				// Create a unique salt for the user
				$salt = generate_token ( 20 );
					
				// Hash the salt
				$salt_hash = '$2a$15$' . md5 ( $salt );
					
				// Generate the password hash
				$hash = generateHash ( $salt_hash, $pass );
					
				// Create a boolean for all
				$all = false;
					
				// Create a boolean for none
				$none = false;
				$team = null;
				// Create the team value
				foreach ( $teams as $value ) {
					// If the selected value is all
					if ($value == "all")
						$all = true;
						
					// If the selected value is none
					if ($value == "none")
						$none = true;
			
					$team .= ":";
					$team .= $value;
					$team .= ":";
				}
					
				// If no value was submitted then default to none
				if ($value == "")
					$none = true;
			
				// If all was selected then assign all teams
				if ($all)
					$team = "all";
			
				// If none was selected then assign no teams
				if ($none)
					$team = "none";
			
				// Insert a new user
				if (add_user ($company_id, $type, $user, $email, $name, $salt, $hash, $team, $admin, $review_critical, $review_high, $review_medium, $review_low, $submit_risks, $modify_risks, $plan_mitigations, $close_risks, $multi_factor )){
					// Audit log
					$risk_id = 1000;
					$message = "A new user was added by the \"" . $_SESSION ['user'] . "\" user.";
					write_log ( $risk_id, $_SESSION ['uid'], $message );
						
					$alert = "good";
					$alert_message = "The new user was added successfully.";
				}
				else {
					$alert = "bad";
					$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
				}
					
			} 		// Otherwise, the user already exists
			else {
				$alert = "bad";
				$alert_message = "The username already exists.  Please try again with a different username.";
			}
		}
		else{
			$alert = "bad";
			$alert_message = "The password entered was not strong enough.  Please try again with a different password that meets the following requirements:</br>A combination of upper and lower case characteres, digits and special characters.";
		} 
	} 	// Otherewise, the two passwords are different
	else {
		$alert = "bad";
		$alert_message = "The password and repeat password entered were different.  Please try again.";
	}
}

// Check if a user was enabled
if (isset ( $_POST ['enable_user'] )) {
	$value = ( int ) $_POST ['disabled_users'];
	
	// Verify value is an integer
	if (is_int ( $value )) {
		if (enable_user ( $value )){
			// Audit log
			$risk_id = 1000;
			$message = "A user was enabled by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
			
			// There is an alert message
			$alert = "good";
			$alert_message = "The user was enabled successfully.";
		}
		else {
			$alert = "bad";
			$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
		}
	}
}

// Check if a user was disabled
if (isset ( $_POST ['disable_user'] )) {
	$value = ( int ) $_POST ['enabled_users'];
	
	// Verify value is an integer
	if (is_int ( $value )) {
		if (disable_user ( $value )){
			// Audit log
			$risk_id = 1000;
			$message = "A user was disabled by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
			
			// There is an alert message
			$alert = "good";
			$alert_message = "The user was disabled successfully.";
		}
		else {
			$alert = "bad";
			$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
		}
	}
}

// Check if a user was deleted
if (isset ( $_POST ['delete_user'] )) {
	$value = ( int ) $_POST ['user'];
	
	// Verify value is an integer
	if (is_int ( $value )) {
		if (delete_value ( "user", $value )){
			// Audit log
			$risk_id = 1000;
			$message = "An existing user was deleted by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
			
			// There is an alert message
			$alert = "good";
			$alert_message = "The existing user was deleted successfully.";
		}
		else {
			$alert = "bad";
			$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
		}
	}
}

// Check if a password reset was requeted
if (isset ( $_POST ['password_reset'] )) {
	$value = ( int ) $_POST ['user'];
	
	// Verify value is an integer
	if (is_int ( $value )) {
		password_reset_by_userid ( $value );
		
		// Audit log
		$risk_id = 1000;
		$message = "A password reset request was submitted by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
		
		// There is an alert message
		$alert = "good";
		$alert_message = "A password reset email was sent to the user.";
	}
}

?>

<!doctype html>
<html>

<head>
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<title>R2MS: Reporting & Risk Management Service</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/bootstrap-responsive.css">
<script type="text/javascript">
      function handleSelection(choice) {
        if (choice=="1") {
          document.getElementById("simplerisk").style.display = "";
        }
        if (choice=="2") {
          document.getElementById("simplerisk").style.display = "none";
        }
      }
    </script>
</head>

<body>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/bootstrap-responsive.css">
	<link rel="stylesheet" href="../css/divshot-util.css">
	<link rel="stylesheet" href="../css/divshot-canvas.css">
	<link rel="stylesheet" href="../css/display.css">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" target="_blank" href="http://www.hacklabs.com/"></a>
				<div class="navbar-content">
					<ul class="nav">
						<li><a href="../index.php"><?php echo $lang['Home']; ?></a></li>
						<li><a href="../management/index.php"><?php echo $lang['RiskManagement']; ?></a>
						</li>
						<li><a href="../reports/index.php"><?php echo $lang['Reporting']; ?></a>
						</li>
						<li class="active"><a href="index.php"><?php echo $lang['Configure']; ?></a>
						</li>
					</ul>
				</div>
<?php
if (isset ( $_SESSION ["access"] ) && $_SESSION ["access"] == "granted") {
	echo "<div class=\"btn-group pull-right\">\n";
	echo "<a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">" . $_SESSION ['name'] . "<span class=\"caret\"></span></a>\n";
	echo "<ul class=\"dropdown-menu\">\n";
	echo "<li>\n";
	echo "<a href=\"../account/profile.php\">" . $lang ['MyProfile'] . "</a>\n";
	echo "</li>\n";
	echo "<li>\n";
	echo "<a href=\"../logout.php\">" . $lang ['Logout'] . "</a>\n";
	echo "</li>\n";
	echo "</ul>\n";
	echo "</div>\n";
}
?>
        </div>
		</div>
	</div>
<?php
if ($alert == "good") {
	echo "<div id=\"alert\" class=\"container-fluid\">\n";
	echo "<div class=\"row-fluid\">\n";
	echo "<div class=\"span12 greenalert\">" . $alert_message . "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "<br />\n";
} else if ($alert == "bad") {
	echo "<div id=\"alert\" class=\"container-fluid\">\n";
	echo "<div class=\"row-fluid\">\n";
	echo "<div class=\"span12 redalert\">" . $alert_message . "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "<br />\n";
}
?>
    <div class="container-fluid">
		<div class="row-fluid">
			<div class="span3">
				<ul class="nav  nav-pills nav-stacked">
					<li><a href="index.php"><?php echo $lang['ConfigureRiskFormula']; ?></a>
					</li>
					<li><a href="risk_template.php"><?php echo $lang['RiskTemplate']; ?></a>
					</li>
					<li><a href="review_settings.php"><?php echo $lang['ConfigureReviewSettings']; ?></a>
					</li>
					<li><a href="add_remove_values.php"><?php echo $lang['AddAndRemoveValues']; ?></a>
					</li>
					<li><a href="company_management.php"><?php echo $lang['CompanyManagement']; ?></a>
					</li>
					<li class="active"><a href="user_management.php"><?php echo $lang['UserManagement']; ?></a>
					</li>
					<li><a href="project_management.php"><?php echo $lang['ProjectManagement']; ?></a>
					</li>
					<li><a href="custom_names.php"><?php echo $lang['RedefineNamingConventions']; ?></a>
					</li>
					<li><a href="audit_trail.php"><?php echo $lang['AuditTrail']; ?></a>
					</li>
				</ul>
			</div>
			<div class="span9">
				<div class="row-fluid">
					<div class="span12">
						<div class="hero-unit">
							<form name="add_user" method="post" action="">
								<p>
								
								
								<h4><?php echo $lang['AddANewUser']; ?>:</h4>
		<?php echo $lang['Type']; ?>: <?php create_dropdown("typeuser"); ?>
		
		<!-- 
		<select name="type" id="select"
									onChange="handleSelection(value)">
									<option selected value="1">SimpleRisk</option>
		-->									
		<?php
		// If the custom authentication extra is enabeld
		if (custom_authentication_extra ()) {
			// Display the LDAP option
			echo "<option value=\"2\">LDAP</option>\n";
		}
		?>
                </select><br />
                
                <?php echo $lang['Company']; ?>: <?php create_dropdown("company",null,null,true,false,true); ?> </br>
                
                <?php echo $lang['FullName']; ?>: <input required name="name"
									type="text" maxlength="50" size="20" value="<?=@$_POST['name']?>" /><br />
                <?php echo $lang['EmailAddress']; ?>: <input required
									name="email" type="email" maxlength="200" size="20" value="<?=@$_POST['email']?>"/><br />
                <?php echo $lang['Username']; ?>: <input name="new_user"
									type="text" maxlength="20" size="20" value="<?=@$_POST['new_user']?>"/><br />
								<div id="simplerisk">
                <?php echo $lang['Password']; ?>: <input required name="password"
										type="password" pattern=".{8,}" title="8 minimum length with a combination of lower and upper case characters, digits and special characters" maxlength="50" size="20" autocomplete="off" /><br />
                <?php echo $lang['RepeatPassword']; ?>: <input required
										name="repeat_password" pattern=".{8,}" title="8 minimum length with a combination of lower and upper case characters, digits and special characters" type="password" maxlength="50"
										size="20" autocomplete="off" /><br />
								</div>
								<h6>
									<u><?php echo $lang['Teams']; ?></u>
								</h6>
                <?php create_multiple_dropdown("team", null, null, true); ?>
                <h6>
									<u><?php echo $lang['UserResponsibilities']; ?></u>
								</h6>
								<ul>
									<li><input name="submit_risks" type="checkbox" />&nbsp;<?php echo $lang['AbleToSubmitNewRisks']; ?></li>
									<li><input name="modify_risks" type="checkbox" />&nbsp;<?php echo $lang['AbleToModifyExistingRisks']; ?></li>
									<li><input name="close_risks" type="checkbox" />&nbsp;<?php echo $lang['AbleToCloseRisks']; ?></li>
									<li><input name="plan_mitigations" type="checkbox" />&nbsp;<?php echo $lang['AbleToPlanMitigations']; ?></li>
									<li><input name="review_low" type="checkbox" />&nbsp;<?php echo $lang['AbleToReviewLowRisks']; ?></li>
									<li><input name="review_medium" type="checkbox" />&nbsp;<?php echo $lang['AbleToReviewMediumRisks']; ?></li>
									<li><input name="review_high" type="checkbox" />&nbsp;<?php echo $lang['AbleToReviewHighRisks']; ?></li>
									<li><input name="review_critical" type="checkbox" />&nbsp;<?php echo $lang['AbleToReviewCriticalRisks']; ?></li>
									<li><input name="admin" type="checkbox" />&nbsp;<?php echo $lang['AllowAccessToConfigureMenu']; ?></li>
								</ul>
								<h6>
									<u><?php echo $lang['MultiFactorAuthentication']; ?></u>
								</h6>
								<input type="radio" name="multi_factor" value="1" checked />&nbsp;<?php echo $lang['None']; ?><br />
<?php
// If the custom authentication extra is installed
if (custom_authentication_extra ()) {
	// Include the custom authentication extra
	require_once (__DIR__ . "/../extras/authentication/index.php");
	
	// Display the multi factor authentication options
	multi_factor_authentication_options ( 1 );
}
?>
                <input type="submit" value="<?php echo $lang['Add']; ?>"
									name="add_user" /><br />
								</p>
							</form>
						</div>
						<div class="hero-unit">
							<form name="select_user" method="post"
								action="view_user_details.php">
								<p>
								
								
								<h4><?php echo $lang['ViewDetailsForUser']; ?>:</h4>
                <?php echo $lang['DetailsForUser']; ?> <?php create_dropdown("user"); ?>&nbsp;&nbsp;<input
									type="submit" value="<?php echo $lang['Select']; ?>"
									name="select_user" />
								</p>
							</form>
						</div>
						<div class="hero-unit">
							<form name="enable_disable_user" method="post" action="">
								<p>
								
								
								<h4><?php echo $lang['EnableAndDisableUsers']; ?>:</h4>
		<?php echo $lang['EnableAndDisableUsersHelp']; ?>.
		</p>
								<p>
                <?php echo $lang['DisableUser']; ?> <?php create_dropdown("enabled_users"); ?>&nbsp;&nbsp;<input
										type="submit" value="<?php echo $lang['Disable']; ?>"
										name="disable_user" />
								</p>
								<p>
                <?php echo $lang['EnableUser']; ?> <?php create_dropdown("disabled_users"); ?>&nbsp;&nbsp;<input
										type="submit" value="<?php echo $lang['Enable']; ?>"
										name="enable_user" />
								</p>
							</form>
						</div>
						<div class="hero-unit">
							<form name="delete_user" method="post" action="">
								<p>
								
								
								<h4><?php echo $lang['DeleteAnExistingUser']; ?>:</h4>
                <?php echo $lang['DeleteCurrentUser']; ?> <?php create_dropdown("user"); ?>&nbsp;&nbsp;<input
									type="submit" value="<?php echo $lang['Delete']; ?>"
									name="delete_user" />
								</p>
							</form>
						</div>
						<div class="hero-unit">
							<form name="password_reset" method="post" action="">
								<p>
								
								
								<h4><?php echo $lang['PasswordReset']; ?>:</h4>
                <?php echo $lang['SendPasswordResetEmailForUser']; ?> <?php create_dropdown("user"); ?>&nbsp;&nbsp;<input
									type="submit" value="<?php echo $lang['Send']; ?>"
									name="password_reset" />
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
