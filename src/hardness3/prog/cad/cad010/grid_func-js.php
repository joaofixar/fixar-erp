<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad010CampoExclusao(D109_Id, divId) 
{
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/cad/cad010/grid_func-ajax/excluir/?ajax=true&D109_Id=' + encodeURIComponent(D109_Id) + '&callback=?', function(request) {
				if (request.code) {
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="D109 _Id-*")
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

function cad010CampoExclusaoCompetencia(D115_Id, divId) 
{
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/cad/cad010/grid_func-ajax/excluircompetencia/?ajax=true&D115_Id=' + encodeURIComponent(D115_Id) + '&callback=?', function(request) {
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
	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do registro??", $buttons);
}

function cad010CadImpDigital(D109_Id){
	netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	var exe = window.Components.classes['@mozilla.org/file/local;1'].createInstance(Components.interfaces.nsILocalFile);
	exe.initWithPath("C:\\hardness\\hardnessCadDigital.exe");
	var run = window.Components.classes['@mozilla.org/process/util;1'].createInstance(Components.interfaces.nsIProcess);
	run.init(exe);
	var parameters = [D109_Id];
	run.run(false, parameters, parameters.length);
}

} </script></div>