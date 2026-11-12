<?php
namespace hardness;

// Classes
require_once('bibliotecas/classes/FIN005.php');
$FIN005 = new FIN005();
require_once('prog/fin/fin005/form_func-js.php');

	switch ($g['r'][3]) {
		case 'fin005FormCadastro':
			//Busca dados do titulo (T002) para preencher o formulário
			if(!empty($r_T002_Id)){

				$sqlT002 = mysql_query("SELECT * 
				  						  FROM T002 
									 LEFT JOIN D024 ON D024_Id = T002_D024_Id 
									 LEFT JOIN D018 ON D018_Id = D024_D018_Id
									 LEFT JOIN D020 ON D020_Id = D024_D020_Id
									 LEFT JOIN D027 ON D027_Id = T002_D027_Id 
									 LEFT JOIN D007 ON D007_Id = D027_D007_Id 
										 WHERE T002_Id = '{$r_T002_Id}'");
				$T002 = mysql_fetch_array($sqlT002);
			}

			//print_r(gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco','left join D062 on D062_Id=D007_D062_Id where D062_Id is not null and D007_C004_Id='.$g['empresaAtual'].' order by D007_Id', true, $T002['D007_Id']));
			//die($T002['T002_Data_Vencimento']);
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
			$form_tabela = 'T117';
			$form_campo_pk = 'T117_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin005FormCadastro_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin005/form/fin005FormCadastro/';

			//form editor
			$form_editor = array('geral' => '{"mensagemJurosMultas":{"top":190.99266052246094,"left":4.9141998291015625,"width":504.431372,"hidden":0},"T117_D024_Id":{"top":6.9730377197265625,"left":81.28675842285156,"width":490.431372,"hidden":0},"T117_D007_Id":{"top":29.975494384765625,"left":84.19117736816406,"width":511,"hidden":0},"T117_Percentual_Multa":{"top":52.977935791015625,"left":44.19117736816406,"width":88.43137200000001,"hidden":0},"T117_Percentual_Juros_Dia":{"top":52.977935791015625,"left":471.4215850830078,"width":89.43137200000001,"hidden":0},"T117_Data_Vencimento":{"top":75.99264526367188,"left":56.72795104980469,"width":88.43137200000001,"hidden":0},"T117_Instrucoes":{"top":98.98284912109375,"left":70.93138122558594,"height":85.43137200000001,"width":504.431372,"hidden":0}}');

			// novo formulário
			$form = new Formulario('form_fin005_FormCadastro');


			$pesquisaSelect = <<<EOT
				select: function(event, ui) {
					// Código Ação padrão do autocomplete (automacaoFormulario.php)
					var id = $(this).attr('id').replace(/\_Pesquisa$/, '');
					if (ui.item.value != 0) {
						$(this).val(ui.item.label);
						$('#' + id).val(ui.item.value);
						$(this).data('selectLabel', ui.item.label);
						$(this).data('selectValue', ui.item.value);
					} else {
						$(this).val('');
						$('#' + id).val(0);
					}
					// (FIM) Código Ação padrão do autocomplete (automacaoFormulario.php)
					// Nosso código (irá atualizar o GRID de baixo)
					divSetarAcaoId('{$r_divReload}', ui.item.value);
					divRefresh('{$r_divReload}', true);
					return false;
				}
EOT;
			// Definição do Formulário
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T117_D024_Id' => array('name' => 'T117_D024_Id', 'label' => 'Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%4/%5] - %6 Tel:(%8)%9'), 'pesquisaSelect' => $pesquisaSelect, 'length' => array(53,255)),
						'T117_D007_Id' => array('name' => 'Consulta_T117_D007_Id', 'label' => 'Banco', 'type' => 'select','extra' => "onchange=\"preencheCampos(this, '{$g['divId']}', '{$T002['T002_Valor_Total']}')\"", 'select' =>
							gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco','left join D062 on D062_Id=D007_D062_Id where D062_Id is not null and D007_Flag_Ativo = "S" order by D007_Id', true)
						),
						'T117_Percentual_Multa' => array('name' => 'T117_Percentual_Multa', 'label' => 'Multa Atraso(%)',	'type' => 'moeda', 'value' => (!empty($T002['D007_Percentual_Multa'])) ? @$T002['D007_Percentual_Multa']:'0,00', 'length' => array(5,6)),
						'T117_Percentual_Juros_Dia' => 	array('name' => 'T117_Percentual_Juros_Dia', 'label' => 'Juros Mês(%)', 'type' => 'moeda', 'align'=>'right', 'style'=>'float:right','value' => (!empty($T002['D007_Percentual_Juros_Dia'])) ? @$T002['D007_Percentual_Juros_Dia']:'0,00', 'length' => array(5,6)),
						'T117_Data_Vencimento' => array('name' => 'T117_Data_Vencimento', 'label' => 'Vencimento','type' => 'data', 'length' => array(10,10), 'value' => @gCorrigeData($T002['T002_Data_Vencimento'])),
						'T117_Instrucoes' => array('name' => 'T117_Instrucoes', 'label' => 'Instruções','type' => 'textarea', 'length' => array(3,50), 'value' => @$T002['D007_Instrucoes_Boleto']),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'T002_Id' => array('name' => 'T002_Id', 'label' => 'T002_Id', 'type' => 'hidden', 'value' => @$r_T002_Id, 'reject' => true),
						'mensagemJurosMultas' => array('name' => 'mensagemJurosMultas', 'label' => 'Mensagem Juros/Multa', 'type' => 'text', 'value' => $mensagemJurosMultas, 'reject' => true),

					)
				)
			);
			$form->definirFormularioArray($formArray);
			if(!empty($T002['T002_D024_Id'])){
				$D024 = "(".$T002['D024_Id'].") ".$T002['D024_Nome_Empresa']." [".$T002['D020_Nome_Cidade']."/".$T002['D018_UF']."] - Tel:(".$T002['D024_DDD_Telefone_1'].") ".$T002['D024_Telefone_1']; 
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#T117_D024_Id').val('{$T002['T002_D024_Id']}')
						$('#T117_D024_Id_Pesquisa').val('{$D024}')
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
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$retorno = $FIN005->gerarBoleto($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Boleto gerado com sucesso!', false);
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
						echo gGeraAlertMsg('Error', 'Erro ao gerar boleto:<br />' . $retorno);
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
			$botoesDir .= '<button type="submit" id="fin005FormCadastro_btn">Gerar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;

		case 'fin005FormAtualizarVencimento':
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
			$form_tabela = 'T117';
			$form_campo_pk = 'T117_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin005FormAtualizarVencimento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin005/form/fin005FormAtualizarVencimento/';

			$T117 = mysql_query("SELECT T117_Valor_Total, T002_Valor_Total, T002_Data_Vencimento_Original,T002_Data_Emissao, T002_T035_Id,T035_Nome_Arquivo  FROM T002 LEFT JOIN T117 ON T002_Id=T117_T002_Id OR T117_Id=T002_T117_Id LEFT JOIN T035 ON T035_Id=T002_T035_Id WHERE T117_Id='{$acaoId}'");
			$mT117 = mysql_fetch_array($T117);

			//form editor
			$form_editor = array('geral' => '{"T117_Data_Emissao":{"top":7,"left":30.671875,"width":86,"hidden":0},"emissao_titulo":{"top":7,"left":245.40625,"width":88,"hidden":0},"T117_Data_Vencimento":{"top":30,"left":8.03125,"width":88,"hidden":0},"vencimento_titulo":{"top":30,"left":230.109375,"width":88,"hidden":0},"T117_Valor_Total":{"top":53,"left":47.984375,"width":88,"hidden":0},"valor_titulo":{"top":53,"left":262.71875,"width":88,"hidden":0},"T117_Percentual_Multa":{"top":99,"left":27.03125,"width":88,"hidden":0},"T117_Percentual_Juros_Dia":{"top":122,"left":38.03125,"width":88,"hidden":0},"gerarJurosMulta":{"top":145,"left":2.5625,"width":96,"hidden":0},"atualizarValor":{"top":168,"left":9.265625,"width":256,"hidden":0},"livre":{"top":210,"left":16,"width":null,"hidden":0}}');

			// novo formulário
			$form = new Formulario('fin005FormAtualizarVencimento');

			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T117_Id' => array('name' => 'T117_Id', 'type' => 'hidden'),
						'T117_D007_Id' => array('name' => 'T117_D007_Id', 'type' => 'hidden'),
						'T117_Percentual_Multa' => 	array('name' => 'T117_Percentual_Multa', 'label' => 'Multa Atraso(%)', 'type' => 'moeda', 'align'=>'right', 'style'=>'float:right','value' => '0,00', 'length' => array(5,6)),
						'T117_Percentual_Juros_Dia' => 	array('name' => 'T117_Percentual_Juros_Dia', 'label' => 'Juros Mês(%)', 'type' => 'moeda', 'align'=>'right', 'style'=>'float:right','value' => '0,00', 'length' => array(5,6)),
						'T117_Data_Vencimento' => array('name' => 'T117_Data_Vencimento', 'label' => 'Vencimento boleto','type' => 'data', 'length' => array(10,10)),
						'T117_Data_Emissao' => array('name' => 'T117_Data_Emissao', 'label' => 'Emissão boleto','type' => 'data', 'length' => array(10,10)),
						'T117_Valor_Total' => 	array('name' => 'T117_Valor_Total', 'label' => 'Valor boleto', 'type' => 'moeda', 'align'=>'right', 'style'=>'float:right', 'length' => array(10,10)),
						'emissao_titulo' => array('name' => 'emissao_titulo', 'label' => 'Emissão titulo','type' => 'data','reject'=>true,'readonly'=>'readonly','value'=>gCorrigeData($mT117['T002_Data_Emissao']), 'length' => array(10,10)),
						'vencimento_titulo' => array('name' => 'vencimento_titulo', 'label' => 'Vencimento titulo','type' => 'data','reject'=>true,'readonly'=>'readonly','value'=>gCorrigeData($mT117['T002_Data_Vencimento_Original']), 'length' => array(10,10)),
						'valor_titulo' => 	array('name' => 'valor_titulo', 'label' => 'Valor titulo','reject'=>true,'readonly'=>'readonly', 'type' => 'moeda', 'align'=>'right', 'style'=>'float:right','value' => (!empty($mT117['T002_Valor_Total'])) ? @$mT117['T002_Valor_Total']:'0,00', 'length' => array(5,6)),
						'gerarJurosMulta' => array('name' => 'gerarJurosMulta', 'label' => 'Gerar juros e multa ?', 'type' => 'select', 'reject' => true, 
							'select' => array(
								array('title' => 'Sim', 'value' => 'S'),
								array('title' => 'Não', 'value' => 'N'),
							)
						),
						'atualizarValor' => array('name' => 'atualizarValor', 'label' => 'Atualizar c/base no:', 'type' => 'select', 'reject' => true, 
							'select' => array(
								array('title' => 'Valor e vencimento do boleto', 'value' => '1'),
								array('title' => 'Valor e vencimento do titulo', 'value' => '2'),
							)
						)
					)
				)
			);

			if($mT117['T002_T035_Id'] > 0){
				$formArray['campos']['geral']['livre'] = array('name' => 'linha', 'type'=>'comentario', 'value' => '<b>Atenção!</b></font><br><b>Esse boleto está registrado no banco, na remessa nº '.$mT117['T002_T035_Id'].'.</b><br>Poderá ser necessário também atualizar o boleto no seu home banking.', 'label' => '', 'reject' => true, 'style' => 'color: #EF1010; font-size: 14px;');
			} elseif($g['C031']['atualizarNossoNumeroBoleto'] == 'S'){
				$formArray['campos']['geral']['livre'] = array('name' => 'linha', 'type'=>'comentario', 'value' => "<b>Atenção!</b></font><br><b>A configuração está definida para gerar um novo nosso número, e um novo boleto será criado.</br><font style='font-size:10px;font-weight:normal'>CONFIGURAÇÃO->OPÇÕES->FINANCEIRO->BOLETO->Atualizar nosso número do boleto.</font>", 'label' => '', 'reject' => true, 'style' => 'color: #EF1010; font-size: 14px;');
			}
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
					// Gerar Boleto
					$retorno = $FIN005->atualizarVencimentoBoleto($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Boleto atualizado com sucesso!', false);
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
						echo gGeraAlertMsg('Error', 'Erro ao gerar boleto:<br />' . $retorno);
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
			$botoesDir .= '<button type="submit" id="fin005FormAtualizarVencimento_btn">Atualizar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;

		case 'fin005FormAtualizarVencimentoMarcados':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin005FormAtualizarVencimentoMarcados_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin005/form/fin005FormAtualizarVencimentoMarcados/';

			//form editor
			$form_editor = array('geral' => '{"atualizarValor":{"top":98.98898315429688,"left":9.068603515625,"width":221,"hidden":0},"T117_Data_Vencimento":{"top":6.98529052734375,"left":40.80877685546875,"width":88.03921600000001,"hidden":0},"T117_Percentual_Multa":{"top":29.993865966796875,"left":28.27813720703125,"width":89.03921600000001,"hidden":0},"T117_Percentual_Juros_Dia":{"top":52.987152099609375,"left":39.49139404296875,"width":87.03921600000001,"hidden":0},"gerarJurosMulta":{"top":75.9957275390625,"left":6.0355224609375,"width":95,"hidden":0},"T117_Valor_Total":{"top":75.9957275390625,"left":513.219970703125,"width":77.03921600000001,"hidden":1}}');

			// novo formulário
			$form = new Formulario('fin005FormAtualizarVencimentoMarcados');

			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T117_Percentual_Multa' => array('name' => 'T117_Percentual_Multa', 'label' => 'Multa Atraso(%)',	'type' => 'moeda','value' => '0,00', 'length' => array(5,6)),
						'T117_Percentual_Juros_Dia' => 	array('name' => 'T117_Percentual_Juros_Dia', 'label' => 'Juros Mês(%)', 'type' => 'moeda', 'align'=>'right', 'style'=>'float:right','value' => '0,00', 'length' => array(5,6)),
						'T117_Data_Vencimento' => array('name' => 'T117_Data_Vencimento', 'label' => 'Vencimento','type' => 'data', 'length' => array(10,10)),
						'T117_Valor_Total' => array('name' => 'T117_Valor_Total', 'label' => 'Valor','type' => 'moeda', 'length' => array(10,10)),
						'gerarJurosMulta' => array('name' => 'gerarJurosMulta', 'label' => 'Gerar juros e multa ?', 'type' => 'select', 'reject' => true, 
							'select' => array(
								array('title' => 'Sim', 'value' => 'S'),
								array('title' => 'Não', 'value' => 'N'),
							)
						),
						'atualizarValor' => array('name' => 'atualizarValor', 'label' => 'Atualizar c/base no:', 'type' => 'select', 'reject' => true, 
							'select' => array(
								array('title' => 'Valor e vencimento do titulo', 'value' => '2'),
							)
						),
						'clausulaExtra' => array('name' => 'clausulaExtra', 'value' => $r_extra, 'reject' => true, 'type' => 'hidden')
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
					// Gerar Boleto
					$extra = $form->campoValorEnviado('clausulaExtra');
					$retorno = $FIN005->atualizarVencimentoBoletosMarcados($form, $extra);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Boletos atualizados com sucesso!', false);
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
						echo gGeraAlertMsg('Error', 'Erro ao gerar boleto:<br />' . $retorno);
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
			$botoesDir .= '<button type="submit" id="fin005FormAtualizarVencimentoMarcados_btn">Gerar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;
	}
?>