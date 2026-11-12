<?php
namespace hardness;

set_time_limit(0);
// vars
$vars = '';
foreach ($_REQUEST as $key => $value) {
	if ($key == 'r' || $key == 'ajax' || $key == 'divId' || $key == 'divIdRoot' || $key == 'divIdParent' || is_array($value)) { continue; }
	$vars .= "&{$key}=" . urlencode($value);
}

switch ($g['r'][3]) {
	/* GRID PRINCIPAL */

	case 'content':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/importacaoDeDados/tab/', '&tab=geral' . $vars),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/sistema/funcoes/importacaoDeDados/grid/', 'Importação de Dados', $vars);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;

	 case 'grid':
	 	// Simula o carregamento do grid original (para poder pegar o arquivo de definição do grid)
		$g['r'] = $r_href;
		$g['r_'] = $g['r'];
		$g['r_'] = preg_replace('/^\//', '', $g['r_']);
		$g['r'] = explode('/', trim($g['r'], '/'));
        $programaPhp = "prog/{$g['r'][0]}/{$g['r'][1]}/{$g['r'][2]}.php";

         // Para funcionar em grid personalizados pela IDE (mesmo código existente no index.php)
         if ("{$g['r'][0]}/{$g['r'][1]}" == 'sistema/personalizacao') {
             // Grids novos personalizados
             $programaPhp = "prog/sistema/personalizacaoLowCode/{$g['r'][2]}.php";
             $programaPhpFramework = "framework/{$programaPhp}";
             if (file_exists($programaPhpFramework)) {
                 $programaPhp = $programaPhpFramework;
             }
             $g['rOriginal'] = $g['r'];
             $g['r'][3] = $g['r'][2];
         }

		if (!file_exists($programaPhp)) { die('Erro interno - Por favor contate o suporte técnico.'); }
		ob_start();
		include($programaPhp);
		ob_end_clean();

		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
        is_dir($dir) or mkdir($dir, 0777, true);
	 	// Arquivo de configurações de importação e último arquivo enviado
        $idGridImp = trim($g['r_'], '/');
        $idGridImp = str_replace('/', '-', $idGridImp);
        if(isset($r_acaoId) && !empty($r_acaoId) && isset($from) && !empty($from)){
        	$idGridImp .= '-' . $from . '-' . $r_acaoId;
        }

        if(is_array($importacaoDados)){
			foreach ($importacaoDados as $key => $value) {
				if(strpos($key, 'C004_Id') !== false){
					$idGridImp = $value.$idGridImp;
					$C004_Id = $value;
				}
			}
		}

		$arquivoConf = $dir . $idGridImp . '-conf';
		$arquivoImp = $dir . $idGridImp . '-arquivo';
		$arquivoTrigger = $dir . $idGridImp . '-trigger';

		// Recuperar arquivo de configuração
		if (isset($r_arquivoConf) && $r_arquivoConf) {
			$array = unserialize(file_get_contents($dir . $r_arquivoConf));
			if (isset($array['conf']) && isset($array['arquivo'])) {
				file_put_contents($arquivoImp, base64_decode($array['arquivo']));
				file_put_contents($arquivoConf, $array['conf']);
				file_put_contents($arquivoTrigger, $array['trigger']);
			}
			$config = unserialize($array['conf']);
			$checked = ($config['info']['pularLinha'] == 'true') ? 'checked="checked"' : '';
		}

		// Botões
		$btns = '';
		if (file_exists($arquivoConf)) {
			$config = unserialize(file_get_contents($arquivoConf));
			$checked = ($config['info']['pularLinha'] == 'true') ? 'checked="checked"' : '';
			$btns .= '<button type="button" id="importacaoDeDadosImportar">Importar</button>&nbsp;';
			$btns .= '<button type="button" id="importacaoDeDadosSalvar">Salvar Conf.</button>&nbsp;';
			$btns .= '<button type="button" id="importacaoDeDadosRecuperar">Recuperar Conf.</button>&nbsp;';
			$btns .= '<button type="button" id="importacaoDeDadosTrigger">Trigger SQL</button>&nbsp;';
			$btns .= '<input type="checkbox" id="importacaoPularPrimeiraLinha" '.$checked.'><font size="2"> Pular Primeira Linha</font></input>&nbsp;&nbsp;&nbsp;';
			$btns .= '<font size="2">&nbsp;&nbsp;&nbsp;&nbsp;Tabela: '.$from.'</font>';
		}

	 	echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="importacaoDeDadosEnviarCSV">Enviar Arquivo .CSV</button>&nbsp;{$btns}
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#importacaoDeDadosEnviarCSV').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Arquivo .CSV', '/sistema/funcoes/importacaoDeDados/contentEnviarCSV/', '&href=' + encodeURIComponent('{$r_href}') + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&C004_Id=' + encodeURIComponent('{$C004_Id}') + '&from=' + encodeURIComponent('{$from}'), [430,120]);
				});
				$('#importacaoDeDadosImportar').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Incluir/Alterar": function() { 
							acaoImportar('both');
							$(this).dialog("destroy").remove(); 
						},
						/*"Insert": function() { 
							acaoImportar('insert');
							$(this).dialog("destroy").remove(); 
						},*/
						"Alterar": function() { 
							acaoImportar('update');
							$(this).dialog("destroy").remove(); 
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); } 
					};
    				dialogConfirm("Executar Importação", "Escolha uma das duas opções:<br/><br/><b>Incluir/Alterar</b>: Irá incluir novos registros e alterar os que já existem (se a Chave Primária existir);<br/><b>Alterar</b>: Somente irá alterar registros que já existem.<br/><br/>Deseja continuar?", buttons)
				});
				function acaoImportar(tipo, simula) {
					var simula = (typeof(simula) == 'undefined') ? false : simula;
					$('#{$g['divId']}').showLoading();
					$.getJSON('/sistema/funcoes/importacaoDeDados/importar/?ajax=true&href=' + encodeURIComponent('{$r_href}') + '&tipo=' + tipo + '&simula=' + simula + '{$vars}' + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&C004_Id=' + encodeURIComponent('{$C004_Id}') + '&from=' + encodeURIComponent('{$from}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							dialogConfirm("Ok", "CSV Importado!<br/>Registros OK ("+request.linhasOk+") Registros ERRO ("+request.linhasErro+")<br/><br/>Insert (caso houver):<br/>" + request.logInsert + "<br/>Update (caso houver):<br/>" + request.logUpdate + "<br/>Erros (caso houver):<br/>" + request.erros);
						} else {
							dialogConfirm("Erro", "Ocorre um erro ao tentar importar o arquivo.<br /><br />Erros:<br />" + request.erros);
						}
					});
				}
				// Salvar Conf.
				$('#importacaoDeDadosSalvar').unbind('click').bind('click', function(data) {
					var idUnico = unique();
					var buttons = { 
						"Salvar": function() { 
							$.getJSON('/sistema/funcoes/importacaoDeDados/salvar/?ajax=true&idGridImp=' + encodeURIComponent('{$idGridImp}') + '&nome=' + encodeURIComponent($('#' + idUnico).val()) + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&from=' + encodeURIComponent('{$from}') + '&callback=?', function(request) {
								dialogConfirm("Ok", "Informações salvas!");
							});
							$(this).dialog("destroy").remove(); 
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); } 
					};
    				dialogConfirm("Salvar as configurações", "Nome: <input type='text' id='" + idUnico + "' size='30'>", buttons)
				});
				// Recuparar Conf.
				$('#importacaoDeDadosRecuperar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Recuperar Configuração', '/sistema/funcoes/importacaoDeDados/contentRecuperar/', '&idGridImp=' + encodeURIComponent('{$idGridImp}'));
				});
				// Trigger
				$('#importacaoDeDadosTrigger').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Comando SQL executado no final da operação', '/sistema/funcoes/importacaoDeDados/formTrigger/', '&tab=geral&idGridImp=' + encodeURIComponent('{$idGridImp}'), [850,380]);
				});
				//Pular primeira linha
				$('#importacaoPularPrimeiraLinha').unbind('click').bind('click', function(data) {
					$.getJSON('/sistema/funcoes/importacaoDeDados/pularPrimeiraLinha/?ajax=true&primeiraLinha=' + encodeURIComponent($('#importacaoPularPrimeiraLinha').prop('checked')) + '&href=' + encodeURIComponent('{$r_href}') + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&C004_Id=' + encodeURIComponent('{$C004_Id}') + '&from=' + encodeURIComponent('{$from}') + '&callback=?', function(request) {
						//
					});
				});
			} </script></div>
EOT;

		// Combo de campos com as colunas do grid
		$selectArray = array(array('title' => 'NENHUM', 'value' => ''));
		foreach ($grid as $value) {
            $colunaValor = $value['colunaValorOriginal'] ? $value['colunaValorOriginal'] : $value['colunaValor'];
			// colunaExibir
			//if (isset($value['colunaExibir']) && !$value['colunaExibir']) { continue; }
			// ignorar expressões SQL (subselects, max, count, etc... somente pegar campos)
            if (substr($colunaValor, 0, strpos($colunaValor, '_')) != $from) { continue; }
            //if (strpos($colunaValor,'_Id') !== false && strpos($colunaValor,'_Id') < (strlen($colunaValor)-4)) { continue; }
			if (empty($value['colunaTitulo'])) { continue; }
            if (!preg_match('#^([a-z]|[0-9])+_([a-z]|[0-9]|_|-|>)+$#i', $colunaValor)) { continue; }

            //
			//$selectArray[] = array('title' => (empty($value['colunaTitulo']) ? $colunaValor : $value['colunaTitulo']), 'value' => $colunaValor);
			//$colunaValor=substr($colunaValor,0,4).' '.$from;
			$colunaTitulo = $value['colunaTitulo']; //.' - '.strtolower(str_replace('_',' ',str_replace($from, '', $colunaValor)));
			$selectArray[] = array('title' => $colunaTitulo, 'tooltip' => $value['colunaTitulo'], 'value' => $colunaValor);
		}
		/*
		usort($selectArray, function($a, $b) {
			if ($a['value'] == $b['value']) {
		        return 0;
		    }
		    return ($a['value'] < $b['value']) ? -1 : 1;
		});
		*/

		// Conf
		if (!file_exists($arquivoConf)) {
			die('<div style="margin-top: 5px; width: 100%; height: 1px; background-color: gray;"></div><div style="margin: 10px; font-size: 1.1em;">Por favor, envie um arquivo .CSV para iniciar a importação.</div>');
		} else {
			$conf = unserialize(file_get_contents($arquivoConf));
			$info = &$conf['info'];
		}

		// Processa o .CSV
		$grid = array();
		$rows = 0;
		$geradoDados = array();
		if (($handle = fopen($arquivoImp, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		    	$cols = count($data);
		    	// Cria as colunas no grid
		    	if (empty($grid)) {
		    		$rowsArray = array();
		    		for ($c=0; $c < $cols; $c++) {
		    			$checked = '';
		    			$checkedLike = '';
		    			$showLike = 'display: none;';
						$grid[] = array('colunaValor' => 'Coluna' . ($c+1) , 'colunaTitulo' => 'Coluna ' . ($c+1));
						$selectArrayTmp = $selectArray;
						if (isset($info[$c])) {
							foreach ($selectArrayTmp as &$value) {
								if (isset($info[$c][0]) && ($value['value'] == $info[$c][0])) {
									$value['selected'] = true;
								}
								if (isset($info[$c][1]) && $info[$c][1]) {
									$checked = ' checked="checked"';
									$showLike = '';
								}
								if (isset($info[$c]['like']) && $info[$c]['like']) {
									$checkedLike = ' checked="checked"';
								}
							}
						}

						$rowsArray[] = "<div>Chave Primária: <input type=\"checkbox\" onChange=\"campoColunaPK('{$c}', \$(this)); return false;\"{$checked} /><span style=\"{$showLike}\" class=\"buscaParcial\">&nbsp;Busca Parcial: <input type=\"checkbox\" onChange=\"campoColunaLike('{$c}', \$(this)); return false;\"{$checkedLike} /></span><br />Campo: <select class=\"formInput ui-state-default ui-corner-all\" onChange=\"campoColuna('{$c}', \$(this)); return false;\">" . gProcessaSelect($selectArrayTmp) . "</select></div>";
					}
					$geradoDados[] = $rowsArray;
				}
		    	$rows++;
		    	if ($rows > 50) { continue; }
		    	$rowsArray = array();
		    	$temBusca = false;
		    	foreach ($data as $value) {
		    		if (!empty($busca) && stripos($value, $busca) !== false) { $temBusca = true; }
		        	$rowsArray[] = utf8_encode($value);
		    	}
		    	if (empty($busca) || $temBusca) {
		    		$geradoDados[] = $rowsArray;
				}
			}
			fclose($handle);
		}
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function campoColuna(coluna, elem) {
					$.getJSON('/sistema/funcoes/importacaoDeDados/campoColuna/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&campo=' + encodeURIComponent(elem.val()) + '&href=' + encodeURIComponent('{$r_href}') + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&C004_Id=' + encodeURIComponent('{$C004_Id}') + '&from=' + encodeURIComponent('{$from}') + '&callback=?', function(request) {
						//
					});
				}

				function campoColunaPK(coluna, elem) {
					if (elem.prop('checked')) {
						elem.closest('div').find('.buscaParcial').show();
					} else {
						elem.closest('div').find('.buscaParcial').hide();
					}
					$.getJSON('/sistema/funcoes/importacaoDeDados/campoColunaPK/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&pk=' + encodeURIComponent(elem.prop('checked')) + '&href=' + encodeURIComponent('{$r_href}') + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&C004_Id=' + encodeURIComponent('{$C004_Id}') + '&from=' + encodeURIComponent('{$from}') + '&callback=?', function(request) {
						//
					});
				}

				function campoColunaLike(coluna, elem) {
					$.getJSON('/sistema/funcoes/importacaoDeDados/campoColunaLike/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&pk=' + encodeURIComponent(elem.prop('checked')) + '&href=' + encodeURIComponent('{$r_href}') + '&acao_Id=' + encodeURIComponent('{$r_acaoId}') + '&C004_Id=' + encodeURIComponent('{$C004_Id}') + '&from=' + encodeURIComponent('{$from}') + '&callback=?', function(request) {
						//
					});
				}

				// limpa arquivoConf (utilizado para recarregar)
				divSetarAcaoId('{$g['divId']}', '', 'arquivoConf');
			} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;

	 /* ENVIO ARQUIVO CSV */

	 case 'contentEnviarCSV':
	 	/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/importacaoDeDados/formEnviarCSV/', '&tab=geral&href=' . urldecode($r_href)),
		);
		echo gProcessaAcoes($actions);
	 break;

	 case 'formEnviarCSV':
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
		$enviar_btnId = 'importacaoDeDadosEnviarCSVBTN';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/importacaoDeDados/formEnviarCSV/';
		
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
        $form = new Formulario('importacaoDeDadosEnviarCSV');
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
					// Arquivo de configurações de importação e último arquivo enviado
					$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
					is_dir($dir) or mkdir($dir, 0777, true);
					$idGridImp = trim($r_href, '/');
					$idGridImp = str_replace('/', '-', $idGridImp);
					if(isset($r_acao_Id) && !empty($r_acao_Id) && isset($r_from) && !empty($r_from)){
			        	$idGridImp .= '-' . $r_from . '-' . $r_acao_Id;
			        }
			        if(isset($r_C004_Id) && !empty($r_C004_Id)){
			    		$idGridImp = $r_C004_Id.$idGridImp;
			    	}
					$arquivoConf = $dir . $idGridImp . '-conf';
					$arquivoImp = $dir . $idGridImp . '-arquivo';
					// Arquivo
					move_uploaded_file($uploadCSV['tmp_name'], $arquivoImp);
					// Conf
					if (empty($limparConf) && file_exists($arquivoConf)) {
						$conf = unserialize(file_get_contents($arquivoConf));
						$conf['tipo'] = 'CSV';
					} else {
						$conf = array(
							'tipo' => 'CSV',
							'info' => array()
						);
					}
					file_put_contents($arquivoConf, serialize($conf));
				}
				// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
				$alert .= gGeraAlertMsg('Operação OK', 'Arquivo enviado com sucesso!', false);
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
		$botoesDir .= '<button type="submit" id="importacaoDeDadosEnviarCSVBTN">Enviar</button>';
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

	/* Ações */

	case 'campoColuna':
		$resposta = array('code' => true, 'data' => array());

		// Arquivo de configurações de importação e último arquivo enviado
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
		is_dir($dir) or mkdir($dir, 0777, true);
		$idGridImp = trim($r_href, '/');
		$idGridImp = str_replace('/', '-', $idGridImp);
		if(isset($r_acao_Id) && !empty($r_acao_Id) && isset($r_from) && !empty($r_from)){
        	$idGridImp .= '-' . $r_from . '-' . $r_acao_Id;
        }
        if(isset($r_C004_Id) && !empty($r_C004_Id)){
    		$idGridImp = $r_C004_Id.$idGridImp;
    	}
		$arquivoConf = $dir . $idGridImp . '-conf';
		$arquivoImp = $dir . $idGridImp . '-arquivo';

		// Conf
		if (file_exists($arquivoConf)) {
			$conf = unserialize(file_get_contents($arquivoConf));
			$conf['info'][$r_coluna] = array($r_campo, isset($conf['info'][$r_coluna][1]) ? $conf['info'][$r_coluna][1] : false);
		}
		file_put_contents($arquivoConf, serialize($conf));

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'campoColunaPK':
		$resposta = array('code' => true, 'data' => array());

		// Arquivo de configurações de importação e último arquivo enviado
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
		is_dir($dir) or mkdir($dir, 0777, true);
		$idGridImp = trim($r_href, '/');
		$idGridImp = str_replace('/', '-', $idGridImp);
		if(isset($r_acao_Id) && !empty($r_acao_Id) && isset($r_from) && !empty($r_from)){
        	$idGridImp .= '-' . $r_from . '-' . $r_acao_Id;
        }
        if(isset($r_C004_Id) && !empty($r_C004_Id)){
    		$idGridImp = $r_C004_Id.$idGridImp;
    	}
		$arquivoConf = $dir . $idGridImp . '-conf';
		$arquivoImp = $dir . $idGridImp . '-arquivo';

		// Conf
		if (file_exists($arquivoConf)) {
			$conf = unserialize(file_get_contents($arquivoConf));
			if ($r_pk == 'true') {
				/*foreach ($conf['info'] as $value) {
					$value[1] = false;
				}*/
				$conf['info'][$r_coluna][1] = true;
			} else {
				$conf['info'][$r_coluna][1] = false;
			}
		}
		file_put_contents($arquivoConf, serialize($conf));

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'campoColunaLike':
		$resposta = array('code' => true, 'data' => array());

		// Arquivo de configurações de importação e último arquivo enviado
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
		is_dir($dir) or mkdir($dir, 0777, true);
		$idGridImp = trim($r_href, '/');
		$idGridImp = str_replace('/', '-', $idGridImp);
		if(isset($r_acao_Id) && !empty($r_acao_Id) && isset($r_from) && !empty($r_from)){
        	$idGridImp .= '-' . $r_from . '-' . $r_acao_Id;
        }
        if(isset($r_C004_Id) && !empty($r_C004_Id)){
    		$idGridImp = $r_C004_Id.$idGridImp;
    	}
		$arquivoConf = $dir . $idGridImp . '-conf';
		$arquivoImp = $dir . $idGridImp . '-arquivo';

		// Conf
		if (file_exists($arquivoConf)) {
			$conf = unserialize(file_get_contents($arquivoConf));
			if ($r_pk == 'true') {
				/*foreach ($conf['info'] as $value) {
					$value[1] = false;
				}*/
				$conf['info'][$r_coluna]['like'] = true;
			} else {
				$conf['info'][$r_coluna]['like'] = false;
			}
		}
		file_put_contents($arquivoConf, serialize($conf));

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'pularPrimeiraLinha':
		$resposta = array('code' => true, 'data' => array());

		// Arquivo de configurações de importação e último arquivo enviado
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
		is_dir($dir) or mkdir($dir, 0777, true);
		$idGridImp = trim($r_href, '/');
		$idGridImp = str_replace('/', '-', $idGridImp);
		if(isset($r_acao_Id) && !empty($r_acao_Id) && isset($r_from) && !empty($r_from)){
        	$idGridImp .= '-' . $r_from . '-' . $r_acao_Id;
        }
        if(isset($r_C004_Id) && !empty($r_C004_Id)){
    		$idGridImp = $r_C004_Id.$idGridImp;
    	}
		$arquivoConf = $dir . $idGridImp . '-conf';
		$arquivoImp = $dir . $idGridImp . '-arquivo';

		// Conf
		if (file_exists($arquivoConf)) {
			$conf = unserialize(file_get_contents($arquivoConf));
			$conf['info']['pularLinha'] = $r_primeiraLinha;
		}
		file_put_contents($arquivoConf, serialize($conf));

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'importar':
		global $g;

		$resposta = array('code' => true, 'data' => array());

		// Simula o carregamento do grid original (para poder pegar o arquivo de definição do grid)
		$g['r'] = $r_href;
		$g['r_'] = $g['r'];
		$g['r_'] = preg_replace('/^\//', '', $g['r_']);
		$g['r'] = explode('/', trim($g['r'], '/'));
		$programaPhp = "prog/{$g['r'][0]}/{$g['r'][1]}/{$g['r'][2]}.php";

        // Para funcionar em grid personalizados pela IDE (mesmo código existente no index.php)
        if ("{$g['r'][0]}/{$g['r'][1]}" == 'sistema/personalizacao') {
            // Grids novos personalizados
            $programaPhp = "prog/sistema/personalizacaoLowCode/{$g['r'][2]}.php";
            $programaPhpFramework = "framework/{$programaPhp}";
            if (file_exists($programaPhpFramework)) {
                $programaPhp = $programaPhpFramework;
            }
            $g['rOriginal'] = $g['r'];
            $g['r'][3] = $g['r'][2];
        }

		if (!file_exists($programaPhp)) { die('Erro interno - Por favor contate o suporte técnico.'); }
		ob_start();
		include($programaPhp);
		ob_end_clean();

		$r_tabela = $from;

		$pathLegado = "configuracoes/{$r_tabela}.php";
		$pathIde = $g['pathDados'] . "personalizacaoConf/{$r_tabela}-triggers.php";
		$pathApp = "apps/ativos/{$g['dbDatabase']}/personalizacaoConf/{$r_tabela}-triggers.php";
		$conf = array();

		if(file_exists($pathLegado))
		{
			include $pathLegado;
		}
		else if(file_exists(($pathApp)))
		{
			include $pathApp;
		}
		else
		{
			include $pathIde;
		}
		
		include (file_exists($pathLegado) ? $pathLegado : $pathIde);

		/* Config do arquivo de configuração da tabela
		que indica se o importar CSV deve chamar as triggers php
		ou não */
		$triggerPhpAtivo = (isset($conf['importarCSV']['triggerPhpAtivo']) ? $conf['importarCSV']['triggerPhpAtivo'] : false);

		// Arquivo de configurações de importação e último arquivo enviado
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
        is_dir($dir) or mkdir($dir, 0777, true);
        $idGridImp = trim($g['r_'], '/');
        $idGridImp = str_replace('/', '-', $idGridImp);
        if(isset($r_acaoId) && !empty($r_acaoId) && isset($from) && !empty($from)){
        	$idGridImp .= '-' . $from . '-' . $r_acaoId;
        }
        if(isset($r_C004_Id) && !empty($r_C004_Id)){
    		$idGridImp = $r_C004_Id.$idGridImp;
    	}
		$arquivoConf = $dir . $idGridImp . '-conf';
		$arquivoImp = $dir . $idGridImp . '-arquivo';
		$arquivoTrigger = $dir . $idGridImp . '-trigger';
		// Importar
		$cacheConversorCampos = array();
		$resposta['erros'] = '';
		$resposta['logInsert'] = '';
		$resposta['logUpdate'] = '';
		if (!file_exists($arquivoImp)) {
			$resposta['code'] = false;
			$resposta['erros'] = 'Arquivo .CSV não foi carregado!';
		} else {
			$functionPersonalizaLimparAcentos = false;
			$gLimpaAcentosImportarCSV = "{$g['pathDados']}/personalizacaoConf/gLimpaAcentosImportarCSV-alterarCallback.php";
			if (file_exists($gLimpaAcentosImportarCSV)) {
				// Apenas personalização, a função gLimpaAcentos é utilizada em outros locais, por isso não foi alterada para não dar nenhum problema
				// Personaliza essa funcção no cliente e trata os caracretes que ele quer manter na importacaoCSV
				require_once ($gLimpaAcentosImportarCSV);
				$functionPersonalizaLimparAcentos = true;
			} 

			$confCSV = unserialize(file_get_contents($arquivoConf));
			// carrega arquivo
			$info = &$confCSV['info'];
			// processa o info para encontrar FKs
			foreach ($info as $key => $value) {
				if (!isset($value[0]) || empty($value[0])) { continue; }
				if($key == 'pularLinha') { continue; }
				if (!preg_match("#^{$from}_#", $value[0])) {
					$tmp = explode('_', $value[0]);
					$info[$key] = array($from . '_' . $tmp[0] . '_Id', $value[1], $value[0], 'like' => $value['like']);
				}
			}
			// gera INSERT/UPDATE base
			$infoCols = '';
			$infoValues = '';
			$infoSet = '';
			$infoPk = array();
			$verificaDuplicado = array();
            $camposJson = [];
			foreach ($info as $key => $value) {
				if (strpos($key, 'pularLinha') !== false) { continue; }
				if (!isset($value[0]) || empty($value[0])) { continue; }
				if (in_array($value[0], $verificaDuplicado)) { 
					$resposta['code'] = false;
					$resposta['erros'] .= "Coluna da tabela selecionada mais de uma vez: {$value[0]}<br />";
				}
				$verificaDuplicado[] = $value[0];
				// PK
				if (isset($value[1]) && $value[1]) {
					/*if (!empty($infoPk)) {
						$resposta['code'] = false;
						$resposta['erros'] .= "Foi definido mais de uma chave primária (PK).";
					}*/
					$infoPk[] = $value;
					//continue;
                }
                if (strpos($value[0], '->') !== false) {
                    // Suporte campos JSON
                    $campo_ = explode('->', $value[0]);
                    $campoJson = trim($campo_[0]);
                    $itemJson = trim($campo_[1]);
                    $camposJson[$campoJson][$itemJson] = $key;
                } else {
                    $infoCols .= empty($infoCols) ? $value[0] : ', ' . $value[0];
                    $infoValues .= empty($infoValues) ? "'[[{$key}]]'" : ", '[[{$key}]]'";
                    if (!$value[1]) {
                        $infoSet .= empty($infoSet) ? "{$value[0]} = '[[{$key}]]'" : ", {$value[0]} = '[[{$key}]]'";
                    }
                }
            }
            // Suporte campos JSON
            foreach ($camposJson as $campoJson => $itensJson) {
                $campos .= (empty($campos)) ? $campoJson : ", {$campoJson}";
                $itensJson_ = '';
                foreach ($itensJson as $jsonItem => $jsonValor) {
                    if (strlen($jsonValor) == 0) { continue; }
                    $itensJson_ .= ", '$.{$jsonItem}', '[[{$jsonValor}]]'";
                }
                if (strlen($itensJson_) != 0) {
                    $infoCols .= empty($infoCols) ? $campoJson : ', ' . $campoJson;
                    $infoValues .= empty($infoValues) ? "JSON_SET(COALESCE({$campoJson}, '{}'){$itensJson_})" : ", JSON_SET(COALESCE({$campoJson}, '{}'){$itensJson_})";
                    $infoSet .= empty($infoSet) ? "{$campoJson} = JSON_SET(COALESCE({$campoJson}, '{}'){$itensJson_})" : ", {$campoJson} = JSON_SET(COALESCE({$campoJson}, '{}'){$itensJson_})";
                }
            }
			// arquivo definido no grid
			if (isset($importacaoDados)) {
				foreach ($importacaoDados as $key => $value) {
					$infoCols .= empty($infoCols) ? $key : ', ' . $key;
					$value = mysql_real_escape_string($value);
					$infoValues .= empty($infoValues) ? "'{$value}'" : ", '{$value}'";
				}
			}
			// verifica se foi definido uma PK
			if (($r_tipo == 'update' || $r_tipo == 'both') && empty($infoPk)) {
				$resposta['code'] = false;
				$resposta['erros'] .= "Não foi definido uma coluna de chave primária (PK).";
			}
			// não continuar em caso de erro
			if ($resposta['code']) {
				if ($r_tipo == 'insert') {
					$insert = "INSERT INTO {$r_tabela} ({$infoCols}) VALUE ({$infoValues});";
                    // Não é mais utilizado
                    die();
				} else if ($r_tipo == 'update') {
					$pk = '';
					$joins = '';
					foreach ($infoPk as $key => $value) {
						// Se for uma FK, é necessário fazer os joins para o update
						if (isset($value[2]) && $value[2]) {
							$fkTmp = explode('_', $value[2]);
							$joins .= "JOIN {$fkTmp[0]} ON {$fkTmp[0]}_Id = {$value[0]} ";
							//$joins .= "JOIN {$fkTmp[1]} ON {$fkTmp[1]}_Id = {$value[2]} ";
							if (isset($value['like']) && $value['like']) {
								$pk .= "AND {$value[2]} LIKE '[[PK{$key}]]%' ";
							} else {
								$pk .= "AND {$value[2]} = '[[PK{$key}]]' ";
							}
						} else {
							if (isset($value['like']) && $value['like']) {
								$pk .= "AND {$value[0]} LIKE '[[PK{$key}]]%' ";
							} else {
								$pk .= "AND {$value[0]} = '[[PK{$key}]]' ";
							}
						}
					}
					// arquivo definido no grid
					if (isset($importacaoDados)) {
						foreach ($importacaoDados as $key => $value) {
							$value = mysql_real_escape_string($value);
							$pk .= " AND {$key} = '{$value}' ";
						}
					}
					$pk = trim($pk, 'AND');
					$insert = "UPDATE {$r_tabela} {$joins} SET {$infoSet} WHERE {$pk};";
				}
				// Identificar chaves duplicadas
				$chavesPrimariasDuplicadas = array();
				foreach ($infoPk as $key => $value) {
					if (isset($value[2]) && $value[2]) {
						$chavesPrimariasDuplicadas[$key] = array('Nome' => $value[2], 'Chaves' => array());
					} else {
						$chavesPrimariasDuplicadas[$key] = array('Nome' => $value[0], 'Chaves' => array());
					}
				}

				//Verifica códigos duplicados na tabela de produtos do fornecedor
    			if($r_tabela == 'D069') {
    				$keyD069_Codigo_Barras = '';
    				foreach ($info as $key => $arrayCampo) {
    					if($arrayCampo[0] == 'D069_Codigo_Barras'){
    						$keyD069_Codigo_Barras = $key;
    					}
    				}
    				if($keyD069_Codigo_Barras != ''){
    					$codigoBarrasDuplicado = array();
    					if (($handle = fopen($arquivoImp, "r")) !== FALSE) {
    						$count = 0;
							while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
								$count++;
								if (isset($info['pularLinha']) && $count == 1) { continue; }
								$colunaValor = $data[$keyD069_Codigo_Barras];
								if(in_array($colunaValor, $codigoBarrasDuplicado)){
									$resposta['code'] = false;
									$resposta['erros'] .= "Não é permitido duplicidade para o campo D069_Codigo_Barras. O código '{$colunaValor}' já existe!<br>";
								} else {
									foreach ($data as $key => $value) {
										$valores[$info[$key][0]] = $value;
									}
									foreach ($importacaoDados as $key => $value) {
										$valores[$key] = $value;
									}
									$restricao = $g['conf']->restricaoIncluirUpdate('D069_Codigo_Barras', $colunaValor, $valores);
									if($restricao !== true){
										$resposta['code'] = false;
										$resposta['erros'] .= "{$restricao}<br>";
									}
									$codigoBarrasDuplicado[] = $colunaValor;
								}
							}
							fclose($handle);
						}
	    			}
				}
				if($resposta['code']){
					// processa CSV e preenche linha por linha o INSERT/UPDATE
					$resposta['linhasOk'] = 0;
					$resposta['linhasErro'] = 0;
					if (($handle = fopen($arquivoImp, "r")) !== FALSE) {
						$count = 0;
						while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
							$count++;
							if (isset($info['pularLinha']) && $count == 1) { continue; }
							// Decide se vai fazer um INSERT ou um UPDATE
							$r_tipo_both = false;
							if ($r_tipo == 'both') {
								// PK e JOINS necessário para o SELECT e UPDATE
								$pk = '';
								$joins = '';
								foreach ($infoPk as $key => $value) {
									// Se for uma FK, é necessário fazer os joins para o update
									if (isset($value[2]) && $value[2]) {
										$fkTmp = explode('_', $value[2]);
										$joins .= "JOIN {$fkTmp[0]} ON {$fkTmp[0]}_Id = {$value[0]} ";
										//$joins .= "JOIN {$fkTmp[1]} ON {$fkTmp[1]}_Id = {$value[2]} ";
										if (isset($value['like']) && $value['like']) {
											$pk .= "AND {$value[2]} LIKE '[[PK{$key}]]%' ";
										} else {
											$pk .= "AND {$value[2]} = '[[PK{$key}]]' ";
										}
									} else {
										if (isset($value['like']) && $value['like']) {
											$pk .= "AND {$value[0]} LIKE '[[PK{$key}]]%' ";
										} else {
											$pk .= "AND {$value[0]} = '[[PK{$key}]]' ";
										}
									}
								}
								// arquivo definido no grid
								if (isset($importacaoDados)) {
									foreach ($importacaoDados as $key => $value) {
										$value = mysql_real_escape_string($value);
										$pk .= " AND {$key} = '{$value}' ";
									}
								}
								$pk = trim($pk, 'AND');
								$sql = "SELECT * FROM {$r_tabela} {$joins} WHERE {$pk};";
								$pkCount = 0;
								foreach ($data as $key => $value) {
									// Padronização de valores
									$value = trim($value);
									if (preg_match('#^[0-9]+,[0-9]+$#', $value)) {
										$value = str_replace(',', '.', $value);
									} else if (preg_match('#^([0-9]+\.)+[0-9]+,[0-9]+$#', $value)) {
										$value = str_replace('.', '', $value);
										$value = str_replace(',', '.', $value);
									} else if (preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $value)) {
										$value = explode('/', $value);
										$value = "{$value[2]}-{$value[1]}-{$value[0]}";
									} else {
										if($functionPersonalizaLimparAcentos){
											// Só entra aqui se existir arquivo personalizado
											$value = strtoupper(_gLimpaAcentosImportarCSV($value));
										} else {
											$value = strtoupper(gLimpaAcentos($value));
										}
										//$value = strtoupper(gLimpaAcentos($value));
									}
									// fim padronização de valores
									if (isset($info[$key]) && isset($info[$key][1]) && $info[$key][1]) {
										if (empty($value)) {
											continue 2;
										}
			        					$sql = str_replace("[[PK{$pkCount}]]", $value, $sql);
			        					$pkCount++;
									}
								}
                                $sql = gSubstituirCamposJson($sql);
								$resCheck = mysqli_query($sql);
								if (!$resCheck) { continue; }
								if (mysql_numrows($resCheck)) {
									$r_tipo_both = 'update';
									$insertLinha = "UPDATE {$r_tabela} {$joins} SET {$infoSet} WHERE {$pk};";
								} else {
									$insertLinha = "INSERT INTO {$r_tabela} ({$infoCols}) VALUE ({$infoValues});";
								}
							} else {
								$insertLinha = $insert;
							}
			    			$cols = count($data);
			    			$pkCount = 0;

							foreach ($data as $key => $value) {
			    				// Padronização de valores
			    				$value = trim($value);
								if (preg_match('#^[0-9]+,[0-9]+$#', $value)) {
									$value = str_replace(',', '.', $value);
								} else if (preg_match('#^([0-9]+\.)+[0-9]+,[0-9]+$#', $value)) {
									$value = str_replace('.', '', $value);
									$value = str_replace(',', '.', $value);
								} else if (preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $value)) {
									$value = explode('/', $value);
									$value = "{$value[2]}-{$value[1]}-{$value[0]}";
								} else {
									if($functionPersonalizaLimparAcentos){
										// Só entra aqui se existir arquivo personalizado
										$value = strtoupper(_gLimpaAcentosImportarCSV($value));
									} else {
										$value = strtoupper(gLimpaAcentos($value));
									}
									//$value = strtoupper(gLimpaAcentos($value));
								}
								// fim padronização de valores
			    				// É FK! Tentar achar a chave primária
								$value = mysql_real_escape_string(utf8_encode($value));
								// Se for FK e não for PK
								if (isset($info[$key][2]) && !empty($info[$key][2]) && !(isset($info[$key]) && isset($info[$key][1]) && $info[$key][1])) {
									$campoFK = $info[$key][2];
									$tabelaFK = explode('_', $campoFK);
									$tabelaFK = $tabelaFK[0];
									$sql = "SELECT {$tabelaFK}_Id FROM {$tabelaFK} WHERE {$campoFK} = '{$value}';";
                                    $sql = gSubstituirCamposJson($sql);
									$resFK = mysqli_query($sql);
									if (!$resFK || mysql_num_rows($resFK) == 0) {
										// Se não existir, insere a FK
										$sql = "INSERT INTO {$tabelaFK} ({$campoFK}) VALUES ('{$value}');";
                                        $sql = gSubstituirCamposJson($sql);
										($triggerPhpAtivo) ? mysql_query($sql) : mysqli_query($sql);
										$value = mysql_insert_id();
									} else {
										$rowFK = mysql_fetch_array($resFK);
										$value = $rowFK[0];
									}
								}
								// conversões de campos
								//$cacheConversorCampos
								//SHOW COLUMNS FROM T002 WHERE `Field` = 'T002_Valor_Juros'
			        			// é a chave primária?
			        			if ($r_tipo == 'update' || $r_tipo_both == 'update') {
			        				if (isset($info[$key]) && isset($info[$key][1]) && $info[$key][1]) {
			        					if (empty($value)) {
											continue 2;
										}
										// Identificar chaves duplicadas
										//if (in_array($value, $chavesPrimariasDuplicadas[$pkCount]['Chaves'])) {
										//	$resposta['erros'] .= "PK Duplicada: {$chavesPrimariasDuplicadas[$pkCount]['Nome']} - {$value}\n<br />";
										//	$resposta['linhasErro']++;
										//	continue 2;
										//} else {
										//	$chavesPrimariasDuplicadas[$pkCount]['Chaves'][] = $value;
										//}
			        					$insertLinha = str_replace("[[PK{$pkCount}]]", $value, $insertLinha);
			        					$pkCount++;
									}
									$insertLinha = str_replace("[[{$key}]]", $value, $insertLinha);
								} else {
									$insertLinha = str_replace("[[{$key}]]", $value, $insertLinha);
								}
			    			}
			    			if ($r_simula == 'false') {
			    				$result = ($triggerPhpAtivo) ? mysql_query($insertLinha) : mysqli_query($insertLinha);
							}
			    			if (strpos($insertLinha, 'INSERT INTO') !== false && strlen($resposta['logInsert']) < 1000) {
			    				$resposta['logInsert'] .= $insertLinha . "\n<br />";
							} else if (strlen($resposta['logUpdate']) < 1000) {
			    				$resposta['logUpdate'] .= $insertLinha . "\n<br />";
							}
							if ($r_simula == 'false' && !$result) {
								if (strlen($resposta['erros']) < 5000) {
							    	$resposta['erros'] .= mysql_error() . ' - ' . $insertLinha . "<br />";
								} else {
									if (strlen($resposta['erros']) < 5100) {
										$resposta['erros'] .= '.';
									}
								}
							    $resposta['linhasErro']++;
							} else {
								$resposta['linhasOk']++;
							}
						}
						fclose($handle);
					}
				}
			}
		}

		// Executa SQL de trigger
		if (file_exists($arquivoTrigger)) {
			$arquivoTrigger = file_get_contents($arquivoTrigger);
			if (!empty($arquivoTrigger)) {
				if(strpos($arquivoTrigger, '<?php')!== false){
					$arquivoTrigger = str_replace('<?php','', $arquivoTrigger);
					eval("$arquivoTrigger");
				}else {
					$resposta['erros'] .= "\n<br/>Triggers executadas!";
					$arquivoTrigger = explode(';', $arquivoTrigger);
					foreach ($arquivoTrigger as $sql) {
						$sql = trim($sql);
						if (!empty($sql)) {
							mysqli_query($sql);
							//$resposta['erros'] .= "\n<br/>{$sql}";
						}
					}
				}		
			}
		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	/**
	* Salvar o arquivo com as referencias
	*/
	case 'salvar':
		$resposta = array('code' => true, 'data' => array());

		// Arquivo de configurações de importação e último arquivo enviado
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
        is_dir($dir) or mkdir($dir, 0777, true);
        //if(isset($r_acao_Id) && !empty($r_acao_Id) && isset($r_from) && !empty($r_from)){
        //	$r_idGridImp .= '-' . $r_from . '-' . $r_acao_Id;
        //}
		$arquivoConf = $dir . $r_idGridImp . '-conf';
		$arquivoImp = $dir . $r_idGridImp . '-arquivo';
		$arquivoTrigger = $dir . $r_idGridImp . '-trigger';
		// salvar arquivo e configurações
		$arquivoParaSalvar = $dir . $r_idGridImp . '-salvo-' . base64_encode($r_nome);
		$array = array(
			'nome' => $r_nome,
			'conf' => file_get_contents($arquivoConf),
			'arquivo' => base64_encode(file_get_contents($arquivoImp)),
			'trigger' => file_exists($arquivoTrigger) ? file_get_contents($arquivoTrigger) : ''
		);
		file_put_contents($arquivoParaSalvar, serialize($array));

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'contentRecuperar':
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
			array($content1, '/sistema/funcoes/importacaoDeDados/tabRecuperar/', '&idGridImp=' . urlencode($r_idGridImp)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tabRecuperar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/sistema/funcoes/importacaoDeDados/gridRecuperar/', 'Configurações Salvas', '&idGridImp=' . urlencode($r_idGridImp));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;

	 case 'gridRecuperar':
		$grid = array(
			array('colunaValor' => 'id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'excluir', 'colunaTitulo' => '', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'arquivos', 'colunaTitulo' => 'Nome', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'tabela', 'colunaTitulo' => 'Tabela', 'chavePrimaria' => true),
			//array('colunaValor' => 'file', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'time', 'colunaTitulo' => 'Data/Hora'),
		);
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		$geradoSql = '';
		$geradoDados = array();
		$arquivos = scandir("{$g['pathDados']}importacaoDeDadosViaGrid/");
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Nome');
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || strpos($arquivo, $r_idGridImp . '-salvo-') === false) { continue; }
			$array = unserialize(file_get_contents("{$g['pathDados']}importacaoDeDadosViaGrid/" . $arquivo));
			$excluir = '<span onclick="excluir(this);" title="Excluir" class="ui-icon ui-icon-circle-close"></span>';
			$geradoDados[] = array($arquivo, $excluir, strtoupper($array['nome']), date("d/m/Y H:i:s", filemtime("{$g['pathDados']}importacaoDeDadosViaGrid/" . $arquivo)));
		}
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var str = id[0].replace(/id-/, '');
					divSetarAcaoId('{$g['divIdParent']}', str, 'arquivoConf')
					divRefresh('{$g['divIdParent']}');
					fecharJanela('{$g['divId']}');
				});

				function excluir(elem) {
					var id = $(elem).closest('tr').attr('id').split('|');
					var str = id[0].replace(/id-/, '');
					var buttons = { 
						"Sim": function() { 
							$.ajax({
								url: '/sistema/funcoes/importacaoDeDados/excluir/',
								dataType: 'html',
								data: 'ajax=true&arquivo=' + str,
								success: function(data) {
									$(elem).closest('tr').remove();
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm("Atenção!", "Deseja excluir este item?", buttons)
				}
			} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;

	 case 'excluir':
		$arquivoInfo = "{$g['pathDados']}importacaoDeDadosViaGrid/{$r_arquivo}";
		if (!file_exists($arquivoInfo)) {
			die('Erro - arquivo não existe.');
		} else {
			unlink($arquivoInfo);
		}
	break;

	case 'formTrigger':
		global $g;

		$r_idGridImp = isset($r_triggerEnviarCSV['idGridImp']) ? $r_triggerEnviarCSV['idGridImp'] : $r_idGridImp;
		// carrega conteúdo (caso existir já)
		$dir = "{$g['pathDados']}importacaoDeDadosViaGrid/";
		$arquivoTrigger = $dir . $r_idGridImp . '-trigger';
		$SQL = '';
		if (file_exists($arquivoTrigger)) {
			$SQL = file_get_contents($arquivoTrigger);
		}

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
		$enviar_btnId = 'triggerEnviarCSVBTN';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/importacaoDeDados/formTrigger/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'SQL' => array('name' => 'SQL', 'reject' => true, 'label' => 'SQL', 'type' => 'textarea', 'uppercase' => false, 'length' => array(17,150), 'uppercase' => false, 'code' => 'php', 'codeModoSimples' => true, 'value' => $SQL),
					'idGridImp' => array('name' => 'idGridImp', 'reject' => true, 'label' => 'idGridImp', 'type' => 'hidden', 'uppercase' => false, 'length' => array(17,150), 'value' => $r_idGridImp),
				),
			)
		);
        $form = new Formulario('triggerEnviarCSV');
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
				$sql = $form->campoValorEnviado('SQL');
				file_put_contents($arquivoTrigger, $sql);
				// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
				$alert .= gGeraAlertMsg('Operação OK', 'Arquivo enviado com sucesso!', false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						//divRefresh('{$g['divIdParent']}', true);
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
		$botoesDir .= '<button type="submit" id="triggerEnviarCSVBTN">Enviar</button>';
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
