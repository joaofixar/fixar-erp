function abrirDialogImpressaoNFCe(T007_Id, Query, valorNaDanfe, Contingencia = 'N'){
	var buttons = {
		"Impressão": function() {
			window.open('/hardness3/hardness/danfe/imprimir_danfe.php?Query=' + encodeURIComponent(Query) + '&vlrNF=' + encodeURIComponent(valorNaDanfe) + '&T007_Id=' + encodeURIComponent(T007_Id) + '&impressaoHtml=true','DANFE' + encodeURIComponent(Query),'toolbar=yes,menubar=yes,location=no,scrollbars=no,toolbar=no,status=yes,resizable=yes,width=750,height=642,top=0,left=0');
			$(this).dialog("destroy").remove();
		},
		"PDF": function() {
			window.open('/hardness3/hardness/danfe/imprimir_danfe.php?Query=' + encodeURIComponent(Query) + '&vlrNF=' + encodeURIComponent(valorNaDanfe) + '&T007_Id=' + encodeURIComponent(T007_Id) + '&impressaoHtml=false','DANFE' + encodeURIComponent(Query),'toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { 
			$(this).dialog("destroy").remove();
		}
	}

	const titulo = Contingencia == 'N' ? 'DANFE NFC-e ' : 'DANFE NFC-e Contingência';

	var dialog = dialogConfirm(titulo, 'Selecione a maneira que deseja abrir o danfe', buttons);
};