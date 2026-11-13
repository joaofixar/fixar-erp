function checkboxExcluirItemPedido(checkbox,T006_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='1';
    } else {
        selected='2';          
    }
    $.getJSON('/ven/ven002/grid_func-ajax/checkboxExcluirItemPedido/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}