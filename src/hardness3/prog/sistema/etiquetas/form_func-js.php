<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function etiquetasGenericaXX000(divId, impLetras, impInicio, impFim, addZeros){
	$('#' + divId).showLoading();
  	$.getJSON('/sistema/etiquetas/form_func-ajax/etiquetasGenericaXX000/?ajax=true&impLetras=' + encodeURIComponent(impLetras) + '&impInicio=' + encodeURIComponent(impInicio) + '&impFim=' + encodeURIComponent(impFim) + '&addZeros=' + encodeURIComponent(addZeros) + '&callback=?', function(request) {
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