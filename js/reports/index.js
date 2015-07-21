/*
 * David Zarza Luna - HackLabs
* Start Modyfing 2014/9/5
*/
$(document).ready(function(){
	if($('#company').length > 0){
		$("#company").change(function(e){
			$("#pies").css("visibility","hidden");
			$('#project').off('change');
			var company = $('#company').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/getAjaxProjectsVersionsFromCompany.php", data, function(response){
				$('#project').find('option').remove().end();
				if(response.valid == true){
					$('#project').append('<option value="0">--</option>');	
					$.each(response.data, function(key,value){
						$('#project').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' +value.category+'</option>');
					});
					$('#project').change(function(e){
						$("#pies").css("visibility","hidden");
						$('#content-open_risk_level_pie').empty();
						$('#content-open_risk_status_pie').empty();
						$('#content-open_risk_location_pie').empty();
						$('#content-open_risk_team_pie').empty();
						$('#content-open_risk_technology_pie').empty();
						$('#content-open_risk_owner_pie').empty();
						$('#content-open_risk_owners_manager_pie').empty();
						$('#content-open_risk_scoring_method_pie').empty();
						$('#content-closed_risk_reason_pie').empty();
						
						var project = $('#project').find(":selected").val();
						var data2 = {'company' : company, 'project' : project};
						jQuery.getJSON("../includes/ajax/reports/index.php", data2, function(response){
							$('#content-open_risk_level_pie').append(response.message.open_risk_level_pie);
							$('#content-open_risk_status_pie').append(response.message.open_risk_status_pie);
							$('#content-open_risk_location_pie').append(response.message.open_risk_location_pie);
							$('#content-open_risk_team_pie').append(response.message.open_risk_team_pie);
							$('#content-open_risk_technology_pie').append(response.message.open_risk_technology_pie);
							$('#content-open_risk_owner_pie').append(response.message.open_risk_owner_pie);
							$('#content-open_risk_owners_manager_pie').append(response.message.open_risk_owners_manager_pie);
							$('#content-open_risk_scoring_method_pie').append(response.message.open_risk_scoring_method_pie);
							$('#content-closed_risk_reason_pie').append(response.message.closed_risk_reason_pie);
							$("#pies").css("visibility","visible");
						});
					});
				}
			});
		});
	}
	else {
		jQuery.getJSON("../includes/ajax/getAjaxProjectsVersionsFromCompany.php", null, function(response){
			if(response.valid == true){
				$.each(response.data, function(key,value){
					$('#project').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' +value.category+'</option>');
				});
				$('#project').change(function(e){
					$("#pies").css("visibility","hidden");
					$('#n_openrisks').empty();
					$('#content-open_risk_level_pie').empty();
					$('#content-open_risk_status_pie').empty();
					$('#content-open_risk_location_pie').empty();
					$('#content-open_risk_team_pie').empty();
					$('#content-open_risk_technology_pie').empty();
					$('#content-open_risk_owner_pie').empty();
					$('#content-open_risk_owners_manager_pie').empty();
					$('#content-open_risk_scoring_method_pie').empty();
					$('#content-closed_risk_reason_pie').empty();
					$('#n_closerisks').empty();
					var project = $('#project').find(":selected").val();
					if(project > 0){
						var data2 = {'project' : project};
						jQuery.getJSON("../includes/ajax/reports/index.php", data2, function(response){
							$('#n_openrisks').append(response.message.n_openrisks);
							$('#content-open_risk_level_pie').append(response.message.open_risk_level_pie);
							$('#content-open_risk_status_pie').append(response.message.open_risk_status_pie);
							$('#content-open_risk_location_pie').append(response.message.open_risk_location_pie);
							$('#content-open_risk_team_pie').append(response.message.open_risk_team_pie);
							$('#content-open_risk_technology_pie').append(response.message.open_risk_technology_pie);
							$('#content-open_risk_owner_pie').append(response.message.open_risk_owner_pie);
							$('#content-open_risk_owners_manager_pie').append(response.message.open_risk_owners_manager_pie);
							$('#content-open_risk_scoring_method_pie').append(response.message.open_risk_scoring_method_pie);
							$('#content-closed_risk_reason_pie').append(response.message.closed_risk_reason_pie);
							$('#n_closerisks').append(response.message.n_closed_risks);
							$("#pies").css("visibility","visible");
						});
					}
				});
			}
		});
		
	}
});