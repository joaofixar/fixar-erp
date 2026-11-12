function pesquisaFormulario(divId, campo_id, divIdAberto, divIdRoot){
	divSetarAcaoId(divIdAberto, campo_id);
	divRefresh(divIdAberto, true);	
	$('#' + divIdAberto).closest('.content').parent().parent().find('.ui-icon-triangle-1-w').data('id', campo_id);
	$('#' + divId).closest('.ui-dialog-content').dialog('close');
}