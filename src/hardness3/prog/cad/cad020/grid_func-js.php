<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad002CampoExcluir1(T140_Id, divId) 
{
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/cad/cad002/grid_func-ajax/excluir/?ajax=true&T140_Id=' + encodeURIComponent(T140_Id) + '&callback=?', function(request) {
				if (request.code) {
					$('#' + request.data).remove();	// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
					reloadUI(divId);				// atualiza elementos (tabulação do grid)
					alert("Certificado Excluído");
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

/**
* Esta função é antiga, utilizada somente antes do REFACTORY do sistema de ETIQUETAS
* A partir de agora, todas as etiquetas são gerencidas/criadas pelo editor de ediquetas
* Somente foi mantido este código, porque a CRV utiliza
* Na CRV as etiquetas possuem várias particularidades, que com o tempo devem ser passadas para o editor de etiquetas
*/
function cad020ImpTag(D00X_Id, divId, QtdEtiquetas, QtdProdutos, TipoEtiqueta, D138_Id){
	$('#' + divId).showLoading();
  	$.getJSON('/est/est002/grid_func-ajax/impTag/?ajax=true&D00X_Id=' + encodeURIComponent(D00X_Id) + '&QtdEtiquetas=' + encodeURIComponent(QtdEtiquetas) + '&TipoEtiqueta=' + encodeURIComponent(TipoEtiqueta) + '&QtdProdutos=' + encodeURIComponent(QtdProdutos) + '&D138_Id=' + encodeURIComponent(D138_Id) + '&callback=?', function(request) {
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

function cad020CheckboxImprimir(checkbox,D009_Id,divId) {
    var selected;
    if($(checkbox).is(':checked'))
        selected='S';
    else
        selected='N';
    $('#' + divId).showLoading();                
    $.getJSON('/cad/cad020/grid_func-ajax/cad020CheckboxImprimir/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
    });    
}

} </script></div>