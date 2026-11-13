<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
function est016CampoExclusaoServico(T230_Id,divId) {
	var $buttons = {
		"Remover": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/est/est016/grid_func-ajax/excluirServico/?ajax=true&T230_Id=' + encodeURIComponent(T230_Id) + '&callback=?', function(request) {
  				$('#' + divId).hideLoading();
				if (request.code) {
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
					reloadUI(divId);							// atualiza elementos (tabulação do grid)
				} else {
					alert(request.data);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Confirmação', "Confirma a exclusão?", $buttons);
	$(this).dialog("destroy").remove();
}
}

function est016ExcluirServico(T230_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est016/grid_func-ajax/excluirServico/?ajax=true&T230_Id=' + encodeURIComponent(T230_Id) + '&callback=?', function(request) {
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

function excluirProdutoOC(T225_Id, divId){
	$.getJSON('/est/est016/grid_func-ajax/verificaExcluirProduto/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&callback=?', function(request) {
		if (request.code) {
			var $buttons = {
				"Sim": function() {
					//$('#' + divId).showLoading();
					$.getJSON('/est/est016/grid_func-ajax/excluirProdutoOC/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&callback=?', function(request) {
						if (request.code) {
						    $('#' + divId + ' #T225_Id-' + T225_Id).remove(); // remove linha
						    divRefresh($('#' + divId).closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
						} else {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Erro', request.data, $buttons);
						}
						//$('#' + divId).hideLoading();
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Deseja Remover?', 'Você deseja realmente excluir este item?', $buttons);
		} else {
			var $buttons = {
				"Sim": function() {
					//$('#' + divId).showLoading();
					$.getJSON('/est/est016/grid_func-ajax/excluirProdutoOC/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&callback=?', function(request) {
						if (request.code) {
						    $('#' + divId + ' #T225_Id-' + T225_Id).remove(); // remove linha
						    divRefresh($('#' + divId).closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
						} else {
							var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Erro', request.data, $buttons);
						}
						//$('#' + divId).hideLoading();
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Atenção!', 'Você deseja realmente excluir este item?<br>' + request.data, $buttons);
		}
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

function est016CheckboxCotar(checkbox, T225_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/est/est016/grid_func-ajax/est016CheckboxCotar/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});    
}

function est016CheckboxComprar(checkbox, T225_Id, T189_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/est/est016/grid_func-ajax/est016CheckboxComprar/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		if(!request.code){
			dialogConfirm('Erro', request.data);
			$(checkbox).attr('checked', false);
		}
		$('#' + divId).hideLoading();
	});    
}
function est016CheckboxModificar(checkbox, T225_Id, T189_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/est/est016/grid_func-ajax/est016CheckboxModificar/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		if(!request.code){
			dialogConfirm('Erro', request.data);
			$(checkbox).attr('checked', false);
		}
		$('#' + divId).hideLoading();
	});    
}
function est016AprovarPedido(T224_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est016/grid_func-ajax/est016AprovarPedido/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&callback=?', function(request) {

				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    dialogConfirm('Análise de Ordem de Compra', request.data, $buttons);
			    divRefresh(divId);

				$('#' + divId).hideLoading();					
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção!!', 'Deseja aprovar a Ordem de Compra '+T224_Id+' ?', $buttons);
}
</script>
</div>
