<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad051ImprimirEtiquetas(divId, impQuantidade, impQuantidadeEstoque, D138_Id, D004_Id, D009_Id, D024_Id) {
	var selected;
	if($(impQuantidadeEstoque).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();
  	$.getJSON('/cad/cad051/form_func-ajax/imprimirEtiquetas/?ajax=true&impQuantidade=' + encodeURIComponent(impQuantidade) + '&impQuantidadeEstoque=' + selected + '&D138_Id=' + encodeURIComponent(D138_Id) + '&D004_Id=' + encodeURIComponent(D004_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
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
function cad051ImprimirEtiquetasTesteGenerico(divId, param) {
	$('#' + divId).showLoading();
  	$.getJSON('/cad/cad051/form_func-ajax/imprimirEtiquetas/?ajax=true&param=' + encodeURIComponent(param) + '&callback=?', function(request) {
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
} </script></div>