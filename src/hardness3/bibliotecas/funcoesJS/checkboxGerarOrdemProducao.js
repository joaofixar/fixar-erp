function checkboxGerarOrdemProducao(checkbox,T006_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/est/est017/grid_func-ajax/checkboxGerarOrdemProducao/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}