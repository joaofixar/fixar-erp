function campoMarcarDesmembramentoRecebida(checkbox, T002_Id, divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/fin/fin003/grid_func-ajax/campoMarcarDesmembramentoRecebida/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {
        if(request.code){
            divRefresh($('#'+divId).parent().attr('id'));
            $('#' + divId).hideLoading();
        }
    });    
}