function campoInfo(elemId, campo, titulo, acaoId, place, width, divId, divIdRoot){
	if ($('#' + elemId).data('show')) {
		$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
	} else {
		$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);

		var contentWidth = parseFloat($('#' + elemId).closest('.ui-tabs-panel').css('width'));
		var contentHeight = parseFloat($('#' + elemId).closest('.ui-tabs-panel').css('height'));
		var popoverLeft = $('#' + elemId).position().left + parseFloat($('#' + elemId).parent().outerWidth());
		var popoverWidth = width;

		if((contentWidth - (popoverLeft+10)) < popoverWidth) {
			place = 'left';
		} else if((contentWidth - (popoverLeft+10)) > popoverWidth) {
			place = 'right';
		}

		$.ajax({
			url: '/sistema/ajax/campoInfo/dadosInfo/',
			dataType: 'html',
			data: 'ajax=true&divIdRoot='+ encodeURIComponent(divIdRoot) +'&divId=' + encodeURIComponent(divId) + '&acaoId=' + encodeURIComponent(acaoId) + '&campo=' + encodeURIComponent(campo),
			type: 'post',
			success: function(response) {
				$('#' + elemId).popover('destroy');
				$('#' + elemId).popover({
				    html: true,
				    placement: place,
				    title: titulo,
				    content: response
				});
				$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
				$('#' + elemId).data('show', true);
				$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px; color: #000 important;\'>close</span>').bind('click', function() {
					$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					$('body').unbind('click');
				}).appendTo($('.popover-title'));
				$('body').unbind('mousedown').bind('mousedown', function(event) {
					if (!$(event.target).closest('.popover').size()) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$('body').unbind('mousedown');
					}
				});
				$('#' + elemId).popover('show');
				var popover = $('.popover').filter(function() {
					return $(this).find('#demonstrativoCFOP').length;
				}).last();
				if (popover.length) {
					var tema = getComputedStyle(document.documentElement);
					var fundo = tema.getPropertyValue('--fundo-grid');
					var fundoTitulo = tema.getPropertyValue('--fundo-dialog-titlebar');
					var letra = tema.getPropertyValue('--letra-form');
					var letraTitulo = tema.getPropertyValue('--letra1');
					var borda = tema.getPropertyValue('--borda-content-cabecalho');
					popover.addClass('popover-demonstrativo-cfop').css({
						'background-color': fundo,
						'border-color': borda,
						'color': letra
					});
					popover.find('.popover-inner, .popover-content').css({ 'background-color': fundo, 'color': letra });
					popover.find('.popover-title').css({ 'background-color': fundoTitulo, 'color': letraTitulo });
				}
				$('#' + elemId).next().find('input:visible:last').focus();
				$('.popover').css('width', width);
				$('.popover').css('max-height', '450px');
				$('.popover-content').css({'max-height': '400px', 'overflow-y': 'auto'});
				$('#' + elemId).popover('show');

				if(width != 236 && place == 'left'){
					var popoverPosLeft = $('.popover').position().left;
					var posLeft = (popoverPosLeft - width) + (popoverLeft - popoverPosLeft) - parseFloat($('#' + elemId).parent().outerWidth() + 3);
					$('.popover').css('left', posLeft);
				}

				var popoverHeight = parseFloat($('.popover').css('height'));
				var popoverTop = $('.popover').position().top;
				var popoverTopRef = popoverTop;

				var arrowTop = $('.popover').find('.arrow').position().top;
				contentHeight = contentHeight+25;
				if((popoverHeight+popoverTop) > contentHeight){
					var newTop = popoverTop - ((popoverHeight+popoverTop)-contentHeight);
					$('.popover').css('top', newTop);
					$('.popover').find('.arrow').css('top', arrowTop+(popoverTopRef-newTop));
				}

				if(popoverTop < 0){
					$('.popover').find('.arrow').css('top', arrowTop+(popoverTop));
					$('.popover').css('top', 0);
				}
			}
		});
		setTimeout(function(){
			$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top);
		}, 300);
	}
}