<?php
namespace hardness;

set_time_limit(0);

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
			array($content1, '/sistema/funcoes/editarRegistro/tab/', '&tabelas=' . urlencode($r_tabelas))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'tab':
		$tabelas = unserialize(base64_decode($r_tabelas));
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		foreach ($tabelas as $tabela => $id) {
			$tabsList[] = array(uniqid(), '/sistema/funcoes/editarRegistro/form/', 'Editar registro - ' . $tabela.': '. $id, '&tab=geral&tabela=' . urlencode($tabela) . '&acaoId=' . urlencode($id));
		}
		
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'form':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = $r_tabela;
		$form_campo_pk = $r_tabela.'_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarRegistroBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarRegistro/form/';

		// Definição do Formulário
		$formArray = array();

		$sqlColumns = mysql_query("SHOW COLUMNS FROM {$r_tabela}");
		while($rowColumns = mysql_fetch_array($sqlColumns)){
			$formArray['campos']['geral'][$rowColumns['Field']] = array('name' => $rowColumns['Field'], 'label' => $rowColumns['Field'], 'type' => 'text', 'length' => array(100, 100), 'style' => 'margin-bottom: 2px;', 'extra' => ($rowColumns['Field'] == $form_campo_pk) ? '' : 'class="travadoEdicao"', 'uppercase' => false, 'readonly' => ($rowColumns['Field'] == $form_campo_pk) ? true : false);
			if($rowColumns['Field'] == $form_campo_pk) { continue; }
			$formArray['campos']['geral']['__'.$rowColumns['Field']] = array('name' => '__'.$rowColumns['Field'], 'type' => 'hidden', 'reject' => true, 'value' => '0');
		}
		$form = new Formulario('form-editarRegistro');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela, true)) {
			// Form sem campos habilitados
			if ($formCodigo == 3) {
				echo gGeraAlertMsg('Erro', 'Nenhum campo do formulário foi habilitado para edição!');
				echo <<<EOT
	                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                        divRefresh('{$g['divId']}', true);
                    } </script></div>
EOT;
			// Form com erros
			} else if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form, false, false)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
	                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$g['divId']}', true);
                            } </script></div>
EOT;
                    }
    	        } else {
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        echo gGeraAlertMsg('Operação OK', 'Registro Inserido com Sucesso!');
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, false, false, false);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesEsq .= "<button onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Auditoria de inclusão/alteração/exclusão de registros', '/etc/etc007/content/etc007ContentAuditoria/', '&tabela=' + encodeURIComponent('{$r_tabela}') + '&id=' + encodeURIComponent('{$r_acaoId}'));\">Auditoria</button>";
		$botoesDir .= '<button type="submit" id="editarRegistroBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.travadoEdicao').each(function(i, elem){
					$(elem).prop('readonly', true).css('background-color', 'var(--fundo-campo-form-readonly)');
					var id = $(elem).attr('id');
					$(elem).parent().append('<button style="float: right; height: 20px; paddin-top: 0px" onclick="if($(\'#'+id+'\').prop(\'readonly\') !== true) { $(\'#'+id+'\').prop(\'readonly\', true).css(\'background-color\', \'var(--fundo-campo-form-readonly\'); $(\'#__'+id+'\').val(\'0\'); } else { $(\'#'+id+'\').prop(\'readonly\', false).css(\'background-color\', \'var(--fundo-campo-form)\'); $(\'#__'+id+'\').val(\'1\'); } return false;">Habilitar</button>');
					setTimeout(function(){
						$(elem).parent().next().find('.ui-button-text').css('padding', '0px');
					}, 10);
				});
			} </script></div>
EOT;
	break;
}

function retornaTipoCampo($dataType){
	preg_match('#([a-z]+)\(([0-9]+)\)|.+#', $dataType, $match);
	echo $dataType;
	print_r($match);
	echo "<br>";

	switch ($dataType) {
		case 'date':
			$type = 'text';
			$size = '10';			
		break;
		case 'varchar':
			$type = 'text';
			$size = '10';			
		break;
		
		default:
			$type = 'text';
		break;
	}
	return array('type' => $type, 'size' => $size, 'decimals' => $decimals);
}
