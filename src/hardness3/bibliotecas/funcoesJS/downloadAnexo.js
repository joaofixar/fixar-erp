function downloadAnexo(acaoId, nr) {
	var buttons = {
		"Sim": function() {
			$('#iframeDownloadAnexo').remove();
			$('body').append('<iframe id="iframeDownloadAnexo" style="display: none;"/>');
			$('#iframeDownloadAnexo').attr({target: '_blank', src: '/etc/etc006/form_func-ajax/downloadAnexo/?ajax=true&acaoId=' + acaoId + '&file=' + nr});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); }
	};
	dialogConfirm("Atenção", "Deseja fazer o download deste anexo?", buttons)
}