<?php
namespace hardness;
function validaCampoStatus($array){
	
	if($array['todosCampos']['T011_Status_Aprovacao'] == '2'){
		$sql = "select C007_Primeiro_Nome from C007 WHERE C007_Id='{$array['todosCampos']['T011_C007_Id_Aprovador']}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		$nomeUsuario = $linha['C007_Primeiro_Nome'];
		
		return "<font color='green'>Aprovado</font> por ".$nomeUsuario;
		
	}elseif($array['todosCampos']['T011_Status_Aprovacao'] == '3'){
		
		$sql = "select C007_Primeiro_Nome from C007 WHERE C007_Id='{$array['todosCampos']['T011_C007_Id_Aprovador']}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		$nomeUsuario = $linha['C007_Primeiro_Nome'];
		
		return "<font color='red'>Negado</font> por ".$nomeUsuario;
		
	}elseif($array['todosCampos']['T011_Status_Aprovacao'] == '1'){
		
		return "Pendente";
	}
}

function validaCampoPendente($array){
	global $g;
	$pendente = $array['todosCampos']['T012_Quantidade_Pendente'];
	if($array['todosCampos']['T012_Quantidade_Pendente'] != 0){
		$pendente = gCorrigeNumero($pendente);
		return "<font color='red'><b>".$pendente."</b></font>";
	} else {
		return $pendente = gCorrigeNumero($pendente);;
	}
}

function est006_Dia_Semana($array){
	global $g;
	$data=$array['todosCampos']['T190_Data_Vencimento'];
	return gPegaDiaSemana($data);
}

function T190_D031_Id_Portador($array) {
	return $array['todosCampos']['D031_Portador'];
}

function campoExclusao($array) {
	global $g;
	$status = $array['todosCampos']['T011_Status_Aprovacao'];
    if($status == '1') {
		return <<<EOT
			<a href="#" onclick="est006CampoExclusao('{$array['todosCampos']['T011_Id']}','{$g['divId']}')">
				<span class="ui-icon ui-icon-circle-close" title="Excluir ordem de compra"></span>
			</a>
EOT;
	} else {
		return "";
	}
}
?>
