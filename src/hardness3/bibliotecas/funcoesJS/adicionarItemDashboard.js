// Adiciona um item do dashboard, mais não carrega
function adicionarItemDashboard(divId, D132_Id, tab, removeLoading, tipo, primeiroItem, left, top, width, height) {
	var screenWidth = $('#' + divId).outerWidth(true);
	if (tipo == 1) {
		var width = 635;
		var height = 420;
	} else {
		var width = 311;
		var height = 65;
	}

	var $div = $('<div id="' + tab + D132_Id + '" formEditorHidden="0" formEditorID="' + D132_Id + '" formEditorDIV="1" class="formEditor formEditor-' + D132_Id + ' formEditorBI"></div>')
	.css({
		'position' : 'relative',
		'top' : '35px',
		'width': width + 'px',
		'max-width': width + 'px',
		'height': height + 'px',
		'margin' : '5px',
		'overflow': 'auto',
		'border-radius': '6px',
	})
	.appendTo($('#' + divId + ' .formFormatoEditor'));
	// Show loading do sistema sempre faz um overlay fixo por cima (não funciona no scroll), vamos usar diferente aqui
	//$('#' + tab + D132_Id).showLoading();
	$('#' + tab + D132_Id).append(
		$('<div class="loading-indicator-overlay-manual" style="width: 100%; height: 100%;"><div class="loading-indicator-manual" style="margin: auto; height: 100%;"></div></div>')
	);
}

// Faz o loading do item, um a um
function carregarItemDashboard(divId, D132_Ids, tab, removeLoading, D132_Tipos, left, top, width, height) {
	var D132_Id = D132_Ids.shift();
	var D132_Tipo = D132_Tipos.shift();
	var $div = $('#' + tab + D132_Id);
	if (D132_Tipo == 1) {
		// Grid/gráfico
		$.getJSON('/etc/etc011/ajax/adicionarItemDashboard/?ajax=true&D132_Id=' + encodeURIComponent(D132_Id) + '&callback=?', function (request) {
			if (request.code) {
				$div.css('overflow', (request.overflow) ? 'auto' : 'hidden');
				//$('#' + divId).hideLoading();
				// Adiciona ao form o item
				//$('#' . divId).showLoading();
				//$div.showLoading();
				$.ajax({
					url: '/etc/etc011/grid/etc011GridPivot/',
					dataType: 'html',
					data: 'ajax=true&bi=visu&acaoId=&D132_Id=' + D132_Id + '&divId=' + tab + D132_Id + '&tab=' + encodeURIComponent(tab),
					success: function (data) {
						$div.html(data);
						var colunasRemovidas = [];
						$('#' + tab + D132_Id + ' thead tr').each(function (indexTR, elemTR) {
							$(elemTR).find("th").each(function (indexTH, elemTH) {
								if ($(elemTH).hasClass('hideDashBoard')) {
									$('#' + tab + D132_Id + ' div [valor="' + $(elemTH).attr('valor') + '"]').remove();
									$(elemTH).remove();
									if (indexTR == 1) {
										colunasRemovidas.push(indexTH);
									}
								}
							});
						});

						$('#' + tab + D132_Id + ' tbody tr').each(function (index, elem) {
							$(elem).find('td').each(function (i, e) {
								if ($.inArray(i, colunasRemovidas) != '-1') {
									$(e).remove();
								}
							});
						});

						//reloadUI('{$value[0]}');
						//corrigeConteudo('{$value[0]}');
						//corrigeGrid('{$value[0]}');
						$('#' + tab + D132_Id).data({
							'divId': tab + D132_Id,
							'divIdRoot': '',
							'divIdParent': '',
							'vars': '&bi=visu&acaoId=&D132_Id=' + D132_Id + '&tab=' + encodeURIComponent(tab),
							'href': '/etc/etc011/grid/etc011GridPivot/'
						}).addClass('gridPivot');
						// Antes existia um loading geral (no formDash.php) e aqui era removido o loading ao adicionar o último item
						// if(removeLoading == 'S'){
						// 	$div.hideLoading();
						// 	$('#' + divId).hideLoading();
						// }
						//$('#' + tab + D132_Id).hideLoading();

						// Chama o próximo
						if (D132_Ids.length > 0) {
							carregarItemDashboard(divId, D132_Ids, tab, removeLoading, D132_Tipos, left, top, width, height);
						}
						return false;
					}
				});
			} else {
				var $buttons = {
					"Fechar": function () {
						$(this).dialog("destroy").remove();
					}
				}
				dialogConfirm('Erro', request.data, $buttons);
			}
		});
	} else {
		var $div = $('#' + tab + D132_Id);
		// Indicador
		$.ajax({
			url: '/etc/etc011/ajax/visualizarIndicador/',
			dataType: 'html',
			data: 'ajax=true&bi=visu&acaoId=&D132_Id=' + D132_Id + '&tab=' + encodeURIComponent(tab),
			success: function (data) {
				$div.html(data);
				$('#' + tab + D132_Id).data({
					'divId': tab + D132_Id,
					'divIdRoot': '',
					'divIdParent': '',
					'vars': '&bi=visu&acaoId=&D132_Id=' + D132_Id + '&tab=' + encodeURIComponent(tab),
					'href': '/etc/etc011/ajax/visualizarIndicador/'
				});
				// Chama o próximo
				if (D132_Ids.length > 0) {
					carregarItemDashboard(divId, D132_Ids, tab, removeLoading, D132_Tipos, left, top, width, height);
				}
				return false;
			}
		});
	}

}
