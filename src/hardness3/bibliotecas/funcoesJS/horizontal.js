function horizontal(elem) {
	var divId = gridPegarDivId(elem);
	var trId = $(elem).closest('tr').attr('id');
	var janelaId = unique();
	abrirJanela(false, divIdRootAberto(), divId, janelaId, '#', 'Visualizar Item', '', '', [880,500]);
	setTimeout(function() {
		// copia
		var $janelaId = $('#' + janelaId);
		var $divId = $('#' + divId);
		var $table = $('<table></table>').addClass('grid');
		// loop thead
		$divId.find('.gridThead > div').each(function(index, value) {
			var $tr = $('<tr></tr>').attr('id', trId);
			var label = $(this).text();
			if (!isempty(label)) { label = label + ':' }
			$('<td></td>').css({ // .addClass('ui-widget-header ui-corner-all gridTheadTh')
				'padding': '3px 8px',
				'text-align': 'right'
			}).html(label).appendTo($tr);
			$tr.appendTo($table);
		});
		// loop tr
		$divId.find('#' + trId + ' > td').each(function(index, value) {
			if ($(this).find('.ui-icon-pencil').size()) {
				var backg = 'none repeat scroll 0 0 #FFFFFF';
			} else {
				var backg = 'none repeat scroll 0 0 #EDE7DE';
			}
			var $clone = $(this).clone().addClass('ui-corner-all').css({
				'text-align': 'left',
				'padding': '1px 3px',
				'width': '50%',
				'background': backg,
				'border': '1px solid #C0C0C0',
				'border-radius': '3px',
				'height': '16px',
				'font-size': '12px',
				'color': '#0A1C1D',
				'white-space': 'normal'
			}).appendTo($table.find('tr:eq(' + index + ')'));
			$clone.find('.ui-icon-pencil').hide();
		});
		// divida a tabela1 para tabela2
		var nrMetadeTrs = $table.find('tr').size() / 2;
		var count = 0;
		$table.find('tr').each(function(index, value) {
			if (index > nrMetadeTrs) {
				$(this).find('td').appendTo($table.find('tr:eq(' + count + ')'));
				count++;
			}
		});

		// Adiciona table na janela e faz uma limpeza
		var $content = $('<div></div>').attr('id', unique()).addClass('content').css({'width:': '100%', 'height': '100%', 'overflow': 'auto'});
		$table.appendTo($content);
		$content.appendTo($janelaId);
		$janelaId.scrollTop(1);
		$janelaId.scrollTop(0);
		$janelaId.parent().find('.dialog-auditoria').remove();
		$janelaId.parent().find('.dialog-refresh').remove();
		$janelaId.find('.ui-icon-zoomin').closest('tr').remove();
		$janelaId.find('.ui-icon-circle-close').closest('tr').remove();
	}, 100);
}