<?php
namespace hardness;
/**
 * Weiberlan
 * 12/11/2011
 */
 
require_once('prog/ven/ven002/form_func-js.php');
require_once("bibliotecas/classes/VEN012.php");
require_once("bibliotecas/classes/VEN002.php");
$VEN012 = new VEN012();
$VEN002 = new VEN002();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'ven012FormCabecalhoPedido':
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven012FormCabecalhoPedido_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven012/form/ven012FormCabecalhoPedido/';

		// novo formulário
		$form = new Formulario('form_fin002_form1');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_Id' => array('name' => 'T005_Id','label'=>'Pedido', 'type' => 'text', 'readonly' => true, 'length' => array(10,60)),
					'T005_D024_Id' => array('name' => 'Consulta_T005_D024_Id','label'=>'Cliente', 'type' => 'select', 'disabled' => true, 'align' => 'right',
						'select' => gGeraSelect('D024','D024_Id','D024_Nome_Empresa',"")
					),
					'T005_Valor_Total' => array('name' => 'T005_Valor_Total','label'=>'Valor', 'type' => 'text', 'readonly' => true, 'align' => 'right', 'length' => array(15,60)),
					'T005_D024_Id' => array('name' => 'Consulta_T005_D024_Id','label'=>'Endereço', 'type' => 'select', 'disabled' => true, 'reject' => true,
						'select' => gGeraSelect('D024','D024_Id','D024_Endereco',"")
					 ),
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
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesMid .= '<button type="submit" id="ven012FormCabecalhoPedido_btn">Continuar</button>';
		$botoesMid .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$botoesMid .= '<button onclick="" class="">Atualizar Aliquotas</button>';
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'ven012FormPedido':
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;	
		$T005_Id = (isset($r_ven012FormPedido)) ? $r_ven012FormPedido['T005_Id'] : $r_T005_Id;
		
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		$sql = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
		
		$result = mysql_query($sql);
		$retorno = mysql_fetch_array($result);
		
		$sql2 = "SELECT * FROM D024 WHERE D024_Id='{$retorno['T005_D024_Id']}'";
		
		$result2 = mysql_query($sql2);
		$retorno2 = mysql_fetch_array($result2);
		
		$D024_Nome_Empresa = $retorno2['D024_Nome_Empresa'];
		
		$sql3 = "SELECT * FROM D018 WHERE D018_Id='{$retorno2['D024_D018_Id']}'";
		$result3 = mysql_query($sql3);
		$estado = mysql_fetch_array($result3);
		
		$sql4 = "SELECT * FROM D020 WHERE D020_Id='{$retorno2['D024_D020_Id']}'";
		$result4 = mysql_query($sql4);
		$cidade = mysql_fetch_array($result4);
		
		$sql5 = "SELECT * FROM D006 WHERE D006_Id='{$retorno['T005_D006_Id']}'";
		$result5 = mysql_query($sql5);
		$CFOP = mysql_fetch_array($result5);
		$CFOPCodigo = $CFOP['D006_Codigo_CFOP'];
		$CFOPDescricao = $CFOP['D006_Descricao'];

		$T006=mysql_query("select sum(T006_Valor_ICMS_Credito) T006_Valor_ICMS_Credito,
                          T006_Aliquota_ICMS_Credito
                     	  from T006
                    	  where T006_T005_Id='$T005_Id'
                    	  group by T006_T005_Id");
		echo mysql_error();
		$mT006=mysql_fetch_array($T006);
   
        $mT006['T008_Valor_ICMS_Credito']=number_format($mT006['T006_Valor_ICMS_Credito'],2,',','.');

        // busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente='99999999999';
		if ($acaoId>0){
			$sql = "SELECT T005_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id, D024_D020_Id FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE T005_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idTransportadoraCidade=$aD024['D020_D022_Id'];
			$idCliente=$aD024['T005_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
		} 
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_ven012FormPedido)) ? $r_ven012FormPedido['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven012FormPedido_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven012/form/ven012FormPedido/';
		// Editor
		$form_editor = array(
        	'cabecalho' => '{"T005_Id":{"top":0,"left":0,"width":0,"hidden":1},"T005_Valor_Total_Trib_Transparencia":{"top":0,"left":0,"width":0,"hidden":1},"D024_Nome_Empresa":{"top":6.98529052734375,"left":60.56982421875,"width":383,"hidden":0},"Numero_Nota":{"top":236.99447631835938,"left":354.70281982421875,"width":104.03921600000001,"hidden":0},"Endereco":{"top":29.993865966796875,"left":92.784912109375,"width":376.039216,"hidden":0},"Bairro":{"top":52.9718017578125,"left":109.48223876953125,"width":376.039216,"hidden":0},"Cidade":{"top":75.98037719726562,"left":104.56494140625,"width":376.039216,"hidden":0},"T005_Data_Emissao":{"top":98.98898315429688,"left":71.61456298828125,"width":120.03921600000001,"hidden":0},"T005_Data_Saida":{"top":98.98898315429688,"left":350.2451171875,"width":104.03921600000001,"hidden":0},"CFOP":{"top":121.98223876953125,"left":110.876220703125,"width":376.039216,"hidden":0},"T005_Flag_Destino_Produto":{"top":144.99081420898438,"left":93.811279296875,"width":384,"hidden":0},"T005_Valor_Total_Produtos":{"top":167.98406982421875,"left":66.7279052734375,"width":119.03921600000001,"hidden":0},"T005_Valor_Total_IPI":{"top":167.98406982421875,"left":397.6868896484375,"width":104.03921600000001,"hidden":0},"T005_Valor_ICMS_Substituicao_Retencao":{"top":190.97732543945312,"left":96.52264404296875,"width":119.03921600000001,"hidden":0},"T005_Valor_Total_ICMS":{"top":190.97732543945312,"left":384.32904052734375,"width":104.03921600000001,"hidden":0},"T005_Valor_Total":{"top":213.98593139648438,"left":88.48040771484375,"width":119.03921600000001,"hidden":0},"T006_Valor_ICMS_Credito":{"top":213.98593139648438,"left":346.3695068359375,"width":104.03921600000001,"hidden":0},"T005_Flag_ACP":{"top":236.99447631835938,"left":74.55572509765625,"width":128,"hidden":0},"T005_Observacao":{"top":259.9877624511719,"left":43.7806396484375,"height":60.039215999999996,"width":376.039216,"hidden":0},"T005_Observacao_Nota_Fiscal":{"top":328.9674987792969,"left":22.36517333984375,"height":60.039215999999996,"width":376.039216,"hidden":0},"T005_D148_Id_Entrega":{"top":397.9626159667969,"left":52.54290771484375,"width":383,"hidden":0},"T005_D148_Id_Cobranca":{"top":420.9712219238281,"left":43.1832275390625,"width":383,"hidden":0},"T005A_D148_Id_Redespacho":{"top":443.9797668457031,"left":29.5343017578125,"width":383,"hidden":0}}',
            'frete' => '{"T005_D022_Id":{"top":7,"left":7,"width":200,"hidden":0},"T005_Flag_Totalizar_Peso":{"top":283,"left":20,"width":64,"hidden":0},"T005_Flag_Frete":{"top":30,"left":64,"width":116,"hidden":0},"T005_Valor_Frete":{"top":53,"left":36,"width":100,"hidden":0},"T005_D036_Id":{"top":76,"left":69,"width":98,"hidden":0},"T005_Valor_Despesas_Acessorias":{"top":99,"left":3,"width":100,"hidden":0},"T005_Transportadora_Placa_Veiculo":{"top":122,"left":63,"width":75,"hidden":0},"T005_Estado_Veiculo":{"top":122,"left":299,"width":49,"hidden":0},"T005_Quantidade_Volumes":{"top":145,"left":41,"width":100,"hidden":0},"T005_Especie":{"top":168,"left":51,"width":100,"hidden":0},"T005_Marca":{"top":191,"left":59,"width":100,"hidden":0},"T005_Numero_Caixas":{"top":214,"left":27,"width":100,"hidden":0},"T005_Peso_Bruto":{"top":237,"left":36,"width":100,"hidden":0},"T005_Peso_Liquido":{"top":260,"left":27,"width":100,"hidden":0}}'
        );

		// novo formulário
		$form = new Formulario('ven012FormPedido');

		// endereço de entrega
		$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true);
		$arrayEndereco[0]['title'] = 'PADRÃO';
		// Ajuste para não mudar o comportamento, já vinha setado o primeiro registro da tabela do tipo de entrega, agora só vai vir setado se a global estiver como S
		// No VEN012->gerarNotaFiscal tem uma validação que se tiver como S é obrigatório escolher o tipo frete (informação não vai para o XML, apenas impressão DANFE)
		// E este dado é utilizado na etiqueta despacho, então poderia ir com informação divergente
		$tipoEntrega  = ($g['C031']['exibirTipoFreteNaNF'] == 'S') ? false : true ;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'cabecalho' => array(
					'T005_Id' => array('name' => 'T005_Id','label'=>'Pedido', 'type' => 'text', 'readonly' => true, 'length' => array(10,60)),
					'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa','label'=>'Cliente Pedido', 'type' => 'select', 'disabled' => true, 'reject' => true,
						'select' => gGeraSelect('D024','D024_Id','D024_Nome_Empresa',"WHERE D024_Id = '{$retorno['T005_D024_Id']}'")
					),
					/*
					'T005_D024_Id_Nfe' => array('name' => 'T005_D024_Id_Nfe','label'=>'Cliente Emissão NFe', 'type' => 'select', 'disabled' => true, 'reject' => true,
						'select' => gGeraSelect('D024','D024_Id','D024_Nome_Empresa',"WHERE D024_Id = '{$retorno['T005_D024_Id_Nfe']}'")
					),
					'T005_D024_Id_Contas' => array('name' => 'T005_D024_Id_Contas','label'=>'Cliente Contas', 'type' => 'select', 'disabled' => true, 'reject' => true,
						'select' => gGeraSelect('D024','D024_Id','D024_Nome_Empresa',"WHERE D024_Id = '{$retorno['T005_D024_Id_Contas']}'")
					), */
					'Endereco' => array('name' => 'Endereco', 'label' => 'Endereço', 'value'=>$retorno2['D024_Endereco'].', '.$retorno2['D024_Numero'].' '.$retorno2['D024_Complemento'], 'type' => 'text','reject' => true, 'disabled'=>true,'length' => array(40,60)),
					'Bairro' => array('name' => 'Bairro', 'label' => 'Bairro', 'value'=>$retorno2['D024_Bairro'].' - '.$retorno2['D024_Cep'], 'type' => 'text','reject' => true, 'disabled'=>true,'length' => array(40,60)),
					'Cidade' => array('name' => 'Cidade', 'label' => 'Cidade', 'value'=>$cidade['D020_Nome_Cidade'].' - '.$estado['D018_UF'], 'type' => 'text','reject' => true, 'disabled'=>true),
                    'T005_Data_Emissao' => array('name' => 'T005_Data_Emissao','label'=>'Data Pedido', 'type' => 'data', 'disabled' => true, 'length' => array(10,10)),
					'CFOP' => array('name' => 'CFOP', 'label' => 'CFOP', 'value'=>$CFOPCodigo.' - '.$CFOPDescricao, 'type' => 'text', 'disabled'=>true,'reject' => true, 'length' => array(60, 60)),
					'T005_D148_Id_Entrega' => array('name' => 'T005_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'disabled'=>true, 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005_D148_Id_Cobranca' => array('name' => 'T005_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'disabled'=>true, 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005A_D148_Id_Redespacho' => array('name' => 'T005A_D148_Id_Redespacho', 'label' => 'Endereço Redespacho', 'type' => 'select', 'disabled'=>true, 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					/*
					'T005_Flag_Revenda_Consumidor' => array('name' => 'T005_Flag_Revenda_Consumidor','label'=>'Tipo', 'type' => 'select', 
						'select' => array(
							array('title' => 'REVENDA', 'value' => '1'), 
							array('title' => 'CONS. ATACADO', 'value' => '2'), 
							array('title' => 'CONS. VAREJO', 'value' => '3'), 
						),
					),
					*/
					'T005_Valor_Total_Produtos' => array('name' => 'T005_Valor_Total_Produtos','label'=>'Valor produtos', 'type' => 'moeda', 'readonly' => true, 'length' => array(15,20)),
					'T005_Valor_Total_IPI' => array('name' => 'T005_Valor_Total_IPI','label'=>'IPI', 'type' => 'moeda', 'readonly' => true, 'length' => array(15,20)),
					'T005_Valor_Total_ICMS' => array('name' => 'T005_Valor_Total_ICMS','label'=>'ICMS', 'type' => 'moeda', 'readonly' => true, 'length' => array(15,20)),
					'T005_Valor_ICMS_Substituicao_Retencao' => array('name' => 'T005_Valor_ICMS_Substituicao_Retencao','label'=>'ICMS ST', 'type' => 'moeda', 'readonly' => true, 'length' => array(15,20)),
                    'T006_Valor_ICMS_Credito' => array('name' => 'T006_Valor_ICMS_Credito', 'value' => $mT006['T006_Valor_ICMS_Credito'], 'label' => 'ICMS Crédito', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true','reject' => true),
					'T005_Valor_Total' => array('name' => 'T005_Valor_Total','label'=>'Valor total', 'type' => 'moeda', 'readonly' => true, 'length' => array(15,20)),
					'T005_Valor_Total_Trib_Transparencia' => array('name' => 'T005_Valor_Total_Trib_Transparencia','label'=>'Valor total trib. (Lei Transp)', 'type' => 'moeda', 'readonly' => true, 'length' => array(15,20)),
					// 'T007_T075_Id' => array('name' => 'T007_T075_Id','label'=>'DI', 'type' => 'select', 'reject' => true,
						// 'select' => gGeraSelect('T075','T075_Id','T075_Numero_DI','', true)
					// ),
					'T005_Data_Saida' => array('name' => 'T005_Data_Saida','label'=>'Data Saída', 'type' => 'data', 'length' => array(10,10)),
					'T005_Flag_ACP' => array('name' => 'T005_Flag_ACP','label'=>'Documento', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),
							array('title' => 'NFCe', 'value' => 8),
						),
					),
					'Numero_Nota' => array('name' => 'Numero_Nota','label'=>'Número NF', 'type' => 'text', 'length' => array(10,10), 'align'=>'right', 'style'=>'float:right', 'reject' => true),
					'T005_Observacao' => array('name' => 'T005_Observacao','label'=>'Observação Cliente', 'type' => 'textarea','extra' => 'title="Este campo não será impresso na DANFE"', 'length' => array(3,65)),   
					'T005_Observacao_Nota_Fiscal' => array('name' => 'T005_Observacao_Nota_Fiscal','label'=>'Observação Nota Fiscal','extra' => 'title="Apenas este campo será impresso na DANFE"', 'type' => 'textarea', 'length' => array(4,65)),
					'T005_Id' => array('name' => 'T005_Id','label'=>'Data Saída', 'value'=>$T005_Id, 'type' => 'hidden', 'length' => array(10,10)),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'T005_Flag_Destino_Produto' => array('name' => 'T005_Flag_Destino_Produto','label'=>'Destino', 'disabled' => true, 'type' => 'select',
                        'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
							array('title' => '3 - CONSUMO', 'value' => "3"),
							array('title' => '4 - ESPECIAL', 'value' => "4"),
							array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
							array('title' => '7 - DEMONSTRAÇÃO', 'value' => "7"),
							array('title' => '8 - CONSIGNAÇÃO', 'value' => "8"),
							array('title' => '9 - VENDA FUTURA', 'value' => "9"),
							array('title' => '10 - VENDA CONTA E ORDEM', 'value' => "10"),
							array('title' => '11 - VENDA PRÓ-EMPREGO', 'value' => "11"),
							array('title' => '12 - REMESSA PARA CONSERTO', 'value' => "12"),
							array('title' => '13 - REMESSA PARA INDUSTRIALIZAÇÃO', 'value' => "13"),
							array('title' => '14 - OUTRAS SAÍDAS', 'value' => "14"),
							array('title' => '15 - AMOSTRA', 'value' => "15"),
							array('title' => '16 - DOAÇÃO', 'value' => "16"),
							array('title' => '17 - VENDA DE ATIVO IMOBILIZADO', 'value' => "17"),
							array('title' => '18 - TRANSFERÊNCIA ENTRE EMPRESAS', 'value' => "18"),
							array('title' => '19 - REMESSA TRIANGULAR', 'value' => "19"),
							array('title' => '20 - REMESSA VENDA FUTURA', 'value' => "20"),
                        )
                    ),
				),
				'frete'	=> array(
					'T005_D022_Id' => array('name' => 'Consulta_T005_D022_Id','label'=>'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D018_UF,D022_Cnpj','LEFT JOIN D018 ON D018_Id=D022_D018_Id'),'length' => array(35,50), 'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\""),
					'T005_Flag_Frete' => array('name' => 'T005_Flag_Frete','label'=>'Frete', 'type' => 'select', 'align'=>'right', 'style'=>'float:right',
						'select' => array(
							array('title' => '0 - Contratação por conta do Remetente', 'value' => '0'),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => '1'),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => '2'),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => '3'),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => '4'),							
							array('title' => '9 - Sem frete', 'value' => '9'),
						),
					),
					'T005_Valor_Frete' => array('name' => 'T005_Valor_Frete','label'=>'Valor Frete', 'type' => 'moeda', 'length' => array(15,20)),
					'T005_D036_Id' => array('name' => 'T005_D036_Id','label'=>'Tipo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 
						'select' => gGeraSelect('D036', 'D036_Id', 'D036_Tipo_Transporte', '', $tipoEntrega),					
					),
					'T005_Valor_Despesas_Acessorias' => array('name' => 'T005_Valor_Despesas_Acessorias','label'=>'Desp. Acessórias', 'type' => 'moeda', 'value'=> '', 'casasDecimais' => '2', 'length' => array(12,10)),   
					'T005_Transportadora_Placa_Veiculo' => array('name' => 'T005_Transportadora_Placa_Veiculo','label'=>'Placa', 'type' => 'text', 'align'=>'right', 'style'=>'padding-left:88px;',  'length' => array(10,20)),   
					'T005_Estado_Veiculo' => array('name' => 'T005_Estado_Veiculo','label'=>'UF', 'type' => 'select', 'align'=>'right', 'style'=>'float:right',
						'select' => gGeraSelect('D018','D018_Id','D018_UF','',true),
					),
					'T005_Quantidade_Volumes' => array('name' => 'T005_Quantidade_Volumes','label'=>'Volumes', 'type' => 'text', 'length' => array(15,20)),   
					'T005_Especie' => array('name' => 'T005_Especie','label'=>'Espécie', 'type' => 'text', 'align'=>'right', 'style'=>'float:right', 'length' => array(15,20)),   
					'T005_Marca' => array('name' => 'T005_Marca','label'=>'Marca', 'type' => 'text', 'length' => array(15,20)),   
					'T005_Numero_Caixas' => array('name' => 'T005_Numero_Caixas','label'=>'Num. Caixas', 'type' => 'text','align'=>'right', 'style'=>'float:right', 'length' => array(15,20)),
					'T005_Peso_Bruto' => array('name' => 'T005_Peso_Bruto','label'=>'Peso Bruto', 'type' => 'moeda', 'casasDecimais' => 3,'length' => array(15,20)),
					'T005_Peso_Liquido' => array('name' => 'T005_Peso_Liquido','label'=>'Peso Líquido', 'type' => 'moeda', 'casasDecimais' => 3, 'length' => array(15,20)),
					'T005_Id' => array('name' => 'T005_Id','label'=>'T005_Id', 'type' => 'hidden', 'value'=>$T005_Id, 'length' => array(3,65)),
					'T005_Flag_Totalizar_Peso' => array('name' => 'T005_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #T005_Flag_ACP').change(function() {
							if (($(this).val() == 7) || ($(this).val() == 5)) {
								$('#{$g['divId']} #Numero_Nota').focus();
								$('#{$g['divId']} .formEditor-Numero_Nota').show();
							}else{
								$('#{$g['divId']} .formEditor-Numero_Nota').hide();
							}
					});
					$('#{$g['divId']} #T005_Flag_ACP').trigger('change');
			} </script></div>
EOT;
        
        $dataSaidaDefault = $g['C031']['dataSaidaDefault'];
        $T005_Data=mysql_query("SELECT T005_Data_Saida, T005_Quantidade_Volumes from T005 WHERE T005_Id='{$T005_Id}'");
        $retorno=mysql_fetch_array($T005_Data);
        if($retorno['T005_Data_Saida']=='0000-00-00' || empty($retorno['T005_Data_Saida'])){
        	if (!empty($dataSaidaDefault)) {
        		eval("\$str = {$dataSaidaDefault};");
        	} else {
        		$str = '';
        	}
            echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} #T005_Data_Saida').val('{$str}');
            } </script></div>
EOT;
        }

        if(($retorno['T005_Quantidade_Volumes'] == 0 || empty($retorno['T005_Quantidade_Volumes'])) && ($g['C031']['quantidadePadraoVolumePedido'] > 0 && !empty($g['C031']['quantidadePadraoVolumePedido']))){
            echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} #T005_Quantidade_Volumes').val('{$g['C031']['quantidadePadraoVolumePedido']}');
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
				//mysql_query("SET AUTOCOMMIT=0");
				//mysql_query("START TRANSACTION");
				$sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form);

				if (isset($r_salvarSemGerar) && $r_salvarSemGerar == 'S') {
					if ($sql) {
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							dialogConfirm('Alterações salvas', 'Os dados do pedido foram alterados sem gerar a nota fiscal.');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
					} else {
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							dialogConfirm('Erro ao salvar', 'Não foi possível salvar as alterações do pedido. Nenhuma nota fiscal foi gerada.');
						} </script></div>
EOT;
					}
				} else {
				if($g['C031']['validarNFEGerarNotaFiscal']=='S') {
					require_once("bibliotecas/validadorNFe/validadorNFe.php");
					$validadorNFe = new validadorNFe();
					$request = $validadorNFe->validadorNFe(null, $T005_Id, null);
				} else {
					$request = true;
				}
			
				if($request === true){
						$T005_Flag_ACP = $form->campoValorEnviado('T005_Flag_ACP');
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
							idProgressBar = dialogAlert('Gerando Nota Fiscal',"<span id='progressBar'><b><br>0% Validando Formulário</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

							$.getJSON('/ven/ven012/form_func-ajax/gerarNFValidacaoFormulario/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
								if (request.code) {
									$('#progressBar').html('<br><b><font color="green">100% Formulário Validado &#10004;</font></b></br>' + '<b>0% Totalizando produtos do pedido</b></br>');
									ven012TotalizarPedidoLoop(idProgressBar,T005_Id, 1); 

									function ven012TotalizarPedidoLoop(idProgressBar,T005_Id,  inicio){
						                $.getJSON('/ven/ven012/form_func-ajax/gerarNFTotalizarPedido/?ajax=true&T005_Id=' + {$T005_Id} + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
						                    if (request.concluido == 0) {
						                    	$('#progressBar').html('<br><b><font color="green">100% Formulário Validado &#10004;</font></b></br><b>' + request.percentual + '%</b>' + '<b> Totalizando produtos do pedido</b></br>');
												ven012TotalizarPedidoLoop(idProgressBar,T005_Id, 0); 
						                    }else{
						                    	if(request.code){
													$('#progressBar').html('<br><b><font color="green">100% Formulário validado &#10004;</font></b></br>' + '<b><font color="green">'+ request.percentual + '% Produtos do pedido totalizados &#10004;</font></b></br>' + '<b>0% Gerando Nota Fiscal</b></br>');
													$.getJSON('/ven/ven012/form_func-ajax/gerarNotaFiscal/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
														if(request.code){
															$('#progressBar').html('<br><b><font color="green">100% Formulário validado &#10004;</font></b></br>' + '<b><font color="green">100% Produtos do pedido totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota Fiscal Gerada &#10004;</font></b></br>'+'<b>0% Totalizando produtos da nota</b></br>');
															ven012TotalizarLoop(idProgressBar,T005_Id, 1);

															function ven012TotalizarLoop(idProgressBar,T005_Id,inicio){
												                $.getJSON('/ven/ven012/form_func-ajax/gerarNFTotalizar/?ajax=true&T005_Id=' + {$T005_Id} + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
												                    if (request.concluido == 0) {
												                    	$('#progressBar').html('<br><b><font color="green">100% Formulário validado &#10004;</font></b></br>' + '<b><font color="green">100% Produtos do pedido totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota Fiscal Gerada &#10004;</font></b></br><b>' + request.percentual + '% Totalizando produtos da nota</b></br>');
																		ven012TotalizarLoop(idProgressBar,T005_Id, 0); 
												                    }else{
												                    	if(request.code){
						                    								if(request.validacaoST){
																				$('#dc-' + idProgressBar).dialog("destroy").remove();
																				$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
																				divRefresh('{$g['divIdParent']}');
						                    									dialogConfirm('Nota Fiscal Gerada com sucesso!','<br><b><font color="green">100% Formulário validado &#10004;</font></b></br>' + '<b><font color="green">100% Produtos do pedido totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota Fiscal Gerada &#10004;</font></b></br>'+ '<b><font color="green">'+request.percentual +'% Produtos da Nota totalizados &#10004;</font></b></br>' + '<br><b><font color = blue>Titulo a pagar gerado automaticamente para GNRE-ST</font></b>');
						                    								}else{
						                    									$('#dc-' + idProgressBar).dialog("destroy").remove();
						                    									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						                    									divRefresh('{$g['divIdParent']}');
						                    									dialogConfirm('Nota Fiscal Gerada com sucesso!','<br><b><font color="green">100% Formulário validado &#10004;</font></b></br>' + '<b><font color="green">100% Produtos do pedido totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota Fiscal Gerada &#10004;</font></b></br>'+ '<b><font color="green">'+request.percentual +'% Produtos da Nota totalizados &#10004;</font></b></br>');
						                    								}
						                    								if({$T005_Flag_ACP} == 3){
													                    		nova_janela('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id='+request.T007_Id+'',0,0,880,650,'yes','no','no','no','yes','no','Impressão ACP');	
													                    	}
												                    	}else{
												                    		var mensagem = "<b><font color='red'>Erro na totalização dos produtos da nota</font></b>";
																			$('#dc-' + idProgressBar).dialog("destroy").remove();
																			$('#{$g['divId']}').hideLoading();
																			var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																			dialogConfirm('Erro', mensagem, \$buttons);	
												                    	}
													                }
												                });
												            }	
														}else if(request.retornarPedidoSeparacao){
															var mensagem = "<b><font color='red'>Um ou mais produtos não foram separados corretamente</font></b><br><br>"+request.data+"<br><br>Selecione uma opção para continuar";
															$('#dc-' + idProgressBar).dialog("destroy").remove();
															if({$g['C031']['statusInicialPedido']}==3 || '{$g['C031']['desabilitarSeparacao']}'=='S'){
																var \$buttons = {	
																	"Refazer separação": function() {
																		$('#{$g['divId']}').showLoading();
																		$.getJSON('/ven/ven012/form_func-ajax/separarPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request) {
																			if (request.code) {
																				divRefresh('{$g['divIdParent']}');
																				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																				dialogConfirm('Ok', request.data, \$buttons);
																			} else {
																				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																				dialogConfirm('Erro', request.data, \$buttons);
																			}
																			$('#{$g['divId']}').hideLoading();
																			divRefresh('{$g['divIdParent']}');
																		});
																		$(this).dialog("destroy").remove();
																	},
																	"Fechar": function() { $(this).dialog("destroy").remove(); }
																};
															}else{
																var \$buttons = {	
																	"Retornar para separação": function() {
																		$('#{$g['divId']}').showLoading();
																		$.getJSON('/ven/ven012/form_func-ajax/retornarPedidoSeparacao/?ajax=true&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request) {
																			if (request.code) {
																				divRefresh('{$g['divIdParent']}');
																				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																				dialogConfirm('Ok', request.data, \$buttons);
																			} else {
																				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																				dialogConfirm('Erro', request.data, \$buttons);
																			}
																			$('#{$g['divId']}').hideLoading();
																			divRefresh('{$g['divIdParent']}');
																		});
																		$(this).dialog("destroy").remove();
																	},
																
																"Fechar": function() { $(this).dialog("destroy").remove(); }
																};
															}
															$('#dc-' + idProgressBar).dialog("destroy").remove();
															$(this).dialog("destroy").remove();
															dialogConfirm('Atenção', mensagem, \$buttons);
														}else{
															var mensagem = request.data;
															$('#dc-' + idProgressBar).dialog("destroy").remove();
															dialogConfirm('Atenção', mensagem, \$buttons);
														}

													});
												}else{
													var mensagem = "<b><font color='red'>Erro na totalização dos produtos do pedido (" + request.data + ")</font></b>";
													$('#dc-' + idProgressBar).dialog("destroy").remove();
													$('#{$g['divId']}').hideLoading();
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
													dialogConfirm('Erro', mensagem, \$buttons);	
												}	
						                    } 
						                });
						            }	                  
								}else{
									var mensagem = '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">'+request.data+request.erroRoolback+'</textarea>';
									$('#dc-' + idProgressBar).dialog("destroy").remove();
									$('#{$g['divId']}').hideLoading();
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro validações', mensagem, \$buttons);
								} 
							});
						} </script></div>
EOT;
				}else{
					$request = json_encode($request);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var buttons = {
					    		"Fechar": function() { 
								$(this).dialog("destroy").remove();
								}
							};
							var request = $request;
	 						dialogConfirm('Alertas de Validação NFe', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' + request + '</textarea>', buttons);
	 					}</script></div>;
EOT;
				}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				
				$sql = "SELECT D024_Endereco FROM D024 WHERE D024_Id = '{$retorno['T005_D024_Id']}';";
				
				$resultado = mysql_query($sql);
				$row = mysql_fetch_assoc($resultado);
				
				$form->campoSetarValor('D024_Endereco', $row['D024_Endereco']);
				
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,false,false,false,false);

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
	break;
	
		case 'ven012FormPagamento':
			$acaoId = (isset($r_T005_Id)) ? $r_T005_Id : false; 
			$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
			  
			//echo $T005_Id;
			//('qqqq');
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado		

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
			$form_tabela = 'T005';
			$form_campo_pk = 'T005_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'ven012FormPagamento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/ven/ven012/form/ven012FormPagamento/';

			//form editor
			$form_editor = array('geral' => '{"Total_Parcelas":{"top":7,"left":16,"width":164,"hidden":0},"selecionaPrimeiroVencimento":{"top":30,"left":16,"width":105,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":176,"width":40,"hidden":0},"primeiroVencimentoData":{"top":30,"left":176,"width":78,"hidden":0},"selecionaDias":{"top":53,"left":6,"width":105,"hidden":0},"IntervalorDias":{"top":53,"left":176,"width":40,"hidden":0},"diaFixo":{"top":53,"left":192,"width":43,"hidden":0},"Portador_D027_Id":{"top":76,"left":17,"width":165,"hidden":0},"ven012FormPagamento_btn":{"top":74,"left":240,"width":null,"hidden":0},"ven012FormAdicionarParcela_btn":{"top":74,"left":352,"width":null,"hidden":1},"T005_Observacao_Pagamento":{"top":99,"left":-2,"height":32,"width":157,"hidden":1},"ven012SalvarObservacao_btn":{"top":97,"left":288,"width":null,"hidden":1}}');

			// novo formulário
			$form = new Formulario('ven012FormPagamento');
			
			$select = array();
			$select[] = array('value' => 'A_COMBINAR', 'title' => 'SEM PARCELAS');
			$T005=mysql_query("select * from T005 where T005_Id='$T005_Id'");
			$mT005=mysql_fetch_array($T005);
			for ($i=1; $i<=24; $i++) {
				$valorParcela=$VEN012->calcularParcelaCombo($i,$mT005['T005_Valor_Total']);
				if($i=='1'){
					$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . "x" . " - " .$valorParcela, 'selected'=>true);
				}else{
					$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . "x" . " - " .$valorParcela);
				}
			}
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'reject' => true),
						//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"', 'reject' => true, 'length' => array(5,8), 'align' => 'right', 'readonly'=>'readonly'),
						'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select','select' => 
						array(
							array('title' => 'Dias', 'value' => 'Dias', 'selected' => true),
							array('title' => 'Data', 'value' => 'Data'),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,4), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'type' => 'data', 'reject' => true, 'align' => 'right'),												
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true,'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => true),
							array('title' => 'Dia Fixo', 'value' => 'F'),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,2), 'align' => 'right'),
						'Portador_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'type' => 'select', 'reject'=>true, 'select' =>
							gGeraSelect('D027', 'D027_Id', 'D027_Portador',' order by D027_Portador')
						),
						'ven012FormPagamento_btn' => array('name' => 'ven012FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'ven012FormAdicionarParcela_btn' => array('name' => 'ven012FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
						'T005_Observacao_Pagamento' => array('name' => 'T005_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
						'ven012SalvarObservacao_btn' => array('name' => 'ven012SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'style' => 'float:right; margin-right:120px; margin-top:15px;', 'reject' => true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'T005_Id' => array('name' => 'T005_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T005_Id, 'reject' => true),
					)
				)
			);

		if($g['C031']['usarFretePrimeiraParcela'] == 'S'){
			$formArray['campos']['geral']['usarFreteParcela'] = array('name' => 'usarFreteParcela', 'label'=>'Frete primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
			$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
		}	
		$form->definirFormularioArray($formArray);
			     
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven012FormAdicionarParcela_btn').unbind('click');
				$('#ven012FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven012/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
						if (request.code) {															
							divRefresh("{$r_divReload}");
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				$('#ven012SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven012/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&callback=?', function(request) {
						if (request.code) {															
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				$('#{$g['divId']} #selecionaDias').change(function() {
					if ($(this).val() == 'I') {
						$('#{$g['divId']} #diaFixo').val('').hide();
						$('#{$g['divId']} #IntervalorDias').show();
						$('#{$g['divId']} #IntervalorDias').focus();
					} else {
						$('#{$g['divId']} #IntervalorDias').val('').hide();
						$('#{$g['divId']} #diaFixo').show();
						$('#{$g['divId']} #diaFixo').focus();
					}
				});
				$('#{$g['divId']} #selecionaDias').trigger('change');
				
				$('#{$g['divId']} #selecionaPrimeiroVencimento').change(function() {
					if ($(this).val() == 'Dias') {
						$('#{$g['divId']} #primeiroVencimentoData').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoNum').show();
						$('#{$g['divId']} #primeiroVencimentoNum').focus();
					} else {
						$('#{$g['divId']} #primeiroVencimentoNum').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoData').show();
						$('#{$g['divId']} #primeiroVencimentoData').focus();
					}
				});
				$('#{$g['divId']} #selecionaPrimeiroVencimento').trigger('change');	

				
				// calcula e mostra o valor das parcelas (simulação)
				/**
				var changeHandler = function() {					
					$.getJSON('/ven/ven012/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
						if (request.code) {															
							//divRefresh("{$r_divReload}");
							$('#{$g['divId']} #valorParcelaSimulacao').val(request.data);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", request.data, buttons)
						}
					});										
				}	
				$('#{$g['divId']} #Total_Parcelas').change(changeHandler).keyup(changeHandler);
				*/								
		    } </script></div>
EOT;
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto			
					$retorno = $VEN002->atualizarParcelas($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
                                       //divRefresh("{$g['divId']}", true);
                                       divRefresh('{$form->campoValorEnviado('divReload')}', true);
                               	}
							} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro ao gerar Pacelas:<br />' . $retorno);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,false,false,false,false);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');


		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento FROM D024 LEFT JOIN T005 ON T005_D024_Id = D024_Id WHERE T005_Id = '{$T005_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 110px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento</span> {$D024['D024_Instrucao_Pagamento']}	
			</div>	
EOT;

	break;
	
	case 'ven012FormDetalhesCheque':
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
		$form_tabela = 'T090';
		$form_campo_pk = 'T090_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven012FormDetalhesCheque_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven012/form/ven012FormDetalhesCheque/';
				
		// novo formulário
		$form = new Formulario('ven012ContentDetalhesCheque');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T090_Numero_Cheque' => array('name' => 'T090_Numero_Cheque', 'label' => 'Número', 'type' => 'text', 'length' => array(20, 120)),
					'T090_Data_Cheque' => array('name' => 'T090_Data_Cheque', 'label' => 'Data', 'type' => 'data', 'length' => array(20, 120)),					
					'T090_Agencia_Cheque' => array('name' => 'T090_Agencia_Cheque', 'label' => 'Agência', 'type' => 'text', 'length' => array(20, 120)),
					'T090_Conta_Corrente_Cheque' => array('name' => 'T090_Conta_Corrente_Cheque', 'label' => 'Conta Corrente', 'type' => 'text', 'length' => array(20, 120)),					
					'T090_Banco_Cheque' => array('name' => 'T090_Banco_Cheque', 'label' => 'Banco', 'type' => 'text', 'length' => array(50, 120)),									
					'T090_Nome_Cheque' => array('name' => 'T090_Nome_Cheque', 'label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),					
					'T090_Cidade_Cheque' => array('name' => 'T090_Cidade_Cheque', 'label' => 'Cidade', 'type' => 'text', 'length' => array(50, 120)),
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
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
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
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
				
	break;	
    
     case 'ven012FormSelecionarEmpresa':
            /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
	
		$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
		$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;

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
        $enviar_btnId = 'ven012SelecionarEmpresa_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/ven/ven012/form/ven012FormSelecionarEmpresa/';

		$form_editor = array(
            'geral' => '{"C004_Id_Novo":{"top":7,"left":-0.5625,"width":300,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven012FormSelecionarEmpresa');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'C004_Id_Novo' => array('name' => 'C004_Id_Novo','label'=>'Empresa', 'type' => 'select','extra' => 'style="width:300px;"' ,
                        'select' => gGeraSelect('C004','C004_Id','C004_Nome_Empresa',"where C004_Flag_Ativo='S'", false, $g['empresaAtual'])
                    ),
                    'T005_Id_Antigo' => array('name' => 'T005_Id_Antigo', 'label' => 'T005_Id_Antigo', 'type' => 'hidden','value' => @$r_T005_Id),
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
                $C004_Id_Novo = $form->campoValorEnviado('C004_Id_Novo');
                $T005_Id_Antigo = $form->campoValorEnviado('T005_Id_Antigo');
				if($mudarFilial == true){
					$retorno = $VEN012->est012MudarFilial($T005_Id_Antigo,$C004_Id_Novo);
				} else if($gerarNF == true){
					$retorno = $VEN012->est012GerarNFeMarcados($T005_Id_Antigo,$C004_Id_Novo);
				} elseif($r_dividirPedidoEmpresa == true){
					$T005 = mysql_query("SELECT T005_D024_Id from T005 WHERE T005_Id='{$T005_Id_Antigo}'");
			        $mT005 = mysql_fetch_array($T005);
			        $D024_Id = $mT005['T005_D024_Id'];
			        $r_sql = urldecode($r_sql);
					$retorno = $VEN012->est012DividirPedidoEmpresa($T005_Id_Antigo, $C004_Id_Novo, $r_sql);
				} else {
					$retorno = $VEN012->est012DividirPedido($T005_Id_Antigo,$C004_Id_Novo);
				}
                
            if ($retorno===true) {
                // Imprime uma mensagem e fecha a janela
                if($mudarFilial == true){
                    $alert .= gGeraAlertMsg('Operação OK', 'Filial alterada com sucesso!', false);
			    	$link = "";
		    	} else if($gerarNF == true){
		    		$alert .= gGeraAlertMsg('Operação OK', 'Nota Gerada com sucesso!', false);
					$link = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal - ID: ' + '{$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [900,550]);";
		    	} else {
		    		if($C004_Id_Novo != $g['empresaAtual']){
		    			$alert .= gGeraAlertMsg('Operação OK', 'Pedido dividido com sucesso!<br><br>Verifique o pedido na outra empresa.', false);
		    		} else {
			    		$alert .= gGeraAlertMsg('Operação OK', 'Pedido dividido com sucesso!', false);
						$link = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent('{$novoT005_Id}') + '&tabela=T005', [1000,615]);";
					}
		    	}
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                setTimeout(function() {
                                {$alert}
                                {$link}
								divRefresh('{$r_divRefresh}');
                                }, 150);
                              });
                            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                            divRefresh('{$g['divIdParent']}', true);
                        } </script></div>
EOT;
                }else{
                   $alert .= gGeraAlertMsg('Erro',"{$retorno}", false);
                   echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            {$alert}
                        } </script></div>
EOT;
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

    break;
    
    
}
?>
