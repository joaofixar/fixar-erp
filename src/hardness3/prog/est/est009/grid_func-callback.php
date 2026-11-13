<?php
namespace hardness;
	
function validaCampoComprar($array){
	global $g,$r_acaoId;
	$retorno = "<input onchange=\"est009CheckboxComprar(this, '{$array['todosCampos']['D009_Id']}', '{$g['divId']}')\" type='checkbox' id='D009_Flag_Cotar' title='Cotar'";
	if ($array['todosCampos']['D009_Flag_Comprar']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}

function validaCampoNaoComprar($array){
	global $g,$r_acaoId;
	$retorno = "<input onchange=\"est009CheckboxNaoComprar(this, '{$array['todosCampos']['D049_Id']}', '{$g['divId']}')\" type='checkbox' id='D049_Flag_Nao_Comprar' title='Não Comprar'";
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

function est009campoConsumido($array){
	if($array['todosCampos']['D009_Quantidade_Consumo']>0){
		return "{$array['todosCampos']['D009_Quantidade_Consumo']}";
	}else{
		return "0,00";
	}
}

function est009DescricaoProduto($array){
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

function est009VendaPerdida($array){
	global $g;
    $T004 = mysql_query("SELECT SUM(T004_Quantidade) as T004_Quantidade
                            FROM T004 				 
                            WHERE T004_D009_Id='{$array['todosCampos']['D009_Id']}'
                            AND T004_Flag_Cancelado='S'");
	$mT004 = mysql_fetch_array($T004);
	$mT004['T004_Quantidade'] = gCorrigeNumero($mT004['T004_Quantidade']);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento Cancelado', '/crm/crm001/content/crm001contentOrcamentoPerdido/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'),[600,600]); return false;\">{$mT004['T004_Quantidade']}</a>";
}

