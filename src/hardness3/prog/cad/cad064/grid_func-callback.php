<?php
namespace hardness;
/**
 * CAD - cad064 - Cadastro de Veículos
 */

function validaCampoCategoria($array){
	if($array['todosCampos']['D170_Categoria']==1){
		return "REBOQUE";
	}else{
		return "TRAÇÃO";
	}
}

function validaCampoTipoRodado($array){
	if($array['todosCampos']['D170_Flag_Rodado']==01){
		return "TRUCK";
	}else if($array['todosCampos']['D170_Flag_Rodado']==02){
		return "TOCO";
	}else if($array['todosCampos']['D170_Flag_Rodado']==03){
		return "CAVALO MECÂNICO";
	}else if($array['todosCampos']['D170_Flag_Rodado']==04){
		return "VAN";
	}else if($array['todosCampos']['D170_Flag_Rodado']==05){
		return "UTILITÁRIO";
	}else {
		return "OUTROS";
	}
}

function validaCampoCarroceria($array){
	if($array['todosCampos']['D170_Flag_Carroceria']==00){
		return "NÃO APLICÁVEL";
	}else if($array['todosCampos']['D170_Flag_Carroceria']==01){
		return "ABERTA";
	}else if($array['todosCampos']['D170_Flag_Carroceria']==02){
		return "FECHADA/BAÚ";
	}else if($array['todosCampos']['D170_Flag_Carroceria']==03){
		return "GRANELEIRA";
	}else if($array['todosCampos']['D170_Flag_Carroceria']==04){
		return "PORTA-CONTEINER";
	}else {
		return "SIDER";
	}
}

function validaCampoStatus($array){
	if($array['todosCampos']['D170_Flag_Ativo']=='S'){
		return "ATIVO";
	}else{
		return "INATIVO";
	}
}

?>
