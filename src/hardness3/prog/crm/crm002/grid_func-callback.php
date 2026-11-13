<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function campoCancelar($array){
	global $g;
	if ($array['todosCampos']['T117_Flag_Cancelado']=='N')
	{
		//No antigo sistema: <a href=\"javascript:cancelar_boleto('$mT117[T117_Id]');\" style='color:blue;text-decoration:none;'>";
		return "<a href=\"#\" onclick=\"fin005CampoCancelar('{$array['todosCampos']['T117_Id']}', '{$g['divId']}')\">Cancelar</a>";
	}
	else
	{
		return "Cancelado";
	}
}

function campoCancelamentoOrcamento($array) {
	global $g;
    $resultado=mysql_query("select T005_Id from T005 where T005_T003_Id='{$array['todosCampos']['T003_Id']}' AND T005_flag_status != '8'");
    $row=mysql_fetch_array($resultado);
    if(empty($row['T005_Id'])){	
		if($array['todosCampos']['T003_Flag_Perdido'] != 'S'){
			return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Motivo Cancelamento', '/ven/ven001/content/ven001ContentCancelamentoOrcamento/', '&acaoId={$array['todosCampos']['T003_Id']}', [400,300]); return false;">
				<span class="ui-icon ui-icon-circle-close" title="Cancelar"></span>
			</a>
EOT;
		}
	}else{
		return "";
	}
}

function campoeMail($array){
	global $g;
	if ($array['todosCampos']['T117_Flag_Cancelado']=='S')
	{
		return "&nbsp;";
	}
	else
	{
		if ($array['todosCampos']['T117_Data_Envio_Email']!='0000-00-00 00:00:00' and $array['todosCampos']['T117_Data_Envio_Email']!='')
		{
			return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T117_D024_Id']}'), [900,550]);\"><span class='ui-icon ui-icon-check' title='E-mail enviado em: ".gCorrigeData($array['todosCampos']['T117_Data_Envio_Email'])."''> </span></a>";
		}
		else
		{
			return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T117_D024_Id']}'), [900,550]);\"><img src='/hardness3/static/img/tmp/icone_enviar.gif' /></a>";
		}
	}
}

function validaCampoBx($array){
	global $g;
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

function validaCampoCancelarNF($array){
	global $g;
	return <<<EOT
    	<a href="#" onclick="est005CampoCancelar('{$array['todosCampos']['T084_Id']}', '{$g['divId']}')">
    		Cancelar
		</a>
EOT;
}

function validaCampoExcluir($array){
	global $g;
	return <<<EOT
    	<a href="#" onclick="est005CampoExcluir('{$array['todosCampos']['T084_Id']}', '{$g['divId']}')">
    		Excluir
		</a>
EOT;
}

function validaNotasLancadas($array){
	if ($array['todosCampos']['T084_Flag_Fechado']=='S')
     {
         return "<span class=\"ui-icon ui-icon-circle-check\"></span>";
     }
     else
     {
     	return "&nbsp";
     }
}

function coloreLinha($array){
	if ($array['todosCampos']['T013_Flag_Travar_Nota'] == 'N'){
		return "<font color='red'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}else if($array['todosCampos']['T013_Flag_Travar_Nota'] == 'S'){
		return "<font color='black'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}
}

function validaCampoAlmoxarifado($array){
		$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness/nota_recebida_lancamento/T013_imprimir_almoxarifado.php3?T013_Id={$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Imprimir</a>";
		return $retorno;
	}

function validaCampoStatus($array){
	
	if($array['todosCampos']['T011_Status_Aprovacao'] == '2'){
		$sql = "select C007_Primeiro_Nome from C007 WHERE C007_Id='{$array['todosCampos']['T011_C007_Id_Aprovador']}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		$nomeUsuario = $linha['C007_Primeiro_Nome'];
		
		return "<font color='green'>Aprovado</font> por ".$nomeUsuario;
		
	}elseif($array['todosCampos']['T011_Status_Aprovacao'] == '3'){
		
		$sql = "select C007_Primeiro_Nome from C007 WHERE C007_Id='{$array['todosCampos']['T011_C007_Id_Aprovador']}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		$nomeUsuario = $linha['C007_Primeiro_Nome'];
		
		return "<font color='red'>Negado</font> por ".$nomeUsuario;
		
	}elseif($array['todosCampos']['T011_Status_Aprovacao'] == '1'){
		
		return "Pendente";
	}
}

function campoFornecedor($array){
	// $sql="SELECT GROUP_CONCAT(D024_Nome_Empresa SEPARATOR '<br />') as D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	// $resultado=mysql_query($sql);
	// $row=mysql_fetch_array($resultado);
	// return $row['D024_Nome_Empresa'];
	
	$sql = "SELECT * FROM T173 LEFT JOIN D024 on D024_Id=T173_D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		$sql2 = "SELECT T174_Valor_Total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}'";
		$result2 = mysql_query($sql2);
		while($linhaT174 = mysql_fetch_array($result2)){
			$totalForn = $totalForn + $linhaT174['T174_Valor_Total'];
		}
		$totalForn = gCorrigeNumero($totalForn);
		if($linha['T173_Flag_Alcada_Status'] == 'K'){
			$alcada = "OK<br />";
		}elseif ($linha['T173_Flag_Alcada_Status'] == 'A'){
			$alcada = "<font color=\"green\">Aprovado</font><br />";		
		}elseif ($linha['T173_Flag_Alcada_Status'] == 'N'){
			$alcada = "<font color=\"red\">Negado - ".$linha['T173_Motivo_Nao_Liberado']."</font><br />";
		}elseif ($linha['T173_Flag_Alcada_Status'] == 'L'){
			$alcada = "<font color=\"blue\">Em Análise</font><br />";	
		}
		$retorno .= $linha['D024_Nome_Empresa']." / ".$alcada;
	}
	return $retorno;
}

function campoValorCotacao($array){
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		$sql2 = "SELECT SUM(T174_Valor_Total) as total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}'";
		$result2 = mysql_query($sql2);
		$linhaT174 = mysql_fetch_array($result2);
		//echo 'totalForn='.$totalForn.'<br />';
		$totalForn = gCorrigeNumero($linhaT174['total']);
		$retorno .= $totalForn."<br />";
	}
	
	return $retorno;
}

function validaCampoEmailEnviadoForn($array){
	$T010_Id=$array['todosCampos']['T010_Id'];
	
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	while($linha = mysql_fetch_array($result)){
		if($linha['T173_Flag_EMail_Enviado'] == "S"){
			$retorno .= "<span class=\"ui-icon ui-icon-mail-closed\"></span>";
		} else {
			$retorno .= "-<br />";
		}
		//$retorno.="<br />";
	}
	return $retorno;
}

function campoValorConfirmado($array){
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		$sql2 = "SELECT SUM(T174_Valor_Total) as total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}' AND T174_Flag_Gerar_OC='1'";
		$result2 = mysql_query($sql2);
		$linhaT174 = mysql_fetch_array($result2);
		//echo 'totalForn='.$totalForn.'<br />';
		if (!empty($linhaT174['total']) && is_numeric($linhaT174['total'])) {
			$totalForn = gCorrigeNumero($linhaT174['total']);
		} else {
			$totalForn = '0,00';
		}
		$retorno .= $totalForn."<br />";
	}
	
	return $retorno;
	
	// $sql="SELECT T173_Valor_Confirmado FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	// $resultado=mysql_query($sql);
	// $retorno='';
	// while($row=mysql_fetch_array($resultado)){
		// $retorno .= gCorrigeNumero($row['T173_Valor_Confirmado']) . "<br />";
	// }
	// return $retorno;
}

function colunaStatusNovo($array){
	if ($array['todosCampos']['T010_Flag_Cotacao_Finalizada'] == "S"){
		return "<font color='blue'><b>Conluído</b></font>";
	} else {
		return "<font color='red'>Pendente</font>";
	}
}

function validaCampoTipo($array){
	if($array['todosCampos']['T189_Flag_Tipo'] == 'C'){
		return "Apenas cotar";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'O'){
		return "Orçamento venda";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'P'){
		return "Pedido venda";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'E'){
		return "Reposição estoque";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'R'){
		return "<font color='red'>Cancelado</font>";
	}
}

function validaCampoComprar($array){
	global $g;
	$disabled="";
	$retorno = "<input onchange=\"est014CheckboxComprar(this, '{$array['todosCampos']['T189_Id']}', '{$g['divId']}')\" type='checkbox' id='T189_Flag_Comprar' title='Comprar'";
	if(empty($array['todosCampos']['T189_D009_Id']) || ($array['todosCampos']['T189_D009_Id'] == "") || ($array['todosCampos']['T189_D009_Id'] == null)){
		$disabled = "disabled='disabled'";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == "R"){
		$disabled = "disabled='disabled'";
	}
	if ($array['todosCampos']['T189_Flag_Comprar']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " {$disabled}/>";
}
	
function campoExclusao($array) {
	global $g;
    $resultado=mysql_query("select T005_Id from T005 where T005_T003_Id='{$array['todosCampos']['T003_Id']}'");
    $row=mysql_fetch_array($resultado);
    if(empty($row['T005_Id'])){	
		if($array['todosCampos']['T003_Flag_Perdido'] != 'S'){
				return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Motivo Cancelamento', '/ven/ven001/content/ven001ContentCancelamentoOrcamento/', '&acaoId={$array['todosCampos']['T003_Id']}', [400,300]); return false;">
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
		}
	}else{
		return "";
	}
}

function campoExclusaoPedido($array){
	global $g;
	if($array['todosCampos']['T005_Flag_Status']>0){
		return "";
	}else{
		//return gExcluirLinha($array, array('T005_Id', $g['divId']));
		return <<<EOT
		<a href="#" onclick="ven002excluirPedido('{$array['todosCampos']['T005_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}

function campoStatus($array) {
	global $g;
	if($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'P'){
		return "Pendente";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'F'){
		return "Finalizado";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'C'){
		return "Cancelado";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'V'){
		return "Vencido";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'A'){
		return "<font color=\"green\">Aprovado</font>";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'N'){
		return "<font color=\"red\">Negado - ".$array['todosCampos']['T003_Liberou_Alcada_Observacao_Nao_Liberado']."</font>";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'L'){
		return "Análise";
	}
}

function validaCampoPedido($array){
	global $g;
    if($array['todosCampos']['T003_Flag_Perdido'] == 'S'){
		// busca o motivo do cancelamento 
		$resultado=mysql_query("select * from T003 where T003_Id='{$array['todosCampos']['T003_Id']}'");
    	$row=mysql_fetch_array($resultado);
		$resultado=mysql_query("select * from D047 where D047_Id='{$row['T003_D047_Id']}'");
    	$row=mysql_fetch_array($resultado);    		
		return "<font color='red'><b>".$row['D047_Descricao_Motivo'].'</b> - '.$array['todosCampos']['T003_Observacao_Orcamento_Perdido']."</font>";
	}else{
		if ($array['todosCampos']['T005_Id']>0 and $array['todosCampos']['T005_Flag_Status']!=5) {
               return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T005_Id']}), [950,615]);\"> <font>".$array['todosCampos']['Pedido']."</font></a>";
        } else {
               return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T005_Id']}), [950,615]);\">{$array['todosCampos']['Pedido']}</a>";
        }
	}
}

function validaCampoStatusGridPrincipal($array){
	$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}' AND (T147_Status='P' OR T147_Status='E')";
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			return "<font color='red'><b>Serviços Pendentes</b></font>";
		}else{
			return "<font color='blue'><b>Serviços Concluídos</b></font>";
		}
	}else{
		return "-";
	}
}

function validaCampoStatusGridPrincipalPedido($array){
	$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}' AND (T147_Status='P' OR T147_Status='E')";
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			return "<font color='red'><b>Serviços Pendentes</b></font>";
		}else{
			return "<font color='blue'><b>Serviços Concluídos</b></font>";
		}
	}else{
		return "-";
	}
}

function excluirNFe($array) {
	global $g;
  	return <<<EOT
    		<a href="#" onclick="ven003excluirNFe('{$array['todosCampos']['T007_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
}

function validaTipo($array){
	$mDocumento[1]='NF';
	$mDocumento[2]='ROM';
	$mDocumento[3]='ACP';
	$mDocumento[4]='CF';
	$mDocumento[5]='D1';
	$mDocumento[6]='NFe';
	$mDocumento[7]='NFSe';
	if (empty($array['todosCampos']['T007_Flag_ACP'])) { $array['todosCampos']['T007_Flag_ACP'] = 1; }
	$retorno = $mDocumento[$array['todosCampos']['T007_Flag_ACP']]; 
	return $retorno;
}

function ValidaCampoDanfe($array){
	$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>DANFE</a>";
	/**
	* onClick dentro de <a>
	* onclick=\"javascript:abre_doc('../almoxarifado/gerar_danfe.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
	*/
	return $retorno;
}

function validaCampoNF($array) {
	$retorno = "<a href=\"#\"  onclick=\"javascript:abre_doc('/hardness/nota_emitida/nf_emitida.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>NF</a>";
	/**
	* onClick dentro de <a>
	* onclick=\"javascript:abre_doc('../nota_emitida/nf_emitida.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
	*/
	return $retorno;
}

function validaCampoACP($array) {
	$retorno="<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>ACP</a>";
	/**
	* onClick entro de <a>
	* onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/imprimir_acp.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
	*/
	return $retorno;
}

function validaCampoXML($array){
	global $g;
	$cAno_Mes_NFe=substr($array['todosCampos']['T007_Data_Emissao'],0,7);
	$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
	
	//return "Ano: " . $cAno_Mes_NFe . "<br />Diretório: " . $Diretorio_NFe; 
	
	 if (!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) && $array['todosCampos']['T007_Flag_ACP']==6)
	 {
		$retorno = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">";
        if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']))
         {
             if (file_exists("$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['todosCampos']['T007_Numero_Protocolo_Nfe'].".xml"))
             {
                 $retorno .= "<font class=fonte><u>XML {$array['todosCampos']['T007_Versao_Nfe']}</u></font></a>";
             }
             else
             {
             	 $retorno .="</a>";
                 $retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</font>";
             }
         }
	return $retorno;
	}
}

function validaCampoEmail($array){
	global $g;
	$retorno = "";
	if(!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) and $array['todosCampos']['T007_Flag_ACP']==6){
		/**
		* onclick=\"javascript:parent.AbrirFormularioEmail('$mT005[T007_Id]')\"
		*/
		if($array['todosCampos']['T007_Data_Envio_XML']!='0000-00-00 00:00:00' and $array['todosCampos']['T007_Data_Envio_XML']!=''){
			$retorno = "<a href='#'><li title='E-mail enviado em: '".$array['todosCampos']['T007_Data_Envio_XML']."><span class='ui-icon ui-icon-check'></span></li></a>";

			/**
			* No attr title do <a> acima, era chamada a seguinte função
			* title='E-mail enviado em: ".troca_datahora_dma($mT005[T007_Data_Envio_XML])."
			*/
			return $retorno;
		}else{
			//$retorno = "<a href=\"#\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></li></a>";
			$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]); return false;\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></li></a>";
			return $retorno;
		}
	}
}

function chamaCampo($array) {
	$modelo = ($array['todosCampos']['T007_Flag_ACP'] == '6') ? '55' : '65';
	$cClausula  = " select ";
	$cClausula .= " T127_Codigo_Retorno, ";
	$cClausula .= " T127_Retorno_Sefaz ";
	$cClausula .= " from T127 ";
	$cClausula .= " where T127_Numero_Nota_Fiscal='{$array['todosCampos']['T007_Numero_Nota_Fiscal']}' ";
	$cClausula .= " and T127_C004_Id='{$array['todosCampos']['T007_C004_Id']}' ";
	$cClausula .= " and T127_Modelo='{$modelo}' ";
	$cClausula .= " order by T127_Data_Transmicao desc  ";
	$cClausula .= " limit 1  ";
	$cSql_T127=mysql_query($cClausula);
	$mT127=mysql_fetch_array($cSql_T127);
	return $mT127['T127_Retorno_Sefaz'];   
}

function ValidaChaveAcesso($array){
	$retorno = "";
	if($array['todosCampos']['T007_Flag_Inutilizado']=='S' and empty($array['todosCampos']['T007_Protocolo_Inutilizacao'])){
		$retorno = "<u>Inutilize essa numeração</u>";
		return $retorno;
	}elseif($array['todosCampos']['T007_Flag_Inutilizado']=='S' and !empty($array['todosCampos']['T007_Protocolo_Inutilizacao'])){
		$retorno = "<u>Protocolo de inutilização: ".$array['todosCampos']['T007_Protocolo_Inutilizacao']."</u>";
		return $retorno;
	}else{
		$retorno = $array['todosCampos']['T007_Chave_Acesso_Nfe'];
		return $retorno;
	}
}

function mostraImagemCadastro($array){
	global $g;
	$novoIdTelaForm = uniqid();
	if($array['todosCampos']['D024_Flag_Pre_Cadastro'] == 'N'){
		$imagem = <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Editar Cadastro', '/cad/cad001/content/cad001contentCadastro/','&acaoId={$array['todosCampos']['D024_Id']}', [950,553]);">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-person" title="Editar Cadastro"></span>
			   </div>
			</span>
EOT;
	}else{
		$imagem	= "";
	}
	return $imagem;
}

function mostraImagemUltimoContato($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = gCorrigeData($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Agenda', '/cad/cad001/content/cad001ContentAbreAgenda/','&D024_Id={$array['todosCampos']['D024_Id']}', [900,550]);">
			<div class="icone ui-state-default ui-corner-all">
				<span class="ui-icon ui-icon-comment" title="Abrir Agenda"></span>
			</div>
			{$retorno}
		</span>
EOT;
	
	return $imagem;
}

function mostraImagemDoc($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Documentos', '/cad/cad001/content/cad001ContentAbreDocumentos/','&D024_Id={$array['todosCampos']['D024_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-document" title="Documentos"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function validaCampoCancelar($array){
	if($array['todosCampos']['T007_Flag_Inutilizado']=="N"){
		
		if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
			
			$retorno = "<b>Cancelada</b>";
			
			return $retorno;
			
		}else{
			//$retorno="<a href=\"#\" onclick=\"javascript:cancela_nfe('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T005_Id']}');\" style='cursor:pointer'>Cancelar</a>";
			$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id=' + encodeURIComponent({$array['todosCampos']['T007_Id']}),0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Cancelar</a>";
			/**
			* onClick dentro de <a>
			* onclick=\"javascript:cancela_nfe('../nota_emitida/formulario_cancelar_nota.php?T007_Id=$mT005[T007_Id]','NF $mT005[T005_Id]');\"
			*/
			return $retorno;
		}
	} else {
		return "";
	}
}

function validaCampoCancelarNFRec($array){
		global $g;
		if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
			$retorno = "";
			$retorno = "<u>Cancelada</u>";
			return $retorno;
		}elseif($array['todosCampos']['T013_Flag_Travar_Nota']=='S'){
			$retorno="";
			$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness/nota_recebida/cancelar_nf.php?T013_Id={$array['todosCampos']['T013_Id']}&Nova_Janela=S','NF {$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Cancelar</a>";
			return $retorno;
		}else{
			//$retorno = "";
			//$retorno = "<a href=\"#\" onclick=\"/est/est004/grid_func-ajax/excluirNota/?T013_Id='" . urlencode($array['todosCampos']['T013_Id']) . "'\">Excluir</a>";
			//$retorno = "<a href=\"#\"  style='cursor:pointer'>Excluir</a>";
			return <<<EOT
				<a href="#" onclick="EST004ExcluirNota('{$array['todosCampos']['T013_Id']}', '{$g['divId']}')">Excluir</a>
EOT;
		}
	}

function validaCampoXMLNFRec($array){
	// echo "<td align=center bgcolor='$cor' class='td_itens' onclick=\"javascript:parent.AbrirXML('$mT013[T013_Id]')\" style='cursor:pointer'>";

	global $confUsuario, $g;
	if (!empty($array['todosCampos']['T013_Chave_Acesso_Nfe'])){
		if (!empty($array['todosCampos']['T013_Numero_Protocolo_Nfe'])){
                //if (file_exists("dados_usuarios/{$confUsuario['dbDatabase']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
                if (file_exists("{$g['pathDadosAntigo']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
				$retorno = "<a href=\"/est/est004/grid_func-ajax/downloadxml/?ajax=true&T013_Id=" . urlencode($array['todosCampos']['T013_Id']) . "\">XML</a>";
			} else {
				$retorno = "<a title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</a>";
			}
			return $retorno;
		}
		return '';
	}else{        
		return '';
	}
	 return false;		
}

function validaCampoDanfeNFRec($array){
	$retorno = "";
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id={$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>DANFE</a>";
	return $retorno;
}

function campoExclusaoFin($array) {
	global $g;
	if ($array['todosCampos']['T007_Id'] > 0){
		return "&nbsp";
	} else {
    	return <<<EOT
    		<a href="#" onclick="fin001CampoExclusao('{$array['todosCampos']['T002_Id']}','{$array['todosCampos']['T002_T035_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}
function validaCampoNFOrcamentos($array){
    $retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>{$array['todosCampos']['NF']}</a>";
    /**
    * onClick dentro de <a>
    * onclick=\"javascript:abre_doc('../almoxarifado/gerar_danfe.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
    */
    return $retorno;
}

function campoCancelamento($array){
	global $g;

	if(empty($array['todosCampos']['T005_Flag_Status']) || ($array['todosCampos']['T005_Flag_Status']==9) || ($array['todosCampos']['T005_Flag_Status']==25)){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Pedido', '/ven/ven002/content/ven002contentCancelarPedido/', '&T005_Id={$array['todosCampos']['T005_Id']}', [350,200]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar pedido"></span>
		</a>
EOT;
	}
}

?>