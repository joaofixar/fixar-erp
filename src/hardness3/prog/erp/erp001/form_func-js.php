<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function erp001ProcuraCliente(divId, D024_Id, content1) {
		$('#' + divId).showLoading();
		$.getJSON('/erp/erp001/form_func-ajax/adicionarCliente/?ajax=true&D024_Id=' + D024_Id + '&callback=?', function(request) {
		if(request.code) {
			$('#' + divId).hideLoading();
			divRefresh(content1);
			fecharJanela(divId);
			var x = $('#' + 'content1' + '> .tabs > .ui-tabs-nav > li').size()-2;
			setTimeout(
				function(){
					$('.tabs').tabs('select', x);
				},1000);
		} else {
			dialogConfirm('erp', request.data);
		}
		});
	}
}</script></div>
