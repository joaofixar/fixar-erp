<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function srv002MarcarConferido(T147_Id, divId){
			var $buttons = {
				"Sim": function() {
					$('#' + divId).showLoading();
					$.getJSON('/srv/srv002/grid_func-ajax/marcarConferido/?ajax=true&T147_Id=' + encodeURIComponent(T147_Id) + '&callback=?', function(request) {
						if (request.code) {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    //dialogConfirm('Ok', 'Serviço conferido', $buttons);
						    divRefresh(divId, true);
						} else {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Erro', request.data, $buttons);
						}
						$('#' + divId).hideLoading();
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Atenção!!', 'Confirma a conferência do serviço '+T147_Id+' ?', $buttons);
		}	
		function srv002MarcarReprovado(T147_Id, divId){
			var $buttons = {
				"Sim": function() {
					$('#' + divId).showLoading();
					$.getJSON('/srv/srv002/grid_func-ajax/marcarReprovado/?ajax=true&T147_Id=' + encodeURIComponent(T147_Id) + '&callback=?', function(request) {
						if (request.code) {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    //dialogConfirm('Ok', 'Serviço conferido', $buttons);
						    divRefresh(divId, true);
						} else {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Erro', request.data, $buttons);
						}
						$('#' + divId).hideLoading();
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Atenção!!', 'Você tem certeza que deseja reprovar o serviço '+T147_Id+' ?', $buttons);
		}
} </script>
</div>
