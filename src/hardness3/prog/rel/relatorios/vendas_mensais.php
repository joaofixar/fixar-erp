<?php
namespace hardness;
	set_time_limit(0);
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('default_charset', 'utf-8');
	switch ($g['r'][3]) {
		case 'contentVendasMensais':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/rel/relatorios/vendas_mensais/formVendasMensais/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'formVendasMensais':
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
			$enviar_btnId = 'vendasMensaisBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/rel/relatorios/vendas_mensais/formVendasMensais/';

			// editor
			$form_editor = array(
				'geral' => '{"Mes":{"top":6.984375,"left":37,"width":128,"hidden":0},"Ano":{"top":29.96875,"left":39,"width":128,"hidden":0},"C007_Id":{"top":52.984375,"left":11,"width":223,"hidden":0},"C007_Cargo":{"top":76,"left":28,"width":216,"hidden":0},"C007_C007_Id":{"top":99,"left":5,"width":223,"hidden":0}}'
			);

			$arrayAno = array();
			$arrayAno[] = array('title' => date("Y", strtotime("+1 year")), 'value' => date("Y", strtotime("+1 year")));
			$arrayAno[] = array('title' => date("Y"), 'value' => date("Y"), 'selected' => 'selected');
			$arrayAno[] = array('title' => date("Y", strtotime("-1 year")), 'value' => date("Y", strtotime("-1 year")));
			$arrayAno[] = array('title' => date("Y", strtotime("-2 year")), 'value' => date("Y", strtotime("-2 year")));
			$arrayAno[] = array('title' => date("Y", strtotime("-3 year")), 'value' => date("Y", strtotime("-3 year")));
					


			// novo formulário
			$form = new Formulario('formVendasMensais');
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			if($g['nivelAlcada'] == '4'){
				$arrayC007 = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'WHERE C007_Ativo!="N" AND C007_Id != "63" ORDER BY C007_Nome', true);
				$arrayC007[0]['title'] = 'RESUMO';			
				$arrayC007[0]['value'] = '-1';			
				$arrayC007Supervisor = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'WHERE C007_Ativo!="N" AND C007_Id != "63" AND (C007_Flag_Vendedor="D" OR C007_Flag_Vendedor="S") ORDER BY C007_Nome', true);
				$formArray = array(
					'campos' => array(
						'geral' => array(
							'Mes' => array('name' => 'Mes', 'label' => 'Mês', 'type' => 'select', 'select' => 
								array(
									array('title' => 'Janeiro', 'value' => '01', 'selected'=>'selected'),
									array('title' => 'Fevereiro', 'value' => '02'),
									array('title' => 'Marco', 'value' => '03'),
									array('title' => 'Abril', 'value' => '04'),
									array('title' => 'Maio', 'value' => '05'),
									array('title' => 'Junho', 'value' => '06'),
									array('title' => 'Julho', 'value' => '07'),
									array('title' => 'Agosto', 'value' => '08'),
									array('title' => 'Setembro', 'value' => '09'),
									array('title' => 'Outubro', 'value' => '10'),
									array('title' => 'Novembro', 'value' => '11'),
									array('title' => 'Dezembro', 'value' => '12'),
								)
							),
							'Ano' => array('name' => 'Ano', 'label' => 'Ano', 'type' => 'select', 'select' => $arrayAno, 'reject' => true),
							'C007_Id' => array('name' => 'C007_Id', 'label' => 'Vendedor', 'type' => 'select', 
								'select' => $arrayC007
							),
							'C007_Cargo' => array('name' => 'C007_Cargo', 'label' => 'Cargo', 'type' => 'text'),
							'C007_C007_Id' => array('name' => 'C007_C007_Id', 'label' => 'Supervisor', 'type' => 'select', 
								'select' => $arrayC007Supervisor
							),
						)	
					)
				);
			} else if($g['nivelAlcada'] == '2'){
				$arrayC007 = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', "WHERE C007_Ativo='S' AND C007_Id != '63' AND (C007_C007_Id='{$g['usuarioAtual']}' OR C007_C007_Id_2='{$g['usuarioAtual']}') ORDER BY C007_Nome", true);
				$arrayC007[0]['title'] = 'RESUMO';			
				$arrayC007[0]['value'] = '-1';			
				$arrayC007Supervisor = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', "WHERE C007_Ativo='S' AND C007_Id = '{$g['usuarioAtual']}'", false);
				$formArray = array(
					'campos' => array(
						'geral' => array(
							'Mes' => array('name' => 'Mes', 'label' => 'Mês', 'type' => 'select', 'select' => 
								array(
									array('title' => 'Janeiro', 'value' => '01', 'selected'=>'selected'),
									array('title' => 'Fevereiro', 'value' => '02'),
									array('title' => 'Marco', 'value' => '03'),
									array('title' => 'Abril', 'value' => '04'),
									array('title' => 'Maio', 'value' => '05'),
									array('title' => 'Junho', 'value' => '06'),
									array('title' => 'Julho', 'value' => '07'),
									array('title' => 'Agosto', 'value' => '08'),
									array('title' => 'Setembro', 'value' => '09'),
									array('title' => 'Outubro', 'value' => '10'),
									array('title' => 'Novembro', 'value' => '11'),
									array('title' => 'Dezembro', 'value' => '12'),
								)
							),
							'Ano' => array('name' => 'Ano', 'label' => 'Ano', 'type' => 'select', 'select' => $arrayAno, 'reject' => true),
							'C007_Id' => array('name' => 'C007_Id', 'label' => 'Vendedor', 'type' => 'select', 
								'select' => $arrayC007
							),
							'C007_C007_Id' => array('name' => 'C007_C007_Id', 'label' => 'Supervisor', 'type' => 'select', 
								'select' => $arrayC007Supervisor
							),
						)	
					)
				);
			} else {
				$arrayC007 = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', "WHERE C007_Ativo!='N' AND C007_Id = '{$g['usuarioAtual']}' ORDER BY C007_Nome");
				$formArray = array(
					'campos' => array(
						'geral' => array(
							'Mes' => array('name' => 'Mes', 'label' => 'Mês', 'type' => 'select', 'select' => 
								array(
									array('title' => 'Janeiro', 'value' => '01', 'selected'=>'selected'),
									array('title' => 'Fevereiro', 'value' => '02'),
									array('title' => 'Mar�o', 'value' => '03'),
									array('title' => 'Abril', 'value' => '04'),
									array('title' => 'Maio', 'value' => '05'),
									array('title' => 'Junho', 'value' => '06'),
									array('title' => 'Julho', 'value' => '07'),
									array('title' => 'Agosto', 'value' => '08'),
									array('title' => 'Setembro', 'value' => '09'),
									array('title' => 'Outubro', 'value' => '10'),
									array('title' => 'Novembro', 'value' => '11'),
									array('title' => 'Dezembro', 'value' => '12'),
								)
							),
							'Ano' => array('name' => 'Ano', 'label' => 'Ano', 'type' => 'select', 'select' => $arrayAno, 'reject' => true),
							'C007_Id' => array('name' => 'C007_Id', 'label' => 'Vendedor', 'type' => 'select', 
								'select' => $arrayC007
							),
						)	
					)
				);
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
					$Mes = $_REQUEST['formVendasMensais']['Mes'];
					$Ano = $_REQUEST['formVendasMensais']['Ano'];
					$C007_Id = $form->campoValorEnviado('C007_Id');
					$C007_C007_Id = $form->campoValorEnviado('C007_C007_Id');
					$C007_Cargo = $form->campoValorEnviado('C007_Cargo');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/rel/relatorios/vendas_mensais/vendasMensais/?ajax=true&Mes={$Mes}&Ano={$Ano}&C007_Id={$C007_Id}&C007_C007_Id={$C007_C007_Id}&C007_Cargo={$C007_Cargo}',0,0,815,790,'no','no','no','no','yes','yes', 'Vendas mensais');
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
			$botoesDir .= '<button type="submit" id="vendasMensaisBtn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;

		case 'vendasMensais':
			$mes = $r_Mes;
			$ano = $r_Ano;
			$C007_Id = $r_C007_Id;
			$C007_Cargo = $r_C007_Cargo;
			$whereC007_C007_Id = ($r_C007_C007_Id>0) ? "AND C007_C007_Id = '$r_C007_C007_Id'" : "" ;

			$sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Programacao_Vendas LIKE '%{$mes}/{$ano}%'");
			if($C007_Id == '-1'){
				$sqlC007 = mysql_query("SELECT C007_Programacao_Vendas FROM C007 WHERE C007_Programacao_Vendas LIKE '%{$mes}/{$ano}%' AND C007_Ativo != 'N' AND C007_Id != '63' AND C007_Cargo like '%$C007_Cargo%' $whereC007_C007_Id");
			}
			if(mysql_num_rows($sqlC007) <= 0){
				die("Não foi definida a programação para o mês informado");
			}
			while ($C007 = mysql_fetch_array($sqlC007)) {

				$nomeVendedor = $C007['C007_Nome'];
				$cotaAnual = 0;
				$clausulaC007 = ($C007_Id != '-1') ? "AND T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";

				//	PEGA A PROGRAMAÇÃO DO MÊS SELECIONADO
				$C007['C007_Programacao_Vendas'] = str_replace('.', '', $C007['C007_Programacao_Vendas']);
				$C007['C007_Programacao_Vendas'] = str_replace(',', '.', $C007['C007_Programacao_Vendas']);
				preg_match_all('#^(' . "{$mes}/{$ano}" . ') +([^-]+)-([^ ]+) +([0-9.,]+)#ms', $C007['C007_Programacao_Vendas'], $periodosTerceiroMes);
				$cotaMensalTerceiroMes += array_sum($periodosTerceiroMes[4]);
				$cotaAnual += array_sum($periodosTerceiroMes[4]);
				$_cotaMensalTerceiroMes += array_sum($periodosTerceiroMes[4]);
				$dataFinalTerceiroMes = formataDataMes($periodosTerceiroMes[3][count($periodosTerceiroMes[3])-1], $ano);
				$dataInicialTerceiroMes = formataDataMes($periodosTerceiroMes[2][0], $ano);
				//Pega os dois meses anteriores
				$dataParametro = $dataFinalTerceiroMes;
				if(date("d", strtotime($dataParametro)) == '31' && date("m", strtotime($dataParametro)) != '03') {
					$dataParametro = date("Y-m-d", strtotime("-1 day", strtotime($dataParametro)));
				} else if(date("m", strtotime($dataParametro)) == '03'){
					$dataParametro = date("Y-m-28", strtotime($dataParametro));
				}

				$primeiroMes = date("m", strtotime("-2 month", strtotime($dataParametro)));
				$anoPrimeiroMes = date("Y", strtotime("-2 month", strtotime($dataParametro)));
				$segundoMes = date("m", strtotime("-1 month", strtotime($dataParametro)));
				$anoSegundoMes = date("Y", strtotime("-1 month", strtotime($dataParametro)));
				$terceiroMes = $mes;
				if($dataInicialTerceiroMes > $dataFinalTerceiroMes) {
					$dataInicialTerceiroMes = formataDataMes($periodosTerceiroMes[2][0], $ano-1);
				}
				foreach($periodosTerceiroMes[0] as $periodo => $periodos){
					$_cotaSemana[$periodo] += $periodosTerceiroMes[4][$periodo];
				}
			}

			// PEGA A PROGRAMAÇÃO DOS MESES ANTERIORES
			$sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Programacao_Vendas LIKE '%{$segundoMes}/{$anoSegundoMes}%' AND C007_Programacao_Vendas LIKE '%{$primeiroMes}/{$anoPrimeiroMes}%'");
			if($C007_Id == '-1'){
				$sqlC007 = mysql_query("SELECT C007_Programacao_Vendas FROM C007 WHERE C007_Programacao_Vendas LIKE '%{$segundoMes}/{$anoSegundoMes}%' AND C007_Programacao_Vendas LIKE '%{$primeiroMes}/{$anoPrimeiroMes}%' AND C007_Ativo != 'N' AND C007_Id != '63' AND C007_Cargo like '%$C007_Cargo%' $whereC007_C007_Id");
			}
			if(mysql_num_rows($sqlC007) <= 0){
				die("Não foi definida a programação para o meses anteriores");
			}
			while ($C007 = mysql_fetch_array($sqlC007)) {

				//Programação do primeiro mês
				$C007['C007_Programacao_Vendas'] = str_replace('.', '', $C007['C007_Programacao_Vendas']);
				$C007['C007_Programacao_Vendas'] = str_replace(',', '.', $C007['C007_Programacao_Vendas']);
				preg_match_all('#^(' . "{$primeiroMes}/{$anoPrimeiroMes}" . ') +([^-]+)-([^ ]+) +([0-9.,]+)#ms', $C007['C007_Programacao_Vendas'], $periodosPrimeiroMes);
				$cotaMensalPrimeiroMes += array_sum($periodosPrimeiroMes[4]);
				$cotaAnual += array_sum($periodosPrimeiroMes[4]);
				$_cotaMensalPrimeiroMes += array_sum($periodosPrimeiroMes[4]);
				$dataInicialPrimeiroMes = formataDataMes($periodosPrimeiroMes[2][0], $anoPrimeiroMes);
				$dataFinalPrimeiroMes = formataDataMes($periodosPrimeiroMes[3][count($periodosPrimeiroMes[3])-1], $anoPrimeiroMes);
				//Se a data inicial for maior que a data final, houve mudan�a de ano
				if($dataInicialPrimeiroMes > $dataFinalPrimeiroMes) {
					$dataInicialPrimeiroMes = formataDataMes($periodosPrimeiroMes[2][0], $ano-1);
				}

				//Programação do segundo mês
				preg_match_all('#^(' . "{$segundoMes}/{$anoSegundoMes}" . ') +([^-]+)-([^ ]+) +([0-9.,]+)#ms', $C007['C007_Programacao_Vendas'], $periodosSegundoMes);
				$cotaMensalSegundoMes += array_sum($periodosSegundoMes[4]);
				$cotaAnual += array_sum($periodosSegundoMes[4]);
				$_cotaMensalSegundoMes += array_sum($periodosSegundoMes[4]);
				$dataInicialSegundoMes = formataDataMes($periodosSegundoMes[2][0], $anoSegundoMes);
				$dataFinalSegundoMes = formataDataMes($periodosSegundoMes[3][count($periodosSegundoMes[3])-1], $anoSegundoMes);
				//Se a data inicial for maior que a data final, houve mudan�a de ano
				if($dataInicialSegundoMes > $dataFinalSegundoMes) {
					$dataInicialSegundoMes = formataDataMes($periodosSegundoMes[2][0], $ano-1);
				}
			}
			$_cotaMensalPrimeiroMes = gCorrigeNumero($_cotaMensalPrimeiroMes);
			$_cotaMensalSegundoMes = gCorrigeNumero($_cotaMensalSegundoMes);
			$_cotaMensalTerceiroMes = gCorrigeNumero($_cotaMensalTerceiroMes);

			$primeiroMes = gRetornaMes($primeiroMes);
			$segundoMes = gRetornaMes($segundoMes);
			$terceiroMes = gRetornaMes($terceiroMes);

			$dia_inicial = date('z', strtotime($dataInicialTerceiroMes));
			$dias_mes = date('z', strtotime($dataFinalTerceiroMes));
			$data_inicial_historico = strtotime("-2 months", strtotime($dataInicialTerceiroMes));

			$C007_Cota_Vendas_Mensal = 1;

			$dataInicialAno = $dataInicialPrimeiroMes;
			$dataFinalAno = $dataFinalTerceiroMes;

			$sqlPrimeiroMes = mysql_query("SELECT *, round((((SUM(T007_Valor_Total_Produtos)-
														  IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
														  IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
														  IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
														  IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
														  IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0))-SUM(T007_Valor_Total_Custo))/
														(SUM(T007_Valor_Total_Produtos)-
														  IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
														  IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
														  IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
														  IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
														  IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0)))*100,2) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sqlSegundoMes = mysql_query("SELECT *, round((((SUM(T007_Valor_Total_Produtos)-
														  IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
														  IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
														  IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
														  IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
														  IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0))-SUM(T007_Valor_Total_Custo))/
														(SUM(T007_Valor_Total_Produtos)-
														  IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
														  IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
														  IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
														  IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
														  IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0)))*100,2) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sqlTerceiroMes = mysql_query("SELECT *, round((((SUM(T007_Valor_Total_Produtos)-
														  IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
														  IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
														  IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
														  IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
														  IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0))-SUM(T007_Valor_Total_Custo))/
														(SUM(T007_Valor_Total_Produtos)-
														  IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
														  IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
														  IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
														  IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
														  IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0)))*100,2) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");

			$resPrimeiroMes = mysql_fetch_array($sqlPrimeiroMes);
			$resSegundoMes = mysql_fetch_array($sqlSegundoMes);
			$resTerceiroMes = mysql_fetch_array($sqlTerceiroMes);
			//Valor total de vendas por mês
			$valorRealizadoPrimeiroMes = gCorrigeNumero($resPrimeiroMes['Realizado']);
			$valorRealizadoSegundoMes = gCorrigeNumero($resSegundoMes['Realizado']);
			$valorRealizadoTerceiroMes = gCorrigeNumero($resTerceiroMes['Realizado']);
			//Quantidade total de clientes atendidos por mês
			$quantidadeClientesPrimeiroMes = $resPrimeiroMes['Atendidos'];
			$quantidadeClientesSegundoMes = $resSegundoMes['Atendidos'];
			$quantidadeClientesTerceiroMes = $resTerceiroMes['Atendidos'];
			$quantidadeClientesAno = $quantidadeClientesPrimeiroMes + $quantidadeClientesSegundoMes + $quantidadeClientesTerceiroMes;
			//Quantidade de NF emitidas por mês
			$quantidadeNFPrimeiroMes = $resPrimeiroMes['NFEmitidas'];
			$quantidadeNFSegundoMes = $resSegundoMes['NFEmitidas'];
			$quantidadeNFTerceiroMes = $resTerceiroMes['NFEmitidas'];
			//Valor total de Vendas por mês
			$valorClientesPrimeiroMes = gCorrigeNumero($resPrimeiroMes['Realizado']);
			$valorClientesSegundoMes = gCorrigeNumero($resSegundoMes['Realizado']);
			$valorClientesTerceiroMes = gCorrigeNumero($resTerceiroMes['Realizado']);
			//Percentual Atingido por mês
			$percentualAtingidoPrimeiroMes = gCorrigeNumero($resPrimeiroMes['Realizado']/$cotaMensalPrimeiroMes*100);
			$percentualAtingidoSegundoMes = gCorrigeNumero($resSegundoMes['Realizado']/$cotaMensalSegundoMes*100);
			$percentualAtingidoTerceiroMes = gCorrigeNumero($resTerceiroMes['Realizado']/$cotaMensalTerceiroMes*100);
			//Margem Média por mês
			$margemMediaPrimeiroMes = gCorrigeNumero($resPrimeiroMes['MargemVenda']);
			$margemMediaSegundoMes = gCorrigeNumero($resSegundoMes['MargemVenda']);
			$margemMediaTerceiroMes = gCorrigeNumero($resTerceiroMes['MargemVenda']);

			//Select T014 notas devolu��o recebidas
			$sql1PrimeiroMes = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialPrimeiroMes}' AND T013_Data_Entrada <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			log("sql1PrimeiroMes: SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialPrimeiroMes}' AND T013_Data_Entrada <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sql1SegundoMes = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialSegundoMes}' AND T013_Data_Entrada <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			log("sql1SegundoMes: SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialSegundoMes}' AND T013_Data_Entrada <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sql1TerceiroMes = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialTerceiroMes}' AND T013_Data_Entrada <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			log("sql1TerceiroMes: SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialTerceiroMes}' AND T013_Data_Entrada <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$res1PrimeiroMes = mysql_fetch_array($sql1PrimeiroMes);
			$res1SegundoMes = mysql_fetch_array($sql1SegundoMes);
			$res1TerceiroMes = mysql_fetch_array($sql1TerceiroMes);

			$clausulaC007NF = ($C007_Id != '-1') ? "AND T7VN.T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";
			//Select T008 notas devolu��o emitidas
			$sql2PrimeiroMes = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}' ) AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			log("sql2PrimeiroMes: SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}' ) AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sql2SegundoMes = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}' ) AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalSegundoMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			log("sql2SegundoMes: SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}' ) AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalSegundoMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sql2TerceiroMes = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}' ) AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalTerceiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			log("sql2TerceiroMes: SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}' ) AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalTerceiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$res2PrimeiroMes = mysql_fetch_array($sql2PrimeiroMes);
			$res2SegundoMes = mysql_fetch_array($sql2SegundoMes);
			$res2TerceiroMes = mysql_fetch_array($sql2TerceiroMes);

			//Total de NF Devolvidas
			$quantidadeDevolucaoPrimeiroMes = $res1PrimeiroMes['NFEmitidasDevolvidas'] + $res2PrimeiroMes['NFEmitidasDevolvidas'];
			$quantidadeDevolucaoSegundoMes = $res1SegundoMes['NFEmitidasDevolvidas'] + $res2SegundoMes['NFEmitidasDevolvidas'];
			$quantidadeDevolucaoTerceiroMes = $res1TerceiroMes['NFEmitidasDevolvidas'] + $res2TerceiroMes['NFEmitidasDevolvidas'];
			//Valor total de devolu��es
			$valorDevolucaoPrimeiroMes = $res1PrimeiroMes['RealizadoDevolvido'] + $res2PrimeiroMes['RealizadoDevolvido'];
			$valorDevolucaoPrimeiroMes = gCorrigeNumero($valorDevolucaoPrimeiroMes);
			$valorDevolucaoSegundoMes = $res1SegundoMes['RealizadoDevolvido'] + $res2SegundoMes['RealizadoDevolvido'];
			$valorDevolucaoSegundoMes = gCorrigeNumero($valorDevolucaoSegundoMes);
			$valorDevolucaoTerceiroMes = $res1TerceiroMes['RealizadoDevolvido'] + $res2TerceiroMes['RealizadoDevolvido'];
			$valorDevolucaoTerceiroMes = gCorrigeNumero($valorDevolucaoTerceiroMes);

			
			$valorClientesNovosAno = 0;
			$quantidadeClientesNovosAno = 0;
			$quantidadeClientesNovosPrimeiroMes = 0;
			$quantidadeClientesNovosSegundoMes = 0;
			$quantidadeClientesNovosTerceiroMes = 0;
			$sql3PrimeiroMes = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Flag_Cancelada != 'S' {$clausulaC007}  {$whereC007_C007_Id} AND C007_Cargo like '%$C007_Cargo%' GROUP BY T007_D024_Id");
			$sql3SegundoMes = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Flag_Cancelada != 'S' {$clausulaC007} {$whereC007_C007_Id} AND C007_Cargo like '%$C007_Cargo%' GROUP BY T007_D024_Id");
			$sql3TerceiroMes = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Flag_Cancelada != 'S' {$clausulaC007} {$whereC007_C007_Id} AND C007_Cargo like '%$C007_Cargo%' GROUP BY T007_D024_Id");
			
			$valorClientesNovosPrimeiroMes = 0;
			while($res3PrimeiroMes = mysql_fetch_array($sql3PrimeiroMes)){
				$verifica = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_D024_Id = '{$res3PrimeiroMes['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialPrimeiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
				if(mysql_num_rows($verifica) <= 0){
					$quantidadeClientesNovosPrimeiroMes++;
					$sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$res3PrimeiroMes['T007_D024_Id']}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
					$resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
					$valorClientesNovosPrimeiroMes += $resRealizadosNovos['RealizadoNovos'];
				}
			}
			$quantidadeClientesNovosAno += $quantidadeClientesNovosPrimeiroMes;
			$valorClientesNovosAno += $valorClientesNovosPrimeiroMes;
			$valorClientesNovosPrimeiroMes = gCorrigeNumero($valorClientesNovosPrimeiroMes);
			$valorClientesNovosSegundoMes = 0;
			while($res3SegundoMes = mysql_fetch_array($sql3SegundoMes)){
				$verifica = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_D024_Id = '{$res3SegundoMes['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialSegundoMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
				if(mysql_num_rows($verifica) <= 0){
					$quantidadeClientesNovosSegundoMes++;
					$sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$res3SegundoMes['T007_D024_Id']}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
					$resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
					$valorClientesNovosSegundoMes += $resRealizadosNovos['RealizadoNovos'];
				}
			}	
			$quantidadeClientesNovosAno += $quantidadeClientesNovosSegundoMes;		
			$valorClientesNovosAno += $valorClientesNovosSegundoMes;
			$valorClientesNovosSegundoMes = gCorrigeNumero($valorClientesNovosSegundoMes);
			$valorClientesNovosTerceiroMes = 0;
			while($res3TerceiroMes = mysql_fetch_array($sql3TerceiroMes)){
				$verifica = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_D024_Id = '{$res3TerceiroMes['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialTerceiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
				if(mysql_num_rows($verifica) <= 0){
					$quantidadeClientesNovosTerceiroMes++;
					$sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$res3TerceiroMes['T007_D024_Id']}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
					$resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
					$valorClientesNovosTerceiroMes += $resRealizadosNovos['RealizadoNovos'];
				}
			}
			$quantidadeClientesNovosAno += $quantidadeClientesNovosTerceiroMes;	
			$valorClientesNovosAno += $valorClientesNovosTerceiroMes;
			$valorClientesNovosTerceiroMes = gCorrigeNumero($valorClientesNovosTerceiroMes);
			$diasUteisPrimeiroMes = calcularDiasUteis($dataInicialPrimeiroMes, $dataFinalPrimeiroMes);
			$diasUteisSegundoMes = calcularDiasUteis($dataInicialSegundoMes, $dataFinalSegundoMes);
			$diasUteisTerceiroMes = calcularDiasUteis($dataInicialTerceiroMes, $dataFinalTerceiroMes);
			$diasUteisAno = calcularDiasUteis($dataInicialAno, $dataFinalAno);

			$sql4PrimeiroMes = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedioPrimeiroMes FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sql4SegundoMes = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedioSegundoMes FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$sql4TerceiroMes = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedioTerceiroMes FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$res4PrimeiroMes = mysql_fetch_array($sql4PrimeiroMes);
			$res4SegundoMes = mysql_fetch_array($sql4SegundoMes);
			$res4TerceiroMes = mysql_fetch_array($sql4TerceiroMes);
			$prazoMediaVendasPrimeiroMes = round($res4PrimeiroMes['PrazoMedioPrimeiroMes']);
			$prazoMediaVendasSegundoMes = round($res4SegundoMes['PrazoMedioSegundoMes']);
			$prazoMediaVendasTerceiroMes = round($res4TerceiroMes['PrazoMedioTerceiroMes']);

			/****************************************************************************************************************************************************************************************************
			* 						CALCULO DOS DADOS ANUAIS
			*****************************************************************************************************************************************************************************************************/
			$sqlAno = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos/T007_Valor_Total_Custo) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$resAno = mysql_fetch_array($sqlAno);

			$valorRealizadoAno = gCorrigeNumero($resAno['Realizado']);
			$percentualAtingidoAno = gCorrigeNumero($resAno['Realizado']/($cotaAnual)*100);
			$cotaAnual = gCorrigeNumero($cotaAnual);
			$valorClientesAno = gCorrigeNumero($resAno['Realizado']);
			$quantidadeNFAno = $resAno['NFEmitidas'];

			$margemMediaAno = gCorrigeNumero($resAno['MargemVenda']);

			//Select T014 notas devolvidas
			$sqlAno1 = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialAno}' AND T013_Data_Entrada <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$resAno1 = mysql_fetch_array($sqlAno1);

			//Select T008 notas devolu��o emitidas
			$clausulaC007NF = ($C007_Id != '-1') ? "AND T7VN.T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";
			$sqlAno2 = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}') AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialAno}' AND T7DV.T007_Data_Emissao <= '{$dataFinalAno}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$resAno2 = mysql_fetch_array($sqlAno2);

			$valorDevolucaoAno = gCorrigeNumero($resAno1['RealizadoDevolvido'] + $resAno2['RealizadoDevolvido']);
			$quantidadeDevolucaoAno = $resAno1['NFEmitidasDevolvidas'] + $resAno2['NFEmitidasDevolvidas'];

			//$quantidadeClientesNovosAno = 0;
			//$sqlAno3 = mysql_query("SELECT * FROM T007 WHERE T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} GROUP BY T007_D024_Id");
			//$valorClientesNovosAno = 0;
			//while($resAno3 = mysql_fetch_array($sqlAno3)){
			//	$verifica = mysql_query("SELECT * FROM T007 WHERE T007_D024_Id = '{$resAno3['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialAno}'");
			//	if(mysql_num_rows($verifica) <= 0){
			//		$quantidadeClientesNovosAno++;
			//		$sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 WHERE T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$resAno3['T007_D024_Id']}'");
			//		$resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
			//		$valorClientesNovosAno += $resRealizadosNovos['RealizadoNovos'];
			//	}
			//}
			$valorClientesNovosAno = gCorrigeNumero($valorClientesNovosAno);

			$sqlAno4 = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedio FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
			$resAno4 = mysql_fetch_array($sqlAno4);

			$prazoMediaVendasAno = round($resAno4['PrazoMedio']);

			$html = <<<EOT
				<div style="max-width: 1078px; max-height: 100%; min-height: 765px; border: 4px solid #000; font-family: Arial; font-size: 12px;">
					<div style="border-bottom: 3px solid #000; margin: 2px 5px; font-weight: bold; font-size: 14px;">Controle de Vendas</div>
					<div style="border-bottom: 2px solid #000; margin: 2px 5px; font-weight: bold; font-size: 12px;">{$nomeVendedor}</div>
					<table cellspacing="4" style="font-family: Arial; font-size: 14px;" width="100%">
					<tr>
						<td width="20%"></td>
						<td width="20%" align="center" style="border-bottom: 2px solid #000;">{$primeiroMes}</td>
						<td width="20%" align="center" style="border-bottom: 2px solid #000;">{$segundoMes}</td>
						<td width="20%" align="center" style="border-bottom: 2px solid #000;">{$terceiroMes}</td>
						<td width="20%" align="center" style="border-bottom: 2px solid #000;">{$ano}</td>
					</tr>
					<tr>
						<td width="20%">Dias úteis</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisAno}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Cota Mensal</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$_cotaMensalPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$_cotaMensalSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$_cotaMensalTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$cotaAnual}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Realizado</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoAno}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">% Atingido</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoPrimeiroMes}%</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoSegundoMes}%</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoTerceiroMes}%</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoAno}%</td>
					</tr>
					<tr style="margin-top: 5px;">
						<td width="20%" style="border-bottom: 2px solid #000; font-weight: bold;">Clientes Atendidos</td>
						<td width="20%"></td>
						<td width="20%"></td>
						<td width="20%"></td>
						<td width="20%"></td>
					</tr>
					<tr>
						<td width="20%">Quantidade</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesAno}</td>
					</tr>
					<tr>
						<td width="20%">Valor</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesAno}</td>
					</tr>
					<tr style="margin-top: 5px;">
						<td width="20%" style="border-bottom: 2px solid #000; font-weight: bold;">Clientes Novos</td>
						<td width="20%"></td>
						<td width="20%"></td>
						<td width="20%"></td>
						<td width="20%"></td>
					</tr>
					<tr>
						<td width="20%">Quantidade</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosAno}</td>
					</tr>
					<tr>
						<td width="20%">Valor</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosAno}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Qt NFs Emitidas</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFAno}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Qt Devoluções</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoAno}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Vl Devoluções</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoAno}</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Margem Média</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaPrimeiroMes}%</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaSegundoMes}%</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaTerceiroMes}%</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaAno}%</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold;">Pzo Medio Vendas</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasPrimeiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasSegundoMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasTerceiroMes}</td>
						<td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasAno}</td>
					</tr>
					<tr>
						<td colspan="5" style="border-bottom: 2px solid #000;"></td>
					</tr>
					</table>
EOT;
			$diasMax = 0;
			$totalSemana = array();
			//$mudouAno = false;
     		foreach($periodosTerceiroMes[0] as $periodo => $periodos){
 				$dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano);
				$dataFinalPeriodo = formataDataMes($periodosTerceiroMes[3][$periodo], $ano);
 				if($dataInicialPeriodo > $dataFinalPeriodo){
					$dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano-1);
					//$mudouAno = true;
				}
		    	$dataInicialPeriodo = strtotime($dataInicialPeriodo);
		    	$dataFinalPeriodo = strtotime($dataFinalPeriodo);
		    	$datediff = $dataFinalPeriodo - $dataInicialPeriodo;
	    		$dias = floor($datediff/(60*60*24)) - 1;
		    	if($dias > $diasMax) $diasMax = $dias;
		 	}
		 	//$mudouAno = false;			
			foreach($periodosTerceiroMes[0] as $periodo => $periodos){
				$dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano);
				$dataFinalPeriodo = formataDataMes($periodosTerceiroMes[3][$periodo], $ano);
				if($dataInicialPeriodo > $dataFinalPeriodo){
					$dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano-1);
					//$mudouAno = true;
				}
				$cotaSemana = $_cotaSemana[$periodo];
				$semana = $periodo+1;
				$html .= <<<EOT
					<table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
								Semana {$semana}
							</td>
						</tr>
EOT;
				$dataInicialPeriodo = date("Y-m-d", strtotime("-1 day", strtotime($dataInicialPeriodo)));
				$diasSemana = 0;
				$sqlValidaData=mysql_query("SELECT DATEDIFF('{$dataFinalPeriodo}', '{$dataInicialPeriodo}') as dias");
				$mSqlValidaData=mysql_fetch_array($sqlValidaData);
				if($mSqlValidaData['dias']>50 || $mSqlValidaData['dias'] <= 0){
					$stringValida = utf8_encode("Erro: Verifique se as configura��es do relat�rio de vendas, deste mês do seu usu�rio, est�o no padr�o incorreto.");
                    die($stringValida);
				}
				while($dataInicialPeriodo != $dataFinalPeriodo){
					$dataInicialPeriodo = date("Y-m-d", strtotime("+1 day", strtotime($dataInicialPeriodo)));
					if(date("w", strtotime($dataInicialPeriodo)) != 0 && date("w", strtotime($dataInicialPeriodo)) != 6){
						$diasSemana++; //Contador utilizado para ajustar o layout
						$diaSemana = substr(gCorrigeData($dataInicialPeriodo), 0, 5);
						$sqlDia = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as valorTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao = '{$dataInicialPeriodo}' {$clausulaC007} AND T007_Flag_Cancelada != 'S' AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
						$resDia = mysql_fetch_array($sqlDia);
						$valorDia = gCorrigeNumero($resDia['valorTotal']);
						
						$valorSemana += $resDia['valorTotal'];

						//Select T014 notas devolu��es recebidas
						$sqlDevolucao = mysql_query("SELECT SUM(T014_Valor_Total_Custo) as valorTotal FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno LEFT JOIN D006 ON D006_Id = T014_D006_Id WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada = '{$dataInicialPeriodo}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
						$resDevolucao = mysql_fetch_array($sqlDevolucao);
						
						//Select T008 notas devolu��o emitidas
						$clausulaC007NF = ($C007_Id != '-1') ? "AND T7VN.T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";
						$sqlDevolucao2 = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS valorTotal FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id  LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE (T7DV.T007_C004_Id='{$g['empresaAtual']}' OR T7VN.T007_C004_Id='{$g['empresaAtual']}') AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao = '{$dataInicialPeriodo}' AND C007_Cargo like '%$C007_Cargo%'");
						$resDevolucao2 = mysql_fetch_array($sqlDevolucao2);

						$valorDevolucaoSemana += $resDevolucao['valorTotal'] + $resDevolucao2['valorTotal'];

						$html .= <<<EOT
							<tr>
								<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$diaSemana}</td>
								<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia}</td>
							</tr>
EOT;

					}
				}
				$i = $diasMax - $diasSemana;
				if($i > 0){
					for($j = 0; $j < $i; $j++){
						$html .= <<<EOT
							<tr>
								<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;"></td>
								<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;"></td>
							</tr>
EOT;
					}
				}
				$totalSemanaMenosDevolucao = $valorSemana - $valorDevolucaoSemana;
				$totalMenosDevolucaoMes += $totalSemanaMenosDevolucao;
				$totalMenosDevolucao = gCorrigeNumero($totalSemanaMenosDevolucao);
				$totalMes += $valorSemana;
				$valorSemana = gCorrigeNumero($valorSemana);
				$totalDevolucaoMes += $valorDevolucaoSemana;
				$valorDevolucaoSemana = gCorrigeNumero($valorDevolucaoSemana);
				$percentualSemana = gCorrigeNumero($totalSemanaMenosDevolucao/$cotaSemana*100);
				$cotaMensal += $cotaSemana; 
				$cotaSemana = gCorrigeNumero($cotaSemana);
				$html .= <<<EOT
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$valorSemana}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$valorDevolucaoSemana}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucao}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaSemana}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualSemana}%</td>
					</tr>
EOT;
				$totalSemana[$periodo] = $totalMenosDevolucao;
				$valorSemana = $valorDevolucaoSemana = $totalMenosDevolucao = 0;
			}
			//die();
			$html .= <<<EOT
				<table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
					<tr>
						<td colspan="2" style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
							Acumulado Mês
						</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 1</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[0]}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 2</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[1]}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 3</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[2]}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 4</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[3]}</td>
					</tr>
EOT;
			$i = $diasMax - 4;
			for($j = 0; $j < $i; $j++){
				$html .= <<<EOT
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;"></td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;"></td>
					</tr>
EOT;
			}
			$percentualMes = gCorrigeNumero($totalMenosDevolucaoMes/$cotaMensal*100);
			$totalMes = gCorrigeNumero($totalMes);
			$totalDevolucaoMes = gCorrigeNumero($totalDevolucaoMes);
			$totalMenosDevolucaoMes = gCorrigeNumero($totalMenosDevolucaoMes);
			$cotaMensal = gCorrigeNumero($cotaMensal);
			$html .= <<<EOT
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$totalMes}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalDevolucaoMes}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucaoMes}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaMensal}</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
						<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualMes}%</td>
					</tr>
				</table>
EOT;


/*
			$dia_inicial = $dataInicialTerceiroMes;

			$quantidade_dias = ($dias_mes - $dia_inicial) +1 ;
			$contadorSemana = 1;
			$mes = array();
			for ($i = 0; $i < $quantidade_dias; $i++) {
				$novaData = strtotime(date("Y-m-d", strtotime($data_inicial)) . " +$i day");
				$dataBanco = date('Y-m-d', $novaData);
				if(date("w", $novaData) == 6){
					$contadorSemana++;
				}
				$mes[$contadorSemana][date("w", $novaData)] = $dataBanco;
			}

			//print_r($mes);
			$contadorSemana = 0;
			$totalSemana = array();
			$valorDevolucao = array();
			$percentualSemana = array();
			$cotaSemanal = array();
			foreach($mes as $semana){
				$valorDia = array();
				$valorSemana = 0;
				$valorDevolucaoSemana = 0;
				foreach($semana as $key => $dia) {
					$sqlDia = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as valorTotal FROM T007 WHERE T007_Data_Emissao = '{$dia}' {$clausulaC007} AND T007_Flag_Cancelada != 'S'");
					$resDia = mysql_fetch_array($sqlDia);
					$semana[$key] = strtotime($semana[$key]);
					$semana[$key] = date('d', $semana[$key]).'/'.substr(gRetornaMes(date("n", $semana[$key])), 0, 3);
					$valorDia[$key] = gCorrigeNumero($resDia['valorTotal']);
					if($key != 0 && $key != 6) $valorSemana += $resDia['valorTotal'];

					$sqlDevolucao = mysql_query("SELECT SUM(T013_Valor_Total) as valorTotal FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T013_D006_Id WHERE T013_Data_Entrada = '{$dia}' AND T013_Data_Entrada <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV'");
					$resDevolucao = mysql_fetch_array($sqlDevolucao);
					$valorDevolucaoSemana += $resDevolucao['valorTotal'];
				}
				$contadorSemana++;
				$valorDevolucao[$contadorSemana] = gCorrigeNumero($valorDevolucaoSemana);
				$totalSemana[$contadorSemana] = gCorrigeNumero($valorSemana);
				$totalSemanaMenosDevolucao = $valorSemana - $valorDevolucaoSemana;
				$totalMenosDevolucao[$contadorSemana] = gCorrigeNumero($totalSemanaMenosDevolucao);

				$diasSemana = (count($semana) > 5) ? count($semana)-2 : count($semana);
				$valorCotaSemanal = $C007_Cota_Vendas_Diaria*$diasSemana;

				$percentualSemana[$contadorSemana] = gCorrigeNumero($totalSemanaMenosDevolucao/$valorCotaSemanal*100);
				
				$cotaSemanal[$contadorSemana] = gCorrigeNumero($valorCotaSemanal);
				$html .= <<<EOT
					<table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
								Semana {$contadorSemana}
							</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[1]}</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[1]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[2]}</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[2]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[3]}</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[3]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[4]}</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[4]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[5]}</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[5]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$totalSemana[$contadorSemana]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$valorDevolucao[$contadorSemana]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucao[$contadorSemana]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaSemanal[$contadorSemana]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualSemana[$contadorSemana]}%</td>
						</tr>
					</table>
EOT;
			}
			$html .= <<<EOT
					<table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
						<tr>
							<td colspan="2" style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
								Acumulado Mês
							</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 1</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[1]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 2</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[2]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 3</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[3]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 4</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[4]}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 5</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[5]}</td>
						</tr>
EOT;
			foreach($totalSemana as $key => $valor){
				$totalSemana[$key] = gCorrigeNumeroInverte($valor);
			}
			foreach($valorDevolucao as $key => $valor){
				$valorDevolucao[$key] = gCorrigeNumeroInverte($valor);
			}
			foreach($totalMenosDevolucao as $key => $valor){
				$totalMenosDevolucao[$key] = gCorrigeNumeroInverte($valor);
			}
			foreach($cotaSemanal as $key => $valor){
				$cotaSemanal[$key] = gCorrigeNumeroInverte($valor);
			}
			$totalMes = gCorrigeNumero(array_sum($totalSemana));
			$totalDevolucaoMes = gCorrigeNumero(array_sum($valorDevolucao));
			$totalMenosDevolucaoMes = gCorrigeNumero(array_sum($totalMenosDevolucao));
			$cotaMensal = gCorrigeNumero(array_sum($cotaSemanal));
			$percentualMes = gCorrigeNumero(array_sum($totalSemana)/array_sum($cotaSemanal)*100);
			$html .= <<<EOT
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$totalMes}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalDevolucaoMes}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucaoMes}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaMensal}</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
							<td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualMes}%</td>
						</tr>
					</table>
EOT;
			$html .= "</div>";*/
			echo $html;
		break;
	}

	function calcularDiasUteis($DataInicial, $DataFinal){
		$diasUteis = 0;
		while($DataInicial != $DataFinal){
			$diaSemana = date("w", strtotime($DataInicial));
			if($diaSemana != 0 && $diaSemana != 6){
				$diasUteis++;
			}
			$DataInicial = date("Y-m-d", strtotime("+1 day", strtotime($DataInicial)));
		}	

		$diaSemana = date("w", strtotime($DataInicial));
		if($diaSemana != 0 && $diaSemana != 6){
			$diasUteis++;
		}	
		return $diasUteis;
	}

	function formataDataMes($dataMes, $ano){
		$valor = explode("/", $dataMes);
		return $ano."-".$valor[1]."-".$valor[0];
	}
