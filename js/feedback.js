jQuery(document).ready(function($) {

	$('#feedback').on('submit', function(e){

		e.preventDefault();
		$('#resultMessage').removeClass("alert-success");
		$('#resultMessage').removeClass("alert-warning");

		$.ajax({
			url: window.location.href,
			type: 'POST',
			dataType: 'json',
			data: $(this).serialize(),
		})
		.done(function(result) {
			if (result.status)
			{
				$('#resultMessage').addClass("alert-success");
			}
			else
			{
				$('#resultMessage').addClass("alert-warning");
			}
			
			$('#resultMessage').text(result.message);
		})
		.fail(function() {
			$('#resultMessage').addClass("alert-warning");
			$('#resultMessage').text("Falha no envio de feedback");
		})
		.always(function() {
			console.log("complete");
		});
		

	})
	
});