<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad019CheckboxImprimir(checkbox,D004_Id,divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/cad/cad019/grid_func-ajax/cad019CheckboxImprimir/?ajax=true&D004_Id=' + encodeURIComponent(D004_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });    
}

} </script></div>