<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	
function ven012CampoExcluirNFe(T007_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven012/grid_func-ajax/excluirNFe/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
					if (request.code) {
 						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', request.data, $buttons);
					    divRefresh(divId, true);
					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Erro', request.data, $buttons);
					}
					$('#' + divId).hideLoading();
					reloadUI(divId);
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Deseja Excluir?', 'ATENCAO! Confirma a exclusao da NFe?', $buttons);
	}


function ven012RetornarPedido(T005_Id, divId){
		var $buttons = {
			"Sim": function() {
				//var T005_Id = $('#' + divId + ' #T005_Id').val();
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven012/grid_func-ajax/retornarPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
					if (request.code) {
 						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', 'Pedido retornado!', $buttons);
					    divRefresh(divId, true);
					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Erro', request.data, $buttons);
					}
					$('#' + divId).hideLoading();
					reloadUI(divId);
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Atenção!!', 'Confirma retorno do pedido '+T005_Id+' para o vendedor?', $buttons);
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

function ven012ImprimirPedido(T005_Id){
	$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
		if(request.code===true) {
			var w = window.open(request.data);
			w.addEventListener("load", function(event) {
				$.getJSON('/ven/ven002/form_func-ajax/ven002DeletarPDFPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {});
			});
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			dialogConfirm('Erro', request.data, $buttons);
		}
	});
}

function ven012CheckboxDividir(checkbox,id,divId,TXXX) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/ven/ven012/grid_func-ajax/ven012CheckboxDividir/?ajax=true&id=' + encodeURIComponent(id) + '&selected=' + selected + '&TXXX=' + encodeURIComponent(TXXX) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });    
}

function ven012CheckboxComissao(checkbox,id,divId,TXXX) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/ven/ven012/grid_func-ajax/ven012CheckboxComissao/?ajax=true&id=' + encodeURIComponent(id) + '&selected=' + selected + '&TXXX=' + encodeURIComponent(TXXX) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });
}

function ven012DuplicarProduto(divId, T006_Id){
	$('#' + divId).showLoading();                
    $.getJSON('/ven/ven012/grid_func-ajax/ven012DuplicarProduto/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if(request.code == true){
			divRefresh(divId);
        }
    });
}

function ven012MarcarParaEnviar(checkbox,divId,T007_Id) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/ven/ven012/grid_func-ajax/ven012MarcarParaEnviar/?ajax=true&selected=' + selected + '&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });    
}

} </script></div>