function campoMarcarDesmembramento(checkbox, T015_Id, divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/fin/fin003/grid_func-ajax/campoMarcarDesmembramento/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&selected=' + selected + '&callback=?', function(request) {
        if(request.code){
            divRefresh($('#'+divId).parent().attr('id'));
            $('#' + divId).hideLoading();
        }
    });    
}