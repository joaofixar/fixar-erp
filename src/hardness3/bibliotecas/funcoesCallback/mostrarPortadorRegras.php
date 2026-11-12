<?php
namespace hardness;

function mostrarPortadorRegras($array) {
	global $g;
	//Dados do combobox	
	$T217_Id = $array['todosCampos']['T217_Id'];
	$D031_Id = $array['todosCampos']['T217_D031_Id'];
	$D027_Id = $array['todosCampos']['T217_D027_Id'];
	$mostrar = $array['todosCampos']['D027_Portador'].$array['todosCampos']['D031_Portador'];

	$idselect = 'select' . uniqid() . '_' . $array['todosCampos']['T217_Id'];
	$iddiv = 'div' . uniqid() . '_' . $array['todosCampos']['T217_Id'];

	if($D031_Id > 0){
		$selected = 'D031-'.$D031_Id;
	} else {
		$selected = 'D027-'.$D027_Id;
	}

	$arrayD031 = gGeraSelect('D031', 'CONCAT("D031-",D031_Id)', 'D031_Portador', '', true, 'D031-'.$D031_Id);
	$groupOption = array(array('title' => 'optgroup', 'value' => 'PORTADOR PAGAR'));
	array_splice($arrayD031, 1, 0, $groupOption);

	$arrayD027 = gGeraSelect('D027', 'CONCAT("D027-",D027_Id)', 'D027_Portador', '', true, 'D027-'.$D027_Id);
	$groupOption = array(
						array('title' => 'optgroup', 'value' => ''),
						array('title' => 'optgroup', 'value' => 'PORTADOR RECEBER')
					);
	array_splice($arrayD027, 1, 0, $groupOption);
	unset($arrayD027[0]);
	
	$arrayPortador = array_merge($arrayD031, $arrayD027);
	foreach ($arrayPortador as $key => $campo) {
		if($campo['value'] == $selected){
			$arrayPortador[$key]['selected'] = true;
		}
	}

	$result = "<select id=\"{$idselect}\" onchange=\"atualizarPortadorRegras(this);\" T217_Id='{$T217_Id}' onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">";
	$result .= gProcessaSelect($arrayPortador);
	
	$result .= <<<EOT
		</select>
		<div id='{$iddiv}' onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span style="padding-right: 16px; cursor: text;">{$mostrar}</span></div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function atualizarPortadorRegras(select){
				var T217_Id = $(select).attr('T217_Id');
				var id = $(select).val().split('-');
				var acaoId = id[1];
				var tabela = id[0];
				$.getJSON('/fin/fin003/grid_func-ajax/atualizarPortadorRegras/?ajax=true&T217_Id='+ T217_Id + '&idPortador=' + acaoId + '&tabela=' + tabela + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}');
					}
				});	
			}
		}</script></div>
EOT;
	return $result;
}