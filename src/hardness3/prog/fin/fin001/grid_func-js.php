<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)
function fin001CampoExclusao(T002_Id, T035_Id, divId) {
    if (T035_Id > 0)
    {
    	var $buttons = {
			"Deletar": function() {
				var $buttons = {
					"Deletar": function() {
						// AJAX
						//parent.MostrarMensagem('inline');
              			//document.getElementById('fme_gravar').src='excluir.php?T002_Id='+T002_Id+'&T035_Id='+T035_Id;
              			$('#' + divId).showLoading();
              			$.getJSON('/fin/fin001/grid_func-ajax/excluir/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&T035_Id=' + encodeURIComponent(T035_Id) + '&callback=?', function(request) {
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
    			dialogConfirm('Tem certeza?', "Tem certeza mesmo?", $buttons);
    			$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENCAO! Esse titulo foi enviado para banco na remessa " + T035_Id + "!", $buttons);
	}
    else
    {
    	var $buttons = {
			"Deletar": function() {
				var $buttons = {
					"Deletar": function() {
						// sistema antigo:
						//parent.MostrarMensagem('inline');
             			//document.getElementById('fme_gravar').src='excluir.php?T002_Id='+T002_Id;
             			$('#' + divId).showLoading();
              			$.getJSON('/fin/fin001/grid_func-ajax/excluir/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&T035_Id=' + encodeURIComponent(T035_Id) + '&callback=?', function(request) {
              				$('#' + divId).hideLoading();
							if (request.code) {
								$('#' + request.data).remove();				// apaga linha
								reloadUI(divId);							// atualiza elementos (tabulação do grid)
							} else {
								alert(request.data);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Cancelar": function() { $(this).dialog("destroy").remove(); }
				}
    			dialogConfirm('Tem certeza?', "ATENCAO! Confirma a exclusao do titulo?", $buttons);
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENCAO! Confirma a exclusao do titulo?", $buttons);
    }
}

function fin001CampoExclusaoAd(T020_Id,T020_T002_Id,divId) {
	var $buttons = {
		"Remover": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/fin/fin001/grid_func-ajax/excluirAd/?ajax=true&T020_Id=' + encodeURIComponent(T020_Id) + '&T020_T002_Id=' + encodeURIComponent(T020_T002_Id) + '&callback=?', function(request) {
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

function fin001CampoExclusaoRemessa(T035_Id,divId) {
	var $buttons = {
		"Remover": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/fin/fin001/form_func-ajax/removerRemessa/?ajax=true&T002_Id=' + encodeURIComponent(T035_Id) + '&callback=?', function(request) {
  				$('#' + divId).hideLoading();
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Ok', 'Remessa removida com sucesso!', $buttons);
				    divRefresh(divId, true);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Erro', request.data, $buttons);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Confirmação', "Esta ação irá remover a remessa de todos os títulos da remessa atual, confirma?", $buttons);
	$(this).dialog("destroy").remove();
}

function fin001CheckboxRm(checkbox,T002_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	
	$('#' + divId).showLoading();				
	$.getJSON('/fin/fin001/grid_func-ajax/checkboxRm/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {
		if(request.code) {
			carregaInputRemessa();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			dialogConfirm('Erro', request.data, $buttons);
			$(checkbox).attr('checked', false);
		}
		$('#' + divId).hideLoading();
	});	
}

function fin001CheckboxBx(checkbox,T002_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	
	$('#' + divId).showLoading();				
	$.getJSON('/fin/fin001/grid_func-ajax/checkboxBx/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {
		carregaInputBaixar();
		$('#' + divId).hideLoading();					
	});	
}

function fin001CheckboxRp(checkbox,T002_Id,divId) {
	var selected;
	if ($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';

	$('#' + divId).showLoading();
	$.getJSON('/fin/fin001/grid_func-ajax/checkboxRp/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {
		carregaInputReparcelar();
		carregaInputReparcelar2();
		carregaInputAgrupar();
		$('#' + divId).hideLoading();
	});
}

function fin001ValidaCampoRmEstorno(event, T002_Id,T002_DR,divId,divIdRoot){
	if((T002_DR==0)){
		var $buttons = {
				"Ok": function() { $(this).dialog("destroy").remove(); }
			}		
		dialogConfirm('Alerta', "Este título ainda não foi baixado...", $buttons);		
	}else{
		// chama o form de estorno
		var content=unique();
		abrirJanela(event, divIdRoot, divId, content, '', 'Estorno de Título - ID: '+T002_Id, '/fin/fin001/form/fin001form05/', '&acaoId=' + T002_Id + '&tab=estorno', [490,230]);
	}
}

function carregaInputBaixar() {
	$.ajax({
		url: '/fin/fin001/grid_func-ajax/carregaInputBaixar/',
		dataType: 'json',
		data: 'ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?',
		type: 'POST',
		success: function(request) {
			//$.getJSON('/fin/fin001/grid_func-ajax/carregaInputBaixar/?ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?', function(request) {
			$('#inputBaixar').val(request.data);
		}
	});
}

function carregaInputReparcelar() {

	$.ajax({
		url: '/fin/fin001/grid_func-ajax/carregaInputReparcelar/',
		dataType: 'json',
		data: 'ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?',
		type: 'POST',
		success: function(request) {
			// $.getJSON('/fin/fin001/grid_func-ajax/carregaInputReparcelar/?ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?', function(request) {
			$('#inputReparcelar').val(request.data);
		}
	});
}
function carregaInputReparcelar2() {

	$.ajax({
		url: '/fin/fin001/grid_func-ajax/carregaInputReparcelar2/',
		dataType: 'json',
		data: 'ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?',
		type: 'POST',
		success: function(request) {
		//	$.getJSON('/fin/fin001/grid_func-ajax/carregaInputReparcelar2/?ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?', function(request) {
			$('#inputReparcelar2').val(request.data);
		}
	});
}

function carregaInputAgrupar() {
	$.ajax({
		url: '/fin/fin001/grid_func-ajax/carregaInputAgrupar/',
		dataType: 'json',
		data: 'ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?',
		type: 'POST',
		success: function(request) {
		// $.getJSON('/fin/fin001/grid_func-ajax/carregaInputAgrupar/?ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?', function(request) {
			$('#inputAgrupar').val(request.data);
		}
	});
}

function carregaInputRemessa() {
	$.ajax({
		url: '/fin/fin001/grid_func-ajax/carregaInputRemessa/',
		dataType: 'json',
		data: 'ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?',
		type: 'POST',
		success: function(request) {
		//$.getJSON('/fin/fin001/grid_func-ajax/carregaInputRemessa/?ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?', function(request) {
			$('#inputRemessa').val(request.data);
		}
	});

}

/**
* Edita um campo diretamente no formulário (e gera uma AJAX)
*/
function baixarTitulosEditarCampo($campo) {
	// id="T103_Id-481|T002_Id-123"
	var rowId = $campo.closest('tr').attr('id');
	if (!rowId || rowId.indexOf('|') < 0) {
		return;
	}
	var id_ = rowId.split('|');
	id = id_[0].replace('T103_Id-', '');
	
	if ($campo.is(':checkbox')) {
		id = id_[1].replace('T002_Id-', '');
    	var Conteudo_Campo_Editado = ($campo.is(':checked')) ? 'S' : 'N';
	} else {
   		var Conteudo_Campo_Editado = $campo.val();
	}
	var Campo_Editado = $campo.attr('id').replace(/_$/, '');
	$.getJSON('/fin/fin001/grid_func-ajax/baixarTitulosEditarCampo/?ajax=true&acaoId=' + encodeURIComponent(id) + '&Campo_Editado=' + encodeURIComponent(Campo_Editado) + '&Conteudo_Campo_Editado=' + encodeURIComponent(Conteudo_Campo_Editado) + '&callback=?', function(request) {
		atualizaTotaisBaixar();
	});
	
}

function atualizaTotaisBaixar(){
	$.getJSON('/fin/fin001/grid_func-ajax/atualizaTotaisBaixar/?ajax=true&extra='+encodeURIComponent($('#fin001ExtraBtns').data('extra'))+'&callback=?', function(request) {
		$('#fin001BaixarValor').html(request.data['fin001BaixarValor']);
		$('#fin001BaixarValorRecebido').html(request.data['fin001BaixarValorRecebido']);
	});
}

function fin001MarcarVisto(checkbox,T002_Id,divId) {
    var selected;
    if ($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';

    $('#' + divId).showLoading();
    $.getJSON('/fin/fin001/grid_func-ajax/fin001MarcarVisto/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });
}

function fin001excluirRetorno(T037_Id,divId) {
	var $buttons = {
		"Sim": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/fin/fin001/grid_func-ajax/fin001excluirRetorno/?ajax=true&T037_Id=' + encodeURIComponent(T037_Id) + '&callback=?', function(request) {
  				$('#' + divId).hideLoading();
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('OK', request.data, $buttons);
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
					reloadUI(divId);							// atualiza elementos (tabulação do grid)
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Erro', request.data, $buttons);
					$(checkbox).attr('checked', false);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Atenção', "Você tem certeza que deseja excluir este retorno?", $buttons);
	$(this).dialog("destroy").remove();
}

function fin001MarcarBoletoAtualizar(checkbox,T002_Id,divId) {
	var selected;
	if($(checkbox).is(':checked')) {
		selected='S';
	} else {
		selected='N';          
	}
	$.getJSON('/fin/fin001/grid_func-ajax/marcarTituloEnviarEmail/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}

} </script></div>
