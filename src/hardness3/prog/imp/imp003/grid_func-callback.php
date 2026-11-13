<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 */

function campoAbrirDocumento($array){
			$id = $array['todosCampos']['T155_Id'];
			$retorno = "<a href=\"/imp/imp003/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
	}
function campoCheckboxChecklist($array){
	global $g,$r_acaoId;
		$retorno = "<input onchange=\"imp003CheckboxChecklist(this, '{$array['todosCampos']['D122_Id']}', '{$r_acaoId}', '{$g['divId']}')\" type='checkbox' id='T161_Flag_Ok' title='Confirmar Item'";
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

function campoFlagCalculoManualDI($array){
    $flag = trim((string)$array['campoAtual']);

    if ($flag === 'I') {
        return "DUIMP";
    }

    if ($flag === 'S') {
        return "MANUAL";
    }

    return "NORMAL";
}

function campoExclusao($array) {
    global $g;
    return <<<EOT
        <a href="#" onclick="imp003CampoExclusao('{$array['todosCampos']['T015_Id']}', '{$g['divId']}')">
            <span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
        </a>
EOT;
}
function campoExclusaoReceber($array) {
	global $g;
	if ($array['todosCampos']['T007_Id'] > 0){
		return "&nbsp";
	} else {
    	return <<<EOT
    		<a href="#" onclick="imp003CampoExclusaoReceber('{$array['todosCampos']['T002_Id']}','{$array['todosCampos']['T002_T035_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}

function geraCheckList($array) {
	$D122=mysql_query("SELECT * from D122");// WHERE D116_Id=".$array['todosCampos']['T147_D116_Id']);			
	$total=mysql_num_rows($D122);
	$T161=mysql_query("SELECT * from T161 WHERE T161_T075_Id=".$array['todosCampos']['T075_Id']." AND T161_Flag_Ok='S'");
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
			LEFT JOIN T075 ON T075_Id=T006_T075_Id 
			WHERE T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11' 
			AND T006_T075_Id='{$array['todosCampos']['T055_T075_Id']}' 
			AND T006_D009_Id='{$array['todosCampos']['T055_D009_Id']}'");
	$retorno=mysql_fetch_array($retorno);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pedidos', '/imp/imp003/content/imp003ContentPedidosProduto/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T055_Id']}), [700,400]);\"> <font style='font-weight:bold'>".$retorno['Quantidade']."</font></a>";	
}

function campoPrecoLiquido($array){
	global $g;
	$retorno=mysql_query("SELECT sum(T006_Valor_Total_Preco) as Preco FROM T006 
			left join T005 on T005_Id=T006_T005_Id
			LEFT JOIN T075 ON T075_Id=T006_T075_Id 
			WHERE T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11' 
			AND T006_T075_Id='{$array['todosCampos']['T055_T075_Id']}' 
			AND T006_D009_Id='{$array['todosCampos']['T055_D009_Id']}'");
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
			LEFT JOIN T075 ON T075_Id=T006_T075_Id 
			WHERE T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11' 
			AND T006_T075_Id='{$array['todosCampos']['T055_T075_Id']}' 
			AND T006_D009_Id='{$array['todosCampos']['T055_D009_Id']}'");
	$retorno=mysql_fetch_array($retorno);
	if($retorno['Preco'] > 0){
		return gCorrigeNumero($retorno['Preco']);
	} else {
		return null;
	}
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

function notaFiscal($array) {
    global $g;

	$T007_Id = $array['todosCampos']['T007_Id'];

	if (empty($array['campoAtual'])) {
		return '-';
	} else {
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Nota Fiscal - ID: ' + {$T007_Id} , '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent({$T007_Id}), [950,615]);\">{$array['campoAtual']}</a>";	
	}
}

function checkboxExcluirItemDI($array) {
	global $g;

	$T055_Id = $array['todosCampos']['T055_Id'];
	
	$restricao = $g['conf']->restricaoTravar('excluir', 'T055_Id', $T055_Id, '', '');
	$disabled = '';
	if($restricao !== true){
		$disabled = 'disabled=disabled';
	}

	$retorno = "<input onclick=\"checkboxExcluirItemDI(this, '{$T055_Id}', '{$g['divId']}')\" type='checkbox' id='T055_Flag_Excluir' title='Excluir Item' ". $disabled;
	if ($array['todosCampos']['T055_Flag_Excluir'] == 'S'){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";	
}

function validaCampoXMLDi($array){

	global $confUsuario, $g;
	if (!empty($array['todosCampos']['T075_Numero_DI'])){
		$numeroDI = str_replace(array('/','.',' ','-'),'',$array['todosCampos']['T075_Numero_DI']);
		//if (file_exists("dados_usuarios/{$confUsuario['dbDatabase']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
		if (file_exists("{$g['pathDadosAntigo']}/di/xml/recebido/{$numeroDI}.xml")){
			$retorno = "<a href=\"/imp/imp003/grid_func-ajax/downloadxml/?ajax=true&T075_Numero_DI=" . urlencode($numeroDI) . "\">XML</a>";
		} else {
			$retorno = "<a title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'> </a>";
		}
		return $retorno;		
	}else{        
		return '';
	}
	 		
}

function coloreLinhaProdutos($array){
	global $g;
	if ($array['todosCampos']['T055_D009_Id'] <= 0 or $array['todosCampos']['D009_C004_Id'] != $g['empresaAtual']){
		return "<font style='color:var(--BGVermelho);'>" . $array['todosCampos']['T055_Descricao_Produto'] . "</font>";
	}else{
		return "<font style='color:var(--letra-grid-linhaPreta);'>" . $array['todosCampos']['T055_Descricao_Produto'] . "</font>";
	}
}

function vincularProduto($array, $params = null){
	global $g;	

	if ($array['todosCampos']['T055_D009_Id'] > 0) {
		$codigoProduto = (empty($array['todosCampos']['T055_Codigo_Produto'])) ? "Sem codigo" : $array['todosCampos']['T055_Codigo_Produto'];
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastro produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['D001_Id']}), [700,400]);\">".$codigoProduto."</a>";	
	} else{
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Vincular produto', '/imp/imp003/content/imp003ContentAssociarItemProduto/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T055_Id']}), [700,150]);\">Vincular</a>";	
	}
}

function desvincularProduto($array){
	global $g;

	// Só vai ter opção para desvincular se o item tiver sido importado através do XML
	$D009_Id 					   = $array['todosCampos']['T055_D009_Id'];
	$T055A_Flag_Item_Importado_XML = $array['todosCampos']['T055A_Flag_Item_Importado_XML']; 
	$T055_Id 					   = $array['todosCampos']['T055_Id'];
	
	if($D009_Id > 0 && $T055A_Flag_Item_Importado_XML == 'S'){
		return <<<EOT
		<a href="#" onclick="desvincularProduto('{$T055_Id}','{$g['divId']}')">
			<span title="Desvincular produto">Desvincular</span>
		</a>
EOT;
	}else{
		return '';
	}

}

function tipoRateio($array) {
	global $g;
	if($array['todosCampos']['T168_Flag_Tipo_Rateio'] == 'C'){
		return 'CUSTO';
	} elseif($array['todosCampos']['T168_Flag_Tipo_Rateio'] == 'P'){
		return 'PESO';
	} else {
		return '';
	}
}

function baseRateio($array) {
	global $g;
	if($array['todosCampos']['T168_Flag_Base_Rateio'] == '1'){
		return 'TOTAL DI';
	} elseif($array['todosCampos']['T168_Flag_Base_Rateio'] == '2'){
		return 'TOTAL ADIÇÃO';
	} elseif($array['todosCampos']['T168_Flag_Base_Rateio'] == '3'){
		return 'PESO ADIÇÃO';
	} else {
		return '';
	}
}

function tipoDespesa($array) {
	global $g;
	if($array['todosCampos']['T168_Flag_Tipo'] == 'F'){
		return 'FÓRMULA';
	} elseif($array['todosCampos']['T168_Flag_Tipo'] == 'V'){
		return 'VALOR FIXO';
	} else {
		return '-';
	}
}

function tipoDespesaComex($array) {
	if($array['todosCampos']['T168_Tipo_Despesa'] == '2'){
		return 'THC';
	} elseif($array['todosCampos']['T168_Tipo_Despesa'] == '3'){
		return 'SEGURO';
	} elseif($array['todosCampos']['T168_Tipo_Despesa'] == '4'){
		return 'SISCOMEX';
	} elseif($array['todosCampos']['T168_Tipo_Despesa'] == '5'){
		return 'AFRMM';
	} elseif($array['todosCampos']['T168_Tipo_Despesa'] == '6'){
		return 'FRETE';
	} else {
		return 'OUTROS';
	}
}

function tipoComex($array) {
	if($array['todosCampos']['T168_Tipo'] == '1'){
		return 'OUTRAS';
	} elseif($array['todosCampos']['T168_Tipo'] == '2'){
		return 'ADUANEIRAS';
	} elseif($array['todosCampos']['T168_Tipo'] == '3'){
		return 'ACESSÓRIAS';
	}
}

function verificaProdutoImportadoXML($array, $params = null){
	global $g;	

	if($array['todosCampos']['T310_Id'] > 0){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Itens importados XML', '/imp/imp003/content/imp003ContentItensImportadosDI/', '&T310_Id=' + encodeURIComponent({$array['todosCampos']['T310_Id']}) + '&T055_Id=' + encodeURIComponent({$array['todosCampos']['T055_Id']}), [700,400]);\">Vinculado</a>";	
	} else {
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Itens importados XML', '/imp/imp003/content/imp003ContentItensImportadosDI/', '&T075_Id=' + encodeURIComponent({$array['todosCampos']['T055_T075_Id']}) + '&T055_Id=' + encodeURIComponent({$array['todosCampos']['T055_Id']}), [700,400]);\"><span style=\"color: red;\">Vincular</span></a>";	
	}
}

function descricaoItemXML($array, $params = null){
	global $g;	

	$descricao = $array['campoAtual'];
	$descricao = substr($descricao,0,150).'<br>'.substr($descricao,150,150).'<br>'.substr($descricao,300,150);


	return $descricao;	
}

function exclusaoDI($array){
	global $g;

	if($array['todosCampos']['T007_Id'] > 0 || ($array['todosCampos']['T075_Data_Estoque'] > '0000-00-00' )){
		return "";
	}else{
		return <<<EOT
		<a href="#" onclick="excluirDI('{$array['todosCampos']['T075_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}

// Fim funções referentes a contas a receber


?>