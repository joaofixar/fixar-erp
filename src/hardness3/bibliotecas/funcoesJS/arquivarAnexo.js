function arquivarAnexo(divId, acaoId, nr, nomeArquivo) {
	var buttons = {
		"Anexar a um produto": function() {
			abrirJanela(false, divIdRootAberto(), divId, unique(), '', 'Arquivar anexo', '/etc/etc006/content/etc006ContentArquivarAnexo/', '&tipo=produto&T139_Id=' + acaoId + '&file=' + nr + '&nomeArquivo=' + nomeArquivo, [400,150]);
			$(this).dialog("destroy").remove();
		},
		"Anexar a um cliente": function() {
			abrirJanela(false, divIdRootAberto(), divId, unique(), '', 'Arquivar anexo', '/etc/etc006/content/etc006ContentArquivarAnexo/', '&tipo=cliente&T139_Id=' + acaoId + '&file=' + nr + '&nomeArquivo=' + nomeArquivo, [400,150]);
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	};
	dialogConfirm("Atenção", "Deseja arquivar este anexo em algum cadastro?", buttons)
}