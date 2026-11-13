function checkboxD069FlagTabelaPrincipal(checkbox,D069_Id,divId, atualizarGrid) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/cad/cad002/grid_func-ajax/cad002CheckboxD069FlagTabelaPrincipal/?ajax=true&D069_Id=' + encodeURIComponent(D069_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        console.log(atualizarGrid);
        if(atualizarGrid == '1'){
        	divRefresh(divId);
        }
    });    
}