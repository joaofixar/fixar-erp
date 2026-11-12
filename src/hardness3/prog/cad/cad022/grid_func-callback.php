<?php
namespace hardness;

function campoTipo($array){
	switch ($array['todosCampos']['D027_Flag_Tipo']) {
	  	case '1':
	  		return "BOLETO";
	  		break;
	  	case '2':
	  		return "CHEQUE";
	  		break;
	  	case '3':
	  		return "PROMISSÓRIA";
	  		break;
	  	case '4':
	  		return "E-COMMERCE";
	  		break;
	  	case '5':
	  		return "OUTROS";
	  		break;
	  	default:
	  		return "";
	  		break;
	}
}

function campoVendaPrazo($array){
	if($array['todosCampos']['D027_Flag_Prazo'] == 'S'){
		return "Sim";
	} if($array['todosCampos']['D027_Flag_Prazo'] == 'N'){
		return "Não";
	} if($array['todosCampos']['D027_Flag_Prazo'] == 'X'){
		return "Nenhum";
	}
}

function campoBoleto($array){
	if($array['todosCampos']['D027_Flag_Boleto'] == 'N'){
		return "Não";
	} elseif($array['todosCampos']['D027_Flag_Boleto'] == 'S'){
		return "Sim";
	}
}

function campoCupom($array){
	if($array['todosCampos']['D027_Flag_Cupom'] == 'N'){
		return "Não";
	} elseif($array['todosCampos']['D027_Flag_Cupom'] == 'S'){
		return "Sim";
	}
}

function validaCampoComercial($array){
	global $g;
	$retorno = <<<EOT
		<input type='checkbox' name='D027_Flag_Comercial' title='Comercial' onClick='cad022CheckboxFlagComercial(this, "{$array['todosCampos']['D027_Id']}", "{$g['divId']}")'
EOT;

	if ($array['todosCampos']['D027_Flag_Comercial']=='S'){
		$retorno .= <<<EOT
			checked = 'checked'
EOT;
	}
	$retorno .= <<<EOT
		/>
EOT;
	return $retorno;
}

function campoFormaPagamento($array){
	switch ($array['todosCampos']['D027_Tipo_Forma_Pagamento_NFCE']) {
	  	case '01':
	  		return "DINHEIRO";
	  		break;
	  	case '02':
	  		return "CHEQUE";
	  		break;
	  	case '03':
	  		return "CARTÃO DE CRÉDITO";
	  		break;
	  	case '04':
	  		return "CARTÃO DE DÉBITO";
	  		break;
	  	case '05':
	  		return "CRÉDITO LOJA";
	  		break;
		case '10':
			return "VALE ALIMENTAÇÃO";
			break;
		case '11':
			return "VALE REFEIÇÃO";
			break;
		case '12':
			return "VALE PRESENTE";
			break;
		case '13':
			return "VALE COMBUSTÍVEL";
			break;
		case '14':
			return "DUPLICATE MERCATIL";
			break;
		case '15':
			return "BOLETO BANCARIO";
			break;
		case '16':
			return "DEPÓSITO BANCÁRIO";
			break;
		case '17':
			return "PIX";
			break;
		case '18':
			return "TRANSFERÊNCIA BANCÁRIA, CARTEIRA DIGITAL";
			break;
		case '19':
			return "PROGRAMA DE FIDELIDADE, CASHBACK, CRÉDITO VIRTUAL";
			break;
		case '90':
			return "SEM PAGAMENTO";
			break;
		case '99':
			return "OUTROS";
			break;
	  	default:
	  		return "";
	  		break;
	}
}