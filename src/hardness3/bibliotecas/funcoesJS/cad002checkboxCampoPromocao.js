function cad002checkboxCampoPromocao(checkbox,D009_Id,divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/cad/cad002/grid_func-ajax/CAD002checkboxCampoPromocao/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });    
}