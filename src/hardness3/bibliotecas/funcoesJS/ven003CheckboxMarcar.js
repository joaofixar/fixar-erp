function ven003CheckboxMarcar(checkbox,T007_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/ven/ven003/grid_func-ajax/ven003CheckboxMarcarNota/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}