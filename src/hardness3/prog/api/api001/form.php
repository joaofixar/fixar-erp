<?php
namespace hardness;
/**
 * Raquel
 * 21/03/2018
 */
require_once("bibliotecas/classes/API001.php");
$API001 = new API001();
require_once("bibliotecas/classes/API001PolicyAdmin.php");
$API001PolicyAdmin = new API001PolicyAdmin();
/**
 * Ações
 */
switch ($g['r'][3]) {

	case 'API001FormPolicyAdmin':
		try {
			$API001PolicyAdmin->assertAuthorized();
			$API001PolicyAdmin->resetRestApiUnlock();
		} catch (\Throwable $throwable) {
			echo gGeraAlertMsg('Acesso negado', $throwable->getMessage());
			break;
		}

		$containerId = uniqid('api001PolicyAdmin_');
		$empresaAtual = urlencode((string) ($g['empresaAtual'] ?? 1));
		$usuarioAtual = urlencode((string) ($g['usuarioAtual'] ?? 0));
		$dbAtual = urlencode((string) ($confUsuario['dbDatabase'] ?? ''));
		$baseAjax = "/api/api001/outro/%s/?ajax=true&noEmp=true&p={$empresaAtual}&u={$usuarioAtual}&db={$dbAtual}";
		$accessStatusUrl = sprintf($baseAjax, 'API001PolicyAccessStatus');
		$accessUnlockUrl = sprintf($baseAjax, 'API001PolicyUnlock');
		$statusUrl = sprintf($baseAjax, 'API001PolicyStatus');
		$loadTableUrl = sprintf($baseAjax, 'API001PolicyLoadTable');
		$previewUrl = sprintf($baseAjax, 'API001PolicyPreview');
		$saveUrl = sprintf($baseAjax, 'API001PolicySave');
		$removeUrl = sprintf($baseAjax, 'API001PolicyRemoveTable');
		$tokenStatusUrl = sprintf($baseAjax, 'API001TokenStatus');
		$tokenCreateUrl = sprintf($baseAjax, 'API001TokenCreate');
		$tokenSetStatusUrl = sprintf($baseAjax, 'API001TokenSetStatus');
		$docsUrl = '/api/docs';
		$hostAtual = strtolower(trim((string) ($_SERVER['HTTP_HOST'] ?? '')));
		$hostAtual = preg_replace('/:\d+$/', '', $hostAtual) ?? '';
		$isLocalhostAccess = in_array($hostAtual, array('localhost', '127.0.0.1', '::1'), true);

		echo \RestApi\Admin\PolicyAdminView::render(array(
			'container_id' => $containerId,
			'access_status_url' => $accessStatusUrl,
			'access_unlock_url' => $accessUnlockUrl,
			'status_url' => $statusUrl,
			'load_table_url' => $loadTableUrl,
			'preview_url' => $previewUrl,
			'save_url' => $saveUrl,
			'remove_url' => $removeUrl,
			'token_status_url' => $tokenStatusUrl,
			'token_create_url' => $tokenCreateUrl,
			'token_set_status_url' => $tokenSetStatusUrl,
			'docs_url' => $docsUrl,
			'is_localhost_access' => $isLocalhostAccess ? '1' : '0',
		));
	break;

	case 'API001FormAPI':
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'API001FormDadosCliente_btn';
		// Form que será executado ao clicar no botãoAPI001FormAPI
		$enviar_acaoHref = '/api/api001/form/API001FormAPI/';

		// novo formulário
		$form = new Formulario('biFormDadosCliente');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Data_Inicio' => array('name' => 'Data_Inicio','label'=>'* Data Inicial', 'type' => 'data', 'length' => array(15,60)),
					'Data_Fim' => array('name' => 'Data_Fim','label'=>'* Data Final', 'type' => 'data', 'length' => array(15,60)),
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
				$cArquivo = $API001->exportarAPI001($form);
				if (!file_exists($cArquivo)) {
					echo gGeraAlertMsg('Erro', $cArquivo);
				}else{
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							window.location = '/api/api001/exportar/API001exportarBI/?ajax=true&cArquivo=' + encodeURIComponent('$cArquivo');
						} </script></div>
EOT;
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="API001FormDadosCliente_btn">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'API001FormConfApi':
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
		$form_tabela = 'C033';
		$form_campo_pk = 'C033_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'API001FormConfApiBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/api/api001/form/API001FormConfApi/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"C033_Nome":{"top":7,"left":29.015625,"width":568,"hidden":0},"C033_D024_Id":{"top":30,"left":15.4375,"width":554,"hidden":0},"C033_Login":{"top":53,"left":31.5,"width":569,"hidden":0},"C033_Senha":{"top":76,"left":28.140625,"width":568,"hidden":0},"C033_Token":{"top":99,"left":28.9375,"height":85,"width":568,"hidden":0},"C033_Metodo":{"top":191,"left":20.9375,"width":568,"hidden":0},"C033_Url":{"top":214,"left":45.078125,"width":568,"hidden":0}}',
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
		        	'C033_Nome' => array('name' => 'C033_Nome', 'label'=>'Nome', 'type' => 'text'),
					'C033_D024_Id' => array('name' => 'C033_D024_Id', 'label'=> 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
		        	'C033_Login' => array('name' => 'C033_Login', 'label'=>'Login', 'type' => 'text','uppercase' => false),
		        	'C033_Senha' => array('name' => 'C033_Senha', 'label'=>'Senha', 'type' => 'text', 'uppercase' => false),
		        	'C033_Token' => array('name' => 'C033_Token', 'label'=>'Token', 'type' => 'textarea', 'uppercase' => false),
		        	'C033_Url' => array('name' => 'C033_Url', 'label'=>'Url', 'type' => 'text','length' => array(20,200), 'uppercase' => false),
		        	'C033_Metodo' => array('name' => 'C033_Metodo', 'label'=>'Método', 'type' => 'text','length' => array(10,20), 'uppercase' => false),
				)
			)
		);
        $form = new Formulario('form-abc001FormNome');
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
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
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
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="API001FormConfApiBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'API001FormApiProcessos':
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
		$form_tabela = 'C037';
		$form_campo_pk = 'C037_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'API001FormApiProcessosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/api/api001/form/API001FormApiProcessos/';
		
		//form editor
		$form_editor = array(
			'geral' => '{"C037_Nome":{"top":7,"left":17.015625,"width":1111,"hidden":0},"C037_Codigo":{"top":33,"left":21.203125,"height":412,"width":1111,"hidden":0}}',
		);

		// $D024_Id $D024_Nome_Empresa $D024_Endereco $T005_Id $T005_Data_Emissao $T005_Valor_Total $Teste $Achou 

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'C037_C033_Id' => array('name' => 'C037_C033_Id', 'label'=> 'API', 'value' => "$r_C033_Id", 'type' => 'hidden'),
					'C037_Nome' => array('name' => 'C037_Nome', 'label'=>'Nome', 'type' => 'text'),
					'C037_Codigo' => array('name' => 'Código', 'label' => 'Valor', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php'),
				)
			)
		);
		$form = new Formulario('form-abc001FormNome');
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
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="API001FormApiProcessosBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$botoesEsq .= '<button id="API001ProcessosExecutar" class="btnFechar">Executar</button>';

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#API001ProcessosExecutar').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Resultado', '/api/api001/outro/API001ExecutarProcesso/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&C033_Id=' + encodeURIComponent('{$r_C033_Id}'), [900,500]);
				});
			} </script></div>
EOT;

		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'API001FormApiCallback':
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
		$form_tabela = 'C038';
		$form_campo_pk = 'C038_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'API001FormApiCallbackBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/api/api001/form/API001FormApiCallback/';
		
		//form editor
		$form_editor = array(
			'geral' => '{"C038_Nome":{"top":7,"left":17.015625,"width":1111,"hidden":0},"C038_Codigo":{"top":33,"left":21.203125,"height":412,"width":1111,"hidden":0}}',
		);

		// $D024_Id $D024_Nome_Empresa $D024_Endereco $T005_Id $T005_Data_Emissao $T005_Valor_Total $Teste $Achou 

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'C038_C033_Id' => array('name' => 'C038_C033_Id', 'label'=> 'API', 'value' => "$r_C033_Id", 'type' => 'hidden'),
					'C038_Nome' => array('name' => 'C038_Nome', 'label'=>'Nome', 'type' => 'text'),
					'C038_Codigo' => array('name' => 'Código', 'label' => 'Valor', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php'),
				)
			)
		);
		$form = new Formulario('form-abc001FormNome');
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
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="API001FormApiCallbackBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$botoesEsq .= '<button id="API001CallbackExecutar" class="btnFechar">Executar</button>';

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#API001CallbackExecutar').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Resultado', '/api/api001/outro/API001ExecutarProcesso/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&C033_Id=' + encodeURIComponent('{$r_C033_Id}'), [900,500]);
				});
			} </script></div>
EOT;

		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

}

?>
