<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
function ven002CampoExclusaoServico(T147_Id,divId) {
	var $buttons = {
		"Remover": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/ven/ven002/grid_func-ajax/excluirServico/?ajax=true&T147_Id=' + encodeURIComponent(T147_Id) + '&callback=?', function(request) {
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

function ven002ExcluirServico(T147_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/ven/ven002/grid_func-ajax/excluirServico/?ajax=true&T147_Id=' + encodeURIComponent(T147_Id) + '&callback=?', function(request) {
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

function ven002CancelarServico(T147_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/ven/ven002/grid_func-ajax/cancelarServico/?ajax=true&T147_Id=' + encodeURIComponent(T147_Id) + '&callback=?', function(request) {
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
	dialogConfirm('Deseja Cancelar?', 'Você deseja realmente cancelar este item?', $buttons);
}	

function cad061ExcluirProduto(D158_Id, D158_D157_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad061/grid_func-ajax/excluirProduto/?ajax=true&D158_Id=' + encodeURIComponent(D158_Id) + '&D158_D157_Id=' + encodeURIComponent(D158_D157_Id) + '&callback=?', function(request) {
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
function ven002CancelarProduto(T006_Id, T006_D157_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/ven/ven002/grid_func-ajax/cancelarProduto/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&T006_T005_Id=' + encodeURIComponent(T006_T005_Id) + '&callback=?', function(request) {
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
	dialogConfirm('Deseja Cancelar?', 'Você deseja realmente cancelar este item?', $buttons);
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

function ven002CheckboxCotar(checkbox, T006_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/ven/ven002/grid_func-ajax/ven002CheckboxCotar/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});    
}

function ven002CheckboxComprar(checkbox, T006_Id, T189_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/ven/ven002/grid_func-ajax/ven002CheckboxComprar/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		if(!request.code){
			dialogConfirm('Erro', request.data);
			$(checkbox).attr('checked', false);
		}
		$('#' + divId).hideLoading();
	});    
}
function ven002CheckboxModificar(checkbox, T006_Id, T189_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/ven/ven002/grid_func-ajax/ven002CheckboxModificar/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		if(!request.code){
			dialogConfirm('Erro', request.data);
			$(checkbox).attr('checked', false);
		}
		$('#' + divId).hideLoading();
	});    
}
</script>
</div>
