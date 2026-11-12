<?php
namespace hardness;

function T008_T066_Id_Local($array) {
	global $g;
	
	if (empty($array['todosCampos']['T008_D009_Id']) || ($array['todosCampos']['D006_Flag_Estoque'] != 'C' && $array['todosCampos']['D006_Flag_Estoque'] != 'D')) {
		return '-';
	}
	
	$mostrar = $array['todosCampos']['D004_Local'];
	$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;
	$options = gGeraSelect('T066', 
						   'T066_Id', 
						   'D004_Local,FORMAT(T066_Quantidade_Estoque_Liquido,4)', 
						   "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$array['todosCampos']['T008_D009_Id']}' ORDER BY D004_Local", true, $array['todosCampos']['T008_T066_Id'], 'LOCAÇÃO: %1 [ESTQ: %2]');
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
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT008Local(event, this, '{$array['todosCampos']['T008_Id']}', '{$array['todosCampos']['T008_D009_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	<div id="{$iddiv}" onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT008Local(event, select, T008_Id, D009_Id){
			var campo = $(select).val();
			if (campo == 'AL') {
				abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent(D009_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&T008_Id=' + encodeURIComponent(T008_Id), [700,350]);
				$('#{$idselect}').val(0);
				$('#{$idselect}').hide();
				$('#{$iddiv}').show();
			} else {
				$.getJSON('/est/est004/grid_func-ajax/atualizaT008Local/?ajax=true&T008_Id='+ encodeURIComponent(T008_Id) + '&T066_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id');
					}
				});	
			}
		}
	}</script></div>
EOT;
	return $result;
}
