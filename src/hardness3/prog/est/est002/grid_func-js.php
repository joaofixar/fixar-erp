<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function est002RetornarPedido(T005_Id, divId){
		var $buttons = {
			"Sim": function() {
				//var T005_Id = $('#' + divId + ' #T005_Id').val();
				$('#' + divId).showLoading();
				$.getJSON('/est/est002/grid_func-ajax/retornarPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', 'Pedido retornado!', $buttons);
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
		dialogConfirm('Atenção!!', 'Confirma retorno do pedido '+T005_Id+' para o vendedor?', $buttons);
	}
	
	function est002ConfirmarEntrega(T005_Id, divId){
		var $buttons = {
			"Sim": function() {
				//var T005_Id = $('#' + divId + ' #T005_Id').val();
				$('#' + divId).showLoading();
				$.getJSON('/est/est002/grid_func-ajax/confirmarEntrega/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', request.data, $buttons);
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
		dialogConfirm('Atenção!!', 'Confirma entrega do pedido '+T005_Id+'?', $buttons);
	}
	/**
		$('#ven002FaturarEmergencia').unbind('click').bind('click', function(data) {
			var \$buttons = {
				"Sim": function() {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Faturar Emergência', '/ven/ven002/form/ven002FormFaturarEmergencia/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [450,180]);
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Faturar Emergência?', 'Este pedido será encaminhado direto para emissão de NFe!', \$buttons);
		});	
	*/

	function est002CheckboxColeta(checkbox, T005_Id, divId){
		var selected;
		if($(checkbox).is(':checked')) {
			selected='S';
		} else {
			selected='N';
		}
		$('#' + divId).showLoading();				
		$.getJSON('/est/est002/grid_func-ajax/checkboxFlagColeta/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&selected=' + selected + '&callback=?', function(request) {
			divRefresh(divId, true);
			$('#' + divId).hideLoading();
		});	
	}

	function est003RequisitarCompra(T006_Id, D009_Id, divId){
		var $buttons = {
			"Sim": function() {
				//var T005_Id = $('#' + divId + ' #T005_Id').val();
				$('#' + divId).showLoading();				
				$.getJSON('/est/est003/grid_func-ajax/est003RequisitarCompra/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&divId=' + encodeURIComponent(divId) + '&callback=?', function(request) {
					divRefresh(divId, true);
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Atenção!!', 'Deseja criar uma requisição de compra??', $buttons);
	}

	function imprimirEtiquetasProdutosPedido(divId, D138_Id, T005_Id) {
		$('#' + divId).showLoading();
		$.getJSON('/cad/cad051/form_func-ajax/imprimirEtiquetas/?ajax=true&D138_Id=' + encodeURIComponent(D138_Id) + '&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
			if (request.code) {
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				var exe = window.Components.classes['@mozilla.org/file/local;1'].createInstance(Components.interfaces.nsILocalFile);
				exe.initWithPath("C:\\hardness\\hardnessImprimir.exe");
				var run = window.Components.classes['@mozilla.org/process/util;1'].createInstance(Components.interfaces.nsIProcess);
				run.init(exe);
				var parameters = [request.data];
				run.run(false, parameters, parameters.length);
			}
		});
	}

	function est002validaCampoCheckboxRomaneio(checkbox,T007_Id,divId) {
	    var selected;
	    if($(checkbox).is(':checked'))
	        selected='S';
	    else
	        selected='N';
	    $('#' + divId).showLoading();                
	    $.getJSON('/est/est002/grid_func-ajax/est002validaCampoCheckboxRomaneio/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&selected=' + selected  + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
	    });    
	}

	function est002validaCampoCheckboxEnvioEmail(checkbox,T007_Id,divId) {
	    var selected;
	    if($(checkbox).is(':checked'))
	        selected='S';
	    else
	        selected='N';
	    $('#' + divId).showLoading();                
	    $.getJSON('/est/est002/grid_func-ajax/est002validaCampoCheckboxEnvioemail/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&selected=' + selected  + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
	    });    
	}

	function est002CorreioPostaEtiqueta(T007_Id, divId,mensagem){
		var $buttons = {
			"Sim": function() {
				//var T005_Id = $('#' + divId + ' #T005_Id').val();
				$('#' + divId).showLoading();
				$.getJSON('/est/est002/grid_func-ajax/correioPostarEtiqueta/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Sucesso',request.data, $buttons);
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
		dialogConfirm('Atenção!!', mensagem, $buttons);
	}

	function est002excluirVolume(T314_Id, divId) {
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est002/grid_func-ajax/est002ExcluirVolume/?ajax=true&T314_Id=' + encodeURIComponent(T314_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttonsSucesso = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    divRefresh(divId, true);
					} else {
						var $buttonsErro = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Erro', request.data, $buttonsErro);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Atenção!!', 'Este processo irá cancelar o volume e desfazer as separações desses itens.<br><br>Deseja continuar?', $buttons);
	}
} </script></div>