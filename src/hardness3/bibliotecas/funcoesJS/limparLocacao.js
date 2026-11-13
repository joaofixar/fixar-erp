function limparLocacao(divId, T066_Id){
	var buttons = {
		"Sim": function() {
			$.getJSON('/cad/cad002/grid_func-ajax/limparLocacao/?ajax=true&T066_Id='+ encodeURIComponent(T066_Id) + '&callback=?', function(request) {
				divRefresh(divId);
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Confirmar?', "Atenção! Você deseja limpar esta locação?", buttons);
}