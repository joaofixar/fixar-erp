<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function excluirEmail(T139_Id, divId) {
	var $buttons = {
		"Deletar": function() {
			// AJAX
            $('#' + divId).showLoading();
            $.getJSON('/etc/etc006/grid_func-ajax/excluirEmail/?ajax=true&T139_Id=' + encodeURIComponent(T139_Id) + '&callback=?', function(request) {
              	$('#' + divId).hideLoading();
				if (request.code) {
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
					reloadUI(divId);							// atualiza elementos (tabulação do grid)
				} else {
					dialogConfirm('Erro ao excluir', request.data);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
    dialogConfirm('Exclusão', "Deseja excluir este e-mail?", $buttons);
    $(this).dialog("destroy").remove();
}

function selecionarEmail(checkbox, T139_Id, divId) {
    var selected;
    if($(checkbox).is(':checked'))
		selected='1';
    else
    	selected='0';
    
    $('#' + divId).showLoading();
    $.getJSON('/etc/etc006/grid_func-ajax/selecionarEmail/?ajax=true&T139_Id=' + encodeURIComponent(T139_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
    });
}

function fin001DownloadRemessa(T002_T035_Id) {

    $.getJSON('/fin/fin001/grid_func-ajax/downloadRemessa/?ajax=true&T002_T035_Id=' + encodeURIComponent(T002_T035_Id) + '&callback=?', function(request) {
            var $buttons = {
                    "Ok": function() { $(this).dialog("destroy").remove(); }
            }
            dialogConfirm('Alerta', request.data, $buttons);
    });
}

function removerEmailSpam(T139_Id, remetente, divId, divIdRefresh) {
    var $buttons = {
        "Remover": function() {
            abrirJanela(false, divIdRootAberto(), divId, unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&tab=geral&T139_Id=' + encodeURIComponent(T139_Id) + '&divIdRefresh=' + encodeURIComponent(divIdRefresh), [700,405]);
            $(this).dialog("destroy").remove();
        },
        "Cancelar": function() { $(this).dialog("destroy").remove(); }
    }
    dialogConfirm('Exclusão', "Deseja remover o email de <b>" + remetente + "</b> da pasta de spam?<br><br>Os emails recebidos de <b>" + remetente + "</b> serão automaticamente enviados<br> para a pasta de emails recebidos", $buttons);
    $(this).dialog("destroy").remove();
}

function processarImagensEmail(T139_Id, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/etc/etc006/grid_func-ajax/processarImagensEmail/?ajax=true&T139_Id=' + encodeURIComponent(T139_Id) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code) {
            $('#' + divId + ' .imagensOcultasNotificacao').remove();
            $('#' + divId + ' .corpoEmailMontado').html(request.data);
        } else {
            dialogConfirm('Erro', 'Erro ao processar e-mail');
        }
    });
}

} </script></div>