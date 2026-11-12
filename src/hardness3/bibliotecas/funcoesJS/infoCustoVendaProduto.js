function infoCustoVendaProduto(elemId, url, extra, width, center) {
	if ($('#' + elemId).data('show')) {
		$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
	} else {
		$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);

		var place = 'left';

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
			url: url,
			dataType: 'html',
			data: 'ajax=true&extra=' + encodeURIComponent(extra),
			type: 'post',
			success: function(response) {
				$('#' + elemId).popover('destroy');
				$('#' + elemId).popover({
				    html: true,
				    placement: place,
				    title: 'Dados do produto',
				    content: response,
				    container: 'body'
				});
				$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
				$('#' + elemId).data('show', true);
				$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
					$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					$('body').unbind('click');
				}).appendTo($('.popover-title'));

				if(center == 'N'){
					setTimeout(function() {
						$('#' + elemId).popover('show');
						$('.popover').css('width', width);
						$('.popover').css('max-height', '450px');
						$('.popover-content').css({'max-height': '400px', 'overflow-y': 'auto'});
						$('#' + elemId).popover('show');
						$('#' + elemId).next().find('input:visible:last').focus();

						$('body').unbind('mousedown').bind('mousedown', function(event) {
							if (!$(event.target).closest('.popover').size()) {
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('body').unbind('mousedown');
							}
						});
					}, 200);

					setTimeout(function() {
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
						// console.log(popoverTop);
						if(popoverTop < 0){
							$('.popover').find('.arrow').css('top', arrowTop+(popoverTop));
							$('.popover').css('top', 0);
						}

						$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top);
					}, 210);
				} else {
					setTimeout(function() {
						$('#' + elemId).popover('show');
						$('.popover').css('width', width);
						$('.popover').css('max-height', '450px');
						$('.popover-content').css({'max-height': '400px', 'overflow-y': 'auto'});
						$('#' + elemId).popover('show');
						$('#' + elemId).next().find('input:visible:last').focus();

						$('body').unbind('mousedown').bind('mousedown', function(event) {
							if (!$(event.target).closest('.popover').size()) {
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('body').unbind('mousedown');
							}
						});

						var popoverTop = $('.popover').offset().top + 50;
						var popoverLeft = $('.popover').offset().left - 300;
						$('.popover').css({'top': popoverTop, 'left': popoverLeft, 'position': 'fixed'});
						$('.popover').find('.arrow').remove();
					}, 200);
				}
			}
		});
	}
}