$(document).ready(function(){
	if($('#company').length > 0){
		$("#company").change(function(e){
			$('#tablecontent').empty();
			$('#project').off('change');
			var company = $('#company').find(":selected").val();
			var data = {'company' : company}; 
			jQuery.getJSON("../includes/ajax/reports/projects_and_risks.php", data, function(response){
				$('#project').find('option').remove().end();
				$('#tablecontent').append(response.message);
			});
		});
	}
});