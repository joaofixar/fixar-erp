<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

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
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T005_Id']}), [950,615]);\"> <font style='font-weight:bold'>".$array['todosCampos']['Pedido']."</font></a>";
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

/**
 * 
 */
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
	$cClausula  = " select ";
	$cClausula .= " T127_Codigo_Retorno, ";
	$cClausula .= " T127_Retorno_Sefaz ";
	$cClausula .= " from T127 ";
	$cClausula .= " where T127_Numero_Nota_Fiscal='{$array['todosCampos']['T007_Numero_Nota_Fiscal']}' ";
	$cClausula .= " and T127_C004_Id='{$array['todosCampos']['T007_C004_Id']}' ";
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

function erp001_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['R001_Data'];
	$data = gPegaDiaSemana($data);
	if($data == "Sábado"){
		$data = substr($data, 0, 4);
	} else {
		$data = substr($data, 0, 3);
	}
	return $data;
}

function retornaAno($array) {
	$ano = $array['campoAtual'];
	$ano = substr($ano, 0, 4);
	return $ano;
}

function retornaMes($array) {
	$ano = $array['campoAtual'];
	$ano = gCorrigeData($ano);
	$ano = substr($ano, 3, 11);
	return $ano;
}

function saldoProjetado($array){
	global $Saldo_Projetado; 
	$Valor_Receber = $array['todosCampos']['R002_Valor_Receber'];
	$Valor_Receber_Projecao = $array['todosCampos']['R002_Valor_Receber_Projecao'];
	$Valor_Pagar = $array['todosCampos']['R002_Valor_Pagar'];
	$Valor_Pagar_Projecao = $array['todosCampos']['R002_Valor_Pagar_Projecao'];
	$Saldo_Projetado += ($Valor_Receber + $Valor_Receber_Projecao) - ($Valor_Pagar + $Valor_Pagar_Projecao);
	return $Saldo_Projetado;
}

function saldoRealizado($array){
	global $Saldo_Realizado; 
	$Valor_Recebido = $array['todosCampos']['R002_Valor_Recebido'];
	$Valor_Pago = $array['todosCampos']['R002_Valor_Pago'];
	$Saldo_Realizado += ($Valor_Recebido + $Valor_Pago);
	return $Saldo_Realizado;
}

function saldoReal($array){
	global $Saldo_Real;
	$Data = $array['todosCampos']['R002_Data'];
	$Valor_Receber = $array['todosCampos']['R002_Valor_Receber'];
	$Valor_Receber_Projecao = $array['todosCampos']['R002_Valor_Receber_Projecao'];
	$Valor_Pagar = $array['todosCampos']['R002_Valor_Pagar'];
	$Valor_Pagar_Projecao = $array['todosCampos']['R002_Valor_Pagar_Projecao'];
	if($Data >= date("Y-m-d")){
		$Saldo_Real += ($Valor_Receber + $Valor_Receber_Projecao) - ($Valor_Pagar + $Valor_Pagar_Projecao);
	}
	return $Saldo_Real;
}

function retornaDia($array) {
	global $g;
	$data=$array['todosCampos']['R002_Data'];
	$data = gPegaDiaSemana($data);
	if($data == "Sábado"){
		$data = substr($data, 0, 4);
	} else {
		$data = substr($data, 0, 3);
	}
	return $data;
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