function globalCheckbox(checkbox, tabelaPk, campo, valoresValidos, divId, campoC007, refresh) {
    var selected, limpar;
    var id = $(checkbox).closest('tr').attr('id');
    var pk = $(checkbox).closest('td').data('pk');
    if (pk) {
        id = id ? id + "|" + pk : pk;
    }
   	valoresValidos = valoresValidos.split('|');
    if($(checkbox).is(':checked')) {
        selected = valoresValidos[1];
        limpar = 'N';
    } else {
        selected = valoresValidos[0];
        limpar = 'S';
    }

    $.getJSON('/sistema/funcoes/gCheckbox/gCheckbox/?ajax=true&chavesPrimarias=' + encodeURIComponent(id) +  '&tabelaPk=' + encodeURIComponent(tabelaPk) + '&campo=' + encodeURIComponent(campo) + '&campoC007=' + encodeURIComponent(campoC007) + '&limparC007=' + limpar + '&selected=' + selected + '&callback=?', function(request) {
        if(request.code){
            if (refresh === 'full') {
                divRefresh(divId);
            } else if (refresh !== 'no') {
                divRefresh(divId, true, false, false, id, false);
            }
        }
    });
}