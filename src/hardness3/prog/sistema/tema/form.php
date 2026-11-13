<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'temaFormAdd':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D087';
		$form_campo_pk = 'D087_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001FormDocumentosBtnAdd', 'cad001FormDocumentosBtnCln', 'cad001FormDocumentosBtnNew');
	    // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/tema/form/temaFormAdd/';
		
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad001FormDocumentos');
		$formArray['campos']['geral']['nome'] 					= array('name' => 'nome', 'label'=>'Nome do Tema', 'type' => 'text', 'uppercase' => false, 'value' => 'empresa', 'readonly' => true);
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['cad001FormDocumentosBtnNew'] = array('name' => 'cad001FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		//$formArray['campos']['geral']['cad001FormDocumentosBtnCln'] = array('name' => 'cad001FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001FormDocumentosBtnAdd'] = array('name' => 'cad001FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
				// Insert
				// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
				$arquivo = $form->campoValorEnviado('uploadArquivo');
				$nome = $form->campoValorEnviado('nome');
				if (empty($nome)) {
					echo gGeraAlertMsg('Erro', 'Nome deve ser preenchido.');
				} else {
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						$file = '/tmp/tema.zip';
						$dir = '/tmp/tema';
						@unlink($file);
						shell_exec("rm -Rf {$dir}");
						move_uploaded_file($arquivo['tmp_name'], $file);
						shell_exec("unzip {$file} -d {$dir}/");
						shell_exec("mkdir static/css/temas/{$nome}");
						shell_exec("mkdir static/css/temas/{$nome}/images/");
						@unlink("static/css/temas/{$nome}/jqueryui.css");
						$findCss = scandir("{$dir}/css");
						$findCss = "{$dir}/css/{$findCss[2]}/";
						$findCss_ = scandir($findCss);
						$findImages = $findCss . 'images/';
						$findCss = $findCss . $findCss_[3];
						$findCssContent = file_get_contents($findCss);
						$findCssContent = str_replace('images/', "/hardness3/static/css/temas/{$nome}/images/", $findCssContent);
						$h = fopen("static/css/temas/{$nome}/jqueryui.css", 'w');
						fwrite($h, $findCssContent);
						fclose($h);
						shell_exec("chmod 777 static/css/temas/{$nome}/jqueryui.css");
						//copy("static/css/temas/padrao/tema.css", "static/css/temas/{$nome}/tema.css");
						shell_exec("rsync -av {$findImages}* static/css/temas/{$nome}/images/");
						echo gGeraAlertMsg('Ok', 'Ok');
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');		
	break;
}
?>
