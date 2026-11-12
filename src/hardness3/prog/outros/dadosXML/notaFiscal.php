<?php
namespace hardness;

switch($g['r'][3]){
	case 'content':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/outros/dadosXML/notaFiscal/form/', '&tab=geral'),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'form':
		global $g;
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'XXXX';
		$form_campo_pk = 'XXXX_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'processarXMLNotaFiscal_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/outros/dadosXML/notaFiscal/form/';

        //form editor
        $form_editor = array(
            'geral' => '{"dataInicial":{"top":7,"left":38,"width":88,"hidden":0},"dataFinal":{"top":30,"left":45,"width":88,"hidden":0}}',
        );

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'dataInicial' => array('name' => 'dataInicial', 'label' => 'Data inicial', 'type' => 'data', 'reject' => true),
					'dataFinal' => array('name' => 'dataFinal', 'label' => 'Data final', 'type' => 'data', 'reject' => true),
				)
			)
		);
        $form = new Formulario('processarXMLNotaFiscal');
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
				$dataInicial = gCorrigeDataInverte($form->campoValorEnviado('dataInicial'));
				$dataFinal = gCorrigeDataInverte($form->campoValorEnviado('dataFinal'));
				$retorno = processarDadosXML($dataInicial, $dataFinal);
				if($retorno === true){
		    		echo <<<EOT
				   		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				   			window.open('/hardness3/{$g['pathDadosUsuario']}dadosXML/dadosXML_notaFiscal_{$dataInicial}_{$dataFinal}.csv');
		   				} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Erro', $retorno);
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="processarXMLNotaFiscal_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	break;
}

function processarDadosXML($dataInicial, $dataFinal, $tags = "DigestValue,verAplic")
{
	global $g;
	$sqlT007 = mysql_query("SELECT * FROM T007 LEFT JOIN D024 ON D024_Id=T007_D024_Id WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' AND T007_C004_Id = '{$g['empresaAtual']}' AND (T007_Flag_ACP = '6' OR T007_Flag_ACP = '8') ORDER BY T007_Data_Emissao");
	if(mysql_num_rows($sqlT007) > 0){
		$id = "dadosXML_notaFiscal_{$dataInicial}_{$dataFinal}";
		$dir = $g['pathDadosUsuario'] . 'dadosXML/';
		is_dir($dir) or mkdir($dir, 0777, true);
		$file = $dir . $id . '.csv';
		$fp = fopen($file, 'w');

		$tags = explode(",", $tags);
		$csv = array(utf8_decode('Número'), utf8_decode('Data de Emissão'), utf8_decode('Tipo'), utf8_decode('Chave de Acesso'), utf8_decode('Protocolo'), utf8_decode('Cliente'));
		foreach ($tags as $tag) {
			$csv[] = $tag;
		}
		fputcsv($fp, $csv, ';');

		while($rowT007 = mysql_fetch_array($sqlT007)){
			if(!empty($rowT007['T007_Numero_Protocolo_Nfe'])){
				$csv = array($rowT007['T007_Numero_Nota_Fiscal'], $rowT007['T007_Data_Emissao'], ($rowT007['T007_Flag_ACP'] == '6') ? 'NF-e' : 'NFC-e', $rowT007['T007_Chave_Acesso_Nfe'], $rowT007['T007_Numero_Protocolo_Nfe'], utf8_decode($rowT007['D024_Nome_Empresa']));
				$cAno_Mes_NFe = substr($array['todosCampos']['T007_Data_Emissao'],0,7);
				$local1 = "{$g['pathDadosAntigo']}/nfe/xml/autorizado/{$cAno_Mes_NFe}/".$rowT007['T007_Numero_Protocolo_Nfe'].".xml";
				//Arquivos XML antigos da Alumifix
				$local2 = "{$g['pathDadosAntigo']}/empresas/E003/nfe/xml/autorizado/".$rowT007['T007_Numero_Protocolo_Nfe'].".xml";
				$local3 = "{$g['pathDadosAntigo']}/empresas/E003/nfe/xml/autorizado/{$cAno_Mes_NFe}/".$rowT007['T007_Numero_Protocolo_Nfe'].".xml";

				if(!file_exists($local1)){
					if(!file_exists($local2)){
						if(!file_exists($local3)){
							if(!empty($rowT007['T007_Arquivo_XML'])){
								$conteudoXML = $rowT007['T007_Arquivo_XML'];
							} else {
								$csv[] = "XML não encontrado";
							}
						} else {
							$arquivoXML = $local3;
						}
					} else {
						$arquivoXML = $local2;
					}
				} else {
					$arquivoXML = $local1;
				}
				if(empty($conteudoXML)){
					$conteudoXML = file_get_contents($arquivoXML);
				}

				$dom = new \DomDocument;
            	@$dom->loadXML($conteudoXML);
				foreach ($tags as $tag) {
					$csv[] = utf8_decode($dom->getElementsByTagName($tag)->item(0)->nodeValue);
				}

				fputcsv($fp, $csv, ';');
				unset($conteudoXML);
			}
		}
		fclose($fp);
		return true;
	}
	return "Nenhuma nota fiscal encontada!";
}