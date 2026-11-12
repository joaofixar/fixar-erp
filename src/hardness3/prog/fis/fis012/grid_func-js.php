<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function fis012CancelarMfde(T276_Id, divId, divIdRoot){
		$('#' + divId).showLoading();
		$.getJSON('/fis/fis012/grid_func-ajax/cancelarMdfe/?ajax=true&T276_Id=' + encodeURIComponent(T276_Id) + '&callback=?', function(request) {
			if (request.code) {
				abrirJanela(false, divIdRoot, divId, unique(), '', 'Cancelar MDF-e', '/fis/fis012/content/fis012ContentCancelarMdfe/', '&T276_Id=' + T276_Id, [500,300]);
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, buttons);
			}
			$('#' + divId).hideLoading();
		});
	}

	function abre_doc(parametro,nome_janela) {
		var param='';
		param+='menubar=yes,';
		param+='toolbar=no,';
		param+='location=no,';
		param+='top=0,';
		param+='left=0,';
		param+='status=no,';
		param+='scrollbars=yes,';
		param+='resizable=yes,';
		var Total_Largura=950;
		var Total_Altura=600;
		param+='width='+Total_Largura+',';
		param+='height='+Total_Altura+'';
		new_window=window.open(parametro,nome_janela,param);
		new_window.focus();
	}

	function excluirMDFe(T276_Id,divId){
		var buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/fis/fis012/grid_func-ajax/excluirMDFe/?ajax=true&T276_Id=' + encodeURIComponent(T276_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId);
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Ok', request.data, buttons);    		
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Erro', request.data, buttons);
					}
					$('#' + divId).hideLoading();
					reloadUI(divId);
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Excluir MDF-e', 'Confirma a exclusão do MDF-e? ', buttons);
	}
} </script></div>