<?php
//require_once('prog/crm/crm001/grid_func-js-principalOrcamentos.php');
//require_once('prog/crm/crm001/grid_func-js-principalPedidos.php');
	
?>

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
    
function ven001CampoExclusao(T003_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/ven/ven001/grid_func-ajax/excluir/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();	// apaga linha (cada elemento <tr> tem o id="d110 _Id-*")
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
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusão do orçamento " + T003_Id +"?", $buttons);
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

function crm001EnviarNotificacao(D181_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/crm/crm001/grid_func-ajax/enviarNotificacao/?ajax=true&D181_Id=' + encodeURIComponent(D181_Id) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Sucesso', request.data, $buttons);
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
	dialogConfirm('Enviar', 'Deseja enviar essa notificação para todos os clientes?', $buttons);
}

function ven001ExcluirProduto(T004_Id, divId){
	var $buttons = {
		"Sim": function() {
			//$('#' + divId).showLoading();
			$.getJSON('/ven/ven001/grid_func-ajax/excluirProduto/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&callback=?', function(request) {
				if (request.code) {
					$('#' + divId + ' #T004_Id-' + T004_Id).remove(); // remove linha
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
function crm001CheckboxDuplicar(checkbox,T005_Id,divId) {
	    var selected;
	    if($(checkbox).is(':checked'))
	        selected='S';
	    else
	        selected='N';
	    $('#' + divId).showLoading();                
	    $.getJSON('/crm/crm001/grid_func-ajax/crm001CheckboxDuplicarPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&selected=' + selected  + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
	    });    
	}

function crm001CheckboxGerarNF(checkbox,T005_Id,divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/crm/crm001/grid_func-ajax/crm001CheckboxGerarNF/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&selected=' + selected  + '&callback=?', function(request) {
    	if(!request.code){        	
        	var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			dialogConfirm('Erro', "Este pedido ja está marcado", $buttons);
        }
        $('#' + divId).hideLoading();
    });    
}

function crm001CheckBoxEnviarAgenda(checkbox,D024_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/crm/crm001/grid_func-ajax/crm001CheckBoxEnviarAgenda/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

function crm001cancelarNotaFiscal(T007_Id, divId, divIdRoot){
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

function crm001EstornarCancelarNotaFiscal(T007_Id, divId, divIdRoot){
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


function campoAtivarCliente(D024_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/crm/crm001/form_func-ajax/ativarClienteVendedor/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
			    $('#' + divId).hideLoading();
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
					dialogConfirm("Sucesso", request.data, $buttons);
					atualizarLinhaGrid(divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
					dialogConfirm("Erro!", request.data, $buttons);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção', 'Você deseja ativar este cliente?', $buttons);
}

} </script></div>