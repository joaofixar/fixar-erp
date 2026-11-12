<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function est020excluirCTe(T084_Id,divId){
		var buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est020/grid_func-ajax/excluirCTe/?ajax=true&T084_Id=' + encodeURIComponent(T084_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId);
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Ok', request.data, buttons);    		
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Erro', request.data, buttons);
					}
					$('#' + divId).hideLoading();
					reloadUI(divId);
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Excluir CTe', 'Confirma a exclusão da CT-e?', buttons);
	}		
	function est020CheckboxEnviarCTe(checkbox, T084_Id, divId) {
		var selected='N';
		if($(checkbox).is(':checked')) {
			selected='S';		
		}

		$('#' + divId).showLoading();                
		$.getJSON('/est/est020/grid_func-ajax/est020CheckboxEnviarCTe/?ajax=true&T084_Id=' + encodeURIComponent(T084_Id) + '&selected=' + selected + '&callback=?', function(request) {
			if(!request.code){
				dialogConfirm('Erro', request.data);
				$(checkbox).attr('checked', false);
			}
			$('#' + divId).hideLoading();
		});    
	}
	function est020imprimirDACTE(T084_Id, divId) {			
		$('#' + divId).showLoading();
		$.ajax({
			url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
			type: 'GET',
			dataType: 'jsonp',
			data: {
				'ajax': 'true',
				'acaoId': '9da6468cc29f3b4f47d95d743fc68e8e',
				'link': '/sistema/personalizacao/tela/991025d354f9429b1f56e30b19ea5619/?ajax=true&acaoId=' + encodeURIComponent(T084_Id),
			},
			success: function(resposta) {
				$('#' + divId).hideLoading();
				window.open(resposta.data, '_blank');
			}
		});
	}
} </script></div>