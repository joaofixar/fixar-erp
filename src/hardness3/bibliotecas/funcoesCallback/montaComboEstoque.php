<?php
namespace hardness;

function montaComboEstoque($array){
	global $g;
	//Dados do combobox	
	$C004_Id_Estoque = $array['todosCampos']['T006_C004_Id_Estoque'];
	$estoqueLiquido = gCorrigeNumero($array['todosCampos']['T006_Quantidade_Estoque']);

	if($C004_Id_Estoque <= 0 || empty($C004_Id_Estoque)) {
			$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	
	$select[] = "<option value=\"\" {$selected}>...</option>";
	$D009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, C004_Nome_Abreviado, C004_Id FROM D009 LEFT JOIN C004 ON C004_Id=D009_C004_Id WHERE D009_D049_Id = '{$array['todosCampos']['D049_Id']}' AND D009_C004_Id != '{$g['empresaAtual']}'");
	while($row = mysql_fetch_array($D009)){
		$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$C004_Id = str_pad($row['C004_Id'], 3, '0', STR_PAD_LEFT);
		if($C004_Id_Estoque == $row['C004_Id']) {
			$selected = 'selected="selected"';
			$mostrar = "{$row['D009_Quantidade_Estoque_Liquido']} - {$C004_Id}";
		} else {
			$selected = '';
		}
		$select[] = "<option value=\"{$row['C004_Id']}\" {$selected}>{$row['D009_Quantidade_Estoque_Liquido']} - {$C004_Id}</option>";
	}

	//Dados do return
	$idselect = 'select' . uniqid() . '_' . $array['todosCampos']['T006_Id'];
	$iddiv = 'div' . uniqid() . '_' . $array['todosCampos']['T006_Id'];
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaEstoqueT006(this);\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
		</select>
		<div id='{$iddiv}' onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span style="padding-right: 16px; cursor: text;">{$mostrar}</span></div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function atualizaEstoqueT006(select){
				var acaoId = $(select).val();
				var T006_Id = $(select).attr('id');
				T006_Id = T006_Id.split('_');
				T006_Id = T006_Id[1];
				$.getJSON('/ven/ven002/grid_func-ajax/atualizaEstoqueT006/?ajax=true&T006_Id='+ encodeURIComponent(T006_Id) + '&campoId=' + acaoId + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}');
					}
				});	
			}
		}</script></div>
EOT;
	return $result;
}