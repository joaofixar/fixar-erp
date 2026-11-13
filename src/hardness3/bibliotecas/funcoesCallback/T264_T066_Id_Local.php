<?php
namespace hardness;

function T264_T066_Id_Local($array) {
	global $g;
	$paramlocacaoPadraoProducao = $g['C031']['locacaoPadraoProducao'];

	if (empty($array['todosCampos']['T264_D009_Id'])) {
		return '-';
	}

	$mostrar = $array['todosCampos']['T264_T066_Id'].' - '.$array['todosCampos']['D004_Local'];
	$mostrar = empty($array['campoAtual']) ? ' - ' : $mostrar;

	if(!isset($g['c029Ids'][189])){
		return $mostrar;
	}

	$options = gGeraSelect('T066', 'T066_Id', 'T066_Id,D004_Local', "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$array['todosCampos']['T264_D009_Id']}' ORDER BY D004_Local", true, $array['todosCampos']['T264_T066_Id']);
	$options[] = array(
		'title' => 'optgroup',
		'value' => '- - - - - - - - - - - - - - - - -'
	);
	$options[] = array(
		'title' => 'ALTERAR LOCAÇÃO',
		'value' => 'AL'
	);

	$options = gProcessaSelect($options);
	$idselect = uniqid();
	$iddiv = uniqid();
	$locacao = <<<EOT
		<div id="{$iddiv}" onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
EOT;

	if (!empty($paramlocacaoPadraoProducao)) {
		$locacao = "<div id='{$iddiv}' $(this).hide();'><span style='padding-right: 16px;'>{$mostrar}</span></div>";
	}

	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT264Local(event, this, '{$array['todosCampos']['T264_Id']}', '{$array['todosCampos']['T264_D009_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	{$locacao}
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT264Local(event, select, T264_Id, D009_Id){
			var campo = $(select).val();
			if (campo == 'AL') {
				abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent(D009_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&T264_Id=' + encodeURIComponent(T264_Id), [700,350]);
				$('#{$idselect}').val(0);
				$('#{$idselect}').hide();
				$('#{$iddiv}').show();
			} else {
				$.getJSON('/est/est017/grid_func-ajax/atualizaT264Local/?ajax=true&T264_Id='+ encodeURIComponent(T264_Id) + '&T066_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}', true, false, false, T264_Id, 'T264_Id');
					}
				});	
			}
		}
	}</script></div>
EOT;
	return $result;
}
