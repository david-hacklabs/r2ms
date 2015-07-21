$(document).ready(function(){
	if($('#company').length > 0){
		$("#company").change(function(e){
			var company = $('#company').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/getAjaxProjectsVersionsFromCompany.php", data, function(response){
				$('#project').find('option').remove().end();
				if(response.valid == true){
					$.each(response.data, function(key,value){
						$('#project').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' + value.category+'</option>');
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
			}
		});
	}
	if($('#risk_template').length > 0){
		$("#risk_template").change(function(e){
			$('#subject').val('');
			$('#description').val('');
			$('#impact_risk_template').val('');
			$('#detail').val('');
			$('#recommendation').val('');
			var risk_template = $('#risk_template').find(":selected").val();
			var data = {'risk_template' : risk_template}; 
			jQuery.getJSON("../includes/ajax/getAjaxRiskTemplate.php", data, function(response){
				if(response.valid == true){
					$.each(response.data, function(key,value){
						$('#subject').val(value.name);
						$('#description').val(value.description);
						$('#impact_risk_template').val(value.impact);
						$('#detail').val(value.detail);
						$('#recommendation').val(value.recommendation);
					});
				}
			});
		});
	}
});