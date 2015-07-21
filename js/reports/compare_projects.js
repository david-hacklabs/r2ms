/*
 * David Zarza Luna - HackLabs
* Start Modyfing 2014/9/5
*/
$(document).ready(function(){
	if($('#company').length > 0){
		$("#company").change(function(e){
			$("#pies").css("visibility","hidden");
			$('#project').off('change');
			$('#project').off('change');
			var company = $('#company').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/getAjaxProjectsVersionsFromCompany.php", data, function(response){
				$('#project').find('option').remove().end();
				$('#project2').find('option').remove().end();
				if(response.valid == true){
					$('#project').append('<option value="0">--</option>');
					$('#project2').append('<option value="0">--</option>');
					$.each(response.data, function(key,value){
						$('#project').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' +value.category+'</option>');
						$('#project2').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' +value.category+'</option>');
					});
				}
			});
			});
		}
	else {
		jQuery.getJSON("../includes/ajax/getAjaxProjectsVersionsFromCompany.php", null, function(response){
			if(response.valid == true){
				$('#project').find('option').remove().end();
				$('#project2').find('option').remove().end();
				$('#project').append('<option value="0">--</option>');
				$('#project2').append('<option value="0">--</option>');
				$.each(response.data, function(key,value){
					$('#project').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' +value.category+'</option>');
					$('#project2').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - v.'+ value.version +' - ' +value.category+'</option>');
				});
			}
		});
	}
	if ($("#bcompare").length > 0){
		$("#bcompare").click(function(){
			$('#alert').empty();
			$('#compare_open_risk_bar').empty();
			var errormsg = "<div class='container-fluid'><div class='row-fluid'><div class='span12 redalert'>Two different projects must be selected to be compared</div></div></div>";
			var project = $('#project').find(":selected").val();
			var project2 = $('#project2').find(":selected").val();
			if($('#company').length > 0){
				var company = $('#company').find(":selected").val();
			}
			else var company = 1;
			if(company != 0 || project != 0 || project2 != 0) {
				if (project != project2){
						var data = {'company':company,'project':project,'project2':project2}
						jQuery.getJSON("../includes/ajax/reports/compare_projects.php", data, function(response){
							$('#compare_open_risk_bar').append(response.message.compare_open_risk_bar);
							$("#bars").css("visibility","visible");
						});
					}else {
						$('#alert').append(errormsg);
					}
			}
			else {
				$('#alert').append(errormsg);
			}
		});
	}
});