<?php
namespace hardness;

    function ven003ExibirVinculoNFDC($array)
    {
        $valor = (string) ($array['campoAtual'] ?? $array['todosCampos']['NFDebitoCredito'] ?? '-');
        $texto = htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        // O status vem da própria NF vinculada, não da situação da NF de origem.
        return preg_replace('/\bNF ([0-9]{7}) \(c\)/',
            'NF <span style="color:var(--BGVermelho, #d00)" title="Nota cancelada">$1 (c)</span>', $texto);
    }

    function ven003ExibirOrigemNFDC($array)
    {
        $texto = ven003ExibirVinculoNFDC($array);
        $id = (int) ($array['todosCampos']['NFOrigemId'] ?? 0);
        return $id > 0 ? '<a href="#" class="nfDCOrigem" data-nf-origem="' . $id . '" title="Abrir NF de origem">' . $texto . '</a>' : $texto;
    }

    function ven003ExibirTipoFiscalNFDC($array)
    {
        // Mesmas descrições do cadastro da NF; apenas exibição, sem regra fiscal.
        $tipos = array(
            '6' => array(
                '01' => 'Transferência de créditos p/ cooperativas',
                '02' => 'Anulação de crédito por saídas imunes/isentas',
                '03' => 'Débitos de NF não processadas na apuração',
                '04' => 'Multa e juros',
                '05' => 'Transferência de crédito de sucessão',
                '06' => 'Pagamento antecipado',
                '07' => 'Perda em estoque',
                '08' => 'Desenquadramento do Simples Nacional',
            ),
            '5' => array(
                '01' => 'Multa e juros',
                '02' => 'Crédito presumido ZFM',
                '03' => 'Retorno por recusa total/não localização',
                '04' => 'Redução de valores',
                '05' => 'Transferência de crédito na sucessão',
                '06' => 'Retorno por recusa parcial',
            ),
        );
        $tipo = trim((string) ($array['campoAtual'] ?? ''));
        $finalidade = (string) ($array['todosCampos']['T007_Flag_Finalidade_NFe'] ?? '');
        $descricao = $tipos[$finalidade][$tipo] ?? '';
        $texto = $tipo === '' ? '-' : $tipo . ($descricao !== '' ? ' - ' . $descricao : '');
        return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    function ven003ExibirSituacaoNFDC($array)
    {
        $nota = $array['todosCampos'];
        if (($nota['T007_Flag_Cancelada'] ?? 'N') === 'S') { return 'Cancelada'; }
        if (($nota['T007_Flag_Inutilizado'] ?? 'N') === 'S') { return 'Inutilizada'; }
        return !empty($nota['T007_Numero_Protocolo_Nfe']) ? 'Com protocolo' : 'Sem protocolo';
    }

    function ven003RecuperarInutilizacao($array)
    {
        global $g;
        $t127Id = (int) ($array['todosCampos']['T127_Id'] ?? 0);
        if ($t127Id <= 0) {
            return '';
        }

        return '<a href="#" onclick="abrirJanela(false, \'' . $g['divIdRoot'] . '\', \'' . $g['divId'] . '\', unique(), \'\', \'Recuperar inutilização\', \'/ven/ven003/outro/ven003OutroRecuperarInutilizacao/\', \'&T127_Id=' . $t127Id . '&ven003DivRefresh=' . rawurlencode($g['divId']) . '\', [680, 440]); return false;">Recuperar</a>';
    }
	function ValidaCampoMDFe($array){
		$T276 = mysql_query("SELECT T284_T276_Id FROM T284 WHERE T284_T007_Id='{$array['todosCampos']['T007_Id']}'");
		log("SELECT T284_T276_Id FROM T284 WHERE T284_T007_Id='{$array['todosCampos']['T007_Id']}'");
		$mT276 = mysql_fetch_array($T276);
		$retorno = "";
		$retorno = "<a href=\"#\" onclick=\"javascript:nova_janela('/hardness3/prog/fis/fis012/content/fis012MdfeCadastro/.php?T276_Id={$mT276['T284_T276_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>{$mT276['T284_T276_Id']}</a>";
		return $retorno;
	}

	// function validaFlagNotaMarcada($array){
	// 	include('prog/ven/ven003/grid_func-js.php');
	// 	global $g;
	// 	$T007_Id = $array['todosCampos']['T007_Id'];
	// 	$T007_Flag_Marcado = $array['todosCampos']['T007_Flag_Marcado'];
	// 	$retorno = "<input onclick=\"ven003CheckboxMarcar(this, '{$T007_Id}', '{$g['divId']}');\" type='checkbox' id='T007_Flag_Marcado'";
	// 	if ($T007_Flag_Marcado=='S'){
	// 		$retorno .="checked='checked'";
	// 	}
	//     return $retorno .= " />";
	// }
	
	function chamaCampo($array) {
		$modelo = ($array['todosCampos']['T007_Flag_ACP'] == '6') ? '55' : '65';
		$cClausula  = " select ";
		$cClausula .= " T127_Codigo_Retorno, ";
		$cClausula .= " T127_Retorno_Sefaz ";
		$cClausula .= " from T127 ";
		$cClausula .= " where T127_Numero_Nota_Fiscal='{$array['todosCampos']['T007_Numero_Nota_Fiscal']}' ";
		$cClausula .= " and T127_C004_Id='{$array['todosCampos']['T007_C004_Id']}' ";
		$cClausula .= " and ({$array['todosCampos']['T007_Flag_ACP']} = '6' or {$array['todosCampos']['T007_Flag_ACP']} = '8')";
		$cClausula .= " and T127_Modelo='{$modelo}' ";
		$cClausula .= " order by T127_Data_Transmicao desc  ";
		$cClausula .= " limit 1  ";
		$cSql_T127=mysql_query($cClausula);
		$mT127=mysql_fetch_array($cSql_T127);
		return $mT127['T127_Retorno_Sefaz'];   
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
		if(isset($protocoloNF) || $contingenciaOffline == 'S') {
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
	function ValidaCampoDamdfe($array){
		global $g;
		$protocoloMDFe = $array['todosCampos']['T276_Numero_Protocolo_MDFe'];
		$T276_Id = $array['todosCampos']['T284_T276_Id'];
		$T276_C004_Id = $array['todosCampos']['T276_C004_Id'];
		$Query = md5($T276_C004_Id.'|'.$T276_Id);
		if(!empty($protocoloMDFe)){
			$retorno = "<a href=\"#\" onclick=\"window.open('/hardness3/hardness/damdfe/imprimir_damdfe.php?Query={$Query}&T276_Id={$T276_Id}&T276_C004_Id={$T276_C004_Id}','DAMDFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');\" style='cursor:pointer'>DAMDFE</a>";
		}else{
			$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/nota_emitida2/gerar_pre_damdfe.php?T276_Id={$array['todosCampos']['T284_T276_Id']}&T276_C004_Id={$T276_C004_Id}','MDFe {$array['todosCampos']['T284_T276_Id']}');\" style='cursor:pointer'>DAMDFE</a>";	
		}			

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
	function validaCampoNF($array) {
		$retorno = "";
		$retorno = "<a href=\"#\"  onclick=\"javascript:abre_doc('/hardness/nota_emitida/nf_emitida.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>NF</a>";
		/**
		* onClick dentro de <a>
		* onclick=\"javascript:abre_doc('../nota_emitida/nf_emitida.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
		*/
		return $retorno;
	}
	function validaCampoACP($array) {
		global $g;
		$retorno="";
		$retorno="<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true'+ '&T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>ACP</a>";
		// $retorno="<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/imprimir_acp.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>ACP</a>";
		/**
		* onClick entro de <a>
		* onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/imprimir_acp.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
		*/
		return $retorno;
	}

	function validaCampoCancelar($array){
		global $g;

		$T007_Numero_Nota_Fiscal   = $array['todosCampos']['T007_Numero_Nota_Fiscal'];
		$T007_Numero_Protocolo_Nfe = $array['todosCampos']['T007_Numero_Protocolo_Nfe'];
		$modelo					   = ($array['todosCampos']['T007_Flag_ACP'] == '6') ? '55' : '65';

		$sqlT127 = mysql_query("SELECT * 
								  FROM T127 
								 WHERE T127_Numero_Nota_Fiscal = '{$T007_Numero_Nota_Fiscal}' 
								   AND T127_C004_Id = '{$g['empresaAtual']}' 
								   AND T127_Modelo = '{$modelo}' 
							  ORDER BY T127_Id 
							      DESC LIMIT 1");
		$T127 = mysql_fetch_array($sqlT127);

		$T127_Codigo_Retorno = $T127['T127_Codigo_Retorno'];		
		
		if($array['todosCampos']['T007_Flag_Inutilizado']=="N"){
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				if(($T127_Codigo_Retorno == '301' || $T127_Codigo_Retorno == '302' || $T127_Codigo_Retorno == '303' || $T127_Codigo_Retorno == '205' || $T127_Codigo_Retorno == '110') && !empty($T007_Numero_Protocolo_Nfe)){
					$retorno = "<b>Denegada</b>";
				} else {
					if (empty($array['todosCampos']['T007_Numero_Protocolo_Cancelamento_Nfe']) && ($array['todosCampos']['T007_Flag_ACP'] == '6')){

						$retorno="<a href=\"#\" onclick=\"ven003EstornarCancelarNotaFiscal('{$array['todosCampos']['T007_Id']}', '{$g['divId']}', '{$g['divIdRoot']}'); return false;\">Estornar</a>";

					} else if(!empty($array['todosCampos']['T007_Observacao_Cancelamento'])){

						$arrayMotivoCancelamento = explode("Motivo do Cancelamento :", $array['todosCampos']['T007_Observacao_Cancelamento']);
						$motivoCancelamento      = @$arrayMotivoCancelamento[1];
						$motivoCancelamento      = (!empty($motivoCancelamento)) ? $motivoCancelamento : "";
						$retorno                 = "<span title='{$array['todosCampos']['T007_Observacao_Cancelamento']}'>" . $motivoCancelamento . "</span>";
						
					}else{
						$retorno = "<b>Cancelada</b>";
					}					
				}
				return $retorno;
			} else {
				//if($T127_Codigo_Retorno == '301' || $T127_Codigo_Retorno == '302' || $T127_Codigo_Retorno == '303' || $T127_Codigo_Retorno == '205' || $T127_Codigo_Retorno == '110'){					
				//	$retorno="<a href=\"#\" onclick=\"ven003DenegarNFe('{$array['todosCampos']['T007_Id']}', '{$g['divId']}', '{$g['divIdRoot']}'); return false;\">Denegar</a>";
					//$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness3/old/lib/denegar_nfe.php?T007_Id=' + encodeURIComponent({$array['todosCampos']['T007_Id']}),0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Denegar</a>";
				//} else {
				$retorno="<a href=\"#\" onclick=\"ven003cancelarNotaFiscal('{$array['todosCampos']['T007_Id']}', '{$g['divId']}', '{$g['divIdRoot']}'); return false;\">Cancelar</a>";
					//$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id=' + encodeURIComponent({$array['todosCampos']['T007_Id']}),0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Cancelar</a>";
					//$retorno="<a href=\"#\" onclick=\"javascript:cancela_nfe('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T005_Id']}');\" style='cursor:pointer'>Cancelar</a>";
				//}
				return $retorno;
			}
		} else {
			return "";
		}
	}

	function validaCampoAlmoxarifado($array){
		$retorno = "";
		$retorno = "<a href=\"#\" onclick=\"javascript:nova_janela('/hardness/nota_emitida/imprimir_almoxarifado.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>Almox</a>";

		/**
		* onClick dentro do <u>
		* onclick=\"javascript:abre_doc('../nota_emitida/imprimir_almoxarifado.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
		*/
		return $retorno;
	}

	function validaCampoXML($array){
		global $g;
		$cAno_Mes_NFe=substr($array['todosCampos']['T007_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		
		//return "Ano: " . $cAno_Mes_NFe . "<br />Diretório: " . $Diretorio_NFe; 
		
		 if (!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) && ($array['todosCampos']['T007_Flag_ACP']==6 or $array['todosCampos']['T007_Flag_ACP']==8))
		 {
		 	$retorno='';
            if (!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']))
             {

                 if (file_exists("$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['todosCampos']['T007_Numero_Protocolo_Nfe'].".xml"))
                 {
					$retorno  = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">";    
					$retorno .= "<font class=fonte><u>XML {$array['todosCampos']['T007_Versao_Nfe']}</u></font>";
					$retorno .= "</a>";
				}
                 else
                 {
  					 $retorno  = "<a title='Baixar XML autorizado do banco para o disco' target='_blank' href=\"/ven/ven003/grid_func-ajax/baixaXML/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">";    
                     $retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe no disco!'>Erro!</font>";
					 $retorno .= "</a>";
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

	function validaCampoXMLCancelado($array){
		global $g;
		$cAno_Mes_NFe=substr($array['todosCampos']['T007_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		
		//return "Ano: " . $cAno_Mes_NFe . "<br />Diretório: " . $Diretorio_NFe; 
		
		if (!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) && $array['todosCampos']['T007_Flag_ACP']==6)
		{
			$retorno='';
			if (!empty($array['todosCampos']['T007_Numero_Protocolo_Cancelamento_Nfe']))
			{
				$retorno = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXMLCancelamento/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">";
				if (file_exists("$Diretorio_NFe/xml/autorizado/ID".$array['todosCampos']['T007_Numero_Protocolo_Cancelamento_Nfe'].".xml"))
				{
					$retorno .= "<font class=fonte><u>{$array['todosCampos']['T007_Numero_Protocolo_Cancelamento_Nfe']}</u></font></a>";
				}
				else
				{
					$retorno .="</a>";
					$retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML de cancelamento referente a esta NFe não existe!'>{$array['todosCampos']['T007_Numero_Protocolo_Cancelamento_Nfe']}</font>";
				}
			}
			return $retorno;
		}
	}
	function validaCampoEmail($array){
		global $g;
		$retorno = "";

		if($array['todosCampos']['T007_Flag_ACP']==3){
			if($array['todosCampos']['T007_Data_Envio_Email'] =='0000-00-00 00:00:00'){
				$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Flag_ACP=' + encodeURIComponent('{$array['todosCampos']['T007_Flag_ACP']}') + '&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]); return false;\"><span title=\"Enviar ACP por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></span></a>";
			}else{
				$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Flag_ACP=' + encodeURIComponent('{$array['todosCampos']['T007_Flag_ACP']}') + '&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]); return false;\"><span title='E-mail enviado em: ".$array['todosCampos']['T007_Data_Envio_Email']."'><span class=\"ui-icon ui-icon-check\"></span></li></a>";
			}
		} else if (!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) and ($array['todosCampos']['T007_Flag_ACP']==6 || $array['todosCampos']['T007_Flag_ACP']==8)){
					// não pode mandar e-mail se for denegado
			$modelo = ($array['todosCampos']['T007_Flag_ACP'] == '6') ? '55' : '65';
			$T127 = mysql_query("SELECT * FROM T127 WHERE T127_Numero_Nota_Fiscal = '{$array['todosCampos']['T007_Numero_Nota_Fiscal']}' AND T127_C004_Id = '{$array['todosCampos']['T007_C004_Id']}' AND T127_Modelo = '{$modelo}' ORDER BY T127_Id DESC LIMIT 1");
			$mT127 = mysql_fetch_array($T127);
			if($mT127['T127_Codigo_Retorno'] != '100'){
	    		return '';
			}
			/**
			* onclick=\"javascript:parent.AbrirFormularioEmail('$mT005[T007_Id]')\"
			*/
			if($array['todosCampos']['T007_Data_Envio_XML']!='0000-00-00 00:00:00' and $array['todosCampos']['T007_Data_Envio_XML']!=''){
				$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]); return false;\"><span title='E-mail enviado em: ".$array['todosCampos']['T007_Data_Envio_XML']."'><span class=\"ui-icon ui-icon-check\"></span></li></a>";
				//$retorno = "<a href='#'><li title='E-mail enviado em: '".$array['todosCampos']['T007_Data_Envio_XML']."><span class='ui-icon ui-icon-check'></span></li></a>";
			}else{
				//$retorno = "<a href=\"#\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></li></a>";
				$retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T007_Id=' + encodeURIComponent('{$array['todosCampos']['T007_Id']}'), [900,550]); return false;\"><span title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></span></a>";
			}

		}

		//$retorno = $array['todosCampos']['T007_Flag_ACP'];
		return $retorno;
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
	
	function excluirNFe($array) {
		global $g;

	  	return <<<EOT
	    		<a href="#" onclick="ven003excluirNFe('{$array['todosCampos']['T007_Id']}','{$g['divId']}')">
	    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
				</a>
EOT;
		
	}

	function excluirProduto($array){
		global $g;
		$restricao = $g['conf']->restricaoTravar('excluir', 'T008_Id', $array['todosCampos']['T008_Id'], 'T008_T007_Id', $array['todosCampos']['T008_T007_Id']);
		log("retorno:".$restricao);
		if ($restricao === true) {
			return <<<EOT
				<span onclick="ven003ExcluirProduto('{$array['todosCampos']['T008_Id']}', '{$array['todosCampos']['T008_T007_Id']}', '{$g['divId']}');"> 
					<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
				</span>
EOT;
	}
	return '';
	
}

	function campoCupom($array){
		if ($array['todosCampos']['T132_Cupom_Numero_EFC']>0)
		{
				return "{$array['todosCampos']['T132_Cupom_Numero_EFC']}/{$array['todosCampos']['T132_Cupom_Numero_COO']}/{$array['todosCampos']['T132_Cupom_Modelo']}";
		}else{
		        return "";
		}	
	}
	function campoNumeroDocumento($array){
		if($array['todosCampos']['T132_Pessoa_Fisica_Juridica']=='J')
		{
			return "{$array['todosCampos']['T132_Cnpj']}";
		}
		elseif($array['todosCampos']['T132_Pessoa_Fisica_Juridica']=='F')
		{
			return "{$array['todosCampos']['T132_Cpf']}";
		}
		else
		{
	        return "";
		}
	}

	function ven003_Dia_Semana($array) {
		global $g;
		$data=$array['todosCampos']['T118_Data_Vencimento'];
		return gPegaDiaSemana($data);
	}

	function T118_D027_Id_Portador($array) {
		return $array['todosCampos']['D027_Portador'];
	}

	function buscaVeiculo($array) {
	    $D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['T203_D116_Id']);            
	    $veiculo=@mysql_fetch_array($D116);
	    return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];
	}
	function validaCampoStatus($array){
	    if(($array['todosCampos']['T203_Status'] == "P") || ($array['todosCampos']['T203_Status'] == "NULL")){
	        return "<font color='red'><b>Pendente</b></font>";
	    }else if($array['todosCampos']['T203_Status'] == "E"){
	        return "<font color='green'><b>Execução</b></font>";
	    }else if($array['todosCampos']['T203_Status'] == "C"){
	        return "<font color='blue'><b>Concluído</b></font>";
	    }else if($array['todosCampos']['T203_Status'] == "N"){
	        return "<b>Cancelado</b>";
	    }else if($array['todosCampos']['T203_Status'] == "NULL"){
	        return "<b>Cancelado</b>";
	    }
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
	function ven003DownloadXmlCce($array){
	    $T199_Id = $array['todosCampos']['T199_Id'];
	    if($array['todosCampos']['T199_Flag_Enviado'] == 'S'){
	        return <<<EOT
	            <a href="/ven/ven003/grid_func-ajax/abreXMLCCe/?ajax=true&T199_Id={$T199_Id}">
	                <span>Download</span>
	            </a>
EOT;
	    }else{
	        return "";
	    }
	}

	/**
	 * Ações disponíveis para cada ocorrência de evento da NF-e.
	 * A geração e transmissão utilizam a camada central nfe4_eventos.class.php.
	 */
	function ven003AcoesEventoNFe($array){
		global $g;

		$T321_Id = (int)$array['todosCampos']['T321_Id'];
		$status = $array['todosCampos']['T321_Status'];
		$tipoEvento = $array['todosCampos']['T321_Tipo_Evento'];
		$possuiCancelamento = !empty($array['todosCampos']['Possui_Cancelamento']);

		if ($status == 'RASCUNHO' || $status == 'PRONTO_ENVIO' || $status == 'REJEITADO' || $status == 'ERRO_COMUNICACAO') {
			$tentativa = ($status == 'REJEITADO' || $status == 'ERRO_COMUNICACAO');
			$rotulo = $tentativa ? 'Tentar novamente' : 'Transmitir evento';
			$titulo = $tentativa
				? 'Gerar, assinar e transmitir novamente o evento'
				: 'Gerar, assinar e transmitir o evento para a SEFAZ';

			return '<button type="button" title="' . $titulo . '" onclick="return ven003EnviarEventoNFe(event, ' . $T321_Id . ');">' . $rotulo . '</button>';
		}

		if ($tipoEvento == '112140' && $status == 'AUTORIZADO' && !$possuiCancelamento) {
			return '<button type="button" title="Criar o evento de cancelamento" onclick="return ven003CriarCancelamentoEventoNFe(event, ' . $T321_Id . ');">Cancelar evento</button>';
		}

		return '';
	}

	/** Retorna o nome fiscal completo do evento, mantendo o código em coluna própria. */
	function ven003DescricaoEventoNFe($array)
	{
		$tipos = array(
			'112140' => 'Fornecimento não realizado com pagamento antecipado',
			'110001' => 'Cancelamento de Evento',
		);

		$tipo = $array['todosCampos']['T321_Tipo_Evento'];
		return isset($tipos[$tipo]) ? $tipos[$tipo] : 'Evento NF-e não identificado';
	}

	/** Identifica o evento que foi cancelado quando a linha representa o 110001. */
	function ven003EventoOriginalNFe($array)
	{
		$T321_IdOriginal = (int)$array['todosCampos']['T321_T321_Evento_Original_Id'];
		if (empty($T321_IdOriginal)) {
			return '-';
		}

		$tipos = array(
			'112140' => 'Fornecimento não realizado com pagamento antecipado',
			'110001' => 'Cancelamento de Evento',
		);
		$original = mysql_fetch_assoc(mysql_query("SELECT
				T321_Tipo_Evento,
				T321_Sequencial_Evento
			FROM T321
			WHERE T321_Id = '{$T321_IdOriginal}'"));
		if (empty($original)) {
			return 'Evento original não localizado';
		}

		$tipoOriginal = $original['T321_Tipo_Evento'];
		$nomeOriginal = isset($tipos[$tipoOriginal]) ? $tipos[$tipoOriginal] : 'Evento NF-e não identificado';
		$sequencia = $original['T321_Sequencial_Evento'];

		return $tipoOriginal . ' - ' . $nomeOriginal . ' (seq. ' . $sequencia . ')';
	}

	/** Baixa o XML assinado ou o procEventoNFe, conforme exista protocolo. */
	function ven003BaixarXmlEventoNFe($array)
	{
		$T321_Id = (int)$array['todosCampos']['T321_Id'];
		$status = $array['todosCampos']['T321_Status'];
		if ($status == 'RASCUNHO') {
			return '-';
		}

		return '<button type="button" title="Baixar XML do evento" onclick="return ven003BaixarXmlEventoNFe(event, ' . $T321_Id . ');">Baixar XML</button>';
	}

	function ven003CampoNF($array){
	    global $g;
	    $novoIdTelaForm = uniqid();
	    return "<a href=\"#\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','&acaoId={$array['todosCampos']['T007_Id']}', [900,550]);\">{$array['todosCampos']['T007_Numero_Nota_Fiscal']}</a>";
	}

	function validaCampoEmailCce($array){
	    global $g;
	    $retorno = "";
	    if($array['todosCampos']['T199_Flag_Enviado'] == 'S'){
	        if($array['todosCampos']['T199_Data_Envio_Email_Cce']!='0000-00-00 00:00:00' && $array['todosCampos']['T199_Data_Envio_Email_Cce']!=''){
	            $retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T199_Id=' + encodeURIComponent('{$array['todosCampos']['T199_Id']}'), [900,550]); return false;\"><span title='E-mail enviado em: ".$array['todosCampos']['T199_Data_Envio_Email_Cce']."'><span class=\"ui-icon ui-icon-check\"></span></li></a>";
	            //$retorno = "<a href='#'><li title='E-mail enviado em: '".$array['todosCampos']['T007_Data_Envio_XML']."><span class='ui-icon ui-icon-check'></span></li></a>";
	        }else{
	            //$retorno = "<a href=\"#\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></li></a>";
	            $retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T199_Id=' + encodeURIComponent('{$array['todosCampos']['T199_Id']}'), [900,550]); return false;\"><span title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></span></a>";
	        }
	    }else{
	        $retorno = '';
	    }
	    return $retorno;
	}

	function T008_D006_Id_CFOP($array){
	    return $array['todosCampos']['D006_Codigo_CFOP'];
	}

	function ven003_Detalhes($array) {
		global $g;
		if($array['todosCampos']['D027_Flag_Tipo']=='2'){
			return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/ven/ven003/content/ven003ContentDetalhesCheque/', '&T118_Id={$array['todosCampos']['T118_Id']}', [390,270]);\">
					<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
						<span class=\"ui-icon ui-icon-zoomin\"></span>
					</li>
					</a>";
		}
	}

	function TaxaFreteDeTransportadora($array){		
		if($array['todosCampos']['D022_Taxa_Frete']>0)
		{
			return number_format($array['todosCampos']['D022_Taxa_Frete'], 2, '.', '');
		}
		else 
		{
			return "<font style=\"color: var(--BGVermelho)\">FALTA</font>";
		}
	}

	function adicionarEspape($x)
    {
    	$X = trim($x);
    	$x = preg_replace("/\s+/", " ",$x);

        /*$replace = array(
            '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
            '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
            'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
            'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
            'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
            'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
            'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
            'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
            'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
            'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
            '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
            'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
            'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
            'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
            'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
            'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
            'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
            'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
            '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
            'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
            'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
            'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
            'ю' => 'yu', 'я' => 'ya', '▒' => 'A',
        );*/

        //$x = str_replace(array_keys($replace), $replace, $x);

	    $x = utf8_decode($x);

        ////-- ELIMINA CARACTERES ESPECIAIS
        define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª&.$" );
        define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  BCED$" );
       	
        $x=strtr($x, utf8_decode(ACENTOS), NORMAL);

        //$x = urldecode($x);

        $novoX = '';
        for ($i = 0; $i < strlen($x); $i++) {
            $code = ord($x[$i]);
            if ($code == 192) { $novoX .= 'A'; continue; }
            if ($code == 193) { $novoX .= 'A'; continue; }
            if ($code == 194) { $novoX .= 'A'; continue; }
            if ($code == 195) { $novoX .= 'A'; continue; }
            if ($code == 196) { $novoX .= 'A'; continue; }
            if ($code == 197) { $novoX .= 'A'; continue; }
            if ($code == 198) { $novoX .= 'A'; continue; }
            if ($code == 199) { $novoX .= 'C'; continue; }
            if ($code == 200) { $novoX .= 'E'; continue; }
            if ($code == 201) { $novoX .= 'E'; continue; }
            if ($code == 202) { $novoX .= 'E'; continue; }
            if ($code == 203) { $novoX .= 'E'; continue; }
            if ($code == 204) { $novoX .= 'I'; continue; }
            if ($code == 205) { $novoX .= 'I'; continue; }
            if ($code == 206) { $novoX .= 'I'; continue; }
            if ($code == 207) { $novoX .= 'I'; continue; }
            if ($code == 210) { $novoX .= 'O'; continue; }
            if ($code == 211) { $novoX .= 'O'; continue; }
            if ($code == 212) { $novoX .= 'O'; continue; }
            if ($code == 213) { $novoX .= 'O'; continue; }
            if ($code == 214) { $novoX .= 'O'; continue; }
            if ($code == 217) { $novoX .= 'U'; continue; }
            if ($code == 218) { $novoX .= 'U'; continue; }
            if ($code == 219) { $novoX .= 'U'; continue; }
            if ($code == 220) { $novoX .= 'U'; continue; }
            if ($code == 224) { $novoX .= 'A'; continue; }
            if ($code == 225) { $novoX .= 'A'; continue; }
            if ($code == 226) { $novoX .= 'A'; continue; }
            if ($code == 227) { $novoX .= 'A'; continue; }
            if ($code == 228) { $novoX .= 'A'; continue; }
            if ($code == 229) { $novoX .= 'A'; continue; }
            if ($code == 231) { $novoX .= 'C'; continue; }
            if ($code == 232) { $novoX .= 'E'; continue; }
            if ($code == 233) { $novoX .= 'E'; continue; }
            if ($code == 234) { $novoX .= 'E'; continue; }
            if ($code == 235) { $novoX .= 'E'; continue; }
            if ($code == 236) { $novoX .= 'I'; continue; }
            if ($code == 237) { $novoX .= 'I'; continue; }
            if ($code == 238) { $novoX .= 'I'; continue; }
            if ($code == 239) { $novoX .= 'I'; continue; }
            if ($code == 242) { $novoX .= 'O'; continue; }
            if ($code == 243) { $novoX .= 'O'; continue; }
            if ($code == 244) { $novoX .= 'O'; continue; }
            if ($code == 245) { $novoX .= 'O'; continue; }
            if ($code == 246) { $novoX .= 'O'; continue; }
            if ($code == 249) { $novoX .= 'U'; continue; }
            if ($code == 250) { $novoX .= 'U'; continue; }
            if ($code == 251) { $novoX .= 'U'; continue; }
            if ($code == 252) { $novoX .= 'U'; continue; }
            $novoX .= $x[$i];
		}
		//return $novoX;
        $x = $novoX;
        
        $cCharPermitido='&ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$"';
        for ($n = 0;$n<strlen($x);$n++)
        {
             $c=substr($x,$n,1);
             if (strpos($cCharPermitido,strtoupper($c))!==FALSE)
             {
                $r.=$c;
             }
             else
             {
                $r.="";
             }
        }
        $x=$r;         
       
   	return "$x";
	}

	function ven003LogCCe($array){
	    global $g;
	    $novoIdTelaForm = uniqid();
		return "<a href=\"#\" onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'CC-e', '/ven/ven003/content/ven003contentLogCCe/','&T199_Id={$array['todosCampos']['T199_Id']}', [900,550]);\">{$array['todosCampos']['RetornoSefaz']}</a>";
	}

	function insereRegistroT001($array){
		global $g;
		$T008_Id = $array['todosCampos']['T008_Id'];
		
		if($g['usuarioAtual'] == 63 AND $array['todosCampos']['T001_Id'] <= 0){
			return <<<EOT
			<a href="#" onclick="insereRegistroT001('{$array['todosCampos']['T008_Id']}','{$g['divId']}')">
				<span title="Insere T001">Insere T001</span>
			</a>
EOT;
		}else{
			return $array['todosCampos']['T001_Id'];
		}

	}

	function T008A_D184_Id_Unidade($array, $param = array('divReload' => false)) {
		global $g;
	
		$mostrar = "<font style='background:green;color:white;padding:4px;border-radius: .425rem;'>" . $array['todosCampos']['unConversao.D037_Unidade'] . "</font>";
		$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;

		if(!empty($array['todosCampos']['T007_Numero_Protocolo_Nfe']) || $array['todosCampos']['T007_Flag_Travar_Nota'] == 'S'){
			return $mostrar;
		}
		
		$options = gGeraSelect('D184', 
							   'D184_Id', 
							   'D037_Unidade', 
							   "LEFT JOIN D037 ON D184_D037_Id = D037_Id WHERE D184_D001_Id = '{$array['todosCampos']['D001_Id']}' ORDER BY D037_Unidade", 
								true, 
								$array['todosCampos']['T008A_D184_Id']);
		$options[] = array(
			'title' => 'optgroup',
		);
	
		$options = gProcessaSelect($options);
		$idselect = uniqid();
		$iddiv    = uniqid();
		$result   = "<select id=\"{$idselect}\" onchange=\"atualizaUnidade(event, this, '{$array['todosCampos']['T008A_Id']}', '{$array['todosCampos']['T008_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
		$result   .= <<<EOT
					</select>
						<div id="{$iddiv}" onclick="$('#{$idselect}').show().focus(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								function atualizaUnidade(event, select, T008A_Id, T008_Id){
									var campo = $(select).val();
									$.getJSON('/ven/ven003/grid_func-ajax/atualizarUnidade/?ajax=true&T008A_Id='+ encodeURIComponent(T008A_Id) + '&D184_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
										if(request.code){
											divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id');
											divRefresh('{$param['divReload']}');
	
										}
									});									
								}
						}</script></div>
	EOT;
	
		return $result;
	
	}

	function campoPrecoTabelaNotaFiscal($array){
		global $g;

		$T008A_D184_Id 			   = $array['todosCampos']['T008A_D184_Id'];
		$D184_Quantidade_Conversao = $array['todosCampos']['D184_Quantidade_Conversao'];
		$D184_Tipo_Operacao        = $array['todosCampos']['D184_Tipo_Operacao'];

		if($T008A_D184_Id > 0){
			// Valida se tem unidade de conversão e faz o calculo
			if($D184_Tipo_Operacao == 'D'){
				$array['campoAtual'] = $array['campoAtual'] / $D184_Quantidade_Conversao;
			}elseif($D184_Tipo_Operacao == 'M'){
				$array['campoAtual'] = $array['campoAtual'] * $D184_Quantidade_Conversao;
			}
		}

		return $array['campoAtual'];
	}

	function ven003NFDcNumeroItem($array){
		static $numeroItem = 0;
		$numeroItem++;
		return $numeroItem;
	}

	function ven003NFDcCampoQuantidade($array){
		global $g;

		$casasQuantidade = (int) $g['c020']['C020_Casas_Decimais_Quantidade'];
		if ($casasQuantidade < 0 || $casasQuantidade > 6) $casasQuantidade = 3;
		// Coluna livre não entra no SELECT: utilizar a quantidade real já selecionada.
		$quantidade = (float) $array['todosCampos']['T008_Quantidade'];
		$quantidadeOrigem = number_format($quantidade, 6, '.', '');
		$valorExibicao = number_format($quantidade, $casasQuantidade, ',', '.');
		$T008_Id = (int) $array['todosCampos']['T008_Id'];

		return '<input type="text" class="qtdNFDCOrigem" data-t008-id="' . $T008_Id . '" data-quantidade-origem="' . $quantidadeOrigem . '" value="' . $valorExibicao . '" style="width:95px;text-align:right" />';
	}

    function ven003NFDcCampoPreco($array){
        $T008_Id = (int) $array['todosCampos']['T008_Id'];
        return '<input type="text" inputmode="decimal" class="precoNFDCOrigem" data-t008-id="' . $T008_Id . '" value="" placeholder="Manter preço" title="Preço unitário novo. Em branco, mantém o preço da origem. Até 10 casas decimais." maxlength="32" style="width:120px;text-align:right" />';
    }
	function T008A_D014_Id_PlanoContas($array){
		$sql = "SELECT D014_SubConta FROM D014 WHERE D014_Id = '{$array['todosCampos']['T008A_D014_Id']}'";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		return $row['D014_SubConta'];
	}

?>
