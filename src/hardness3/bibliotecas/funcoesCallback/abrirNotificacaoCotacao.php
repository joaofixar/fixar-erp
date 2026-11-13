<?php
namespace hardness;

function abrirNotificacaoCotacao($array, $param){
	global $g;
	$campoAtual = explode('-', $array['campoAtual']);
	$acaoId = $campoAtual[0];
	$campoAtual = $campoAtual[1];

	$titulo = isset($param[0]) ? $param[0] : '';
	$url = isset($param[1]) ? $param[1] : '';
	$local = isset($param[2]) ? $param[2] : 'right';
	if (isset($param[3]) && $param[3]) {
		$campoAtual = gCorrigeNumero($campoAtual, $param[3]);
	}
	$width = (isset($param[4]) && $param[4]) ? $param[4] : '236'; //236 é o width default do popover

	if($acaoId <= 0){
		return $campoAtual;
	}

	$id = 'popover-' . uniqid();
	$onclick = <<<EOT
if ($('#{$id}').data('show')) {
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
} else {
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
	
	var place = '{$local}';
	var contentWidth = parseFloat($('#{$id}').closest('.ui-tabs-panel').css('width'));
	var contentHeight = parseFloat($('#{$id}').closest('.ui-tabs-panel').css('height'));
	var popoverLeft = $('#{$id}').position().left + parseFloat($('#{$id}').parent().outerWidth());
	var popoverWidth = '{$width}';
	
	if((contentWidth - (popoverLeft+10)) < popoverWidth) {
		place = 'left';
	} else if((contentWidth - (popoverLeft+10)) > popoverWidth) {
		place = 'right';
	}
	$.ajax({
		url: '{$url}',
		dataType: 'html',
		data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$acaoId}'),
		type: 'post',
		success: function(response) {
			$('#{$id}').popover('destroy');
			$('#{$id}').popover({
			    html: true,
			    placement: place,
			    title: '{$titulo}',
			    content: response
			});
			$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
			$('#{$id}').popover('show');
			$('#{$id}').data('show', true);
			$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
				$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
				$('body').unbind('click');
			}).appendTo($('.popover-title'));
			$('body').unbind('mousedown').bind('mousedown', function(event) { 
				if (!$(event.target).closest('.popover').size()) {
					$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					$('body').unbind('mousedown');
				}
			});
			$('#{$id}').next().find('input:visible:last').focus();
			$('#{$id}').popover('show');
			var preHeight = parseFloat($('.popover').css('height'));

			$('.popover').css('width', {$width});
			if('{$width}' != 236 && place == 'left'){
				var popoverPosLeft = $('.popover').position().left;
				var posLeft = (popoverPosLeft - '{$width}') + (popoverLeft - popoverPosLeft) - parseFloat($('#{$id}').parent().outerWidth() + 3);
				$('.popover').css('left', posLeft);
			}

			var popoverHeight = parseFloat($('.popover').css('height'));
			var popoverTop = $('.popover').position().top;
			var arrowTop = $('.popover').find('.arrow').position().top;
			contentHeight = contentHeight+25;
			if((popoverHeight+popoverTop) > contentHeight){
				var newTop = popoverTop - ((popoverHeight+popoverTop)-contentHeight);
				$('.popover').css('top', newTop);
				$('.popover').find('.arrow').css('top', arrowTop+(popoverTop-newTop));
			}
			
			setTimeout(function(){
				popoverHeight = parseFloat($('.popover').css('height'));
				if(preHeight != popoverHeight){
					if(((popoverHeight+popoverTop)-contentHeight) > 0){
						newTop = popoverTop - ((popoverHeight+popoverTop)-contentHeight);
						console.log('A');
						console.log(newTop);
					} else {
						console.log('B');
						console.log(newTop);
						newTop = popoverTop;
					}
					$('.popover').css('top', newTop);
					$('.popover').find('.arrow').css('top', arrowTop+(popoverTop-newTop));
				}
			}, 350);
		}
	});
}
EOT;
	$onclick = str_replace("\n", ' ', $onclick);
	$dadosCampoAtual = explode("|", $campoAtual);
	if($dadosCampoAtual[0] == 'S'){
		$campoAtual = "<font color='blue'>".gCorrigeNumero($dadosCampoAtual[1],5)."</font>";
	} else {
		$campoAtual = gCorrigeNumero($dadosCampoAtual[1],5);
	}
	/*if($array['todosCampos']['T004_T219_Id'] == $acaoId || $array['todosCampos']['T006_T219_Id'] == $acaoId){
		$campoAtual = "<span style='background-color:#BADCFC; font-weight: bold; color: blue;'>{$campoAtual}</span>";
	}*/
	return <<<EOT
<a id="{$id}" href="#" onclick="{$onclick}">{$campoAtual}</a>
EOT;
}