<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/

function campoOpcoes($array){
	 	global $g;
	$retorno = ($array);
			
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Faturamento', '/ven/ven012/content/ven012ContentFaturamento/','&T005_Id={$array['todosCampos']['T005_Id']}&tabela=T005&acaoId={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Faturamento"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="ven012RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	/*$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness/pedido_venda/gerar_impressao.php3?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;*/
	$imagem .= <<<EOT
		<span onclick="ven012ImprimirPedido('{$array['todosCampos']['T005_Id']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiqueta', '/ven/ven012/content/ven123/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
function ven012_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T090_Data_Vencimento'];
	return gPegaDiaSemana($data);
}
function ven012_Detalhes($array) {
	global $g;
	if($array['todosCampos']['D027_Flag_Tipo']=='2'){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/ven/ven012/content/ven012ContentDetalhesCheque/', '&T090_Id={$array['todosCampos']['T090_Id']}', [390,270]);\">
				<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
				</a>";	
	}
}	

function T090_D027_Id_Portador($array) {
	return $array['todosCampos']['D027_Portador'];
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
			function validaCampoInutilizar($array) {

		$retorno = "<a href=\"#\">INUTILIZAR</a>";
		/**
		* onClick dentro do <a>:
		* onclick=\"javascript:parent.inutilizarNFe('$mT005[T007_Id]');\"
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
                 $retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe! - $Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['todosCampos']['T007_Numero_Protocolo_Nfe'].".xml'>Erro!</font>";
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
				$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]); return false;\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\" style=\"margin-left: 19px;\"></span></li></a>";
				return $retorno;
			}
		}
	}

function validaCampoCancelar($array){
		if($array['todosCampos']['T007_Flag_Inutilizado']=="N"){
			
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				
				$retorno = "<u>Cancelada</u>";
				
				return $retorno;
				
			}else{
				
				$retorno="";
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

function campoInutilizar($array){
	global $Perfil_Usuario;
	global $g;
	$cAcessoExclusaoNFe='N';
	$cSql_C030=mysql_query("select * from C030 where C030_C029_Id=90 and C030_Id_Perfil='$Perfil_Usuario' and C030_Acesso_Liberado='S'");
    if (mysql_num_rows($cSql_C030)>0)
    {
       $cAcessoExclusaoNFe='S';
    }
     
      if ($cAcessoExclusaoNFe=='S'){
	  	 global $g;
	  	 if ($array['todosCampos']['T127_Id']>0)
	  	 {
	  	 	// OPCAO PARA INUTILIZACAO DA NOTA FISCAL
	  	 	return "<img src='/hardness3/static/img/tmp/fechar.gif' onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Inutilizar NFe', '/ven/ven012/content/ven012ContentInutilizar/','&T007_Id={$array['todosCampos']['T007_Id']}', [500,300]);\">"; 
 	  	 	
	  	 }
	  	 else
	  	 {
	  	 	//OPCAO PARA EXCLUIR A NOTA
			return "<img src='/hardness3/static/img/tmp/fechar.gif'  title=\"Excluir a nota fiscal\" onClick=\"ven012CampoExcluirNFe('{$array['todosCampos']['T007_Id']}','{$g['divId']}', '{$g['divIdRoot']}') \">";
			
	  	 }	
	  }
	
}

function campoGerarXML($array){
	return "<img src='/hardness3/static/img/tmp/continuar_pedido.gif' title=\"\" onclick=\"nova_janela('/hardness/almoxarifado/gerar_xml.php?T007_Id={$array['todosCampos']['T007_Id']}',0,0,990,600,'yes','no','no','no','yes','no','IMPRESSAO_DANFE');\" style='cursor:pointer;'>";
}

function pegaValidadeCertificado($Certificado,$Senha,$String='N')
{
   if ($String=='N')
   {
   		$key  = file_get_contents($Certificado);
   }
   else
   {
   		$key = $Certificado;
   }		
   $resp = openssl_pkcs12_read ($key,$x509certdata,$Senha);
   $data = openssl_x509_parse($x509certdata['cert']);
   $in=$data['validTo'];

   $year  = substr($in, 0, 2); /* NOTE: Yes, this returns a two digit year */
   $month = substr($in, 2, 2);
   $day   = substr($in, 4, 2);
   $hour  = substr($in, 6, 2);
   $min   = substr($in, 8, 2);
   $sec   = substr($in, 10, 2);
   if (!empty($in))
   {
       $cRetorno=mktime($hour, $min, $sec, $month, $day, $year);
   }
   else
   {
       $cRetorno='';
   }
   return $cRetorno;
}

function CampoObservacao($array){
	//return "<font style=\"color: var(--BGVermelho);\">{$array['campoAtual']}</font>";
	$sql="concat(T005_Observacao_1,\" \",(select \"<font style=\"color: var(--BGVermelho);\">NOTA CANCELADA!</font>\" from T007 where T007_T005_Id={$array['todosCampos']['T005_Id']} and T007_Flag_Cancelada=\"S\" group by T005_Id))";
	$resultado = mysql_query($sql);
	$row=mysql_fetch_array($resultado);
	
	return $resultado;
}

function validaCampoStatusServicos($array){
	$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}' AND (T147_Status='P' OR T147_Status='E')";
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			return "<font style='color:var(--BGVermelho);'><b>Serviços Pendentes</b></font>";
		}else{
			return "<font style='color:var(--BGAzul);'><b>Serviços Concluídos</b></font>";
		}
	}else{
		return "-";
	}
}
function validaCampoRecibo($array){
	global $g;
	$novoIdTelaForm = uniqid();
	return "<a href=\"#\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Recibo', '/ven/ven012/outro/ven012ReciboNotaFiscal/','&T007_Id={$array['todosCampos']['T007_Id']}', [1000,700]);\">Recibo</a>";
}

function geraImagem($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/ven/ven002/content/ven002content08/', '&T147_Id={$array['todosCampos']['T147_Id']}', [700,500]);\">
				<li title=\"Detalhes do Serviço\" class=\"ui-state-default ui-corner-all\">			
				<span class=\"ui-icon ui-icon-zoomin\" title='Detalhes'></span>
				</li>
			</a>";
}

function buscaVeiculo($array) {
	$D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['T147_D116_Id']);			
	$veiculo=@mysql_fetch_array($D116);
	return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];
}

function validaCampoStatus($array){
	if(($array['todosCampos']['T147_Status'] == "P") || ($array['todosCampos']['T147_Status'] == "NULL")){
		return "<font style='color:var(--BGVermelho);'><b>Pendente</b></font>";
	}else if($array['todosCampos']['T147_Status'] == "E"){
		return "<font color='green'><b>Execução</b></font>";
	}else if($array['todosCampos']['T147_Status'] == "C"){
		return "<font style='color:var(--BGAzul);'><b>Concluído</b></font>";
	}else if($array['todosCampos']['T147_Status'] == "N"){
		return "<b>Cancelado</b>";
	}else if($array['todosCampos']['T147_Status'] == "NULL"){
		return "<b>Cancelado</b>";
	}
}
function calculaPrecoTotalTroca($array){
	$preco=$array['todosCampos']['T138_Valor_Preco_Unitario'];
	$qte=$array['todosCampos']['T138_Quantidade'];
	return $preco*$qte;
}

function retornaOperacaoTroca($array){
	if ($array['todosCampos']['T138_Operacao']=='P')
		return 'PARTE PAGAMENTO';
	else 	
		return 'TROCA';	
}

function ven012CampoDividir($array,$TXXX){
    global $g,$r_acaoId;
    $disabled = '';
    if(!empty($array['todosCampos']['T007_Id']) || $array['todosCampos']['T006_Flag_Cancelado']=='S'){
    	$disabled = ' disabled="disabled" ';
    }
    if($TXXX=="T138"){
        $id = $array['todosCampos']['T138_Id'];
        $Flag_Dividir = $array['todosCampos']['T138_Flag_Dividir'];
        $idInput = "T138_Flag_Dividir";
        if(!empty($array['todosCampos']['T007_Id'])){
            $disabled = ' disabled="disabled" ';
        }
    }elseif($TXXX=="T147"){
        $id = $array['todosCampos']['T147_Id'];
        $Flag_Dividir = $array['todosCampos']['T147_Flag_Dividir'];
        $idInput = "T147_Flag_Dividir";
    }else{
        $id = $array['todosCampos']['T006_Id'];
        $Flag_Dividir = $array['todosCampos']['T006_Flag_Dividir'];
        $idInput = "T006_Flag_Dividir";
    }
    
    $retorno = "<input onclick=\"ven012CheckboxDividir(this, '{$id}', '{$g['divId']}', '{$TXXX}');\" type='checkbox' {$disabled} id='{$idInput}' title='Dividir'";
    if ($Flag_Dividir=='S'){
        return $retorno .="checked='checked'";
    }
    return $retorno .= " />";
}

function ven012CampoComissao($array,$TXXX){
    global $g,$r_acaoId;
    if($TXXX=="T138"){
        $id = $array['todosCampos']['T138_Id'];
        $Flag_Comissao = $array['todosCampos']['T138_Flag_Comissao'];
        $idInput = "T138_Flag_Comissao";
    }elseif($TXXX=="T147"){
        $id = $array['todosCampos']['T147_Id'];
        $Flag_Comissao = $array['todosCampos']['T147_Flag_Comissao'];
        $idInput = "T147_Flag_Comissao";
    }else{
        $id = $array['todosCampos']['T006_Id'];
        $Flag_Comissao = $array['todosCampos']['T006_Flag_Comissao'];
        $idInput = "T006_Flag_Comissao";
    }
    
    $retorno = "<input onclick=\"ven012CheckboxComissao(this, '{$id}', '{$g['divId']}', '{$TXXX}');\" type='checkbox' id='{$idInput}' title='Comissionado'";
    if ($Flag_Comissao=='S'){
        return $retorno .="checked='checked'";
    }
    return $retorno .= " />";
}
function ven012CampoNF($array){
    global $g;
    $novoIdTelaForm = uniqid();
    return "<a href=\"#\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','&acaoId={$array['todosCampos']['T007_Id']}', [900,550]);\">{$array['todosCampos']['Nota_Fiscal']}</a>";
}
function ven012CampoNFServicos($array){
    global $g;
    $T007=mysql_query("SELECT T007_Flag_ACP FROM T007 WHERE T007_Id='{$array['todosCampos']['T007_Id']}'");
    $mT007 = mysql_fetch_assoc($T007);
    if($mT007['T007_Flag_ACP'] == '7'){
    	$novoIdTelaForm = uniqid();
    	return "<a href=\"#\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','&acaoId={$array['todosCampos']['T007_Id']}', [900,550]);\">{$array['todosCampos']['Nota_Fiscal']}</a>";
    }
}
function ven012DuplicarProduto($array){
    global $g;
    return <<<EOT
    <span class="ui-icon ui-icon-circle-plus" title="Duplicar Produto" onclick='ven012DuplicarProduto("{$g['divId']}", "{$array['todosCampos']['T006_Id']}");'></span>
EOT;
}

function T006_D006_Id_CFOP($array){
    return $array['todosCampos']['D006_Codigo_CFOP'];
}

function atualizarPrecoProduto($array){
	global $g;
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T006_D009_Id = $array['todosCampos']['T006_D009_Id'];	
	$sql = mysql_query("SELECT T006_Valor_Preco_Sem_Desconto_Unitario FROM T006 WHERE T006_D009_Id = '{$T006_D009_Id}' AND T006_Id != '{$T006_Id}' ORDER BY T006_Id DESC LIMIT 1");
	if(mysql_num_rows($sql) > 0){
		$res = mysql_fetch_array($sql);
		$T006_Valor_Preco = gCorrigeNumero($res['T006_Valor_Preco_Sem_Desconto_Unitario']);
		return "<a href=\"#\" onclick=\"atualizarPrecoProduto('{$g['divId']}', '{$T006_Id}', '{$T006_Valor_Preco}');\"><span style=\"text-decoration: underline;\">{$T006_Valor_Preco}</span></a>"; 
	} else {
		return '-';
	}
}

function campoMarcarEnviar($array) {
	global $g;
	$checked  = '';
	if ($array['todosCampos']['T007_Flag_Enviar'] == 'S') {
		$checked = " checked='checked'";
	}
	if($array['todosCampos']['T007_Flag_Almoxarifado'] == 'S' && !empty($array['todosCampos']['T007_Numero_Nota_Fiscal'])) {
		return "<input onclick=\"ven012MarcarParaEnviar(this, '{$g['divId']}', '{$array['todosCampos']['T007_Id']}');\" type='checkbox' title='Marcar para Enviar'{$checked}>";
	} else {
		return '';
	}
}

function campoClienteEmissorNF($array) {
	global $g;
	$sqlD024 = mysql_query("SELECT D024_Nome_Fantasia FROM T005 LEFT JOIN D024 ON D024_Id = T005_D024_Id_Nfe WHERE T005_Id = '{$array['todosCampos']['T005_Id']}'");
	$D024 = mysql_fetch_array($sqlD024);
	return $D024['D024_Nome_Fantasia'];
}

?>
