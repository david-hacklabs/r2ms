$(document).ready(function(){
	if($('#addversion-company-select').length > 0){
		$("#addversion-company-select").change(function(e){
			var company = $('#addversion-company-select').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/getAjaxProjectsFromCompany.php", data, function(response){
				$('#addversion-project-select').find('option').remove().end();
				if(response.valid == true){
					$.each(response.data, function(key,value){
						$('#addversion-project-select').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - '+value.category+'</option>');
					});
				}
			});
		});
	}
	if($('#remove-company-select').length > 0){
		$("#remove-company-select").change(function(e){
			var company = $('#remove-company-select').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/getAjaxProjectsFromCompany.php", data, function(response){
				$('#remove-project-select').find('option').remove().end();
				if(response.valid == true){
					$('#remove-project-select').append('<option value="">--</option>');
					$.each(response.data, function(key,value){
						$('#remove-project-select').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - '+value.category+'</option>');
					});
					$('#remove-project-select').change(function(e){
						var project = $('#remove-project-select').find(":selected").val();
						var data2 = {'company' : company, 'project' : project};
						jQuery.getJSON("../includes/ajax/getAjaxVersionsFromProject.php", data2, function(response){
							$('#remove-project-version-select').find('option').remove().end();
							if(response.valid == true){
								$('#remove-project-version-select').append('<option value="">--</option>');
								$.each(response.data, function(key,value){
									$('#remove-project-version-select').append('<option value="'+value.value+'">'+value.name+'</option>');
								});
							}
						});
					});
				}
			});
		});
	}
	
	if($('#company-select').length > 0){
		$("#company-select").change(function(e){
			var company = $('#company-select').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/getAjaxProjectsAndClientsFromCompany.php", data, function(response){
				$('#project-select').find('option').remove().end();
				if(response.validProject == true){
					$.each(response.dataProject, function(key,value){
						$('#project-select').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - '+value.category+'</option>');
					});
				}
				$('#client-company').find('option').remove().end();
				if(response.validClient == true){
					$.each(response.dataClient, function(key,value){
						$('#client-company').append('<option value="'+value.value+'">'+value.name+' - '+value.email+'</option>');
					});
				}
			});
		});
		
	}
	
	if($('#company-list-select').length > 0){
		$("#company-list-select").change(function(e){
			var company = $('#company-list-select').find(":selected").val();
			var data = {'company' : company}; 
			$('#project-list-select').off('change');
			jQuery.getJSON("../includes/ajax/getAjaxProjectsFromCompany.php", data, function(response){
				$('#project-list-select').find('option').remove().end();
				if(response.valid == true){
					$('#project-list-select').append('<option value="0">--</option>');
					$.each(response.data, function(key,value){
						$('#project-list-select').append('<option value="'+value.value+'">'+value.creation+' - '+value.name+' - '+value.category+'</option>');
						$('#project-list-select').change(function(e){
							var company = $('#company-list-select').find(":selected").val();
							var project = $('#project-list-select').find(":selected").val();
								var data2 = {'company' : company, 'project' : project};
								jQuery.getJSON("../includes/ajax/getAjaxClientsFromProject-Client.php", data2, function(response){
									$('#clientwithprojects').removeClass('greenalert');
									$('#clientwithprojects').removeClass('redalert');
									if(response.valid == true){
										$('#clientwithprojects').empty();
										$.each(response.data, function(key,value){
											$('#clientwithprojects').addClass('greenalert')
											$('#clientwithprojects').append(value.nameemail+"</br>");
										});
									}
									else {
										$('#clientwithprojects').empty();
										$('#clientwithprojects').addClass('redalert')
										$('#clientwithprojects').append("There are not clients associated to this project.</br>");
									}
								});
						});
					});
				}
			});
		});
	}
});

function addProjectToClient(){
	var company = $('#company-select').find(":selected").val();
	var project = $('#project-select').find(":selected").val();
	var client = $('#client-company').find(":selected").val();
	$("#container-project-client").css("display","none");
	$('#info-project-client').removeClass('greenalert');
	$('#info-project-client').removeClass('redalert');
	$('#info-project-client').empty();
	if (Math.floor(project) == project && $.isNumeric(project) > 0 && Math.floor(client) == client && $.isNumeric(client) > 0){
		var data = {'company' : company, 'project' : project, 'client' : client};
		jQuery.getJSON("../includes/ajax/admin/addProjectToClient.php", data, function(response){
			$("#container-project-client").css("display","block");
			if (response.success == true){
				$('#info-project-client').addClass('greenalert').append(response.message);
			}
			else{
				$('#info-project-client').addClass('redalert').append(response.message);
			}
		});
	}
};

function deleteProjectToClient(){
	var company = $('#company-select').find(":selected").val();
	var project = $('#project-select').find(":selected").val();
	var client = $('#client-company').find(":selected").val();
	$("#container-project-client").css("display","none");
	$('#info-project-client').removeClass('greenalert');
	$('#info-project-client').removeClass('redalert');
	$('#info-project-client').empty();
	if (Math.floor(project) == project && $.isNumeric(project) > 0 && Math.floor(client) == client && $.isNumeric(client) > 0){
		var data = {'company' : company, 'project' : project, 'client' : client};
		jQuery.getJSON("../includes/ajax/admin/removeProjectToClient.php", data, function(response){
			$("#container-project-client").css("display","block");
			if (response.success==true){
				$('#info-project-client').addClass('greenalert').append(response.message);
			}
			else{
				$('#info-project-client').addClass('redalert').append(response.message);
			}
		});
	}
};