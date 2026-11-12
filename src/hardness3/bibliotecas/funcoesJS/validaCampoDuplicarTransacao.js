function validaCampoDuplicarTransacao(T216_Id, divId) {
	

    var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			divId = $('#' + divId).parent().parent().attr('id');
		    $.getJSON('/fin/fin003/grid_func-ajax/duplicarTransacao/?ajax=true&T216_Id=' + encodeURIComponent(T216_Id) + '&callback=?', function(request) {
		    	$('#' + divId).hideLoading();
		    	if(request.code){
		    		divRefresh(divId);
		    	}
		    });
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção', 'Deseja duplicar transação?', buttons);
}