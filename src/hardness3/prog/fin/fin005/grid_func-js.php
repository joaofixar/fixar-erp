<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	/**
	* FIN - FIN005 - Boletos Bancários
	* Weiberlan
	* 17/11/2011
	*/
	function fin005CampoCancelar(T117_Id, divId) 
	{
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/fin/fin005/grid_func-ajax/cancelar/?ajax=true&T117_Id=' + encodeURIComponent(T117_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId);
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); }
		}
		dialogConfirm('Deseja Cancelar?', "ATENCAO! Confirma o cancelamento do boleto??", $buttons);
	}
	
	/**
	* FIN - FIN005 - Gerar Boleto
	* Tiago
	* 18/11/2011
	*/
	function fin005FlagGerarBoleto(checkbox, T002_Id, divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected = '1';
		else
			selected = '0';
		$('#' + divId).showLoading();				
		$.getJSON('/fin/fin005/grid_func-ajax/flagGerarBoleto/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});	
	}

	function fin005CampoDesfazerCancelamento(T117_Id, T002_Id, divId) {
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/fin/fin005/grid_func-ajax/desfazerCancelar/?ajax=true&T117_Id=' + encodeURIComponent(T117_Id) + '&T002_Id=' + encodeURIComponent(T002_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId);
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); }
		}
		dialogConfirm('Deseja Cancelar?', "ATENCAO! Deseja restaurar o boleto??", $buttons);
	}
} </script></div>