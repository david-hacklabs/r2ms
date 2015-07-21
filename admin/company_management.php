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
if (isset ( $_POST ['add_company'] )) {
	$name = addslashes ( $_POST ['name'] );
	$address = addslashes ( $_POST ['address'] );
	$zip = addslashes ( $_POST ['zip'] );
	$country = addslashes ( $_POST ['country'] );
	$contactemail = addslashes ( $_POST ['contactemail'] );
	$contactname = addslashes ( $_POST ['contactname'] );
	if(!empty($name) && !empty($address) && !empty($zip) && (strlen($zip) < 5) && !empty($country) 
			&& !empty($contactemail) && preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $contactemail) && !empty($contactname)){
		
		// Verify that the user does not exist
		if (! company_exist ( $name )) {
			// Insert a new user
			if (add_company ( $name, $address, $zip, $country, $contactemail, $contactname)){
				// Audit log
				$risk_id = 1000;
				$message = "A new company was added by the \"" . $_SESSION ['user'] . "\" user.";
				write_log ( $risk_id, $_SESSION ['uid'], $message );
					
				$alert = "good";
				$alert_message = "The new company was added successfully.";
			}
			else {
				$alert = "bad";
				$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
			}
		} 		// Otherwise, the user already exists
		else {
			$alert = "bad";
			$alert_message = "The company already exists.  Please try again with a different company.";
		}
	}
	else {
		$risk_id = 1000;
		$message = "Error inserting new company \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
		$alert = "bad";
		$alert_message = "Error inserting new company.  Please try again.";
	}
}

// Check if a company was deleted
if (isset ( $_POST ['delete_company'] )) {
	$value = ( int ) $_POST ['company'];

	// Verify value is an integer
	if (is_int ( $value )) {
		if (delete_value ( "company", $value )){
			// Audit log
			$risk_id = 1000;
			$message = "An existing company was deleted by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
			
			// There is an alert message
			$alert = "good";
			$alert_message = "The existing company was deleted successfully.";
		}
		else {
			$alert = "bad";
			$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
		}
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
					<li class="active"><a href="company_management.php"><?php echo $lang['CompanyManagement']; ?></a>
					</li>
					<li><a href="user_management.php"><?php echo $lang['UserManagement']; ?></a>
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
							<form name="add_company" method="post" action="">
								<p>
								
								
								<h4><?php echo $lang['AddANewCompany']; ?>:</h4>
		<?php
		// If the custom authentication extra is enabeld
		if (custom_authentication_extra ()) {
			// Display the LDAP option
			echo "<option value=\"2\">LDAP</option>\n";
		}
		?>
				<?php echo $lang['CompanyName']; ?>: <input required name="name"
									type="text" maxlength="50" size="20" value="<?=@$_POST['name']?>"/><br />
                <?php echo $lang['CompanyAddress']; ?>: <input required
									name="address" type="text" maxlength="200" size="20" value="<?=@$_POST['address']?>"/><br />
				<?php echo $lang['CompanyZip']; ?>: <input
									name="zip" type="number" maxlength="5" value="<?=@$_POST['zip']?>"/><br />
                <?php echo $lang['CompanyCountry']; ?>: <?php create_dropdown("country", @$_POST['company'], null, true, false, true); ?><br />
				<?php echo $lang['CompanyEmailContact']; ?>: <input required name="contactemail"
									type="email" maxlength="200" size="20" value="<?=@$_POST['contactemail']?>"/><br />
				<?php echo $lang['CompanyNameContact']; ?>: <input required name="contactname"
									type="text" maxlength="20" size="20" value="<?=@$_POST['contactname']?>"/>
				<br />
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
									name="add_company" /><br />
								</p>
							</form>
						</div>
						<div class="hero-unit">
							<form name="select_company" method="post"
								action="view_company_details.php">
								<p>
								
								
								<h4><?php echo $lang['ViewDetailsForCompany']; ?>:</h4>
                <?php echo $lang['DetailsForCompany']; ?> <?php create_dropdown("company"); ?>&nbsp;&nbsp;<input
									type="submit" value="<?php echo $lang['Select']; ?>"
									name="select_company" />
								</p>
							</form>
						</div>
						<div class="hero-unit">
							<form name="delete_company" method="post" action="">
								<p>
								
								
								<h4><?php echo $lang['DeleteAnExistingCompany']; ?>:</h4>
                <?php echo $lang['DeleteCurrentUser']; ?> <?php create_dropdown("company"); ?>&nbsp;&nbsp;<input
									type="submit" value="<?php echo $lang['Delete']; ?>"
									name="delete_company" />
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
