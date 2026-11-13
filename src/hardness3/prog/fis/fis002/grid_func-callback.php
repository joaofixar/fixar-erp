<?php
namespace hardness;

// Imagem para exclusão da linha
function campoEstoque($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Estoque']=='D'){
		return "Debita";
	} if ($array['todosCampos']['D006_Flag_Estoque']=='C'){
    	return "Credita";
	}elseif($array['todosCampos']['D006_Flag_Estoque']=='N'){
		return "Não Altera Estoque";
	}
}
function campoFinanceiro($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Gera_Contas']=='R'){
		return "Receber";
	} if ($array['todosCampos']['D006_Flag_Gera_Contas']=='P'){
    	return "Pagar";
	}elseif($array['todosCampos']['D006_Flag_Gera_Contas']=='N'){
		return "Não Gera Contas";
	}
}
function campoICMS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_ICMS']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_ICMS']=='N'){
		return "Não Isento";
	}
}
function campoIPI($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_IPI']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_IPI']=='N'){
		return "Não Isento";
	}
}
function campoPIS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_PIS']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_PIS']=='N'){
		return "Não Isento";
	}
}
function campoCOFINS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_COFINS']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_COFINS']=='N'){
		return "Não Isento";
	}
}

function campoRegimeTributario($array) {
	global $g;
	if ($array['todosCampos']['D143_C004_Codigo_Regime_Tributario']=='1'){
		return "Simples Nacional";
	}elseif($array['todosCampos']['D143_C004_Codigo_Regime_Tributario']=='2'){
		return "Simples Nacional é excesso de sublimite de receita bruta";
	}elseif($array['todosCampos']['D143_C004_Codigo_Regime_Tributario']=='3'){
		return "Regime Normal";
	}
}

function campoTipoContribuinte($array) {
	global $g;
	if ($array['todosCampos']['D143_D024_Flag_Contribuinte']=='9'){
		return "Não contribuinte";
	}elseif($array['todosCampos']['D143_D024_Flag_Contribuinte']=='1'){
		return "Consumidor Final";
	}elseif($array['todosCampos']['D143_D024_Flag_Contribuinte']=='2'){
		return "Revenda";
	}elseif($array['todosCampos']['D143_D024_Flag_Contribuinte']=='3'){
		return "Produtor rural";
	}elseif($array['todosCampos']['D143_D024_Flag_Contribuinte']=='4'){
		return "Simples";
	}elseif($array['todosCampos']['D143_D024_Flag_Contribuinte']=='5'){
		return "Industria";
	}elseif($array['todosCampos']['D143_D024_Flag_Contribuinte']=='8'){
		return "Industria Triangular";
	}
}

function campoDentroForaEstado($array){
	global $g;
	if ($array['todosCampos']['D143_Dentro_Fora_Estado']=='D'){
		return "Dentro";
	}elseif($array['todosCampos']['D143_Dentro_Fora_Estado']=='F'){
		return "Fora";
	}	
}

function campoUFTemST($array){
	global $g;
	if ($array['todosCampos']['D143_UF_Tem_ST']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D143_UF_Tem_ST']=='N'){
		return "Não";
	}	
}

function campoNCMTemST($array){
	global $g;
	if ($array['todosCampos']['D143_NCM_Tem_ST']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D143_NCM_Tem_ST']=='N'){
		return "Não";
	}	
}

function campoPessoa($array){
	global $g;
	if ($array['todosCampos']['D143_Pessoa_Fisica_Juridica']=='PF'){
		return "Física";
	}elseif($array['todosCampos']['D143_Pessoa_Fisica_Juridica']=='PJ'){
        return "Jurídica";
    }elseif($array['todosCampos']['D143_Pessoa_Fisica_Juridica']=='PI'){
        return "Internacional";
    }	
}

function campoTipoRegra($array){
	global $g;
	if ($array['todosCampos']['D143_Flag_Cabecalho_Item']=='C'){
		return "Cabeçalho";
	}elseif($array['todosCampos']['D143_Flag_Cabecalho_Item']=='I'){
		return "Item";
	}		
}

function campoTipo($array){
	global $g;
	if ($array['todosCampos']['D143_Produzido_Adquirido']=='P'){
		return "Produzido/Importado";
	}elseif($array['todosCampos']['D143_Produzido_Adquirido']=='A'){
		return "Aquisição Nacional";
	}		
}

function campoAtiva($array){
	global $g;
	if ($array['todosCampos']['D143_Flag_Inativar']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D143_Flag_Inativar']=='N'){
		return "Não";
	}	
}
?>