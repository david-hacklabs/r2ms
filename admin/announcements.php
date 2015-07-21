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

// Default is no alert
$alert = false;

// Check if access is authorized
if (! isset ( $_SESSION ["admin"] ) || $_SESSION ["admin"] != "1") {
	header ( "Location: ../index.php" );
	exit ( 0 );
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
							<h4>SimpleRisk Announcements</h4>
							<p>
                  <?php echo get_announcements(); ?>
                </p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
