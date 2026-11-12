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
		return "Não Altera";
	}
}
function campoFinanceiro($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Gera_Contas']=='R'){
		return "Receber";
	} if ($array['todosCampos']['D006_Flag_Gera_Contas']=='P'){
    	return "Pagar";
	}elseif($array['todosCampos']['D006_Flag_Gera_Contas']=='N'){
		return "Não Gera";
	}
}
function campoICMS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_ICMS']=='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_Isento_ICMS']=='N'){
		return "SIM";
	}
}
function campoST($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Substituicao_Tributaria']!='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_Substituicao_Tributaria']=='S'){
		return "SIM";
	}
}
function campoMMF($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_MMF']!='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_MMF']=='S'){
		return "SIM";
	}
}
function campoAtualizaCusto($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Atualiza_Custo']!='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_Atualiza_Custo']=='S'){
		return "SIM";
	}
}
function campoIPI($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_IPI']=='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_Isento_IPI']=='N'){
		return "SIM";
	}
}
function campoPIS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_PIS']=='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_Isento_PIS']=='N'){
		return "SIM";
	}
}
function baseCalculoPIS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Forma_Calculo_PIS']=='V'){
		return "VENDA";
	}elseif($array['todosCampos']['D006_Flag_Forma_Calculo_PIS']=='L'){
		return "LUCRO";
	}
}
function baseCalculoCOFINS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Forma_Calculo_COFINS']=='V'){
		return "VENDA";
	}elseif($array['todosCampos']['D006_Flag_Forma_Calculo_COFINS']=='L'){
		return "LUCRO";
	}
}
function campoCOFINS($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_COFINS']=='S'){
		return "-";
	}elseif($array['todosCampos']['D006_Flag_Isento_COFINS']=='N'){
		return "SIM";
	}
}

function campoRegimeTributario($array) {
	global $g;
	if ($array['todosCampos']['D137_C004_Codigo_Regime_Tributario']=='1'){
		return "Simples Nacional";
	}elseif($array['todosCampos']['D137_C004_Codigo_Regime_Tributario']=='2'){
		return "Simples Nacional é excesso de sublimite de receita bruta";
	}elseif($array['todosCampos']['D137_C004_Codigo_Regime_Tributario']=='3'){
		return "Regime Normal";
	}
}

function campoTipoContribuinte($array) {
	global $g;
	if ($array['todosCampos']['D137_D024_Flag_Contribuinte']=='9'){
		return "Não contribuinte";
	}elseif($array['todosCampos']['D137_D024_Flag_Contribuinte']=='1'){
		return "Consumidor Final";
	}elseif($array['todosCampos']['D137_D024_Flag_Contribuinte']=='2'){
		return "Revenda";
	}elseif($array['todosCampos']['D137_D024_Flag_Contribuinte']=='3'){
		return "Produtor rural";
	}elseif($array['todosCampos']['D137_D024_Flag_Contribuinte']=='4'){
		return "Simples";
	}elseif($array['todosCampos']['D137_D024_Flag_Contribuinte']=='5'){
		return "Industria";
	}elseif($array['todosCampos']['D137_D024_Flag_Contribuinte']=='8'){
		return "Industria Triangular";
	}
}

function campoDentroForaEstado($array){
	global $g;
	if ($array['todosCampos']['D137_Dentro_Fora_Estado']=='D'){
		return "Dentro";
	}elseif($array['todosCampos']['D137_Dentro_Fora_Estado']=='F'){
		return "Fora";
	}	
}

function campoUFTemST($array){
	global $g;
	if ($array['todosCampos']['D137_UF_Tem_ST']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D137_UF_Tem_ST']=='N'){
		return "Não";
	}	
}

function campoNCMTemST($array){
	global $g;
	if ($array['todosCampos']['D137_NCM_Tem_ST']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D137_NCM_Tem_ST']!='S'){
		return "Não";
	}	
}

function campoPessoa($array){
	global $g;
	if ($array['todosCampos']['D137_Pessoa_Fisica_Juridica']=='PF'){
		return "Física";
	}elseif($array['todosCampos']['D137_Pessoa_Fisica_Juridica']=='PJ'){
        return "Jurídica";
    }elseif($array['todosCampos']['D137_Pessoa_Fisica_Juridica']=='PI'){
        return "Internacional";
    }	
}

function campoTipoRegra($array){
	global $g;
	if ($array['todosCampos']['D137_Flag_Cabecalho_Item']=='C'){
		return "Cabeçalho";
	}elseif($array['todosCampos']['D137_Flag_Cabecalho_Item']=='I'){
		return "Item";
	}		
}

function campoTipo($array){
	global $g;
	if ($array['todosCampos']['D137_Produzido_Adquirido']=='P'){
		return "Produzido/Importado";
	}elseif($array['todosCampos']['D137_Produzido_Adquirido']=='A'){
		return "Aquisição Nacional";
	}		
}

function campoAtiva($array){
	global $g;
	if ($array['todosCampos']['D137_Flag_Inativar']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D137_Flag_Inativar']=='N'){
		return "Não";
	}	
}


function campoEstoqueCompras($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Estoque']=='D'){
		return "Debita";
	} if ($array['todosCampos']['D006_Flag_Estoque']=='C'){
    	return "Credita";
	}elseif($array['todosCampos']['D006_Flag_Estoque']=='N'){
		return "Não altera estoque";
	}
}
function campoFinanceiroCompras($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Gera_Contas']=='R'){
		return "Receber";
	} if ($array['todosCampos']['D006_Flag_Gera_Contas']=='P'){
    	return "Pagar";
	}elseif($array['todosCampos']['D006_Flag_Gera_Contas']=='N'){
		return "Não Gera Contas";
	}
}
function campoICMSCompras($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_ICMS']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_ICMS']=='N'){
		return "Não Isento";
	}
}
function campoIPICompras($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_IPI']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_IPI']=='N'){
		return "Não Isento";
	}
}
function campoPISCompras($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_PIS']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_PIS']=='N'){
		return "Não Isento";
	}
}
function campoCOFINSCompras($array) {
	global $g;
	if ($array['todosCampos']['D006_Flag_Isento_COFINS']=='S'){
		return "Isento";
	}elseif($array['todosCampos']['D006_Flag_Isento_COFINS']=='N'){
		return "Não Isento";
	}
}

function campoRegimeTributarioCompras($array) {
	global $g;
	if ($array['todosCampos']['D145_C004_Codigo_Regime_Tributario']=='1'){
		return "Simples Nacional";
	}elseif($array['todosCampos']['D145_C004_Codigo_Regime_Tributario']=='2'){
		return "Simples Nacional é excesso de sublimite de receita bruta";
	}elseif($array['todosCampos']['D145_C004_Codigo_Regime_Tributario']=='3'){
		return "Regime Normal";
	}
}

function campoTipoContribuinteCompras($array) {
	global $g;
	if ($array['todosCampos']['D145_D024_Flag_Contribuinte']=='9'){
		return "Não contribuinte";
	}elseif($array['todosCampos']['D145_D024_Flag_Contribuinte']=='1'){
		return "Consumidor Final";
	}elseif($array['todosCampos']['D145_D024_Flag_Contribuinte']=='2'){
		return "Revenda";
	}elseif($array['todosCampos']['D145_D024_Flag_Contribuinte']=='3'){
		return "Produtor rural";
	}elseif($array['todosCampos']['D145_D024_Flag_Contribuinte']=='4'){
		return "Simples";
	}elseif($array['todosCampos']['D145_D024_Flag_Contribuinte']=='5'){
		return "Industria";
	}elseif($array['todosCampos']['D145_D024_Flag_Contribuinte']=='8'){
		return "Industria Triangular";
	}
}

function campoDentroForaEstadoCompras($array){
	global $g;
	if ($array['todosCampos']['D145_Dentro_Fora_Estado']=='D'){
		return "Dentro";
	}elseif($array['todosCampos']['D145_Dentro_Fora_Estado']=='F'){
		return "Fora";
	}	
}

function campoUFTemSTCompras($array){
	global $g;
	if ($array['todosCampos']['D145_UF_Tem_ST']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D145_UF_Tem_ST']=='N'){
		return "Não";
	}	
}

function campoNCMTemSTCompras($array){
	global $g;
	if ($array['todosCampos']['D145_NCM_Tem_ST']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D145_NCM_Tem_ST']=='N'){
		return "Não";
	}	
}

function campoPessoaCompras($array){
	global $g;
	if ($array['todosCampos']['D145_Pessoa_Fisica_Juridica']=='PF'){
		return "Física";
	}elseif($array['todosCampos']['D145_Pessoa_Fisica_Juridica']=='PJ'){
        return "Jurídica";
    }elseif($array['todosCampos']['D145_Pessoa_Fisica_Juridica']=='PI'){
        return "Internacional";
    }	
}

function campoTipoRegraCompras($array){
	global $g;
	if ($array['todosCampos']['D145_Flag_Cabecalho_Item']=='C'){
		return "Cabeçalho";
	}elseif($array['todosCampos']['D145_Flag_Cabecalho_Item']=='I'){
		return "Item";
	}		
}

function campoTipoCompras($array){
	global $g;
	if ($array['todosCampos']['D145_Produzido_Adquirido']=='P'){
		return "Produzido/Importado";
	}elseif($array['todosCampos']['D145_Produzido_Adquirido']=='A'){
		return "Aquisição Nacional";
	}		
}

function campoAtivaCompras($array){
	global $g;
	if ($array['todosCampos']['D145_Flag_Inativar']=='S'){
		return "Sim";
	}elseif($array['todosCampos']['D145_Flag_Inativar']=='N'){
		return "Não";
	}	
}
function campoPercentualIcmsIntDifal($array){
	global $g;
	if ($array['todosCampos']['D006_Flag_Endereco_Aliquota_Icms_Interno_Difal'] == 'P'){
		return "PADRÃO";
	}elseif($array['todosCampos']['D006_Flag_Endereco_Aliquota_Icms_Interno_Difal'] == 'E'){
		return "ENTREGA";
	}	
}
function campoRegraDestino($array) {
	global $g;

	$destino = !empty($array['todosCampos']['D137_Flag_Destino_Produto'])
					? $array['todosCampos']['D137_Flag_Destino_Produto'] 
					: $array['todosCampos']['D145_Flag_Destino_Produto']; 

	switch($destino)
	{
		case 1:
			return '1 - INDUSTRIALIZAÇÃO';
			break;
		case 2:
			return '2 - REVENDA';
			break;
		case 3:
			return '3 - CONSUMO';
			break;
		case 4:
			return '4 - ESPECIAL';
			break;
		case 5:
			return '5 - BONIFICAÇÃO';
			break;
		case 6:
			return '6 - TROCA';
			break;
		case 7:
			return '7 - DEMONSTRAÇÃO';
			break;
		case 8:
			return '8 - CONSIGNAÇÃO';
			break;
		case 9:
			return '9 - VENDA FUTURA';
			break;
		case 10:
			return '10 - VENDA CONTA E ORDEM';
			break;
		case 11:
			return '11 - VENDA PRÓ-EMPREGO';
			break;
		case 12:
			return '12 - REMESSA PARA CONSERTO';
			break;
		case 13:
			return '13 - REMESSA PARA INDUSTRIALIZAÇÃO';
			break;
		case 14:
			return '14 - OUTRAS SAÍDAS';
			break;
		case 15:
			return '15 - AMOSTRA';
			break;
		case 16:
			return '16 - DOAÇÃO';
			break;
		case 17:
			return '17 - VENDA DE ATIVO IMOBILIZADO';
			break;
		case 18:
			return '18 - TRANSFERÊNCIA ENTRE EMPRESAS';
			break;
		case 19:
			return '19 - REMESSA TRIANGULAR';
			break;
		case 20:
			return '20 - REMESSA VENDA FUTURA';
			break;
	}
}

function tipoDescontoAplicadoValorTotal($array) {
	global $g;
	if ($array['campoAtual']=='C'){
		return "CONDICIONAL";
	}elseif($array['campoAtual']=='I'){
		return "INCONDICIONAL";
	}
}

?>