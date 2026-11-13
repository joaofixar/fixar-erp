function campoAutorizarBaixaRetorno(checkbox, T038_Id, divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin001/grid_func-ajax/campoAutorizarBaixaRetorno/?ajax=true&T038_Id=' + encodeURIComponent(T038_Id) + '&selected=' + selected + '&callback=?', function(request) {
        if(request.code){
            $('#' + divId).hideLoading();
        }
    });
}