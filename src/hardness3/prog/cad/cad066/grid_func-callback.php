<?php
namespace hardness;
/**
 * CAD - cad064 - Cadastro de Veículos
 */

function campoTelefone($array){
	if(!empty($array['todosCampos']['D172_DDD_Telefone']) && !empty($array['todosCampos']['D172_Telefone'])){
			return "({$array['todosCampos']['D172_DDD_Telefone']}) {$array['todosCampos']['D172_Telefone']}";
	}else{
		return "&nbsp";
	}
}

function validaCampoStatus($array){
	if($array['todosCampos']['D172_Flag_Ativo']=='S'){
		return "ATIVO";
	}else{
		return "INATIVO";
	}
}

function validaCampoStatusApolice($array){
	if($array['todosCampos']['D173_Flag_Ativo']=='S'){
		return "ATIVO";
	}else if($array['todosCampos']['D173_Flag_Ativo']=='N'){
		return "INATIVO";
	}else{
		return "";
	}
}

function colunaAverbacoes($array){
	$retorno = '';
	$averbacoes = explode("<br>", $array['todosCampos']['averbacoes']);
	log("D173_Id: ".$array['todosCampos']['D173_Id']);
	foreach ($averbacoes as $averbacao) {
		$dados = explode("|", $averbacao);
		$nomeAverbacao = $dados[0].'-'.substr($dados[1], 0, 25);
		$retorno .= "<font color='#413a3a'>{$nomeAverbacao}</font><br>";
		log("nome averbacao: ".$nomeAverbacao);
	}
	$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Averbação', '/cad/cad066/content/cad066ContentFormCadastrarAverbacoes/', '&D173_Id=' + encodeURIComponent('{$array['todosCampos']['D173_Id']}'), [560,95]); return false;\"><font color='#413a3a'>INCLUIR</font></a>";
	return $retorno;
}

?>
