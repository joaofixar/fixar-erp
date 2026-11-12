<?php
namespace hardness;

use Exception;

require_once('prog/cad/cad001/form_func-js.php');
	
/**
* Ações
*/
switch ($g['r'][3]) {			
	case 'etc011FormSalvar':
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
		$enviar_btnId = 'etc011FormSalvarFromBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc011/form/etc011FormSalvar/';

		// novo formulário
		$form = new Formulario('etc011FormSalvarForm');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D132_Descricao' => array('name' => 'D132_Descricao', 'type' => 'text', 'label' => 'Descrição', 'length' => array(40,70)),
					'D132_Salvo' => array('name' => 'D132_Salvo', 'type' => 'hidden', 'uppercase' => false),
					'D132_Filtro' => array('name' => 'D132_Filtro', 'type' => 'hidden', 'uppercase' => false),
					/*'fato' => array('name' => 'fato', 'type' => 'hidden', 'value' => @$r_fato, 'reject' => true),
					'fato2' => array('name' => 'fato2', 'type' => 'hidden', 'value' => @$r_fato2, 'reject' => true),
					'dimensao1' => array('name' => 'dimensao1', 'type' => 'hidden', 'value' => @$r_dimensao1, 'reject' => true),
					'dimensao1ord' => array('name' => 'dimensao1ord', 'type' => 'hidden', 'value' => @$r_dimensao1ord, 'reject' => true),
					'dimensao2' => array('name' => 'dimensao2', 'type' => 'hidden', 'value' => @$r_dimensao2, 'reject' => true),
					'dimensao2ord' => array('name' => 'dimensao2ord', 'type' => 'hidden', 'value' => @$r_dimensao2ord, 'reject' => true),
					'medida' => array('name' => 'medida', 'type' => 'hidden', 'value' => @$r_medida, 'reject' => true),
					'incluirLinhaVazia' => array('name' => 'incluirLinhaVazia', 'type' => 'hidden', 'value' => @$r_incluirLinhaVazia, 'reject' => true),
					'incluirColunaVazia' => array('name' => 'incluirColunaVazia', 'type' => 'hidden', 'value' => @$r_incluirColunaVazia, 'reject' => true),
					'mostrarCurvaABC' => array('name' => 'mostrarCurvaABC', 'type' => 'hidden', 'value' => @$r_mostrarCurvaABC, 'reject' => true),
					'exibir' => array('name' => 'exibir', 'type' => 'hidden', 'value' => @$r_exibir, 'reject' => true),
					'tipodata' => array('name' => 'tipodata', 'type' => 'hidden', 'value' => @$r_tipodata, 'reject' => true),*/
				)
			)
		);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Salva dados do Relatório
			$array = array(
				'fato' => $r_fato,
				'fato2' => $r_fato2,
				'dimensao1' => $r_dimensao1,
				'dimensao1ord' => $r_dimensao1ord,
				'dimensao2' => $r_dimensao2,
				'dimensao2ord' => $r_dimensao2ord,
				'medida' => $r_medida,
				'incluirLinhaVazia' => $r_incluirLinhaVazia,
				'incluirColunaVazia' => $r_incluirColunaVazia,
				'mostrarCurvaABC' => $r_mostrarCurvaABC,
				'ocultarColunasDashboard' => $r_ocultarColunasDashboard,
				'tipodata' => $r_tipodata,
				'exibir' => $r_exibir,
				'incluirGraficoLabel' => $r_incluirGraficoLabel,
				'incluirGraficoLegend' => $r_incluirGraficoLegend,
				'numMetricas' => $r_numMetricas,
				'ocultarSempreColunas' => $r_ocultarSempreColunas,
				'ocultarPorcentagem' => $r_ocultarPorcentagem,
				'ocultarTotais' => $r_ocultarTotais,
			);
			for ($i = 1; $i < 10; $i++) {
				$var = 'r_fatoa' . $i;
				if (isset($$var)) {
					$array['fatoa' . $i] = $$var;
					$var = 'r_fato2a' . $i;
					$array['fato2a' . $i] = $$var;
					$var = 'r_tipodataa' . $i;
					$array['tipodataa' . $i] = $$var;
					$var = 'r_medidaa' . $i;
					$array['medidaa' . $i] = $$var;
				}
			}
			$form->campoSetarValor('D132_Salvo', serialize($array), true);
			// Salva o filtro do Relatório
			$id = $r_md5;
			$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
			$file = $dir . $id;
			if (file_exists($file)) {
				$form->campoSetarValor('D132_Filtro', file_get_contents($file), true);
			}

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
							$sql = "UPDATE D182 SET D182_Grid_Salvo = NULL WHERE D132_Id = '{$acaoId}' AND D182_C007_Id = '{$g['usuarioAtual']}'";
							mysql_query($sql);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									fecharJanela('{$g['divId']}');
								} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						
						echo gGeraAlertMsg('Salvo!', 'O relatório/gráfico foi salvo.');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		$botoesDir .= '<button type="submit" id="etc011FormSalvarFromBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'etc011IncluirIndicador':
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
		$enviar_btnId = 'etc011IndicadorBIbtn'; //etc011IndicadorBIbtn
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc011/form/etc011IncluirIndicador/';
		// Editor
		$form_editor = array(
			'geral' => '{"D132_Descricao":{"top":7,"left":13.015625,"width":366,"hidden":0},"D132_Salvo":{"top":33,"left":11.859375,"height":448,"width":824,"hidden":0}}'
		);

		$codigoCallback = '';
		if (empty($r_acaoId)) {
			$codigoCallback = <<<'EOT'
<?php
// $extra
return array(
	'label' => 'Total Mês',
	'valor' => 'R$ 1.000,00',
	'classe' => 'padrao' 
);
EOT;


		}

		// Novo Formulário
		$form = new Formulario('etc011IndicadorBI');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D132_Descricao' => array('name' => 'D132_Descricao','label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),
					'D132_Salvo' => array('name' => 'D132_Salvo', 'label' => 'Código', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'value' => $codigoCallback),
					'D132_Tipo' => array('name' => 'D132_Tipo','label' => 'Tipo', 'type' => 'hidden', 'value' => 2),
                    //'D132_Filtro' => array('name' => 'D132_Filtro','label' => 'Tipo', 'type' => 'hidden', 'value' => ''),
                    //'D132_Grupo' => array('name' => 'D132_Grupo','label' => 'Tipo', 'type' => 'hidden', 'value' => 1),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		    E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
                $codigoPHP = trim($form->campoValorEnviado('D132_Salvo'));
                // Testa o código
                try {
                    $codigo = $codigoPHP;
					$codigo = str_replace('<?php', '', $codigo);
                    eval($codigo);
                } catch (Exception $e) {
                    echo gGeraAlertMsg('Erro PHP', 'Erro');
                    die();
                }
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$acaoId = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad010/content/cad010content03/', '&acaoId=' + encodeURIComponent('{$acaoId}'),'[900,510]');
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="etc011IndicadorBIbtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
		
}
