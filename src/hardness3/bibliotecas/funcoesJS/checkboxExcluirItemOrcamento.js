function checkboxExcluirItemOrcamento(checkbox,T004_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='1';
    } else {
        selected='2';          
    }
    $.getJSON('/ven/ven001/grid_func-ajax/checkboxExcluirItemOrcamento/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}