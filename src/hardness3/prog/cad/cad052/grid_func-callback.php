<?php
namespace hardness;
	
function custoTotal($array){
	$valorUnit = $array['todosCampos']['D009_Valor_Custo_Unitario'];
	$quantidade = $array['todosCampos']['D074_Quantidade'];
	return $valorUnit * $quantidade;
}

function custoTotalProcessos($array){
	$leadTime = $array['todosCampos']['D075_Lead_Time'];
	$custoMaquina = $array['todosCampos']['D079_Custo_Minuto'];
	$custoFuncao = $array['todosCampos']['D078_Custo_Minuto'];
	$D075_Id = $array['todosCampos']['D075_Id'];
	//SQL do Custo Total dos Materiais de Consumo do Processo
	$sql = "SELECT SUM(D009_Valor_Custo_Unitario * D076_Quantidade)
					FROM D076
					LEFT JOIN D009 ON D009_Id = D076_D009_Id 
					WHERE D076_D075_Id = '{$D075_Id}'";
	$res = mysql_query($sql);
	$res = mysql_fetch_array($res);
	$custoMateriais = $res[0];
	return $leadTime * ($custoMaquina + $custoFuncao) + $custoMateriais;
}

function custoTotalMateriais($array){
	$D075_Id = $array['todosCampos']['D075_Id'];
	$sql = "SELECT SUM(D009_Valor_Custo_Unitario * D076_Quantidade)
					FROM D076
					LEFT JOIN D009 ON D009_Id = D076_D009_Id 
					WHERE D076_D075_Id = '{$D075_Id}'";
	$res = mysql_query($sql);
	$res = mysql_fetch_array($res);
	return $res[0];
}

function custoTotalMaterial($array){
	$quantidade = $array['todosCampos']['D076_Quantidade'];
	$custoUnitario = $array['todosCampos']['D009_Valor_Custo_Unitario'];
	return $quantidade * $custoUnitario;
}

function campoExclusaoItem($array) {
	global $g;
	$resultado=mysql_query("select D074_Id from D074 where D074_Id='{$array['todosCampos']['D074_Id']}'");
    $row=mysql_fetch_array($resultado);
    if(!empty($row['D074_Id'])){
		return <<<EOT
		<span onclick="cad052ExcluirItem('{$array['todosCampos']['D074_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	}
}

function campoExclusaoProcesso($array) {
	global $g;
	$resultado=mysql_query("SELECT * FROM D076 WHERE D076_D075_Id='{$array['todosCampos']['D075_Id']}'");
    if(mysql_num_rows($resultado) <= 0){
		return <<<EOT
		<span onclick="cad052ExcluirProcesso('{$array['todosCampos']['D075_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	} else {
		return '';
	}
}

function campoConsumiveis($array) {
	global $g;
	$D001_Id = $array['todosCampos']['D075_D001_Id'];
	$resultado=mysql_query("select D075_Id from D075 where D075_Id='{$array['todosCampos']['D075_Id']}'");
    $row=mysql_fetch_array($resultado);
    $novoIdTelaForm = uniqid();
    if(!empty($row['D075_Id'])){
		return <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Consumiveis', '/cad/cad052/content/cad052ContentConsumiveis/', '&D001_Id=' + encodeURIComponent('{$D001_Id}') + '&D075_Id=' + encodeURIComponent('{$row['D075_Id']}'), [800,500])"> 
			<span class="ui-icon ui-icon-written-document" title="Consumivel"></span>
		</span>
EOT;
	}
}
?>
