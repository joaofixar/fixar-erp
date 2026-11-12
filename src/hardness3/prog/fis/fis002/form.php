<?php
namespace hardness;

require_once("bibliotecas/classes/FIS002.php");
$FIS002 = new FIS002();

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/

	case 'fis002FormICMS':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D053';
		$form_campo_pk = 'D053_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis002FormIcmsBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis002/form/fis002FormICMS/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D053_Id' => array('name' => 'D053_Id', 'label' => 'Id', 'type' => 'hidden', 'value'=>$acaoId),
					'D053_D018_Id' => array('name' => 'D053_D018_Id', 'label'=> 'UF', 'disabled' => true, 'type' => 'select', 'extra '=> 'style="width:80px;"', 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF',"order by D018_UF"),
					),
					'D053_Aliquota_ICMS_1' => array('name' => 'D053_Aliquota_ICMS_1', 'label' => 'Aíquota ICMS 1', 'type' => 'moeda', 'length' => array(10,30)),
					'D053_Percentual_Reducao_ICMS_1' => array('name' => 'D053_Percentual_Reducao_ICMS_1', 'label' => 'Percentual Redução ICMS 1', 'type' => 'moeda', 'length' => array(10,30)),
					'D053_Aliquota_ICMS_2' => array('name' => 'D053_Aliquota_ICMS_2', 'label' => 'Alíquota ICMS 2', 'type' => 'moeda', 'length' => array(10,30)),
					'D053_Percentual_Reducao_ICMS_2' => array('name' => 'D053_Percentual_Reducao_ICMS_2', 'label' => 'Percentual Redução ICMS 2', 'type' => 'moeda', 'length' => array(10,30)),
					'D053_Flag_ST' => array('name' => 'D053_Flag_ST', 'label'=>'Subs. Tributária', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					'D053_ST_ICMS_Interno' => array('name' => 'D053_ST_ICMS_Interno', 'label' => 'ICMS Interno', 'type' => 'moeda', 'length' => array(15,30)),
					'D053_ST_ICMS_Inter_Estadual' => array('name' => 'D053_ST_ICMS_Inter_Estadual', 'label' => 'ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15,30)),   
					//'D053_ST_VA' => array('name' => 'D053_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(15,30)),
					//'D053_ST_VA_2' => array('name' => 'D053_ST_VA_2', 'label' => 'Margem Valor Agregado 2', 'type' => 'moeda', 'length' => array(15,30)),
					'D053_Observacao' => array('name' => 'D053_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(3,40)),
					'D053_Flag_Ajusta_MVA' => array('name' => 'D053_Flag_Ajusta_MVA', 'label'=>'Ajusta MVA', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
				)
			)
		);
        $form = new Formulario('fis002FormICMS');
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fis002FormIcmsBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fis002FormICMSExcessao':
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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D143';
		$form_campo_pk = 'D143_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis002FormRegra_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis002/form/fis002FormICMSExcessao/';

		// novo formulário
		$form = new Formulario('fis002FormRegra');

		//form editor
        $form_editor = array(
            'geral' => '{"D143_D006_Id":{"top":7,"left":203.328125,"width":191,"hidden":0},"D143_C004_Codigo_Regime_Tributario":{"top":30,"left":147.0625,"width":192,"hidden":0},"D143_D024_Flag_Contribuinte":{"top":53,"left":150.515625,"width":192,"hidden":0},"D143_Empresas":{"top":76,"left":60.125,"width":184,"hidden":0},"D143_Dentro_Fora_Estado":{"top":122,"left":157.46875,"width":100,"hidden":0},"D143_Pessoa_Fisica_Juridica":{"top":145,"left":197.1875,"width":100,"hidden":0},"D143_Estados_Especificos":{"top":99,"left":44.21875,"width":184,"hidden":0},"D143_UF_Tem_ST":{"top":168,"left":174.59375,"width":100,"hidden":0},"D143_NCM_Tem_ST":{"top":191,"left":164.203125,"width":100,"hidden":0},"D143_Flag_UF_Convenio_NCM_ST":{"top":214,"left":118.34375,"width":100,"hidden":0},"D143_D005_Id":{"top":237,"left":208.828125,"width":191,"hidden":0},"D143_Produzido_Adquirido":{"top":260,"left":171.3125,"width":192,"hidden":0},"D143_Origem_Mercadoria":{"top":283,"left":57.671875,"width":184,"hidden":0},"D143_Flag_Cabecalho_Item":{"top":329,"left":179.875,"width":100,"hidden":0},"D143_Flag_Inativar":{"top":352,"left":197.8125,"width":100,"hidden":0},"D143_Flag_Preferencial_Sobre_D024_D006_Id":{"top":375,"left":73.90625,"width":100,"hidden":0},"D143_C004_Id":{"top":398,"left":189.859375,"width":191,"hidden":0},"D143_Flag_Revenda_Consumidor":{"top":421,"left":36.890625,"width":184,"hidden":0},"D143_Peso_Minimo":{"top":444,"left":25.171875,"width":56,"hidden":0},"D143_Percentual_Reducao_ICMS_1":{"top":444,"left":222.46875,"width":56,"hidden":0},"D143_Peso_Maximo":{"top":467,"left":22.734375,"width":56,"hidden":0},"D143_Aliquota_ICMS_2":{"top":467,"left":281.78125,"width":56,"hidden":0},"D143_Aliquota_ICMS_1":{"top":490,"left":25.78125,"width":56,"hidden":0},"D143_Percentual_Reducao_ICMS_2":{"top":490,"left":222.46875,"width":56,"hidden":0},"D143_ST_ICMS_Interno":{"top":513,"left":41.078125,"width":56,"hidden":0},"D143_ST_ICMS_Inter_Estadual":{"top":513,"left":263.453125,"width":56,"hidden":0},"D143_MVA":{"top":536,"left":82.859375,"width":56,"hidden":0},"D143_Flag_ST":{"top":536,"left":252.84375,"width":96,"hidden":0},"D143_Flag_MVA_Ajustado":{"top":559,"left":256.09375,"width":96,"hidden":0}}',
        );

		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'D143_D006_Id' => array('name' => 'D143_D006_Id', 'label'=>'CFOP', 'extra'=>'style="width:200px"','type' => 'select','select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo="N" order by D006_Codigo_CFOP', true)
					), 			
					'D143_Empresas' => array('name' => 'D143_Empresas','label'=>'ID das empresas<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D143_C004_Id' => array('name' => 'D143_C004_Id', 'label'=>'Empresa', 'type' => 'select','select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado', 'order by C004_Id', true)
					), 			
					'D143_Flag_Revenda_Consumidor' => array('name' => 'D143_Flag_Revenda_Consumidor','label'=>'Revenda/Consumidor<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D143_Origem_Mercadoria' => array('name' => 'D143_Origem_Mercadoria','label'=>'Origens Produtos<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D143_C004_Codigo_Regime_Tributario' => array('name' => 'D143_C004_Codigo_Regime_Tributario','label'=>'Regime Tributário','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3")
						)
					),			
					'D143_D024_Flag_Contribuinte' => array('name' => 'D143_D024_Flag_Contribuinte', 'label' => 'Tipo Contribuinte','extra' =>'style="width:200px"', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'Não contribuinte', 'value' => '9'),   
							array('title' => 'Consumidor Final', 'value' => '1'),   
							array('title' => 'Revenda', 'value' => '2'),   
							array('title' => 'Produtor rural', 'value' => '3'),
							array('title' => 'Simples', 'value' => '4'),   
							array('title' => 'Industria', 'value' => '5'),
							array('title' => 'Industria Triangular', 'value' => '8')   
						)
					),
					'D143_Produzido_Adquirido' => array('name' => 'D143_Produzido_Adquirido','label'=>'Tipo Produto','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'PRODUZIDO/IMPORTADO', 'value' => "P"),
							array('title' => 'AQUISIÇÃO NACIONAL', 'value' => "A"),
						)
					),				
					'D143_Estados_Especificos' => array('name' => 'D143_Estados_Especificos','label'=>'Estados Específicos<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D143_Dentro_Fora_Estado' => array('name' => 'D143_Dentro_Fora_Estado','label'=>'Dentro/Fora UF','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'DENTRO', 'value' => "D"),
							array('title' => 'FORA', 'value' => "F"),
						)
					),																																								
					'D143_UF_Tem_ST' => array('name' => 'D143_UF_Tem_ST','label'=>'UF tem ST?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D143_D005_Id' => array('name' => 'D143_D005_Id', 'label'=>'NCM', 'extra'=>'style="width:200px"','type' => 'select','select' => 
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal,D005_Observacao', '', true)
					), 			
					'D143_NCM_Tem_ST' => array('name' => 'D143_NCM_Tem_ST','label'=>'NCM tem ST?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D143_Flag_UF_Convenio_NCM_ST' => array('name' => 'D143_Flag_UF_Convenio_NCM_ST','label'=>'Convênio NCM/UF ST?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D143_Pessoa_Fisica_Juridica' => array('name' => 'D143_Pessoa_Fisica_Juridica','label'=>'Pessoa','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'FÍSICA', 'value' => "PF"),
							array('title' => 'JURÍDICA', 'value' => "PJ"),
                            array('title' => 'INTERNACIONAL', 'value' => "PI"),
						)
					),	
					'D143_Flag_Cabecalho_Item' => array('name' => 'D143_Flag_Cabecalho_Item','label'=>'Tipo Regra','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'ITEM', 'value' => "I", 'selected' => true),
							array('title' => 'CABEÇALHO', 'value' => "C"),
						)
					),						
					'D143_Flag_Inativar' => array('name' => 'D143_Flag_Inativar','label'=>'Inativar','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																						
					'D143_Flag_Preferencial_Sobre_D024_D006_Id' => array('name' => 'D143_Flag_Preferencial_Sobre_D024_D006_Id','label'=>'Preferencial sobre CFOP Cliente','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																						
					'D143_Peso_Minimo' => array('name' => 'D143_Peso_Minimo', 'label'=>'Peso mínimo Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(5,5)),
					'D143_Peso_Maximo' => array('name' => 'D143_Peso_Maximo', 'label'=>'Peso máximo Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(5,5)),

					'D143_Aliquota_ICMS_1' => array('name' => 'D143_Aliquota_ICMS_1', 'label'=>'Alíquota ICMS 1', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_Percentual_Reducao_ICMS_1' => array('name' => 'D143_Percentual_Reducao_ICMS_1', 'label'=>'Percentual Redução ICMS 1', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_Aliquota_ICMS_2' => array('name' => 'D143_Aliquota_ICMS_2', 'label'=>'Alíquota ICMS 2', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_Percentual_Reducao_ICMS_2' => array('name' => 'D143_Percentual_Reducao_ICMS_2', 'label'=>'Percentual Redução ICMS 2', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_Flag_ST' => array('name' => 'D143_Flag_ST','label'=>'Subs. Tributária','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),
					'D143_ST_ICMS_Interno' => array('name' => 'D143_ST_ICMS_Interno', 'label'=>'ICMS Interno', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_ST_ICMS_Inter_Estadual' => array('name' => 'D143_ST_ICMS_Inter_Estadual', 'label'=>'ICMS Inter Estadual', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_MVA' => array('name' => 'D143_MVA', 'label'=>'MVA', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D143_Flag_MVA_Ajustado' => array('name' => 'D143_Flag_MVA_Ajustado','label'=>'MVA Ajustado?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
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
					$verificacao=$FIS002->verificaCampoEstados($form);
					if ($verificacao===true){
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
					}else{
						echo gGeraAlertMsg('Erro', 'Campo "Estados Específicos" preenchido de forma incorreta<br />Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS');
					}
				} else {
					// Insert
					$verificacao=$FIS002->verificaCampoEstados($form);
					if ($verificacao===true){
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
					}else{
						echo gGeraAlertMsg('Erro', 'Campo "Estados Específicos" preenchido de forma incorreta<br />Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS');
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
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fis002FormRegra_btn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fis002FormICMSTransporte':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D196';
		$form_campo_pk = 'D196_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis002FormIcmsTransporteBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis002/form/fis002FormICMSTransporte/';
		$form_editor = array(
			'geral'    => '{"D196_D018_Id_Origem":{"top":7,"left":55.078125,"width":95,"hidden":0},"D196_D018_Id_Destino":{"top":30,"left":53.34375,"width":96,"hidden":0},"D196_Aliquota_ICMS_Interno":{"top":53,"left":43.046875,"width":88,"hidden":0},"D196_Aliquota_ICMS_Inter_Estadual":{"top":76,"left":10.25,"width":89,"hidden":0}}',						
		);		


		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D196_Id' => array('name' => 'D196_Id', 'label' => 'Id', 'type' => 'hidden', 'value'=>$acaoId),
					'D196_D018_Id_Origem' => array('name' => 'D196_D018_Id_Origem', 'label'=> 'UF Origem', 'type' => 'select', 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF',"order by D018_UF"),
					),
					'D196_D018_Id_Destino' => array('name' => 'D196_D018_Id_Destino', 'label'=> 'UF Destino', 'type' => 'select', 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF',"order by D018_UF"),
					),
					'D196_Aliquota_ICMS_Interno' => array('name' => 'D196_Aliquota_ICMS_Interno', 'label' => 'ICMS Interno', 'type' => 'moeda'),
					'D196_Aliquota_ICMS_Inter_Estadual' => array('name' => 'D196_Aliquota_ICMS_Inter_Estadual', 'label' => 'ICMS Inter Estadual', 'type' => 'moeda'),   					
					'D196_Aliquota_ICMS_Inter_Estadual' => array('name' => 'D196_Aliquota_ICMS_Inter_Estadual', 'label' => 'ICMS Inter Estadual', 'type' => 'moeda'),   					
					'D196_C004_Id' => array('name' => 'D196_C004_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $g['empresaAtual']),					
				)
			)
		);
        $form = new Formulario('fis002FormICMSTransporte');
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
		$botoesDir .= '<button type="submit" id="fis002FormIcmsTransporteBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>