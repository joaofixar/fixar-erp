<?php
namespace hardness;


switch ($g['r'][3]) {
	/**
	* Grid Principal
	* Chamado em: Menu
	*/
	case 'content':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array($content1, '/rel/pedidos/entregador/tab/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tab':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/rel/pedidos/entregador/filtro/', 'Entregador por Rota', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'filtro':
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		$form_tabela = '';
		$form_campo_pk = '';
		$enviar_btnId = 'filtroBtn';
		$enviar_acaoHref = '/rel/pedidos/entregador/filtro/';

		// Form Editor
		$form_editor = array(
			'geral' => '{"Data_Inicial":{"top":7,"left":21,"width":86,"hidden":0},"Data_Final":{"top":7,"left":176,"width":86,"hidden":0},"Entregador":{"top":30,"left":20,"width":191,"hidden":0},"Rota":{"top":53,"left":51,"width":184,"hidden":0},"Empresa":{"top":76,"left":30,"width":184,"hidden":0}}',
		);
		$form = new Formulario('filtro');

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Data_Inicial' => array('name' => 'Data_Inicial', 'label'=>'Data Inicial', 'type' => 'data', 'reject' => true),
					'Data_Final' => array('name' => 'Data_Final', 'label'=>'', 'type' => 'data', 'reject' => true),
					'Entregador' => array('name' => 'Entregador', 'label' => 'Entregador', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '', true)
					),
					'Rota' => array('name' => 'Rota', 'label' => 'Rota', 'type' => 'text', 'reject' => true),
					'Empresa' => array('name' => 'Empresa', 'label' => 'Empresa', 'type' => 'text', 'reject' => true),
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
				$Data_Inicial = $form->campoValorEnviado('Data_Inicial');
				$Data_Final = $form->campoValorEnviado('Data_Final');
				$Entregador = $form->campoValorEnviado('Entregador');
				$Rota = $form->campoValorEnviado('Rota');
				$Empresa = $form->campoValorEnviado('Empresa');
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						nova_janela('/rel/pedidos/entregador/relatorio/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Empresa={$Empresa}&Rota={$Rota}&Entregador={$Entregador}',0,0,830,750,'no','no','no','no','yes','yes',0);
						//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					} </script></div>
EOT;
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="filtroBtn">Imprimir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'relatorio':
		$Data_Inicial = gCorrigeDataInverte($r_Data_Inicial);
		$Data_Final = gCorrigeDataInverte($r_Data_Final);
		$Entregador = $r_Entregador;
		$Rota = $r_Rota;
		$Empresa = $r_Empresa;
		//Extrai o filtro data
		if(!empty($Data_Inicial) && !empty($Data_Final)){
			$filtroData = "WHERE (T005_Data_Entrega >= '{$Data_Inicial}' AND T005_Data_Entrega <= '{$Data_Final}')";
			$textoData = "Data de Entrega: ".$r_Data_Inicial." a ".$r_Data_Final;
		} else if(!empty($Data_Inicial) && empty($Data_Final)){
			$filtroData = "WHERE (T005_Data_Entrega = '{$Data_Inicial}')";
			$textoData = "Data de Entrega: ".$r_Data_Inicial;
		} else if(empty($Data_Inicial) && empty($Data_Final)){
			$textoData = "";
			$filtroData = "";
		}
		//Extrai o filtro entregador
		if(!empty($Entregador)){
			$filtroEntregador = "AND (D022_Id = '{$Entregador}')";
			$D022 = mysql_query("SELECT D022_Nome_Empresa FROM D022 WHERE D022_Id = '{$Entregador}'");
			$D022 = mysql_fetch_array($D022);
			$textoEntregador = "Entregador: ".$D022['D022_Nome_Empresa'];
		} else {
			$filtroEntregador = "";
			$textoEntregador = "";
		}
		//Extrai o filtro Rota
		if(!empty($Rota)){
			$filtroRota = "AND (T208_Rota LIKE '%{$Rota}%')";
			$textoRota = "Rota: ".$Rota;
		} else {
			$filtroRota = "";
			$textoRota = "";
		}
		//Extrai o filtro Empresa
		if(!empty($Empresa)){
			$filtroEmpresa = "AND (C004_Nome_Empresa LIKE '%{$Empresa}%')";
		} else {
			$filtroEmpresa = "";
		}
		//Left Joins do SQL
		$extra = <<<EOT
			LEFT JOIN D022 ON T006_D022_Id = D022_Id
			LEFT JOIN D006 ON T006_D006_Id = D006_Id
			LEFT JOIN T005 ON T006_T005_Id = T005_Id
			LEFT JOIN D024 ON T005_D024_Id = D024_Id
			LEFT JOIN D020 ON D024_D020_Id = D020_Id
			LEFT JOIN D009 ON T006_D009_Id = D009_Id
			LEFT JOIN D049 ON D009_D049_Id = D049_Id
			LEFT JOIN D001 ON D049_D001_Id = D001_Id
			LEFT JOIN T208 ON T006_T208_Id = T208_Id
			LEFT JOIN C004 ON T005_C004_Id = C004_Id
			LEFT JOIN D037 ON D001_D037_Id = D037_Id
EOT;
		$agora = date("Y-m-d");
		$agora = gCorrigeData($agora);
		$resRota = mysql_query("SELECT * FROM T006 {$extra} {$filtroData} {$filtroEntregador} {$filtroRota} {$filtroEmpresa} AND T005_Flag_Status != 8 GROUP BY T208_Rota ORDER BY T006_Hora_Entrega ASC");
		log("SELECT * FROM T006 {$extra} {$filtroData} {$filtroEntregador} {$filtroRota} {$filtroEmpresa} AND T005_Flag_Status != 8 GROUP BY T208_Rota ORDER BY T006_Hora_Entrega ASC");
		echo <<<EOT
			<style>
				.linhas {
					border: 0 1px 1px 1px solid 000;
				}
				#titulo {
					padding: 15px 0 15px 125px;
					font-size: 16px;
					font-family: Verdana;
					font-weight: bold;
				}
				#filtro {
					border-radius: 5px 5px;
					font-size: 12px;
					font-family: Verdana;
					width: 780px;
					align: center;
					border: 1px solid gray;
					background-color: #F4F2F2;
					padding: 10px 0 10px 15px;
					margin-bottom: 20px;
				}
				#relatorio {
					width: 800px;
					float: left;
					font-size: 11px;
					font-family: Verdana;
					-moz-columns: 2;
					-moz-column-width: 320px;
					-webkit-columns: 2;
					-webkit-column-width: 320px;
					-moz-column-count: 2;
					-moz-column-rule-width: 15px;
				}
			}
			</style>
			<div id="titulo">
				Relatório de Entregador por Rota - Gerado em: {$agora}
			</div>
			<div id="filtro">
					{$textoData} &nbsp;&nbsp; {$textoEntregador} &nbsp;&nbsp; {$textoRota}
			</div>
			<div id="relatorio">
EOT;
		while($rowRota = mysql_fetch_array($resRota)){
			//Rotas
			$resCliente = mysql_query("SELECT *, GROUP_CONCAT(DISTINCT T006_T005_Id SEPARATOR ', ') as Pedidos FROM T006 {$extra} {$filtroData} {$filtroEntregador} {$filtroRota} {$filtroEmpresa} AND T006_T208_Id = '{$rowRota['T208_Id']}' AND T005_Flag_Status != 8 GROUP BY D024_Id ORDER BY T006_Hora_Entrega ASC");
			log("SEL 2 === SELECT *, GROUP_CONCAT(DISTINCT T006_T005_Id SEPARATOR ', ') as Pedidos FROM T006 {$extra} {$filtroData} {$filtroEntregador} {$filtroRota} {$filtroEmpresa} AND T006_T208_Id = '{$rowRota['T208_Id']}' AND T005_Flag_Status != 8 GROUP BY D024_Id ORDER BY D024_Nome_Empresa,T006_Hora_Entrega ASC");
			echo "<div style='border-bottom: 1px solid #000; width: 100%; font-size: 12px; font-weight: bold; margin-top: 5px; padding-top: 22px; margin-bottom: -15px'>".$rowRota['T208_Rota']."</div>";
			while($rowCliente = mysql_fetch_array($resCliente)){
				//Clientes
				echo "<div style='border-bottom: 1px solid #000; width: 100%; font-size: 11px; font-weight: bold; clear: left; padding-top: 18px;'>".$rowCliente['D024_Nome_Fantasia']." - ".gCorrigeTempo($rowCliente['T006_Hora_Entrega'])."</div>";
				echo "<div style='border-bottom: 1px solid #000; width: 100%; font-size: 9px; font-weight: bold; clear: left;'>{$rowCliente['D024_Endereco']}, {$rowCliente['D024_Numero']} - {$rowCliente['D024_Bairro']} - {$rowCliente['D020_Nome_Cidade']}</div>";
				echo "<div style='border-bottom: 1px solid #000; width: 100%; font-size: 9px; font-weight: bold; clear: left;'>Pedido(s): {$rowCliente['Pedidos']}</div>";
				$ultimoD006_Flag_Venda_Compra_Outros = '';
				//venda ou cfop
				$resProdutos = mysql_query("SELECT *, SUM(T006_Quantidade) as Quantidade FROM T006 {$extra} {$filtroData} {$filtroEntregador} {$filtroRota} {$filtroEmpresa} AND T006_T208_Id = '{$rowRota['T208_Id']}' AND T005_D024_Id = '{$rowCliente['D024_Id']}' AND T005_Flag_Status != 8 GROUP BY T006_Hora_Entrega, D001_Id, D006_Flag_Venda_Compra_Outros ORDER BY D006_Flag_Venda_Compra_Outros ASC, D001_Descricao_Produto ASC");
				while($rowProdutos = mysql_fetch_array($resProdutos)){
					if($ultimoD006_Flag_Venda_Compra_Outros != $rowProdutos['D006_Flag_Venda_Compra_Outros']){
						$ultimoD006_Flag_Venda_Compra_Outros = $rowProdutos['D006_Flag_Venda_Compra_Outros'];
						$tipo = ($rowProdutos['D006_Flag_Venda_Compra_Outros'] == 'V') ? 'VENDA' : $rowProdutos['D006_Descricao'];
						echo "<div style='border-bottom: 1px solid #000; width: 100%; font-size: 9px; font-weight: bold; clear: left;'>{$tipo}</div>";
					}
					//Produtos
					$rowProdutos['D001_Descricao_Produto'] = empty($rowProdutos['D001_Descricao_Produto']) ? '&nbsp;' : $rowProdutos['D001_Descricao_Produto'];
					$rowProdutos['Quantidade'] = gCorrigeNumero($rowProdutos['Quantidade']);
					$rowProdutos['D037_Unidade'] = empty($rowProdutos['D037_Unidade']) ? '&nbsp;' : $rowProdutos['D037_Unidade'];
					if(strlen($rowProdutos['D001_Descricao_Produto']) > 37){
						$rowProdutos['Quantidade']	 = '<br>'.$rowProdutos['Quantidade'];
						$rowProdutos['D037_Unidade'] = '<br>'.$rowProdutos['D037_Unidade'];
					}
					echo <<<EOT
						<div>
							<div style='border-bottom: 1px solid #000; width: 70%; float:left;'>{$rowProdutos['D001_Descricao_Produto']}</div>
							<div style='border-bottom: 1px solid #000; width: 20%; float:left; text-align: right;'>{$rowProdutos['Quantidade']}</div>
							<div style='border-bottom: 1px solid #000; width: 10%; float:left; text-align: center;'>{$rowProdutos['D037_Unidade']}</div>
						</div>
EOT;
				}
			}
		}
		echo <<<EOT
			</div>
EOT;
	break;
}