<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function est015CampoExclusao(T218_Id, divId) 
{
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/est/est015/grid_func-ajax/excluir/?ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&callback=?', function(request) {
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
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do registro??", $buttons);
}

function est015CheckboxComprar(checkbox,T219_Id,divId, refresh) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est015/grid_func-ajax/est015CheckboxComprar/?ajax=true&T219_Id=' + encodeURIComponent(T219_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if(refresh == 'S'){
			divRefresh(divId);
		}
	});	
}
function est015ExcluirServico(T223_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est015/grid_func-ajax/excluirServico/?ajax=true&T223_Id=' + encodeURIComponent(T223_Id) + '&callback=?', function(request) {
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

function est015ExcluirProduto(T219_Id, divId){
	$.getJSON('/est/est015/grid_func-ajax/verificaExcluirProduto/?ajax=true&T219_Id=' + encodeURIComponent(T219_Id) + '&callback=?', function(request) {
		if (request.code) {
			var $buttons = {
				"Sim": function() {
					//$('#' + divId).showLoading();
					$.getJSON('/est/est015/grid_func-ajax/excluirProduto/?ajax=true&T219_Id=' + encodeURIComponent(T219_Id) + '&callback=?', function(request) {
						if (request.code) {
						    $('#' + divId + ' #T219_Id-' + T219_Id).remove(); // remove linha
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
			if (request.data != 'Este produto está vinculado a um OC.') {
				var $buttons = {
					"Sim": function() {
						//$('#' + divId).showLoading();
						$.getJSON('/est/est015/grid_func-ajax/excluirProduto/?ajax=true&T219_Id=' + encodeURIComponent(T219_Id) + '&callback=?', function(request) {
							if (request.code) {
								$('#' + divId + ' #T219_Id-' + T219_Id).remove(); // remove linha
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
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    dialogConfirm('Erro', "Não foi possivel excluir produto!<br>A OC com este produto ja foi gerada.", $buttons);
			}
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
	function est015CheckboxDividir(checkbox,id,divId,TXXX) {
	    var selected;
	    if($(checkbox).is(':checked'))
	        selected='S';
	    else
	        selected='N';
	    $('#' + divId).showLoading();                
	    $.getJSON('/est/est015/grid_func-ajax/est015CheckboxDividir/?ajax=true&id=' + encodeURIComponent(id) + '&selected=' + selected + '&TXXX=' + encodeURIComponent(TXXX) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
	    });    
	}

	function est015CheckboxCotar(checkbox, T219_Id, divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		$('#' + divId).showLoading();                
		$.getJSON('/est/est015/grid_func-ajax/est015CheckboxCotar/?ajax=true&T219_Id=' + encodeURIComponent(T219_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});
	}

	function est015AprovarOrcamento(T218_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est015/grid_func-ajax/est015AprovarOrcamento/?ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', 'Cotação de Compra aprovada!', $buttons);
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
		dialogConfirm('Atenção!!', 'Deseja aprovar a Cotação de Compra '+T218_Id+' ?', $buttons);
	}
} </script></div>