<?php
namespace hardness;
//require_once('prog/est/est012/form_func-js.php');
require_once("old/lib/funcoes.php");

require_once("bibliotecas/classes/EST012.php");
$EST012 = new EST012();


/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Form Principal
	 * Chamado em: ven013tab02
	 */
	case 'est012FormNaoLiberar':
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
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T010';
		$form_campo_pk = 'T010_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est012FormNaoLiberarBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est012/form/est012FormNaoLiberar/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T173_Motivo_Nao_Liberado' => array('name' => 'T173_Motivo_Nao_Liberado', 'label' => '* Motivo', 'type' => 'textarea', 'length' => array(3,50)),
					'T173_Id' => array('name' => 'T173_Id', 'value'=>@$r_T173_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(3,50)),
				)
			)
		);
        $form = new Formulario('est012FormNaoLiberar');
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
				$retorno = $EST012->reprovarCotacao($form->campoValorEnviado('T173_Id'),$form->campoValorEnviado('T173_Motivo_Nao_Liberado'));
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Cotação negada com Sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
										if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
											divRefresh('{$g['divIdParent']}', true);
										}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,"Você deseja recusar esta cotação?");

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
		$botoesDir .= '<button type="submit" id="est012FormNaoLiberarBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est013FormInventario':
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
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D000';
		$form_campo_pk = 'D000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est013FormInventario_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est013/form/est013FormInventario/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"dataInventario":{"top":7,"left":51,"width":70,"hidden":0},"departamento":{"top":30,"left":45,"width":319,"hidden":0},"setor":{"top":53,"left":48,"width":319,"hidden":0},"grupo":{"top":76,"left":43,"width":319,"hidden":0},"subgrupo":{"top":99,"left":20,"width":319,"hidden":0},"nacimpo":{"top":122,"left":33,"width":320,"hidden":0},"custoInventario":{"top":145,"left":45,"width":320,"hidden":0},"estoqueInventario":{"top":168,"left":34,"width":320,"hidden":0},"ordenacaoInventario":{"top":191,"left":14,"width":320,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'dataInventario' => array('name' => 'dataInventario', 'label' => 'Data', 'type' => 'data', 'reject' => true),
		        	'departamento' => array('name' => 'departamento', 'label' => 'Depto', 'type' => 'select', 'select'=>
						gGeraSelect('C008', 'C008_Id', 'C008_Tipo', 'order by C008_Tipo', true),
						'reject' => true
					),
					'setor' => array('name' => 'setor', 'label' => 'Setor', 'type' => 'select', 'select' => 
						gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo', "order by D060_Descricao_Grupo", true),
						'reject' => true
					),
					'grupo' => array('name' => 'grupo', 'label'=>'Grupo', 'type' => 'select', 'restricao' => false, 'select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true),
						'reject' => true
					),
					'subgrupo' => array('name' => 'subgrupo', 'label'=>'Sub-Grupo', 'restricao' => false,'type' => 'select', 'select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto', ' order by D002_Descricao_Produto', true),
						'reject' => true
					),
					'nacimpo' => array('name' => 'nacimpo', 'label' => 'Nac/Imp', 'type' => 'select', 'select' => array(
						array('title' => 'Todos', 'value' => 'T'),
						array('title' => 'Nacional', 'value' => 'N'),
						array('title' => 'Importado', 'value' => 'I'),
					), 'reject' => true),
					'custoInventario' => array('name' => 'custoInventario', 'label' => 'Custo', 'type' => 'select', 'select' => array(
						array('title' => 'Último Custo Compra', 'value' => '1'),
						array('title' => 'Custo Médio', 'value' => '2'),
						array('title' => 'Não Exibir a Coluna Custo', 'value' => '0'),
					), 'reject' => true),
					'estoqueInventario' => array('name' => 'estoqueInventario', 'label' => 'Estoque', 'type' => 'select', 'select' => array(
						array('title' => 'Todos', 'value' => '4'),
						array('title' => 'Positivo', 'value' => '1'),
						array('title' => 'Zero', 'value' => '2'),
						array('title' => 'Negativo', 'value' => '3'),
					), 'reject' => true),
					'ordenacaoInventario' => array('name' => 'ordenacaoInventario', 'label' => 'Ordenar por', 'type' => 'select', 'select' => array(
						array('title' => 'Código Produto', 'value' => 'D001_Codigo_Produto'),
						array('title' => 'Descrição Produto', 'value' => 'D001_Descricao_Produto'),
					), 'reject' => true),
					'csv' => array('name' => 'csv', 'type' => 'hidden', 'reject' => true),
				)
			)
		);
        $form = new Formulario('est013FormInventario');
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
				$data 			= $form->campoValorEnviado('dataInventario');
				$custo 			= $form->campoValorEnviado('custoInventario');
				$estoque 		= $form->campoValorEnviado('estoqueInventario');
				$ordenacao 		= $form->campoValorEnviado('ordenacaoInventario');
				$departamento 	= $form->campoValorEnviado('departamento');
				$setor 			= $form->campoValorEnviado('setor');
				$grupo 			= $form->campoValorEnviado('grupo');
				$subgrupo 		= $form->campoValorEnviado('subgrupo');
				$nacimpo 		= $form->campoValorEnviado('nacimpo');
				$csv 			= $form->campoValorEnviado('csv');
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var csv = '{$csv}';
						var data = '{$data}';
						var valores = '{$custo}';
						var estoque = '{$estoque}';
						var ordem = '{$ordenacao}';
						var departamento = '{$departamento}';
						var setor = '{$setor}';
						var grupo = '{$grupo}';
						var subgrupo = '{$subgrupo}';
						var nacimpo = '{$nacimpo}';
						$('#csv').val('');
						nova_janela('/est/est013/outro/relatorioInventario/?ajax=true&Data='+data+'&Fornecedor=0&Valor_Preco=0&Valores='+valores+'&Estoque='+estoque+'&Ordem='+ordem+'&Departamento='+departamento+'&Setor='+setor+'&Grupo='+grupo+'&Subgrupo='+subgrupo+'&nacimpo='+nacimpo+'&CSV='+csv,0,0,800,600,'no','no','no','no','yes','yes', 'Inventário');
					} </script></div>
EOT;
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
		echo $g['smarty']->fetch('formEditor.tpl');
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="$(\'#csv\').val(\'true\'); $(\'#est013FormInventario_btn\').trigger(\'click\');">CSV</button>';
		$botoesDir .= '<button type="submit" id="est013FormInventario_btn">Imprimir</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
