function checkboxCotarEmergencia(checkbox,T004_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/est/est009/grid_func-ajax/checkboxCotarEmergencia/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}