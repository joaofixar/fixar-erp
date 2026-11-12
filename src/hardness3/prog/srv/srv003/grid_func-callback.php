<?php
namespace hardness;

function validaCampoPrioridade($array){
	if($array['todosCampos']['T147_Prioridade'] == '99999'){
		return "-";
	}else{
		return "<font color='red'><b>".$array['todosCampos']['T147_Prioridade']."<b/></font>";
	}
}

function validaCampoStatus($array){
	if($array['todosCampos']['T147_Pausado']=="S"){
		return "<font color='red'><b>PAUSADO</b></font>";
	}elseif($array['todosCampos']['T147_Status']=="E"){
		return "<font color='green'><b>EXECUÇÃO</b></font>";
	}else if($array['todosCampos']['T147_Status']=="P"){
		return "<b>PENDENTE</b>";
	}
}

function validaCampoVeiculoSrv003($array){
	return $array['todosCampos']['D116_Placa']." - ".substr($array['todosCampos']['D116_Modelo'], 0, 10)." - ".$array['todosCampos']['D116_Cor'];
}

function validaCampoCliente($array){
	return substr($array['todosCampos']['D024_Nome_Empresa'], 0, 20);
}
function validaCampoProduto($array){
	$sql = "SELECT D001_Descricao_Produto FROM T006 
	LEFT JOIN D009 ON D009_Id=T006_D009_Id 
	LEFT JOIN D049 ON D049_Id=D009_D049_Id 
	LEFT JOIN D001 ON D001_Id=D049_D001_Id 
	WHERE T006_Id='{$array['todosCampos']['T147_T006_Id']}'";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	return $linha['D001_Descricao_Produto'];
}
?>