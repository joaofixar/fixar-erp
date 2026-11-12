<?php
namespace hardness;
require_once('prog/etc/etc006/grid_func-js.php');
require_once('prog/etc/etc006/form_func-js.php');
set_time_limit(0); 
ini_set('mysql.connect_timeout','0'); 
ini_set('max_execution_time', '0');
/**
 * Ações
 */
switch ($g['r'][3]) {
	
	/**
	* Form Email
	* Chamado em: etc006TabEmail
	*/
	case 'etc006FormEmail':
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);

		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
		$mail->setarLido($r_acaoId);
		$cabecalho = $mail->pegarMensagemCabecalho($r_acaoId);
		$body = $mail->pegarMensagemCorpo($r_acaoId);
		$anexos = $mail->listaAnexos($r_acaoId);

		// Verifica se o e-mail caiu para spam
		$sqlT139 = mysql_query("SELECT T139_Flag_Spam, T139_De, T139_T003_Id, T139_Estrutura FROM T139 WHERE T139_Id = '{$r_acaoId}'");
		$rowT139 = mysql_fetch_assoc($sqlT139);
		$rowT139['T139_De'] = str_replace('"', '', $rowT139['T139_De']);
		$temImagensOcultas = (strpos($body, 'cid:') !== false);
		if($rowT139['T139_Flag_Spam'] == 'S'){
			echo <<<EOT
				<div style="width: border-box; height: 20px; padding: 4px; background-color: #ffc; font-weight: bold; border: 1px solid #555; margin: 4px;">
					<img src='/hardness3/static/img/tmp/spam.png' style='width: 20px; height: auto; vertical-align: middle;' title='Este e-mail foi considerado um spam'/>
					Este e-mail foi enviado para spam, pois o endereço do remetente ou o domínio não foram reconhecidos pelo sistema. Para remove-lo do spam <a onclick="removerEmailSpam('{$r_acaoId}', '{$rowT139['T139_De']}', '{$g['divId']}', '{$g['divIdParent']}')" style="font-size: 12px; color: #000; cursor: pointer;">clique aqui</a>.
				</div>
EOT;
		}
		if($temImagensOcultas){
			echo <<<EOT
				<div style="width: border-box; min-height: 16px; padding: 4px; background-color: #fff; font-weight: bold; border: 1px solid #555; margin: 4px;" class="imagensOcultasNotificacao">
					<img src='/hardness3/static/img/tmp/photo.png' style='width: 20px; height: auto; vertical-align: middle;' title='Exibir imagens do corpo do e-mail'/>
					Este e-mail possui imagens incorporadas ocultas, <a onclick="processarImagensEmail('{$r_acaoId}', '{$g['divId']}')" style="font-size: 12px; color: #000; cursor: pointer;">clique aqui</a> para exibir.
				</div>
EOT;
		}

		// Impressão
		if (isset($r_p)) { 
			echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>{$cabecalho['T139_Titulo']}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript">
			window.print();
		</script>
	</head>
	<body style="overflow: auto; padding: 2px;">
EOT;
		} else {
			echo '<div class="visuEmail" style="padding: 10px; padding-top: 3px;">';
		}
		// Cabeçalho
		foreach ($cabecalho as $key => &$value) {
			$value = htmlentities($value,ENT_COMPAT,'utf-8');
		}
		//Remover cabeçalho quando aberto na tela de leitura de anexos
		if(!isset($r_removerCabecalho)){
			echo '<table>';
			//$tmpD013_Id = $mail->associarEmails($r_acaoId);
			if (empty($tmpD013_Id)) {
				$addTxt = '';
				$addemail = "<a href=\"#\" style=\"color: #413a3a; text-decoration:none;\" id=\"etc006AddEmail\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Contato', '/etc/etc006/content/etc006ContentCadastroContato/', '&T139_Id=".urlencode($r_acaoId)."&email=".urlencode(base64_encode($cabecalho['T139_De']))."', [500,220]); return false;\">";
			} else {
				$sql = <<<EOT
					SELECT D024_Nome_Fantasia, D013_Nome_Contato FROM T139
					LEFT JOIN D024 ON D024_Id=T139_D024_Id
					LEFT JOIN D013 ON D013_Id=T139_D013_Id
					WHERE T139_Id = '{$r_acaoId}'
EOT;
				$res = mysql_query($sql);
				$row = mysql_fetch_assoc($res);
				$addTxt = "({$row['D013_Nome_Contato']} em {$row['D024_Nome_Fantasia']})";
				$addTxt = (strlen($addTxt) > 50) ? '<br>'.$addTxt : $addTxt;
				$addTxt = "<span style=\"font-size: 11px;\">&nbsp;{$addTxt}</span>";
				$addemail = "<a href=\"\" style=\"color: #413a3a; text-decoration:none;\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Contato', '/etc/etc006/content/etc006ContentCadastroContato/', '&acaoId=".urlencode($tmpD013_Id)."&T139_Id=".urlencode($r_acaoId)."&email=".urlencode(base64_encode($cabecalho['T139_De']))."', [500,220]); return false;\">";
			}
			$cabecalho['T139_De'] = str_replace('"', '', $cabecalho['T139_De']);
			$email = explode('&lt;', $cabecalho['T139_De']);
			if(count($email) > 1){
				$email[1] = "&lt;".$email[1];
			}
			$txtEmail = "<span style=\"font-weight: bold;\" title=\"{$email[0]} - {$email[1]}\">{$email[0]}</span>";

			$cabecalho['T139_Para'] = str_replace('"', '', $cabecalho['T139_Para']);
			$emailPara = explode('&lt;', $cabecalho['T139_Para']);
			if(count($emailPara) > 1){
				$emailPara[1] = "&lt;".$emailPara[1];
			}

			//Verifica se o anexo foi arquivado em algum produto
			$arrayAnexosArquivados = array();
			$sqlAnexosArquivados = mysql_query("SELECT T140_Id_Anexo, D001_Codigo_Produto, D001_Descricao_Produto FROM T140 LEFT JOIN D001 ON D001_Id=T140_D001_Id WHERE T140_T139_Id = '{$r_acaoId}'");
			while($rowAnexosArquivados = mysql_fetch_assoc($sqlAnexosArquivados)){
				$arrayAnexosArquivados[$rowAnexosArquivados['T140_Id_Anexo']][] = $rowAnexosArquivados['D001_Codigo_Produto'] . ' - ' . $rowAnexosArquivados['D001_Descricao_Produto'];
			}
			if (sizeof($anexos) > 0) {
				echo <<<EOT
					<tr>
						<td style="font-size: 11px; width: 28px;">De:</td>
						<td style="font-size: 12px; max-width:100%; white-space:nowrap;">{$addemail}{$txtEmail}</a>{$addTxt}<td>
						<td rowspan="2" style="font-size: 11px; vertical-align: top; border-left: 1px solid #888; padding-left: 5px;">
EOT;
	   			for ($j=0;$j<sizeof($anexos);$j++) {
	   				$anexos[$j] = mb_strtolower($anexos[$j], 'UTF-8');
	   				if(empty($anexos[$j])) { continue; }
	   				$nomeCompleto = $anexos[$j];
	   				if(strlen($anexos[$j]) > 22){
	   					$nomeAnexo = strrev(substr(strrev($anexos[$j]),0,4));
	   					$anexos[$j] = substr($anexos[$j], 0, 15) . '..' . $nomeAnexo;
	   				}
	   				$xml = $produtoAnexado = $cssProdutoAnexado = '';
	   				if(isset($arrayAnexosArquivados[$j])){
	   					foreach ($arrayAnexosArquivados[$j] as $produto) {
	   						$produtoAnexado .= trim($produto)."\n";
	   					}
	   					$produtoAnexado = "\n Anexado ao(s) produto(s): \n" . $produtoAnexado;
	   					$cssProdutoAnexado = 'color: blue;';
	   				}
	   				if (isset($g['c029Ids'][21]) && preg_match('/\.xml$/i', $anexos[$j])) {
	   					$xml = "&nbsp;&nbsp;<a href='#' onclick=\"importarXML('{$j}')\"><button type='button' style='padding: 0;' class='xmlbtn'>Importar XML</button></a>";
					}
					$iconeArquivar = "<span style=\"float: right; margin-left: 2px;\" onclick=\"arquivarAnexo('{$g['divId']}', '{$r_acaoId}', '{$j}', '{$nomeCompleto}');\"><span class=\"ui-icon ui-icon-folder-collapsed\" style=\"cursor: pointer; margin-right: -4px;\" title=\"Download anexo\"></span></span>";
					if(preg_match('/[\.pdf|\.png|\.jpg|\.txt|\.xls|\.xlsx|\.doc|\.docx]$/i', $anexos[$j])){
						echo "<div style=\"display: inline; float: left; margin-right: 10px;\" title=\"{$nomeCompleto} {$produtoAnexado}\"><span onclick=\"downloadAnexo('{$r_acaoId}', '{$j}');\"><span class=\"ui-icon ui-icon-arrowthick-1-s\" style=\"float:left; cursor: pointer; margin-right: -4px;\" title=\"Download anexo\"></span></span><span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><a href=\"#\" style=\"color: #413a3a; {$cssProdutoAnexado}\" onclick=\"abrirTelaLeituraAnexo('{$j}', '{$rowT139['T139_T003_Id']}')\">{$anexos[$j]}</a> {$iconeArquivar}</div>";
					} else {
	   					echo "<div style=\"display: inline; float: left; margin-right: 10px;\" title=\"{$nomeCompleto} {$produtoAnexado}\"><span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><a href=\"#\" style=\"color: #413a3a; {$cssProdutoAnexado}\" onclick=\"downloadAnexo('{$r_acaoId}', '{$j}')\">{$anexos[$j]}</a>{$xml} {$iconeArquivar}</div>";
	   				}
	   			}
	   			echo <<<EOT
	   					</td>
	   				</tr>
					<tr>
						<td style="width: 28px;">Para:</td>
						<td style="font-size: 12px;" title="{$emailPara[0]} - {$emailPara[1]}">{$emailPara[0]}<td>
					</tr>
EOT;
			} else {
				echo "<tr><td>De:</td><td>{$addemail}{$txtEmail}</a>{$addTxt}<td></tr>";
				echo "<tr><td>Para:</td><td style=\"font-weight: bold;\" title=\"{$emailPara[0]} - {$emailPara[1]}\">{$emailPara[0]}<td></tr>";
			}

			if (!empty($cabecalho['T139_Cc'])) {
				echo "<tr><td>Cc:</td><td style=\"font-weight: bold;\">{$cabecalho['T139_Cc']}<td></tr>";
			}
			if (!empty($cabecalho['T139_Cco'])) {
				echo "<tr><td>Cco:</td><td style=\"font-weight: bold;\">{$cabecalho['T139_Cco']}<td></tr>";
			}
			//echo "<tr><td>Horário:</td><td style=\"font-weight: bold;\">{$cabecalho['T139_Data_Hora']}<td></tr>";
			//echo "<tr><td>Assunto:</td><td style=\"font-weight: bold; font-size: 15px;\">{$cabecalho['T139_Titulo']}<td></tr>";
			// Anexos
			echo '</table>';

			echo '<div style="width: 100%; height: 1px; background-color: #888;"></div>';
		}
		echo '<div style="width: 100%; height: 100%; padding-top: 5px;" class="corpoEmailMontado"><font size="2">';
			$rowT139['T139_Estrutura'] = unserialize($rowT139['T139_Estrutura']);
			$estrutura = pegarCodificacao($rowT139['T139_Estrutura']);
			$body = ($estrutura == '' && strpos($body, 'windows-1252') === false && strpos($body, 'iso-8859-1') === false) ? utf8_decode($body) : $body;
			echo $body;
		echo '</font></div>';

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function abrirTelaLeituraAnexo(nr, T003_Id) {
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&ocultarOrcamento=true&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&file=' + nr + '&T003_Id=' + encodeURIComponent(T003_Id) + '&C007_Id=' + encodeURIComponent("{$r_C007_Id}"), [900,600]);
			}
			function importarXML(nr) {
				var buttons = { 
					"Sim": function() { 
						$('#{$g['divId']}').showLoading();
						$.getJSON('/etc/etc006/form_func-ajax/importarXML/?ajax=true&acaoId={$r_acaoId}&file=' + encodeURIComponent(nr) + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								dialogConfirm("Ok!", 'Arquivo importado com Sucesso!');
							} else {
								dialogConfirm("Erro!", request.data);
							}
						});
						$(this).dialog("destroy").remove(); 
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
    			dialogConfirm("Importar XML", 'Deseja importar este XML?', buttons)
			}
			$('#{$g['divId']}').css({'background': 'white', 'border-bottom': '1px solid silver'});
			//$('.xmlbtn > span.ui-button-text').css('margin', '0 7px');
		} </script></div>
EOT;
		echo '</div>';
		
		// Impressão
		if (isset($r_p)) { 
			echo <<<EOT
	</body>
</html>
EOT;
		}
	break;
	
	/**
	* Form Email
	* Chamado em: etc006TabEmail
	*/
	case 'etc006FormEmailEnviar':
		// Trata os diferentes casos de abertura para enviar email
		$monta = array();
		// Via resposta
		if (!isset($r_emailEnviado) && isset($r_acaoId) && !empty($r_acaoId)) {
			$r_acaoId = mysql_real_escape_string($r_acaoId);
			$sql = "SELECT * FROM T139 WHERE T139_Id = {$r_acaoId};";
			$retorno = mysql_query($sql);
			$row = mysql_fetch_assoc($retorno);
			$monta = array();
			$monta['Para'] = $row['T139_De'];
			$monta['Assunto'] = 'Re: ' . $row['T139_Titulo'];
			$monta['Corpo'] = $row['T139_Mensagem_Corpo'];
			$monta['Corpo'] = '<p><br _moz_dirty="" type="_moz"></p>' . "{$row['T139_De']} escreveu em {$row['T139_Data_Hora']}:\n<br /><hr>" . $monta['Corpo'];
		}
		// Via Boletos NFE
        if (!empty($r_idNF)) {
        	require_once('bibliotecas/classes/VEN003.php');
			$VEN003 = new VEN003();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $VEN003->montarEmailBoletos($r_idNF);
			}else{
				$monta_ = $VEN003->montarEmailBoletos($r_idNF);
			}
		}
		// Via orçamento
		if (!empty($r_T003_Id)) {
			require_once('bibliotecas/classes/VEN001.php');
			$VEN001 = new VEN001();
			$formatoOrcamento = $g['C031']['formatoOrcamentoEmail'];
			if ($formatoOrcamento == 'html') {
				// html
				if (!isset($r_emailEnviado)) {
					$monta = $VEN001->montarEmail($r_T003_Id);
				}
			} else if ($formatoOrcamento == 'pdf') {
				// pdf
				if (!isset($r_emailEnviado)) {
					$monta = $monta_ = $VEN001->montarEmailPdf($r_T003_Id);
				} else {
					$monta_ = $VEN001->montarEmailPdf($r_T003_Id);
				}
			} else {
				// ambos
				if (!isset($r_emailEnviado)) {
					$monta = $monta_ = $VEN001->montarEmail($r_T003_Id);
					$montaTmp = $VEN001->montarEmailPdf($r_T003_Id);
					$monta['Anexos'] = $monta_['Anexos'] = $montaTmp['Anexos'];
					$monta['AnexosWeb'] = $monta_['AnexosWeb'] = $montaTmp['AnexosWeb'];
				} else {
					$monta_ = $VEN001->montarEmail($r_T003_Id);
					$montaTmp = $VEN001->montarEmailPdf($r_T003_Id);
					$monta_['Anexos'] = $montaTmp['Anexos'];
					$monta_['AnexosWeb'] = $montaTmp['AnexosWeb'];
				}
			}
		}

		// Via Cotação de Compra novo (email link)
		
		if (!empty($r_emailLink) && $r_emailLink == true) {
			require_once('bibliotecas/classes/EST015.php');
			$EST015 = new EST015();
			if (!isset($r_emailEnviado)) {
				if ($g['C031']['habilitaEmail'] == 'S') {
					$monta = $monta_ = $EST015->montarEmailLinkPolux($r_T218_Id);
					if(empty($monta)){
						$monta = $monta_ = $EST015->montarEmailLink($r_T218_Id);
					}
				}else{
					$monta = $monta_ = $EST015->montarEmailLink($r_T218_Id);
				}
			} else {
				if ($g['C031']['habilitaEmail'] == 'S') {
					$monta_ = $EST015->montarEmailLinkPolux($r_T218_Id);
					if(empty($monta_)){
						$monta_ = $EST015->montarEmailLink($r_T218_Id);
					}
				}else{
					$monta_ = $EST015->montarEmailLink($r_T218_Id);
				}
			}
		}
		
		// Enviar dados do cliente B2B (email link)
		if ($r_senhaB2B == true) {
			require_once('bibliotecas/classes/B2B001.php');
			$B2B001 = new B2B001();
			$monta = $monta_ = $B2B001->montarEmailSenhaB2B($r_D013_Id);
		}

		// Via Cotação de Compra novo (email link)
		if (!empty($r_T218_Id) && empty($r_emailLink)) {
			require_once('bibliotecas/classes/EST015.php');
			$EST015 = new EST015();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $EST015->montarEmail($r_T218_Id);
			} else {
				$monta_ = $EST015->montarEmail($r_T218_Id);
			}
		}

		// Via Pedido
		if (!empty($r_T005_Id)) {
			require_once('bibliotecas/classes/VEN002.php');
			$VEN002 = new VEN002();
			$formatoPedido = $g['C031']['formatoPedidoEmail'];
			if ($formatoPedido == 'html') {
				// html
				if (!isset($r_emailEnviado)) {
					$monta = $VEN002->montarEmail($r_T005_Id);
				}
			} else if ($formatoPedido == 'pdf') {
				// pdf
				if (!isset($r_emailEnviado)) {
					$monta = $monta_ = $VEN002->montarEmailPdf($r_T005_Id);
				} else {
					$monta_ = $VEN002->montarEmailPdf($r_T005_Id);
				}
			} else {
				// ambos
				if (!isset($r_emailEnviado)) {
					$monta = $monta_ = $VEN002->montarEmail($r_T005_Id);
					$montaTmp = $VEN002->montarEmailPdf($r_T005_Id);
					$monta['Anexos'] = $monta_['Anexos'] = $montaTmp['Anexos'];
					$monta['AnexosWeb'] = $monta_['AnexosWeb'] = $montaTmp['AnexosWeb'];
				} else {
					$monta_ = $VEN002->montarEmail($r_T005_Id);
					$montaTmp = $VEN002->montarEmailPdf($r_T005_Id);
					$monta_['Anexos'] = $montaTmp['Anexos'];
					$monta_['AnexosWeb'] = $montaTmp['AnexosWeb'];
				}
			}
		}

		// Via Pedido
		//if (!isset($r_emailEnviado) && !empty($r_T005_Id)) {
//			require_once('bibliotecas/classes/VEN002.php');
//			$VEN002 = new VEN002();
//			$monta = $VEN002->montarEmail($r_T005_Id);
//		}
		// Via Agenda de Contatos
		if (!isset($r_emailEnviado) && !empty($r_D117_Id)) {
			require_once('bibliotecas/classes/CAD013.php');
			$CAD013 = new CAD013();
			$monta = $CAD013->montarEmail($r_D117_Id);
		}
		// Via Cliente/Fornecedor
		if (!isset($r_emailEnviado) && !empty($r_D024_Id)) {
			require_once('bibliotecas/classes/CAD001.php');
			$CAD001 = new CAD001();
			$monta = $CAD001->montarEmail($r_D024_Id);
		}
		// Via Ordem de Compra
		if (!empty($r_T011_Id)) {
			require_once('bibliotecas/classes/EST006.php');
			$EST006 = new EST006();
			// ambos
			if (!isset($r_emailEnviado)) {
				//$monta = $monta_ = $EST006->montarEmail($r_T011_Id);
				$monta = $EST006->montarEmailPdf($r_T011_Id);
				$monta['Anexos'] = $monta_['Anexos'] = $monta['Anexos'];
				$monta['AnexosWeb'] = $monta_['AnexosWeb'] = $monta['AnexosWeb'];
			} else {
				$monta_ = $EST006->montarEmailPdf($r_T011_Id);
				$montaTmp = $EST006->montarEmailPdf($r_T011_Id);
				$monta_['Anexos'] = $montaTmp['Anexos'];
				$monta_['AnexosWeb'] = $montaTmp['AnexosWeb'];
			}
		}
		// DI - Declaração de Importação
		if (!isset($r_emailEnviado) && !empty($r_T075_Id)) {
			require_once('bibliotecas/classes/IMP003.php');
			$IMP003 = new IMP003();
			$monta = $IMP003->montarEmail($r_T075_Id);
		}
		// Boleto bancário
		if(!empty($r_T117_Id)) {
			if (!isset($r_emailEnviado)) {
				require_once('bibliotecas/classes/FIN005.php');
				$FIN005 = new FIN005();
				$monta = $FIN005->montarEmail($r_T117_Id);
				$montaPDF = $FIN005->montarEmailPdf($r_T117_Id);
				$monta_['Anexos'] = $montaPDF['Anexos'];
				$monta_['AnexosWeb'] = $montaPDF['AnexosWeb'];
			} else if(isset($r_emailEnviado)){
				require_once('bibliotecas/classes/FIN005.php');
				$FIN005 = new FIN005();
				$montaPDF = $FIN005->montarEmailPdf($r_T117_Id);
				$monta_['Anexos'] = $montaPDF['Anexos'];
				$monta_['AnexosWeb'] = $montaPDF['AnexosWeb'];
			}
		}

		// Via NF ou ACP
		if (!empty($r_T007_Id)) {
			$T007_Flag_ACP = $r_T007_Flag_ACP;
			log("T007_Flag_ACP = ". $T007_Flag_ACP);
			require_once('bibliotecas/classes/VEN003.php');
			$VEN003 = new VEN003();
			if (!isset($r_emailEnviado)) {
				if($T007_Flag_ACP == 3){
					$monta = $monta_ = $VEN003->montarEmailACP($r_T007_Id);
				} else {
					$monta = $monta_ = $VEN003->montarEmail($r_T007_Id);
				}	
			} else {
				if($T007_Flag_ACP == 3){
					$monta_ = $VEN003->montarEmailACP($r_T007_Id);
				}else{
					$monta_ = $VEN003->montarEmail($r_T007_Id);
				}		
			}
			if(!is_array($monta_)){
				echo gGeraAlertMsg('Erro', $monta_);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela('{$g['divId']}');
					} </script></div>
EOT;
				break;
			}
		}
		// Via Reparcelamento contas a receber
		if (!empty($r_T166_Id)) {
			require_once('bibliotecas/classes/FIN001.php');
			$FIN001 = new FIN001();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $FIN001->montarEmailReparcelamento($r_T166_Id);
			} else {
				$monta_ = $FIN001->montarEmailReparcelamento($r_T166_Id);
			}
		}
		// Via Reparcelamento contas a pagar
		if (!empty($r_T196_Id)) {
			require_once('bibliotecas/classes/FIN002.php');
			$FIN002 = new FIN002();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $FIN002->montarEmailReparcelamentoPagar($r_T196_Id);
			} else {
				$monta_ = $FIN002->montarEmailReparcelamentoPagar($r_T196_Id);
			}
		}
		// Via Cotação de Compra
		if (!empty($r_T173_Id)) {
			require_once('bibliotecas/classes/EST008.php');
			$EST008 = new EST008();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $EST008->montarEmail($r_T173_Id);
			} else {
				$monta_ = $EST008->montarEmail($r_T173_Id);
			}
		}
		// Via Controle Interno
		if (!empty($r_ETC013OS)) {
			require_once('bibliotecas/classes/EST013.php');
			$EST013 = new EST013();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $EST013->montarEmail($r_ETC013OS);
			} else {
				$monta_ = $EST013->montarEmail($r_ETC013OS);
			}
		}
		// Via Cotação de Compra
		if (!empty($r_T010_Id)) {
			require_once('bibliotecas/classes/EST008.php');
			$EST008 = new EST008();
			// pdf
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $EST008->EST008montarEmailPdf($r_T010_Id, $r_T173_D024_Id);
			} else {
				$monta_ = $EST008->EST008montarEmailPdf($r_T010_Id, $r_T173_D024_Id);
			}
		}
        // Via CCe
        if (!empty($r_T199_Id)) {
            require_once('bibliotecas/classes/VEN003.php');
            $VEN003 = new VEN003();
            if (!isset($r_emailEnviado)) {
                $monta = $monta_ = $VEN003->montarEmailPdfCce($r_T199_Id);
            } else {
                $monta_ = $VEN003->montarEmailPdfCce($r_T199_Id);
            }
        }

		if (!empty($r_T002_Id) && $r_emailTituloReceber == 'S') {
			require_once('bibliotecas/classes/FIN001.php');
            $FIN001 = new FIN001();
			if (!isset($r_emailEnviado)) {
                $monta = $monta_ = $FIN001->montarEmail($r_T002_Id);
            } else {
                $monta_ = $FIN001->montarEmail($r_T002_Id);
            }
		}

		if (!empty($r_T002_Id) && ($r_emailTituloReceber != 'S' || $r_emailTituloReceber == '')) {
       		 // Via Contas a Receber
            require_once('bibliotecas/classes/FIN001.php');
            $FIN001 = new FIN001();
            if (!isset($r_emailEnviado)) {
                $monta = $monta_ = $FIN001->montarEmailPdf($r_T002_Id);
            } else {
                $monta_ = $FIN001->montarEmailPdf($r_T002_Id);
            }
        }

		// Via Reparcelamento contas a receber
		if (!empty($r_T180_D024_Id)) {
			require_once('bibliotecas/classes/FIN001.php');
			$FIN001 = new FIN001();
			if (!isset($r_emailEnviado)) {
				$monta = $monta_ = $FIN001->montarEmailSimulacaoReparcelamento($r_T180_D024_Id);
			} else {
				$monta_ = $FIN001->montarEmailSimulacaoReparcelamento($r_T180_D024_Id);
			}
		}

		// Via Ordem de Compra Novo
		// carlos carvalho
        if (!empty($r_T224_Id) && empty($r_emailFollowUp)) {
            require_once('bibliotecas/classes/EST016.php');
            $EST016 = new EST016();
            if (!isset($r_emailEnviado)) {
                $monta = $monta_ = $EST016->montarEmail($r_T224_Id);
            } else {
                $monta_ = $EST016->montarEmail($r_T224_Id);
            }
        }

		//Via email follow up
		if (!empty($r_T224_Id) && !empty($r_emailFollowUp)) {
            require_once('bibliotecas/classes/EST016.php');
            $EST016 = new EST016();
            if (!isset($r_emailEnviado)) {
                $monta = $monta_ = $EST016->montarEmailSMT($r_T224_Id);
            } else {
                $monta_ = $EST016->montarEmailSMT($r_T224_Id);
            }
        }

        // Via Agenda
        if (!empty($r_D085_Id)) {
            require_once('bibliotecas/classes/ETC006.php');
            $ETC006 = new ETC006();
            if (!isset($r_emailEnviado)) {
                $monta = $monta_ = $ETC006->montarEmailAgenda($r_D085_Id);
            } else {
                $monta_ = $ETC006->montarEmailAgenda($r_D085_Id);
            }
        }

		// Monta
		$r_T139_Valor_Total = isset($r_T139_Valor_Total) ? $r_T139_Valor_Total : false;
		$r_T139_Numero_Registros = isset($r_T139_Numero_Registros) ? $r_T139_Numero_Registros : false;
		if (!empty($monta)) {

			$r_emailPara = $monta['Para'];
			$r_emailParaCc = isset($monta['cc']) ? $monta['cc'] : '';
			$r_emailAssunto = $monta['Assunto'];
			$r_emailMensagem = $monta['Corpo'];
			$r_T180_D024_Id = (isset($monta['T180_D024_Id']) && !empty($monta['T180_D024_Id'])) ? $monta['T180_D024_Id'] : $r_T180_D024_Id;
			$r_D024_Id = (isset($monta['D024_Id']) && !empty($monta['D024_Id'])) ? $monta['D024_Id'] : $r_D024_Id;
			// $r_senhaB2B = isset($r_senhaB2B)? $r_senhaB2B: false;
			$r_D013_Id = (isset($monta['D013_Id']) && !empty($monta['D013_Id'])) ? $monta['D013_Id'] : $r_D013_Id;
			$r_T003_Id = (isset($monta['T003_Id']) && !empty($monta['T003_Id'])) ? $monta['T003_Id'] : $r_T003_Id;
			$r_T218_Id = (isset($monta['T218_Id']) && !empty($monta['T218_Id'])) ? $monta['T218_Id'] : $r_T218_Id;
			$r_T005_Id = (isset($monta['T005_Id']) && !empty($monta['T005_Id'])) ? $monta['T005_Id'] : $r_T005_Id;
			$r_T224_Id = (isset($monta['T224_Id']) && !empty($monta['T224_Id'])) ? $monta['T224_Id'] : $r_T224_Id;
			$r_D117_Id = (isset($monta['D117_Id']) && !empty($monta['D117_Id'])) ? $monta['D117_Id'] : $r_D117_Id;
			$r_T011_Id = (isset($monta['T011_Id']) && !empty($monta['T011_Id'])) ? $monta['T011_Id'] : $r_T011_Id;
			$r_T075_Id = (isset($monta['T075_Id']) && !empty($monta['T075_Id'])) ? $monta['T075_Id'] : $r_T075_Id;
			$r_T117_Id = (isset($monta['T117_Id']) && !empty($monta['T117_Id'])) ? $monta['T117_Id'] : $r_T117_Id;
			$r_T007_Id = (isset($monta['T007_Id']) && !empty($monta['T007_Id'])) ? $monta['T007_Id'] : $r_T007_Id;
			$r_T166_Id = (isset($monta['T166_Id']) && !empty($monta['T166_Id'])) ? $monta['T166_Id'] : $r_T166_Id;
			$r_T196_Id = (isset($monta['T196_Id']) && !empty($monta['T196_Id'])) ? $monta['T196_Id'] : $r_T196_Id;
			$r_T173_Id = (isset($monta['T173_Id']) && !empty($monta['T173_Id'])) ? $monta['T173_Id'] : $r_T173_Id;
            $r_T199_Id = (isset($monta['T199_Id']) && !empty($monta['T199_Id'])) ? $monta['T199_Id'] : $r_T199_Id;
			$r_T010_Id = (isset($monta['T010_Id']) && !empty($monta['T010_Id'])) ? $monta['T010_Id'] : $r_T010_Id;
			$r_T002_Id = (isset($monta['T002_Id']) && !empty($monta['T002_Id'])) ? $monta['T002_Id'] : $r_T002_Id;
			$r_emailTituloReceber = (isset($monta['emailTituloReceber']) && !empty($monta['emailTituloReceber'])) ? $monta['emailTituloReceber'] : $r_emailTituloReceber;
			$r_idNF = (isset($monta['idNF']) && !empty($monta['idNF'])) ? $monta['idNF'] : $r_idNF;
			$r_T173_D024_Id = (isset($monta['T173_D024_Id']) && !empty($monta['T173_D024_Id'])) ? $monta['T173_D024_Id'] : $r_T173_D024_Id;
			$r_T139_Valor_Total = (isset($monta['T139_Valor_Total']) && !empty($monta['T139_Valor_Total'])) ? $monta['T139_Valor_Total'] : $r_T139_Valor_Total;
			$r_T139_Numero_Registros = (isset($monta['T139_Numero_Registros']) && !empty($monta['T139_Numero_Registros'])) ? $monta['T139_Numero_Registros'] : $r_T139_Numero_Registros;
			$r_T173_Id_EmailGravar = (isset($monta['T173_Id_EmailGravar']) && !empty($monta['T173_Id_EmailGravar'])) ? $monta['T173_Id_EmailGravar'] : $r_T173_Id_EmailGravar;		
		}

		// Email
		$r_emailPara = isset($r_emailPara) ? $r_emailPara : '';
		$r_emailPara_ = htmlentities($r_emailPara,ENT_COMPAT,'utf-8');
		$r_emailParaCc = isset($r_emailParaCc) ? $r_emailParaCc : '';
		$r_emailParaCc_ = htmlentities($r_emailParaCc,ENT_COMPAT,'utf-8');
		$r_emailParaCco = isset($r_emailParaCco) ? $r_emailParaCco : '';
		$r_emailParaCco_ = htmlentities($r_emailParaCco,ENT_COMPAT,'utf-8');
		$r_emailAssunto = isset($r_emailAssunto) ? $r_emailAssunto : '';
		$r_emailAssunto_ = htmlentities($r_emailAssunto,ENT_COMPAT,'utf-8');
		$r_emailAssuntoGravarBanco = $r_emailAssunto;
		$r_emailMensagem = isset($r_emailMensagem) ? $r_emailMensagem : '';
		if (!isset($r_emailEnviado) && !empty($g['c007']['C007_Email_Assinatura']) && trim($g['c007']['C007_Email_Assinatura']) != '<br>') {
			if (isset($r_acaoId) && !empty($r_acaoId)) {
				// Via resposta
				$r_emailMensagem = "<font size='3'><br /><br /><font size='2'>{$g['c007']['C007_Email_Assinatura']}</font></font>" . $r_emailMensagem;
			} else {
				$r_emailMensagem .= "<font size='3'><br /><br /><font size='2'>{$g['c007']['C007_Email_Assinatura']}</font></font>";
			}
		}
		$r_emailMensagem_ = htmlentities($r_emailMensagem,ENT_COMPAT,'utf-8');

		$editor = 'var' . uniqid();
		$sql = "SELECT * FROM C007 WHERE C007_id = '{$g['usuarioAtual']}';";
		$C007 = mysql_query($sql);
		$C007 = mysql_fetch_assoc($C007);

		if (isset($r_suporte) || isset($r_emailNotificacaoSuporte)) {
			if (empty($C007['C007_Email']) || isset($r_emailNotificacaoSuporte)) {
				$C007['C007_Email'] = 'suporte@hardness.com.br';
				$C007['C007_Primeiro_Nome'] = 'Suporte Hardness';
			}
			$C007['C007_Email_POP_Servidor'] = 'mail.hardness.com.br';
			$C007['C007_Email_POP_Porta'] = '143';
			$C007['C007_Email_SMTP_Senha'] = 'hard3871';
			$C007['C007_Email_SMTP_Servidor'] = 'mail.hardness.com.br';
			$C007['C007_Email_SMTP_Autenticacao'] = 'true';
			$C007['C007_Email_SMTP_Usuario'] = 'suporteinterno@hardness.com.br';
			$C007['C007_Email_SMTP_Porta'] = '587';

			if(strpos($_SERVER['HTTP_HOST'], "chasistemas") !== false){
				if (empty($C007['C007_Email']) || isset($r_emailNotificacaoSuporte)) {
					$C007['C007_Email'] = 'suporte@chasistemas.com.br';
					$C007['C007_Primeiro_Nome'] = 'Suporte CHA Sistemas';
				}
				$C007['C007_Email_POP_Servidor'] = 'email-ssl.com.br';
				$C007['C007_Email_POP_Porta'] = '465';
				$C007['C007_Email_SMTP_Senha'] = 'chasup2016*';
				$C007['C007_Email_SMTP_Servidor'] = 'email-ssl.com.br';
				$C007['C007_Email_SMTP_Autenticacao'] = 'true';
				$C007['C007_Email_SMTP_Usuario'] = 'suporte@chasistemas.com.br';
				$C007['C007_Email_SMTP_Porta'] = '993';
			}
		}

        if (empty($C007['C007_Email'])) {
                $C007['C007_Email'] = 'nfe@hardness.com.br';
                $C007['C007_Primeiro_Nome'] = $C004['C004_Nome_Abreviado'];
                $C007['C007_Email_SMTP_Senha'] = 'hgrw nvpe xliv cluz';
                $C007['C007_Email_SMTP_Servidor'] = 'smtp.gmail.com';
                $C007['C007_Email_SMTP_Autenticacao'] = 'true';
                $C007['C007_Email_SMTP_Usuario'] = 'nfe@hardness.com.br';
                $C007['C007_Email_SMTP_Porta'] = '465';
        }

		//Email padrao para NFe
		if (!empty($r_T007_Id) && !empty($g['C031']['NFe_Email'])) {
			$C007['C007_Email'] = $g['C031']['NFe_Email'];
			$C007['C007_Primeiro_Nome'] = "NFe";
			$C007['C007_Email_SMTP_Senha'] = $g['C031']['NFe_Email_SMTP_Senha'];
			$C007['C007_Email_SMTP_Servidor'] = $g['C031']['NFe_Email_SMTP_Servidor'];
			$C007['C007_Email_SMTP_Autenticacao'] = $g['C031']['NFe_Email_SMTP_Autenticacao'];
			$C007['C007_Email_SMTP_Usuario'] = $g['C031']['NFe_Email_SMTP_Usuario'];
			$C007['C007_Email_SMTP_Porta'] = $g['C031']['NFe_Email_SMTP_Porta'];
		}

		//Email padrao para Boleto
		if (!empty($r_T117_Id) && !empty($g['C031']['Boleto_Email'])) {
			$C007['C007_Email'] = $g['C031']['Boleto_Email'];
			$C007['C007_Primeiro_Nome'] = "Financeiro";
			$C007['C007_Email_SMTP_Senha'] = $g['C031']['Boleto_Email_SMTP_Senha'];
			$C007['C007_Email_SMTP_Servidor'] = $g['C031']['Boleto_Email_SMTP_Servidor'];
			$C007['C007_Email_SMTP_Autenticacao'] = $g['C031']['Boleto_Email_SMTP_Autenticacao'];
			$C007['C007_Email_SMTP_Usuario'] = $g['C031']['Boleto_Email_SMTP_Usuario'];
			$C007['C007_Email_SMTP_Porta'] = $g['C031']['Boleto_Email_SMTP_Porta'];
		}

		//Email padrao para OC e Cotação
		if (((!empty($r_T218_Id) || !empty($r_T011_Id) || !empty($r_T173_Id) || !empty($r_T010_Id) || !empty($r_T224_Id)) || (!empty($r_emailLink) && $r_emailLink == true)) && !empty($g['C031']['OC_Email'])) {
			$C007['C007_Email'] = $g['C031']['OC_Email'];
			$C007['C007_Primeiro_Nome'] = "Compras";
			$C007['C007_Email_SMTP_Senha'] = $g['C031']['OC_Email_SMTP_Senha'];
			$C007['C007_Email_SMTP_Servidor'] = $g['C031']['OC_Email_SMTP_Servidor'];
			$C007['C007_Email_SMTP_Autenticacao'] = $g['C031']['OC_Email_SMTP_Autenticacao'];
			$C007['C007_Email_SMTP_Usuario'] = $g['C031']['OC_Email_SMTP_Usuario'];
			$C007['C007_Email_SMTP_Porta'] = $g['C031']['OC_Email_SMTP_Porta'];
		}

		// Esta function pode ser personalizada para retornar um array com personalizações para utilização neste código
		require_once('bibliotecas/classes/ETC006.php');
        $ETC006 = new ETC006();
        $arrayPersonalizacaoDe = $ETC006->personalizacaoDe($C007['C007_Primeiro_Nome']);
		
		//$De = $arrayPersonalizacaoDe['De'];

		$De = htmlentities("\"{$arrayPersonalizacaoDe['De']}\" <{$C007['C007_Email']}>",ENT_COMPAT,'utf-8');

		if (isset($r_emailEnviado) && empty($r_emailPara)) {
			echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher o campo "Para:"');
		} else if (isset($r_emailEnviado) && empty($r_emailAssunto)) {
			echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher o campo "Assunto:"');
		} else if (isset($r_emailEnviado) && empty($r_emailMensagem)) {
			echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher uma mensagem no texto do e-mail.');
		} else if (isset($r_emailEnviado)) {
			//require("bibliotecas/phpmailer/phpmailer.inc.php");
			require("bibliotecas/phpmailer/class.phpmailer.php");
			$mail = new \phpmailer;
			$mail->CharSet = "UTF-8";

			// Mail
			$mail->From     = $C007['C007_Email'];
			$mail->FromName = $arrayPersonalizacaoDe['De'];
			$mail->Host     = $C007['C007_Email_SMTP_Servidor'];
			$mail->Mailer   = "smtp";
			$mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
			$mail->Username = $C007['C007_Email_SMTP_Usuario'];
			$mail->Password = $C007['C007_Email_SMTP_Senha'];

			// ******** Trocado UTF8 devido ao erro no Titulo do Email ************
			if ((mb_detect_encoding($r_emailAssunto)=='UTF-8') && (!mb_detect_encoding($r_emailAssunto,'UTF-8',true))) {   
				$r_emailAssunto = utf8_encode($r_emailAssunto); 
		   	}else{
			 	$r_emailAssunto = '=?UTF-8?B?'.base64_encode($r_emailAssunto).'?=';
			}
 
			// ******Só irá realizar o encode UTF8 caso o assunto do email seja diferente de UTF8 *******
			//if (mb_detect_encoding($r_emailAssunto)!='UTF-8'){
			//	$r_emailAssunto=utf8_encode($r_emailAssunto);
			//}
			
			$mail->Subject	= $r_emailAssunto;

			$mail->Body     = $r_emailMensagem;

			if ($r_suporte) {
				$hoje = date('d/m/Y H:i:s');
				$screenshot = '(não tem)';
				if ($r_screenshot) {
					$anexoDir = $g['pathDados'] . 'tmp/';
	        		is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
	        		$file = $anexoDir . uniqid() . '.html';
	        		file_put_contents($file, "<img src=\"{$r_screenshot}\" />");
	        		$screenshot = '<a href="' . $confUsuario['urlRaiz'] . 'hardness3/' . $file . '" target=_blank>Ver Imagem</a>';
	        	}
	        	if($r_tipoSuporte == 'erro'){
					$extra = 'O erro foi encaminhado para a equipe de suporte da Hardness Sistemas e será verificado o mais breve possível<br/>';
				} else if($r_tipoSuporte == 'duvida'){
					$extra = 'Sua dúvida foi encaminhada para a equipe de suporte da Hardness Sistemas e será respondida em breve<br/>';
				} else if($r_tipoSuporte == 'sugestao'){
					$extra = 'Sua sugestão de melhoria foi recebida e poderá ser incorporada em futuras versões.<br/>';
				} else {
					$extra = 'Foi efetuado uma requisição de suporte no sistema Hardness.<br/>';
				}
				$extra .= <<<EOT
					Segue abaixo uma cópia da requisição:<br/>
					<br/>
					<hr>
					<br/>
					<span style="opacity: 0.9;">Requisição aberta em</span>: {$hoje}<br/>
					<span style="opacity: 0.9;">Na empresa</span>: {$g['C004']['C004_Nome_Empresa']}<br/>
					<span style="opacity: 0.9;">Pelo usuário</span>: {$g['C007']['C007_Primeiro_Nome']} (código: {$g['usuarioAtual']})<br/>
					<span style="opacity: 0.9;">Banco de dados</span>: {$confUsuario['dbDatabase']}<br/>
					<span style="opacity: 0.9;">Endereço de acesso</span>: {$confUsuario['urlRaiz']}<br/>
					<span style="opacity: 0.9;">Foto automática da tela</span>: {$screenshot}<br/>
					<span style="opacity: 0.9;">Mensagem escrita pelo usuário</span>:<br/>
					<br/>
EOT;
				$mail->Body = $extra . $mail->Body;
			}

			$mail->AltBody  = "ALT BODY";
			//$mail->CharSet  = 'iso-8859-1';
			$mail->SetLanguage("br");
			if (!isset($r_emailNotificacaoSuporte)) {
				$mail->ConfirmReadingTo = $C007['C007_Email'];
			}
			$mail->IsHTML(true);
			$mail->Timeout = 60;
			if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
			{
				$mail->SMTPSecure   = "ssl";
				$mail->Port         = "465";
			}
			if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
			{
				$mail->SMTPSecurity = "ssl";
				$mail->SMTPSecure   = "ssl";
				$mail->Port         = "465";
			}
			if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
			{
				$mail->SMTPSecure   = "ssl";
			}
			if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
			{
				$mail->SMTPSecure   = "tls";
				$mail->SMTPSecurity = 'tls';
			}
			if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
            {
                $mail->SMTPSecure   = "tls";
            }
			if (!empty($C007['C007_Email_SMTP_Porta']))
			{
				$mail->Port         = $C007['C007_Email_SMTP_Porta']; 
			}
			if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
			{
				$mail->SMTPSecure   = "tls";
			}
            if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                    $mail->SMTPSecure = "tls";
                    $mail->SMTPSecurity = 'tls';
                    //$mail->Port = '465';
            }
            if ($C007['C007_Email']=='nfe@hardness.com.br') {
                    $mail->SMTPSecure = "ssl";
            }
			if (strpos($C007['C007_Email_SMTP_Servidor'],"nacomaq") !== false)
			{
				$mail->SMTPSecure   = "ssl";
				$mail->Port         = "465";
			}
			if (strpos($C007['C007_Email_SMTP_Servidor'],"smtp.promotec.ind.br") !== false)
			{
				$mail->Port         = "587";
			}
			switch ($C007['C007_Email_SMTP_Seguranca']) {
				case 1:
					$mail->SMTPSecure   = "ssl";
					$mail->SMTPSecurity = 'ssl';
					break;
				case 2:
					$mail->SMTPSecure   = "tls";
					$mail->SMTPSecurity = 'tls';
					break;
			}

			$todosEmailsEnviados = array();
			$mPara=preg_split("/[;,]+/",$r_emailPara);
			for($x=0;$x<count($mPara);$x++)
			{
				$email = $mPara[$x];
				if (strpos($email, '<') !== false) {
					$email = explode('<', $email);
					$email = explode('>', $email[1]);
					$email = $email[0];
				}
				$todosEmailsEnviados[] = strtolower(trim($email));
				$mail->AddAddress($email);
			}
			$mPara=preg_split("/[;,]+/",$r_emailParaCc);
			for($x=0;$x<count($mPara);$x++)
			{
				$email = $mPara[$x];
				if (strpos($email, '<') !== false) {
					$email = explode('<', $email);
					$email = explode('>', $email[1]);
					$email = $email[0];
				}
				$todosEmailsEnviados[] = strtolower(trim($email));
				$mail->AddCC($email);
			}
			$mPara=preg_split("/[;,]+/",$r_emailParaCco);
			for($x=0;$x<count($mPara);$x++)
			{
				$email = $mPara[$x];
				if (strpos($email, '<') !== false) {
					$email = explode('<', $email);
					$email = explode('>', $email[1]);
					$email = $email[0];
				}
				$todosEmailsEnviados[] = strtolower(trim($email));
				$mail->AddBCC($email);
			}

			$anexosGravar = array();
			$T139_Estrutura = array('anexosManual' => array());
			if (isset($monta_['Anexos'])) {
				foreach ($monta_['Anexos'] as $path) {
					$anexosGravar[] = $path;
					$T139_Estrutura['anexosManual'][] = basename($path);
					$mail->AddAttachment($path,basename($path));
				}
			}
			// carrega arquivos anexados (versão de envio via usuário)
			$dir = "/tmp/{$r_divId}/";
			$conteudoAnexos = '';
			if (is_dir($dir)) {
				$dh  = opendir($dir);
				while (false !== ($filename = readdir($dh))) {
					if ($filename == '.' || $filename == '..') { continue; }
					$anexosGravar[] = $dir.'/'.$filename;
					$T139_Estrutura['anexosManual'][] = basename($dir.'/'.$filename);
					$mail->AddAttachment($dir.'/'.$filename,basename($dir.'/'.$filename));
				}
			}
			//$mail->AddAttachment($cArquivoXML,basename($cArquivoXML));

			if($mail->Send())
			{
				if(!empty($T007_Flag_ACP)){
					mysql_query("update T007 set T007_Data_Envio_Email=now() where T007_Id='{$r_T007_Id}'");
				}
				if(!empty($r_idNF)){				
                    mysql_query("update T007 set T007_Data_Envio_Email=now(), T007_Data_Envio_Email_Boleto=now() where T007_Id='{$r_idNF}'");
                }
				if(!empty($r_T117_Id)){		
					mysql_query("update T117 set T117_Data_Envio_Email=now() where T117_Id='{$r_T117_Id}'");
				}
				if(!empty($r_T007_Id) && empty($T007_Flag_ACP)){		
                    mysql_query("update T007 set T007_Data_Envio_XML=now() where T007_Id='{$r_T007_Id}'");
                }
                if(!empty($r_T199_Id)){		
                    mysql_query("update T199 set T199_Data_Envio_Email_Cce=now() where T199_Id='{$r_T199_Id}'");
                }
                if(!empty($r_T173_Id)){
					$T173 = mysql_query("SELECT T173_T010_Id FROM T173 WHERE T173_Id='{$r_T173_Id}'");
					$mT173 = mysql_fetch_array($T173);
					$r_T010_Id = $mT173['T173_T010_Id'];
                }
                $T173_Id=(!empty($r_T173_Id)) ? $r_T173_Id : $r_T173_Id_EmailGravar;
                
                if((!empty($T173_Id)) && (!empty($r_T010_Id))){
					$T174 = mysql_query("UPDATE T174 SET T174_Flag_Email_Enviado='S' WHERE T174_T173_Id='{$T173_Id}' AND T174_T010_Id='{$r_T010_Id}'");
                }

			   if(!empty($r_T003_Id)){
				   	$agora = date("Y-m-d H:i:s"); 
					$T003A = mysql_query("UPDATE T003A LEFT JOIN T003 ON T003A_T003_Id = T003_Id SET T003A_Data_Envio_Primeiro_Email = '{$agora}' WHERE T003A_T003_Id ='{$r_T003_Id}' AND T003A_Data_Envio_Primeiro_Email = '0000-00-00 00:00:00'");
			   }

			   if(!empty($r_T002_Id)){
					$agora = date("Y-m-d H:i:s"); 
					$T003A = mysql_query("UPDATE T002A SET T002A_Data_Envio_Email = '{$agora}' WHERE T002A_T002_Id = '{$r_T002_Id}'");
				}
				
				// Gravar como e-mail enviado
				$T139_De = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
				$T139_Estrutura = serialize($T139_Estrutura);
				$sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_D013_Id, T139_T003_Id, T139_D117_Id, T139_C007_Id, T139_Valor_Total, T139_Numero_Registros, T139_T117_Id,T139_T005_Id,T139_T011_Id, T139_Cc, T139_Cco,T139_T075_Id,T139_T007_Id,T139_T199_Id,T139_T173_Id, T139_T010_Id, T139_T224_Id, T139_T218_Id) VALUES ('S', 0, '".mysql_real_escape_string($r_emailAssuntoGravarBanco)."', '{$T139_De}', '".mysql_real_escape_string($r_emailPara)."', '".date('Y-m-d H:i:s')."', '" . mysql_real_escape_string($T139_Estrutura) . "', '".mysql_real_escape_string($r_emailMensagem)."', '1', '".mysql_real_escape_string($r_D024_Id)."', '".mysql_real_escape_string($r_D013_Id)."', '".mysql_real_escape_string($r_T003_Id)."', '".mysql_real_escape_string($r_D117_Id)."', '".mysql_real_escape_string($g['usuarioAtual'])."', '".mysql_real_escape_string($r_T139_Valor_Total)."', '".mysql_real_escape_string($r_T139_Numero_Registros)."', '".mysql_real_escape_string($r_T117_Id)."', '".mysql_real_escape_string($r_T005_Id)."', '".mysql_real_escape_string($r_T011_Id)."', '".mysql_real_escape_string($r_emailParaCc)."', '".mysql_real_escape_string($r_emailParaCco)."', '".mysql_real_escape_string($r_T075_Id)."', '".mysql_real_escape_string($r_T007_Id)."', '".mysql_real_escape_string($r_T199_Id)."', '".mysql_real_escape_string($T173_Id)."', '".mysql_real_escape_string($r_T010_Id)."', '".mysql_real_escape_string($r_T224_Id)."', '".mysql_real_escape_string($r_T218_Id)."');";
				mysql_query($sql);
				$T139LastId = $g['mysqlLastId'];

				//Grava na agenda o envio do e-mail
				if(!empty($r_T003_Id)){
					$D085_Mensagem = "Email enviado: orçamento {$r_T003_Id}";
					$sqlT259 = mysql_query("SELECT T259_Id, T259_C007_Id FROM D085 LEFT JOIN T259 ON T259_Id=D085_T259_Id WHERE D085_Entidade = 'T003' AND D085_Id_Entidade = '{$r_T003_Id}'");
				} else if(!empty($r_T005_Id)){
					$D085_Mensagem = "Email enviado: pedido {$r_T005_Id}";
					$sqlT259 = mysql_query("SELECT T259_Id, T259_C007_Id FROM D085 LEFT JOIN T259 ON T259_Id=D085_T259_Id WHERE D085_Entidade = 'T005' AND D085_Id_Entidade = '{$r_T005_Id}'");
				} else if(!empty($r_T007_Id)){
					$D085_Mensagem = "Email enviado: nota fiscal {$r_T007_Id}";
					$sqlT259 = mysql_query("SELECT T259_Id, T259_C007_Id FROM D085 LEFT JOIN T259 ON T259_Id=D085_T259_Id WHERE D085_Entidade = 'T007' AND D085_Id_Entidade = '{$r_T007_Id}'");
				} else {
					$D085_Mensagem = "Email enviado: " . mysql_real_escape_string($r_emailAssuntoBkp);
				}
				//mysql_query("INSERT INTO D085 (D085_D024_Id, D085_C007_Id, D085_T003_Id, D085_T005_Id, D085_T007_Id, D085_T139_Id, D085_D013_Id, D085_Data, D085_Hora, D085_Mensagem) VALUES ('{$r_D024_Id}', '{$g['usuarioAtual']}', '{$r_T003_Id}', '{$r_T005_Id}', '{$r_T007_Id}', '{$T139LastId}', current_date(), current_time(), '{$D085_Mensagem}')");

				if(!empty($r_T003_Id) || !empty($r_T005_Id) || !empty($r_T007_Id)) {
					$rowT259 = mysql_fetch_assoc($sqlT259);
					//Atualiza o assunto da tarefa relacionada ao email do orçamento/pedido/nota
					require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
					$arrayAssuntoTarefa = array(
						'campoAtual' => '',
						'todosCampos' => array(
							'T259_Id' => $rowT259['T259_Id'],
							'T259_C007_Id' => $rowT259['T259_C007_Id']
						)
					);
					assuntoTarefa($arrayAssuntoTarefa, false);
				}

				// Grava os email enviados automaticamente em 'Contatos' na empresa associada (os emails que não existem cadastrados)
				if ($g['C031']['EmailEnviadosAddContatosEmpresa'] == 'S') {
					$D024_Auto = '';
					if (!empty($r_D024_Id)) {
						$D024_Auto = $r_D024_Id;
					}
					if (!empty($r_D013_Id)) {
						$D024_Auto = mysql_query("SELECT D013_D024_Id FROM D013 WHERE D013_Id = '{$r_D013_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['D013_D024_Id'];
					}
					if (!empty($r_T011_Id)) {
						$D024_Auto = mysql_query("SELECT T011_D024_Id FROM T011 WHERE T011_Id = '{$r_T011_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T011_D024_Id'];
					}
					if (!empty($r_T075_Id)) {
						$D024_Auto = mysql_query("SELECT T075_D024_Id FROM T075 WHERE T075_Id = '{$r_T075_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T075_D024_Id'];
					}
					if (!empty($r_T117_Id)) {
						$D024_Auto = mysql_query("SELECT T117_D024_Id FROM T117 WHERE T117_Id = '{$r_T117_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T117_D024_Id'];
					}
					if (!empty($r_T166_Id)) {
						$D024_Auto = mysql_query("SELECT T166_D024_Id FROM T166 WHERE T166_Id = '{$r_T166_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T166_D024_Id'];
					}
					if (!empty($r_T196_Id)) {
						$D024_Auto = mysql_query("SELECT T196_D024_Id FROM T196 WHERE T196_Id = '{$r_T196_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T196_D024_Id'];
					}
					if (!empty($r_T173_Id)) {
						$D024_Auto = mysql_query("SELECT T173_D024_Id FROM T173 WHERE T173_Id = '{$r_T173_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T173_D024_Id'];
					}
					if (!empty($r_T010_Id)) {
						$D024_Auto = mysql_query("SELECT T010_D024_Id FROM T010 WHERE T010_Id = '{$r_T010_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T010_D024_Id'];
					}
					if (!empty($r_T003_Id)) {
						$D024_Auto = mysql_query("SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_T003_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T003_D024_Id'];
					}
					if (!empty($r_T218_Id)) {
						$D024_Auto = mysql_query("SELECT T218_D024_Id FROM T218 WHERE T218_Id = '{$r_T218_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T218_D024_Id'];
					}
					if (!empty($r_T224_Id)) {
						$D024_Auto = mysql_query("SELECT T224_D024_Id FROM T224 WHERE T224_Id = '{$r_T224_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T224_D024_Id'];
					}
					if (!empty($r_T005_Id)) {
						$D024_Auto = mysql_query("SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$r_T005_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T005_D024_Id'];
					}
					if (!empty($r_T007_Id)) {
						$D024_Auto = mysql_query("SELECT T007_D024_Id FROM T007 WHERE T007_Id = '{$r_T007_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T007_D024_Id'];
					}
					if (!empty($r_T002_Id)) {
						$D024_Auto = mysql_query("SELECT T002_D024_Id FROM T002 WHERE T002_Id = '{$r_T002_Id}'");
						$D024_Auto = mysql_fetch_array($D024_Auto);
						$D024_Auto = $D024_Auto['T002_D024_Id'];
					}
					if (!empty($D024_Auto)) {
						$emails = array();
						$sql = "SELECT D024_E_Mail FROM D024 WHERE D024_Id = '{$D024_Auto}';";
						$res = mysql_query($sql);
						while($row = mysql_fetch_assoc($res)) {
							$emails[] = strtolower(trim($row['D024_E_Mail']));
						}
						$sql = "SELECT D013_E_Mail FROM D013 WHERE D013_D024_Id = '{$D024_Auto}';";
						$res = mysql_query($sql);
						while($row = mysql_fetch_assoc($res)) {
							$emails[] = strtolower(trim($row['D013_E_Mail']));
						}
						foreach ($todosEmailsEnviados as $key => $value) {
							if (in_array($value, $emails)) { continue; }
							if (!empty($value)) {
								$value = mysql_real_escape_string($value);
								$sql = "INSERT INTO D013 (D013_D024_Id, D013_Nome_Contato, D013_E_Mail) VALUES ('{$D024_Auto}', '{$value}', '{$value}');";
								mysql_query($sql);
							}
						}
					} else {
						$emails = array();
						$sql = "SELECT D024_E_Mail FROM D024;";
						$res = mysql_query($sql);
						while($row = mysql_fetch_assoc($res)) {
							$emails[] = strtolower(trim($row['D024_E_Mail']));
						}
						$sql = "SELECT D013_E_Mail FROM D013;";
						$res = mysql_query($sql);
						while($row = mysql_fetch_assoc($res)) {
							$emails[] = strtolower(trim($row['D013_E_Mail']));
						}
						foreach ($todosEmailsEnviados as $key => $value) {
							if (in_array($value, $emails)) { continue; }
							if (!empty($value)) {
								$value = mysql_real_escape_string($value);
								$sql = "INSERT INTO D013 (D013_Nome_Contato, D013_E_Mail) VALUES ('{$value}', '{$value}');";
								mysql_query($sql);
							}
						}
					}
				}

				// Salvar anexos
				$dir = $g['pathDados'] . 'anexosEmail/';
				is_dir($dir) or mkdir($dir, 0777, true);
				foreach ($anexosGravar as $key => $anexo) {
					$arquivo = "{$dir}{$T139LastId}-{$key}";
					copy($anexo, $arquivo);
				}

				if (isset($monta_['Anexos'])) {
					foreach ($monta_['Anexos'] as $path) {
						gDeletarArquivo($path);
					}
				}

				// Associar e-mail
				if (!isset($r_suporte)) {
					$sql_ = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
					$resultado_ = mysql_query($sql_);
					$C007_ = mysql_fetch_assoc($resultado_);
					require_once('sistema/comunicacaoEmail.php');
					$comunicacaoEmail_ = new comunicacaoEmail($C007_['C007_Email_POP_Servidor'], $C007_['C007_Email_SMTP_Usuario'], $C007_['C007_Email_SMTP_Senha'], false, false, $C007_['C007_Email_POP_Porta']);
					$comunicacaoEmail_->associarEmails($g['mysqlLastId']);
				}

				// Mensagem
				$alert = gGeraAlertMsg('E-mail enviado com sucesso!', "De: {$C007['C007_Email']}<br />Para: {$r_emailPara}<br />SMTP: {$C007['C007_Email_SMTP_Servidor']}", false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
								{$alert}
							}, 150);
  						});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						var divIdParent = '{$g['divIdParent']}';
						if (!isempty(divIdParent)) {
							divRefresh('{$g['divIdParent']}', true);
						}
					} </script></div>
EOT;
				/* Callbacks */
				// Via Controle Interno
				if (!empty($r_ETC013OS)) {
					$EST013->montarEmailCallback($r_ETC013OS, array('Para' => $r_emailPara));
				}
				if (!empty($r_T173_Id)) {
					$EST008->montarEmailCallback($r_T173_Id);
				}
				
				if ($r_emailResposta && !empty($r_orcId) && !empty($r_acaoId)) {
					echo <<<JS
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							// $('#' + divId).showLoading();
							$.getJSON('/etc/etc006/grid_func-ajax/vincularEmailOrcamentoUpdate/?ajax=true&T139_Id=' + encodeURIComponent('{$r_acaoId}') + '&T003_Id=' + encodeURIComponent('{$r_orcId}') + '&callback=?', function(request) {
								// $('#' + divId).hideLoading();
								if (!request.code) {
									dialogConfirm('Erro', request.data);
								}
								
								if ('{$r_divRefreshTop}' && '{$r_divRefreshTop}'.length) {
									window.divRefresh('{$r_divRefreshTop}', true);
								}
								window.divRefresh(divId, true);
							});
						} </script></div>
JS;		
				}
			}
			else
			{
				$cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
				$cErro=str_replace(array("<p>","</p>"),"<br /><br />",$cErro);
				echo gGeraAlertMsg('Não foi possível enviar o e-mail!', "De: {$C007['C007_Email']}<br />Para: {$r_emailPara}<br />SMTP: {$C007['C007_Email_SMTP_Servidor']}<br />SMTP Secure: {$mail->SMTPSecure}<br />PORT: {$mail->Port}<br />ERRO:<br />{$cErro}");
			}
		}

		if (isset($r_suporte)) {
			echo '<form id="etc006EmailEnviarForm">';
			echo '<table>';
				//echo "<div style='padding: 7px;'><strong style='font-size: 1.1em;'>Requisição de Suporte</strong></div>";
				echo "<input name=\"emailDe\" type=\"hidden\" value=\"{$De}\" />";
				echo "<input name=\"emailPara\" type=\"hidden\" value=\"suporte@hardness.com.br\" />";
				//if ($g['C007']['C007_Email'] != $C007['C007_Email']) {
				echo "<input name=\"emailParaCc\" type=\"hidden\" value=\"{$g['C007']['C007_Email']}\" />";
				//}
				echo "<input name=\"suporte\" type=\"hidden\" value=\"true\" />";
				echo "<input name=\"tipoSuporte\" type=\"hidden\" value=\"{$r_tipoSuporte}\" />";
				$cod = substr(md5(uniqid()), 0, 10);
				if($r_tipoSuporte == 'erro'){
					$msgSuporte = 'Envio de erro';
				} else if($r_tipoSuporte == 'duvida'){
					$msgSuporte = 'Dúvida e informação';
				} else if($r_tipoSuporte == 'sugestao'){
					$msgSuporte = 'Sugestão de melhoria';
				} else {
					$msgSuporte = 'Requisição de Suporte';
				}
				echo "<input name=\"emailAssunto\" type=\"hidden\" value=\"{$msgSuporte} - Hardness Sistemas ({$cod})\" />";
				echo "<input id=\"screenshot\" name=\"screenshot\" type=\"hidden\" value=\"\" />";
				//echo "<tr><td>Anexos:</td><td>";
				//	echo "<span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><span style=\"float: left; margin-top: 1px;\"><a href=\"#\" onclick=\"var img = $('<img />').attr('src', $('#screenshot').val()); var tmp = $('<div></div>').css({'width': '850px', 'height': '500px'}).append(img); var jQueryHTML = $('<div></div>').append(tmp); dialogConfirm('Foto da Tela', false, false, false, false, jQueryHTML); return false;\">Foto automática da tela de trabalho</a></span>";
				//echo "</td></tr>";
		} else {
			echo '<form id="etc006EmailEnviarForm" class="formFormulario">';
			echo '<table>';
				echo "<tr><td>De:</td><td style=\"font-weight: bold;\"><input id=\"emailDe\" name=\"emailDe\" type=\"text\" value=\"{$De}\" disabled=\"disabled\" size=\"65\" class='itemForm itemForm-De formInput ui-state-default ui-corner-all'></td></tr>";
				echo "<tr><td>Para:</td><td style=\"font-weight: bold;\"><input name=\"emailPara\" id=\"emailPara\" type=\"text\" size=\"65\" value=\"$r_emailPara_\"  class='itemForm formInput ui-state-default ui-corner-all'></td></tr>";
				if (!empty($r_emailParaCc)) {
					echo "<tr><td>Cc:</td><td style=\"font-weight: bold;\"><input name=\"emailParaCc\" id=\"emailParaCc\" value=\"$r_emailParaCc_\" type=\"text\" size=\"65\" class='itemForm formInput ui-state-default ui-corner-all'></td></tr>";
				} else {
					echo "<tr style=\"display: none;\"><td>Cc:</td><td style=\"font-weight: bold;\"><input name=\"emailParaCc\" id=\"emailParaCc\" value=\"$r_emailParaCc_\" type=\"text\" size=\"65\" class='itemForm formInput ui-state-default ui-corner-all'></td></tr>";
				}
				echo "<tr style=\"display: none;\"><td>Cco:</td><td style=\"font-weight: bold;\"><input name=\"emailParaCco\" id=\"emailParaCco\" type=\"text\" size=\"65\" class='itemForm formInput ui-state-default ui-corner-all'></td></tr>";
				echo "<tr><td></td><td><span><a href=\"#\" id=\"addCc\">Adicionar Cc</a>&nbsp;&nbsp;&nbsp;</span><span><a href=\"#\" id=\"addCco\">Adicionar Cco</a></span></td></tr>";
				echo "<tr><td>Assunto:</td><td style=\"font-weight: bold;\"><input id=\"emailAssunto\" name=\"emailAssunto\" type=\"text\" size=\"90\" value=\"$r_emailAssunto_\" class='itemForm  itemForm-Assunto formInput ui-state-default ui-corner-all'></td></tr>";
		}
			//if (isset($monta_['Anexos']) && !empty($monta_['Anexos'])) {
//				echo "<tr><td>Anexos:</td><td>";
//			} else {
			echo <<<EOT
<tr><td>Anexos:</td><td>
<div id="file-uploader">       
    <noscript>          
        <p>Please enable JavaScript to use file uploader.</p>
        <!-- or put a simple form for upload here -->
    </noscript>         
</div>
</td></tr>
EOT;
				// carrega arquivos já enviados
				$dir = "/tmp/{$r_divId}/";
				$conteudoAnexos = '';
				if (is_dir($dir)) {
					$dh  = opendir($dir);
					while (false !== ($filename = readdir($dh))) {
					    if ($filename == '.' || $filename == '..') { continue; }
					    //$tamanho = filesize($dir . '/' . $filename);
					    $tamanho = '';
						$conteudoAnexos .= <<<EOT
						<li class=" qq-upload-success">
							<span class="qq-upload-file">{$filename}</span>
							<span class="qq-upload-size" style="display: inline;">{$tamanho}</span>
							<span class="qq-upload-failed-text">Failed</span>
						</li>
EOT;
					}
					$conteudoAnexos = str_replace("\n", '', $conteudoAnexos);
					$conteudoAnexos = str_replace("\r", '', $conteudoAnexos);
					$conteudoAnexos = str_replace("'", "\\'", $conteudoAnexos);
				}
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						// File upload
						var uploader = new qq.FileUploader({
						    element: document.getElementById('file-uploader'),
						    action: '/etc/etc006/outro/',
						    params: {'ajax': 'true', 'divId': '{$g['divId']}'},
						    onSubmit: function(id, fileName){ fixEditorSize(); },
						    onCancel: function(id, fileName){ fixEditorSize(); },
						});
						setTimeout(function() {
							$('.qq-upload-list').html('{$conteudoAnexos}');
							fixEditorSize();
						}, 200);
					} </script></div>
EOT;
			//}
			// Anexos gerados pelo sistema
			echo "<tr><td>&nbsp;</td><td>";
			foreach ($monta_['Anexos'] as $key => $path) {
				$arq = basename($path);
				if (isset($monta_['AnexosWeb'])) {
					echo "<span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><span style=\"float: left; margin-top: 1px;font-size:12px\"><a href=\"#\" onclick=\"window.open('{$monta_['AnexosWeb'][$key]}');\">{$arq}</a></span>";
				} else {
					echo "<span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><span style=\"float: left; margin-top: 1px;font-size:12px\">{$arq}</span>";
				}
			}
			echo "</td></tr>";
			// Anexos
			/*
			if (sizeof($anexos) > 0) {
				echo "<tr><td>Anexos:</td><td>";
   				for ($j=0;$j<sizeof($anexos);$j++) {
   					echo "<div style=\"display: inline; float: left; margin-right: 10px;\"><span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><a href=\"#\" onclick=\"downloadAnexo('{$j}')\">{$anexos[$j]}</a></div>";
   				}
   				echo '</td></tr>';
			}
			*/
		echo '</table>';
		echo "<input name=\"idNF\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_idNF}\">";
		echo "<input name=\"emailEnviado\" type=\"text\" size=\"90\" style=\"display: none;\">";
		echo "<input name=\"T180_D024_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T180_D024_Id}\">";
		echo "<input name=\"D024_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_D024_Id}\">";
		echo "<input name=\"D013_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_D013_Id}\">";
		echo "<input name=\"T003_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T003_Id}\">";
		echo "<input name=\"T218_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T218_Id}\">";
		echo "<input name=\"T005_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T005_Id}\">";
		echo "<input name=\"T224_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T224_Id}\">";
		echo "<input name=\"T011_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T011_Id}\">";
		echo "<input name=\"T075_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T075_Id}\">";
		echo "<input name=\"D117_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_D117_Id}\">";
		echo "<input name=\"T117_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T117_Id}\">";
		echo "<input name=\"T007_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T007_Id}\">";
        echo "<input name=\"T199_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T199_Id}\">";
		echo "<input name=\"T166_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T166_Id}\">";
		echo "<input name=\"T196_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T196_Id}\">";
		echo "<input name=\"T173_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T173_Id}\">";
		echo "<input name=\"ETC013OS\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_ETC013OS}\">";
		echo "<input name=\"T010_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T010_Id}\">";
		echo "<input name=\"T002_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T002_Id}\">";
		echo "<input name=\"emailTituloReceber\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_emailTituloReceber}\">";
		echo "<input name=\"T173_D024_Id\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T173_D024_Id}\">";
		echo "<input name=\"T139_Valor_Total\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T139_Valor_Total}\">";
		echo "<input name=\"T139_Numero_Registros\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T139_Numero_Registros}\">";
		echo "<input name=\"T173_Id_EmailGravar\" type=\"text\" size=\"90\" style=\"display: none;\" value=\"{$r_T173_Id_EmailGravar}\">";
		echo '</form>';
		//echo '<div style="width: 100%; height: 2px; background-color: #A6C9E2;"></div>';
			echo '<div style="width: 100%; height: 2px; background-color: #A6C9E2;"></div>';
			echo '<div id="emailEditorDiv" style="background-color: white;">';
				//echo '<textarea id="editorEmail" class="ckeditor" name="editorEmail" style="width: 100%; height: 100%;"></textarea>';
				echo "<textarea id=\"{$editor}\">{$r_emailMensagem_}</textarea>";
			echo '</div>';
			$minHeight = '0';
			if ($r_suporte) {
				$minHeight = '300';
				echo '<div style="background-color: transparent; height: 15px; padding: 5px;">';
					echo 'Foto automática da tela do sistema:';
				echo '</div>';
				echo '<div id="screenshotDiv" style="background-color: transparent; height: 275px; overflow: scroll;">';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;Carregando...';
				echo '</div>';
			}
		//echo '</div>';

		if ($r_suporte) {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#screenshot').val(canvasImg);
				var img = $('<img />').attr('src', canvasImg);
				$('#screenshotDiv').html('').append(img);
			} </script></div>
EOT;
		}
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// Corrigir Editor
				$('#{$g['divId']}').closest('.ui-dialog-content').data('callFixEditor', 'true');
				setTimeout(function() {
					$('#emailEditorDiv').find('> div:eq(1)').css('border', 'none');
					//$('#emailEditorDiv').find('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
					$('#emailEditorDiv').find('.nicEdit-panelContain').css({'border': 'none', 'background': 'none'});
					//$('#emailEditorDiv').find('.nicEdit-button-undefined').css({'border': '1px solid #A6C9E2', 'background': 'none'});
				}, 100);
				function fixEditorSize() {
					setTimeout(function() {
						var divId = $('#{$g['divId']}');
						var editor = $('#emailEditorDiv');
						var width = divId.width() - 2;
						editor.find('> div').width(width);
						divId.css('overflow', 'hidden')
						editor.find('.nicEdit-main').width(width-8).css('overflow', 'auto');
						var height = divId.height() - divId.find('form').height() - editor.find('> div:eq(0)').height() - 3;
						editor.find('> div:eq(1)').height(height-{$minHeight});
						editor.find('.nicEdit-main').height(height-8-{$minHeight});
					}, 100);
				}
				$('#addCc').click(function() {
					$(this).parent().remove();
					$('#emailParaCc').closest('tr').show();
					fixEditorSize();
					return false;
				});
				$('#addCco').click(function() {
					$(this).parent().remove();
					$('#emailParaCco').closest('tr').show();
					fixEditorSize();
					return false;
				});
				$(document).ready(function() {
					/*if (typeof(CKEDITOR.instances.{$editor}) != 'undefined') {
						CKEDITOR.instances.editor1.destroy();
					}
					var editor = CKEDITOR.replace('{$editor}', {
						toolbar : 'Basic',
						uiColor : '#9AB8F3',
						height : '238px',
						toolbar_Basic: [ ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList'] ]
					});*/
					new nicEditor({
						iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
						buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
					}).panelInstance('{$editor}');
					fixEditorSize();
				    setTimeout(function() {
  						//$('#{$g['divId']} #cke_{$editor}').css({'border': '0', 'padding': '0'});
  						$('#{$g['divId']} #emailPara').focus();
					}, 100);
				});
				var auto = {
					source: function( request, response ) {
						$.ajax({
							url: "/sistema/funcoes/pesquisa/pesquisaEmail/",
							dataType: "jsonp",
							data: 'ajax=true&pesquisa=' + encodeURIComponent(request.term),
							success: function( data ) {
								if (isempty(data)) {
									data = [{
									}];
								}
								response( $.map( data, function( item ) {
									return {
										label: item.title,
										value: item.value
									}
								}));
							}
						});
					},
					delay: 200,
					select: function(event, ui) {
						if (ui.item.value != 0) {
							var value = $(this).val();
							var match = value.match(/.*,.*/);
							if (!match) {
								$(this).val(ui.item.label);
							} else {
								value = value.replace(/,[^,]+$/, '');
								$(this).val(value + ', ' + ui.item.label);
							}
						}
						return false;
					},
					change: function(event, ui) {
						return false;
					},
					focus: function(event, ui) {
						if (ui.item.value != 0) {
							var value = $(this).val();
							var match = value.match(/.*,.*/);
							if (!match) {
								$(this).val(ui.item.label);
							} else {
								value = value.replace(/,[^,]+$/, '');
								$(this).val(value + ', ' + ui.item.label);
							}
						}
						return false;
					}
				};
				$('#{$g['divId']} #emailPara').autocomplete(auto);
				$('#{$g['divId']} #emailParaCc').autocomplete(auto);
				$('#{$g['divId']} #emailParaCco').autocomplete(auto);

				// Enviar a versão HTML do GRID
				// Como funciona: pegamos os dados do divIdParent (que é o grid) para montar a URL de requisição e injetar ela dentro do e-mail
				var generateHTML = '{$r_sendGriHTML}';
				if (!isempty(generateHTML)) {
					var divParent = $('#{$g['divIdParent']}');
					$('#{$g['divId']}').showLoading();
					$.ajax({
						url: divParent.data('href'),
						dataType: "html",
						data: 'ajax=true&generateEmail=true&generateHTML=true&' + generateHTML + divParent.data('vars'),
						success: function( data ) {
							$('#{$g['divId']}').hideLoading();
							if (!isempty(data)) {
								$('.nicEdit-main').prepend('Segue relatório.<br />' + data);
							}
						}
					});
				}
			} </script></div>
EOT;

		// Botões
		$botoesEsq = '';
		$botoesMid = '';
		$botoesDir = '';
		$botoesDir .= '<button id="etc006EmailEnviarBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		if (isset($r_suporte)) {
			$botoesEsq .= '<a href="http://www.hardness.com.br/downloads/suporte.exe" target="_blank"><button>Suporte: Ammyy</button></a>';
			$botoesEsq .= '<a href="http://download.teamviewer.com/download/TeamViewerQS_pt.exe" target="_blank"><button>Suporte: TeamViewer</button></a>';
		}
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EmailEnviarBtn').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					setTimeout(function(){
						$('.loading-indicator-overlay').css({'opacity': '1', 'background': 'background: rgba(255, 255, 255, 0.5)'}).append('<div style="top: 55%; position: relative; text-align: center; color: blue;">Enviando... Aguarde ou feche a janela, o e-mail continuará sendo enviado.</div>');
					}, 8000);
					var nicE = new nicEditors.findEditor('{$editor}'); // CKEDITOR.instances.{$editor}.getData()
					$.ajax({
						url: '/etc/etc006/form/etc006FormEmailEnviar/',
						dataType: 'html',
						type: 'POST',
						cache: false,
						data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&divId={$g['divId']}&' + $("#etc006EmailEnviarForm").serialize() + '&emailMensagem=' + encodeURIComponent(nicE.getContent()) +'&T007_Flag_ACP=' + encodeURIComponent('$T007_Flag_ACP'),
						success: function(data) {
							$('#{$g['divId']}').hideLoading();
							$('#{$g['divId']}').html(data);
							reloadUI('{$g['divId']}');
							$('#{$g['divId']}').find('> .buttonsBottom').each( function() {
								if (typeof($(this).attr('id')) != 'undefined') {
									moveBtnsToContent('{$g['divId']}');
								}
							});
							return false;
						}
					});
				});
			} </script></div>
EOT;
	break;
	
	/**
	* Form Email
	* Chamado em: etc006TabEmail
	*/
	case 'etc006FormAssoc':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T139';
		$form_campo_pk = 'T139_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('etc006FormAssocBtnAdd', 'etc006FormAssocBtnCln', 'etc006FormAssocBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc006/form/etc006FormAssoc/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T139_D024_Id' => array('name' => 'T139_D024_Id', 'label' => 'Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id',""), 'length' => array(50,30)),
					'T139_D013_Id' => array('name' => 'T139_D013_Id', 'label' => 'Contato', 'type' => 'pesquisa', 'pesquisa' => array('D013', 'D013_Id', 'D013_Nome_Contato'), 'length' => array(50,30)),
					'T139_T003_Id' => array('name' => 'T139_T003_Id', 'label' => 'Orçamento', 'type' => 'pesquisa', 'pesquisa' => array('T003', 'T003_Id', 'T003_Id,T003_Nome_Cliente,T003_Data_Emissao'), 'length' => array(50,30)),
					'T139_T218_Id' => array('name' => 'T139_T218_Id', 'label' => 'Cotação de Compra', 'type' => 'pesquisa', 'pesquisa' => array('T218', 'T218_Id', 'T218_Id,T218_Nome_Cliente,T218_Data_Emissao'), 'length' => array(50,30)),
					'T139_T005_Id' => array('name' => 'T139_T005_Id', 'label' => 'Pedido', 'type' => 'pesquisa', 'pesquisa' => array('T005', 'T005_Id', 'T005_Id,D024_Nome_Empresa,T005_Data_Emissao', 'LEFT JOIN D024 ON D024_Id=T005_D024_Id'), 'length' => array(50,30)),
					'T139_T224_Id' => array('name' => 'T139_T224_Id', 'label' => 'Ordem de Compra', 'type' => 'pesquisa', 'pesquisa' => array('T224', 'T224_Id', 'T224_Id,D024_Nome_Empresa,T224_Data_Emissao', 'LEFT JOIN D024 ON D024_Id=T224_D024_Id'), 'length' => array(50,30)),
					'T139_T075_Id' => array('name' => 'T139_T075_Id', 'label' => 'Declaração de Importação', 'type' => 'pesquisa', 'pesquisa' => array('T075', 'T075_Id', 'T075_Numero_DI'), 'length' => array(50,30)),
					'T139_T010_Id' => array('name' => 'T139_T010_Id', 'label' => 'Cotação', 'type' => 'pesquisa', 'pesquisa' => array('T010', 'T010_Id', 'T010_Id'), 'length' => array(50,30)),
				)
			)
		);
        $form = new Formulario('formabc001FormNome');
        // Botões dentro do Formulário
		$formArray['campos']['geral']['etc006FormAssocBtnCln'] = array('name' => 'etc006FormAssocBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['etc006FormAssocBtnAdd'] = array('name' => 'etc006FormAssocBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Atualiza o GRID e da um reload no FORM
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Atualiza o GRID e da um reload no FORM
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;
	
	case 'etc006FormCadastroContato':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_email = (isset($r_etc006FormCadastroCont)) ? $r_etc006FormCadastroCont['email'] : $r_email;
		$r_T139_Id = (isset($r_etc006FormCadastroCont)) ? $r_etc006FormCadastroCont['T139_Id'] : $r_T139_Id;
		
		$email_ = base64_decode($r_email);
		$email_ = explode('<', html_entity_decode($email_));
		if (isset($email_[1])) {
			$nome = $email_[0];
			$nome = str_replace('"', '', $nome);
			$email = $email_[1];
			$email = str_replace('>', '', $email);
		} else {
			$nome = '';
			$email = $email_[0];
		}
		$sql = "SELECT T139_D024_Id, D024_Nome_Fantasia from T139 LEFT JOIN D024 ON D024_Id=T139_D024_Id WHERE T139_Id = '".mysql_real_escape_string($r_T139_Id)."';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D013';
		$form_campo_pk = 'D013_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('etc006FormContatosBtnAdd', 'etc006FormContatosBtnCln', 'etc006FormContatosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc006/form/etc006FormCadastroContato/';
		$form_editor = array(
			'geral' => '{"D013_D024_Id":{"top":7,"left":24,"width":329,"hidden":0},"D013_Nome_Contato":{"top":30,"left":38,"width":344,"hidden":0},"D013_E_Mail":{"top":53,"left":44,"width":344,"hidden":0},"D013_Tipo_Contato":{"top":76,"left":53,"width":344,"hidden":0},"D013_Data_Nascimento":{"top":99,"left":16,"width":72,"hidden":0},"D013_DDD_Telefone_1":{"top":122,"left":32,"width":24,"hidden":0},"D013_Telefone_1":{"top":122,"left":112,"width":104,"hidden":0},"etc006FormContatosBtnCln":{"top":120,"left":368,"width":null,"hidden":0},"etc006FormContatosBtnAdd":{"top":120,"left":304,"width":null,"hidden":0}}'
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('etc006FormCadastroCont');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'email' => array('name' => 'email', 'label' => '', 'type' => 'hidden', 'value' => @$r_email, 'reject' => true),
					'T139_Id' => array('name' => 'T139_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T139_Id, 'reject' => true),
					'D013_D024_Id' => array('name' => 'D013_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id,D024_Nome_Empresa',""), 'length' => array(50,60), 'value' => $row['T139_D024_Id'], 'value_Pesquisa' => $row['D024_Nome_Fantasia']),
					'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Nome', 'type' => 'text','length' => array(50,60), 'value' => $nome),
					'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'E-Mail', 'type' => 'text','length' => array(50,90), 'value' => $email),
					'D013_Tipo_Contato' => array('name' => 'D013_Tipo_Contato', 'label'=>'Tipo', 'type' => 'text','length' => array(50,90)),
					'D013_Data_Nascimento' => array('name' => 'D013_Data_Nascimento', 'label'=>'Nascimento', 'type' => 'data','length' => array(20,30)),
					'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Telefone', 'type' => 'text','length' => array(2,2)),
					'D013_Telefone_1' => array('name' => 'D013_Telefone', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
				),
			) // 'campos'
		);

		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			//$formArray['campos']['geral']['etc006FormContatosBtnNew'] = array('name' => 'etc006FormContatosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['etc006FormContatosBtnCln'] = array('name' => 'etc006FormContatosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['etc006FormContatosBtnAdd'] = array('name' => 'etc006FormContatosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		if(!empty($row['T139_D024_Id'])){
			$D024['Cliente'] = mysql_real_escape_string($D024['Cliente']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#D013_D024_Id').val('{$row['T139_D024_Id']}');
					$('#D013_D024_Id_Pesquisa').val('{$row['D024_Nome_Fantasia']}');
				} </script></div>
EOT;
		}

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							$sql = "UPDATE T139 SET T139_D024_Id = '{$form->campoValorEnviado('D013_D024_Id')}', T139_D013_Id = '{$r_acaoId}' WHERE T139_Id = '{$form->campoValorEnviado('T139_Id')}';";
							mysql_query($sql);
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Contato Incluído', 'O e-mail foi adicionado ao contato e associado com a empresa.', false);
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$sql = "UPDATE T139 SET T139_D024_Id = '{$form->campoValorEnviado('D013_D024_Id')}', T139_D013_Id = '{$g['mysqlLastId']}' WHERE T139_Id = '{$form->campoValorEnviado('T139_Id')}';";
						mysql_query($sql);
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Contato Alterado', 'O e-mail foi adicionado ao contato e associado com a empresa!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
  							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;

	case 'etc006FormInserirAgendaTarefa':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;
		$sqlT259 = mysql_query("SELECT T259_D024_Id FROM T259 WHERE T259_Id = '{$T259_Id}'");
		$rowT259 = mysql_fetch_assoc($sqlT259);

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc006FormInserirAgendaTarefa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc006/form/etc006FormInserirAgendaTarefa/';

		// novo formulário
		$form = new Formulario('etc006FormInserirAgendaTarefa');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":39,"width":792,"hidden":0},"etc006FormInserirAgendaTarefa_btn":{"top":7,"left":822,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $rowT259['T259_D024_Id']),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(3,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'etc006FormInserirAgendaTarefa_btn' => array('name' => 'etc006FormInserirAgendaTarefa_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'etc006FormArquivarAnexo':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'TXXX';
		$form_campo_pk = 'TXXX_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc006FormArquivarAnexo_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc006/form/etc006FormArquivarAnexo/';

		// novo formulário
		$form = new Formulario('etc006FormArquivarAnexo');
		$form_editor = array('geral' => '{"etc006FormArquivarAnexo_btn":{"top":30,"left":310,"width":null,"hidden":0},"Id":{"top":7,"left":5,"width":297,"hidden":0}}');
		/*echo <<<EOT
			<div style="margin: 3px;">
				{$r_nomeArquivo}
			</div>
EOT;*/
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T139_Id' => array('name' => 'T139_Id', 'type' => 'hidden', 'value' => $r_T139_Id, 'reject' => true),
					'file' => array('name' => 'file', 'type' => 'hidden', 'value' => $r_file, 'reject' => true),
					'tipo' => array('name' => 'tipo', 'type' => 'hidden', 'value' => $r_tipo, 'reject' => true),
					'nomeArquivo' => array('name' => 'nomeArquivo', 'type' => 'hidden', 'value' => $r_nomeArquivo, 'reject' => true),
					'etc006FormArquivarAnexo_btn' => array('name' => 'etc006FormArquivarAnexo_btn', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
				)
			)
		);
		if($r_tipo == 'cliente'){
			$formArray['campos']['geral']['Id'] = array('name' => 'Id', 'label'=> "Cliente", 'reject' => true, 'type' => 'pesquisa',
				'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)
			);
		} else if($r_tipo == 'produto'){
			$formArray['campos']['geral']['Id'] = array('name' => 'Id', 'label'=> "Produto", 'reject' => true, 'type' => 'pesquisa',
				'pesquisa' => array('D001','D001_Id','D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca','left join D049 on D049_D001_Id=D001_Id left join D082 on D082_Id=D049_D082_Id order by D001_Codigo_Produto, D001_Descricao_Produto','%1 - %2 - %3'), 'length' => array(80,30)
			);
		}

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$sql = mysql_query("SELECT T139_C007_Id FROM T139 WHERE T139_Id = '{$form->campoValorEnviado('T139_Id')}'");
				$row = mysql_fetch_assoc($sql);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						setTimeout(function(){
							$('#{$g['divId']}').showLoading();
						}, 100);
						$.ajax({
							url: '/etc/etc006/form_func-ajax/arquivarAnexo/',
							dataType: 'jsonp',
							type: 'post',
							data: 'ajax=true&C007_Id={$row['T139_C007_Id']}&file={$form->campoValorEnviado('file')}&Id={$form->campoValorEnviado('Id')}&nomeArquivo={$form->campoValorEnviado('nomeArquivo')}&tipo={$form->campoValorEnviado('tipo')}&T139_Id={$form->campoValorEnviado('T139_Id')}',
							success: function(request) {
								if (request.code) {
									$('#{$g['divId']}').hideLoading();
									dialogConfirm("Ok!", 'Anexo arquivado com Sucesso!');
								} else {
									dialogConfirm("Erro!", request.data);
								}
							}
						});
					} </script></div>
EOT;
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'etc006FormEditarWhitelist':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$sqlT258 = mysql_query("SELECT T258_D024_Id FROM T258 WHERE T258_Id = '{$acaoId}'");
		$rowT258 = mysql_fetch_assoc($sqlT258);

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T258';
		$form_campo_pk = 'T258_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc006FormEditarWhitelist_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc006/form/etc006FormEditarWhitelist/';

		// novo formulário
		$form = new Formulario('etc006FormEditarWhitelist');
		$form_editor = array('geral' => '{"T258_D024_Id":{"top":30,"left":15,"width":377,"hidden":0},"etc006FormEditarWhitelist_btn":{"top":75,"left":404,"width":null,"hidden":0},"T258_D013_Id":{"top":53,"left":20,"width":399,"hidden":0},"T258_Registro":{"top":7,"left":17,"width":392,"hidden":0},"T258_Tipo":{"top":76,"left":37,"width":96,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T258_D024_Id' => array('name' => 'T258_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'extra' => "onchange=\"carregaContatos('{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id ORDER BY D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]')),
					'T258_D013_Id' => array('name' => 'T258_D013_Id', 'label'=> 'Contato', 'type' => 'select', 'select' => gGeraSelect('D013', 'D013_Id', 'D013_Nome_Contato, D013_E_Mail', "WHERE D013_D024_Id = '{$rowT258['T258_D024_Id']}'", true)),
					'T258_Registro' => array('name' => 'T258_Registro', 'label' => 'Registro', 'type' => 'text'),
					'T258_Tipo' => array('name' => 'T258_Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'E-mail', 'value' => 'E'),
							array('title' => 'Domínio', 'value' => 'D')
						)
					),
					'etc006FormEditarWhitelist_btn' => array('name' => 'etc006FormEditarWhitelist_btn', 'type' => 'submit', 'reload' => false, 'value' => 'Alterar', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
}

function pegarCodificacao($estrutura)
{
	if(empty($estrutura->parts[0])){
		if(is_array($estrutura->parameters)){
			return $estrutura->parameters[0]->value;
		} else {
			return '';
		}
	}
	$estrutura = $estrutura->parts[0];
	if(!empty($estrutura->parts[0])){
		return pegarCodificacao($estrutura);
	} else {
		if(is_array($estrutura->parameters)){
			return $estrutura->parameters[0]->value;
		} else {
			return '';
		}
	}
}

  
