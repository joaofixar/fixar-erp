<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function ven001CampoExclusao(T003_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/ven/ven001/grid_func-ajax/excluir/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="d110 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						dialogConfirm('Exlcusão', 'Orçamento ' + T003_Id + ' excluído com sucesso.');
					} else {
						dialogConfirm('ERRO', request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do registro?", $buttons);
}

function ven001CampoCancelamento(T004_Id, divId) 
    {
    	var $buttons = {
			"Sim": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/ven/ven001/grid_func-ajax/ven001CancelarProduto/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="d110 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Registro excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Cancelar?', "ATENCAO! Confirma o cancelamento do registro??", $buttons);
}

function ven001ExcluirServico(T145_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/ven/ven001/grid_func-ajax/excluirServico/?ajax=true&T145_Id=' + encodeURIComponent(T145_Id) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    //dialogConfirm('Ok', 'Serviço conferido', $buttons);
				    divRefresh(divId, true);
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

function ven001ExcluirProduto(T004_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/ven/ven001/grid_func-ajax/excluirProduto/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&callback=?', function(request) {
				if (request.code) {
				    $('#' + divId + ' #T004_Id-' + T004_Id).remove(); // remove linha
				    divRefresh($('#' + divId).closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
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
	function ven001CheckboxDividir(checkbox,id,divId,TXXX) {
	    var selected;
	    if($(checkbox).is(':checked'))
	        selected='S';
	    else
	        selected='N';
	    $('#' + divId).showLoading();                
	    $.getJSON('/ven/ven001/grid_func-ajax/ven001CheckboxDividir/?ajax=true&id=' + encodeURIComponent(id) + '&selected=' + selected + '&TXXX=' + encodeURIComponent(TXXX) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
	    });    
	}

	function ven001CheckboxCotar(checkbox, T004_Id, divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		$('#' + divId).showLoading();                
		$.getJSON('/ven/ven001/grid_func-ajax/ven001CheckboxCotar/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});
	}

	function ven001CheckboxPrazoCompra(checkbox, T004_Id, divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		$('#' + divId).showLoading();                
		$.getJSON('/ven/ven001/grid_func-ajax/ven001CheckboxPrazoCompra/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});
	}

} </script></div>