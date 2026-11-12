<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */

function campoDebitoCredito($array){
		if ($array['todosCampos']['D014_Flag_Tipo']=='RC'){
			return "ENTRADA";
		}elseif($array['todosCampos']['D014_Flag_Tipo']=='PG'){
		return "Saída";
		}
}

function campoGrupoNegrito($array){ // segundo parametro $campo
	/**
	if($campo == 'D014_Flag_Tipo'){
		if ($array['todosCampos']['D014_Flag_Tipo']=='RC'){
			$array['campoAtual'] = "(+)";
		}elseif($array['todosCampos']['D014_Flag_Tipo']=='PG'){
			$array['campoAtual'] = "(-)";
		}	
	}
	*/
	if ($array['todosCampos']['D014_Flag_Plano'] == "G") {
    	return "<span style=\"font-weight: bold; font-size:12px;\">" . trim($array['campoAtual']) . "</span>";
	} else {
		return $array['campoAtual'];
	}
}

function validaCampoMes($array){
	if($array['todosCampos']['T018_Mes'] == '1'){
		return "Janeiro";
	} else if($array['todosCampos']['T018_Mes'] == '2'){
		return "Fevereiro";
	} else if($array['todosCampos']['T018_Mes'] == '3'){
		return "Março";
	} else if($array['todosCampos']['T018_Mes'] == '4'){
		return "Abril";
	} else if($array['todosCampos']['T018_Mes'] == '5'){
		return "Maio";
	} else if($array['todosCampos']['T018_Mes'] == '6'){
		return "Junho";
	} else if($array['todosCampos']['T018_Mes'] == '7'){
		return "Julho";
	} else if($array['todosCampos']['T018_Mes'] == '8'){
		return "Agosto";
	} else if($array['todosCampos']['T018_Mes'] == '9'){
		return "Setembro";
	} else if($array['todosCampos']['T018_Mes'] == '10'){
		return "Outubro";
	} else if($array['todosCampos']['T018_Mes'] == '11'){
		return "Novembro";
	} else if($array['todosCampos']['T018_Mes'] == '12'){
		return "Dezembro";
	} 
}

function campoConfirmado($array){
	global $g;	
	$retorno = <<<EOT
	<input type='checkbox' name='T018_Flag_Confirmado' title='Indisponível' onclick='cad034CheckboxFlagConfirmado(this, "{$array['todosCampos']['T018_Id']}", "{$g['divId']}")'
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
}

function validacheckAtivo($array){
	global $g;	
	$retorno = <<<EOT
	<input type='checkbox' name='D014_Flag_Ativo' title='Ativo' onclick='cad034CheckboxFlagAtivo(this, "{$array['todosCampos']['D014_Id']}", "{$g['divId']}")'
EOT;
	if ($array['todosCampos']['D014_Flag_Ativo']=='S'){
		$retorno .= <<<EOT
		checked = 'checked'
EOT;
	}
	$retorno .= <<<EOT
	 />
EOT;
	return $retorno;
}

function validacheckAtivoGrupo($array){
	global $g;	
	$retorno = <<<EOT
	<input type='checkbox' name='D032_Flag_Ativo' title='Ativo' onclick='cad034CheckboxFlagAtivoGrupo(this, "{$array['todosCampos']['D032_Id']}", "{$g['divId']}")'
EOT;
	if ($array['todosCampos']['D032_Flag_Ativo']=='S'){
		$retorno .= <<<EOT
		checked = 'checked'
EOT;
	}
	$retorno .= <<<EOT
	 />
EOT;
	return $retorno;
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
?>