function fin005MarcarBoletoAtualizar(checkbox,T117_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/fin/fin005/grid_func-ajax/marcarBoletoAtualizar/?ajax=true&T117_Id=' + encodeURIComponent(T117_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}