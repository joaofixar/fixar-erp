<?php
namespace hardness;
function validaCampoFinalizado($array){
	
	if ($array['todosCampos']['T047_Flag_Status']=='A'){
         return "<span class=\"ui-icon ui-icon-circle-check\"></span>";
    }
}
function validaCampoExlcluirSubItem($array){
	global $g;
	if($array['todosCampos']['T047_Flag_Status']!='A'){
		return gExcluirLinha($array, array('T071_Id', $g['divId']));
	}
}
function validaCampoExcluirItens($array){
	global $g;
	if($array['todosCampos']['T047_Flag_Status']!='A'){
		return gExcluirLinha($array, array('T048_Id', $g['divId']));
	}
}

function campoTipoMontagem($array){
    if($array['todosCampos']['T048_Flag_Tipo_Montagem']=='M'){
        return "MONTAGEM";
    }elseif($array['todosCampos']['T048_Flag_Tipo_Montagem']=='D'){
        return "DESMONTAGEM";
    }
}

function T048_T066_Id_Local($array) {
	global $g;
	
	if (empty($array['todosCampos']['D009_Id'])) {
		return '-';
	}

	if ($array['todosCampos']['T047_Flag_Status']=='A'){
		return $array['todosCampos']['T048_T066_Id'].'-'.$array['todosCampos']['D004_Local'];
	}
	
	$mostrar = $array['todosCampos']['T048_T066_Id'].'-'.$array['todosCampos']['D004_Local'];
	$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;

	$options = gGeraSelect('T066', 'T066_Id', 'D004_Local,format(T066_Quantidade_Estoque,0),if(T066A_Data_Validade = "0000-00-00","", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', "LEFT JOIN D004 ON T066_D004_Id = D004_Id LEFT JOIN T066A ON T066_Id = T066A_T066_Id WHERE T066_D009_Id = '{$array['todosCampos']['D009_Id']}' ORDER BY IF(T066A_Data_Validade = '0000-00-00','9999-99-99', T066A_Data_Validade) ASC, T066_Quantidade_Estoque DESC, T066_Id ASC", true, $array['todosCampos']['T071_T066_Id'], "%1 (QTD:%2) %3");
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
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT048Local(event, this, '{$array['todosCampos']['T048_Id']}', '{$array['todosCampos']['D009_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	<div id="{$iddiv}" onclick="$('#{$idselect}').show().focus(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT048Local(event, select, T048_Id, D009_Id){
			var campo = $(select).val();
			if (campo == 'AL') {
				abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent(D009_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&T048_Id=' + encodeURIComponent(T048_Id), [700,350]);
				$('#{$idselect}').val(0);
				$('#{$idselect}').hide();
				$('#{$iddiv}').show();
			} else {
				$.getJSON('/est/est007/grid_func-ajax/atualizaT048Local/?ajax=true&T048_Id='+ encodeURIComponent(T048_Id) + '&T066_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}', true, false, false, T048_Id, 'T048_Id');
					}
				});	
			}
		}
	}</script></div>
EOT;
	return $result;
}

function T071_T066_Id_Local($array) {
	global $g;
	
	if (empty($array['todosCampos']['D009_Id'])) {
		return '-';
	}

	if ($array['todosCampos']['T047_Flag_Status']=='A'){
		return $array['todosCampos']['T071_T066_Id'].'-'.$array['todosCampos']['D004_Local'];
	}
	
	$mostrar = $array['todosCampos']['T071_T066_Id'].'-'.$array['todosCampos']['D004_Local'];
	$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;

	$options = gGeraSelect('T066', 'T066_Id', 'D004_Local,format(T066_Quantidade_Estoque,0),if(T066A_Data_Validade = "0000-00-00","", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', "LEFT JOIN D004 ON T066_D004_Id = D004_Id LEFT JOIN T066A ON T066_Id = T066A_T066_Id WHERE T066_D009_Id = '{$array['todosCampos']['D009_Id']}' ORDER BY IF(T066A_Data_Validade = '0000-00-00','9999-99-99', T066A_Data_Validade) ASC, T066_Quantidade_Estoque DESC, T066_Id ASC", true, $array['todosCampos']['T071_T066_Id'], "%1 (QTD:%2) %3");
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
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT071Local(event, this, '{$array['todosCampos']['T071_Id']}', '{$array['todosCampos']['D009_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	<div id="{$iddiv}" onclick="$('#{$idselect}').show().focus(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT071Local(event, select, T071_Id, D009_Id){
			var campo = $(select).val();
			if (campo == 'AL') {
				abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent(D009_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&T071_Id=' + encodeURIComponent(T071_Id), [700,350]);
				$('#{$idselect}').val(0);
				$('#{$idselect}').hide();
				$('#{$iddiv}').show();
			} else {
				$.getJSON('/est/est007/grid_func-ajax/atualizaT071Local/?ajax=true&T071_Id='+ encodeURIComponent(T071_Id) + '&T066_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}', true, false, false, T071_Id, 'T071_Id');
					}
				});	
			}
		}
	}</script></div>
EOT;
	return $result;
}

?>