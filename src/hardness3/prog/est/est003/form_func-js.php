<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {


function carregaCFOP_Contatos(elem, divId, T005_C004_Id_Estoque, alterarVendedorAusente, alterarVendedor) {
    //$('#' + divId).showLoading();
    setTimeout(function() {
	    $.getJSON('/ven/ven002/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T005_D024_Id=' + encodeURIComponent($('#T005_D024_Id').val()) + '&T005_C004_Id_Estoque=' + encodeURIComponent(T005_C004_Id_Estoque) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#T005_D006_Id').html(request.data['T005_D006_Id']);
				$('#T005_C007_Id_Vendedor_Interno').val(request.data['T005_C007_Id_Vendedor_Interno']);
				$('#T005_C007_Id_Vendedor_Externo').val(request.data['T005_C007_Id_Vendedor_Externo']);
				$('#T005_Flag_Destino_Produto').val(request.data['T005_Flag_Destino_Produto']);
				if ($('#T005_D021_Id').size() > 0) { $('#T005_D021_Id').html(request.data['T005_D021_Id']); } 


				$('.itemForm-T005_C007_Id_Vendedor_Interno').attr('onfocus', 'this.initialSelect = this.selectedIndex;').attr('onchange', 'this.selectedIndex = this.initialSelect;').attr('readonly', 'readonly');
				if(request.data['notificarVendedor'] == 1){
					$('.itemForm-T005_C007_Id_Vendedor_Interno').css('border', '1px solid #023adf');
				} else if(request.data['notificarVendedor'] == 2){
					$('.itemForm-T005_C007_Id_Vendedor_Interno').css('border', '1px solid #E99601');
					if(alterarVendedorAusente == 'S'){
						$('.itemForm-T005_C007_Id_Vendedor_Interno').removeAttr('onfocus').removeAttr('onchange').removeAttr('readonly');
					}
				}
				if(alterarVendedor == 'S'){
					$('.itemForm-T005_C007_Id_Vendedor_Interno').removeAttr('onfocus').removeAttr('onchange').removeAttr('readonly');
				}

				// Contatos
				$('#T005_D013_Id').html(request.data['T005_D013_Id']);
				var T005_D013_Id_refreshBtn = $('#T005_D013_Id_refreshBtn').attr('onclick');
				var matches = T005_D013_Id_refreshBtn.match(/, '[^']+'/g);
				T005_D013_Id_refreshBtn = T005_D013_Id_refreshBtn.replace(matches[1], ", '" + request.data['T005_D013_Id_refreshBtn'] + "'");
				$('#T005_D013_Id_refreshBtn').attr('onclick', T005_D013_Id_refreshBtn);

				// endereco entrega
				if ($('#T005_D148_Id_Entrega').size() > 0) { $('#T005_D148_Id_Entrega').html(request.data['T005_D148_Id_Entrega']); } 
				var T005_D148_Id_Entrega_refreshBtn = $('#T005_D148_Id_Entrega_refreshBtn').attr('onclick');
				var matches = T005_D148_Id_Entrega_refreshBtn.match(/, '[^']+'/g);
				T005_D148_Id_Entrega_refreshBtn = T005_D148_Id_Entrega_refreshBtn.replace(matches[1], ", '" + request.data['T005_D148_Id_Entrega_refreshBtn'] + "'");
				$('#T005_D148_Id_Entrega_refreshBtn').attr('onclick', T005_D148_Id_Entrega_refreshBtn);

				// endereco cobranca
				if ($('#T005_D148_Id_Cobranca').size() > 0) { $('#T005_D148_Id_Cobranca').html(request.data['T005_D148_Id_Cobranca']); } 
				var T005_D148_Id_Cobranca_refreshBtn = $('#T005_D148_Id_Cobranca_refreshBtn').attr('onclick');
				var matches = T005_D148_Id_Cobranca_refreshBtn.match(/, '[^']+'/g);
				T005_D148_Id_Cobranca_refreshBtn = T005_D148_Id_Cobranca_refreshBtn.replace(matches[1], ", '" + request.data['T005_D148_Id_Cobranca_refreshBtn'] + "'");
				$('#T005_D148_Id_Cobranca_refreshBtn').attr('onclick', T005_D148_Id_Cobranca_refreshBtn);

				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    			dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}, 100);
}

} </script>
</div>
