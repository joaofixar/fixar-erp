<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'sistemaPImpCSVEnviar':
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
			$formId = (isset($r_formcad024FormPrincipal)) ? $r_formcad024FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T000';
		$form_campo_pk = 'T000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'sistemaPImpCSVEnviarEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/importacaoCSV/form/sistemaPImpCSVEnviar/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'uploadCSV' => array('name' => 'uploadCSV', 'reject' => true, 'label' => 'Arquivo .CSV', 'type' => 'file', 'length' => array(20,80)),
					'limparConf' => array('name' => 'limparConf', 'reject' => true, 'label' => 'Limpar Configurações?', 'type' => 'checkbox'),
				),
			)
		);
        $form = new Formulario('sistemaPImpCSVEnviarForm');
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
				// Insert
				$limparConf = $form->campoValorEnviado('limparConf');
				$uploadCSV = $form->campoValorEnviado('uploadCSV');
				if ($uploadCSV['error'] == UPLOAD_ERR_OK) {
					$dir = "{$g['pathDados']}importacaoCSV/";
					$arquivo = $dir . 'importacaoCSV-ultimoArquivo.csv';
					unlink($arquivo);
					move_uploaded_file($uploadCSV['tmp_name'], $arquivo);
					if (!empty($limparConf)) {
						$arquivo = $dir . 'importacaoCSV.serialized';
						file_put_contents($arquivo, serialize(array()));
					}
					file_put_contents($dir . 'importacaoCSV-ultimoArquivo.nome', $uploadCSV['name']);
				}
				// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
				$alert = gGeraAlertMsg('Operação OK', 'Arquivo enviado com sucesso!', false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true);
					} </script></div>
EOT;
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="sistemaPImpCSVEnviarEnviar">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;

	case 'sistemaPImpCSVEnviarZIP':
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
			$formId = (isset($r_formcad024FormPrincipal)) ? $r_formcad024FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T000';
		$form_campo_pk = 'T000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'sistemaPImpCSVEnviarEnviarZIP';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/importacaoCSV/form/sistemaPImpCSVEnviarZIP/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'uploadZIP' => array('name' => 'uploadZIP', 'reject' => true, 'label' => 'Arquivo .ZIP', 'type' => 'file', 'length' => array(20,80)),
				),
			)
		);
        $form = new Formulario('sistemaPImpCSVEnviarZIPForm');
		$form->definirFormularioArray($formArray);

		// Carrega arquivos salvos
		$dir = "{$g['pathDados']}importacaoCSV/";
		$arquivosSalvos = array();
		$arquivos = scandir($dir);
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || $arquivo == 'importacaoCSV.serialized' || strpos($arquivo, 'serialized') === false) { continue; }
			$array = unserialize(file_get_contents($dir . $arquivo));
			unset($array['arquivoCSV']);
			$array['path'] = $dir . $arquivo;
			$arquivosSalvos[] = $array;
			unset($array);
		}
		usort($arquivosSalvos, function($a, $b) {
			if (!isset($a['ordem']) && !isset($b['ordem'])) {
				return 0;
			} else if (!isset($a['ordem'])) {
				return -1;
			} else if (!isset($b['ordem'])) {
				return 1;
			} else {
				if ($a['ordem'] == $b['ordem']) {
					return 0;
				}
				return ($a['ordem'] < $b['ordem']) ? -1 : 1;
			}
		});

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
				// Insert
				$limparConf = $form->campoValorEnviado('limparConf');
				$uploadZIP = $form->campoValorEnviado('uploadZIP');
				if ($uploadZIP['error'] == UPLOAD_ERR_OK) {
					//move_uploaded_file($uploadZIP['tmp_name'], $arquivo);
					$zip = zip_open($uploadZIP['tmp_name']);
					if ($zip)
					{
						// guarda arquivo atual (para depois do processo voltar)
						$arquivoCSV = "{$dir}importacaoCSV-ultimoArquivo.csv";
						file_put_contents('/tmp/importacaoCSV-ultimoArquivo.csv', file_get_contents($arquivoCSV));
						$arquivoInfo = "{$dir}importacaoCSV.serialized";
						file_put_contents('/tmp/importacaoCSV.serialized', file_get_contents($arquivoInfo));
						$arquivoNome = "{$dir}importacaoCSV-ultimoArquivo.nome";
						file_put_contents('/tmp/importacaoCSV-ultimoArquivo.nome', file_get_contents($arquivoNome));
						//
						$retorno = '';
						while ($zip_entry = zip_read($zip)) 
						{
							$arquivoZIP = basename(zip_entry_name($zip_entry));

							if (preg_match('#.csv$#i', $arquivoZIP))
							{
								if (zip_entry_open($zip, $zip_entry, "r")) 
								{
									$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
									zip_entry_close($zip_entry);
								}
								foreach ($arquivosSalvos as $salvo) {
									if (strtolower(trim($arquivoZIP)) == strtolower(trim($salvo['arquivoNome']))) {
										// grava o arquivo para o arquivo atual
										file_put_contents($arquivoCSV, $buf);
										file_put_contents($arquivoInfo, serialize($salvo['importacaoCSV']));
										file_put_contents($arquivoNome, $salvo['arquivoNome']);
										// parametros para importação
										$r_tabela = $r_acaoId = $salvo['tabela'];
										$r_tipo = 'both';
										$r_simula = 'false';
										$g['r'][3] = 'importar';
										$retornoImportar = true;
										// chama a importação
										$retorno .= "<h1>{$r_tabela} - {$salvo['arquivoNome']}</h1>";
										include 'prog/sistema/importacaoCSV/grid_func-ajax.php';
										$retorno .= "CSV Importado!<br/>Registros OK (" . $resposta['linhasOk'] . ") Registros ERRO (" . $resposta['linhasErro'] . ")<br/><br/>Insert (caso houver):<br/>" . $resposta['logInsert'] . "<br/>Update (caso houver):<br/>" . $resposta['logUpdate'] . "<br/>Erros (caso houver):<br/>" . $resposta['erros'] . "<br /><br />";
										// Salva o último .CSV no registro salvo (caso for carregar mais tarde)
										$array = unserialize(file_get_contents($salvo['path']));
										$array['arquivoCSV'] = base64_encode($buf);
										file_put_contents($salvo['path'], serialize($array));
									}
								}
							}
						}
						$retorno = empty($retorno) ? 'Sem arquivos .CSV encontrados no ZIP.' : "<div style='overflow: auto; width: 1000px; height: 400px;'>{$retorno}</div>";
						echo gGeraAlertMsg('Ok!', $retorno);
						zip_close($zip);
						// volta arquivos que eram atuais
						file_put_contents($arquivoCSV, file_get_contents('/tmp/importacaoCSV-ultimoArquivo.csv'));
						file_put_contents($arquivoInfo, file_get_contents('/tmp/importacaoCSV.serialized'));
						file_put_contents($arquivoNome, file_get_contents('/tmp/importacaoCSV-ultimoArquivo.nome'));
					} else {
						echo gGeraAlertMsg('Erro', 'Erro abrindo arquivo .ZIP');
					}
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Erro', 'Ocorreu um erro tentando enviar o arquivo: ' . $uploadZIP['error']);
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="sistemaPImpCSVEnviarEnviarZIP">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;
}
?>
