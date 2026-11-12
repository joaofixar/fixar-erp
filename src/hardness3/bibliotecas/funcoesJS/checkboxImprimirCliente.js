function checkboxImprimirCliente(checkbox,D024_Id,divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/cad/cad001/grid_func-ajax/cad001CheckboxImprimir/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });    
}