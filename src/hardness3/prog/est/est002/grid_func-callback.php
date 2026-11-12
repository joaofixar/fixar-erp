<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/

function campoOpcoes($array){
	 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Estoquista', '/est/est002/content/est002ImprimirPedido/','&T005_Id={$array['todosCampos']['T005_Id']}', [450,150]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Imprima o pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="est002RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est002/content/est002ContentEtiquetas/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function campoOpcoesImpressao($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$imagem = '';
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem .= <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Estoquista', '/est/est002/content/est002ImprimirPedido/','&T005_Id={$array['todosCampos']['T005_Id']}', [450,170]);">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-circle-arrow-e" title="Imprima o pedido"></span>
			   </div>
			</span>
EOT;
	}
	$imagem .= <<<EOT
		<span onclick="est002RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem .= <<<EOT
			<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
			   </div>
			</span>
EOT;
	}
	return $imagem;
}

function validaCampoCheckboxRomaneio($array){
	include('prog/est/est002/grid_func-js.php');
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	$T007_Flag_Imprimir_Romaneio = $array['todosCampos']['T007_Flag_Imprimir_Romaneio'];

	$retorno = "<input onclick=\"est002validaCampoCheckboxRomaneio(this, '{$T007_Id}', '{$g['divId']}');\" type='checkbox' id='T007_Flag_Imprimir_Romaneio'";
	if ($T007_Flag_Imprimir_Romaneio=='S'){
		$retorno .="checked='checked'";
	}
    return $retorno .= " />";
}

function validaCampoCheckboxEnvioEmail($array){
	include('prog/est/est002/grid_func-js.php');
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	$T007_Data_Envio_XML_Transportadora = $array['todosCampos']['T007_Data_Envio_XML_Transportadora'];
	$T007_Flag_Enviar_XML_Transportadora = $array['todosCampos']['T007_Flag_Enviar_XML_Transportadora'];
	if($T007_Data_Envio_XML_Transportadora!="0000-00-00"){
		return gCorrigeData($T007_Data_Envio_XML_Transportadora);
	}else{
		$retorno = "<input onclick=\"est002validaCampoCheckboxEnvioEmail(this, '{$T007_Id}', '{$g['divId']}');\" type='checkbox' id='T007_Flag_Enviar_XML_Transportadora'";
		if ($T007_Flag_Enviar_XML_Transportadora=='S'){
			$retorno .="checked='checked'";
		}
	    return $retorno .= " />";
	}
	
}

function correioPostaEtiqueta($array){
	include('prog/est/est002/grid_func-js.php');
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	$T005_Id = $array['todosCampos']['T005_Id'];
	$T268_Codigo_Rastreio = $array['todosCampos']['T268_Codigo_Rastreio'];

	if(!empty($T268_Codigo_Rastreio)){
		$mensagem = "Confirma impressão de etiqueta do pedido ".$T005_Id."?";
	}else{
		$mensagem = "Confirma postagem no correio e impressão de etiqueta do pedido ".$T005_Id."?";;
	}

 	return  <<<EOT
		<span onclick="est002CorreioPostaEtiqueta('{$T007_Id}', '{$g['divId']}','{$mensagem}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir etiqueta correio"></span>
		   </div>
		</span>
EOT;
	
}

function campoOpcoesSeparacao($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = '';
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem .= <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Confirmar Sepração do Pedido', '/est/est002/content/est002SeparacaoPedido/','&acaoId={$array['todosCampos']['T005_Id']}&T005_Id={$array['todosCampos']['T005_Id']}', [780,550]);">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-circle-arrow-e" title="Separando o Pedido"></span>
			   </div>
			</span>
EOT;
	}
	$imagem .= <<<EOT
		<span onclick="est002RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem .= <<<EOT
			<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
			   </div>
			</span>
EOT;
	}
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est002/content/est002ContentEtiquetas/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
	
}

function campoOpcoesConferencia($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Faturamento', '/est/est002/content/est002FaturamentoPedido/','&acaoId={$array['todosCampos']['T005_Id']}&T005_Id={$array['todosCampos']['T005_Id']}', [500,400]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Conferência Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="est002RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-search"  title="Ver Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est002/content/est002ContentEtiquetas/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function campoOpcoesEmissaoNF($array){
	 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Estoquista', '/est/est002/content/est002ImprimirPedido/','&T005_Id={$array['todosCampos']['T005_Id']}', [500,300]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Emitir NF"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est002/content/est002ContentEtiquetas/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function campoOpcoesAguardandoEntrega($array){
	 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="est002ConfirmarEntrega('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Confirmar Entrega"></span>
		   </div>
		</span>	
EOT;
	$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est002/content/est002ContentEtiquetas/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function campoOpcoesGeral($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$imagem = '';
	$imagem .= <<<EOT
		<span onclick="est002RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem .= <<<EOT
			<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
			   </div>
			</span>
EOT;
	}
	return $imagem;
}

function campoCFOP($array){
	$retorno = "{$array['todosCampos']['D006_Codigo_CFOP']} - ({$array['todosCampos']['D006_Descricao_Comercial']})";
	return $retorno;
}

function ValidaCampoDanfe($array){
	$retorno = "";
	$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>DANFE</a>";
	/**
	* onClick dentro de <a>
	* onclick=\"javascript:abre_doc('../almoxarifado/gerar_danfe.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
	*/
	return $retorno;
}


function validaCampoXML($array){
	if (!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) and $array['todosCampos']['T007_Flag_ACP']==6){
		$retorno = "";
		$retorno = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">XML</a>";
        /*if (!empty($mT005[T007_Numero_Protocolo_Nfe]))
        {
        if (file_exists("$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$mT005[T007_Numero_Protocolo_Nfe].".xml"))
        {
        $retorno = "<font class=fonte><u>XML $mT005[T007_Versao_Nfe]</u></font>";
        }
        else
        {
        $retorno = "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</font>";
        }
        }*/
      	return $retorno;
		}
}

function retornaImpressaoRomaneio($array){
	
	$T005_T275_Id = $array['todosCampos']['T005_T275_Id'];
	log("T005_T275_Id:".$T005_T275_Id);

	if($T005_T275_Id>0){
		return <<<EOT
			<b><a href="#" onclick="nova_janela('/est/est002/outro/est002ImprimirRomaneioDeColeta/?ajax=true&T275_Id=' + encodeURIComponent('{$T005_T275_Id}') ,0,0,850,700,'yes','no','no','no','yes','no','Impressao_Romaneio');">{$T005_T275_Id}</a></b>
EOT;
	}else{
		return " - ";
	}
}
function validaCampoEmail($array){
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
				$retorno = "<a href=\"#\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></li></a>";
				return $retorno;
				}
	}
}

function validaCampoCancelar($array){
		if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
			$retorno = "";
			$retorno = "<u>Cancelada</u>";
			return $retorno;
		}else{
			$retorno="";
			$retorno="<a href=\"#\" onclick=\"javascript:cancela_nfe('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T005_Id']}');\" style='cursor:pointer'>Cancelar</a>";

			/**
			* onClick dentro de <a>
			* onclick=\"javascript:cancela_nfe('../nota_emitida/formulario_cancelar_nota.php?T007_Id=$mT005[T007_Id]','NF $mT005[T005_Id]');\"
			*/
			return $retorno;
		}
	}
	
function campoOC($array){
	 if ($array['todosCampos']['OC']>0)
     {
	 	$retorno = number_format($array['todosCampos']['OC'],2,',','.');
     }
     else
	 {
     	$retorno = "&nbsp;";
		}
	 return $retorno;
}

function validaCampoRetirante($array){
	if(!empty($array['todosCampos']['T175_C007_Id'])){
		$sql = "SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$array['todosCampos']['T175_C007_Id']}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_assoc($result);
		return $linha['C007_Primeiro_Nome'];
	}else if(!empty($array['todosCampos']['T175_D109_Id'])){
		$sql = "SELECT D109_Nome FROM D109 WHERE D109_Id='{$array['todosCampos']['T175_D109_Id']}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_assoc($result);
		return $linha['D109_Nome'];
	}
}

function calculaQteEntregue($array){
	$sql = "SELECT SUM(T179_Quantidade_Entregue) FROM T179 WHERE T179_T006_Id='{$array['todosCampos']['T006_Id']}'";

		$result = mysql_query($sql);
		$linha = mysql_fetch_assoc($result);
		if ($linha['SUM(T179_Quantidade_Entregue)']>0)
			return $linha['SUM(T179_Quantidade_Entregue)'];
		else 
			return '0';	
}

function validaCampoStatusEmprestimo($array){
	if($array['todosCampos']['T175_Flag_Operacao']=='E'){
		return "<b>Empréstimo</b>";
	}else if($array['todosCampos']['T175_Flag_Operacao']=='D'){
		return "<b>Devolução</b>";
	}
}

function validaCampoConfirmarDevolucao($array){
    global $g;
    // if($array['todosCampos']['T175_Flag_Status']=="A"){
            // return <<<EOT
            // <span class="ui-icon ui-icon-circle-check" title="Confirmar Devolução" 
                // onClick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Confirmar Devolução', '/est/est002/content/est002ContentConfirmarEmprestimo/', '&tab=geral&acaoId=' + encodeURIComponent('{$array['todosCampos']['T175_Id']}'), [300,200]);
            // "></span>
// EOT;
    // }
}

function comboColeta($array){
	global $g;
	$T005_Flag_Coleta = $array['todosCampos']['T005_Flag_Coleta'];
	if($T005_Flag_Coleta == 'S') {
		return "<input type='checkbox' checked='checked' onclick=\"est002CheckboxColeta(this, '{$array['todosCampos']['T005_Id']}', '{$g['divId']}')\" type='checkbox' id='T005_Flag_Coleta'>";
	} else {
		return "<input onclick=\"est002CheckboxColeta(this, '{$array['todosCampos']['T005_Id']}', '{$g['divId']}')\" type='checkbox' id='T005_Flag_Coleta'>";
	}
}

function campoOpcoesConferencia5($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="nova_janela('/est/est002/grid_func-ajax/est002ImprimirFicha/?ajax=true&T005_Id={$array['todosCampos']['T005_Id']}','Pedido {$array['todosCampos']['T005_Id']}',0,720,760,'no','no','no','no','yes','yes',0);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-contact" title="Imprimir"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function enviarCCe($array){
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Carta de Correção Eletrônica', '/ven/ven003/content/ven003contentCCe/','&acaoId={$T007_Id}', [990,550]);\">CC-e</a>";
	return <<<EOT
		<a href="#" onclick="nova_janela('/hardness/carta_correcao/principal.php?T007_Id=$T007_Id&Nova_Janela=S',0,0,900,600,'no','no','no','no','yes','yes',0);">
			<span>CC-e</span>
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

function imprimirEtiquetaDanfe($array) {

    // seu código
    // $array['campoAtual']
    // $array['todosCampos']['impEtiquetaDanfe']
    // $array['totais']['Nome_Total']

	$T007_Id = $array['todosCampos']['T007_Id'];

	if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe'])) {
		$retorno = <<<EOT
			<span onclick="impEtiquetaDanfe_grid2({$T007_Id})">
			<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon ui-icon-print"  title="Imprimir etiqueta DANFE simplificada"></span>
			</div>
			</span>
EOT;
	} else {
		$retorno = <<<EOT
			<span onclick="alert('Nota sem protocolo de autorização')">
			<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon  ui-icon-closethick"  title="Nota sem protocolo de autorização"></span>
			</div>
			</span>
EOT;
	}

	return $retorno;
}

function imprimirEtiquetaTransporte($array) {

    // seu código
    // $array['campoAtual']
    // $array['todosCampos']['impEtiquetaTransp']
    // $array['totais']['Nome_Total']
    $T007_Id = $array['todosCampos']['T007_Id'];
    $T005_Id = $array['todosCampos']['T005_Id'];

	if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe'])) {
		$retorno = <<<EOT
			<span onclick="impEtiquetaTransporte_grid2({$T005_Id}, {$T007_Id})">
			<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon ui-icon-print"  title="Imprimir etiqueta de transporte"></span>
			</div>
			</span>
EOT;
	} else {
		$retorno = <<<EOT
			<span onclick="alert('Nota sem protocolo de autorização')">
			<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon  ui-icon-closethick"  title="Nota sem protocolo de autorização"></span>
			</div>
			</span>
EOT;
	}
	return $retorno;
}

function enviarDadosNFTray($array) {

    // seu código
    // $array['campoAtual']
    // $array['todosCampos']['impEtiquetaTransp']
    // $array['totais']['Nome_Total']
    $T007_Id = $array['todosCampos']['T007_Id'];

	if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe'])) {
		$retorno = <<<EOT
			<span onclick="enviarDadosTray_grid2({$T007_Id})">
			<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon ui-icon-circle-arrow-n"  title="Enviar dados de NF para Tray"></span>
			</div>
			</span>
EOT;
	} else {
		$retorno = <<<EOT
			<span onclick="alert('Nota sem protocolo de autorização')">
			<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon  ui-icon-closethick"  title="Nota sem protocolo de autorização"></span>
			</div>
			</span>
EOT;
	}

	return $retorno;

}

function excluirVolume($array, $params = null) {
	global $g;

	$divId = !is_null($params) ? $params['divJanela'] : '';

	return <<<EOT
		<a href="#" onclick="est002excluirVolume('{$array['todosCampos']['T314_Id']}', '{$divId}')">
			<span class="ui-icon ui-icon-circle-close" title="Cancelar Volume"></span>
		</a>
EOT;
	
}

function T313_D109_Id_Primeiro($array) {
	global $g;

	$mostrar  = empty($array['campoAtual']) ? '-' : $array['todosCampos']['D109_Primeiro.D109_Nome'];
	$options  = gProcessaSelect(gGeraSelect('D109', 'D109_Id', 'D109_Codigo_Barras, D109_Nome', "WHERE D109_Flag_Ativo = 'S' ORDER BY D109_Nome", true, $array['todosCampos']['T313_D109_Id_Primeiro']));
	$idSelect = uniqid();
	$idDiv    = uniqid();
	$T005_Id  = $array['todosCampos']['T005_Id'];
	$T006_Id  = $array['todosCampos']['T006_Id'];
	$D009_Id  = $array['todosCampos']['D009_Id'];
	$result = "<select id=\"{$idSelect}\" onchange=\"atualizaT313_D109_Id_Primeiro(event, this, '{$T005_Id}', '{$T006_Id}', '{$D009_Id}');\" onblur=\"$(this).hide(); $('#{$idDiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	<div id="{$idDiv}" onclick="$('#' + '{$idSelect}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; margin-right: 2px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT313_D109_Id_Primeiro(event, select, T005_Id, T006_Id, D009_Id) {
			var campo = $(select).val();
			$.getJSON('/est/est002/grid_func-ajax/atualizaT313_D109_Id/?ajax=true&T005_Id='+ encodeURIComponent(T005_Id) + '&T006_Id='+ encodeURIComponent(T006_Id) + '&D009_Id='+ encodeURIComponent(D009_Id) + '&D109_Id='+ encodeURIComponent(campo) + '&Campo='+ encodeURIComponent("T313_D109_Id_Primeiro") + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});
		}
	}</script></div>
EOT;
	return $result;
}

function T313_D109_Id_Segundo($array) {
	global $g;

	$mostrar  = empty($array['campoAtual']) ? '-' : $array['todosCampos']['D109_Segundo.D109_Nome'];
	$options  = gProcessaSelect(gGeraSelect('D109', 'D109_Id', 'D109_Codigo_Barras, D109_Nome', "WHERE D109_Flag_Ativo = 'S' ORDER BY D109_Nome", true, $array['todosCampos']['T313_D109_Id_Segundo']));
	$idSelect = uniqid();
	$idDiv    = uniqid();
	$T005_Id  = $array['todosCampos']['T005_Id'];
	$T006_Id  = $array['todosCampos']['T006_Id'];
	$D009_Id  = $array['todosCampos']['D009_Id'];
	$result = "<select id=\"{$idSelect}\" onchange=\"atualizaT313_D109_Id_Segundo(event, this, '{$T005_Id}', '{$T006_Id}', '{$D009_Id}');\" onblur=\"$(this).hide(); $('#{$idDiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	<div id="{$idDiv}" onclick="$('#' + '{$idSelect}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; margin-right: 2px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT313_D109_Id_Segundo(event, select, T005_Id, T006_Id, D009_Id) {
			var campo = $(select).val();
			$.getJSON('/est/est002/grid_func-ajax/atualizaT313_D109_Id/?ajax=true&T005_Id='+ encodeURIComponent(T005_Id) + '&T006_Id='+ encodeURIComponent(T006_Id) + '&D009_Id='+ encodeURIComponent(D009_Id) + '&D109_Id='+ encodeURIComponent(campo) + '&Campo='+ encodeURIComponent("T313_D109_Id_Segundo") + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});
		}
	}</script></div>
EOT;
	return $result;
}

?>
