<?php
namespace hardness;

function camposAtualizadosTabFornecedor($array, $param=false){
	global $g;
	$arrayCampos = array(
		'campoNCM' => 'NCM',
		'campoOrigemProduto' => 'Origem Produto',
		'campoIPI' => 'Alíquota IPI',
		'campoICMS' => 'Alíquota ICMS',
		'campoICMSST' => 'Alíquota ST',
		'campoCFOP' => 'CFOP',
		'campoNossoCodigo' => 'Nosso Código',
	);
	$exibir = '<table border=0 cellspacing=0 cellpadding=0 border-spacing=0 width=90%>';
	$campoAtual = unserialize($array['campoAtual']);
	foreach ($campoAtual as $campo => $valor) {
		if(strpos($campo, "campo") === false) { continue; }
		$exibir .= "<tr><td style=\'padding: 1px 2px;\'>".$arrayCampos[$campo]." </td><td style=\'padding: 1px 2px;\'> ".(($valor == "true") ? "SIM" : "NÃO") ."</td></tr>";
	}
	$exibir .= '</table><br>';
	$exibir .= $campoAtual['usuario']."<br>";
	$exibir .= gCorrigeData($campoAtual['dataHora'])."<br>";

	$width = "200";
	$id = 'popover-' . uniqid();
	$onclick = <<<EOT
if ($('#{$id}').data('show')) {
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
} else {
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);

	var place = 'left';

	var contentWidth = parseFloat($('#{$id}').closest('.ui-tabs-panel').css('width'));
	var contentHeight = parseFloat($('#{$id}').closest('.ui-tabs-panel').css('height'));
	var popoverLeft = $('#{$id}').position().left + parseFloat($('#{$id}').parent().outerWidth());
	var popoverWidth = '{$width}';
	
	if((contentWidth - (popoverLeft+10)) < popoverWidth) {
		place = 'left';
	} else if((contentWidth - (popoverLeft+10)) > popoverWidth) {
		place = 'right';
	}

	$('#{$id}').popover('destroy');
	$('#{$id}').popover({
	    html: true,
	    placement: place,
	    title: 'Campos Atualizados',
	    content: '{$exibir}',
	    container: 'body'
	});
	$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
	$('#{$id}').data('show', true);
	$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
		$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
		$('body').unbind('click');
	}).appendTo($('.popover-title'));

	setTimeout(function() {
		$('#{$id}').popover('show');
		$('.popover').css('width', {$width});
		$('.popover').css('max-height', '450px');
		$('.popover-content').css({'max-height': '400px', 'overflow-y': 'auto'});
		$('#{$id}').popover('show');
		$('#{$id}').next().find('input:visible:last').focus();

		$('body').unbind('mousedown').bind('mousedown', function(event) { 
			if (!$(event.target).closest('.popover').size()) {
				$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
				$('body').unbind('mousedown');
			}
		});
	}, 200);

	setTimeout(function() {
		if('{$width}' != 236 && place == 'left'){
			var popoverPosLeft = $('.popover').position().left;
			var posLeft = (popoverPosLeft - '{$width}') + (popoverLeft - popoverPosLeft) - parseFloat($('#{$id}').parent().outerWidth() + 3);
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
		console.log(popoverTop);
		if(popoverTop < 0){
			$('.popover').find('.arrow').css('top', arrowTop+(popoverTop));
			$('.popover').css('top', 0);
		}

		$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top);
	}, 210);
}
EOT;
	$onclick = str_replace("\n", ' ', $onclick);
	$onclick = str_replace("\r", ' ', $onclick);
	return <<<EOT
		<div id="{$id}" onclick="{$onclick}" class="icone ui-state-default ui-corner-all">
			<span class="ui-icon ui-icon-help"></span>
		</div>
EOT;
}