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
			array($content1, '/sistema/funcoes/expImpPersonalizacao/tab/', '')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'tab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/expImpPersonalizacao/exportar/', 'Exportar', '', false, true),
			array(uniqid(), '/sistema/funcoes/expImpPersonalizacao/importar/', 'Importar', '&tab=geral', false, true)

		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'exportar':
		// dir & files
		$dir = "{$g['pathDados']}tmp/";
        is_dir($dir) or mkdir($dir, 0777, true);
        $arquivoReltivo = $dir . 'personalizacao.tar.gz';
        $arquivoPath = $confUsuario['pathRaiz'] . $arquivoReltivo;
        
        // compacta as personalizações para o arquivo
        $cmd = "cd {$confUsuario['pathRaiz']}{$g['pathDados']} && tar cvf - personalizacao* | gzip -9c > {$arquivoPath}";
        echo shell_exec($cmd);
        
        // html
		echo '<div style="margin: 10px; font-size: 1.1em;">';
		echo "Arquivo contendo todas as personalizações (Configurações, Form, Grid e Tab).<br /><br />";
		echo '<a target="_blank" href="/hardness3/' . $arquivoReltivo . '"><button>Baixar Arquivo</button></a>';
		echo '</div>';
	break;
	
	case 'importar':
		// segurança extra
		if (!isset($g['usuarioAtual']) || $g['usuarioAtual'] != 63) { die(); }
		
		// dir & files
		$dir = "{$g['pathDados']}tmp/";
        is_dir($dir) or mkdir($dir, 0777, true);
        $arquivoReltivo = $dir . 'personalizacao.tar.gz';
        $arquivoPath = $confUsuario['pathRaiz'] . $arquivoReltivo;
        
        // html
		echo '<div style="margin: 10px; font-size: 1.4em;">';
		echo '<span style="font-size: 1.6em; color: red; font-weight: bold;">!!! Atenção !!!</span><br />';
		echo "Este processo irá <b>SUBSTITUIR</b> todos os arquivos de <b>PERSONALIZAÇÕES</b> pelas enviadas no arquivo!<br />";
		echo '</div>';
		
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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'impPersonalizacaoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/expImpPersonalizacao/importar/';
		
		$form = new Formulario('impPersonalizacaoForm');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'uploadArquivo' => array('name' => 'uploadArquivo', 'reject' => true, 'label' => 'Upload Logomarca', 'type' => 'file', 'length' => array(40,80)),
				),
			),
		);	
		$formArray['campos']['geral']['impPersonalizacaoBtn'] = array('name' => 'impPersonalizacaoBtn', 'type'=>'button', 'reload' => false, 'value' => 'Importar!', 'align' => 'right', 'reject' => true);		
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
				$arquivo = $form->campoValorEnviado('uploadArquivo');
				if (!empty($arquivo['name']) && $arquivo['error'] == UPLOAD_ERR_OK) {
					// arquivos
					move_uploaded_file($arquivo['tmp_name'], $arquivoPath);
					$cmd = "cd {$confUsuario['pathRaiz']}{$g['pathDados']} && tar -zxvf {$arquivoPath}"; // rm -Rf personalizacaoConf && rm -Rf personalizacaoForm && rm -Rf personalizacaoGrid && rm -Rf personalizacaoTab &&
        			$log = shell_exec($cmd);
        			$log = str_replace("\n", "&#10;", $log);
        			// dump
        			$arquivo = "{$confUsuario['pathRaiz']}{$g['pathDados']}dump.sql";
        			if (file_exists($arquivo)) {
        				$dump = file_get_contents($arquivo);
        				$log .= '\nSQL Executadas:\n' . $dump;
        				mysql_query($dump);
        				unlink($arquivo);
        			}
        			// log
        			echo gGeraAlertMsg('Log da importação - Arquivos copiados', "<textarea style='width: 1000px; height: 450px;'>{$log}</textarea>");
				} else {
					echo gGeraAlertMsg('Erro', "Erro enviando arquivo!");
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
}
?>