<?php
namespace hardness;

switch($g['r'][3]){
	case 'inventario':
		$filtroObrigatorio = true;

		/**
		* Definição da SQL
		*/
		$from = 'D001';
		$extra = <<<EOT
			LEFT JOIN D005 ON D001_D005_Id=D005_Id
            LEFT JOIN D049 ON D049_D001_Id=D001_Id
            LEFT JOIN D009 ON D009_D049_Id=D049_Id
            LEFT JOIN C008 ON C008_Id=D001_C008_Id
            LEFT JOIN D003 ON D003_Id=D001_D003_Id
            LEFT JOIN D002 ON D002_Id=D001_D002_Id
            LEFT JOIN D060 ON D060_Id=D001_D060_Id
            LEFT JOIN D037 ON D037_Id=D001_D037_Id
            LEFT JOIN D024 ON D024_Id=D049_D024_Id
			LEFT JOIN D041 ON D041_D049_Id=D049_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}'
            WHERE D009_C004_Id = '{$g['empresaAtual']}'
			GROUP BY D009_Id
			ORDER BY D001_Codigo_Produto
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/

		$selectC008 = gGeraSelect('C008', 'C008_Id', 'C008_Tipo', 'ORDER BY C008_Tipo', true);
		$selectC008[0] = array('title' => 'TODOS', 'value' => '');
		$selectD060 = gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo', "order by D060_Descricao_Grupo", true);
		$selectD060[0] = array('title' => 'TODOS', 'value' => '');
		$selectD003 = gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true);
		$selectD003[0] = array('title' => 'TODOS', 'value' => '');
		$selectD002 = gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto', ' order by D002_Descricao_Produto', true);
		$selectD002[0] = array('title' => 'TODOS', 'value' => '');
		$gridFiltro = array(
			'T001_Data_Lancamento' => array('titulo' => 'Data Base', 'tipo' => 'data', 'naoGerarExtra' => true),
            'D001_C008_Id' => array('titulo' => 'Departamento', 'tipo' => 'select', 'select' => $selectC008),
            'D001_D060_Id' => array('titulo' => 'Setor', 'tipo' => 'select', 'select' => $selectD060),
            'D001_D003_Id' => array('titulo' => 'Grupo', 'tipo' => 'select', 'select' => $selectD003),
            'D001_D002_Id' => array('titulo' => 'Sub-Grupo', 'tipo' => 'select', 'select' => $selectD002),
            //'D001_Codigo_Produto' => array('titulo' => 'Produto', 'tipo' => 'text'),
            'D049_Origem_Mercadoria' => array('titulo' => 'Nac/Imp', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'TODOS', 'value' => ''),
                    array('title' => 'NACIONAL', 'value' => '0;3;4;5'),
                    array('title' => 'IMPORTADO', 'value' => '1;2;6;7'),
                )
            ),
            'custoInventario' => array('titulo' => 'Custo', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Custo última compra', 'value' => '1'),
					array('title' => 'Custo médio', 'value' => '2'),
					array('title' => 'Custo tabela', 'value' => '3'),
					array('title' => 'Custo manual', 'value' => '4'),
					array('title' => 'Custo Preenchido', 'value' => '5'),
					array('title' => 'Não Exibir a Coluna Custo', 'value' => '0'),
                ), 'naoGerarExtra' => true
            ),
            'Estoque' => array('titulo' => 'Estoque', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => '4'),
					array('title' => 'Positivo', 'value' => '1'),
					array('title' => 'Zero', 'value' => '2'),
					array('title' => 'Negativo', 'value' => '3'),
                ), 'naoGerarExtra' => true
            ),
            'IFNULL(D149_Flag_ST[[VIRGULA]]"N")' => array('titulo' => 'NCM ST', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'text'),
            'D001_Flag_Tipo_Categoria' => array('titulo' => 'Categoria', 'tipo' => 'text'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Definição do grid
		*/
		$Valor_Preco=0; //Estava fixo no relatório anterior
		$Valores = $g['ui']->getGridFiltroCampoBusca('custoInventario');
		$Estoque = $g['ui']->getGridFiltroCampoBusca('Estoque');
		$T001_Data_Lancamento = $g['ui']->getGridFiltroCampoBusca('T001_Data_Lancamento');
		if(($T001_Data_Lancamento[0] != $T001_Data_Lancamento[1]) && $T001_Data_Lancamento[1]>0){
			$erro = <<<EOT
				<span style="font-size:20px;">
					<br />
					&nbsp;&nbsp;As datas-base devem ser iguais, por favor corrija o filtro
				</span>
EOT;
			die($erro);
		}
		if(empty($T001_Data_Lancamento[0])){
			$T001_Data_Lancamento[0] = date('d/m/Y');
		}
		$T001_Data_Lancamento = gCorrigeDataInverte($T001_Data_Lancamento[0]);

		if($T001_Data_Lancamento == date("Y-m-d")) {
            // Caso a data do relatorio seja a data de hoje
			$Saldo = "(SELECT SUM(T066_Quantidade_Estoque) FROM T066 WHERE T066_D009_Id=D009_Id) as Saldo";
    		$ICMS  = '0 as ICMS';
			if ($Valores == 3) {
			    $Custo = "D009_Valor_Custo_Tabela as Custo";
			} else if ($Valores == 4) {
			    $Custo = "D009_Valor_Preco_Tabela as Custo";
			} else if ($Valores == 5) {
				$Custo = "D009_Valor_Custo_Compra as Custo";
			} else {
				$Custo = "D009_Valor_Custo_Unitario as Custo";
    		}

		} else {
            // Caso a data do relatorio seja retroativa 
			$Saldo = "(SELECT T001_Quantidade_Saldo_Mes_Anterior FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento > '{$T001_Data_Lancamento}' LIMIT 1) as Saldo";
    		$ICMS  = "(SELECT T001_Valor_ICMS  FROM T001 LEFT JOIN D006 ON D006_Id=T001_D006_Id WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <= '{$T001_Data_Lancamento}' AND D006_Flag_Deb_Crd_ICMS = 'S' AND T001_Valor_ICMS > 0 AND T001_Flag_Operacao='E' AND D006_Flag_Venda_Compra_Outros='C' ORDER BY T001_Id desc LIMIT 1) as ICMS";
			if($Valores == 2){
    			$Custo = "(SELECT T001_Valor_Custo_Medio_Mes_Anterior FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento > '{$T001_Data_Lancamento}' LIMIT 1) as Custo";
			} else if ($Valores == 1) {
        		$Custo = "(SELECT T001_Valor_Custo_Mes_Anterior FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento > '{$T001_Data_Lancamento}' LIMIT 1) as Custo";
    		} else if ($Valores == 3) {
    		    $Custo = "D009_Valor_Custo_Tabela as Custo";
    		} else if ($Valores == 4) {
    		    $Custo = "D009_Valor_Preco_Tabela as Custo";
			} else if ($Valores == 5) {
				$Custo = "D009_Valor_Custo_Compra as Custo";
    		}
		}
		log($ICMS);
		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Item', 'style' => 'width: 1%', 'callback' => 'gContador'),
			array('colunaValor' => "(SELECT T001_Id FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento > '{$T001_Data_Lancamento}' limit 1) as ultimoID", 'colunaTitulo' => 'ultimoID', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Valor_Custo_Ultima_Compra', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => $Saldo, 'colunaTitulo' => 'Saldo', 'style' => 'width: 1%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
		);
		if($Valores > 0){
			$colunaCusto = array('colunaValor' => $Custo, 'colunaTitulo' => 'Custo', 'style' => 'width: 1%', 'colunaAlinhar' => 'right');
			$colunaCusto['colunaCasasDecimais'] = '2';
			$grid[] = $colunaCusto;
			if ($Valores == 5) {
				$grid[] = array('colunaValor' => "'' as Tipo_Custo", 'colunaTitulo' => 'Tipo custo', 'style' => 'width: 1%', 'colunaAlinhar' => 'center');
			}
			$grid[] = array('colunaValor' => '0 as Total', 'colunaTitulo' => 'Total custo', 'style' => 'width: 1%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2');
		}
		$grid[] = array('colunaValor' => $ICMS, 'colunaTitulo' => 'ICMS Créd', 'style' => 'width: 1%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2');
		$grid[] = array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso Kg', 'style' => 'width: 1%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3');
		$grid[] = array('colunaValor' => '0 as Peso_Total', 'colunaTitulo' => 'Total Kg', 'style' => 'width: 1%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3');
		$grid[] = array('colunaValor' => 'D149_Flag_ST', 'colunaTitulo' => 'NCM ST', 'style' => 'width: 1%', 'colunaAlinhar' => 'center');
		$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaAlinhar' => 'left', 'style' => 'width: 1%');
		$grid[] = array('colunaValor' => 'D001_Flag_Tipo_Item', 'colunaTitulo' => 'Categoria', 'style' => 'width: 1%', 'colunaAlinhar' => 'center');

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, '100000');

		$geradoDados = array();
		$geradoSql = str_replace(" LIMIT 0,100000", "", $geradoSql);
		//print_r($geradoSql);
		$sql = mysql_query($geradoSql);
		while($row = mysql_fetch_assoc($sql)){
			$custoOrigem = '';
			if ($Valores == 5 && $row['Custo'] > 0) {
				if ($row['D009_Flag_Custo_Utilizado'] == 'MA') {
					$custoOrigem = 'M';
				} else if ($row['D009_Flag_Custo_Utilizado'] == 'M') {
					$custoOrigem = 'ME';
				} else if ($row['D009_Flag_Custo_Utilizado'] == 'U') {
					$custoOrigem = 'U';
				} else if ($row['D009_Flag_Custo_Utilizado'] == 'T') {
					$custoOrigem = 'T';
				}
			}

			if ($row['ultimoID']<=0){
	            $row['Saldo'] = $row['D009_Quantidade_Estoque'];
	            if ($Valores == 2) {
	                $row['Custo'] = $row['D009_Valor_Custo_Medio_Unitario'];
	    		} else if ($Valores == 3) {
	    		    $row['Custo'] = $row['D009_Valor_Custo_Tabela'];
	    		} else if ($Valores == 4) {
	    		    $row['Custo'] = $row['D009_Valor_Preco_Tabela'];
	            } else if ($Valores != 5) {
	                $row['Custo'] = $row['D009_Valor_Custo_Ultima_Compra'];
	            }
	        }

			if ($Estoque=='1' and $row['Saldo']<=0) {
            	continue;
	        } else if ($Estoque=='2' and ($row['Saldo']>0 or $row['Saldo']<0)) {
	            continue;
	        } else if ($Estoque=='3' and $row['Saldo']>=0) {
	            continue;
	        }

			$linha = array();
			foreach ($row as $key => $value) {
				if ($key === "Tipo_Custo" && $Valores == 5) {
					$linha[] = $custoOrigem;
					continue;
				}
				if($key === "Total"){
					$linha[] = $row['Saldo']*$row['Custo'];
					continue;
				}
				if($key === "Peso_Total"){
					$linha[] = $row['Saldo']*$row['D001_Peso_Unitario_Kg'];
					continue;
				}
				$linha[] = $value;
			}
			$geradoDados[] = $linha;

			$total    += ($row['Saldo']*$row['Custo']);
        	$total_kg += ($row['Saldo']*$row['D001_Peso_Unitario_Kg']);
        	$row_ = $row;
		}
		$linha = array();
		foreach ($row_ as $key => $value) {
			if($key === "Total"){
				$linha[] = $total;
				continue;
			}
			if($key === "Peso_Total"){
				$linha[] = $total_kg;
				continue;
			}
			$linha[] = '';
		}
		$geradoDados[] = $linha;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('removerScrollLoading', true);
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'estoque':
		$filtroObrigatorio = true;
		//$g['cacheCount'] = true;
		/**
		* Definição da SQL
		*/
		$from = 'C004';
		$extra = <<<EOT
EOT;
		//Utilizado nas subquery FROM D001
		$extraD001 = <<<EOT
			LEFT JOIN D005 ON D001_D005_Id=D005_Id
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN D002 ON D002_Id=D001_D002_Id
			LEFT JOIN D060 ON D060_Id=D001_D060_Id
			LEFT JOIN D037 ON D037_Id=D001_D037_Id
			LEFT JOIN D024 ON D024_Id=D049_D024_Id
			LEFT JOIN C004 ON C004_Id=D009_C004_Id
EOT;
		//Utilizado nas subquery FROM T001
		$extraT001 = <<<EOT
			LEFT JOIN D009 ON D009_Id=T001_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D005 ON D001_D005_Id=D005_Id
			LEFT JOIN D002 ON D002_Id=D001_D002_Id
			LEFT JOIN D060 ON D060_Id=D001_D060_Id
			LEFT JOIN D037 ON D037_Id=D001_D037_Id
			LEFT JOIN D024 ON D024_Id=D049_D024_Id
			LEFT JOIN C004 ON C004_Id=D009_C004_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data', 'naoGerarExtra' => true),
			'C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto', 'naoGerarExtra' => true),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                    array('title' => 'MÊS', 'campoAgrupar' => 'Agrupar', 'campoMostrar' => 'Agrupar', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),//, 'colunasOcultar' => array('T007_Data_Emissao')),
                )
            ),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Definição do header
		*/
		$C004_Nome_Abreviado = $g['ui']->getGridFiltroCampoExtra('C004_Nome_Abreviado');
		$C004 = mysql_query("SELECT * FROM C004 WHERE (1=1) $C004_Nome_Abreviado ORDER BY C004_Id");

		$grid = array(array('colunaValor' => "'' as DRE", 'colunaTitulo' => 'D.R.E.', 'style' => 'font-weight: bold; font-size: 1.1em', 'colunaOrdenar' => false));
		while($rowC004 = mysql_fetch_assoc($C004)){
			$C004_Nome_Abreviado = str_replace(array(' ','-'), '_', $rowC004['C004_Nome_Abreviado']);
			$grid[] = array('colunaValor' => "'' as {$C004_Nome_Abreviado}", 'colunaTitulo' => $rowC004['C004_Nome_Abreviado'], 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false);
			$empresas[] = $rowC004['C004_Id'];
		}
		$grid[] = array('colunaValor' => "'' as TOTAL", 'colunaTitulo' => 'TOTAL', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false);
		$grid[] = array('colunaValor' => "'' as Agrupar", 'colunaTitulo' => 'TOTAL', 'colunaAlinhar' => 'right', 'colunaExibir' => false);

		$gridOriginal = $grid;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$D024_Nome_Empresa = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa');
		$D024_Nome_Empresa = (!empty($D024_Nome_Empresa)) ? "AND D024_Nome_Empresa LIKE '%{$D024_Nome_Empresa}%'" : "";

		$extra = str_replace('ORDER BY Agrupar ASC', '', $extra);
		$extra .= " {$D024_Nome_Empresa} GROUP BY C004_Id";

		$T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
		$Data_Inicial 	   = gCorrigeDataInverte($T007_Data_Emissao[0]);
		$Data_Final 	   = gCorrigeDataInverte($T007_Data_Emissao[1]);

		/**
		 *	Monta o array do grid do relatório
		 */
		$geradoDados = array();

		$linhaEstoqueAnterior 		= array('Saldo Anterior');
		$linhaEntradas 				= array('Entradas');
		$linhaSaidas 				= array('Saídas');
		$linhaEstoqueFinal 			= array('Saldo Final');
		$linhaQtdeAnteriorFinal 	= array('Saldo Final - Saldo Anterior');
		$linhaCustoEstoqueAnterior 	= array('Anterior');
		$linhaCustoEstoqueFinal 	= array('Final');
		$linhaCustoAnteriorFinal 	= array('Final - Anterior');

		//Estoque Anterior
		$linhaTmpDiferencaSaldo = array();
		$linhaTmpDiferencaCusto = array();
		$linhaTmpCustoInicial   = array();
		$linhaTmpSaldoInicial   = array();

		$extra_ = gInsertExtraWhere($extra, "(SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <= '{$Data_Inicial}' ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1) > 0");
		$query = "SELECT SUM((SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento < '{$Data_Inicial}' ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1)) Saldo,
						 SUM((SELECT (T001_Valor_Custo_Unitario*T001_Quantidade_Saldo) FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento < '{$Data_Inicial}' ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1)) Custo,
						 C004_Id
				    FROM D001 {$extraD001} {$extra_}";
		$query = mysql_query($query);
		while($mEstoqueInicial = mysql_fetch_array($query)){
			$linhaTmpCustoInicial[$mEstoqueInicial['C004_Id']] = $mEstoqueInicial['Custo'];
			$linhaTmpSaldoInicial[$mEstoqueInicial['C004_Id']] = $mEstoqueInicial['Saldo'];

			$linhaTmpDiferencaCusto[$mEstoqueInicial['C004_Id']] = $mEstoqueInicial['Custo'];
			$linhaTmpDiferencaSaldo[$mEstoqueInicial['C004_Id']] = $mEstoqueInicial['Saldo'];
		}
		foreach($empresas as $C004_Id){
			$linhaEstoqueAnterior[] = $linhaTmpSaldoInicial[$C004_Id];
		}
		$linhaEstoqueAnterior[] = array_sum($linhaTmpSaldoInicial);
		$linhaEstoqueAnterior[] = 'Estoque';
		$geradoDados[] = $linhaEstoqueAnterior;


		//Entradas
		$linhaTmp = array();
		$query = "SELECT SUM(T001_Quantidade) estoqueEntradas, C004_Id FROM T001 {$extraT001} {$extra}";
		$query = mysql_query(gInsertExtraWhere($query, "T001_Data_Lancamento >= '{$Data_Inicial}' AND T001_Data_Lancamento <= '{$Data_Final}' AND T001_Flag_Operacao='E'"));
		while($mEstoqueEntradas = mysql_fetch_array($query)){
			$linhaTmp[$mEstoqueEntradas['C004_Id']] = $mEstoqueEntradas['estoqueEntradas'];
		}
		foreach($empresas as $C004_Id){
			$linhaEntradas[] = $linhaTmp[$C004_Id];
		}
		$linhaEntradas[] = array_sum($linhaTmp);
		$linhaEntradas[] = 'Estoque';
		$geradoDados[] = $linhaEntradas;


		//Saidas
		$linhaTmp = array();
		$query = "SELECT SUM(T001_Quantidade) estoqueSaidas, C004_Id FROM T001 {$extraT001} {$extra}";
		$query = mysql_query(gInsertExtraWhere($query, "T001_Data_Lancamento >= '{$Data_Inicial}' AND T001_Data_Lancamento <= '{$Data_Final}' AND T001_Flag_Operacao='S'"));
		while($mEstoqueSaidas = mysql_fetch_array($query)){
			$linhaTmp[$mEstoqueSaidas['C004_Id']] = $mEstoqueSaidas['estoqueSaidas'];
		}
		foreach($empresas as $C004_Id){
			$linhaSaidas[] = $linhaTmp[$C004_Id];
		}
		$linhaSaidas[] = array_sum($linhaTmp);
		$linhaSaidas[] = 'Estoque';
		$geradoDados[] = $linhaSaidas;


		//Estoque Final
		$linhaTmpCustoFinal = array();
		$linhaTmpSaldoFinal = array();
		$extra_ = gInsertExtraWhere($extra, "(SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <= '{$Data_Final}' ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1) > 0");
		$query = "SELECT SUM((SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <= '{$Data_Final}' ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1)) Saldo,
						 SUM((SELECT (T001_Valor_Custo_Unitario*T001_Quantidade_Saldo) FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <= '{$Data_Final}' ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1)) Custo,
						 C004_Id
				    FROM D001 {$extraD001} {$extra_}";
		$query = mysql_query($query);
		while($mEstoqueFinal = mysql_fetch_array($query)){
			$linhaTmpCustoFinal[$mEstoqueFinal['C004_Id']] = $mEstoqueFinal['Custo'];
			$linhaTmpSaldoFinal[$mEstoqueFinal['C004_Id']] = $mEstoqueFinal['Saldo'];

			$linhaTmpDiferencaCusto[$mEstoqueFinal['C004_Id']] = $mEstoqueFinal['Custo'] - $linhaTmpDiferencaCusto[$mEstoqueFinal['C004_Id']];
			$linhaTmpDiferencaSaldo[$mEstoqueFinal['C004_Id']] = $mEstoqueFinal['Saldo'] - $linhaTmpDiferencaSaldo[$mEstoqueFinal['C004_Id']];
		}
		foreach($empresas as $C004_Id){
			$linhaEstoqueFinal[] = $linhaTmpSaldoFinal[$C004_Id];
		}
		$linhaEstoqueFinal[] = array_sum($linhaTmpSaldoFinal);
		$linhaEstoqueFinal[] = 'Estoque';
		$geradoDados[] = $linhaEstoqueFinal;


		//Diferença de saldo
		foreach($empresas as $C004_Id){
			$linhaQtdeAnteriorFinal[] = $linhaTmpDiferencaSaldo[$C004_Id];
		}
		$linhaQtdeAnteriorFinal[] = array_sum($linhaTmpDiferencaSaldo);
		$linhaQtdeAnteriorFinal[] = 'Estoque';
		$geradoDados[] = $linhaQtdeAnteriorFinal;


		//Custo inicial
		foreach($empresas as $C004_Id){
			$linhaCustoEstoqueAnterior[] = $linhaTmpCustoInicial[$C004_Id];
		}
		$linhaCustoEstoqueAnterior[] = array_sum($linhaTmpCustoInicial);
		$linhaCustoEstoqueAnterior[] = 'Custo';
		$geradoDados[] = $linhaCustoEstoqueAnterior;


		//Custo final
		foreach($empresas as $C004_Id){
			$linhaCustoEstoqueFinal[] = $linhaTmpCustoFinal[$C004_Id];
		}
		$linhaCustoEstoqueFinal[] = array_sum($linhaTmpCustoFinal);
		$linhaCustoEstoqueFinal[] = 'Custo';
		$geradoDados[] = $linhaCustoEstoqueFinal;


		//Diferença de custo
		foreach($empresas as $C004_Id){
			$linhaCustoAnteriorFinal[] = $linhaTmpDiferencaCusto[$C004_Id];
		}
		$linhaCustoAnteriorFinal[] = array_sum($linhaTmpDiferencaCusto);
		$linhaCustoAnteriorFinal[] = 'Custo';
		$geradoDados[] = $linhaCustoAnteriorFinal;


		/**
		 * Geração: Grid
		 */
		$geradoDados = $g['ui']->processaArrayDados($gridOriginal, $geradoDados);
		$gridTabela  = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
}
