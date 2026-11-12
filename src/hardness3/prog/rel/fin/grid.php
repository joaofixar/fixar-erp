<?php
namespace hardness;

// Carrega funções callback
require_once('grid_func-callback.php');

switch($g['r'][3]){
	case 'dre':
		global $g;
		$filtroObrigatorio = true;
		/**
		* Definição da SQL
		*/
		$from = 'T007';
		$extra = <<<EOT
				LEFT JOIN C004 ON C004_Id=T007_C004_Id
				LEFT JOIN D024 ON D024_Id=T007_D024_Id
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		//Clausula where sem lef join para levar os filtros da tela de DRE para a tela de demonstrativo
		$filtroDemonstrativo = $g['ui']->gridFiltroExtra(true, $gridFiltro, "");

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['DRE'] == '(+) Receita bruta' || $array['todosCampos']['DRE'] == '(=) Receita liquida' || $array['todosCampos']['DRE'] == '(=) Resultado bruto' || $array['todosCampos']['DRE'] == '(=) Resultado antes IRPJ' || $array['todosCampos']['DRE'] == '(=) Lucro bruto' || $array['todosCampos']['DRE'] == '(=) Lucro líquido'){
				return 'linhaNegrito';
			}
		}

		/**
		* Definição do header
		*/
		$C004_Nome_Abreviado = $g['ui']->getGridFiltroCampoExtra('C004_Nome_Abreviado');
		$C004 = mysql_query("SELECT * FROM C004 WHERE (1=1) $C004_Nome_Abreviado ORDER BY C004_Id");

		$grid = array(array('colunaValor' => "'' as DRE", 'colunaTitulo' => 'D.R.E.', 'colunaOrdenar' => false));
		while($rowC004 = mysql_fetch_assoc($C004)){
			$C004_Nome_Abreviado = str_replace(array(' ','-'), '_', $rowC004['C004_Nome_Abreviado']);
			$C004_Nome_Abreviado = str_replace('&', 'E', $C004_Nome_Abreviado);

			$grid[] = array('colunaValor' => "'' as {$C004_Nome_Abreviado}",  'colunaTitulo' => $rowC004['C004_Nome_Abreviado'], 'colunaAlinhar' => 'right', 'colunaOrdenar' => false, 'callback' => 'demoItemDRE',  'callbackParameter' => "{$rowC004['C004_Id']}|{$filtroDemonstrativo}");
			$empresas[] = $rowC004['C004_Id'];
		}
		$grid[] = array('colunaValor' => "'' as TOTAL", 'colunaTitulo' => 'TOTAL', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false);

		$gridOriginal = $grid;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$extra .= " GROUP BY C004_Id ORDER BY C004_Id";

		/**
		 *	Monta o array do grid do relatório
		 */
		$geradoDados = array();

		$linhaReceitas 				= array('(+) Receita bruta');
		$linhaImpostos 				= array('(-) Impostos');
		$linhaDevolucoes 			= array('(-) Devolucoes');
		$linhaReceitaLiquida 		= array('(=) Receita liquida');
		$linhaCustoProdutos 		= array('(-) Custo produtos');
		$linhaResultadoBruto 		= array('(=) Resultado bruto');
		$linhaDespesaVenda 			= array('(-) Despesa venda');
		$linhaDespesaAdministrativa = array('(-) Despesa administrativa');
		$linhaResultadoAntesIRPJ 	= array('(=) Resultado antes IRPJ');
		$linhaProvisaoIRPJ 			= array('(-) Provisao IRPJ');
		$linhaLucroBruto 			= array('(=) Lucro bruto');
		$linhaParticipacaoSocios 	= array('(-) Participacao socios');
		$linhaInvestimentos 	    = array('(-) Investimentos');
		$linhaLucroLiquido 			= array('(=) Lucro líquido');

		//Receitas
		$linhaTmp = array();
		$query = "select sum(T007_Valor_Total), C004_Id from T007 left join D006 on D006_Id=T007_D006_Id {$extra}";
		log("Query inicio = " . $query);
		$query = gInsertExtraWhere($query, "D006_Flag_Venda_Compra_Outros='V' and T007_Flag_Cancelada!='S' and D006_Flag_Devolucao!='S'");
		log("Query final = " . $query);
		$query = mysql_query($query);
		while($mReceitas = mysql_fetch_array($query)){
			$linhaTmp[$mReceitas['C004_Id']] = $mReceitas[0];
			$linhaTmpReceitaLiquida[$mReceitas['C004_Id']] = $mReceitas[0];
		}
		foreach($empresas as $C004_Id){
			$linhaReceitas[] = $linhaTmp[$C004_Id];
		}
		$linhaReceitas[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaReceitas;


		//Impostos
		// Buscando impostos da notas emitidas
		$linhaTmp = array();
		$query = "select sum(T007_Valor_Total_ICMS+T007_Valor_Total_IPI+T007_Valor_ICMS_Substituicao_Retencao+T007_Valor_Total_PIS+T007_Valor_Total_COFINS), C004_Id from T007 left join D006 on D006_Id=T007_D006_Id {$extra}";
		$query = mysql_query(gInsertExtraWhere($query, "D006_Flag_Venda_Compra_Outros='V' and T007_Flag_Cancelada!='S' and D006_Flag_Devolucao!='S'"));

		while($mImpostos = mysql_fetch_array($query)){
			$linhaTmp[$mImpostos['C004_Id']] = $mImpostos[0];
			$linhaTmpReceitaLiquida[$mImpostos['C004_Id']] -= $mImpostos[0];
		}

		// Buscando impostos lançados no lançamentos bancários com o grupo DRE 4 = impostos
		$query = "select sum(T019_Valor_Lancamento) *-1, C004_Id from T019 left join D014 on D014_Id=T019_D014_Id {$extra}";
		$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
		$query = str_replace('T007_C004_Id', 'T019_C004_Id', $query);
		$query = str_replace('T007_D024_Id', 'T019_D024_Id', $query);
		$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '4'"));

		while($mImpostos = mysql_fetch_array($query)){
			$linhaTmp[$mImpostos['C004_Id']] = $mImpostos[0];
			$linhaTmpReceitaLiquida[$mImpostos['C004_Id']] -= $mImpostos[0];
		}

		foreach($empresas as $C004_Id){
			$linhaImpostos[] = $linhaTmp[$C004_Id];
		}

		$linhaImpostos[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaImpostos;


		//Devoluções emitidas
		$linhaTmp = array();
		$query = "select sum(T007_Valor_Total), C004_Id from T007 left join D006 on D006_Id=T007_D006_Id {$extra}";
		$query = mysql_query(gInsertExtraWhere($query, "D006_Flag_Entrada_Saida='E' and T007_Flag_Cancelada!='S' and D006_Flag_Devolucao='S'"));
		while($mDevolucaoEmitida = mysql_fetch_array($query)){
			$linhaTmp[$mDevolucaoEmitida['C004_Id']] = $mDevolucaoEmitida[0];
			$linhaTmpReceitaLiquida[$mDevolucaoEmitida['C004_Id']] -= $mDevolucaoEmitida[0];
		}
		//Devoluções Recebidas
		$query = "select sum(T013_Valor_Total), C004_Id from T013 left join D006 on D006_Id=T013_D006_Id {$extra}";
		$query = str_replace('T007_Data_Emissao', 'T013_Data_Entrada', $query);
		$query = mysql_query(gInsertExtraWhere($query, "D006_Flag_Entrada_Saida='E' and T013_Flag_Cancelada!='S' and D006_Flag_Devolucao='S'"));
		while($mDevolucaoRecebida = mysql_fetch_array($query)){
			$linhaTmp[$mDevolucaoRecebida['C004_Id']] += $mDevolucaoRecebida[0];
			$linhaTmpReceitaLiquida[$mDevolucaoRecebida['C004_Id']] -= $mDevolucaoRecebida[0];
		}
		foreach($empresas as $C004_Id){
			$linhaDevolucoes[] = $linhaTmp[$C004_Id];
		}
		$linhaDevolucoes[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaDevolucoes;


		//Loop para montar linha receita líquida por empresa
		foreach($empresas as $C004_Id){
			$linhaReceitaLiquida[] = $linhaTmpReceitaLiquida[$C004_Id];
		}
		$linhaReceitaLiquida[] = array_sum($linhaTmpReceitaLiquida);
		$geradoDados[] = $linhaReceitaLiquida;


		//Loop para setar a linha do resultado bruto
		foreach($empresas as $C004_Id){
			$linhaTmpResultadoBruto[$C004_Id] = $linhaTmpReceitaLiquida[$C004_Id];
		}


		//Custo produtos
		$linhaTmp = array();
		$query = "select sum(T008_Valor_Total_Custo), C004_Id from T008 left join T007 on T007_Id=T008_T007_Id left join D006 on D006_Id=T007_D006_Id  {$extra}";
		$query = mysql_query(gInsertExtraWhere($query, "D006_Flag_Venda_Compra_Outros='V' and T007_Flag_Cancelada!='S' and D006_Flag_Devolucao!='S'"));
		while($mCustoProdutos = mysql_fetch_array($query)){
			$linhaTmp[$mCustoProdutos['C004_Id']] = $mCustoProdutos[0];
			$linhaTmpResultadoBruto[$mCustoProdutos['C004_Id']] -= $mCustoProdutos[0];
		}
		foreach($empresas as $C004_Id){
			$linhaCustoProdutos[] = $linhaTmp[$C004_Id];
		}
		$linhaCustoProdutos[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaCustoProdutos;


		//Loop para montar linha do resultado bruto
		foreach($empresas as $C004_Id){
			$linhaResultadoBruto[] = $linhaTmpResultadoBruto[$C004_Id];
		}
		$linhaResultadoBruto[] = array_sum($linhaTmpResultadoBruto);
		$geradoDados[] = $linhaResultadoBruto;


		//Loop para setar a linha do resultado antes IRPJ
		foreach($empresas as $C004_Id){
			$linhaTmpResultadoAntesIRPJ[$C004_Id] = $linhaTmpResultadoBruto[$C004_Id];
		}


		//Despesa venda
		$linhaTmp = array();
		$query = "select sum(T019_Valor_Lancamento) *-1, C004_Id from T019 left join D014 on D014_Id=T019_D014_Id {$extra}";
		$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
		$query = str_replace('T007_C004_Id', 'T019_C004_Id', $query);
		$query = str_replace('T007_D024_Id', 'T019_D024_Id', $query);
		$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '1'"));
		while($mDespesasVenda = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasVenda['C004_Id']] = $mDespesasVenda[0];
			$linhaTmpResultadoAntesIRPJ[$mDespesasVenda['C004_Id']] -= $mDespesasVenda[0];
		}
		foreach($empresas as $C004_Id){
			$linhaDespesaVenda[] = $linhaTmp[$C004_Id];
		}
		$linhaDespesaVenda[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaDespesaVenda;


		//Despesa administrativa
		$linhaTmp = array();
		$query = "select sum(T019_Valor_Lancamento) *-1, C004_Id from T019 left join D014 on D014_Id=T019_D014_Id {$extra}";
		$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
		$query = str_replace('T007_C004_Id', 'T019_C004_Id', $query);
		$query = str_replace('T007_D024_Id', 'T019_D024_Id', $query);
		$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '2'"));
		while($mDespesasAdministrativa = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasAdministrativa['C004_Id']] = $mDespesasAdministrativa[0];
			$linhaTmpResultadoAntesIRPJ[$mDespesasAdministrativa['C004_Id']] -= $mDespesasAdministrativa[0];
		}
		foreach($empresas as $C004_Id){
			$linhaDespesaAdministrativa[] = $linhaTmp[$C004_Id];
		}
		$linhaDespesaAdministrativa[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaDespesaAdministrativa;


		//Loop para montar linha do resultado antes IRPJ
		foreach($empresas as $C004_Id){
			$linhaResultadoAntesIRPJ[] = $linhaTmpResultadoAntesIRPJ[$C004_Id];
		}
		$linhaResultadoAntesIRPJ[] = array_sum($linhaTmpResultadoAntesIRPJ);
		$geradoDados[] = $linhaResultadoAntesIRPJ;


		//Loop para montar linha da provisao IRPJ (ATUALMENTE VALORES ZEROS)
		foreach($empresas as $C004_Id){
			$linhaTmpProvisaoIRPJ[$C004_Id] = 0;
			$linhaProvisaoIRPJ[] = 0;
		}
		$linhaProvisaoIRPJ[] = 0;
		$geradoDados[] = $linhaProvisaoIRPJ;


		//Loop para montar linha do lucro bruto
		foreach($empresas as $C004_Id){
			$linhaLucroBruto[] = $linhaTmpResultadoAntesIRPJ[$C004_Id] - $linhaTmpProvisaoIRPJ[$C004_Id];
			$linhaTmpLucroBruto[$C004_Id] = $linhaTmpResultadoAntesIRPJ[$C004_Id] - $linhaTmpProvisaoIRPJ[$C004_Id];
		}
		$linhaLucroBruto[] = array_sum($linhaTmpLucroBruto);
		$geradoDados[] = $linhaLucroBruto;


		//Loop para setar a linha do lucroLiquido
		foreach($empresas as $C004_Id){
			$linhaTmpLucroLiquido[$C004_Id] = $linhaTmpLucroBruto[$C004_Id];
		}


		//Participação sócios
		$linhaTmp = array();
		$query = "select IFNULL(sum(T019_Valor_Lancamento)*-1, 0), C004_Id from T019 left join D014 on D014_Id=T019_D014_Id {$extra}";
		$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
		$query = str_replace('T007_C004_Id', 'T019_C004_Id', $query);
		$query = str_replace('T007_D024_Id', 'T019_D024_Id', $query);
		$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '3'"));
		while($mDespesasParticipacao = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasParticipacao['C004_Id']] = $mDespesasParticipacao[0];
			$linhaTmpLucroLiquido[$mDespesasParticipacao['C004_Id']] -= $mDespesasParticipacao[0];
		}

		foreach($empresas as $C004_Id){
			$linhaParticipacaoSocios[] = $linhaTmp[$C004_Id];
		}

		$linhaParticipacaoSocios[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaParticipacaoSocios;

		//investimentos
		$linhaTmp = array();
		$query = "select IFNULL(sum(T019_Valor_Lancamento)*-1, 0), C004_Id from T019 left join D014 on D014_Id=T019_D014_Id {$extra}";
		$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
		$query = str_replace('T007_C004_Id', 'T019_C004_Id', $query);
		$query = str_replace('T007_D024_Id', 'T019_D024_Id', $query);
		$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '5'"));
		while($mDespesasInvestimentos = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasInvestimentos['C004_Id']] = $mDespesasInvestimentos[0];
			$linhaTmpLucroLiquido[$mDespesasInvestimentos['C004_Id']] -= $mDespesasInvestimentos[0];
		}

		foreach($empresas as $C004_Id){
			$linhaInvestimentos[] = $linhaTmp[$C004_Id];
		}

		$linhaInvestimentos[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaInvestimentos;


		//Loop para montar linha do resultado antes IRPJ
		foreach($empresas as $C004_Id){
			$linhaLucroLiquido[] = $linhaTmpLucroLiquido[$C004_Id];
		}
		$linhaLucroLiquido[] = array_sum($linhaTmpLucroLiquido);
		$geradoDados[] = $linhaLucroLiquido;

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

	case 'dreFinanceiro':
		global $g;
		$filtroObrigatorio = true;
		/**
		* Definição da SQL
		*/
		$from = 'T019';
		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id=T019_C004_Id
			LEFT JOIN D024 ON D024_Id=T019_D024_Id
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
			'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		//Clausula where sem lef join para levar os filtros da tela de DRE para a tela de demonstrativo
		$filtroDemonstrativo = $g['ui']->gridFiltroExtra(true, $gridFiltro, "");

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
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['DRE'] == '(+) Receita bruta' || $array['todosCampos']['DRE'] == '(=) Receita liquida' || $array['todosCampos']['DRE'] == '(=) Lucro bruto' || $array['todosCampos']['DRE'] == '(=) Lucro líquido'){
				return 'linhaNegrito';
			}
		}

		/**
		* Definição do header
		*/
		$C004_Nome_Abreviado = $g['ui']->getGridFiltroCampoExtra('C004_Nome_Abreviado');
		$C004 = mysql_query("SELECT * FROM C004 WHERE (1=1) $C004_Nome_Abreviado ORDER BY C004_Id");

		$grid = array(array('colunaValor' => "'' as DRE", 'colunaTitulo' => 'D.R.E.', 'colunaOrdenar' => false));
		while($rowC004 = mysql_fetch_assoc($C004)){
			$C004_Nome_Abreviado = str_replace(array(' ','-'), '_', $rowC004['C004_Nome_Abreviado']);
			$C004_Nome_Abreviado = str_replace('&', 'E', $C004_Nome_Abreviado);

			$grid[] = array('colunaValor' => "'' as {$C004_Nome_Abreviado}",  'colunaTitulo' => $rowC004['C004_Nome_Abreviado'], 'colunaAlinhar' => 'right', 'colunaOrdenar' => false, 'callback' => 'demoItemDRE',  'callbackParameter' => "{$rowC004['C004_Id']}|{$filtroDemonstrativo}"); 
			$empresas[] = $rowC004['C004_Id'];
		}
		$grid[] = array('colunaValor' => "'' as TOTAL", 'colunaTitulo' => 'TOTAL', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false);

		$gridOriginal = $grid;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$extra .= " GROUP BY C004_Id ORDER BY C004_Id";

		/**
		 *	Monta o array do grid do relatório
		 */
		$geradoDados = array();

		$linhaReceitas 				= array('(+) Receita bruta');
		$linhaImpostos 				= array('(-) Impostos');
		$linhaDevolucoes 			= array('(-) Devolucoes');
		$linhaReceitaLiquida 		= array('(=) Receita liquida');
		$linhaFornecedores  		= array('(-) Fornecedores');
		$linhaDespesaVenda 			= array('(-) Despesa venda');
		$linhaDespesaAdministrativa = array('(-) Despesa administrativa');
		$linhaLucroBruto 			= array('(=) Lucro bruto');
		$linhaParticipacaoSocios 	= array('(-) Participacao socios');
		$linhaInvestimentos 	    = array('(-) Investimentos');
		$linhaLucroLiquido 			= array('(=) Lucro líquido');

		// Buscando receitas bruta nos lançamentos bancários com o grupo DRE 6
		$linhaTmp = array();
		$query = "SELECT SUM(T019_Valor_Lancamento), 
				         C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id 
			            {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '6'");
		$query = mysql_query($query);

		while($mReceitas = mysql_fetch_array($query)){
			$linhaTmp[$mReceitas['C004_Id']] = $mReceitas[0];
			$linhaTmpReceitaLiquida[$mReceitas['C004_Id']] = $mReceitas[0];
		}
		foreach($empresas as $C004_Id){
			$linhaReceitas[] = $linhaTmp[$C004_Id];
		}

		$linhaReceitas[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaReceitas;

		// Buscando impostos nos lançamentos bancários com o grupo DRE 4
		$linhaTmp = array();
		$query = "SELECT SUM(T019_Valor_Lancamento) * -1, 
		                 C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id 
			            {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '4'");
		$query = mysql_query($query);

		while($mImpostos = mysql_fetch_array($query)){
			$linhaTmp[$mImpostos['C004_Id']] = $mImpostos[0];
			$linhaTmpReceitaLiquida[$mImpostos['C004_Id']] -= $mImpostos[0];
		}

		foreach($empresas as $C004_Id){
			$linhaImpostos[] = $linhaTmp[$C004_Id];
		}

		$linhaImpostos[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaImpostos;
		
		//Buscando devoluções nos lançamentos bancários com o grupo DRE 7
		$linhaTmp = array();
		$query = "SELECT SUM(T019_Valor_Lancamento) * -1, 
		                 C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id 
			            {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '7'");
		$query = mysql_query($query);

		while($mDevolucoes = mysql_fetch_array($query)){
			$linhaTmp[$mDevolucoes['C004_Id']] = $mDevolucoes[0];
			$linhaTmpReceitaLiquida[$mDevolucoes['C004_Id']] -= $mDevolucoes[0];
		}

		foreach($empresas as $C004_Id){
			$linhaDevolucoes[] = $linhaTmp[$C004_Id];
		}
		$linhaDevolucoes[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaDevolucoes;


		//Loop para montar linha receita líquida por empresa
		foreach($empresas as $C004_Id){
			$linhaReceitaLiquida[] = $linhaTmpReceitaLiquida[$C004_Id];
		}
		$linhaReceitaLiquida[] = array_sum($linhaTmpReceitaLiquida);
		$geradoDados[] = $linhaReceitaLiquida;


		//Loop para setar a linha do lucro bruto
		foreach($empresas as $C004_Id){
			$linhaTmpLucroBruto[$C004_Id] = $linhaTmpReceitaLiquida[$C004_Id];
		}

		// Buscando pagamento fornecedores nos lançamentos bancários com o grupo DRE 8
		$linhaTmp = array();
		$query = "SELECT SUM(T019_Valor_Lancamento) * -1, 
		                 C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id 
			            {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '8'");
		$query = mysql_query($query);

		while($mFornecedores = mysql_fetch_array($query)){
			$linhaTmp[$mFornecedores['C004_Id']] = $mFornecedores[0];
			$linhaTmpLucroBruto[$mFornecedores['C004_Id']] -= $mFornecedores[0];
		}

		foreach($empresas as $C004_Id){
			$linhaFornecedores[] = $linhaTmp[$C004_Id];
		}
		$linhaFornecedores[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaFornecedores;

		// Buscando depesas vendas nos lançamentos bancários com o grupo DRE 1
		$linhaTmp = array();
		$query = "SELECT SUM(T019_Valor_Lancamento) *-1, 
					     C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id 
			             {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '1'");
		$query = mysql_query($query);

		while($mDespesasVenda = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasVenda['C004_Id']] = $mDespesasVenda[0];
			$linhaTmpLucroBruto[$mDespesasVenda['C004_Id']] -= $mDespesasVenda[0];
		}
		foreach($empresas as $C004_Id){
			$linhaDespesaVenda[] = $linhaTmp[$C004_Id];
		}
		$linhaDespesaVenda[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaDespesaVenda;


		// Buscando depesas administrativas nos lançamentos bancários com o grupo DRE 2
		$linhaTmp = array();
		$query = "SELECT sum(T019_Valor_Lancamento) *-1, 
						 C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '2'");
		$query = mysql_query($query);

		while($mDespesasAdministrativa = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasAdministrativa['C004_Id']] = $mDespesasAdministrativa[0];
			$linhaTmpLucroBruto[$mDespesasAdministrativa['C004_Id']] -= $mDespesasAdministrativa[0];
		}
		foreach($empresas as $C004_Id){
			$linhaDespesaAdministrativa[] = $linhaTmp[$C004_Id];
		}
		$linhaDespesaAdministrativa[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaDespesaAdministrativa;

		//Loop para montar linha do lucro bruto
		foreach($empresas as $C004_Id){
			$linhaLucroBruto[] = $linhaTmpLucroBruto[$C004_Id];
		}
		$linhaLucroBruto[] = array_sum($linhaTmpLucroBruto);
		$geradoDados[] = $linhaLucroBruto;


		//Loop para setar a linha do lucroLiquido
		foreach($empresas as $C004_Id){
			$linhaTmpLucroLiquido[$C004_Id] = $linhaTmpLucroBruto[$C004_Id];
		}

		// Buscando participação dos sócios nos lançamentos bancários com o grupo DRE 3
		$linhaTmp = array();
		$query = "SELECT IFNULL(SUM(T019_Valor_Lancamento)*-1, 0), 
		                 C004_Id 
					FROM T019 
			   LEFT JOIN D014 ON D014_Id=T019_D014_Id 
			             {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '3'");
		$query = mysql_query($query);

		while($mDespesasParticipacao = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasParticipacao['C004_Id']] = $mDespesasParticipacao[0];
			$linhaTmpLucroLiquido[$mDespesasParticipacao['C004_Id']] -= $mDespesasParticipacao[0];
		}

		foreach($empresas as $C004_Id){
			$linhaParticipacaoSocios[] = $linhaTmp[$C004_Id];
		}

		$linhaParticipacaoSocios[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaParticipacaoSocios;

		// Buscando investimentos nos lançamentos bancários com o grupo DRE 5
		$linhaTmp = array();
		$query = "SELECT IFNULL(SUM(T019_Valor_Lancamento)*-1, 0),
				         C004_Id 
					FROM T019 
			   LEFT JOIN D014 On D014_Id=T019_D014_Id 
			             {$extra}";

		$query = gInsertExtraWhere($query, "D014_Grupo_DRE = '5'");
		$query = mysql_query($query);

		while($mDespesasInvestimentos = mysql_fetch_array($query)){
			$linhaTmp[$mDespesasInvestimentos['C004_Id']] = $mDespesasInvestimentos[0];
			$linhaTmpLucroLiquido[$mDespesasInvestimentos['C004_Id']] -= $mDespesasInvestimentos[0];
		}

		foreach($empresas as $C004_Id){
			$linhaInvestimentos[] = $linhaTmp[$C004_Id];
		}

		$linhaInvestimentos[] = array_sum($linhaTmp);
		$geradoDados[] = $linhaInvestimentos;

		//Loop para montar linha do resultado lucro líquido
		foreach($empresas as $C004_Id){
			$linhaLucroLiquido[] = $linhaTmpLucroLiquido[$C004_Id];
		}
		$linhaLucroLiquido[] = array_sum($linhaTmpLucroLiquido);
		$geradoDados[] = $linhaLucroLiquido;

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

	case 'fluxoCaixa':
		$filtroObrigatorio = true;
		$g['cacheCount'] = true;
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		$extra = <<<EOT
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
			'T002_Data_Vencimento' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T002_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
            'Saldo_Bancario' => array('titulo' => 'Incluir saldo bancário', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'SIM', 'value' => 'S', 'selected' => true),
                    array('title' => 'NÃO', 'value' => 'N'),
                ), 'naoGerarExtra' => true
            ),
            'Titulos_Cobranca' => array('titulo' => 'Incluir títulos cobrança', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'SIM', 'value' => 'S'),
                    array('title' => 'NÃO', 'value' => 'N'),
                ), 'naoGerarExtra' => true
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
		$grid = array(
			array('colunaValor' => 'T002_Id as Campo', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaOrdenar' => false, 'colunaAlinhar' => 'center'),
			array('colunaValor' => '0 as Campo2', 'colunaTitulo' => 'Dia', 'colunaOrdenar' => false, 'colunaAlinhar' => 'center'),
			array('colunaValor' => '0 as Campo3', 'colunaTitulo' => 'A Receber (+)', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo4', 'colunaTitulo' => 'Projeção (+)', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo5', 'colunaTitulo' => 'A Pagar (-)', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo6', 'colunaTitulo' => 'Projeção (-)', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo7', 'colunaTitulo' => 'Saldo Proj', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo8', 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo9', 'colunaTitulo' => 'Recebido (+)', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo10', 'colunaTitulo' => 'Pago (-)', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => '0 as Campo11', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '2', 'colunaOrdenar' => false, 'colunaAlinhar' => 'right'),
		);
		$gridOriginal = $grid;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 *	Monta o array do grid do relatório
		 */
		$geradoDados = array();

		//Imprime o saldo das contas caso selecionado no filtro
		$Saldo_Bancario = $g['ui']->getGridFiltroCampoBusca('Saldo_Bancario');
		$Titulos_Cobranca = $g['ui']->getGridFiltroCampoBusca('Titulos_Cobranca');
		$extraC004 = $g['ui']->getGridFiltroCampoExtra('T002_C004_Id');

		if($Saldo_Bancario == 'S'){
			$extraD007 = str_replace("T002_C004_Id", "D007_C004_Id", $extraC004);
			$sqlD007 = mysql_query("SELECT * FROM D007 WHERE D007_Flag_Ativo = 'S' {$extraD007}");
			while ($mD007 = mysql_fetch_array($sqlD007)) {
			 	if ($mD007['D007_Flag_Fluxo_Caixa'] == 'S') {
			    	$geradoDados[] = array('', '', '', '', 'Saldo ' . $mD007['D007_Nome_Banco'], '', $mD007['D007_Valor_Saldo'], '', '', '', '');
			    	$Saldo_Real += $mD007['D007_Valor_Saldo'];
			    	$Saldo_Projetado+= $mD007['D007_Valor_Saldo'];
			  	}
			}
		}
		//$extra = gRetirarGroupBy(gRetirarOrderBy($extra));
		//Extra T002
		$extraT002 = $extra;
		$extraT002 = gInsertExtraWhere($extraT002, "T002_Data_Recebimento='0000-00-00' AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL) AND T002_Flag_Status!='2'");
		$extraT002 = ($Titulos_Cobranca == 'N') ? gInsertExtraWhere($extraT002, "T002_Flag_Status!='3'") : $extraT002;
		//Extra T005
		$extraT005 = str_replace("T002_C004_Id", "T005_C004_Id", $extra);
		$extraT005 = str_replace("T002_Data_Vencimento", "T005_Data_Emissao", $extraT005);
		$extraT005 = gInsertExtraWhere($extraT005, "T005_Flag_Status = '7'");
		//Extra T015
		$extraT015 = str_replace("T002_C004_Id", "T015_C004_Id", $extra);
		$extraT015 = str_replace("T002_Data_Vencimento", "T015_Data_Vencimento", $extraT015);
		$extraT015 = gInsertExtraWhere($extraT015, "T015_Data_Pagamento='0000-00-00' AND (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL)");
		//Extra T019
		$extraT019 = str_replace("T002_C004_Id", "T019_C004_Id", $extra);
		$extraT019 = str_replace("T002_Data_Vencimento", "T019_Data_Lancamento", $extraT019);
		//Extra T019 - 1
		$extraT019_1 = gInsertExtraWhere($extraT019, "T019_Valor_Lancamento > 0");
        //Extra T019 - 2
		$extraT019_2 = gInsertExtraWhere($extraT019, "T019_Valor_Lancamento < 0");
		//Extra T018
        $extraT018 = str_replace("T002_C004_Id", "T018_C004_Id", $extra);
        $extraT018 = str_replace("T002_Data_Vencimento", "CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento,' 00:00:00')", $extraT018);
        //Extra T018 - 1
		$extraT018_1 = gInsertExtraWhere($extraT018, "D014_Flag_Tipo='RC' AND T018_Valor_Saldo > 0");
        //Extra T018 - 2
		$extraT018_2 = gInsertExtraWhere($extraT018, "D014_Flag_Tipo='PG' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo > 0))");
        //Extra T018 - 3
		$extraT018_3 = gInsertExtraWhere($extraT018, "concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)>=curdate() AND D014_Flag_Tipo='PG' AND T018_Valor_Saldo > 0");

		$sqlGrid = "SELECT T002_Data_Emissao,
                           T002_Data_Vencimento,
                           SUM(T002_Valor_Receber) T002_Valor_Receber,
                           SUM(T002_Valor_Recebido) T002_Valor_Recebido,
                           SUM(T005_Valor_Romaneio) T005_Valor_Romaneio,
                           SUM(T002_Valor_Pagar) T002_Valor_Pagar,
                           SUM(T002_Valor_Pago) T002_Valor_Pago,
                           SUM(Projecao_RC) Projecao_RC,
                           SUM(Projecao_PG) Projecao_PG,
                           SUM(Projecao_PG_Projetado) Projecao_PG_Projetado
                      FROM (

                      	(SELECT T002_Data_Emissao,
                                IF(T117_Data_Vencimento IS NOT NULL,T117_Data_Vencimento,T002_Data_Vencimento) T002_Data_Vencimento,
                                SUM(T002_Valor_Total) T002_Valor_Receber,
                                0 T002_Valor_Recebido,
                                0 T005_Valor_Romaneio,
                                0 T002_Valor_Pagar,
                                0 T002_Valor_Pago,
                                0 Projecao_RC,
                                0 Projecao_PG,
                                0 Projecao_PG_Projetado
                            FROM T002
                       LEFT JOIN T117 on T117_Id=T002_T117_Id
                           	{$extraT002}
                        GROUP BY IF(T117_Data_Vencimento IS NOT NULL,T117_Data_Vencimento,T002_Data_Vencimento)
						ORDER BY IF(T117_Data_Vencimento IS NOT NULL,T117_Data_Vencimento,T002_Data_Vencimento)) 

                      UNION ALL

                         (SELECT T019_Data_Lancamento,
                                 T019_Data_Lancamento,
                                 0 T002_Valor_Receber,
                                 SUM(T019_Valor_Lancamento) T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            FROM T019
                            {$extraT019_1}
                        GROUP BY T019_Data_Lancamento
						ORDER BY T019_Data_Lancamento)

                      UNION ALL

                         (SELECT T005_Data_Emissao,
                                 T005_Data_Emissao,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 SUM(T005_Valor_Total-ifnull((SELECT SUM(T019_Valor_Lancamento) FROM T019 WHERE T019_T005_Id=T005_Id),0)) T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            FROM T005
                           	{$extraT005}
                        GROUP BY T005_Data_Emissao
						ORDER BY T005_Data_Emissao)

                      UNION ALL

                         (SELECT T015_Data_Emissao,
                                 T015_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 SUM(T015_Valor_Total) T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            FROM T015
                            {$extraT015}
                        GROUP BY T015_Data_Vencimento
						ORDER BY T015_Data_Vencimento)

                      UNION ALL

                         (SELECT T019_Data_Lancamento,
                                 T019_Data_Lancamento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 SUM(T019_Valor_Lancamento) T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            FROM T019
                            {$extraT019_2}
                        GROUP BY T019_Data_Lancamento
						ORDER BY T019_Data_Lancamento)

                      UNION ALL

                         (SELECT concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 SUM(T018_Valor_Saldo) Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            FROM T018
                       LEFT JOIN D014 on D014_Id=T018_D014_Id
                            {$extraT018_1}
                        GROUP BY concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)
						ORDER BY concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento))

                      UNION ALL

                         (SELECT concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 SUM(T018_Valor_Saldo) Projecao_PG_Projetado
                            FROM T018
                       LEFT JOIN D014 on D014_Id=T018_D014_Id
                            {$extraT018_2}
                        GROUP BY concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)
						ORDER BY concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento))

                      UNION ALL

                         (SELECT concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 SUM(T018_Valor_Saldo) Projecao_PG,
                                 0 Projecao_PG_Projetado
                            FROM T018
                       LEFT JOIN D014 on D014_Id=T018_D014_Id
                            {$extraT018_3}
                        GROUP BY concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)
						ORDER BY concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento))

                    ) B GROUP BY T002_Data_Vencimento ORDER BY T002_Data_Vencimento";
		$sqlGrid = mysql_query($sqlGrid);

		while($row = mysql_fetch_array($sqlGrid)) {
	    	if($row['T002_Data_Vencimento'] >= date("Y-m-d")) {
            	$Saldo_Real  += ($row['T002_Valor_Receber']+$row['T005_Valor_Romaneio']+$row['Projecao_RC'])-($row['T002_Valor_Pagar']+$row['Projecao_PG']);
          	}
          	$Saldo_Projetado += ($row['T002_Valor_Receber']+$row['T005_Valor_Romaneio']+$row['Projecao_RC'])-($row['T002_Valor_Pagar']+$row['Projecao_PG_Projetado']);
          	$Saldo_Realizado += $row['T002_Valor_Recebido']+$row['T002_Valor_Pago'];

          	$geradoDados[] = array(
          		$row['T002_Data_Vencimento'],
          		substr(diaSemana(date("w", strtotime($row['T002_Data_Vencimento']))+1),0,3),
          		$row['T002_Valor_Receber']+$row['T005_Valor_Romaneio'],
          		$row['Projecao_RC'],
				gCorrigeNumero($row['T002_Valor_Pagar']),
				$row['Projecao_PG_Projetado'],
				$Saldo_Projetado,
				$Saldo_Real,
				$row['T002_Valor_Recebido'],
				$row['T002_Valor_Pago'],
				$Saldo_Realizado,
			);

        	$mTotal[0]+=$row['T002_Valor_Receber']+$row['T005_Valor_Romaneio'];
        	$mTotal[1]+=$row['T002_Valor_Pagar'];
        	$mTotal[2]+=$row['Projecao_RC'];
        	$mTotal[3]+=$row['Projecao_PG'];
        	$mTotal[4]+=$row['T002_Valor_Recebido'];
        	$mTotal[5]+=$row['T002_Valor_Pago'];
        }
        $geradoDados[] = array(
      		'',
      		'Total',
      		$mTotal[0],
      		$mTotal[2],
			gCorrigeNumero($mTotal[1]),
			$mTotal[3],
			'',
			'',
			$mTotal[4],
			$mTotal[5],
			'',
		);

		//Primeira linha grid
		$primeiraLinhaGridHead = "<tr><th colspan=\"4\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\"></th>";
		$primeiraLinhaGridHead.= "<th colspan=\"6\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">A Realizar</th>";
		$primeiraLinhaGridHead.= "<th colspan=\"3\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">Realizado</th></tr>";

		/**
		 * Geração: Grid
		 */
		$geradoDados = $g['ui']->processaArrayDados($gridOriginal, $geradoDados);
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		$g['smarty']->assign('removerScrollLoading', true);
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'balancete':
		$filtroObrigatorio = true;
		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$extra = <<<EOT
			left join T018 on T018_D014_Id=D014_Id and T018_Ano='$Ano' and T018_Mes='$Mes'
            left join D032 on D032_Id=D014_D032_Id
            where D032_Flag_Ativo='S'
            and D014_Flag_Ativo='S'
            order by D032_Tipo,D032_Descricao,D014_SubConta
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
			'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data', 'naoGerarExtra' => true),
            'T018_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                ), 'naoGerarExtra' => true
            ),
            'Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Realizado', 'value' => '1'),
					array('title' => 'A Realizar', 'value' => '2'),
                ), 'naoGerarExtra' => true
            ),
            'Contas' => array('titulo' => 'Contas', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => '2'),
					array('title' => 'Apenas com Movimentação', 'value' => '1'),
                ), 'naoGerarExtra' => true
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
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if(!empty($array['todosCampos']['grupoTipo']) || !empty($array['todosCampos']['D032_Descricao'])){
				return 'linhaNegrito';
			}
		}

		/**
		* Definição do header
		*/
		$Tipo = $g['ui']->getGridFiltroCampoBusca('Tipo');
		$Contas = $g['ui']->getGridFiltroCampoBusca('Contas');
		$T019_Data_Lancamento = $g['ui']->getGridFiltroCampoBusca('T019_Data_Lancamento');
		$T019_Data_Lancamento[0] = gCorrigeDataInverte($T019_Data_Lancamento[0]);
		$T019_Data_Lancamento[1] = gCorrigeDataInverte($T019_Data_Lancamento[1]);
		$extraC004_Id = $g['ui']->getGridFiltroCampoBusca('T018_C004_Id');
		$extraC004_Id = !empty($extraC004_Id) ? "AND (T018_C004_Id = '{$extraC004_Id}')" : '';

		//Extra redefinido para adicionar o C004 no left join T018
		$extra = <<<EOT
			left join T018 on T018_D014_Id=D014_Id and T018_Ano='$Ano' and T018_Mes='$Mes' {$extraC004_Id}
            left join D032 on D032_Id=D014_D032_Id
            where D032_Flag_Ativo='S'
            and D014_Flag_Ativo='S'
            order by D032_Tipo,D032_Descricao,D014_SubConta
EOT;

		if($Tipo == '1') { //Valores realizados
			$extra1 = "T019_D014_Id=D014_Id AND T019_Data_Lancamento >= '{$T019_Data_Lancamento[0]}' AND T019_Data_Lancamento <= '{$T019_Data_Lancamento[1]}' AND T019_Valor_Lancamento > 0 " . str_replace("T018_C004_Id", "T019_C004_Id", $extraC004_Id);
			$extra2 = "T019_D014_Id=D014_Id AND T019_Data_Lancamento >= '{$T019_Data_Lancamento[0]}' AND T019_Data_Lancamento <= '{$T019_Data_Lancamento[1]}' AND T019_Valor_Lancamento < 0 " . str_replace("T018_C004_Id", "T019_C004_Id", $extraC004_Id);
			$grid = array(
				array('colunaValor' => 'D014_D032_Id', 'colunaExibir' => false),
				array('colunaValor' => 'D032_Tipo(D032_Tipo) as grupoTipo', 'colunaTitulo' => 'Tipo', 'colunaOrdenar' => false, 'colunaOcultar' => false, 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaOrdenar' => false, 'colunaOcultar' => false, 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaOrdenar' => false, 'colunaOcultar' => false, 'colunaAlinhar' => 'left'),
				array('colunaValor' => 'D014_Flag_Plano', 'colunaExibir' => false),
				array('colunaValor' => 'D014_Flag_Tipo', 'colunaExibir' => false),
				array('colunaValor' => 'D032_Tipo', 'colunaExibir' => false),
				array('colunaValor' => "(SELECT SUM(T019_Valor_Lancamento) FROM T019 WHERE {$extra1}) Recebido", 'colunaTitulo' => 'Entradas', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
				array('colunaValor' => "(SELECT SUM(T019_Valor_Lancamento*-1) FROM T019 WHERE {$extra2}) Pago", 'colunaTitulo' => 'Saidas', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
				array('colunaValor' => 'T018_Valor', 'colunaExibir' => false),
				array('colunaValor' => '0 as Total', 'colunaTitulo' => 'Total', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
				array('colunaValor' => '0 as Percentual', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'callback' => 'gInserePorcentagem', 'colunaAlinhar' => 'right'),
			);
		} else { //Valores a realizar
			$extra1 = "T002_D014_Id=D014_Id and T002_Data_Vencimento >= '{$T019_Data_Lancamento[0]}' AND T002_Data_Vencimento <= '{$T019_Data_Lancamento[1]}' AND T002_Data_Recebimento='0000-00-00' AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL) AND T002_Flag_Cancelada != 'S' " . str_replace("T018_C004_Id", "T002_C004_Id", $extraC004_Id);
			$extra2 = "T015_D014_Id=D014_Id and T015_Data_Vencimento >= '{$T019_Data_Lancamento[0]}' AND T015_Data_Vencimento <= '{$T019_Data_Lancamento[1]}' AND T015_Data_Pagamento='0000-00-00' AND (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL) AND T015_Flag_Cancelada != 'S' " . str_replace("T018_C004_Id", "T015_C004_Id", $extraC004_Id);
			$grid = array(
				array('colunaValor' => 'D014_D032_Id', 'colunaExibir' => false),
				array('colunaValor' => 'D032_Tipo(D032_Tipo) grupoTipo', 'colunaTitulo' => 'Tipo', 'colunaOrdenar' => false, 'colunaOcultar' => false, 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaOrdenar' => false, 'colunaOcultar' => false, 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaOrdenar' => false, 'colunaOcultar' => false, 'colunaAlinhar' => 'left'),
				array('colunaValor' => 'D014_Flag_Plano', 'colunaExibir' => false),
				array('colunaValor' => 'D014_Flag_Tipo', 'colunaExibir' => false),
				array('colunaValor' => 'D032_Tipo', 'colunaExibir' => false),
				array('colunaValor' => "(SELECT SUM(T002_Valor_Total) FROM T002 WHERE {$extra1}) Recebido", 'colunaTitulo' => 'Entradas', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
				array('colunaValor' => "(SELECT SUM(T015_Valor_Total) FROM T015 WHERE {$extra2}) Pago", 'colunaTitulo' => 'Saidas', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
				array('colunaValor' => 'T018_Valor', 'colunaExibir' => false),
				array('colunaValor' => '0 as Total', 'colunaTitulo' => 'Total', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
				array('colunaValor' => '0 as Percentual', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'callback' => 'gInserePorcentagem', 'colunaAlinhar' => 'right'),
			);
		}
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 10000);

		/**
		 *	Monta o array do grid do relatório
		 */
		$geradoDados = array();
		$Resultado=0;
	    $D032_Id='9999';
	    $D032_Tipo='9999';
		$sql = mysql_query($geradoSql);
		while($row = mysql_fetch_assoc($sql)){
			if($row['D032_Tipo'] != $D032_Tipo) {
				$D032_Tipo = $row['D032_Tipo'];

				$T018=mysql_query("SELECT SUM(T018_Valor) Total FROM D014 LEFT JOIN T018 on T018_D014_Id=D014_Id AND T018_Ano='$Ano' AND T018_Mes='$Mes' {$extraC004_Id} LEFT JOIN D032 on D032_Id=D014_D032_Id WHERE D014_D032_Id = '{$row['D014_D032_Id']}' AND D032_Tipo = '{$D032_Tipo}'");
            	$mT018=mysql_fetch_array($T018);

            	if($Tipo == '1') { //Valores realizados
            		 $T002=mysql_query("SELECT SUM(T019_Valor_Lancamento) FROM T019 LEFT JOIN D014 on D014_Id=T019_D014_Id LEFT JOIN D032 on D032_Id=D014_D032_Id WHERE D032_Tipo='{$row['D032_Tipo']}' AND {$extra1}");
	                $mT002=mysql_fetch_array($T002);

	                $T015=mysql_query("SELECT SUM(T019_Valor_Lancamento*-1) FROM T019 LEFT JOIN D014 on D014_Id=T019_D014_Id LEFT JOIN D032 on D032_Id=D014_D032_Id WHERE D032_Tipo='{$row['D032_Tipo']}' AND {$extra2}");
	                $mT015=mysql_fetch_array($T015);
            	} else { //Valores a realizar
            		$T002=mysql_query("SELECT SUM(T002_Valor_Total) FROM T002 LEFT JOIN D014 on D014_Id=T002_D014_Id LEFT JOIN D032 on D032_Id=D014_D032_Id WHERE D032_Tipo='{$row['D032_Tipo']}' AND {$extra1}");
	                $mT002=mysql_fetch_array($T002);

	                $T015=mysql_query("SELECT SUM(T015_Valor_Total) FROM T015 LEFT JOIN D014 on D014_Id=T015_D014_Id LEFT JOIN D032 on D032_Id=D014_D032_Id WHERE D032_Tipo='{$row['D032_Tipo']}' AND {$extra2}");
	                $mT015=mysql_fetch_array($T015);
            	}

            	$TotalD032=$mT002[0]-$mT015[0];
            	$TotalTipo = $TotalD032;

	            $mD032['T018_Valor']=$mT018['Total'];
	            $mD032['Recebido']=$mT002[0];
	            $mD032['Pago']=$mT015[0];

	            if (($Contas=='1') and ($TotalD032==0)) {
	                continue;
	            }
	            $linha = array();
	            foreach ($row as $key => $value) {
	            	if($key === "grupoTipo" || $key === "D032_Tipo"){
	            		$linha[] = $row[$key];
	            		continue;
	            	}
	            	if($key === "Recebido" || $key === "Pago"){
	            		$linha[] = $mD032[$key];
	            		continue;
	            	}
	            	if($key === "Total"){
	            		$linha[] = $TotalD032;
	            		continue;
	            	}
	            	if($key === "Percentual"){
	            		$linha[] = 100;
	            		continue;
	            	}
	            	$linha[] = '';
	            }
	            $geradoDados[] = $linha;
	        }

	        if($row['D014_D032_Id'] != $D032_Id) {
	        	$D032_Id = $row['D014_D032_Id'];

	        	$T018=mysql_query("SELECT SUM(T018_Valor) Total FROM D014 LEFT JOIN T018 ON T018_D014_Id=D014_Id AND T018_Ano='$Ano' AND T018_Mes='$Mes' {$extraC004_Id} WHERE D014_D032_Id = '{$row['D014_D032_Id']}'");
	            $mT018=mysql_fetch_array($T018);
	            if($Tipo=='1') {
	                $T002=mysql_query("SELECT SUM(T019_Valor_Lancamento) FROM T019 LEFT JOIN D014 ON D014_Id=T019_D014_Id WHERE D014_D032_Id = '{$row['D014_D032_Id']}' AND {$extra1}");
	                $mT002=mysql_fetch_array($T002);

	                $T015=mysql_query("SELECT SUM(T019_Valor_Lancamento*-1) FROM T019 LEFT JOIN D014 ON D014_Id=T019_D014_Id WHERE D014_D032_Id = '{$row['D014_D032_Id']}' AND {$extra2}");
	                $mT015=mysql_fetch_array($T015);
	            } else {
	                $T002=mysql_query("SELECT SUM(T002_Valor_Total) FROM T002 LEFT JOIN D014 ON D014_Id=T002_D014_Id WHERE D014_D032_Id = '{$row['D014_D032_Id']}' AND {$extra1}");
	                $mT002=mysql_fetch_array($T002);

	                $T015=mysql_query("SELECT SUM(T015_Valor_Total) FROM T015 LEFT JOIN D014 ON D014_Id=T015_D014_Id WHERE D014_D032_Id = '{$row['D014_D032_Id']}' AND {$extra2}");
	                $mT015=mysql_fetch_array($T015);
	            }

	            $TotalD032=$mT002[0]-$mT015[0];
	            $TotalGrupo = $TotalD032;

	            $mD032['T018_Valor'] = $mT018['Total'];
	            $mD032['Recebido'] = $mT002[0];
	            $mD032['Pago'] = $mT015[0];

	            $D032_Id=$row['D014_D032_Id'];

	            if (($Contas=='1') and ($TotalD032==0)) {
	                continue;
	            }

	            $linha = array();
	            foreach ($row as $key => $value) {
	            	if($key === "D032_Tipo" || $key === "D032_Descricao"){
	            		$linha[] = $row[$key];
	            		continue;
	            	}
	            	if($key === "Recebido" || $key === "Pago"){
	            		$linha[] = $mD032[$key];
	            		continue;
	            	}
	            	if($key === "Total"){
	            		$linha[] = $TotalD032;
	            		continue;
	            	}
	            	if($key === "Percentual"){
	            		$Percentual = ($TotalD032/$TotalTipo)*100;
	            		$linha[] = ($Percentual > 100 || $Percentual < 0) ? 0 : $Percentual;
	            		continue;
	            	}
	            	$linha[] = '';
	            }
	            $geradoDados[] = $linha;
          	}

	        $Total = $row['Recebido']-$row['Pago'];
	        $Resultado += $Total;
	        if($row['D014_Flag_Tipo']=='RC') {
	            $Total_Projecao+=$row['T018_Valor'];
	        } else {
	            $Total_Projecao-=$row['T018_Valor'];
	        }
	        $Total_Recebido += $row['Recebido'];
	        $Total_Pago += $row['Pago'];
			$D032_Id = $row['D014_D032_Id'];

	        if (($Contas=='1') and ($Total==0) and ($row['T018_Valor']<=0)) {
	            continue;
	        }

	        $linha = array();
            foreach ($row as $key => $value) {
            	if($key === "D032_Tipo" || $key === "D014_SubConta" || $key === "Recebido" || $key === "Pago"){
            		$linha[] = $row[$key];
            		continue;
            	}
            	if($key === "Total"){
            		$linha[] = $Total;
            		continue;
            	}
            	if($key === "Percentual"){
            		$Percentual = ($Total/$TotalGrupo)*100;
            		$linha[] = ($Percentual > 100 || $Percentual < 0) ? 0 : $Percentual;
            		continue;
            	}
            	$linha[] = '';
            }
            $geradoDados[] = $linha;
            $row_ = $row;
		}
		$linha = array();
        foreach ($row_ as $key => $value) {
        	if($key === "D032_Tipo"){
        		$linha[] = $row_[$key];
        		continue;
        	}
        	if($key === "Recebido"){
        		$linha[] = $Total_Recebido;
        		continue;
        	}
        	if($key === "Pago"){
        		$linha[] = $Total_Pago;
        		continue;
        	}
        	if($key === "Total"){
        		$linha[] = $Resultado;
        		continue;
        	}
        	if($key === "Percentual"){
        		$linha[] = 100;
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
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		$g['smarty']->assign('removerScrollLoading', true);
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'inadimplencia':
		global $g;
		//$filtroObrigatorio = true;
        $grid = array(
            array('colunaValor' => '"" as coluna1', 'colunaExibir'=>false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaExibir'=>false),
            array('colunaValor' => 'DATE_FORMAT(T007_Data_Emissao,"%m/%Y") as Mes', 'colunaTitulo' => 'Mês'),
            array('colunaValor' => 'YEAR(T007_Data_Emissao) as Ano', 'colunaTitulo' => 'Ano', 'colunaExibir' => false),
            array('colunaValor' => 'COUNT(DISTINCT T007_Id) Notas', 'colunaTitulo' => 'Notas emitidas', 'colunaTotal' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'COUNT(DISTINCT T007_D024_Id) Clientes', 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'SUM(T002_Valor_Total) as Vendido', 'colunaTitulo' => 'Vendido R$', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => "SUM(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) as Recebido", 'colunaTitulo' => 'Recebido R$', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => "(SUM(IF(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0)) / SUM(T002_Valor_Total)) *100 AS P_Recebido", 'colunaTitulo' => '% Recebido', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right', 'callback'=>'gPorcentagem'),
            array('colunaValor' => "SUM(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0)) as Receber", 'colunaTitulo' => 'Receber R$', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => "(SUM(IF(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0))) / SUM(T002_Valor_Total)*100 AS P_Receber", 'colunaTitulo' => '% Receber', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right', 'callback'=>'gPorcentagem'),
            array('colunaValor' => "SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Atrasado", 'colunaTitulo' => 'Atrasado R$', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => "(SUM(IF(T002_Data_Recebimento='0000-00-00' AND T002_Data_Vencimento<CURDATE(),T002_Valor_Saldo,0))) / SUM(T002_Valor_Total)*100 AS P_Atrasado", 'colunaTitulo' => '% Atrasado', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right', 'callback'=>'gPorcentagem'),
        );

        $from = 'T002';
        $extra = <<<EOT
        	LEFT JOIN T007 ON T007_Id=T002_T007_Id
        	LEFT JOIN D024 ON D024_Id=T002_D024_Id
        	LEFT JOIN D018 ON D018_Id=D024_D018_Id
        	LEFT JOIN D020 ON D020_Id=D024_D020_Id
        	LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
        	LEFT JOIN C004 ON C004_Id=T007_C004_Id
        	WHERE (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
        	AND T002_Flag_Cancelada != 'S'
			AND T002_C004_Id = '{$g['empresaAtual']}'
            GROUP BY DATE_FORMAT(T007_Data_Emissao,"%Y %m")
            ORDER BY T007_Data_Emissao
EOT;
        $extraTotal = <<<EOT
        	LEFT JOIN T007 ON T007_Id=T002_T007_Id
        	LEFT JOIN D024 ON D024_Id=T002_D024_Id
        	LEFT JOIN D018 ON D018_Id=D024_D018_Id
        	LEFT JOIN D020 ON D020_Id=D024_D020_Id
        	LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
        	LEFT JOIN C004 ON C004_Id=T007_C004_Id
        	WHERE (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
        	AND T002_Flag_Cancelada != 'S'
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['coluna1'] == 'Total'){
				return 'linhaNegrito';
			}
		}

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
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'C007_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D018_Uf' => array('titulo' => 'Estado', 'tipo' => 'texto'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

        $Vendido = $g['sqlAuto']->pegarSqlCampo("SUM(T002_Valor_Total)", $from, $extraTotal);
        $Recebido = $g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0))", $from, $extraTotal);
        $Receber = $g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0))", $from, $extraTotal);
        $Atrasado = $g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0))", $from, $extraTotal);


        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        $g['cacheCount'] = true;

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $geradoDados[] = array('Total', '','','','','',$Vendido, $Recebido,'0',$Receber,'0',$Atrasado,'0');

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
}

function diaSemana($dia){
	switch ($dia) {
		case '1': return 'Domingo';		  break;
		case '2': return 'Segunda-Feira'; break;
		case '3': return 'Terça-Feira';	  break;
		case '4': return 'Quarta-Feira';  break;
		case '5': return 'Quinta-Feira';  break;
		case '6': return 'Sexta-Feira';	  break;
		case '7': return 'Sábado';		  break;
	}
}
