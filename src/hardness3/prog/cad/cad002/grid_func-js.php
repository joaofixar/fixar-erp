<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad002CampoExcluir1(T140_Id, D001_Id, divId) 
{
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/cad/cad002/grid_func-ajax/excluir/?ajax=true&T140_Id=' + encodeURIComponent(T140_Id) + '&D001_Id=' + encodeURIComponent(D001_Id) + '&callback=?', function(request) {
				if (request.code) {
					$('#' + request.data).remove();	// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
					reloadUI(divId);				// atualiza elementos (tabulação do grid)
					alert("Certificado Excluído");
					divRefresh(divId);
				} else {
					alert(request.data);
				}
				$('#' + divId).hideLoading();
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Deseja Remover?', "ATENCAO! Confirma a exclusao do Certificado?", $buttons);
}

function cad002CheckboxFlagPadrao(checkbox,D001_Id,divId){
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
	   
		$('#' + divId).showLoading();
		$.getJSON('/cad/cad002/grid_func-ajax/gravarCampo/?ajax=true&D001_Id=' + encodeURIComponent(D001_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();		
		});
}
function AlterarICF1(elem, divId, extra) {
	var buttons = { 
		"Ok": function() { 
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad002/grid_func-ajax/alterarICF1/?ajax=true&extra=' + extra + '&valorDigitado=' + encodeURIComponent(elem.val()) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
				if (request.code) {
					//$('#D021_D020_Id').html(request.data['D021_D020_Id']);
					divRefresh(divId);
				} else {
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				divRefresh(divId);
    				dialogConfirm("OK!", request.data, buttons)
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Fechar": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm("Confirmação", 'Tem certeza que deseja alterar o ICF 1 de todos os elementos desta tabela?', buttons)
}

function AlterarICF2(elem, divId, extra) {
	var buttons = { 
		"Ok": function() { 
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad002/grid_func-ajax/alterarICF2/?ajax=true&extra=' + extra + '&valorDigitado=' + encodeURIComponent(elem.val()) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
				if (request.code) {
					//$('#D021_D020_Id').html(request.data['D021_D020_Id']);
					divRefresh(divId);
				} else {
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				divRefresh(divId);
    				dialogConfirm("OK!", request.data, buttons)
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Fechar": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm("Confirmação", 'Tem certeza que deseja alterar o ICF 2 de todos os elementos desta tabela?', buttons)
}

function AlterarICF3(elem, divId, extra) {
	var buttons = { 
		"Ok": function() { 
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad002/grid_func-ajax/alterarICF3/?ajax=true&extra=' + extra + '&valorDigitado=' + encodeURIComponent(elem.val()) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
				if (request.code) {
					//$('#D021_D020_Id').html(request.data['D021_D020_Id']);
					divRefresh(divId);
				} else {
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				divRefresh(divId);
    				dialogConfirm("OK!", request.data, buttons)
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Fechar": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm("Confirmação", 'Tem certeza que deseja alterar o ICF 3 de todos os elementos desta tabela?', buttons)
}
				
function AlterarIPV(elem, divId, extra) {
	var buttons = { 
		"Ok": function() { 
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad002/grid_func-ajax/alterarIPV/?ajax=true&extra=' + extra + '&valorDigitado=' + encodeURIComponent(elem.val()) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
				if (request.code) {
					//$('#D021_D020_Id').html(request.data['D021_D020_Id']);
					divRefresh(divId);
				} else {
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				divRefresh(divId);
    				dialogConfirm("OK!", request.data, buttons)
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Fechar": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm("Confirmação", 'Tem certeza que deseja alterar o IPV de todos os elementos desta tabela?', buttons)
}
} </script></div>