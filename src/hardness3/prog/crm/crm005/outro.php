<?php
namespace hardness;

require_once('prog/crm/crm005/form_func-js.php');
/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'crm005Data':
		
		$dataAtual = date('d/m/Y');
		$data = (!empty($r_data)) ? $r_data : $dataAtual;
		/**
		* PARTE 1: Variavéis envidas para o formulÃ¡rio
		*/
		// VariÃ¡vel indica qual tab do form serÃ¡ exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que estÃ¡ sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: DefiniÃ§ão do formulÃ¡rio
		*/
		// Este mesmo form serÃ¡ exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form estÃ¡ relacionada, e a chave primÃ¡ria da tabela
		$form_tabela = 'D000';
		$form_campo_pk = 'D000_Id';
		// ID do botão que irÃ¡ executar o envio desse formulÃ¡rio (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lÃ¡)
		$enviar_btnId = 'crm005Data_btn';
		// Form que serÃ¡ executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm005/outro/crm005Data/';

		// novo formulÃ¡rio
		$form = new Formulario('crmPrePedidoData');

		// DefiniÃ§ão do FormulÃ¡rio
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'selectedDate' => array('name' => 'selectedDate', 'label' => 'Data', 'value' => $data,'extra' => 'onchange="carregaData(selectedDate);"', 'type' => 'data', 'length' => array(8,110))
				)
			)
		);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function carregaData(elem){
				var divId = '{$r_divRefresh}';
				var data = elem.value;
				divSetarAcaoId(divId, data, 'data');	
				divRefresh(divId, true);
			}
		} </script></div>
EOT;
		/**
		* PARTE 3: Processamento do formulÃ¡rio (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$data = $form->campoValorEnviado('selectedDate');
				
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var divId = '{$r_divRefresh}';
							var data = '{$data}';
							divSetarAcaoId(divId, data, 'data');	
							divRefresh(divId, true);
					} </script></div>
EOT;
			}
		}

		/**
		* PARTE 4: Gerar o formulÃ¡rio
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulÃ¡rio
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		
		/**
		* BotÃµes
		*/
		// DefiniÃ§ão dos botÃµes
		/*$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="crmDatabtn">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');*/
	break;
	
	case 'crm005Cliente':
		$dataAtual = date('d/m/Y');
		$data = (!empty($r_data)) ? $r_data : $dataAtual;
		$D024_Id =(!empty($r_D024_Id)) ? $r_D024_Id : false;
		
		$D024 = mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
		$mD024 = mysql_fetch_array($D024);
		$nomeEmpresa = isset($mD024['D024_Nome_Empresa']) ? $mD024['D024_Nome_Empresa'] : '';
		$bairro = isset($mD024['D024_Bairro']) ? $mD024['D024_Bairro'] : '';
		$D024_DDD_Telefone_1 = isset($mD024['D024_DDD_Telefone_1']) ? $mD024['D024_DDD_Telefone_1'] : '';
		$D024_Telefone_1 = isset($mD024['D024_Telefone_1']) ? $mD024['D024_Telefone_1'] : '';
		$Nome_Fantasia = isset($mD024['D024_Nome_Fantasia']) ? $mD024['D024_Nome_Fantasia'] : '';
		/**
		* PARTE 1: Variavéis envidas para o formulÃ¡rio
		*/
		// VariÃ¡vel indica qual tab do form serÃ¡ exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que estÃ¡ sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$formIdCliente = (isset($r_formIdCliente)) ? $r_formIdCliente : false;
		$divContent = (isset($r_divRefresh)) ? $r_divRefresh : false;
		

		/**
		* PARTE 2: DefiniÃ§ão do formulÃ¡rio
		*/
		// Este mesmo form serÃ¡ exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form estÃ¡ relacionada, e a chave primÃ¡ria da tabela
		$form_tabela = 'D000';
		$form_campo_pk = 'D000_Id';
		// ID do botão que irÃ¡ executar o envio desse formulÃ¡rio (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lÃ¡)
		$enviar_btnId = 'Procura_Cliente';
		// Form que serÃ¡ executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm005/outro/crm005Cliente/';

		// novo formulÃ¡rio
		$form = new Formulario('crmPrePedidoCliente');
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id 
			left join D018 on D018_Id=D024_D018_Id 
			WHERE D024_Flag_Ativo='S'
			order by D024_Nome_Empresa
EOT;
		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D024_Bairro,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Cliente,Fantasia,Bairro,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'crm005CampoProcuraCliente',																							// callback js
			$g['divId'],																									// divId
			array($formIdCliente, $divContent),																									// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array('colunaExibir'=>false),
				array(),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);
		//form editor
        $form_editor = array(
            'produto' => '{"Procura_Cliente":{"top":7,"left":26,"width":328,"hidden":0},"Bairro":{"top":7,"left":445,"width":168,"hidden":0},"selectedDate":{"top":7,"left":708,"width":65,"hidden":0},"crm005ContentPrePedidoEmpresas":{"top":28,"left":688,"width":null,"hidden":0},"Nome_Fantasia":{"top":30,"left":17,"width":344,"hidden":0},"DDD_Telefone":{"top":30,"left":461,"width":35,"hidden":0},"Telefone":{"top":30,"left":528,"width":120,"hidden":0}}',
        );


		// DefiniÃ§ão do FormulÃ¡rio
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Cliente' => array('name' => 'Procura_Cliente' , 'label' => 'Cliente', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(60,120), 'reject' => true, 'value' => $nomeEmpresa),
					'Bairro' => array('name' => 'Bairro', 'label' => 'Bairro', 'value'=>$bairro, 'type' => 'text', 'length' => array(30,110), 'align' => 'right', 'disabled'=>true),
					'DDD_Telefone' => array('name' => 'DDD_Telefone', 'label' => 'Tel', 'value'=>$D024_DDD_Telefone_1, 'type' => 'text', 'length' => array(2,110), 'align' => 'right', 'disabled'=>true),
					'Telefone' => array('name' => 'Telefone', 'label' => '', 'type' => 'text', 'value'=>$D024_Telefone_1, 'length' => array(15,110), 'align' => 'right', 'disabled'=>true),
					'selectedDate' => array('name' => 'selectedDate', 'label' => 'Data', 'value' => $data,'extra' => 'onchange="carregaData(selectedDate);"', 'style' => 'font-weight:bold', 'type' => 'data', 'length' => array(8,110), 'align' => 'right'),
					'Nome_Fantasia' => array('name' => 'Nome_Fantasia', 'label' => 'Fantasia', 'value' => $Nome_Fantasia, 'type' => 'text', 'length' => array(60,110), 'align' => 'right', 'disabled'=>true),
					'crm005ContentPrePedidoEmpresas' => array('name' => 'crm005ContentPrePedidoEmpresas', 'type'=>'button', 'reload' => false, 'value' =>'Pré-pedidos do dia', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
					'T099_D024_Id' => array('name' => 'T099_D024_Id', 'type' => 'hidden')
				)
			)
		);
		//$formArray['campos']['geral']['crm005ContentPrePedidoEmpresas'] = array('name' => 'crm005ContentPrePedidoEmpresas', 'type'=>'button', 'reload' => false, 'value' =>'Pré-pedido empresas', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			setTimeout(function() {
				$('#' + '{$g['divId']}' + '#Procura_Produto_Pesquisa').focus();
			},500);
			function carregaData(elem){
				var divId = '{$r_divRefresh}';
				var data = elem.value;
				$('#' + '{$formIdCliente}' + '#T099_Data').val(data);
				divSetarAcaoId(divId, data, 'data');	
				divRefresh(divId, true);
			}
			$('#crm005ContentPrePedidoEmpresas').unbind('click');
			$('#crm005ContentPrePedidoEmpresas').unbind('click').bind('click', function(data) {
				//var id = $('#{$g['divId']} #T003_D024_Id').val();
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-pedido Empresas', '/crm/crm005/content/crm005ContentPrePedidoEmpresas/', '&data=' + encodeURIComponent('{$data}') + '&divRefresh=' + encodeURIComponent('{$r_divRefresh}'), [900,600]);											
				return false;
			});
		} </script></div>
EOT;
		/**
		* PARTE 3: Processamento do formulÃ¡rio (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$data = $form->campoValorEnviado('selectedDate');
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var divId = '{$r_divRefresh}';
							var data = '{$data}';
							divSetarAcaoId(divId, data, 'data');	
							divRefresh(divId, true);
					} </script></div>
EOT;
			}
		}

		/**
		* PARTE 4: Gerar o formulÃ¡rio
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulÃ¡rio
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
		
		/**
		* BotÃµes
		*/
		// DefiniÃ§ão dos botÃµes
		/*$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="crmDatabtn">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');*/
	break;
}
?>