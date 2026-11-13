function checkboxProtestarCliente(checkbox,D024_Id, divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/fin/fin007/grid_func-ajax/fin007CheckboxProtestar/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&selected=' + selected + '&callback=?', function(request) {
    	if(request.code){
    		$('#' + divId).hideLoading();
    	}
    });    
}