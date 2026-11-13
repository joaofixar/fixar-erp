<?php
namespace hardness;

function mostrarPortador($array) {
	global $g;
	//Dados do combobox	
	$valorTransacao = $array['todosCampos']['T216_Valor'];
	$T216_Id = $array['todosCampos']['T216_Id'];
	$D031_Id = $array['todosCampos']['T216_D031_Id'];
	$D027_Id = $array['todosCampos']['T216_D027_Id'];
	$idselect = 'select' . uniqid() . '_' . $array['todosCampos']['T216_Id'];
	$iddiv = 'div' . uniqid() . '_' . $array['todosCampos']['T216_Id'];

	if($valorTransacao < 0){
		$mostrar = $array['todosCampos']['D031_Portador'];
		$result = "<select id=\"{$idselect}\" onchange=\"atualizarPortador(this);\" tabela='D031' T216_Id='{$T216_Id}' onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">";
		$result .= gProcessaSelect(gGeraSelect('D031', 'D031_Id', 'D031_Portador', '', true, $D031_Id));
	} else {
		$mostrar = $array['todosCampos']['D027_Portador'];
		$result = "<select id=\"{$idselect}\" onchange=\"atualizarPortador(this);\" tabela='D027' T216_Id='{$T216_Id}' onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">";
		$result .= gProcessaSelect(gGeraSelect('D027', 'D027_Id', 'D027_Portador', '', true, $D027_Id));
	}
	$result .= <<<EOT
		</select>
		<div id='{$iddiv}' onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span style="padding-right: 16px; cursor: text;">{$mostrar}</span></div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function atualizarPortador(select){
				var acaoId = $(select).val();
				var tabela = $(select).attr('tabela');
				var T216_Id = $(select).attr('T216_Id');
				$.getJSON('/fin/fin003/grid_func-ajax/atualizarPortador/?ajax=true&T216_Id='+ T216_Id + '&idPortador=' + acaoId + '&tabela=' + tabela + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}');
					}
				});	
			}
		}</script></div>
EOT;
	return $result;
}