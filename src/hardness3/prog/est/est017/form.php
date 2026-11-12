<?php
namespace hardness;
/**
 * Ações
 */
require_once('prog/est/est017/form_func-js.php');
switch ($g['r'][3]) {
	case 'est017FormProdutoProducao':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
		$form_tabela = 'T256';
		$form_campo_pk = 'T256_Id';

		$form_parent = 'T255_Id';
        $form_parent_valor = $r_T255_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est017FormProdutoProducao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est017/form/est017FormProdutoProducao/';

		$pesquisaGridExtra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D041 ON D041_D049_Id=D049_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D037 ON D037_Id=D001_D037_Id
			WHERE (D009_C004_Id = '{$g['empresaAtual']}'
			AND D001_Flag_Ativo='S')
			GROUP BY D009_Id
			ORDER BY D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																											// tabela
			'D009_Id',																										// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido,D009_Id',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Produto',																							// título da janela
			'Código,Descrição,Marca,Estoque',														// descrição dos campos
			'est017CampoProcuraProdutoOP',																					// callback js
			$g['divId'],																									// divId
			'',
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right'),
				array('colunaExibir' => false)
			)
		);

		$form_editor = array('geral' => '{"T256_D009_Id":{"top":7,"left":28.296875,"width":266,"hidden":0},"T256_Quantidade":{"top":30,"left":10.984375,"width":86,"hidden":0}}');
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est017FormProdutoProducao');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T256_D009_Id' => array('name' => 'T256_D009_Id' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(40,110)),
					'T256_Quantidade' => array('name' => 'T256_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(10,255)),
					'T256_T255_Id' => array('name' => 'T256_T255_Id', 'type'=>'hidden', 'value'=> @$r_T255_Id),
					'T256_Quantidade_Saldo' => array('name' => 'T256_Quantidade_Saldo', 'label' => 'Saldo','type'=>'hidden'),
               	),
            )
		);
		$form->definirFormularioArray($formArray);

		if(!empty($acaoId)){
			$sqlD009 = mysql_query("SELECT CONCAT(D001_Codigo_Produto, ' - ', D001_Descricao_Produto) as Produto FROM D001 LEFT JOIN D049 ON D001_Id=D049_D001_Id LEFT JOIN D009 ON D049_Id=D009_D049_Id LEFT JOIN T256 ON T256_D009_Id=D009_Id WHERE T256_Id = '{$acaoId}'");
			$D009 = mysql_fetch_array($sqlD009);
			$D009['Produto'] = mysql_real_escape_string($D009['Produto']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T256_D009_Id_Pesquisa').val('{$D009['Produto']}');
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
					$T256_Quantidade = $form->campoValorEnviado('T256_Quantidade');
					$form->campoSetarValor('T256_Quantidade_Saldo', gCorrigeNumeroInverte($T256_Quantidade), true);

					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T256_Id = $g['mysqlLastId'];
						$T255_Id = $form->campoValorEnviado('T256_T255_Id');
						mysql_query("insert into 
										T256A 
										(
											T256A_T256_Id,
										 	T256A_T256_Id_Op_Pai,
											T256A_Data_Inicio
										) 
										values 
										(
											'{$T256_Id}',
											'0',
											current_date()
										)");

						require_once('bibliotecas/classes/EST017.php');
						$EST017 = new EST017();
						$retorno = $EST017->processarEngenhariaProduto($T256_Id);

						$EST017->gerarOrdemProducaoComposicao($T255_Id, $T256_Id, 0);

						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								console.log('{$g['divIdParent']}');
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 200);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
		$botoesDir .= '<button type="submit" id="est017FormProdutoProducao_btn">'.$labelButton.'</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est017FormEntradaProducao':
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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

		$form_editor = array(
			'geral' => '{"T257_Quantidade":{"top":7,"left":18,"width":184,"hidden":0},"est017FormEntradaProducao_Btn":{"top":5,"left":288,"width":null,"hidden":0}}'
		);

        // A qual tabela este form está relacionada, e a chave primária da tabela
        $form_tabela = 'T257';
        $form_campo_pk = 'T257_Id';
        $form_parent = 'T256_Id';
        $form_parent_valor = $r_T256_Id;
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'est017FormEntradaProducao_Btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/est/est017/form/est017FormEntradaProducao/';

        $form = new Formulario('est016FormCancelarPedidoForm');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T257_T256_Id' => array('name' => 'T257_T256_Id', 'type' => 'hidden', 'value' => @$r_T256_Id),
					'T257_C007_Id' => array('name' => 'T257_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']),
					'T257_T066_Id' => array('name' => 'T257_T066_Id', 'type' => 'hidden', 'value' => ''),
					'T257_Data_Producao' => array('name' => 'T257_Data_Producao', 'type' => 'hidden', 'value' => date('Y-m-d')),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject' => true),
					'T257_Quantidade' => array('name' => 'T257_Quantidade', 'label'=>'Quantidade', 'type' => 'moeda', 'length' => array(14,12), 'casasDecimais' => '3'),
					'est017FormEntradaProducao_Btn' => array('name' => 'est017FormEntradaProducao_Btn', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
				)
			)
		);

        $form->definirFormularioArray($formArray);

        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
        *            E carregamento dos valores (se não foi enviado e possui acaoId)
        */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
            // Form sem erros
            } else if ($formCodigo == 2) {
                // Insert ou Update?
                if (empty($r_acaoId)) {
					$sql = mysql_query("SELECT T256_Quantidade_Saldo, T256_D009_Id FROM T256 WHERE T256_Id = '{$form->campoValorEnviado('T257_T256_Id')}'");
                	$row = mysql_fetch_array($sql);
                	if(gCorrigeNumeroInverte($form->campoValorEnviado('T257_Quantidade')) <= $row['T256_Quantidade_Saldo']){

						require_once('bibliotecas/classes/EST017.php');
						$EST017 = new EST017();
						$T256_Id = $form->campoValorEnviado('T257_T256_Id');
						$T257_Quantidade = gCorrigeNumeroInverte($form->campoValorEnviado('T257_Quantidade'));
						$parametrosGatilho = array($T256_Id, $T257_Quantidade);
						$erroGatilhoValidacao = $EST017->gatilhoValidacaoInsertT257($parametrosGatilho);

						if(empty($erroGatilhoValidacao)){
							$sqlLocacao = "";
							$paramlocacaoPadraoProducao = $g['C031']['locacaoPadraoProducao'];
							if (!empty($paramlocacaoPadraoProducao)) {
								$sqlOpPai = mysql_query("SELECT T256A_T256_Id_Op_Pai
														   FROM T256A
														  WHERE T256A_T256_Id = '{$T256_Id}'");
								if(mysql_num_rows($sqlOpPai) == 1){
									$sqlLocacao = "AND T066_D004_Id = '{$paramlocacaoPadraoProducao}'";
								}
							}

							$sqlT066 = mysql_query("SELECT T066_Id
							                          FROM T066
													 WHERE T066_D009_Id = '{$row['T256_D009_Id']}'
													 {$sqlLocacao}
													 GROUP BY T066_Id");
							if(mysql_num_rows($sqlT066) == 1){
								$rowT066 = mysql_fetch_array($sqlT066);
								$form->campoSetarValor('T257_T066_Id', $rowT066['T066_Id'], true);
							} else {
								$form->campoSetarValor('T257_T066_Id', null, true);
							}

							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Imprime uma mensagem e fecha a janela
								$alert = gGeraAlertMsg("Operação OK", "Produção inserida com sucesso!", false);
								echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											if (typeof jQuery != 'undefined') {
												{$alert}
												divRefresh("{$g['divId']}", true);
												divRefresh('{$form->campoValorEnviado('divReload')}', true);
											}
										} </script></div>
EOT;
							}
						} else {
							echo gGeraAlertMsg("Erro", $erroGatilhoValidacao);
						}
    	            } else {
    	            	echo gGeraAlertMsg("Erro", "A quantidade produzida deve ser menor ou igual ao saldo a produzir.");
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
        $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

        /**
        * PARTE 5: Exibir o formulário
        */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
  		$g['smarty']->display('formEditor.tpl');
    break;

    case 'est017FormCabecalhoOrdemProducao':
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
		$formId = uniqid();

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T255';
		$form_campo_pk = 'T255_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est017FormCabecalhoOrdemProducaoBtnEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est017/form/est017FormCabecalhoOrdemProducao/';
		// Editor
		$form_editor = array(
			'geral' => '{"T255_Observacao":{"top":76,"left":13,"height":84,"width":376,"hidden":0},"T255_C007_Id":{"top":7,"left":36,"width":383,"hidden":0},"T255_Data_Emissao":{"top":30,"left":32,"width":72,"hidden":0},"T255_Flag_Status":{"top":53,"left":43,"width":120,"hidden":0},"est017FormCabecalhoOrdemProducaoBtnEnviar":{"top":166,"left":400,"width":null,"hidden":0}}',
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Dados Bancários
					'T255_C007_Id' => array('name' => 'T255_C007_Id', 'label' => 'Usuário', 'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Nome, C007_Primeiro_Nome', "WHERE C007_Flag_Ausente != 'S'", true, $g['usuarioAtual'])
					),
					'T255_Data_Emissao' => array('name' => 'T255_Data_Emissao', 'label' => 'Emissão', 'type' => 'data', 'value' => date("d/m/Y")),
					'T255_C004_Id' => array('name' => 'T255_C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'T255_Observacao' => array('name' => 'T255_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(6,6)),
					'T255_Flag_Status' => array('name' => 'T255_Flag_Status', 'label' => 'Status', 'type' => 'select', 'select' => 
						array(
							array('title' => 'ABERTO', 'value' => '0'),
							array('title' => 'CANCELADO', 'value' => '98'),
							array('title' => 'FINALIZADO', 'value' => '99'),
						)
					),
					'est017FormCabecalhoOrdemProducaoBtnEnviar' => array('name' => 'est017FormCabecalhoOrdemProducaoBtnEnviar', 'type'=>'button', 'reload' => false, 'value' => ($r_acaoId > 0) ? 'Alterar' : 'Incluir', 'reject' => true),
				)
			)
		);
        $form = new Formulario('est017FormCabecalhoOrdemProducao');
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
					$T255_Flag_Status = $form->campoValorEnviado('T255_Flag_Status');
					//Verifica se a OP possui produtos produzidos sem entrada no estoque
					$erroFinalizacaoOP = false;
					if($T255_Flag_Status == '99'){
						$sqlT256 = mysql_query("SELECT T256_Id FROM T256 LEFT JOIN T257 ON T257_T256_Id=T256_Id LEFT JOIN T001 on T257_Id=T001_T257_Id WHERE T256_T255_Id = '{$r_acaoId}' AND ((T256_Quantidade_Produzida != T256_Quantidade_Entrada_Estoque AND T256_Quantidade_Produzida > 0) OR T256_Quantidade_Produzida <= 0)");
						if(mysql_num_rows($sqlT256) > 0){
							$erroFinalizacaoOP = true;
						}
					}
					if($erroFinalizacaoOP){
						echo gGeraAlertMsg('Erro', 'Não foi possível finalizar a ordem de produção, existem itens produzidos que não deram entrada no estoque!');
					} else {
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
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$dadosT255 = mysql_query("select 
													T255A_T255_Id
												from 
													T255A
												where
													T255A_Id = '{$g['mysqlLastId']}'");
						$varT255 = mysql_fetch_array($dadosT255);
						$T255_Id = $varT255['T255A_T255_Id'];

						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso! - ');
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Ordem de produção', '/est/est017/content/est017ContentOrdemProducao/', '&acaoId=' + encodeURIComponent('{$T255_Id}') + '&tabela=T255', [990,605]);
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
	break;

	case 'est017FormProdutoComposicao':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
		$form_tabela = 'T261';
		$form_campo_pk = 'T261_Id';
		$form_parent = 'T256_Id';
        $form_parent_valor = @$r_T256_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est017FormProdutoComposicao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est017/form/est017FormProdutoComposicao/';

		$pesquisaGridExtra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D041 ON D041_D049_Id=D049_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D037 ON D037_Id=D001_D037_Id
			WHERE (D009_C004_Id = '{$g['empresaAtual']}'
			AND D001_Flag_Ativo='S')
			GROUP BY D009_Id
			ORDER BY D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																											// tabela
			'D009_Id',																										// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido,D009_Id',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Produto',																							// título da janela
			'Código,Descrição,Marca,Estoque',														// descrição dos campos
			'est017CampoProcuraProduto',																					// callback js
			$g['divId'],																									// divId
			'',
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right'),
				array('colunaExibir' => false)
			)
		);

		$form_editor = array('geral' => '{"T261_D001_Id_Composicao":{"top":7,"left":20,"width":263,"hidden":0},"T261_Quantidade":{"top":30,"left":2,"width":86,"hidden":0}}');
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est017FormProdutoComposicao');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T261_D001_Id_Composicao' => array('name' => 'T261_D001_Id_Composicao' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(40,110)),
					'T261_Quantidade' => array('name' => 'T261_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(10,255), 'casasDecimais' => '5'),
					'T261_T256_Id' => array('name' => 'T261_T256_Id', 'type'=>'hidden', 'value'=> @$r_T256_Id),
               	),
            )
		);
		$form->definirFormularioArray($formArray);

		if(!empty($acaoId)){
			$sqlD009 = mysql_query("SELECT CONCAT(D001_Codigo_Produto, ' - ', D001_Descricao_Produto) as Produto 
			                          FROM D001 
								 LEFT JOIN D049 ON D001_Id = D049_D001_Id
								 LEFT JOIN D009 ON D049_Id = D009_D049_Id
								 LEFT JOIN T261 ON T261_D001_Id_Composicao=D001_Id 
								     WHERE T261_Id = '{$acaoId}'
		                               AND D009_C004_Id='{$g['empresaAtual']}'");
			$D009 = mysql_fetch_array($sqlD009);
			$D009['Produto'] = mysql_real_escape_string($D009['Produto']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T261_D001_Id_Composicao_Pesquisa').val('{$D009['Produto']}');
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
		$botoesDir .= '<button type="submit" id="est017FormProdutoComposicao_btn">'.$labelButton.'</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est017FormProcessos':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
		$form_tabela = 'T262';
		$form_campo_pk = 'T262_Id';
		$form_parent = 'T256_Id';
        $form_parent_valor = @$r_T256_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est017FormProcessos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est017/form/est017FormProcessos/';

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form_editor = array('geral' => '{"T262_Descricao":{"top":30,"left":24,"height":62,"width":248,"hidden":0},"T262_D079_Id":{"top":99,"left":33,"width":128,"hidden":0},"T262_D078_Id":{"top":122,"left":37,"width":128,"hidden":0},"T262_Lead_Time":{"top":145,"left":23,"width":120,"hidden":0},"T262_Numero_Funcionarios":{"top":168,"left":11,"width":120,"hidden":0},"T262_Ordem":{"top":7,"left":40,"width":120,"hidden":0}}');
		$form = new Formulario('est017FormProcessos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T262_Descricao' => array('name' => 'T262_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(4,35), 'align' => 'right'),
                	'T262_D079_Id' => array('name' => 'T262_D079_Id', 'label' => 'Máquina', 'type' => 'select',
						'select' => gGeraSelect('D079', 'D079_Id', 'D079_Nome', 'ORDER BY D079_Id', true)
					),
					'T262_D078_Id' => array('name' => 'T262_D078_Id', 'label' => 'Função', 'type' => 'select',
						'select' => gGeraSelect('D078', 'D078_Id', 'D078_Nome', 'ORDER BY D078_Id', true)
					),
					'T262_Lead_Time' => array('name' => 'T262_Lead_Time', 'label' => 'Lead Time', 'type' => 'moeda'),
					'T262_Numero_Funcionarios' => array('name' => 'T262_Numero_Funcionarios', 'label' => 'Funcionários', 'type' => 'text'),
					'T262_Ordem' => array('name' => 'T262_Ordem', 'label' => 'Ordem', 'type' => 'text'),
					'T262_T256_Id' =>  array('name' => 'T262_T256_Id', 'type'=>'hidden', 'value' => @$r_T256_Id),
				),
            ) // 'campos'
		);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		(!$g['debug']) ? $form->excluirCamposQueNaoExistem($form_tabela) : $form->verificarCamposQueNaoExistem($form_tabela);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
		$botoesDir .= '<button type="submit" id="est017FormProcessos_btn">' . $labelButton . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est017FormConsumivel':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
		$form_tabela = 'T263';
		$form_campo_pk = 'T263_Id';
		$form_parent = 'T262_Id';
        $form_parent_valor = @$r_T262_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est017FormConsumivel_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est017/form/est017FormConsumivel/';

		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S')
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																											// tabela
			'D009_Id',																										// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido,D009_Id',				// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Produto',																							// título da janela
			'Código,Descrição,Marca,Estoque',																				// descrição dos campos
			'est017CampoProcuraConsumivel',																					// callback js
			$g['divId'],																									// divId
			'',																												// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right'),
				array('colunaExibir' => false)
			)
		);
		$form_editor = array('geral' => '{"T263_D009_Id":{"top":7,"left":20,"width":250,"hidden":0},"T263_Quantidade":{"top":30,"left":2,"width":86,"hidden":0}}');
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est017FormConsumivel');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T263_D009_Id' => array('name' => 'T263_D009_Id' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(35,110)),
					'T263_T262_Id' => array('name' => 'T263_T262_Id', 'label' => 'Nome', 'type' => 'hidden', 'value' => @$r_T262_Id, 'align' => 'right'),
					'T263_Quantidade' => array('name' => 'T263_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(10,255), 'casasDecimais' => '5'),
				),

            )
		);
		$form->definirFormularioArray($formArray);

		if(!empty($acaoId)){
			$sqlD009 = mysql_query("SELECT CONCAT(D001_Codigo_Produto, ' - ', D001_Descricao_Produto) as Produto FROM D001 LEFT JOIN D049 ON D001_Id=D049_D001_Id LEFT JOIN D009 ON D049_Id=D009_D049_Id LEFT JOIN T263 ON T263_D009_Id=D009_Id WHERE T263_Id = '{$acaoId}'");
			$D009 = mysql_fetch_array($sqlD009);
			$D009['Produto'] = mysql_real_escape_string($D009['Produto']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T263_D009_Id_Pesquisa').val('{$D009['Produto']}');
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
		$botoesDir .= '<button type="submit" id="est017FormConsumivel_btn">'.$labelButton.'</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
