<?php
namespace hardness;
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();
require_once('bibliotecas/classes/EST004.php');
$EST004 = new EST004();
/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'srm001FormSetFornecedor':
		$content1 = (isset($r_content1)) ? $r_content1 : '';
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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srm/srm001/form/srm001FormSetFornecedor/';

		$sqlC007 = mysql_query("SELECT C007_Flag_Vendedor FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
		$C007 = mysql_fetch_array($sqlC007);
		if($C007['C007_Flag_Vendedor'] == 'V'){
			$pesquisaGridExtra = <<<EOT
				left join D020 on D020_Id=D024_D020_Id 
				left join D018 on D018_Id=D024_D018_Id 
				WHERE D024_Flag_Ativo='S' AND D024_C007_Vendedor_Interno = '{$g['usuarioAtual']}'
				order by D024_Nome_Empresa
EOT;
		} else {
			$pesquisaGridExtra = <<<EOT
				LEFT JOIN D020 ON D020_ID=D024_D020_ID 
				LEFT JOIN D018 ON D018_ID=D024_D018_ID 
				WHERE D024_Flag_Ativo='S'
				ORDER BY D024_Nome_Empresa
EOT;
		}

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Cliente,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'srm001ProcuraFornecedor',																						// callback js
			$g['divId'],																									// divId
			array($content1),																									// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array('colunaExibir'=>false),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);
		/* A linha abaixo era usada para dar nome ao campo e executar o JS comentado a baixo,
		após uma mudança no framework não é mais possível utilizar $uniqid para o
		nome de campos de um form. Por tanto essa parte foi desabilitada e o campo
		foi trocado para nomes fixos -> 'Fornecedor' => array('name' => 'Fornecedor'... */
		//$uniqid = uniqid();
		// novo formulário
		$form = new Formulario('srm001FormSetFornecedorForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Fornecedor' => array('name' => 'Fornecedor' , 'label' => 'Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(69,120), 'reject' => true),
				)
			)
		);
		$limite = 0;
		$form->definirFormularioArray($formArray);

		/* Código atualmente redundante, framework já dá focus no 
		primeiro campo do formulário automaticamente */
// 		echo <<<EOT
// 		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
// 			$('#' + '{$uniqid}').focus();
// 		}</script></div>
// EOT;

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad050FormLocacaoLocal_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'srm001FormImportarXML':
	
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'srm001FormImportarXML_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/srm001FormImportarXML/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('srm001FormImportarXML');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoXMLEst004'] 		= array('name' => 'uploadArquivoXMLEst004', 'label' => 'Selecione o XML', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['srm001FormImportarXML_btn'] 		= array('name' => 'srm001FormImportarXML_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T007_Arquivo_XML'] 				= array('name' => 'T007_Arquivo_XML', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		$formArray['campos']['geral']['incluirItens']					= array('name' => 'incluirItens', 'label' => 'Importar os ítens do XML', 'type' => 'checkbox', 'checked'=>'checked', 'reject' => true);
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoXMLEst004');
				$incluirItens= $form->campoValorEnviado('incluirItens');
				$retorno = $EST004->prepararImportarXML($arquivo,$incluirItens);
				
				if($retorno !== true){
					$retorno = htmlentities(utf8_decode($retorno));
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style="width:900px; height:400px; text-transform: none;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'Nota Importada com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
}
