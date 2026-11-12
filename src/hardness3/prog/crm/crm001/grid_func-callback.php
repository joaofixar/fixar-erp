<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */
//Carrega funções do ajax
require_once('prog/crm/crm001/grid_func-js.php');
require_once('prog/ven/ven002/grid_func-js.php');

function crm001CampoNFPedido($array) {
	global $g;
	$T005_Id = (int) $array['todosCampos']['T005_Id'];
	$valorNaDanfe = $g['C031']['exibirValorNFDANFE'];
	$notas = array();
	$sql = mysql_query("SELECT DISTINCT NF.T007_Id, NF.T007_Numero_Nota_Fiscal, NF.T007_Flag_ACP, NF.T007_D024_Id
		FROM T007 NF
		WHERE IFNULL(NF.T007_Flag_Cancelada,'N') != 'S'
		AND IFNULL(NF.T007_Flag_Inutilizado,'N') != 'S'
		AND (NF.T007_T005_Id = {$T005_Id} OR EXISTS (
			SELECT 1 FROM T008 NFI
			INNER JOIN T006 PI ON PI.T006_Id = NFI.T008_T006_Id
			WHERE NFI.T008_T007_Id = NF.T007_Id AND PI.T006_T005_Id = {$T005_Id}
		)) ORDER BY NF.T007_Id");
	while ($res = mysql_fetch_array($sql)) {
		$T007_Id = (int) $res['T007_Id'];
		$numero = trim($res['T007_Numero_Nota_Fiscal']) !== '' ? $res['T007_Numero_Nota_Fiscal'] : "ID {$T007_Id}";
		$Query = md5($res['T007_D024_Id'].'|'.$T007_Id);
		if ($res['T007_Flag_ACP'] == '3') {
			$notas[] = "<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id={$T007_Id}','NF {$T007_Id}');\" style=\"cursor:pointer\">{$numero}</a>";
		} else {
			$notas[] = "<a href=\"#\" onclick=\"window.open('/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF={$valorNaDanfe}&T007_Id={$T007_Id}','DANFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');\" style=\"cursor:pointer\">{$numero}</a>";
		}
	}
	return implode(', ', $notas);
}

function campoExclusao($array) {
	global $g;
    if(empty($array['todosCampos']['T005_Id'])){	
		if($array['todosCampos']['T003_Flag_Perdido'] != 'S'){
			return <<<EOT
			<a href="#" onclick="ven001CampoExclusao('{$array['todosCampos']['T003_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir orçamento"></span>
			</a>
EOT;
		}
	}else{
		return "";
	}
}
function campoCancelamentoOrcamento($array) {
	global $g;
    if(empty($array['todosCampos']['T005_Id'])){	
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

function enviarNotificacao($array)
{
    global $g;
   
	return <<<EOT
		<a href="#" onclick="crm001EnviarNotificacao('{$array['todosCampos']['D181_Id']}','{$g['divId']}')">
			Enviar
		</a>
EOT;
    
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
		$resultado=mysql_query("select * from D047 where D047_Id='{$array['todosCampos']['T003_D047_Id']}'");
    	$row=mysql_fetch_array($resultado);
		return "<font color='red'><b>".$row['D047_Descricao_Motivo'].'</b> - '.$array['todosCampos']['T003_Observacao_Orcamento_Perdido']."</font>";
	}else{
		$return = '';
		$pedidos = explode("|", $array['todosCampos']['Pedido']);
		foreach ($pedidos as $pedido){

			$pedido = explode("-",$pedido);
			$res = array();
			$res['T005_Id']               = $pedido[0];
			$res['T005_Flag_Status']      = $pedido[1];
			$res['T005_Valor_Total']      = number_format($pedido[2],2,',','.');
			$res['T005_T005_Id_Agrupado'] = $pedido[3];

			if ($res['T005_Id']>0) {
				if($res['T005_T005_Id_Agrupado']>0){
					$sql1 = mysql_query("SELECT T005_Id, 
												T005_Nome_Status as T005_Flag_Status, 
												T005_Valor_Total,
												T005_T005_Id_Agrupado 
										   FROM T005 
										  WHERE T005_Id = '{$res['T005_T005_Id_Agrupado']}'");

					$res1 = mysql_fetch_array($sql1);
					$return .= "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$res1['T005_Id']}), [950,615]);\"><font>".$res1['T005_Id'].' ('.$res['T005_Id'].') - '.$res1['T005_Flag_Status'].' - '.gCorrigeNumero($res1['T005_Valor_Total'])."</font></a><br />";
				} else {
					$return .= "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$res['T005_Id']}), [950,615]);\">{$res['T005_Id']} - {$res['T005_Flag_Status']} - {$res['T005_Valor_Total']}</a><br />";
				}
			}
		}
		$return = substr($return, 0, -6);
		return $return;
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
	$T199=mysql_query("SELECT T199_Id FROM T199 WHERE T199_T007_Id='{$array['todosCampos']['T007_Id']}'");
	$mT199=mysql_fetch_array($T199);
	$cce = mysql_num_rows($T199) > 0 ? '<Br /><span style="background:yellow;color:#000;"><small>CC-e</small></style>' : '';
	$mDocumento[1]='NF';
	$mDocumento[2]='ROM';
	$mDocumento[3]='ACP';
	$mDocumento[4]='CF';
	$mDocumento[5]='D1';
	$mDocumento[6]='NF-e';
	$mDocumento[7]='NFS-e';
	$mDocumento[8]='NFC-e';
	if (empty($array['todosCampos']['T007_Flag_ACP'])) { $array['todosCampos']['T007_Flag_ACP'] = 1; }
	$retorno = $mDocumento[$array['todosCampos']['T007_Flag_ACP']].$cce; 
	return $retorno;
}

function ValidaCampoDanfe($array){
	global $g;
	$protocoloNF = $array['todosCampos']['T007_Numero_Protocolo_Nfe'];
	$contingenciaOffline = $array['todosCampos']['T007A_Flag_Contingencia_Offline'] ?: 'N';
	$valorNaDanfe = $g['C031']['exibirValorNFDANFE'];
	$T007_Id = $array['todosCampos']['T007_Id'];
	$T007_D024_Id = $array['todosCampos']['T007_D024_Id'];
	$T007_Flag_ACP = $array['todosCampos']['T007_Flag_ACP'];
	$Query = md5($T007_D024_Id.'|'.$T007_Id);
	if(isset($protocoloNF)|| $contingenciaOffline == 'S') {
		//$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$array['todosCampos']['T007_Id']}&vlrNF={$valorNaDanfe}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>DANFE</a>";
		if($T007_Flag_ACP==8){
			//AÇÃO PARA DEFINIR SE SERÁ ABERTO A IMPRESSÃO HTML E A IMPRESSÃO PDF
			$retorno = "<a href=\"#\" onclick=\"abrirDialogImpressaoNFCe('{$T007_Id}', '{$Query}', '{$valorNaDanfe}', '{$contingenciaOffline}');\">DANFE</a>";
		}else{
			$retorno = "<a href=\"#\" onclick=\"window.open('/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF={$valorNaDanfe}&T007_Id={$T007_Id}','DANFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');\" style='cursor:pointer'>DANFE</a>";
		}
	} else {
		$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/nota_emitida2/gerar_pre_danfe.php?T007_Id={$array['todosCampos']['T007_Id']}&Fase=2&vlrNF={$valorNaDanfe}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>DANFE</a>";				
	}
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
	global $g;
	$T007_Numero_Nota_Fiscal = $array['todosCampos']['T007_Numero_Nota_Fiscal'];
	$modelo = ($array['todosCampos']['T007_Flag_ACP'] == '6') ? '55' : '65';
	$sqlT127 = mysql_query("SELECT * FROM T127 WHERE T127_Numero_Nota_Fiscal = '{$T007_Numero_Nota_Fiscal}' AND T127_C004_Id = '{$g['empresaAtual']}' AND T127_Modelo = '{$modelo}' ORDER BY T127_Id DESC LIMIT 1");
	$T127 = mysql_fetch_array($sqlT127);
	$T127_Codigo_Retorno = $T127['T127_Codigo_Retorno'];
	if($array['todosCampos']['T007_Flag_Inutilizado']=="N"){
		if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
			if($T127_Codigo_Retorno == '301' || $T127_Codigo_Retorno == '302'){
				$retorno = "<b>Denegada</b>";
			} else {
				if (empty($array['todosCampos']['T007_Numero_Protocolo_Cancelamento_Nfe']) && ($array['todosCampos']['T007_Flag_ACP']==6)){
					$retorno="<a href=\"#\" onclick=\"crm001EstornarCancelarNotaFiscal('{$array['todosCampos']['T007_Id']}', '{$g['divId']}', '{$g['divIdRoot']}'); return false;\">Estornar</a>";
				} else if(!empty($array['todosCampos']['T007_Observacao_Cancelamento'])){
						$arrayMotivoCancelamento = explode("Motivo do Cancelamento :", $array['todosCampos']['T007_Observacao_Cancelamento']);
						$motivoCancelamento = @$arrayMotivoCancelamento[1];
						$motivoCancelamento = (!empty($motivoCancelamento)) ? $motivoCancelamento : "";
						$retorno = $motivoCancelamento;
				}else{
					$retorno = "<b>Cancelada</b>";
				}					
			}
			return $retorno;
		} else {
			if($T127_Codigo_Retorno == '301' || $T127_Codigo_Retorno == '302'){					
				$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness3/old/lib/denegar_nfe.php?T007_Id=' + encodeURIComponent({$array['todosCampos']['T007_Id']}),0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Denegar</a>";
			} else {
				$retorno="<a href=\"#\" onclick=\"crm001cancelarNotaFiscal('{$array['todosCampos']['T007_Id']}', '{$g['divId']}', '{$g['divIdRoot']}'); return false;\">Cancelar</a>";
				//$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id=' + encodeURIComponent({$array['todosCampos']['T007_Id']}),0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Cancelar</a>";
				//$retorno="<a href=\"#\" onclick=\"javascript:cancela_nfe('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T005_Id']}');\" style='cursor:pointer'>Cancelar</a>";
			}
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
		 	$retorno='';
            if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']))
             {
     			$retorno = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">";
                 if (file_exists("$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['todosCampos']['T007_Numero_Protocolo_Nfe'].".xml"))
                 {
                     $retorno .= "<font class=fonte><u>XML {$array['todosCampos']['T007_Versao_Nfe']}</u></font></a>";
                 }
                 else
                 {
                 	 $retorno .="</a>";
                     $retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</font>";
                 }
             } else {
     			$retorno = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXMLAssinado/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">";
                 if (file_exists("$Diretorio_NFe/xml/assinado/NFe".$array['todosCampos']['T007_Chave_Acesso_Nfe'].".xml"))
                 {
                     $retorno .= "<font class=fonte style='color: var(--BGVermelho)'><u>XML {$array['todosCampos']['T007_Versao_Nfe']}</u></font></a>";
                 } else if (file_exists("$Diretorio_NFe/xml/assinado/".$array['todosCampos']['T007_Numero_Nota_Fiscal'].'_'.$array['todosCampos']['T007_C004_Id'].".xml")) {
                     $retorno .= "<font class=fonte style='color: var(--BGVermelho)'><u>XML {$array['todosCampos']['T007_Versao_Nfe']}</u></font></a>";
                 }
                 else
                 {
                 	 $retorno .="</a>";
                     $retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>-</font>";
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
			<!--
			<div class="icone ui-state-default ui-corner-all">
				<span class="ui-icon ui-icon-comment" title="Abrir Agenda"></span>
			</div>
			-->
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

function crm001_Dia_Semana($array) {
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

function plataformaEcommerce($array){
	global $Saldo_Projetado; 
	$Plataforma = $array['todosCampos']['T005_Flag_Plataforma_Ecommerce'];
	if($Plataforma==1){
		return "PLUGG-TO";
	}else if($Plataforma==2){
		return "TRAY";
	}else if($Plataforma==3){
		return "MAGENTO";
	}else if($Plataforma==4){
		return "MAGENTO";
	}else if($Plataforma==5){
		return "MAGENTO";
	}else if($Plataforma==6){
		return "WEBSTORM";
	}else{
		return " - ";
	}

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

function campoCancelamento($array){
	global $g;
	if(empty($array['todosCampos']['T005_Flag_Status']) || ($array['todosCampos']['T005_Flag_Status']==9) || ($array['todosCampos']['T005_Flag_Status']==25) || ($array['todosCampos']['T005_Flag_Status']==14) || ($array['todosCampos']['T005_Flag_Status']==15) || (!empty($array['todosCampos']['T005_Numero_NF_Devolucao'])) ){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Pedido', '/ven/ven002/content/ven002contentCancelarPedido/', '&T005_Id={$array['todosCampos']['T005_Id']}', [450,275]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar pedido"></span>
		</a>
EOT;
	}
}

function validaCampoPreco1($array){
	$array['todosCampos']['D049_Preco_1'] = gCorrigeNumero($array['todosCampos']['D049_Preco_1']);
	if($array['todosCampos']['D049_Flag_Promocao'] == "S"){
		return "<font color='darkgreen'><b>{$array['todosCampos']['D049_Preco_1']}</b></font>";
	} else {
		return $array['todosCampos']['D049_Preco_1'];
	}
}
function validaCampoPreco2($array){
	$array['todosCampos']['D049_Preco_2'] = gCorrigeNumero($array['todosCampos']['D049_Preco_2']);
	if($array['todosCampos']['D049_Flag_Promocao'] == "S"){
		return "<font color='darkgreen'><b>{$array['todosCampos']['D049_Preco_2']}</b></font>";
	} else {
		return $array['todosCampos']['D049_Preco_2'];
	}
}
function validaCampoPreco3($array){
	$array['todosCampos']['D049_Preco_3'] = gCorrigeNumero($array['todosCampos']['D049_Preco_3']);
	if($array['todosCampos']['D049_Flag_Promocao'] == "S"){
		return "<font color='darkgreen'><b>{$array['todosCampos']['D049_Preco_3']}</b></font>";
	} else {
		return $array['todosCampos']['D049_Preco_3'];
	}
}

function validaCampoEnviarAgenda($array){
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$D024_Flag_Enviar_Agenda = $array['todosCampos']['D024_Flag_Enviar_Agenda'];

	$retorno = "<input onclick=\"crm001CheckBoxEnviarAgenda(this, '{$D024_Id}', '{$g['divId']}')\" type='checkbox' id='D024_Flag_Enviar_Agenda' title='Enviar Agenda'";
	if($D024_Flag_Enviar_Agenda=='S'){
		$retorno .="checked='checked'";
	}
	return $retorno .= " />";
}

function validaHoraEnvioEmail($array){
	global $g;

	if($array['todosCampos']['T139_Id'] > 0){
		$dataHoraEnvioPrimeiroEnvio = gCorrigeData($array['todosCampos']['T003A_Data_Envio_Primeiro_Email']);
		$dataHoraEnvio 			    = gCorrigeData($array['todosCampos']['T139_Data_Hora']);		
		$retorno       				= "<a href='#'><li title='E-mail enviado em: $dataHoraEnvio \nPrimeiro envio em: $dataHoraEnvioPrimeiroEnvio'>Ok</span></li></a>";
		
		return $retorno;
	}else{
		return '';
	}
}

function maiorPrevisao($array){
	global $g;

	$T005_Id           = $array['todosCampos']['T005_Id'];
	$statusPedido      = $array['todosCampos']['T005_Flag_Status'];
	$percentualCompra  = $array['todosCampos']['T005A_Percentual_Ordem_Compra'];
	$percentualEstoque = $array['todosCampos']['T005_Percentual_Estoque_Disponivel'];	
	$maiorPrevisao     = '0000-00-00';
	
	if(($statusPedido <= '0' || $statusPedido == '') && $percentualCompra > 0 && $percentualEstoque < 100){

		$T006 = mysql_query("SELECT T006_D009_Id,
								    T006_Quantidade_OC
						       FROM T006
						      WHERE T006_T005_Id ='{$T005_Id}'");

		while($mT006 = mysql_fetch_array($T006)){

			if($mT006['T006_Quantidade_OC'] <= 0 ){
				continue;
			}

			$previsao = mysql_query("SELECT T075_Data_Previsao AS previsaoEntrega
									   FROM T055
								  LEFT JOIN T075 ON T075_Id = T055_T075_Id
									  WHERE T075_C004_Id = '{$g['empresaAtual']}'
										AND T055_D009_Id = '{$mT006['T006_D009_Id']}'
										AND IFNULL(T075_Data_Estoque,'0000-00-00')='0000-00-00' 
										AND T075_Data_Previsao > 0 
										AND T075_Data_Previsao <= DATE(DATE_ADD(NOW(), INTERVAL {$g['C031']['diasMedioPrazo']} DAY))
								   ORDER BY T075_Data_Previsao ASC LIMIT 1");
			
			if(mysql_num_rows($previsao) <= 0){

				$previsao = mysql_query("SELECT T295_Data_Previsao AS previsaoEntrega
										   FROM T296
									  LEFT JOIN T295 ON T295_Id = T296_T295_Id
										  WHERE T295_C004_Id = '{$g['empresaAtual']}'
											AND T296_D009_Id = '{$mT006['T006_D009_Id']}'
											AND (T295_Flag_Status = '3' OR T295_Flag_Status = '4')
											AND T296_Quantidade_Saldo > 0
											AND T296_Flag_Finalizar_Compra != 'S'
											AND T295_Data_Previsao > 0 
											AND T295_Data_Previsao <= DATE(DATE_ADD(NOW(), INTERVAL {$g['C031']['diasMedioPrazo']} DAY))			
									   ORDER BY T295_Data_Previsao ASC LIMIT 1");
			}		
			
			$mprevisao = mysql_fetch_array($previsao);

			$previsaoEntrega = $mprevisao['previsaoEntrega'];

			if($maiorPrevisao < $previsaoEntrega){
				$maiorPrevisao = $previsaoEntrega;
			}
		}
	}

	$maiorPrevisao = ($maiorPrevisao != '0000-00-00') ? gCorrigeData($maiorPrevisao) : '-' ;

	return $maiorPrevisao;
}

?>
