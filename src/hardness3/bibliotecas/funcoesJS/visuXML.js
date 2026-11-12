function visuXML(T007_Id) {
	$.getJSON('/ven/ven012/grid_func-ajax/visuXML/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
		if (!request.code) {
			dialogConfirm("Erro", 'NF sem XML gerado!');
		} else {
			dialogConfirm("Visualização do XML", '<div style="width: 800px; height: 230px;" class="visuXML"></div><br /><textarea style="width: 800px; height: 150px;" class="visuXML2"></textarea>');
			setTimeout(function() {
				$('.visuXML').html(request.table);
				$('.visuXML2').val(request.xml);
			}, 200);
		}
	});
}
