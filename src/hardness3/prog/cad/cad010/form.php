<?php
namespace hardness;
//Classes
//require_once("bibliotecas/classes/CAD010.php");
//$CAD010 = new CAD010();
require_once('prog/cad/cad010/form_func-js.php');
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: cad010tab02
	*/
	case 'cad010form01':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$typeForm = (isset($_REQUEST['typeForm'])) ? $_REQUEST['typeForm'] : false;

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
		$form_tabela = 'D109';
		$form_campo_pk = 'D109_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad010/form/cad010form01/';
		// Editor
		$form_editor = array(
			'funcionarios' => '{"D109_Nome":{"top":7,"left":72,"width":280,"hidden":0},"D109_Funcao":{"top":30,"left":63,"width":280,"hidden":0},"D109_Rg":{"top":53,"left":91,"width":75,"hidden":0},"D109_Cpf":{"top":76,"left":87,"width":125,"hidden":0},"D109_Nascimento":{"top":99,"left":25,"width":75,"hidden":0},"D109_Endereco":{"top":122,"left":60,"width":280,"hidden":0},"D109_Numero":{"top":145,"left":69,"width":75,"hidden":0},"D109_D018_Id":{"top":168,"left":93,"width":185,"hidden":0},"D109_D020_Id":{"top":191,"left":73,"width":185,"hidden":0},"D109_D030_Id":{"top":214,"left":86,"width":185,"hidden":0},"D109_Bairro":{"top":237,"left":77,"width":280,"hidden":0},"buttonSearchCep":{"top":258,"left":208,"width":null,"hidden":0},"D109_Cep":{"top":260,"left":87,"width":75,"hidden":0},"D109_DDD_Telefone":{"top":282.9999694824219,"left":65,"width":30,"hidden":0},"D109_Telefone":{"top":282.9999694824219,"left":160,"width":95,"hidden":0},"D109_Email":{"top":305.9999694824219,"left":82,"width":280,"hidden":0},"D109_Data_Admissao":{"top":328.9999694824219,"left":58,"width":75,"hidden":0},"D109_Data_Demissao":{"top":328.9999694824219,"left":251,"width":88,"hidden":0},"D109_Data_Cadastro":{"top":351.9999694824219,"left":37,"width":75,"hidden":0},"D109_Senha":{"top":351.9999694824219,"left":260,"width":88,"hidden":0},"D109_Inicio_Ausencia":{"top":374.9999694824219,"left":33,"width":75,"hidden":0},"D109_Fim_Ausencia":{"top":374.9999694824219,"left":233,"width":88,"hidden":0},"D109_Carga_Horaria":{"top":397.9999694824219,"left":9,"width":75,"hidden":0},"D109_Flag_Ativo":{"top":397.9999694824219,"left":305,"width":64,"hidden":0},"D109_Codigo_Barras":{"top":420.9999694824219,"left":16,"width":280,"hidden":0}}'
		);

		// Novo Formulário
		$form = new Formulario('form_fin002_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'funcionarios' => array(
					'D109_Nome' => array('name' => 'D109_Nome','label'=>'Nome', 'type' => 'text', 'length' => array(50, 120)),
					'D109_Rg' => array('name' => 'D109_Rg', 'label'=>'RG','type' => 'text', 'length' => array(10, 15)),
					'D109_Cpf' => array('name' => 'D109_Cpf', 'label'=>'CPF','type' => 'cpf'),
					'D109_Nascimento' => array('name' => 'D109_Nascimento', 'label'=>'Data Nascimento','type' => 'data', 'length' => array(10, 15)),
					'D109_Cep' => array('name' => 'D109_Cep', 'label'=>'CEP','type' => 'cep', 'length' => array(10,10)),
                    'buttonSearchCep' =>     array('name' => 'buttonSearchCep',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D109_Cep\", {\"EnderecoDb\": \"D109_Endereco\", \"Cep\": \"D109_Cep\", \"BairroDb\": \"D109_Bairro\", \"UfDb\": \"D109_D018_Id\",\"CidadeDb\": \"D109_D020_Id\", \"PaisDb\": \"D109_D030_Id\"})'", 'align'=>'right', 'value'=>'Buscar', 'reject'=>true),
                    'D109_Endereco' => array('name' => 'D109_Endereco', 'label'=>'Endereço','type' => 'text', 'length' => array(50, 120)),
					'D109_Numero' => array('name' => 'D109_Numero', 'label'=>'Numero','type' => 'text', 'length' => array(10,15)),
					'D109_D018_Id' => array('name' => 'D109_D018_Id', 'label'=>'Função','type' => 'select', 'length' => array(30, 50),
						'select' => gGeraSelect('D018','D018_Id','D018_UF')
					),
					'D109_D018_Id' => array('name' => 'D109_D018_Id', 'label'=>'UF','type' => 'select','extra' => 'style="width:185px;" onchange="alteraEstado(this, \''.$g['divId'].'\')"',
						'select' => gGeraSelect('D018','D018_Id','D018_UF')
					),
					'D109_D020_Id' => array('name' => 'D109_D020_Id', 'label'=>'Cidade','type' => 'select','extra' =>'style="width:185px;"', 
						'select' => gGeraSelect('D020','D020_Id','D020_Nome_Cidade')
					),
					'D109_D030_Id' => array('name' => 'D109_D030_Id', 'label' => 'País', 'type' => 'select','extra' =>'style="width:185px;"', 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)
					),
					'D109_Bairro' => array('name' => 'D109_Bairro', 'label'=>'Bairro','type' => 'text', 'length' => array(50, 120)),
					'D109_DDD_Telefone' => array('name' => 'D109_DDD_Telefone', 'label'=>'Telefone','type' => 'text', 'length' => array(1, 2)),
					'D109_Telefone' => array('name' => 'D109_Telefone', 'label'=>'','type' => 'text','align' => 'right', 'length' => array(14, 14)),
					'D109_Email' => array('name' => 'D109_Email', 'label'=>'Email','type' => 'text', 'length' => array(50, 120)),
					'D109_Data_Admissao' => array('name' => 'D109_Data_Admissao', 'label'=>'Admissão','type' => 'data', 'length' => array(10, 15)),
					'D109_Data_Demissao' => array('name' => 'D109_Data_Demissao', 'label'=>'Demissão','type' => 'data', 'length' => array(10, 15), 'align' => 'right', 'style' => 'float:right;'),
					'D109_Data_Cadastro' => array('name' => 'D109_Data_Cadastro', 'label'=>'Data Cadastro','type' => 'data','value' => date('d/m/Y'), 'length' => array(10, 15)),
					'D109_Senha' => array('name' => 'D109_Senha', 'label'=>'Senha','type' => 'text', 'length' => array(10, 4), 'align' => 'right', 'style' => 'float:right;'),
					'D109_Inicio_Ausencia' => array('name' => 'D109_Inicio_Ausencia', 'label'=>'Início Ausência','type' => 'data', 'length' => array(10, 15)),
					'D109_Fim_Ausencia' => array('name' => 'D109_Fim_Ausencia', 'label'=>'Fim Ausência','type' => 'data', 'length' => array(10, 15), 'align' => 'right', 'style' => 'float:right;'),
					'D109_Carga_Horaria' => array('name' => 'D109_Carga_Horaria', 'label'=>'Carga Horária Diária','type' => 'tempo', 'length' => array(10, 15)),
					'D109_Flag_Ativo' => array('name' => 'D109_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S' , 'selected' => true),
							array('title' => 'Não', 'value' => 'N'), 
						)
					),
					'D109_Codigo_Barras' => array('name' => 'D109_Codigo_Barras', 'label'=>'Código de barras','type' => 'text', 'length' => array(50, 120)),
				)
			)
		);

		// Check if the request was sent by "cad010grid01" or "cad064GridCadastroVeiculos" 
		if ($typeForm == "cad064GridCadastroVeiculos") {
			$formArray['campos']['funcionarios']['D109_Funcao'] = array('name' => 'D109_Funcao', 'label' => 'Função', 'type' => 'text', 'value' => 'MOTORISTA', 'length' => array(50, 90), 'readonly' => true);
		} else {
			$formArray['campos']['funcionarios']['D109_Funcao'] = array('name' => 'D109_Funcao', 'label' => 'Função', 'type' => 'text', 'length' => array(50, 90));
		}

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
		$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
	case 'cad010form02':
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
		$form_tabela = 'D115';
		$form_campo_pk = 'D115_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad010/form/cad010form02/';

		// novo formulário
		$form = new Formulario('form_cad010_form2');
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'competencias' => array(
					'D115_D110_Id' => array('name' => 'Consulta_D115_D110_Id', 'label'=>'Competência', 'type' => 'pesquisa','pesquisa'=>array('D110','D110_Id','D110_Titulo'), 'length' => array(50,20)),
					'content_form2_btn' => array('name' => 'content_form2_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'style'=> 'float:right;','extra'=>'style="margin-left:5px;padding-left:5px;padding-right:5px;padding-top:0px;height:20px;"', 'reject'=>true),
					'D115_D109_Id' => array('name' => 'D115_D109_Id', 'type' => 'hidden','value'=>$acaoId),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden','value' => @$r_divReload,'label'=>'divReload', 'reject' => true),
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
		case 'cad010FormDocumentos':
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
		$form_tabela = 'D123';
		$form_campo_pk = 'D123_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad010FormDocumentosBtnAdd', 'cad010FormDocumentosBtnCln', 'cad010FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad010/form/cad010FormDocumentos/';
		// Array de definição do formulário
		$r_D109_Id = isset($r_cad010FormDocumentos) ? $r_cad010FormDocumentos['D123_D109_Id'] : $r_D109_Id;
		
		$tmp = gGeraSelect('D109', 'D109_Id', 'D109_Nome,D109_Funcao', "WHERE D109_Id = $r_D109_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad010FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['__D123_D109_Id'] 		= array('name' => '__D123_D109_Id', 'label'=>'Funcionário', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
			$tmp
		);
		$formArray['campos']['geral']['D123_D109_Id'] 			= array('name' => 'D123_D109_Id', 'type' => 'hidden', 'value' => $r_D109_Id);
		$formArray['campos']['geral']['D123_C007_Id'] 			= array('name' => 'D123_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['D123_Data_Hora_UpLoad'] 	= array('name' => 'D123_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		$formArray['campos']['geral']['D123_Data_Validade'] 	= array('name' => 'D123_Data_Validade', 'label'=>'Validade do Arquivo', 'type' => 'data', 'length' => array(8, 8));
		$formArray['campos']['geral']['D123_Descricao'] 		= array('name' => 'D123_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['D123_Nome_Arquivo'] 		= array('name' => 'D123_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['cad010FormDocumentosBtnNew'] = array('name' => 'cad010FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		//$formArray['campos']['geral']['cad010FormDocumentosBtnCln'] = array('name' => 'cad010FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad010FormDocumentosBtnAdd'] = array('name' => 'cad010FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		
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
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
					}
				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						// Neste caso, guardamos o nome do arquivo no banco
						$form->campoSetarValor('D123_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}documentos/D109_".str_repeat('0',5-strlen($form->campoValorEnviado('D123_D109_Id'))).$form->campoValorEnviado('D123_D109_Id');
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
						}
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
		
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
}
?>
