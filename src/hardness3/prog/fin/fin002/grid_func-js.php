<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function fin002CampoExclusao(T015_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/fin/fin002/grid_func-ajax/excluir/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Título excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENCAO! Confirma a exclusao do titulo?", $buttons);
}

function fin002CampoExclusaoAd(T033_Id,T033_T015_Id,divId) {
	var $buttons = {
		"Remover": function() {
			$('#' + divId).showLoading();
  			$.getJSON('/fin/fin002/grid_func-ajax/excluirAd/?ajax=true&T033_Id=' + encodeURIComponent(T033_Id) + '&T033_T015_Id=' + encodeURIComponent(T033_T015_Id) + '&callback=?', function(request) {
				if (request.code) {
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
					reloadUI(divId);				// atualiza elementos (tabulação do grid)
				} else {
					alert(request.data);
				}
				$('#' + divId).hideLoading();
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Confirmação', "<center>Confirma a exclusão?</center>", $buttons);
	$(this).dialog("destroy").remove();
}

function fin002ValidaCampoBxEstorno(T015_Id,T015_DP,divId,divIdRoot){	
	if((T015_DP==0)){
		var $buttons = {
				"Ok": function() { $(this).dialog("destroy").remove(); }
			}		
		dialogConfirm('Confirmação', "Este título ainda não foi baixado...", $buttons);		
	}else{		
		// chama o form de estorno
		abrirJanela(false, divIdRoot, divId,unique(), '', 'Estorno de Título - ID: '+T015_Id, '/fin/fin002/form/fin002form04/', '&acaoId=' + T015_Id + '&tab=estorno', [468,200]);
		//console.log('bbb');
	}
}

function fin002CheckboxBx(checkbox,T015_Id,divId){
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
	   
		$('#' + divId).showLoading();
		$.getJSON('/fin/fin002/grid_func-ajax/gravarCampo/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&selected=' + selected + '&callback=?', function(request) {
			carregaInputBaixar();
			$('#' + divId).hideLoading();		
		});
}
 
function carregaInputBaixar() {
	$.getJSON('/fin/fin002/grid_func-ajax/carregaInputBaixar/?ajax=true&extra='+encodeURIComponent($('#fin002ExtraBtns').data('extra'))+'&callback=?', function(request) {
		$('#inputBaixar').val(request.data);
	});
}

function baixarTitulosEditarCampo($campo) {
	// id="T103_Id-481|T015_Id-123"
	var id_ = $campo.closest('tr').attr('id').split('|');
	id = id_[0].replace('T103_Id-', '');
	
	if ($campo.is(':checkbox')) {
		id = id_[1].replace('T015_Id-', '');
    	var Conteudo_Campo_Editado = ($campo.is(':checked')) ? 'S' : 'N';
	} else {
   		var Conteudo_Campo_Editado = $campo.val();
	}
	var Campo_Editado = $campo.attr('id').replace(/_$/, '');
	$.getJSON('/fin/fin002/grid_func-ajax/baixarTitulosEditarCampo/?ajax=true&acaoId=' + encodeURIComponent(id) + '&Campo_Editado=' + encodeURIComponent(Campo_Editado) + '&Conteudo_Campo_Editado=' + encodeURIComponent(Conteudo_Campo_Editado) + '&callback=?', function(request) {
		atualizaTotaisBaixar();
	});
	
}

function atualizaTotaisBaixar(){
	$.getJSON('/fin/fin002/grid_func-ajax/atualizaTotaisBaixar/?ajax=true&extra='+encodeURIComponent($('#fin002ExtraBtns').data('extra'))+'&callback=?', function(request) {
	//$.getJSON('/fin/fin002/grid_func-ajax/atualizaTotaisBaixar/?ajax=true&callback=?', function(request) {
		$('#fin002BaixarValor').html(request.data['fin002BaixarValor']);
		$('#fin002BaixarValorPago').html(request.data['fin002BaixarValorPago']);
	});
}

function fin002CheckboxRp(checkbox,T015_Id,divId) {
    var selected;
    if ($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';

    $('#' + divId).showLoading();
    $.getJSON('/fin/fin002/grid_func-ajax/checkboxRp/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&selected=' + selected + '&callback=?', function(request) {
        carregaInputReparcelar();
        carregaInputAgrupar();
        $('#' + divId).hideLoading();
    });
}

function carregaInputReparcelar() {
    $.getJSON('/fin/fin002/grid_func-ajax/carregaInputReparcelar/?ajax=true&extra='+encodeURIComponent($('#fin002ExtraBtns').data('extra'))+'&callback=?', function(request) {
        $('#inputReparcelar').val(request.data);
    });
}

function carregaInputAgrupar() {
    $.getJSON('/fin/fin002/grid_func-ajax/carregaInputAgrupar/?ajax=true&extra='+encodeURIComponent($('#fin002ExtraBtns').data('extra'))+'&callback=?', function(request) {
        $('#inputAgrupar').val(request.data);
    });
}

function fin002ExcluirEmMassa(divId) {
    var extra = $('#fin002ExtraBtns').data('extra');

    if (!extra) {
        alert('Não foi possível identificar o filtro atual do grid. Atualize a tela e tente novamente.');
        return;
    }

    dialogConfirm('Excluir títulos', 'Confirma a exclusão dos títulos selecionados?', {
        'Excluir': function() {
            $(this).dialog('destroy').remove();
            $('#' + divId).showLoading();

            $.getJSON('/fin/fin002/grid_func-ajax/excluirEmMassa/?ajax=true&callback=?', {
                extra: extra
            }, function(request) {
                if (request.code) {
                    alert(request.data);
                    divRefresh(divId, true);
                } else {
                    alert(request.data || 'Não foi possível excluir os títulos selecionados.');
                }
            }).fail(function() {
                alert('Não foi possível comunicar com o servidor para excluir os títulos.');
            }).always(function() {
                $('#' + divId).hideLoading();
            });
        },
        'Cancelar': function() {
            $(this).dialog('close');
        }
    });
}

function fin002MarcarVisto(checkbox,T015_Id,divId) {
    var selected;
    if ($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';

    $('#' + divId).showLoading();
    $.getJSON('/fin/fin002/grid_func-ajax/fin002MarcarVisto/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });
}

} </script></div>