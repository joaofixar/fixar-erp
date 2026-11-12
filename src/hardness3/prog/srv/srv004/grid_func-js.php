<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function srv004MarcarInspecionado(T138_Id,D001_Descricao_Produto,T138_Quantidade, divId){
			var $buttons = {
				"Sim": function() {
					$('#' + divId).showLoading();
					$.getJSON('/srv/srv004/grid_func-ajax/marcarInspecionado/?ajax=true&T138_Id=' + encodeURIComponent(T138_Id) + '&callback=?', function(request) {
						if (request.code) {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    //dialogConfirm('Ok', 'Produto inspecionado', $buttons);
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
			dialogConfirm('Atenção!!', 'Confirma a inspeção de '+T138_Quantidade+' unidade(s) do produto '+D001_Descricao_Produto+' ?', $buttons);
		}		
} </script>
</div>
