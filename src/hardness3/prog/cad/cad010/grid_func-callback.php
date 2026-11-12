<?php
namespace hardness;
/**
 * CAD - cad010 - Cadastro de Bens
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad010CampoExclusao('{$array['todosCampos']['D109_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function campoExclusaoCompetencia($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad010CampoExclusaoCompetencia('{$array['todosCampos']['D115_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
function campoAbrirDocumento($array){
			$id = $array['todosCampos']['D123_Id'];
			$retorno = "<a href=\"/cad/cad010/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
	}
function campoTelefone($array){
	if(!empty($array['todosCampos']['D109_DDD_Telefone']) && !empty($array['todosCampos']['D109_Telefone'])){
			return "({$array['todosCampos']['D109_DDD_Telefone']}) {$array['todosCampos']['D109_Telefone']}";
	}else{
		return "&nbsp";
	}
}

function validaCampoStatus($array){
	if($array['todosCampos']['D109_Flag_Ativo']=="S"){
		return "Ativo";
	}else{
		return "Inativo";
	}
}

function validaCampoCompetencia($array){
	global $g;
	
	$sql = "SELECT * FROM D115 left join D110 on D110_Id=D115_D110_Id WHERE D115_D109_Id='{$array['todosCampos']['D109_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";
	
	while($linha = mysql_fetch_array($result)){
		//$retorno .= substr($linha['D110_Titulo'], 0, 7).", ";
		$retorno .= $linha['D110_Titulo']. " - ";
	}
	return $retorno;
	
}

function impDigital($array){
	global $g;
	
	return '<a href="#" onclick="cad010CadImpDigital(\''.$array['todosCampos']['D109_Id'].'\'); return false;">Cadastrar Digital</a>';
}
?>
