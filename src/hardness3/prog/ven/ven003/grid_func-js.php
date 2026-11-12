<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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

	function ven003CheckboxMarcar(checkbox,T007_Id,divId) {
	    var selected;
	    if($(checkbox).is(':checked'))
	        selected='S';
	    else
	        selected='N';
	    $('#' + divId).showLoading();                
	    $.getJSON('/ven/ven003/grid_func-ajax/ven003CheckboxMarcarNota/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&selected=' + selected  + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
	    });    
	}

	function ven003ExcluirProduto(T008_Id, T008_T007_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven003/grid_func-ajax/excluirProduto/?ajax=true&T008_Id=' + encodeURIComponent(T008_Id) + '&T008_T007_Id=' + encodeURIComponent(T008_T007_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    //dialogConfirm('Ok', 'Item Removido', $buttons);
					    divRefresh(divId, true);
					    var divOutro = $('#' + divId).parent().closest("div").attr("id");
					    divRefresh(divOutro, true);
					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Erro', request.data, $buttons);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Deseja Remover?', 'Você deseja realmente excluir este item?', $buttons);
	}

	function ven003excluirNFe(T007_Id,divId){
		var buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven003/grid_func-ajax/excluirNFe/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
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
		dialogConfirm('Excluir Nota', 'Confirma a exclusão da NF-e?', buttons);
	}

	function ven003cancelarNotaFiscal(T007_Id, divId, divIdRoot){
		$('#' + divId).showLoading();
		$.getJSON('/ven/ven003/grid_func-ajax/cancelarNotaFiscal/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
			if (request.code) {
				abrirJanela(false, divIdRoot, divId, unique(), '', 'Cancelar Nota Fiscal', '/ven/ven003/content/ven003ContentCancelarNF/', '&T007_Id=' + T007_Id, [500,300]);
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, buttons);
			}
			$('#' + divId).hideLoading();
		});
	}

	function ven003DenegarNFe(T007_Id, divId, divIdRoot){
		$('#' + divId).showLoading();
		$.getJSON('/ven/ven003/grid_func-ajax/denegarNFe/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
			if (request.code) {
	    		dialogConfirm('Sucesso!', 'Nota fiscal denegada');
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, buttons);
			}
			$('#' + divId).hideLoading();
		});
	}

	function ven003EstornarCancelarNotaFiscal(T007_Id, divId, divIdRoot){
		$('#' + divId).showLoading();
		$.getJSON('/ven/ven003/grid_func-ajax/estornarCancelarNotaFiscal/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
			if (request.code) {
	    		dialogConfirm('Sucesso', 'Nota cancelada estornada!');
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, buttons);
			}
			$('#' + divId).hideLoading();
		});
	}

	function insereRegistroT001(T008_Id,divId){
		var buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven003/grid_func-ajax/insereRegistroT001/?ajax=true&T008_Id=' + encodeURIComponent(T008_Id) + '&callback=?', function(request) {
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
		dialogConfirm('Inserir T001', 'Confirma a inclusão do registro no T001?', buttons);
	}

	
} </script></div>