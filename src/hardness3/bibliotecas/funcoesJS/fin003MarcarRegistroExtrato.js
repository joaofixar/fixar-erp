function fin003MarcarRegistroExtrato(checkbox,T216_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/fin/fin003/grid_func-ajax/marcarRegistroExtrato/?ajax=true&T216_Id=' + encodeURIComponent(T216_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}