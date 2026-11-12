<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 */

function campoAbrirDocumento($array){
			$id = $array['todosCampos']['T155_Id'];
			$retorno = "<a href=\"/imp/imp005/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
	}
function campoCheckboxChecklist($array){
	global $g,$r_acaoId;
		$retorno = "<input onchange=\"imp005CheckboxChecklist(this, '{$array['todosCampos']['D122_Id']}', '{$r_acaoId}', '{$g['divId']}')\" type='checkbox' id='T161_Flag_Ok' title='Confirmar Item'";
		if ($array['todosCampos']['T161_Flag_Ok']=='S'){
			//return $retorno .="checked='checked' disabled='disabled'";
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	
}

function auditoriaDI($array,$param = array()){
	global $g,$r_acaoId;
	$T161_Id = $array['todosCampos']['T161_Id'];

    $retorno = <<<EOT
			<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Auditoria ', '/etc/etc007/content/etc007ContentAuditoria/', '&id=' + encodeURIComponent({$T161_Id}) + '&tabela=T161', [990,605]);">
			   <div class="icone ui-state-default ui-corner-all">
	   				<span class="ui-icon ui-icon-info" title="Auditoria"></span>
			   </div>
			</span>
EOT;

        return $retorno;
	
}

function validaCampoME($array){
	global $g;
	
	if($array['todosCampos']['T168_Flag_ME'] == "S"){
		return "Sim";
	}else{
		return "Não";
	}
}
function campoExclusaoReceber($array) {
	global $g;
	if ($array['todosCampos']['T007_Id'] > 0){
		return "&nbsp";
	} else {
    	return <<<EOT
    		<a href="#" onclick="imp005CampoExclusaoReceber('{$array['todosCampos']['T002_Id']}','{$array['todosCampos']['T002_T035_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}

function geraCheckList($array) {
	$D122=mysql_query("SELECT * from D122");// WHERE D116_Id=".$array['todosCampos']['T147_D116_Id']);			
	$total=mysql_num_rows($D122);
	$T161=mysql_query("SELECT * from T161 WHERE T161_T295_Id=".$array['todosCampos']['T295_Id']." AND T161_Flag_Ok='S'");
	$marcados=mysql_num_rows($T161);
	if ($marcados==$total)
		return "<span style='color:green'>$marcados/$total</span>";
	else
		return "$marcados/$total";
}

function campoPedidos($array){
	global $g;
	$retorno=mysql_query("SELECT sum(T006_Quantidade) as Quantidade FROM T006 
			left join T005 on T005_Id=T006_T005_Id
			LEFT JOIN T295 ON T295_Id=T006_T295_Id 
			WHERE T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11' 
			AND T006_T295_Id='{$array['todosCampos']['T296_T295_Id']}' 
			AND T006_D009_Id='{$array['todosCampos']['T296_D009_Id']}'");
	$retorno=mysql_fetch_array($retorno);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pedidos', '/imp/imp005/content/imp005ContentPedidosProduto/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T296_Id']}), [700,400]);\"> <font style='font-weight:bold'>".$retorno['Quantidade']."</font></a>";	
}

function campoPrecoLiquido($array){
	global $g;
	$retorno=mysql_query("SELECT sum(T006_Valor_Total_Preco) as Preco FROM T006 
			left join T005 on T005_Id=T006_T005_Id
			LEFT JOIN T295 ON T295_Id=T006_T295_Id 
			WHERE T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11' 
			AND T006_T295_Id='{$array['todosCampos']['T296_T295_Id']}' 
			AND T006_D009_Id='{$array['todosCampos']['T296_D009_Id']}'");
	$retorno=mysql_fetch_array($retorno);
	if($retorno['Preco'] > 0){
		return gCorrigeNumero($retorno['Preco']);
	} else {
		return null;
	}
}

function campoPrecoBruto($array){
	global $g;
	$retorno=mysql_query("SELECT sum(T006_Valor_Preco_Bruto * T006_Quantidade) as Preco FROM T006 
			left join T005 on T005_Id=T006_T005_Id
			LEFT JOIN T295 ON T295_Id=T006_T295_Id 
			WHERE T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11' 
			AND T006_T295_Id='{$array['todosCampos']['T296_T295_Id']}' 
			AND T006_D009_Id='{$array['todosCampos']['T296_D009_Id']}'");
	$retorno=mysql_fetch_array($retorno);
	if($retorno['Preco'] > 0){
		return gCorrigeNumero($retorno['Preco']);
	} else {
		return null;
	}
}

function campoCancelamentoPO($array) {
	global $g;

    if(!empty($array['todosCampos']['T295_Id']) && in_array($array['todosCampos']['T295_Flag_Status'], ['', '0', '2'])){	
		return "<a href=\"#\" onclick=\"cancelarPO('{$array['todosCampos']['T295_Id']}', '{$g['divId']}')\">Cancelar</a>";
	}
	
	return "";
}

// Funções referentes a contas a receber

function validaCampoBx($array){
	global $g;
	if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
		return '';
	}
	if($array['todosCampos']['T002_Data_Recebimento']=='0000-00-00' && $array['todosCampos']['T002_Flag_Cancelada']!='S'){
		$retorno = "<input type='checkbox' id='T002_Flag_Baixar_Lote' title='Baixar titulo' onChange=\"fin001CheckboxBx(this, '{$array['todosCampos']['T002_Id']}', '{$g['divId']}')\" ";
		
		if ($array['todosCampos']['T002_Flag_Baixar_Lote']=='S'){
			$retorno .="checked='checked'";
		}
		return $retorno .= " />";
	}elseif($array['todosCampos']['T002_Flag_Cancelada']!='S'){
		
		$T002_DR = $array['todosCampos']['T002_Data_Recebimento'];
		if ($T002_DR == null || $T002_DR == '' || $T002_DR == '0000-00-00')
			$T002_DR=0;
		
		return "<a href=\"#\"><span class=\"ui-icon ui-icon-closethick\" title=\"Estornar título\" onClick=\"fin001ValidaCampoRmEstorno(event, '{$array['todosCampos']['T002_Id']}','{$T002_DR}', '{$g['divId']}', '{$g['divIdRoot']}')\">
							</span>
			    </a>";
	}
}

function validaCampoRp($array){
	global $g;
	if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
		return '';
	}
	if($array['todosCampos']['T002_Data_Recebimento']=='0000-00-00' && $array['todosCampos']['T002_T035_Id']<=0 && $array['todosCampos']['T002_Flag_Cancelada']!='S'){
		$retorno = "<input type='checkbox' name='T002_Flag_Reparcelar' title='Reparcelamento / Agrupamento' onChange=\"fin001CheckboxRp(this, '{$array['todosCampos']['T002_Id']}', '{$g['divId']}')\" ";
		
		if ($array['todosCampos']['T002_Flag_Reparcelar']=='S') {
			$retorno .="checked = 'checked'";
		}
		return $retorno .= " />";
	}else{
		return "&nbsp;";
	}
}

function tipoDespesa($array) {
	global $g;
	if($array['todosCampos']['T300_Flag_Tipo'] == 'F'){
		return 'FÓRMULA';
	} elseif($array['todosCampos']['T300_Flag_Tipo'] == 'V'){
		return 'VALOR FIXO';
	} else {
		return '-';
	}
}

function tipoRateio($array) {
	global $g;
	if($array['todosCampos']['T300_Flag_Tipo_Rateio'] == 'C'){
		return 'CUSTO';
	} elseif($array['todosCampos']['T300_Flag_Tipo_Rateio'] == 'P'){
		return 'PESO';
	} else {
		return '';
	}
}

function tipoModalFrete($array){
	global $g;
    if($array['todosCampos']['T295_Flag_Transporte_DI'] == '1'){
		return 'MARITÍMA';
	} elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '2'){
		return 'FLUVIAL';
	} elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '3'){
		return 'LACUSTRE';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '4'){
		return 'AÉREA';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '5'){
		return 'POSTAL';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '6'){
		return 'FERROVIÁRIA';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '7'){
		return 'RODOVIÁRIA';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '8'){
		return 'CONDUTO/REDE TRANSMISSÃO';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '9'){
		return 'MEIOS PRÓPRIOS';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '10'){
		return 'ENTRADA/SAÍDA FICTA';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '11'){
		return 'COURIER';
    }elseif($array['todosCampos']['T295_Flag_Transporte_DI'] == '12'){
		return 'HANDCARRY';
    }else{
		return '-';
	}
}

function checkboxExcluirItemPO($array) {
	global $g;

	$T296_Id = $array['todosCampos']['T296_Id'];
	
	$restricao = $g['conf']->restricaoTravar('excluir', 'T296_Id', $T296_Id, '', '');
	$disabled = '';
	if($restricao !== true){
		$disabled = 'disabled=disabled';
	}

	$retorno = "<input onclick=\"checkboxExcluirItemPO(this, '{$T296_Id}', '{$g['divId']}')\" type='checkbox' id='T296_Flag_Excluir' title='Excluir Item' ". $disabled;
	if ($array['todosCampos']['T296_Flag_Excluir'] == 'S'){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";	
}

function abrirDeclaracaoImportacao($array) {
    global $g;

	if (empty($array['campoAtual'])) {
		return '-';
	} else {
		$retorno = '';
		// pode ter vários T075
		$T075s = explode('|', $array['campoAtual']);
		foreach ($T075s as $T075_Id) {
			if (empty($T075_Id)) { continue; }

			$T075 = mysql_query("SELECT T075_Numero_DI FROM T075 WHERE T075_Id = '{$T075_Id}'");
			$mT075 = mysql_fetch_assoc($T075);
			$DI = (!empty($mT075['T075_Numero_DI'])) ? $mT075['T075_Numero_DI'] : $T075_Id; 

			$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'DI - ID: ' + {$T075_Id} , '/imp/imp003/content/imp003ContentDiIncluir/', '&acaoId=' + encodeURIComponent({$T075_Id}), [950,615]);\">{$DI}, </a>";
		}
		return trim($retorno, ", ");
	}
}

// Fim funções referentes a contas a receber


?>