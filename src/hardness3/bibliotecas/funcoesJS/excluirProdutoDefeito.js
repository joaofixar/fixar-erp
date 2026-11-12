function excluirProdutoDefeito(T206_Id, divId) {
	$.getJSON('/est/est003/grid_func-ajax/est003excluirDefeito/?ajax=true&T206_Id=' + encodeURIComponent(T206_Id) + '&callback=?', function(request) {
    	if(request.code){
    		divRefresh(divId);
    	}
    });    
}