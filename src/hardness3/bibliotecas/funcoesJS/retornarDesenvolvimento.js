function retornarDesenvolvimento(T234_Id, flagStatus, status, divId) {
    var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
		    $.getJSON('/outros/registroSuporte/registroSuporte/alterarStatusDesenvolvimento/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&flagStatus=' + encodeURIComponent(flagStatus) + '&tipo=' + encodeURIComponent('R') + '&callback=?', function(request) {
		    	$('#' + divId).hideLoading();
		    	if(request.code){
		    		divRefresh(divId);
		    	}
		    });
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção', 'Deseja retornar o chamado para ' + status, buttons);
}