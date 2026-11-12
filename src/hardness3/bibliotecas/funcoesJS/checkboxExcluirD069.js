function checkboxExcluirD069(checkbox,D069_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='1';
    } else {
        selected='2';          
    }
    $.getJSON('/cad/cad002/grid_func-ajax/checkboxExcluirD069/?ajax=true&D069_Id=' + encodeURIComponent(D069_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}