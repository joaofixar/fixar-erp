<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function carregaDadosNfeCte(elem, divId) {
			if ($('#T085_T007_Id').val()) {
			    $('#' + divId).showLoading();
			    $.getJSON('/est/est020/form_func-ajax/carregaDadosNfeCte/?ajax=true&C004_Origem=' + encodeURIComponent($('#Origem').val())  + '&T085_T007_Id=' + encodeURIComponent($('#T085_T007_Id').val())+ '&callback=?', 
					function(request) {
						$('#' + divId).hideLoading();
						if (request.code) {									
							if(!request.data['T085_Chave_Acesso_Nfe']){									
								let $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    				dialogConfirm("Erro!", "Essa nota já está vinculada a algum CT-e", $buttons)
							} else {											 
								$('#' + divId + ' #T085_Chave_Acesso_Nfe').val(request.data['T085_Chave_Acesso_Nfe']).prop('readonly', true).css('background-color', '#ede7de');															
							}
						} else {
							let $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data['erro'], $buttons)
						}
					});
			}
		}

		function alteraEstado(elem, divId, campo, campoAtualiza) {			
		    $('#' + divId).showLoading();					
		    $.getJSON('/est/est020/form_func-ajax/alteraEstado/?ajax=true&D018_Id=' + encodeURIComponent($(`#${campo}`).val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {							
					$(`#${campoAtualiza}`).html(request.data['D020_Id']);										
					reloadUI('#' . divId);
				} else {
					let $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
		    		dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}
	} </script>
</div>
