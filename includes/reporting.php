<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * David Zarza Luna - HackLabs
* Start Modyfing 2014/9/5
*/

// Include required configuration files
require_once ('functions.php');
require_once ('HighchartsPHP/Highchart.php');
require_once (language_file ());

/**
 * **************************
 * FUNCTION: GET OPEN RISKS *
 * **************************
 */
function get_open_risks($project) {
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT * FROM `risks` WHERE status != \"Closed\" AND project_version_id = $project" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	return count ( $array );
}

/**
 * ****************************
 * FUNCTION: GET CLOSED RISKS *
 * ****************************
 */
function get_closed_risks($project) {
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT * FROM `risks` WHERE status = \"Closed\" AND project_version_id = $project" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	return count ( $array );
}

/**
 * **************************
 * FUNCTION: GET RISK TREND *
 * **************************
 */
function get_risk_trend($project) {
	$chart = new Highchart ();
	$chart->includeExtraScripts ();
	
	$chart->chart->type = "arearange";
	$chart->chart->zoomType = "x";
	$chart->title->text = "Risks Opened and Closed Over Time";
	$chart->xAxis->type = "datetime";
	$chart->yAxis->title->text = null;
	$chart->yAxis->min = 0;
	$chart->tooltip = array (
			'crosshairs' => true,
			'shared' => true,
			'valueSuffix' => ' risk(s)' 
	);
	$chart->legend->enabled = false;
	$chart->chart->renderTo = "risk_trend_chart";
	$chart->credits->enabled = false;
	$chart->plotOptions->series->marker->enabled = false;
	$chart->plotOptions->series->marker->lineWidth = "2";
	// These set the marker symbol when selected
	$chart->plotOptions->series->marker->symbol = "circle";
	$chart->plotOptions->series->marker->states->hover->enabled = true;
	$chart->plotOptions->series->marker->states->hover->fillColor = "white";
	$chart->plotOptions->series->marker->states->hover->lineColor = "black";
	$chart->plotOptions->series->marker->states->hover->lineWidth = "2";
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT DATE(submission_date) date, COUNT(DISTINCT id) count FROM `risks` WHERE project_version_id = $project GROUP BY DATE(submission_date) ORDER BY DATE(submission_date)" );
	$stmt->execute ();
	
	// Store the list in the array
	$opened_risks = $stmt->fetchAll ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT DATE(a.closure_date) date, COUNT(DISTINCT b.id) count FROM `closures` a JOIN `risks` b ON a.risk_id = b.id WHERE b.status = \"Closed\" AND b.project_version_id = $project GROUP BY DATE(a.closure_date)" );
	$stmt->execute ();
	
	// Store the list in the array
	$closed_risks = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the opened risks array is empty
	if (empty ( $opened_risks )) {
		$opened_risk_data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Set the sum to 0
		$opened_sum = 0;
		$closed_sum = 0;
		
		// Set the start date
		$date = $opened_risks [0] ['date'];
		
		// For each date from the start date until today
		while ( strtotime ( $date ) <= time () ) {
			// If the PHP version is >= 5.5.0
			// array_column is new as of PHP 5.5
			if (strnatcmp ( phpversion (), '5.5.0' ) >= 0) {
				// Search the opened array for the value
				$opened_search = array_search ( $date, array_column ( $opened_risks, 'date' ) );
			} else
				$opened_search = false;
				
				// If the current date is in the opened array
			if ($opened_search !== false) {
				$count = $opened_risks [$opened_search] ['count'];
				$opened_sum += $count;
			}
			
			// If the PHP version is >= 5.5.0
			// array_column is new as of PHP 5.5
			if (strnatcmp ( phpversion (), '5.5.0' ) >= 0) {
				// Search the closed array for the value
				$closed_search = array_search ( $date, array_column ( $closed_risks, 'date' ) );
			} else
				$closed_search = false;
				
				// If the current date is in the closed array
			if ($closed_search !== false) {
				$count = $closed_risks [$closed_search] ['count'];
				$closed_sum += $count;
			}
			
			// Create the data arrays
			$opened_risk_data [] = array (
					(strtotime ( $date ) + 2 * 86400) * 1000,
					$opened_sum 
			);
			$closed_risk_data [] = array (
					(strtotime ( $date ) + 2 * 86400) * 1000,
					$closed_sum 
			);
			$trend_data [] = array (
					(strtotime ( $date ) + 2 * 86400) * 1000,
					$opened_sum - $closed_sum 
			);
			
			// Increment the date one day
			$date = date ( "Y-m-d", strtotime ( "+1 day", strtotime ( $date ) ) );
		}
		
		// Draw the open risks line
		$chart->series [] = array (
				'type' => "line",
				'name' => "Opened Risks",
				'color' => "red",
				'lineWidth' => "2",
				'data' => $opened_risk_data 
		);
		
		// Draw the closed risks line
		$chart->series [] = array (
				'type' => "line",
				'name' => "Closed Risks",
				'color' => "blue",
				'lineWidth' => "2",
				'data' => $closed_risk_data 
		);
		
		// Draw the trend line
		$chart->series [] = array (
				'type' => "line",
				'name' => "Trend",
				'color' => "#000000",
				'lineWidth' => "2",
				'data' => $trend_data 
		);
	}
	$htmloutput = '';
	
	$htmloutput.= $chart->printScripts2 ();
	$htmloutput.= "<div id=\"risk_trend_chart\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "risk_trend_chart" );
	$htmloutput.= "</script>\n";
	return $htmloutput; 
}

/**
 * ********************************
 * FUNCTION: COMPARE OPEN RISK BARS *
 * ********************************
 */

function compare_open_risk_bar($project, $project2){
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "compare_open_risk_bar";
	$chart->chart->type = "column";
	$chart->title->text = "Comparison Open Risks Level";
	
	$chart->xAxis->categories = array();
	$chart->yAxis->min = 0;
	$chart->yAxis->title->text = 'Number of risks';
	$chart->legend->layout = "vertical";
	$chart->legend->backgroundColor = "#FFFFFF";
	$chart->legend->align = "left";
	$chart->legend->verticalAlign = "top";
	$chart->legend->x = 100;
	$chart->legend->y = 70;
	$chart->legend->floating = 1;
	$chart->legend->shadow = 1;
	
	
	
	$chart->tooltip->formatter = new HighchartJsExpr("function() {
		return '' + this.x +': '+ this.y +' vulnerabilities(s) open';}");
	
	$chart->plotOptions->column->pointPadding = 0.2;
	$chart->plotOptions->column->borderWidth = 0;
	
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Get the risk levels
	$stmt = $db->prepare ( "SELECT * from `risk_levels` ORDER BY value DESC" );
	$stmt->execute ();
	$array = $stmt->fetchAll ();
	$critical = $array [0] [0];
	$high = $array [1] [0];
	$medium = $array [2] [0];
	$low = $array [3] [0];
	
	// Query the database
	$stmt = $db->prepare ( "select a.calculated_risk, COUNT(*) AS num, CASE WHEN a.calculated_risk > " . $high . " THEN 'Critical' WHEN a.calculated_risk < " . $critical . " AND a.calculated_risk > " . $medium . " THEN 'High' WHEN a.calculated_risk < " . $high . " AND a.calculated_risk > " . $low . " THEN 'Medium' WHEN a.calculated_risk < " . $medium . " THEN 'Low' END AS level from `risk_scoring` a JOIN `risks` b ON a.id = b.id WHERE b.status != \"Closed\" AND b.project_version_id = $project GROUP BY level ORDER BY a.calculated_risk DESC" );
	$stmt2 = $db->prepare ( "select a.calculated_risk, COUNT(*) AS num, CASE WHEN a.calculated_risk > " . $high . " THEN 'Critical' WHEN a.calculated_risk < " . $critical . " AND a.calculated_risk > " . $medium . " THEN 'High' WHEN a.calculated_risk < " . $high . " AND a.calculated_risk > " . $low . " THEN 'Medium' WHEN a.calculated_risk < " . $medium . " THEN 'Low' END AS level from `risk_scoring` a JOIN `risks` b ON a.id = b.id WHERE b.status != \"Closed\" AND b.project_version_id = $project2 GROUP BY level ORDER BY a.calculated_risk DESC" );
	$stmt3 = $db->prepare("SELECT a.value, CONCAT(b.name,'-v.',a.name,' ',c.name, ' ', DAY(a.creation),'/',MONTHNAME(a.creation), '/', YEAR(a.creation)) as name
							FROM project_version a
							INNER JOIN project as b ON a.project_id = b.value
							INNER JOIN category as c ON b.category_id = c.value
							WHERE a.value = $project");
	$stmt4 = $db->prepare("SELECT a.value, CONCAT(b.name,'-v.',a.name,' ',c.name, ' ', DAY(a.creation),'/',MONTHNAME(a.creation), '/', YEAR(a.creation)) as name
							FROM project_version a
							INNER JOIN project as b ON a.project_id = b.value
							INNER JOIN category as c ON b.category_id = c.value
							WHERE a.value = $project2");
	$stmt->execute ();
	$stmt2->execute ();
	$stmt3->execute ();
	$stmt4->execute ();
	
	// Store the list in the array
	$risksProject = $stmt->fetchAll ();
	$risksProject2 = $stmt2->fetchAll ();
	$nameProject = $stmt3->fetchAll ();
	$nameProject2 = $stmt4->fetchAll ();
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0
		);
	} 	// Otherwise
	else {
		$chart->xAxis->categories = array(
			$nameProject[0]['name'],
			$nameProject2[0]['name']
		);
		
		// Create the data array
		$data = array();
		$data['Critical'] = array();
		$data['High'] = array();
		$data['Medium'] = array();
		$data['Low'] = array();
		$data['Irrelevant'] = array();
		
		$dataCritical = array();
		$dataHigh = array();
		$dataMedium = array();
		$dataLow = array();
		$dataIrrelevant = array();
		if(!array_search('Critical', $risksProject)) $data['Critical'][0] = 0;
		if(!array_search('High', $risksProject)) $data['High'][0] = 0;
		if(!array_search('Medium', $risksProject)) $data['Medium'][0] = 0;
		if(!array_search('Low', $risksProject)) $data['Low'][0] = 0;
		if(!array_search('Critical', $risksProject2)) $data['Critical'][1] = 0;
		if(!array_search('High', $risksProject2)) $data['High'][1] = 0;
		if(!array_search('Medium', $risksProject2)) $data['Medium'][1] = 0;
		if(!array_search('Low', $risksProject2)) $data['Low'][1] = 0;
		
		foreach ( $risksProject as $row ) {
			switch ($row ['level']){
				case 'Critical':
					$data['Critical'][0] = ( int ) $row ['num'];
					break;
				case 'High': 
					$data['High'][0] = ( int ) $row ['num'];
					break;
				case 'Medium': 
					$data['Medium'][0] = ( int ) $row ['num'];
					break;
				case 'Low': 
					$data['Low'][0] = ( int ) $row ['num'];
					break;
			}
		}
		
		foreach ( $risksProject2 as $row ) {
			switch ($row ['level']){
				case 'Critical':
					$data['Critical'][1] = ( int ) $row ['num'];
					break;
				case 'High': 
					$data['High'][1] = ( int ) $row ['num'];
					break;
				case 'Medium': 
					$data['Medium'][1] = ( int ) $row ['num'];
					break;
				case 'Low': 
					$data['Low'][1] = ( int ) $row ['num'];
					break;
			}
		}
		
		$chart->series [] = array (
				'name' => "Critical",
				'color' => '#FF00FF',
				'data' => $data['Critical']
		);
		$chart->series [] =	array (
				'name' => "High",
				'color' => '#FF0000',
				'data' => $data['High']
		);
		$chart->series [] =	array (
				'name' => "Medium",
				'color' => '#FF9900',
				'data' => $data['Medium']
		);
		$chart->series [] =	array (
				'name' => "Low",
				'color' => '#99cc00',
				'data' => $data['Low']
		);
		
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_level_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_level_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ********************************
 * FUNCTION: OPEN RISK LEVEL PIE *
 * ********************************
 */
function open_risk_level_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_level_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Risk Level";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->plotOptions->pie->colors = array ();
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Get the risk levels
	$stmt = $db->prepare ( "SELECT * from `risk_levels` ORDER BY value DESC" );
	$stmt->execute ();
	$array = $stmt->fetchAll ();
	$critical = $array [0] [0];
	$high = $array [1] [0];
	$medium = $array [2] [0];
	$low = $array [3] [0];
	
	// Query the database
	$stmt = $db->prepare ( "select a.calculated_risk, COUNT(*) AS num, CASE WHEN a.calculated_risk > " . $high . " THEN 'Critical' WHEN a.calculated_risk < " . $critical . " AND a.calculated_risk > " . $medium . " THEN 'High' WHEN a.calculated_risk < " . $high . " AND a.calculated_risk > " . $low . " THEN 'Medium' WHEN a.calculated_risk < " . $medium . " THEN 'Low' END AS level from `risk_scoring` a JOIN `risks` b ON a.id = b.id WHERE b.status != \"Closed\" AND project_version_id = $project GROUP BY level ORDER BY a.calculated_risk DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	//print_r($array);
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			switch ($row ['level']){
				case 'Critical': $chart->plotOptions->pie->colors[] = '#FF00FF';break;
				case 'High': $chart->plotOptions->pie->colors[] = '#FF0000';break;
				case 'Medium': $chart->plotOptions->pie->colors[] = '#FF9900';break;
				case 'Low': $chart->plotOptions->pie->colors[] = '#70DE1D';break;
				case 'Irrelevant': $chart->plotOptions->pie->colors[] = '#D8D8D8';break;
			}
			$data [] = array (
					$row ['level'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Level",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_level_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_level_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ********************************
 * FUNCTION: OPEN RISK STATUS PIE *
 * ********************************
 */
function open_risk_status_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_status_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Status";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
    	return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT status, COUNT(*) AS num FROM `risks` WHERE status != \"Closed\" and project_version_id = $project GROUP BY status ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['status'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_status_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_status_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * **********************************
 * FUNCTION: CLOSED RISK REASON PIE *
 * **********************************
 */
function closed_risk_reason_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "closed_risk_reason_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Reasons";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT a.close_reason, b.id, b.status, c.name, COUNT(*) AS num FROM `closures` a JOIN `risks` b ON a.risk_id = b.id JOIN `close_reason` c ON a.close_reason= c.value WHERE b.status = \"Closed\" AND project_version_id = $project GROUP BY c.name ORDER BY COUNT(*) DESC;" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"closed_risk_reason_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "closed_risk_reason_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * **********************************
 * FUNCTION: OPEN RISK LOCATION PIE *
 * **********************************
 */
function open_risk_location_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_location_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Sites/Locations";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT b.name, COUNT(*) AS num FROM `risks` a INNER JOIN `location` b ON a.location = b.value WHERE status != \"Closed\" AND project_version_id = $project GROUP BY b.name ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_location_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_location_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * **********************************
 * FUNCTION: OPEN RISK CATEGORY PIE *
 * **********************************
 */
function open_risk_category_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_category_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Categories";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT b.name, COUNT(*) AS num FROM `risks` a INNER JOIN `category` b ON a.category = b.value WHERE status != \"Closed\" AND project_version_id = $project GROUP BY b.name ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_category_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_category_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ******************************
 * FUNCTION: OPEN RISK TEAM PIE *
 * ******************************
 */
function open_risk_team_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_team_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Teams";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT b.name, COUNT(*) AS num FROM `risks` a INNER JOIN `team` b ON a.team = b.value WHERE status != \"Closed\" and project_version_id = $project GROUP BY b.name ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_team_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_team_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ************************************
 * FUNCTION: OPEN RISK TECHNOLOGY PIE *
 * ************************************
 */
function open_risk_technology_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_technology_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Technologies";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT b.name, COUNT(*) AS num FROM `risks` a INNER JOIN `technology` b ON a.technology = b.value WHERE status != \"Closed\" AND project_version_id = $project GROUP BY b.name ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_technology_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_technology_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ************************************
 * FUNCTION: OPEN RISK OWNER PIE *
 * ************************************
 */
function open_risk_owner_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_owner_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Risk Owners";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT b.name, COUNT(*) AS num FROM `risks` a INNER JOIN `user` b ON a.owner = b.value WHERE status != \"Closed\" AND project_version_id = $project GROUP BY b.name ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_owner_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_owner_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ****************************************
 * FUNCTION: OPEN RISK OWNERS MANAGER PIE *
 * ****************************************
 */
function open_risk_owners_manager_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_owners_manager_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Risk Managers";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT b.name, COUNT(*) AS num FROM `risks` a INNER JOIN `user` b ON a.manager = b.value WHERE status != \"Closed\" AND project_version_id = $project GROUP BY b.name ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput.= "<div id=\"open_risk_owners_manager_pie\"></div>\n";
	$htmloutput.= "<script type=\"text/javascript\">";
	$htmloutput.= $chart->render ( "open_risk_owners_manager_pie" );
	$htmloutput.= "</script>\n";
	return $htmloutput;
}

/**
 * ****************************************
 * FUNCTION: OPEN RISK SCORING METHOD PIE *
 * ****************************************
 */
function open_risk_scoring_method_pie($project) {
	$chart = new Highchart ();
	
	$chart->chart->renderTo = "open_risk_scoring_method_pie";
	$chart->chart->plotBackgroundColor = null;
	$chart->chart->plotBorderWidth = null;
	$chart->chart->plotShadow = false;
	$chart->title->text = "Scoring Method";
	
	$chart->tooltip->formatter = new HighchartJsExpr ( "function() {
        return '<b>'+ this.point.name +'</b>: '+ this.point.y; }" );
	
	$chart->plotOptions->pie->allowPointSelect = 1;
	$chart->plotOptions->pie->cursor = "pointer";
	$chart->plotOptions->pie->dataLabels->enabled = false;
	$chart->plotOptions->pie->showInLegend = 1;
	$chart->credits->enabled = false;
	
	// Open the database connection
	$db = db_open ();
	
	// Query the database
	$stmt = $db->prepare ( "SELECT CASE WHEN scoring_method = 5 THEN 'HackLabs Risk' WHEN scoring_method = 2 THEN 'CVSS' WHEN scoring_method = 1 THEN 'Classic' END AS name, COUNT(*) AS num FROM `risks` a INNER JOIN `risk_scoring` b ON a.id = b.id WHERE status != \"Closed\" AND project_version_id = $project GROUP BY b.scoring_method ORDER BY COUNT(*) DESC" );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		$data [] = array (
				"No Data Available",
				0 
		);
	} 	// Otherwise
	else {
		// Create the data array
		foreach ( $array as $row ) {
			$data [] = array (
					$row ['name'],
					( int ) $row ['num'] 
			);
		}
		
		$chart->series [] = array (
				'type' => "pie",
				'name' => "Status",
				'data' => $data 
		);
	}
	
	$htmloutput = '';
	$htmloutput .= "<div id=\"open_risk_scoring_method_pie\"></div>\n";
	$htmloutput .= "<script type=\"text/javascript\">";
	$htmloutput .= $chart->render ( "open_risk_scoring_method_pie" );
	$htmloutput .= "</script>\n";
	return $htmloutput;
}

/**
 * ***********************************
 * FUNCTION: GET REVIEW NEEDED TABLE *
 * ***********************************
 */
function get_review_needed_table($project) {
	global $lang;
	
	// Get risks marked as consider for projects
	$risks = get_risks ( $project, 3 );
	
	// Start with an empty review status;
	$review_status = "";
	
	$tablecontent = '';
	// For each risk
	foreach ( $risks as $risk ) {
		$risk_id = ( int ) $risk ['id'];
		$subject = htmlentities ( stripslashes ( $risk ['subject'] ), ENT_QUOTES, 'UTF-8' );
		$status = htmlentities ( $risk ['status'], ENT_QUOTES, 'UTF-8' );
		$calculated_risk = htmlentities ( $risk ['calculated_risk'], ENT_QUOTES, 'UTF-8' );
		$color = get_risk_color ( $risk ['calculated_risk'] );
		$dayssince = dayssince ( $risk ['submission_date'] );
		$next_review = next_review ( $color, $risk ['id'], false );
		$next_review_html = next_review ( $color, $risk ['id'] );
		
		// If we have a new review status and its not a date
		if (($review_status != $next_review) && (! preg_match ( '/\d{4}/', $review_status ))) {
			// If its not the first risk
			if ($review_status != "") {
				// End the previous table
				$tablecontent.= "</tbody>\n";
				$tablecontent.= "</table>\n";
				$tablecontent.= "<br />\n";
			}
			
			// Set the new review status
			$review_status = $next_review;
			
			// If the review status is not a date
			if (! preg_match ( '/\d{4}/', $review_status )) {
				// Start the new table
				$tablecontent.= "<table class=\"table table-bordered table-condensed sortable\">\n";
				$tablecontent.= "<thead>\n";
				$tablecontent.= "<tr>\n";
				$tablecontent.= "<th bgcolor=\"#0088CC\" colspan=\"6\"><center><font color=\"#FFFFFF\">" . $review_status . "</font></center></th>\n";
				$tablecontent.= "</tr>\n";
				$tablecontent.= "<tr>\n";
				$tablecontent.= "<th align=\"left\" width=\"50px\">" . $lang ['ID'] . "</th>\n";
				$tablecontent.= "<th align=\"left\" width=\"150px\">" . $lang ['Status'] . "</th>\n";
				$tablecontent.= "<th align=\"left\" width=\"300px\">" . $lang ['Subject'] . "</th>\n";
				$tablecontent.= "<th align=\"center\" width=\"100px\">" . $lang ['Risk'] . "</th>\n";
				$tablecontent.= "<th align=\"center\" width=\"100px\">" . $lang ['DaysOpen'] . "</th>\n";
				$tablecontent.= "<th align=\"center\" width=\"150px\">" . $lang ['NextReviewDate'] . "</th>\n";
				$tablecontent.= "</tr>\n";
				$tablecontent.= "</thead>\n";
				$tablecontent.= "<tbody>\n";
			}
		}
		
		// If the review status is not a date
		if (! preg_match ( '/\d{4}/', $review_status )) {
			$tablecontent.= "<tr>\n";
			$tablecontent.= "<td align=\"left\" width=\"50px\"><a href=\"../management/view.php?id=" . convert_id ( $risk_id ) . "\">" . convert_id ( $risk_id ) . "</a></td>\n";
			$tablecontent.= "<td align=\"left\" width=\"150px\">" . $status . "</td>\n";
			$tablecontent.= "<td align=\"left\" width=\"300px\">" . $subject . "</td>\n";
			$tablecontent.= "<td align=\"center\" bgcolor=\"" . $color . "\" width=\"100px\">" . htmlentities ( $risk ['calculated_risk'], ENT_QUOTES, 'UTF-8' ) . "</td>\n";
			$tablecontent.= "<td align=\"center\" width=\"100px\">" . $dayssince . "</td>\n";
			$tablecontent.= "<td align=\"center\" width=\"150px\">" . $next_review_html . "</td>\n";
			$tablecontent.= "</tr>\n";
		}
	}
	
	return $tablecontent;
}

?>
