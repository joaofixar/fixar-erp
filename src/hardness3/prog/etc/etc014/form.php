<?php
namespace hardness;
require_once('prog/cad/cad001/form_func-js.php');
	
/**
* Ações
*/
switch ($g['r'][3]) {			
	case 'etc014FormSalvar':
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
		$form_tabela = 'D132';
		$form_campo_pk = 'D132_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc014FormSalvarFromBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc014/form/etc014FormSalvar/';

		// novo formulário
		$form = new Formulario('etc014FormSalvarForm');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D132_Descricao' => array('name' => 'D132_Descricao', 'type' => 'text', 'label' => 'Descrição', 'length' => array(40,70)),
					'D132_Salvo' => array('name' => 'D132_Salvo', 'type' => 'hidden', 'uppercase' => false),
					'D132_Filtro' => array('name' => 'D132_Filtro', 'type' => 'hidden', 'uppercase' => false),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'fato' => array('name' => 'fato', 'type' => 'hidden', 'value' => @$r_fato, 'reject' => true),
					'fato2' => array('name' => 'fato2', 'type' => 'hidden', 'value' => @$r_fato2, 'reject' => true),
					'dimensao1' => array('name' => 'dimensao1', 'type' => 'hidden', 'value' => @$r_dimensao1, 'reject' => true),
					'dimensao1ord' => array('name' => 'dimensao1ord', 'type' => 'hidden', 'value' => @$r_dimensao1ord, 'reject' => true),
					'dimensao2' => array('name' => 'dimensao2', 'type' => 'hidden', 'value' => @$r_dimensao2, 'reject' => true),
					'dimensao2ord' => array('name' => 'dimensao2ord', 'type' => 'hidden', 'value' => @$r_dimensao2ord, 'reject' => true),
					'medida' => array('name' => 'medida', 'type' => 'hidden', 'value' => @$r_medida, 'reject' => true),
					'incluirLinhaVazia' => array('name' => 'incluirLinhaVazia', 'type' => 'hidden', 'value' => @$r_incluirLinhaVazia, 'reject' => true),
					'incluirColunaVazia' => array('name' => 'incluirColunaVazia', 'type' => 'hidden', 'value' => @$r_incluirColunaVazia, 'reject' => true),
					'mostrarCurvaABC' => array('name' => 'mostrarCurvaABC', 'type' => 'hidden', 'value' => @$r_mostrarCurvaABC, 'reject' => true),
					'tipodata' => array('name' => 'tipodata', 'type' => 'hidden', 'value' => @$r_tipodata, 'reject' => true),
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
									divRefresh('{$form->campoValorEnviado('divReload')}', true);
									fecharJanela('{$g['divId']}');
								} </script></div>
EOT;
						}
				} else {
					// Insert
					// Salva dados do Relatório
					$array = array(
						'fato' => $form->campoValorEnviado('fato'),
						'fato2' => $form->campoValorEnviado('fato2'),
						'dimensao1' => $form->campoValorEnviado('dimensao1'),
						'dimensao1ord' => $form->campoValorEnviado('dimensao1ord'),
						'dimensao2' => $form->campoValorEnviado('dimensao2'),
						'dimensao2ord' => $form->campoValorEnviado('dimensao2ord'),
						'medida' => $form->campoValorEnviado('medida'),
						'incluirLinhaVazia' => $form->campoValorEnviado('incluirLinhaVazia'),
						'incluirColunaVazia' => $form->campoValorEnviado('incluirColunaVazia'),
						'mostrarCurvaABC' => $form->campoValorEnviado('mostrarCurvaABC'),
						'tipodata' => $form->campoValorEnviado('tipodata'),
					);
					$form->campoSetarValor('D132_Salvo', mysql_real_escape_string(serialize($array)), true);
					// Salva o filtro do Relatório
					$id = md5('/etc/etc014/grid/etc014GridPivot/');
					$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
					$file = $dir . $id;
					if (file_exists($file)) {
						$form->campoSetarValor('D132_Filtro', mysql_real_escape_string(file_get_contents($file)), true);
					}
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							fecharJanela('{$g['divId']}');
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
		$g['smarty']->display('form.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="etc014FormSalvarFromBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
		
}
?>
