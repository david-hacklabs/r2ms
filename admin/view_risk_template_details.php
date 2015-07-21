<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * David Zarza Luna - HackLabs 13/11/2014
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

// Default is no alert
$alert = false;

// Check if access is authorized
if (! isset ( $_SESSION ["access"] ) || $_SESSION ["access"] != "granted") {
	header ( "Location: ../index.php" );
	exit ( 0 );
}
if (!isset ( $_SESSION ["admin"] ) || $_SESSION ["admin"] != "1") {
	header ( "Location: ../index.php" );
	exit ( 0 );
}

if (isset ( $_POST ['update_risk_template'] ) && isset ( $_POST ['risk_template'] )) {
	//Get the risk_storage ID
	$risk_template_id = (int) $_POST ['risk_template'];
	//Verify the risk_storage ID
	if (is_int ($risk_template_id)){
		// Get the submitted values
		$name = $_POST['name'];
		$description = $_POST['description'];
		$impact = $_POST['impact'];
		$detail = $_POST['detail'];
		$recommendation = $_POST['recommendation'];
		$category_id = $_POST['category'];
		
		// Update the risk_template
		if (update_risk_template ( $risk_template_id, $name, $description, $impact, $detail , $recommendation , $category_id)){
			// Audit log
			$risk_id = 1000;
			$message = "An existing risk template template was modified by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
			
			$alert = "good";
			$alert_message = "The risk template template was updated successfully.";
		}
		else {
			$alert = "bad";
			$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
		}
	}
	
}

if (isset ($_POST['risk_template'])) {
	
	//Get the risk template ID
	$risk_template_id = ( int ) $_POST ['risk_template'];
	
	// Get the risk storage template information
	
	$risk_template_info = get_risk_template_by_id ( $risk_template_id );
	
	$name = $risk_template_info ['name'];
	$description = $risk_template_info ['description']; 
	$impact = $risk_template_info ['impact'];
	$detail = $risk_template_info ['detail'];
	$recommendation = $risk_template_info ['recommendation'];
	$category_id = $risk_template_info ['category_id'];
	
}
else {
	
	$risk_template_id = "";
	
	$name = "N/A";
	$description = "N/A";
	$impact = "N/A";
	$detail = "N/A";
	$recommendation = "N/A";
}

?>

<!doctype html>
<html>

<head>
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<title>R2MS: Reporting &amp; Risk Management Service</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/bootstrap-responsive.css">
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
						<li class="active"><a href="risk_template.php"><?php echo $lang['RiskTemplate']; ?></a>
						</li>
						<li><a href="review_settings.php"><?php echo $lang['ConfigureReviewSettings']; ?></a>
						</li>
						<li><a href="add_remove_values.php"><?php echo $lang['AddAndRemoveValues']; ?></a>
						</li>
						<li><a href="company_management.php"><?php echo $lang['CompanyManagement']; ?></a>
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
								<h4><?php echo $lang['DocumentANewRisk']; ?></h4>
								<p><?php echo $lang['UseThisFormHelp2']; ?>.</p>
								<form name="update_risk_template" method="post" action="">
									<input name="risk_template" type="hidden" value="<?=$risk_template_id ?>" />
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="200px"><?php echo $lang['Subject']; ?>:</td>
											<td><input maxlength="100" name="name" id="name"
												class="input-medium" type="text" value="<?php echo htmlentities($name, ENT_QUOTES, 'UTF-8'); ?>" required></td>
										</tr>
									</table>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="200px"><?php echo $lang['Category']; ?>:</td>
											<td><?php create_dropdown("category", $category_id) ?></td>
										</tr>
									</table>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="200px"><?php echo $lang['Description']; ?>:</td>
											<td><textarea name="description" cols="50" rows="3"
													id="description"><?php echo htmlentities($description, ENT_QUOTES, 'UTF-8'); ?></textarea></td>
										</tr>
										<tr>
											<td width="200px"><?php echo $lang['Impact']; ?>:</td>
											<td><textarea name="impact" cols="50" rows="3"
													id="impact"><?php echo htmlentities($impact, ENT_QUOTES, 'UTF-8'); ?></textarea></td>
										</tr>
										<tr>
											<td width="200px"><?php echo $lang['Detail']; ?>:</td>
											<td><textarea name="detail" cols="50" rows="3"
													id="detail"><?php echo htmlentities($detail, ENT_QUOTES, 'UTF-8'); ?></textarea></td>
										</tr>
										<tr>
											<td width="200px"><?php echo $lang['Recommendation']; ?>:</td>
											<td><textarea name="recommendation" cols="50" rows="3"
													id="recommendation"><?php echo htmlentities($recommendation, ENT_QUOTES, 'UTF-8'); ?></textarea></td>
										</tr>
									</table>
									<div class="form-actions">
										<button type="submit" name="update_risk_template" class="btn btn-primary" value="Submit"><?php echo $lang['Update']; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

</body>

</html>
