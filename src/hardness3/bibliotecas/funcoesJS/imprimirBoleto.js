function imprimirBoleto(T117_Id, confUsuario) {
	$.getJSON('/fin/fin005/grid_func-ajax/gerarPDFboleto/?ajax=true&T117_Id=' + encodeURIComponent(T117_Id)  + '&callback=?', function(request) {
		if (request.code) {
			var w = window.open(confUsuario + request.data,0,0,800,500,'no','no','no','no','yes','yes',0);
			w.addEventListener("load", function(event) {
				$.getJSON('/fin/fin005/grid_func-ajax/fin005DeletarPDFBoleto/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {});
			});
		} else {
			window.open('Operação cancelada', request.data);
		}
	});
}
