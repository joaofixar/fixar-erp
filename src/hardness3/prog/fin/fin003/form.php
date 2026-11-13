<?php
namespace hardness;

require_once("bibliotecas/classes/FIN009.php");
$FIN009 = new FIN009();

require_once("prog/fin/fin003/form_func-js.php");
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'fin003FormLancamentos':
	/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		$form_tabela = 'T019';
		$form_campo_pk = 'T019_id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin003EnviarFormulario';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin003/form/fin003FormLancamentos/';

		// form editor
		$form_editor = array(
			'geral' => '{"T019_D031_Id":{"top":145,"left":48,"width":175,"hidden":0},"T019_D007_Id":{"top":7,"left":54,"width":208,"hidden":0},"T019_Data_Lancamento":{"top":30,"left":60,"width":75,"hidden":0},"T019_Numero_Cheque":{"top":30,"left":642,"width":75,"hidden":1},"T019_Referente":{"top":53,"left":33,"width":345,"hidden":0},"T019_Complemento":{"top":53,"left":631,"width":345,"hidden":1},"T019_D024_Id":{"top":76,"left":46,"width":352,"hidden":0},"T019_Valor_Lancamento":{"top":99,"left":58,"width":168,"hidden":0},"T019_Flag_Deb_Crd":{"top":122,"left":14,"width":176,"hidden":0},"T019_D027_Id":{"top":145,"left":48,"width":175,"hidden":0},"T019_D032_Id":{"top":168,"left":52,"width":176,"hidden":0},"T019_D014_Id":{"top":191,"left":54,"width":352,"hidden":0},"T019_Observacao":{"top":214,"left":29,"height":62,"width":347,"hidden":0}}', 
		);
		
		$D007_Id = isset($r_fin003FormLancamentos) ? $r_fin003FormLancamentos['T019_D007_Id'] : $r_D007_Id;
		$g['D007_Id'] = $D007_Id;
		
		$T019 = mysql_fetch_array(mysql_query("SELECT T019_D032_Id FROM T019 WHERE T019_Id = '{$acaoId}'"));
		$D032_Id = (!empty($T019['T019_D032_Id'])) ? $T019['T019_D032_Id'] : 0;

		// Definição do Formulário
		// CARLOS CARVALHO

		if ($g['C031']['agrupamentoDesabilitado'] == 'S'){
			$select = '';
			$extraSelectConta = '';
			$extraSelectGrupo = '';
			$argSelect3 = 'D014_SubConta';
			$argSelect4 = "WHERE D014_Flag_Ativo='S' AND D014_SubConta = 'CAIXAS' ORDER BY D014_SubConta";
/*
			if($g['c029Ids'][267]){
				$desabilitar = true;
			}else{
				$desabilitar = false;
			}
			*/
		}else{
			
			$select = true;
			$desabilitar = false;
			$extraSelectGrupo = "onchange=\"retornaSaldoConta('{$g['divId']}')\"";
			$extraSelectConta = "onchange=\"alteraConta(this, '{$g['divId']}')\"";
			$argSelect3 = 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta';
			$argSelect4 = "LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$D032_Id}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta";
		}

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T019_D007_Id' => array('name' => 'T019_D007_Id', 'label'=>'Conta', 'type' => 'select','disabled' => true, 'select' => 
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco', "WHERE D007_Id='{$D007_Id}' AND D007_C004_Id={$g['empresaAtual']}")
					),	
					'T019_Data_Lancamento' => array('name' => 'T019_Data_Lancamento','label'=>'Data','type' => 'data', 'length' => array(10,10)),	
					'T019_Numero_Cheque' => array('name' => 'T019_Numero_Cheque', 'label'=>'Documento','type' => 'text', 'length' => array(10,10)),
					'T019_Referente' => array('name' => 'T019_Referente', 'label'=>'Descrição','type' => 'text', 'length' => array(64,60)),
					'T019_Complemento' => array('name' => 'T019_Complemento', 'label'=>'Complemento','type' => 'text', 'length' => array(64,100)),
					'T019_Valor_Lancamento' => array('name' => 'T019_Valor_Lancamento', 'label'=>'Valor','type' => 'moeda', 'length' => array(20,10)),												
					'T019_Flag_Deb_Crd' => array('name' => 'T019_Flag_Deb_Crd', 'label' => 'Débito/Crédito', 'type' => 'select','select' => 
						array(
							array('title' => 'Débito', 'value' => '0', 'selected' => true),
							array('title' => 'Crédito', 'value' => '1'),
						)
					),
					'T019_Observacao' => array('name' => 'T019_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(3,61)),
					'T019_D027_Id' => array('name' => 'T019_D027_Id', 'label'=>'Portador', 'type' => 'select','select' => 
						gGeraSelect('D027', 'D027_Id', 'D027_Portador', '', 'true')
					),
					'T019_D031_Id' => array('name' => 'T019_D031_Id', 'label'=>'Portador', 'type' => 'select','select' => 
						gGeraSelect('D031', 'D031_Id', 'D031_Portador', '', 'true')
					),
					/*
					'T019_D032_Id' => array('name' => 'T019_D032_Id', 'label' => 'Grupo', 'disabled' => $desabilitar,'type' => 'select','extra' => $extraSelectConta, 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', $select)
					),
					'T019_D014_Id' => array('name' => 'T019_D014_Id', 'label' => 'Conta', 'disabled' => $desabilitar, 'type' => 'select', 'extra' => $extraSelectGrupo, 'select' => 
						gGeraSelect('D014', 'D014_Id', $argSelect3, $argSelect4, $select)
					),
*/

					'T019_D032_Id' => array('name' => 'T019_D032_Id', 'label' => 'Grupo', 'type' => 'select','extra' => $extraSelectConta, 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', $select)
					),
					'T019_D014_Id' => array('name' => 'T019_D014_Id', 'label' => 'Conta', 'type' => 'select',  'extra' => $extraSelectGrupo, 'select' => 
						gGeraSelect('D014', 'D014_Id', $argSelect3,$argSelect4, $select)
					),

					'T019_D024_Id' => array('name' => 'T019_D024_Id', 'label'=> "Empresa",  'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" '.$clientePorEmpresa.' order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'T019_C004_Id' => array('name' => 'T019_C004_Id', 'label'=>'IdEmpresaAtual', 'type' => 'hidden','value' => $g['empresaAtual']),
					//'T019_D007_Id' => array('name' => 'D007_Id', 'label'=>'IdEmpresaAtual','type' => 'hidden','value' => $r_D007_Id),
					'Saldo_Conta' => array('name' => 'Saldo_Conta', 'label'=>'Saldo Conta', 'type' => 'hidden', 'length' => array(15,10), 'reject'=>true),
				) // geral
			) // campos
		);
        $form = new Formulario('fin003FormLancamentos');
		$form->definirFormularioArray($formArray);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #T019_Flag_Deb_Crd').change(function() {
					if($(this).val() == '0') {
						$('#{$g['divId']} #T019_D027_Id').closest('.formEditor').hide();
						$('#{$g['divId']} #T019_D031_Id').closest('.formEditor').show();
					} else if($(this).val() == '1') {
						$('#{$g['divId']} #T019_D031_Id').closest('.formEditor').hide();
						$('#{$g['divId']} #T019_D027_Id').closest('.formEditor').show();
					}
				});
				$('#{$g['divId']} #T019_Flag_Deb_Crd').trigger('change');
			} </script></div>
EOT;

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

					// Faz o select na conta e testa se a o campo para verificar o saldo da conta está igual a SIM
					$D014_Id = $form->campoValorEnviado('T019_D014_Id');
					$D014=mysql_query("SELECT D014_Flag_Verificar_Saldo from D014 WHERE D014_Id='{$D014_Id}'");
					$mD014 = mysql_fetch_array($D014);

					if($g['C031']['verificarSaldoConta'] == 'S' && $mD014['D014_Flag_Verificar_Saldo'] == 'S'){
						$Saldo_Conta 		   = gCorrigeNumeroInverte($form->campoValorEnviado('Saldo_Conta'));
						$T019_Valor_Duplicata  = gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento'));
						$T019_Flag_Deb_Crd 	   = $form->campoValorEnviado('T019_Flag_Deb_Crd');
						$T019_Data_Lancamento  = $form->campoValorEnviado('T019_Data_Lancamento');
						$T019_Data_Lancamento  = explode("/", $T019_Data_Lancamento);
						$mes = $T019_Data_Lancamento[1];
						$ano = $T019_Data_Lancamento[2];

						$ValorAnterior = mysql_query("SELECT T019_Valor_Lancamento from T019 WHERE T019_Id='{$r_acaoId}'");
						$mValorAnterior = mysql_fetch_array($ValorAnterior);

						$Saldo_Conta = $Saldo_Conta - $mValorAnterior['T019_Valor_Lancamento'];

						if ($T019_Valor_Duplicata<0){
							$T019_Valor_Duplicata=$T019_Valor_Duplicata*-1;
						}

						if($T019_Valor_Duplicata > $Saldo_Conta && $T019_Flag_Deb_Crd == 0){
							$valorNecessario = ($T019_Valor_Duplicata - $Saldo_Conta);
							$Saldo_Conta = gCorrigeNumero($Saldo_Conta);

							$T018=mysql_query("SELECT T018_Valor FROM T018 WHERE T018_D014_Id='{$D014_Id}' AND T018_Mes='{$mes}' AND T018_Ano='{$ano}'");
							$mT018=mysql_fetch_array($T018);
							//die("T018_Valor: ".$mT018['T018_Valor']." Valor Necessario: ".$valorNecessario);
							$valorLimite = gCorrigeNumero($mT018['T018_Valor'] + $valorNecessario);

							echo gGeraAlertMsg('Erro', 'O valor da duplicata é maior que o saldo da conta! <br />Saldo Conta: R$ <b>'.$Saldo_Conta . "</b><br />Limite necessário: <b>" . $valorLimite."</b>");

						}else{
							if ($form->campoValorEnviado('T019_Flag_Deb_Crd') == 0) {
								$form->campoSetarValor('T019_D027_Id', '0', true);
								if ($form->campoValorEnviado('T019_Valor_Lancamento') > 0) {

									$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
								}
							} else {
								$form->campoSetarValor('T019_D031_Id', '0', true);
								if ($form->campoValorEnviado('T019_Valor_Lancamento') < 0) {
									$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
								}
							}
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {

								// Chamada de método que atualiza a projeção orçamentária da conta selecionada paa atualizar o saldo da conta
								$retorno = $FIN009->FIN009RecalculaProjecao($form->campoValorEnviado('T019_D014_Id'),null,null, $mes);

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
					}else{
						if ($form->campoValorEnviado('T019_Flag_Deb_Crd') == 0) {
							$form->campoSetarValor('T019_D027_Id', '0', true);
							if ($form->campoValorEnviado('T019_Valor_Lancamento') > 0) {
								$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
							}
						} else {
							$form->campoSetarValor('T019_D031_Id', '0', true);
							if ($form->campoValorEnviado('T019_Valor_Lancamento') < 0) {
								$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
							}
						}
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

					// Faz o select na conta e testa se a o campo para verificar o saldo da conta está igual a SIM
					$D014_Id = $form->campoValorEnviado('T019_D014_Id');
					$D014=mysql_query("SELECT D014_Flag_Verificar_Saldo, D014_Flag_Registrar_Conta from D014 WHERE D014_Id='{$D014_Id}'");
					$mD014 = mysql_fetch_array($D014);
					if($mD014['D014_Flag_Registrar_Conta'] == 'S'){
						$tipoLancamento = ($form->campoValorEnviado('T019_Flag_Deb_Crd') == 0) ? 'pagar' : 'receber';
						echo gGeraAlertMsg('Erro', 'A conta selecionada não permite a inclusão de lançamentos sem um título a '.$tipoLancamento.'!');
					} else {
						if($g['C031']['verificarSaldoConta'] == 'S' && $mD014['D014_Flag_Verificar_Saldo'] == 'S'){
							$Saldo_Conta = gCorrigeNumeroInverte($form->campoValorEnviado('Saldo_Conta'));
							$Valor_Duplicata = gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento'));
							$T019_Data_Lancamento  = $form->campoValorEnviado('T019_Data_Lancamento');
							$T019_Data_Lancamento  = explode("/", $T019_Data_Lancamento);
							$mes = $T019_Data_Lancamento[1];
							$ano = $T019_Data_Lancamento[2];
							$T019_Flag_Deb_Crd = $form->campoValorEnviado('T019_Flag_Deb_Crd');
	
							//die("Valor duplicata: " . $Valor_Duplicata . " > Saldo Conta: " . $Saldo_Conta . "FLag: " . $T019_Flag_Deb_Crd);
							if ($Valor_Duplicata<0){
								$Valor_Duplicata=$Valor_Duplicata*-1;
							}
	
							if($Valor_Duplicata > $Saldo_Conta && $T019_Flag_Deb_Crd == 0){
								$valorNecessario = ($Valor_Duplicata - $Saldo_Conta);
								$Saldo_Conta = gCorrigeNumero($Saldo_Conta);
	
								$T018=mysql_query("SELECT T018_Valor FROM T018 WHERE T018_D014_Id='{$D014_Id}' AND T018_Mes='{$mes}' AND T018_Ano='{$ano}'");
								$mT018=mysql_fetch_array($T018);
								//die("T018_Valor: ".$mT018['T018_Valor']." Valor Necessario: ".$valorNecessario);
								$valorLimite = gCorrigeNumero($mT018['T018_Valor'] + $valorNecessario);
	
								echo gGeraAlertMsg('Erro', 'O valor da duplicata é maior que o saldo da conta! <br />Saldo Conta: R$ <b>'.$Saldo_Conta . "</b><br />Limite necessário: <b>" . $valorLimite."</b>");
	
							}else{
	
								if ($form->campoValorEnviado('T019_Flag_Deb_Crd') == 0) {
									if ($form->campoValorEnviado('T019_Valor_Lancamento') > 0) {
										$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
									}
								} else {
									if ($form->campoValorEnviado('T019_Valor_Lancamento') < 0) {
										$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
									}
								}
								if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
	
									// Chamada de método que atualiza a projeção orçamentária da conta selecionada paa atualizar o saldo da conta
									$retorno = $FIN009->FIN009RecalculaProjecao($form->campoValorEnviado('T019_D014_Id'),null,null, $mes);
	
									// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
									$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso! ', false);
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
						}else{
							if ($form->campoValorEnviado('T019_Flag_Deb_Crd') == 0) {
								if ($form->campoValorEnviado('T019_Valor_Lancamento') > 0) {
									$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
								}
							} else {
								if ($form->campoValorEnviado('T019_Valor_Lancamento') < 0) {
									$form->campoSetarValor('T019_Valor_Lancamento', gCorrigeNumero(gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento')) * -1), true);
								}
							}
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
								$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso! ', false);
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

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$("#{$g['divId']} .itemForm-T019_D014_Id").change();
			} </script></div>
EOT;

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fin003EnviarFormulario">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
			
	break;

	case 'fin003FormImportarExtrato':
		global $g;
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
		$form_tabela = 'T000';
		$form_campo_pk = 'T000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin003FormImportarExtrato_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin003/form/fin003FormImportarExtrato/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('fin001FormRetornoRemessas');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoTXT'] = array('name' => 'uploadArquivoTXT', 'label' => 'Selecione o OFX', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['fin003FormImportarExtrato_btn'] = array('name' => 'fin003FormImportarExtrato_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Importar extrato OFX', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D007_Id'] = array('name' => 'D007_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_D007_Id, 'reject' => true);

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
				$arquivo = $form->campoValorEnviado('uploadArquivoTXT');
				$D007_Id = $form->campoValorEnviado('D007_Id');
				//Identifica no arquivo de retorno o banco
				if(empty($arquivo['tmp_name'])){
					echo gGeraAlertMsg('Erro', 'Selecione um arquivo para importar');
				} else {
					require_once("bibliotecas/classes/FIN003.php");
					$FIN003 = new FIN003();
					$retorno = $FIN003->importarExtratoBancario($arquivo, $D007_Id);
					if(!is_array($retorno)){
						echo gGeraAlertMsg('Erro', $retorno);
					} else {
						// Imprime uma mensagem
						$T215_Id = $retorno[0];
						echo gGeraAlertMsg('Operação OK', 'Arquivo importado com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divReload}', true);
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transações', '/fin/fin003/content/fin003ContentTransacoes/', '&acaoId=' + encodeURIComponent('{$T215_Id}') + '&tabela=T215', [800,600]);
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
						echo  $processado;
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


	case 'fin003FormIncluirTransferencia':
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
		$form_tabela = 'T019';
		$form_campo_pk = 'T019_id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin003FormIncluirTransferenciaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin003/form/fin003FormIncluirTransferencia/';

		// form editor
		$form_editor = array(
			'geral' => '{"D007_Id_Origem":{"top":7,"left":25,"width":256,"hidden":0},"D007_Id_Destino":{"top":30,"left":23,"width":256,"hidden":0},"T019_Data_Lancamento":{"top":53,"left":61,"width":75,"hidden":0},"T019_Numero_Cheque":{"top":76,"left":36,"width":75,"hidden":0},"T019_Referente":{"top":99,"left":34,"width":345,"hidden":0},"T019_Complemento":{"top":122,"left":26,"width":345,"hidden":0},"T019_Valor_Lancamento":{"top":145,"left":57,"width":168,"hidden":0},"T019_D027_Id":{"top":168,"left":49,"width":176,"hidden":0},"T019_D032_Id":{"top":191,"left":53,"width":176,"hidden":0},"T019_D014_Id":{"top":214,"left":55,"width":352,"hidden":0},"T019_D024_Id":{"top":237,"left":48,"width":352,"hidden":0},"T019_Observacao":{"top":260,"left":30,"height":45,"width":347,"hidden":0}}',
		);
			
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D007_Id_Origem' => array('name' => 'D007_Id_Origem', 'label'=>'Conta Origem', 'type' => 'select', 'reject' => true, 'select' => 
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco', "WHERE D007_Flag_Ativo='S'", true, $r_D007_Id)
					),
					'D007_Id_Destino' => array('name' => 'D007_Id_Destino', 'label'=>'Conta Destino', 'type' => 'select', 'reject' => true, 'select' => 
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco', "WHERE D007_Flag_Ativo='S'", true)
					),
					'T019_Data_Lancamento' => array('name' => 'T019_Data_Lancamento','label'=>'Data','type' => 'data', 'length' => array(10,10)),	
					'T019_Numero_Cheque' => array('name' => 'T019_Numero_Cheque', 'label'=>'Documento','type' => 'moeda', 'casasDecimais' => '0', 'length' => array(10,10)),
					'T019_Referente' => array('name' => 'T019_Referente', 'label'=>'Descrição','type' => 'text', 'length' => array(100,100)),
					'T019_Complemento' => array('name' => 'T019_Complemento', 'label'=>'Complemento','type' => 'text', 'length' => array(64,60)),
					'T019_Valor_Lancamento' => array('name' => 'T019_Valor_Lancamento', 'label'=>'Valor','type' => 'moeda', 'length' => array(20,10)),
					'T019_Observacao' => array('name' => 'T019_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(3,61)),
					'T019_D027_Id' => array('name' => 'T019_D027_Id', 'label'=>'Portador', 'type' => 'select','select' => 
						gGeraSelect('D027', 'D027_Id', 'D027_Portador', '', 'true')
					),
					'T019_D032_Id' => array('name' => 'T019_D032_Id', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','ORDER BY D032_Descricao', true)
					),
					'T019_D014_Id' => array('name' => 'T019_D014_Id', 'label' => 'Conta', 'type' => 'select', 'extra' => "onchange=\"retornaSaldoConta('{$g['divId']}')\"", 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta','LEFT JOIN D154 ON D154_Id=D014_D154_Id ORDER BY D014_SubConta', true)
					),
					'T019_D024_Id' => array('name' => 'T019_D024_Id', 'label'=> "Empresa",  'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'T019_C004_Id' => array('name' => 'T019_C004_Id', 'label'=>'IdEmpresaAtual','type' => 'hidden','value' => $g['empresaAtual']),
					//'T019_D007_Id' => array('name' => 'D007_Id', 'label'=>'IdEmpresaAtual','type' => 'hidden','value' => $r_D007_Id),
					'Saldo_Conta' => array('name' => 'Saldo_Conta', 'label'=>'Saldo Conta','type' => 'hidden', 'length' => array(15,10), 'reject'=>true),
				)
			)
		);
        $form = new Formulario('fin003FormIncluirTransferencia');
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
				require_once("bibliotecas/classes/FIN003.php");
				$FIN003 = new FIN003();
				$retorno = $FIN003->incluirTransferencia($form);
				if($retorno === true){
					// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
					$alert = gGeraAlertMsg('Operação OK', 'Transferência realizada com sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
			  				});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Erro', $retorno);
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

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$("#{$g['divId']} .itemForm-T019_D014_Id").change();
			} </script></div>
EOT;

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fin003FormIncluirTransferenciaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
			
	break;

	case 'fin003FormEditarTransacao':
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T216';
		$form_campo_pk = 'T216_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin003FormEditarTransacaoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin003/form/fin003FormEditarTransacao/';

		$T216 = mysql_fetch_array(mysql_query("SELECT * FROM T216 LEFT JOIN D024 ON D024_Id=T216_D024_Id WHERE T216_Id = '{$acaoId}'"));

		// form editor
		$form_editor = array(
			'geral' => '{"pesquisaGridCliente":{"top":99,"left":13,"width":378,"hidden":0},"T216_Data":{"top":7,"left":83,"width":104,"hidden":0},"T216_Valor":{"top":30,"left":81,"width":104,"hidden":0},"T216_Descricao":{"top":53,"left":56,"width":392,"hidden":0},"T216_Historico":{"top":76,"left":45,"width":392,"hidden":0},"T216_D032_Id":{"top":122,"left":75,"width":207,"hidden":0},"T216_D014_Id":{"top":145,"left":77,"width":207,"hidden":0},"T216_D031_Id":{"top":168,"left":64,"width":207,"hidden":0}}',
		);

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa', // extras sql
			'Pesquisa de Clientes',																							// título da janela
			'Código,N. Empresa,N. Fantasia,Cidade,UF,CPF,CNPJ,DDD,Telefone',												// descrição dos campos
			'campoProcurarClienteTransacao',																							// callback js
			$g['divId'],																									// divId
			0,																												// parametro extra
			array()																											// parametros para cada campo do grid
		);
			
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'pesquisaGridCliente' => array('name' => 'pesquisaGridCliente', 'label' => 'Cliente/Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'reject' => true, 'value' => @$T216['D024_Nome_Empresa']), 
					'T216_D024_Id' => array('name' => 'T216_D024_Id', 'label' => 'Cliente/Forn.', 'type' => 'hidden'),
					'T216_Data' => array('name' => 'T216_Data', 'label' => 'Data', 'type' => 'data', 'readonly' => true),
					'T216_Valor' => array('name' => 'T216_Valor', 'label' => 'Valor', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true),
					'T216_Descricao' => array('name' => 'T216_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'T216_Historico' => array('name' => 'T216_Historico', 'label' => 'Observação', 'type' => 'text'),
					'T216_D032_Id' => array('name' => 'T216_D032_Id', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraContaTransacao(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true)
					),
					'T216_D014_Id' => array('name' => 'T216_D014_Id', 'label' => 'Conta', 'type' => 'select', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta', "LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$T216['T216_D032_Id']}'  AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
				)
			)
		);
		if($T216['T216_Valor'] > 0){
			$formArray['campos']['geral']['T216_D027_Id'] = array('name' => 'T216_D027_Id', 'label' => 'Portador', 'type' => 'select','select' => gGeraSelect('D027', 'D027_Id', 'D027_Portador', '', 'true'));
		} else {
			$formArray['campos']['geral']['T216_D031_Id'] = array('name' => 'T216_D031_Id', 'label' => 'Portador', 'type' => 'select','select' => gGeraSelect('D031', 'D031_Id', 'D031_Portador', '', 'true'));
		}

        $form = new Formulario('fin003FormEditarTransacao');
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
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						echo gGeraAlertMsg('Operação OK', 'Gravado com sucesso!');
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
		$botoesDir = $botoesEsq = '';
        $T216 = mysql_query("select T216_T015_Id from T216 where T216_Id='$acaoId'");
        $mT216 = mysql_fetch_array($T216);
        if ($mT216['T216_T015_Id']>0){
			$botoesEsq .= "<button onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Titulo Pagar - ID: ' + ".$mT216['T216_T015_Id'].", '/fin/fin002/content/fin002content02/','&acaoId=".$mT216['T216_T015_Id']."&tabela=T015', [900,500]);\">Abrir titulo</button>";
	    }
		$botoesDir .= '<button type="submit" id="fin003FormEditarTransacaoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
	break;

	case 'fin003FormEditarRegraExtrato':
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T217';
		$form_campo_pk = 'T217_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin003FormEditarRegraExtratoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin003/form/fin003FormEditarRegraExtrato/';

		$T217 = mysql_fetch_array(mysql_query("SELECT * FROM T217 LEFT JOIN D024 ON D024_Id=T217_D024_Id WHERE T217_Id = '{$acaoId}'"));

		// form editor
		$form_editor = array(
			'geral' => '{"pesquisaGridCliente":{"top":53,"left":10,"width":362,"hidden":0},"T217_Descricao":{"top":7,"left":56,"width":376,"hidden":0},"T217_Historico":{"top":30,"left":45,"width":376,"hidden":0},"portadorId":{"top":76,"left":64,"width":220,"hidden":0},"T217_D032_Id":{"top":99,"left":75,"width":220,"hidden":0},"T217_D014_Id":{"top":122,"left":77,"width":220,"hidden":0}}',
		);

		//Combo portador montado com as tabelas D027 (Receber) e D031 (Pagar)
		if($T217['T217_D031_Id'] > 0){
			$selected = 'D031-'.$T217['T217_D031_Id'];
		} else {
			$selected = 'D027-'.$T217['T217_D027_Id'];
		}

		$arrayD031 = gGeraSelect('D031', 'CONCAT("D031-",D031_Id)', 'D031_Portador', '', true, 'D031-'.$T217['T217_D031_Id']);
		$groupOption = array(array('title' => 'optgroup', 'value' => 'PORTADOR PAGAR'));
		array_splice($arrayD031, 1, 0, $groupOption);

		$arrayD027 = gGeraSelect('D027', 'CONCAT("D027-",D027_Id)', 'D027_Portador', '', true, 'D027-'.$T217['T217_D027_Id']);
		$groupOption = array(
							array('title' => 'optgroup', 'value' => ''),
							array('title' => 'optgroup', 'value' => 'PORTADOR RECEBER')
						);
		array_splice($arrayD027, 1, 0, $groupOption);
		unset($arrayD027[0]);

		$arrayPortador = array_merge($arrayD031, $arrayD027);
		foreach ($arrayPortador as $key => $campo) {
			if($campo['value'] == $selected){
				$arrayPortador[$key]['selected'] = true;
			}
		}

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa', // extras sql
			'Pesquisa de Clientes',																							// título da janela
			'Código,N. Empresa,N. Fantasia,Cidade,UF,CPF,CNPJ,DDD,Telefone',												// descrição dos campos
			'campoProcurarClienteRegra',																							// callback js
			$g['divId'],																									// divId
			0,																												// parametro extra
			array()																											// parametros para cada campo do grid
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T217_Descricao' => array('name' => 'T216_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'T217_Historico' => array('name' => 'T216_Historico', 'label' => 'Observação', 'type' => 'text'),
					'pesquisaGridCliente' => array('name' => 'pesquisaGridCliente', 'label' => 'Cliente/Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'reject' => true, 'value' => @$T217['D024_Nome_Empresa']), 
					'portadorId' => array('name' => 'portadorId', 'label' => 'Portador', 'type' => 'select', 'reject' => true, 'select' => $arrayPortador), 
					'T217_D024_Id' => array('name' => 'T217_D024_Id', 'label' => 'Cliente/Forn.', 'type' => 'hidden'),
					'T217_D027_Id' => array('name' => 'T217_D027_Id', 'label' => 'Portador', 'type' => 'hidden'),
					'T217_D031_Id' => array('name' => 'T217_D031_Id', 'label' => 'Portador', 'type' => 'hidden'),
					'T217_D007_Id' => array('name' => 'T217_D007_Id', 'label' => 'Banco', 'type' => 'hidden', 'value' => @$r_D007_Id),
					'T217_D032_Id' => array('name' => 'T217_D032_Id', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraContaRegra(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','ORDER BY D032_Descricao', true)
					),
					'T217_D014_Id' => array('name' => 'T217_D014_Id', 'label' => 'Conta', 'type' => 'select', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta', "LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$T217['T217_D032_Id']}' ORDER BY D014_SubConta", true)
					),
					//T217_Historico
					//T217_Descricao
					//T217_D027_Id
					//T217_D031_Id
					//T217_D032_Id
					//T217_D014_Id
					//T217_D024_Id
				)
			)
		);

        $form = new Formulario('fin003FormEditarTransacao');
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
				$tabelaPortador = $form->campoValorEnviado('portadorId');
				if(substr($tabelaPortador, 0, 4) == 'D031'){
					$portadorId = explode("-", $tabelaPortador);
					$portadorId = $portadorId[1];
					$form->campoSetarValor("T217_D031_Id", $portadorId, true);
					$form->campoSetarValor("T217_D027_Id", 0, true);
				} else if(substr($tabelaPortador, 0, 4) == 'D027'){
					$portadorId = explode("-", $tabelaPortador);
					$portadorId = $portadorId[1];
					$form->campoSetarValor("T217_D027_Id", $portadorId, true);
					$form->campoSetarValor("T217_D031_Id", 0, true);
				} else {
					$form->campoSetarValor("T217_D031_Id", 0, true);
					$form->campoSetarValor("T217_D027_Id", 0, true);
				}

				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
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
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="fin003FormEditarRegraExtratoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
	break;
}
