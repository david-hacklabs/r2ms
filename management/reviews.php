<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

// Include required functions file
require_once ('../includes/functions.php');
require_once ('../includes/authenticate.php');
require_once ('../includes/display.php');

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
if (isset ( $_GET ['id'] )) {
	$id = htmlentities ( $_GET ['id'], ENT_QUOTES, 'UTF-8' );
	
	// Get the details of the risk
	$risk = get_risk_by_id ( $id );
	
	$status = htmlentities ( $risk [0] ['status'], ENT_QUOTES, 'UTF-8' );
	$subject = htmlentities ( stripslashes ( $risk [0] ['subject'] ), ENT_QUOTES, 'UTF-8' );
	$calculated_risk = htmlentities ( $risk [0] ['calculated_risk'], ENT_QUOTES, 'UTF-8' );
	$mgmt_review = htmlentities ( $risk [0] ['mgmt_review'], ENT_QUOTES, 'UTF-8' );
	
	$scoring_method = htmlentities ( $risk [0] ['scoring_method'], ENT_QUOTES, 'UTF-8' );
	$CLASSIC_likelihood = htmlentities ( $risk [0] ['CLASSIC_likelihood'], ENT_QUOTES, 'UTF-8' );
	$CLASSIC_impact = htmlentities ( $risk [0] ['CLASSIC_impact'], ENT_QUOTES, 'UTF-8' );
	$AccessVector = htmlentities ( $risk [0] ['CVSS_AccessVector'], ENT_QUOTES, 'UTF-8' );
	$AccessComplexity = htmlentities ( $risk [0] ['CVSS_AccessComplexity'], ENT_QUOTES, 'UTF-8' );
	$Authentication = htmlentities ( $risk [0] ['CVSS_Authentication'], ENT_QUOTES, 'UTF-8' );
	$ConfImpact = htmlentities ( $risk [0] ['CVSS_ConfImpact'], ENT_QUOTES, 'UTF-8' );
	$IntegImpact = htmlentities ( $risk [0] ['CVSS_IntegImpact'], ENT_QUOTES, 'UTF-8' );
	$AvailImpact = htmlentities ( $risk [0] ['CVSS_AvailImpact'], ENT_QUOTES, 'UTF-8' );
	$Exploitability = htmlentities ( $risk [0] ['CVSS_Exploitability'], ENT_QUOTES, 'UTF-8' );
	$RemediationLevel = htmlentities ( $risk [0] ['CVSS_RemediationLevel'], ENT_QUOTES, 'UTF-8' );
	$ReportConfidence = htmlentities ( $risk [0] ['CVSS_ReportConfidence'], ENT_QUOTES, 'UTF-8' );
	$CollateralDamagePotential = htmlentities ( $risk [0] ['CVSS_CollateralDamagePotential'], ENT_QUOTES, 'UTF-8' );
	$TargetDistribution = htmlentities ( $risk [0] ['CVSS_TargetDistribution'], ENT_QUOTES, 'UTF-8' );
	$ConfidentialityRequirement = htmlentities ( $risk [0] ['CVSS_ConfidentialityRequirement'], ENT_QUOTES, 'UTF-8' );
	$IntegrityRequirement = htmlentities ( $risk [0] ['CVSS_IntegrityRequirement'], ENT_QUOTES, 'UTF-8' );
	$AvailabilityRequirement = htmlentities ( $risk [0] ['CVSS_AvailabilityRequirement'], ENT_QUOTES, 'UTF-8' );
	$DREADDamagePotential = htmlentities ( $risk [0] ['DREAD_DamagePotential'], ENT_QUOTES, 'UTF-8' );
	$DREADReproducibility = htmlentities ( $risk [0] ['DREAD_Reproducibility'], ENT_QUOTES, 'UTF-8' );
	$DREADExploitability = htmlentities ( $risk [0] ['DREAD_Exploitability'], ENT_QUOTES, 'UTF-8' );
	$DREADAffectedUsers = htmlentities ( $risk [0] ['DREAD_AffectedUsers'], ENT_QUOTES, 'UTF-8' );
	$DREADDiscoverability = htmlentities ( $risk [0] ['DREAD_Discoverability'], ENT_QUOTES, 'UTF-8' );
	$OWASPSkillLevel = htmlentities ( $risk [0] ['OWASP_SkillLevel'], ENT_QUOTES, 'UTF-8' );
	$OWASPMotive = htmlentities ( $risk [0] ['OWASP_Motive'], ENT_QUOTES, 'UTF-8' );
	$OWASPOpportunity = htmlentities ( $risk [0] ['OWASP_Opportunity'], ENT_QUOTES, 'UTF-8' );
	$OWASPSize = htmlentities ( $risk [0] ['OWASP_Size'], ENT_QUOTES, 'UTF-8' );
	$OWASPEaseOfDiscovery = htmlentities ( $risk [0] ['OWASP_EaseOfDiscovery'], ENT_QUOTES, 'UTF-8' );
	$OWASPEaseOfExploit = htmlentities ( $risk [0] ['OWASP_EaseOfExploit'], ENT_QUOTES, 'UTF-8' );
	$OWASPAwareness = htmlentities ( $risk [0] ['OWASP_Awareness'], ENT_QUOTES, 'UTF-8' );
	$OWASPIntrusionDetection = htmlentities ( $risk [0] ['OWASP_IntrusionDetection'], ENT_QUOTES, 'UTF-8' );
	$OWASPLossOfConfidentiality = htmlentities ( $risk [0] ['OWASP_LossOfConfidentiality'], ENT_QUOTES, 'UTF-8' );
	$OWASPLossOfIntegrity = htmlentities ( $risk [0] ['OWASP_LossOfIntegrity'], ENT_QUOTES, 'UTF-8' );
	$OWASPLossOfAvailability = htmlentities ( $risk [0] ['OWASP_LossOfAvailability'], ENT_QUOTES, 'UTF-8' );
	$OWASPLossOfAccountability = htmlentities ( $risk [0] ['OWASP_LossOfAccountability'], ENT_QUOTES, 'UTF-8' );
	$OWASPFinancialDamage = htmlentities ( $risk [0] ['OWASP_FinancialDamage'], ENT_QUOTES, 'UTF-8' );
	$OWASPReputationDamage = htmlentities ( $risk [0] ['OWASP_ReputationDamage'], ENT_QUOTES, 'UTF-8' );
	$OWASPNonCompliance = htmlentities ( $risk [0] ['OWASP_NonCompliance'], ENT_QUOTES, 'UTF-8' );
	$OWASPPrivacyViolation = htmlentities ( $risk [0] ['OWASP_PrivacyViolation'], ENT_QUOTES, 'UTF-8' );
	$custom = htmlentities ( $risk [0] ['Custom'], ENT_QUOTES, 'UTF-8' );
	
	// Get the management reviews for the risk
	$mgmt_reviews = get_review_by_id ( $id );
	
	$review_date = htmlentities ( $mgmt_reviews [0] ['submission_date'], ENT_QUOTES, 'UTF-8' );
	$review = htmlentities ( $mgmt_reviews [0] ['review'], ENT_QUOTES, 'UTF-8' );
	$reviewer = htmlentities ( $mgmt_reviews [0] ['reviewer'], ENT_QUOTES, 'UTF-8' );
	$next_step = htmlentities ( stripslashes ( $mgmt_reviews [0] ['next_step'] ), ENT_QUOTES, 'UTF-8' );
	$comments = htmlentities ( stripslashes ( $mgmt_reviews [0] ['comments'] ), ENT_QUOTES, 'UTF-8' );
	
	if ($review_date == "") {
		$review_date = "N/A";
	} else
		$review_date = date ( DATETIME, strtotime ( $review_date ) );
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
<link rel="stylesheet" href="../css/divshot-util.css">
<link rel="stylesheet" href="../css/divshot-canvas.css">
<link rel="stylesheet" href="../css/display.css">
<script type="text/javascript">
      function showScoreDetails() {
        document.getElementById("scoredetails").style.display = "";
        document.getElementById("hide").style.display = "";
        document.getElementById("show").style.display = "none";
      }

      function hideScoreDetails() {
        document.getElementById("scoredetails").style.display = "none";
        document.getElementById("updatescore").style.display = "none";
        document.getElementById("hide").style.display = "none";
        document.getElementById("show").style.display = "";
      }

      function updateScore() {
        document.getElementById("scoredetails").style.display = "none";
        document.getElementById("updatescore").style.display = "";
        document.getElementById("show").style.display = "none";
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
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" target="_blank" href="http://www.hacklabs.com/"></a>
				<div class="navbar-content">
					<ul class="nav">
						<li><a href="../index.php"><?php echo $lang['Home']; ?></a></li>
						<li class="active"><a href="index.php"><?php echo $lang['RiskManagement']; ?></a>
						</li>
						<li><a href="../reports/index.php"><?php echo $lang['Reporting']; ?></a>
						</li>
<?php
if (isset ( $_SESSION ["admin"] ) && $_SESSION ["admin"] == "1") {
	echo "<li>\n";
	echo "<a href=\"../admin/index.php\">" . $lang ['Configure'] . "</a>\n";
	echo "</li>\n";
}
echo "</ul>\n";
echo "</div>\n";

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
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
					<ul class="nav  nav-pills nav-stacked">
						<li><a href="index.php">I. <?php echo $lang['SubmitYourRisks']; ?></a>
						</li>
						<li><a href="plan_mitigations.php">II. <?php echo $lang['PlanYourMitigations']; ?></a>
						</li>
						<li><a href="management_review.php">III. <?php echo $lang['PerformManagementReviews']; ?></a>
						</li>
						<li class="active"><a href="review_risks.php">IV. <?php echo $lang['ReviewRisksRegularly']; ?></a>
						</li>
					</ul>
				</div>
				<div class="span9">
					<div class="row-fluid">
						<div class="well">
              <?php view_top_table($id, $calculated_risk, $subject, $status, true); ?>
            </div>
					</div>
					<div id="scoredetails" class="row-fluid" style="display: none;">
						<div class="well">
                  <?php
																		// Scoring method is Classic
																		if ($scoring_method == "1") {
																			classic_scoring_table ( $id, $calculated_risk, $CLASSIC_likelihood, $CLASSIC_impact );
																		} 																		// Scoring method is CVSS
																		else if ($scoring_method == "2") {
																			cvss_scoring_table ( $id, $calculated_risk, $AccessVector, $AccessComplexity, $Authentication, $ConfImpact, $IntegImpact, $AvailImpact, $Exploitability, $RemediationLevel, $ReportConfidence, $CollateralDamagePotential, $TargetDistribution, $ConfidentialityRequirement, $IntegrityRequirement, $AvailabilityRequirement );
																		} 																		// Scoring method is DREAD
																		else if ($scoring_method == "3") {
																			dread_scoring_table ( $id, $calculated_risk, $DREADDamagePotential, $DREADReproducibility, $DREADExploitability, $DREADAffectedUsers, $DREADDiscoverability );
																		} 																		// Scoring method is OWASP
																		else if ($scoring_method == "4") {
																			owasp_scoring_table ( $id, $calculated_risk, $OWASPSkillLevel, $OWASPEaseOfDiscovery, $OWASPLossOfConfidentiality, $OWASPFinancialDamage, $OWASPMotive, $OWASPEaseOfExploit, $OWASPLossOfIntegrity, $OWASPReputationDamage, $OWASPOpportunity, $OWASPAwareness, $OWASPLossOfAvailability, $OWASPNonCompliance, $OWASPSize, $OWASPIntrusionDetection, $OWASPLossOfAccountability, $OWASPPrivacyViolation );
																		} 																		// Scoring method is Custom
																		else if ($scoring_method == "5") {
																			custom_scoring_table ( $id, $custom );
																		}
																		?>
            </div>
					</div>
					<div id="updatescore" class="row-fluid" style="display: none;">
						<div class="well">
                  <?php
																		// Scoring method is Classic
																		if ($scoring_method == "1") {
																			edit_classic_score ( $CLASSIC_likelihood, $CLASSIC_impact );
																		} 																		// Scoring method is CVSS
																		else if ($scoring_method == "2") {
																			edit_cvss_score ( $AccessVector, $AccessComplexity, $Authentication, $ConfImpact, $IntegImpact, $AvailImpact, $Exploitability, $RemediationLevel, $ReportConfidence, $CollateralDamagePotential, $TargetDistribution, $ConfidentialityRequirement, $IntegrityRequirement, $AvailabilityRequirement );
																		} 																		// Scoring method is DREAD
																		else if ($scoring_method == "3") {
																			edit_dread_score ( $DREADDamagePotential, $DREADReproducibility, $DREADExploitability, $DREADAffectedUsers, $DREADDiscoverability );
																		} 																		// Scoring method is OWASP
																		else if ($scoring_method == "4") {
																			edit_owasp_score ( $OWASPSkillLevel, $OWASPMotive, $OWASPOpportunity, $OWASPSize, $OWASPEaseOfDiscovery, $OWASPEaseOfExploit, $OWASPAwareness, $OWASPIntrusionDetection, $OWASPLossOfConfidentiality, $OWASPLossOfIntegrity, $OWASPLossOfAvailability, $OWASPLossOfAccountability, $OWASPFinancialDamage, $OWASPReputationDamage, $OWASPNonCompliance, $OWASPPrivacyViolation );
																		} 																		// Scoring method is Custom
																		else if ($scoring_method == "5") {
																			edit_custom_score ( $custom );
																		}
																		?>
            </div>
					</div>
					<div class="row-fluid">
						<div class="well">
							<h4><?php echo $lang['ReviewHistory']; ?></h4>
              <?php get_reviews($id); ?>
            </div>
					</div>
				</div>
			</div>
		</div>

</body>

</html>
