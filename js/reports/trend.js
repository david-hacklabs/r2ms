/*
 * David Zarza Luna - HackLabs
* Start Modyfing 2014/9/5
*/
$(document).ready(function(){
	if($('#company').length > 0){
		$("#company").change(function(e){
			$("#chart").css("visibility","hidden");
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
						$("#chart").css("visibility","hidden");
						$('#trend').empty();
						var project = $('#project').find(":selected").val();
						var data2 = {'company' : company, 'project' : project};
						jQuery.getJSON("../includes/ajax/reports/trend.php", data2, function(response){
							$('#trend').append(response.message.risk_trend);
							$("#chart").css("visibility","visible");
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
					$("#chart").css("visibility","hidden");
					$('#trend').empty();
					var project = $('#project').find(":selected").val();
					if(project > 0){
						var data2 = {'project' : project};
						jQuery.getJSON("../includes/ajax/reports/trend.php", data2, function(response){
							$('#trend').append(response.message.risk_trend);
							$("#chart").css("visibility","visible");
						});
					}
				});
			}
		});
		
	}
});