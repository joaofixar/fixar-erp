<?php
namespace hardness;
function campoConfirmado($array){
	global $g;	
	$retorno = <<<EOT
	<input type='checkbox' name='T018_Flag_Confirmado' title='Indisponível' onclick='fin009CheckboxFlagConfirmado(this, "{$array['todosCampos']['T018_Id']}", "{$g['divId']}")'
EOT;
	if ($array['todosCampos']['T018_Flag_Confirmado']=='S'){
		$retorno .= <<<EOT
		checked = 'checked'
EOT;
	}
	$retorno .= <<<EOT
	 />
EOT;
	return $retorno;
//	if($array['todosCampos']['T018_Flag_Confirmado'] == "S"){
//		return "Sim";
//	} else if($array['todosCampos']['T018_Flag_Confirmado'] == "N"){
//		return "Não";
//	}
}

function ValidaCampoSaldo($array){
	global $g;
	$T018_Valor_Saldo = gCorrigeNumero($array['todosCampos']['T018_Valor_Saldo']);
	if(($array['todosCampos']['D014_Flag_Pagamento_Unico']!='S' and $array['todosCampos']['T018_Valor_Saldo']>0) OR ($array['todosCampos']['D014_Flag_Pagamento_Unico']=='S' and ($array['todosCampos']['T018_Valor_Pago']+$array['todosCampos']['T018_Valor_Pagar'])<=0 AND $array['todosCampos']['T018_Valor_Saldo']>0)){
		return "<font color='blue'><b>$T018_Valor_Saldo</b></font>";
	}else if($array['todosCampos']['D014_Flag_Pagamento_Unico']=='S' and ($array['todosCampos']['T018_Valor_Pago']+$array['todosCampos']['T018_Valor_Pagar'])>0 AND $array['todosCampos']['T018_Valor_Saldo']>0){
		return "<font color='#605E5E'><b>$T018_Valor_Saldo</b></font>";
	}else if($array['todosCampos']['T018_Valor_Saldo']<0){
		return "<font color='red'><b>$T018_Valor_Saldo</b></font>";
	} else {
		return "<b>$T018_Valor_Saldo</b>";
	}
}

function validaCampoOperacao($array){
	if($array['todosCampos']['D014_Flag_Tipo'] == "RC"){
		return "Recebimento";
	}else if($array['todosCampos']['D014_Flag_Tipo'] == "PG"){
		return "Pagamento";
	}
}
?>