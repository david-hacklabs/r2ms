<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * David Zarza Luna - HackLabs
 * Created 2014/5/22
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

// Check if a new project was submitted
if (isset ( $_POST ['add_project'] )) {
	
	$name = addslashes ( $_POST ['name'] );
	$company_id = (int) ( $_POST ['company'] );
	$category_id = (int) ( $_POST ['category'] );
	
	if (is_int ( $company_id ) && is_int( $category_id )) {
		if (existNameByValue('company', $company_id) && existNameByValue('category', $category_id)){
			
			// Insert a new project
			if (add_project ( $name, $company_id, $category_id )){
				// Audit log
				$risk_id = 1000;
				$message = "A new project was added by the \"" . $_SESSION ['user'] . "\" user.";
				write_log ( $risk_id, $_SESSION ['uid'], $message );
				
				$alert = "good";
				$alert_message = "The new project was added successfully.";
			}
			else {
				$alert = "bad";
				$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
			}	
		}
		else {
			
			// Audit log
			$risk_id = 1000;
			$message = "Alert!! Trying to add a new project with non existing company by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
			
			$alert = "bad";
			$alert_message = "The company does not exist.  Please try again with a different username.";
		}
	}
	else {
		
		// Audit log
		$risk_id = 1000;
		$message = "Alert!! Trying to add a new project with non number identification company by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
			
		$alert = "bad";
		$alert_message = "The company does not exist.  Please try again with a different username.";
		
	}
	
	
}

// Check if a project was submitted for deleting
if (isset ( $_POST ['add_project_version'] )) {
	
	$company_id = (int) ( $_POST ['addversion-company-select'] );
	$project_id = (int) ( $_POST ['addversion-project-select'] );
	$version_number = floatval( $_POST ['addversion-version'] );
	
	if (is_int ( $company_id ) && is_int( $project_id ) && $version_number > 0 && $version_number > 1) {
		if (existNameByValue('company', $company_id) && existNameByValue('project', $project_id)){
				
			// Insert a new version
			if (add_version_to_project ( $project_id, $version_number )){
				// Audit log
				$risk_id = 1000;
				$message = "A new version of an existent project was added by the \"" . $_SESSION ['user'] . "\" user.";
				write_log ( $risk_id, $_SESSION ['uid'], $message );
				
				$alert = "good";
				$alert_message = "The new version of an existent project was added successfully.";
			}
			else {
				$alert = "bad";
				$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
			}
		}
		else {
				
			// Audit log
			$risk_id = 1000;
			$message = "Alert!! Trying to add a new version of an existent project with non existing company by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
				
			$alert = "bad";
			$alert_message = "The company or category does not exist.  Please try again with a different username.";
		}
	}
	else {
	
		// Audit log
		$risk_id = 1000;
		$message = "Alert!! Trying to add a new version of an existent project with non number identification company by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
			
		$alert = "bad";
		$alert_message = "The project does not exist.  Please try again with a different username.";
	
	}
}

// Check if a project was submitted for deleting
if (isset ( $_POST ['remove_project'] )) {
	$company_id = (int) ( $_POST ['remove-company-select'] );
	$project_id = (int) ( $_POST ['remove-project-select'] );
	$version_id = (int) ( $_POST ['remove-project-version-select'] );
	
		
	if (is_int ( $company_id ) && is_int( $project_id ) && is_int( $version_id )) {
		if (existNameByValue('company', $company_id) && existNameByValue('project', $project_id) && existNameByValue('project_version', $version_id)
				&& project_exist_in_company($project_id, $company_id) && project_exist_in_version($project_id, $version_id)){
			
			
			// Delete version of project
			if (delete_value('project_version', $version_id)){
				// Audit log
				$risk_id = 1000;
				$message = "A version of one project was deleted by the \"" . $_SESSION ['user'] . "\" user.";
				write_log ( $risk_id, $_SESSION ['uid'], $message );
				
				$alert = "good";
				$alert_message = "The version was deleted successfully.";
			}
			else {
				$alert = "bad";
				$alert_message = "There where an issue with the DB after the attempt to update the content. Please try again.";
			}
			
		}
		else {
				
			// Audit log
			$risk_id = 1000;
			$message = "Alert!! Trying to delete version of one project fail by the \"" . $_SESSION ['user'] . "\" user.";
			write_log ( $risk_id, $_SESSION ['uid'], $message );
				
			$alert = "bad";
			$alert_message = "The company or project do not exist.  Please try again with a different data.";
		}
	}
	else {
	
		// Audit log
		$risk_id = 1000;
		$message = "Alert!! Trying to add a new project with non number identification company by the \"" . $_SESSION ['user'] . "\" user.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
			
		$alert = "bad";
		$alert_message = "The company does not exist. Please try again with a different username.";
	
	}
}

?>

<!doctype html>
<html>

<head>
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/project_management-ajax-request.js"></script>
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
					<li><a href="user_management.php"><?php echo $lang['UserManagement']; ?></a>
					</li>
					<li class="active"><a href="project_management.php"><?php echo $lang['ProjectManagement']; ?></a>
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
							<form name="add_project" method="post" action="">
								<p>
								<h4><?php echo $lang['AddProject']; ?>:</h4>
								<?php echo $lang['NameProject']; ?>: <input required name="name"
									type="text" maxlength="200" size="20" /><br />
								<?php echo $lang['Company']; ?>: <?php create_dropdown("company",null,null,true,false,true); ?> </br>
								<?php echo $lang['Category']; ?>: <?php create_dropdown("category",null,null,true,false,true); ?> </br>
								</p>
								<input type="submit" value="<?php echo $lang['Add']; ?>" name="add_project" />
							</form>
						</div>
						
						<div class="hero-unit">
							<form name="add_project_version" method="post" action="">
								<p>
								<h4><?php echo $lang['AddProjectVersion']; ?>:</h4>
								<?php echo $lang['Company']; ?>: <?php create_dropdown("company", null, 'addversion-company-select', true, false, true); ?> </br>
								<?php echo $lang['NameProject']; ?>: <?php create_dropdown(null,null, "addversion-project-select", true, false, true); ?> </br>
								<?php echo $lang['VersionNumber']; ?>: <input type="number" name='addversion-version' step="0.1" required />
								</p>
								<input type="submit" value="<?php echo $lang['Add']; ?>" name="add_project_version" />
							</form>
						</div>
						
						<div class="hero-unit">
							<form name="remove_project" method="post" action="">
								<p>
								<h4><?php echo $lang['RemoveProject']; ?>:</h4>
								<?php echo $lang['Company']; ?>: <?php create_dropdown("company", null, 'remove-company-select', true, false, true); ?> </br>
								<?php echo $lang['NameProject']; ?>: <?php create_dropdown(null,null, "remove-project-select", true, false, true); ?> </br>
								<?php echo $lang['VersionNumber']; ?>: <?php create_dropdown(null,null, "remove-project-version-select", true, false, true); ?>
								</p>
								<input type="submit" value="<?php echo $lang['Delete']; ?>"	name="remove_project" />
							</form>
						</div>
						
						<div class="hero-unit">
							<form id="project_to_client" method="post" action="">
							<p>
							<h4><?php echo $lang['AddProjectClients']; ?> or <?php echo $lang['RemoveProjectClients']; ?>:</h4>
							<?php echo $lang['Company']; ?>: <?php create_dropdown("company", null, 'company-select',true, false, true); ?> </br>
							<?php echo $lang['Project']; ?>: <?php create_dropdown(null, null, 'project-select',true, false, true); ?>
							<?php echo $lang['Clients']; ?>: <?php create_dropdown(null, null, "client-company",true, false, true); ?>
							</p>
							<input type="button" onClick="addProjectToClient();" value="<?php echo $lang['Add']; ?>" />
							<input type="button" onClick="deleteProjectToClient();" value="<?php echo $lang['Delete']; ?>" />
							</form>
							<div id="container-project-client" class="container-fluid" style="display:none">
								<div id="info-project-client" class="span12"></div>
							</div>
						</div>
						
						<div class="hero-unit">
							<p>
							<h4><?php echo $lang['ListProjectClients']; ?>:</h4>
							<?php echo $lang['Company']; ?>: <?php create_dropdown("company", null, 'company-list-select'); ?> </br>
							<?php echo $lang['Project']; ?>: <?php create_dropdown(null, null, 'project-list-select'); ?></br>
							<div id="clientwithprojects"><div>
							</p>
						</div>
					
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>

