<?php
namespace hardness;
	
function validaCampoComprar($array){
	global $g,$r_acaoId;
	$retorno = "<input onchange=\"est019CheckboxComprar(this, '{$array['todosCampos']['D009_Id']}', '{$g['divId']}')\" type='checkbox' id='D009_Flag_Cotar' title='Cotar'";
	if ($array['todosCampos']['D009_Flag_Comprar']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}

function validaCampoNaoComprar($array){
	global $g,$r_acaoId;
	$retorno = "<input onchange=\"est019CheckboxNaoComprar(this, '{$array['todosCampos']['D049_Id']}', '{$g['divId']}')\" type='checkbox' id='D049_Flag_Nao_Comprar' title='Não Comprar'";
	if ($array['todosCampos']['D049_Flag_Nao_Comprar']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}

function validaCampoCLientes($array) {
// if (mb_strlen($array['campoAtual']) > 60) {
	// return mb_substr($array['campoAtual'], 0, 60) . ' ...';
// }
// return $array['campoAtual'];

$retorno = explode(',', $array['campoAtual']);
return count($retorno);
}

function validaCampoMedia($array){
	return round($array['campoAtual'], 2);
}

function mediaMMF($array){
	if ($array['todosCampos']['D009_Meses_MMF']>0){
		$ultimaData = date("Y-m-d", strtotime("-0" . $array['todosCampos']['D009_Meses_MMF'] . " months"));
		$T001_Medio  = mysql_query("SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_D009_Id='{$array['todosCampos']['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND IF(T001_Flag_MMF='S',1,0)=1");
		$mT001_Media = mysql_fetch_array($T001_Medio);
		$mediaMMF = $mT001_Media['SUM(T001_Quantidade)'] / $array['todosCampos']['D009_Meses_MMF'];
		return $mediaMMF;
	}
	return "";
}

function est019campoConsumido($array){
	if($array['todosCampos']['D009_Quantidade_Consumo']>0){
		return "{$array['todosCampos']['D009_Quantidade_Consumo']}";
	}else{
		return "0,00";
	}
}

function est019DescricaoProduto($array){
	global $g;
	if($array['todosCampos']['D049_Flag_Tipo'] == 'PD'){
		$retorno = $array['campoAtual'];
		$sqlT261 = mysql_query("SELECT D001_Codigo_Produto, 
		                               D001_Descricao_Produto, 
									   T261_Quantidade 
								  FROM T261 
							 LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao 
							 LEFT JOIN D049 ON D001_Id = D049_D001_Id
							 LEFT JOIN D009 ON D049_Id = D009_D049_Id
							 LEFT JOIN D037 ON D037_Id = D001_D037_Id
							     WHERE T261_T006_Id = '{$array['todosCampos']['T006_Id']}' 
								   AND D009_C004_Id='{$g['empresaAtual']}'");
		if(mysql_num_rows($sqlT261) > 0){
			$retorno .= "<br><table cellspacing=0 cellpadding=0 style=\"border: 1px solid gray; border-bottom: 0px; font-size: 11px; font-weight: normal;\">";
			while($rowT261 = mysql_fetch_array($sqlT261)){
				$quantidadeTotal = gCorrigeNumero($rowT261['T261_Quantidade']*$array['todosCampos']['Falta'], 5);
				$rowT261['T261_Quantidade'] = gCorrigeNumero($rowT261['T261_Quantidade'],5);
				$rowT261['D001_Descricao_Produto'] = $rowT261['D001_Descricao_Produto'];
				$retorno .= <<<EOT
					<tr>
						<td style="border-bottom: 1px solid gray;">{$rowT261['D001_Codigo_Produto']}</td>
						<td style="border-bottom: 1px solid gray; border-left: 1px solid gray;">{$rowT261['D001_Descricao_Produto']}</td>
						<td style="border-bottom: 1px solid gray; border-left: 1px solid gray;">{$rowT261['T261_Quantidade']}</td>
						<td style="border-bottom: 1px solid gray; border-left: 1px solid gray;">{$quantidadeTotal}</td>
					</tr>
EOT;
			}
			$retorno .= "</table>";
		} else {
			$retorno .= "<br><span style=\"color: var(--BGVermelho);\">Falta engenharia do produto</span>";
		}
		return $retorno;
	} else {
		return $array['campoAtual'];
	}
}

function est019VendaPerdida($array){
	global $g;
    $T004 = mysql_query("SELECT SUM(T004_Quantidade) as T004_Quantidade
                            FROM T004 				 
                            WHERE T004_D009_Id='{$array['todosCampos']['D009_Id']}'
                            AND T004_Flag_Cancelado='S'");
	$mT004 = mysql_fetch_array($T004);
	$mT004['T004_Quantidade'] = gCorrigeNumero($mT004['T004_Quantidade']);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento Cancelado', '/crm/crm001/content/crm001contentOrcamentoPerdido/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'),[600,600]); return false;\">{$mT004['T004_Quantidade']}</a>";
}

function est019validaCampoCotar($array) {
    global $g;

	$origensNacionais = array('0','3','4','5','8');
	if(in_array($array['todosCampos']['D049_Origem_Mercadoria'],$origensNacionais)){
		return "<input type='checkbox' disabled='disabled'>";
	}

    $retorno = "<input onclick=\"est019CheckboxCotar(this, '{$array['todosCampos']['D009_Id']}', '{$g['divId']}')\" type='checkbox' id='D009_Flag_Cotar' title='Cotar'";
    if ($array['todosCampos']['D009_Flag_Cotar']=='S'){
        if($array['todosCampos']['D009_C007_Id_Comprar'] != $g['usuarioAtual']){
            $C007 = mysql_query("SELECT C007_Nome FROM C007 WHERE C007_Id='{$array['todosCampos']['D009_C007_Id_Comprar']}'");
            $mC007 = mysql_fetch_array($C007);
            $disabled = "disabled='disabled'";
            $nome = substr($mC007['C007_Nome'], 0,8);
            $C007_Nome = <<<EOT
                <font color='gray' style='font-size:8px;'>{$nome}</font>
EOT;
        }
        $retorno .="checked='checked'";
        return $retorno .= " {$disabled}/><br />{$C007_Nome}";
    } else {
        return $retorno .= " />";
    }
}

function est019checkboxCotarEmergencia($array){
	global $g;
	$T004_Flag_Gerar_Cotacao = $array['todosCampos']['T004_Flag_Gerar_Cotacao'];
	$T004_Id = $array['todosCampos']['T004_Id'];
	$T004_C007_Id_Comprador = $array['todosCampos']['T004_C007_Id_Comprador'];
	$C007_Primeiro_Nome = substr($array['todosCampos']['marcou.C007_Primeiro_Nome'], 0,8);

	$origensNacionais = array('0','3','4','5','8');
	if(in_array($array['todosCampos']['D049_Origem_Mercadoria'],$origensNacionais)){
		return "<input type='checkbox' disabled='disabled'>";
	}

	$retorno = "<input onclick=\"est019checkboxCotarEmergencia(this, '{$T004_Id}', '{$g['divId']}');\" type='checkbox' id='T004_Flag_Gerar_Cotacao' title='Cotar'";
	if ($T004_Flag_Gerar_Cotacao == 'S'){
		if($T004_C007_Id_Comprador != $g['usuarioAtual'] && $T004_C007_Id_Comprador > 0 && $T004_C007_Id_Comprador != null){
			return $retorno .="checked='checked' disabled='disabled' /><br /><font color='gray' style='font-size:8px;'>{$C007_Primeiro_Nome}</font> ";
		}
		return $retorno .="checked='checked' /> ";
	} else {
		return $retorno .= " />";
	}
}

function est019checkboxCotarPedidoEmergencia($array){
	global $g;
	$T006_Flag_Gerar_Cotacao = $array['todosCampos']['T006_Flag_Gerar_Cotacao'];
	$T006_Id 				 = $array['todosCampos']['T006_Id'];
	$T006_C007_Id_Comprador  = $array['todosCampos']['T006_C007_Id_Comprador'];
	$C007_Primeiro_Nome 	 = substr($array['todosCampos']['marcou.C007_Primeiro_Nome'], 0,8);

	$origensNacionais = array('0','3','4','5','8');
	if(in_array($array['todosCampos']['D049_Origem_Mercadoria'],$origensNacionais)){
		return "<input type='checkbox' disabled='disabled'>";
	}

	$retorno = "<input onclick=\"est019checkboxCotarPedidoEmergencia(this, '{$T006_Id}', '{$g['divId']}');\" type='checkbox' id='T006_Flag_Gerar_Cotacao' title='Cotar'";
	if ($T006_Flag_Gerar_Cotacao == 'S'){
		if($T006_C007_Id_Comprador != $g['usuarioAtual'] && $T006_C007_Id_Comprador > 0 && $T006_C007_Id_Comprador != null){
			return $retorno .="checked='checked' disabled='disabled' /><br /><font color='gray' style='font-size:8px;'>{$C007_Primeiro_Nome}</font> ";
		}
		return $retorno .="checked='checked' /> ";
	} else {
		return $retorno .= " />";
	}
	
}


