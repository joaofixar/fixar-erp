function abrirNotificacao(elemId, titulo, url, acaoId, place, extra, width, center, divId, divIdParent, divIdRoot){
	if ($('#' + elemId).data('show')) {
		$(this).prev().popover('destroy').data('clicked', false).data('show', false);
	} else {
		$(this).prev().popover('destroy').data('clicked', false).data('show', false);

		var contentWidth = parseFloat($('#' + elemId).closest('.ui-tabs-panel').css('width'));
		var contentHeight = parseFloat($('#' + elemId).closest('.ui-tabs-panel').css('height'));
		var popoverLeft = $('#' + elemId).position().left + parseFloat($('#' + elemId).parent().outerWidth());
		var popoverWidth = width == 'auto' ? 500 : width;

		if((contentWidth - (popoverLeft+10)) < popoverWidth) {
			place = 'left';
		} else if((contentWidth - (popoverLeft+10)) > popoverWidth) {
			place = 'right';
		}
		$('#' + divId).showLoading();
		$.ajax({
			url: url,
			dataType: 'html',
			data: 'ajax=true&divIdRoot=' + encodeURIComponent(divIdRoot) + '&divIdParent=' + encodeURIComponent(divIdParent) + '&acaoId=' + encodeURIComponent(acaoId) + '&extra=' + encodeURIComponent(extra),
			type: 'post',
			success: function(response) {
				$('#' + divId).hideLoading();
				$('#' + elemId).popover('destroy');
				$('#' + elemId).popover({
				    html: true,
				    placement: place,
				    title: titulo,
				    content: response
				});
				if(center == 'N'){
					$(this).prev().popover('destroy').data('clicked', false).data('show', false);
					$('#' + elemId).popover('show');
					$('#' + elemId).data('show', true);
					$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
						$('#' + elemId).popover('destroy').data('clicked', false).data('show', false);
						$('body').unbind('click');
					}).appendTo($('.popover-title'));
					$('body').unbind('mousedown').bind('mousedown', function(event) {
						if (!$(event.target).closest('.popover').size()) {
							$('#' + elemId).popover('destroy').data('clicked', false).data('show', false);
							$('body').unbind('mousedown');
						}
					});
					$('#' + elemId).next().find('input:visible:last').focus();
					$('#' + elemId).popover('show');
					var preHeight = parseFloat($('.popover').css('height'));

					$('.popover').css('width', width);
					if(width != 236 && place == 'left'){
						var popoverPosLeft = $('.popover').position().left;
						var posLeft = (popoverPosLeft - width) + (popoverLeft - popoverPosLeft) - parseFloat($('#' + elemId).parent().outerWidth() + 3);
						$('.popover').css('left', posLeft);
					}

					var popoverHeight = parseFloat($('.popover').css('height'));
					var popoverTop = $('.popover').position().top;

					var arrowTop = $('.popover').find('.arrow').position().top;
					contentHeight = contentHeight+25;

					$('.popover').find('.arrow').css('top', arrowTop);

					if((popoverHeight+popoverTop) > contentHeight){
						var newTop = popoverTop - ((popoverHeight+popoverTop)-contentHeight);
						$('.popover').css('top', newTop);
						$('.popover').find('.arrow').css('top', arrowTop+(popoverTop-newTop));
					}

					if(popoverTop < 0){
						$('.popover').css('top', '3px');
						$('.popover').find('.arrow').css('top', $('#' + elemId).position().top+2);
					}

					setTimeout(function(){
						popoverHeight = parseFloat($('.popover').css('height'));
						if(preHeight != popoverHeight){
							if(((popoverHeight+popoverTop)-contentHeight) > 0){
								newTop = popoverTop - ((popoverHeight+popoverTop)-contentHeight);
							} else {
								newTop = popoverTop;
							}
							$('.popover').css('top', newTop);
							$('.popover').find('.arrow').css('top', arrowTop+(popoverTop-newTop));
						}
						popoverTop = $('.popover').position().top;
						if(popoverTop < 0){
							$('.popover').css('top', '3px');
							$('.popover').find('.arrow').css('top', $('#' + elemId).position().top+2);
						}

						var ajusteY = (place == 'left') ? 10 : -10;
						var x = $('.popover').offset().top-23; var y = $('.popover').offset().left + ajusteY;
						$('.popover').appendTo('body').css({'z-index': '10000', 'top': x+'px', 'left': y+'px'});
						$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top+23);

						setTimeout(function(){
							if(($('body').height() - $('.popover').position().top) < $('.popover').height()){
								var ajusteX = $('.popover').height() - ($('body').height() - $('.popover').position().top) + 10;
								$('.popover').css('top', $('.popover').position().top - ajusteX + 'px');
								$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top + ajusteX);
							}
						}, 50);
					}, 350);
				} else {
					setTimeout(function() {
						$('#' + elemId).popover('show');
						$('.popover').css('width', width);
						$('.popover').css('max-height', '650px');
						$('.popover-content').css({'max-height': '600px', 'overflow-y': 'auto'});
						$('#' + elemId).popover('show');
						$('#' + elemId).next().find('input:visible:last').focus();

						$('body').unbind('mousedown').bind('mousedown', function(event) { 
							if (!$(event.target).closest('.popover').size()) {
								$('#' + elemId).popover('destroy').data('clicked', false).data('show', false);
								$('body').unbind('mousedown');
							}
						});

						var popoverTop = $('.popover').offset().top;
						var popoverLeft = $('.popover').offset().left;
						$('.popover').css({'top': popoverTop, 'left': popoverLeft, 'position': 'fixed'});
						$('.popover').find('.arrow').remove();

						var ajusteY = (place == 'left') ? 10 : -10;
						var x = $('.popover').offset().top; var y = $('.popover').offset().left + ajusteY;
						$('.popover').appendTo('body').css({'z-index': '10000', 'top': x+'px', 'left': y+'px'});
					}, 200);
				}
			}
		});
	}
}