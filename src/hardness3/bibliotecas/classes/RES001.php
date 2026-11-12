<?php
namespace hardness;
definirClasse('RES001');
class RES001_
{
    /**
     * incluirProduto
     *
     * @param Form $form
     * @param String $tipo
     * @return String/bool
     */
    public function atualizarDadosResumoR1($dataAtualizar)
    {
        /**
        * Atualização estatísticas produtos
        */

        global $data, $array, $continue, $C007IdsVendedores, $C007IdsSupervisores, $D024Ids, $D049Ids, $D006Ids;

        // Variavel global utilizada pelas funções
        $array = array();

        /**
        * R001
        */
       /* $sql = "TRUNCATE R001;";
        $result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }*/
		
		$mData = explode('-',$dataAtualizar);

		$fAno = $mData[0];
		$fMes = $mData[1];		
		$fDia = $mData[2];

		if (!checkdate($fMes, $fDia, $fAno)) { return true; }	// datas invalidas
		if (mktime(1, 59, 59, $fMes, $fDia, $fAno) > time()) { return true; } // datas maiores que ontem
		$data = "{$fAno}-{$fMes}-{$fDia}";
		//if ("{$fAno}-{$fMes}-{$fDia}" != '2011-10-25') { continue; }
		echo $data . "\n";                
	
		$sql = "SELECT R001_Id FROM R001 WHERE R001_Data = '{$data}' AND R001_C007_Id_Vendedor = '0' AND R001_C007_Id_Supervisor = '0' AND R001_D024_Id = '0' AND R001_D049_Id = '0';";
		$result = mysql_query($sql);
		if (mysql_numrows($result) > 0) {
			if (mktime(23, 59, 59, $fMes, $fDia, $fAno) < (time() - 2592000)) { 
				// datas com mais de um mês, pula
				//continue; 
			} 
			//continue;
			$sql = "DELETE FROM R001 WHERE R001_Data = '{$data}'";
			$result = mysql_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
		}

		$continue = false;

		// Vendedores
		$C007IdsVendedores = array();
		// Supervisores
		$C007IdsSupervisores = array();
		// Clientes/Fornecedores
		$D024Ids = array();
		// Produtos
		$D049Ids = array();
		// CFOPs
		$D006Ids = array();

		/*
		// Processa todos desta data (ele gera os arrays com os valores abaixo)
		processaR001(0, 0, 0, 0, 0);
		// Para cada vendedor
		foreach ($C007IdsVendedores as $C007_Id_Vendedor => $C007_Id_Vendedor_Valor) {
			if ($continue) { continue; }
		// Para cada supervisor
		foreach ($C007IdsSupervisores as $C007_Id_Supervisor => $C007_Id_Supervisor_Valor) {
				if ($continue) { continue; }
		// Para cada cliente/fornecedor
		foreach ($D024Ids as $D024_Id => $D024_Id_Valor) {
					if ($continue) { continue; }
		// Para cada produto
		foreach ($D049Ids as $D049_Id => $D049_Id_Valor) {
						if ($continue) { continue; }
		// Para cada CFOP
		foreach ($D006Ids as $D006_Id => $D006_Id_Valor) {
							if ($continue) { continue; }
							
							//processaR001($C007_Id_Vendedor, $C007_Id_Supervisor, $D024_Id, $D049_Id, $D006_Id);
							echo "$C007_Id_Vendedor, $C007_Id_Supervisor, $D024_Id, $D049_Id, $D006_Id\n";
						
		} // CFOP
		} // Produto
		} // Cliente/Fornecedor
		} // Supervisor
		} // Vendedor
		die();
		*/

		// Geral
		$retorno = processaR001(0, 0, 0, 0, 0);
		// Para cada vendedor
		foreach ($C007IdsVendedores as $C007_Id_Vendedor => $value) {
			if ($continue) { return true; }
			processaR001($C007_Id_Vendedor, 0, 0, 0, 0);
		}
		// Para cada supervisor
		foreach ($C007IdsSupervisores as $C007_Id_Supervisor => $value) {
			if ($continue) { return true; }
			processaR001(0, $C007_Id_Supervisor, 0, 0, 0);
		}
		// Para cada cliente/fornecedor
		foreach ($D024Ids as $D024_Id => $value) {
			if ($continue) { return true; }
			processaR001(0, 0, $D024_Id, 0, 0);
		}
		// Para cada produto
		foreach ($D049Ids as $D049_Id => $value) {
			if ($continue) { return true; }
			processaR001(0, 0, 0, $D049_Id, 0);
		}
		// Para cada CFOP
		foreach ($D006Ids as $D006_Id => $value) {
			if ($continue) { return true; }
			processaR001(0, 0, 0, 0, $D006_Id);
		}

		return $retorno;

    }
}

/**
* Processa a SQL e insere no array de insert
* 
* @param mixed $sql
*/
function processaSQL1($sql) {
	global $array;
	global $C007IdsVendedores, $C007IdsSupervisores, $D024Ids, $D049Ids, $D006Ids;
	
	// sql
	$result = mysql_query($sql);
	if (!$result) {
		die('Invalid query: ' . mysql_error() . "\n{$sql}");
	}
	if (mysql_numrows($result) == 0) { return false; }
	$row = mysql_fetch_array($result);
	
	// args
	$argsNum = func_num_args();
	$args = func_get_args();
	for ($i = 1; $i < $argsNum; $i++) {
		$array[$args[$i]] = $row[$i-1];
    }
    
    // Verifica todos os vendedores, empresas, produtos, etc que fazem parte desta SQL, para serem processados depois (melhoria de performance significativa)
    $sql = explode('Vendedor.C007_Id', $sql, 2);
    $sql = 'SELECT Vendedor.C007_Id' . $sql[1];
    $res = mysql_query($sql);
    while ($row = mysql_fetch_assoc($res)) {
	    if (!empty($row['Vendedor_C007_Id']) && !isset($C007IdsVendedores[$row['Vendedor_C007_Id']])) { $C007IdsVendedores[$row['Vendedor_C007_Id']] = true; }
	    if (!empty($row['Supervisor_C007_Id']) && !isset($C007IdsSupervisores[$row['Supervisor_C007_Id']])) { $C007IdsSupervisores[$row['Supervisor_C007_Id']] = true; }
	    //if (isset($row['Empresa.D024_Id']) && !empty($row['Empresa.D024_Id']) && !isset($C007IdsVendedores[$row['Empresa.D024_Id']])) { $C007IdsVendedores[$row['Empresa.D024_Id']] = true; }
	    if (isset($row['D024_Id']) && !empty($row['D024_Id']) && !isset($D024Ids[$row['D024_Id']])) { $D024Ids[$row['D024_Id']] = true; }
	    if (!empty($row['D049_Id']) && !isset($D049Ids[$row['D049_Id']])) { $D049Ids[$row['D049_Id']] = true; }
	    if (!empty($row['D006_Id']) && !isset($D006Ids[$row['D006_Id']])) { $D006Ids[$row['D006_Id']] = true; }
	}
}

function processaR001($C007_Id_Vendedor, $C007_Id_Supervisor, $D024_Id, $D049_Id, $D006_Id) {
	global $array, $continue, $data;
    global $C007IdsVendedores, $C007IdsSupervisores, $D024Ids, $D049Ids, $D006Ids;
    

	
	/*
	Não é mais utilizado, o controle é feito nos foreach
	// Previnir ligações de filtros (por performance)
	if ($C007_Id_Vendedor != 0 && ($C007_Id_Supervisor != 0 || $D024_Id != 0 || $D049_Id != 0 || $D006_Id != 0)) { continue; }
	if ($C007_Id_Supervisor != 0 && ($C007_Id_Vendedor != 0 || $D024_Id != 0 || $D049_Id != 0 || $D006_Id != 0)) { continue; }
	if ($D024_Id != 0 && ($C007_Id_Vendedor != 0 || $C007_Id_Supervisor != 0 || $D049_Id != 0 || $D006_Id != 0)) { continue; }
	if ($D049_Id != 0 && ($D024_Id != 0 || $C007_Id_Vendedor != 0 || $C007_Id_Supervisor != 0 || $D006_Id != 0)) { continue; }
	if ($D006_Id != 0 && ($D024_Id != 0 || $C007_Id_Vendedor != 0 || $C007_Id_Supervisor != 0 || $D049_Id != 0)) { continue; }
	//echo "{$C007_Id_Vendedor} - {$C007_Id_Supervisor} - {$D024_Id} - {$D049}\n";
	*/
				
	$Filtro_Vendedor = '';
	$Filtro_Supervisor = '';
	$Filtro_Empresa = '';
	$Filtro_Empresa2 = '';
	$Filtro_Produto = '';
	$Filtro_CFOP = '';
	if ($C007_Id_Vendedor > 0)
	{
		$Filtro_Vendedor = " and Vendedor.C007_Id = '{$C007_Id_Vendedor}' ";
	}
	if ($C007_Id_Supervisor > 0)
	{
		$Filtro_Supervisor = " and Supervisor.C007_Id = '{$C007_Id_Supervisor}' ";
	}
	if ($D024_Id > 0)
	{
		$Filtro_Empresa = " and Empresa.D024_Id = '{$D024_Id}' ";
		$Filtro_Empresa2 = " and D024_Id = '{$D024_Id}' ";
	}
	if ($D049_Id > 0)
	{
		$Filtro_Produto = " and D049_Id = '{$D049_Id}' ";
	}
	if ($D006_Id > 0)
	{
		$Filtro_CFOP = " and D006_Id = '{$D006_Id}' ";
	}
	
	
	// Array utilizado no INSERT
	$array = array(
		'R001_Data' => $data,
		'R001_C007_Id_Vendedor' => $C007_Id_Vendedor,
		'R001_C007_Id_Supervisor' => $C007_Id_Supervisor,
		'R001_D024_Id' => $D024_Id,
		'R001_D049_Id' => $D049_Id,
		'R001_D006_Id' => $D006_Id,
		'R001_Clientes_Nr' => 0,
		'R001_Clientes_Contatos' => 0,
		'R001_Clientes_Novos' => 0,
		'R001_Clientes_Ativos' => 0,
		'R001_Clientes_Inativos' => 0,
		'R001_Orcamentos_Nr' => 0,
		'R001_Orcamentos_Clientes' => 0,
		'R001_Orcamentos_Itens' => 0,
		'R001_Orcamentos_Itens_Diferentes' => 0,
		'R001_Orcamentos_Valor_Total' => 0.00,
		'R001_Orcamentos_Valor_Total_Produtos' => 0.00,
		'R001_Pedidos_Nr' => 0,
		'R001_Pedidos_Clientes' => 0,
		'R001_Pedidos_Itens' => 0,
		'R001_Pedidos_Itens_Diferentes' => 0,
		'R001_Pedidos_Valor_Total' => 0.00,
		'R001_Pedidos_Valor_Total_Produtos' => 0.00,
		'R001_Faturados_Nr' => 0,
		'R001_Faturados_Clientes' => 0,
		'R001_Faturados_Itens' => 0,
		'R001_Faturados_Itens_Diferentes' => 0,
		'R001_Faturados_Valor_Total' => 0.00,
		'R001_Faturados_Valor_Total_Produtos' => 0.00,
		'R001_Faturados_IPV' => 0.00,
		'R001_Faturados_IPV_Real' => 0.00,
		'R001_Faturados_Tributos_ICMS' => 0.00,
		'R001_Faturados_Tributos_ICMS_ST' => 0.00,
		'R001_Faturados_Tributos_IPI' => 0.00,
		'R001_Faturados_Tributos_PIS' => 0.00,
		'R001_Faturados_Tributos_COFINS' => 0.00,
		'R001_Devolucoes_Nr' => 0,
		'R001_Devolucoes_Clientes' => 0,
		'R001_Devolucoes_Valor_Total' => 0.00,
		'R001_Devolucoes_Valor_Total_Produtos' => 0.00,
		'R001_Devolucoes_Tributos_ICMS' => 0.00,
		'R001_Devolucoes_Tributos_ICMS_ST' => 0.00,
		'R001_Devolucoes_Tributos_IPI' => 0.00,
		'R001_Devolucoes_Tributos_PIS' => 0.00,
		'R001_Devolucoes_Tributos_COFINS' => 0.00,
		'R001_Compras_Nr' => 0,
		'R001_Compras_Clientes' => 0,
		'R001_Compras_Valor_Total' => 0.00,
		'R001_Compras_Valor_Total_Produtos' => 0.00,
		'R001_Compras_Tributos_ICMS' => 0.00,
		'R001_Compras_Tributos_ICMS_ST' => 0.00,
		'R001_Compras_Tributos_IPI' => 0.00,
		'R001_Compras_Tributos_PIS' => 0.00,
		'R001_Compras_Tributos_COFINS' => 0.00,
		'R001_Entrada_Nr' => 0,
		'R001_Entrada_Clientes' => 0,
		'R001_Entrada_Itens' => 0,
		'R001_Entrada_Itens_Diferentes' => 0,
		'R001_Entrada_Valor_Total' => 0.00,
		'R001_Entrada_Valor_Total_Produtos' => 0.00,
		'R001_Entrada_Tributos_ICMS' => 0.00,
		'R001_Entrada_Tributos_ICMS_ST' => 0.00,
		'R001_Entrada_Tributos_IPI' => 0.00,
		'R001_Entrada_Tributos_PIS' => 0.00,
		'R001_Entrada_Tributos_COFINS' => 0.00,
		'R001_Saida_Nr' => 0,
		'R001_Saida_Clientes' => 0,
		'R001_Saida_Itens' => 0,
		'R001_Saida_Itens_Diferentes' => 0,
		'R001_Saida_Valor_Total' => 0.00,
		'R001_Saida_Valor_Total_Produtos' => 0.00,
		'R001_Saida_Tributos_ICMS' => 0.00,
		'R001_Saida_Tributos_ICMS_ST' => 0.00,
		'R001_Saida_Tributos_IPI' => 0.00,
		'R001_Saida_Tributos_PIS' => 0.00,
		'R001_Saida_Tributos_COFINS' => 0.00
	);
	
	// D085 - Contatos
	$sql = <<<EOT
		select 
			count(distinct D085_D024_Id) as R001_Clientes_Nr,
            count(distinct D085_Id) as R001_Clientes_Contatos,
            Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            0 as D049_Id,
            0 as D006_Id
	    from D085
            left join C007 as Vendedor on Vendedor.C007_Id=D085_C007_Id
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=D085_D024_Id
	    where 
            D085_Data = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
			{$Filtro_Empresa}
			and D085_Flag_Manual = 'S'
			

EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL1($sql, 'R001_Clientes_Nr', 'R001_Clientes_Contatos');
	}
	
	// Clientes Novos
	$sql = <<<EOT
		select 
			count(*) as R001_Clientes_Novos,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            D024_Id,
            0 as D049_Id,
            0 as D006_Id
        from D024 
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where
            D024_Data_Cadastro = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa2}
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL1($sql, 'R001_Clientes_Novos');
	}
	
	// Clientes Ativos
	$sql = <<<EOT
		select 
			count(*) as R001_Clientes_Ativos,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            D024_Id,
            0 as D049_Id,
            0 as D006_Id
        from D024 
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where
            D024_Data_Ativo = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa2}
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL1($sql, 'R001_Clientes_Ativos');
	}
	
	// Clientes Inativos
	$sql = <<<EOT
		select 
			count(*) as R001_Clientes_Inativos,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            D024_Id,
            0 as D049_Id,
            0 as D006_Id 
        from D024 
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where
            D024_Data_Inativo = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa2}
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL1($sql, 'R001_Clientes_Inativos');
	}
	
	// Orçamentos Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select
				count(distinct T003_Id) as R001_Orcamentos_Nr,
				count(distinct T003_D024_Id) as R001_Orcamentos_Clientes,
				sum(T003_Valor_Total) as R001_Orcamentos_Valor_Total,
				sum(T003_Valor_Total_Produtos) as R001_Orcamentos_Valor_Total_Produtos,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T003
				left join D006 on D006_Id=T003_D006_Id
				left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T003_D024_Id
		    where
                T003_Data_Emissao = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T003_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Orcamentos_Nr', 'R001_Orcamentos_Clientes', 'R001_Orcamentos_Valor_Total', 'R001_Orcamentos_Valor_Total_Produtos');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select
				count(distinct T003_Id) as R001_Orcamentos_Nr,
				count(distinct T003_D024_Id) as R001_Orcamentos_Clientes,
				sum(T004_Valor_Total_Preco) as R001_Orcamentos_Valor_Total,
				sum(T004_Valor_Total_Preco) as R001_Orcamentos_Valor_Total_Produtos,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T003
				left join D006 on D006_Id=T003_D006_Id
				left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T003_D024_Id
		        LEFT JOIN T004 ON T004_T003_Id=T003_Id
	            LEFT JOIN D009 ON D009_Id=T004_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where
                T003_Data_Emissao = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T003_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Orcamentos_Nr', 'R001_Orcamentos_Clientes', 'R001_Orcamentos_Valor_Total', 'R001_Orcamentos_Valor_Total_Produtos');
	}
	
	// Orçamentos Itens
	if ($D049_Id == 0) {
		$R001_Orcamentos_Itens = 'count(T004_Id) as R001_Orcamentos_Itens,';
	} else {
		$R001_Orcamentos_Itens = 'sum(T004_Quantidade) as R001_Orcamentos_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Orcamentos_Itens}
			count(distinct T004_D009_Id) as R001_Orcamentos_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
		from T003
            left join T004 on T004_T003_Id=T003_Id     
	        left join D006 on D006_Id=T003_D006_Id
	        left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=T003_D024_Id
	        LEFT JOIN D009 ON D009_Id=T004_D009_Id
	        LEFT JOIN D049 ON D049_Id=D009_D049_Id
	    where 
            T003_Data_Emissao = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T003_C004_Id='$Empresa_Atual'
	processaSQL1($sql, 'R001_Orcamentos_Itens', 'R001_Orcamentos_Itens_Diferentes');
	
	// Pedidos Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T005_Id) as R001_Pedidos_Nr,
				count(distinct T005_D024_Id) as R001_Pedidos_Clientes,
				sum(T005_Valor_Total) as R001_Pedidos_Valor_Total,
				sum(T005_Valor_Total_Produtos) as R001_Pedidos_Valor_Total_Produtos,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
				0 as D049_Id,
				0 as D006_Id
		    from T005
                left join D006 on D006_Id=T005_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T005_D024_Id
		    where 
                T005_Data_Emissao = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
				and D006_Flag_Venda_Compra_Outros='V'
				and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
EOT;
		// and T005_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Pedidos_Nr', 'R001_Pedidos_Clientes', 'R001_Pedidos_Valor_Total', 'R001_Pedidos_Valor_Total_Produtos');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T005_Id) as R001_Pedidos_Nr,
				count(distinct T005_D024_Id) as R001_Pedidos_Clientes,
				sum(T006_Valor_Total_Preco) as R001_Pedidos_Valor_Total,
				sum(T006_Valor_Total_Preco) as R001_Pedidos_Valor_Total_Produtos,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T005
                left join D006 on D006_Id=T005_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T005_D024_Id
		        LEFT JOIN T006 ON T006_T005_Id=T005_Id
	            LEFT JOIN D009 ON D009_Id=T006_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where 
                T005_Data_Emissao = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
				and D006_Flag_Venda_Compra_Outros='V'
				and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
EOT;
		// and T005_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Pedidos_Nr', 'R001_Pedidos_Clientes', 'R001_Pedidos_Valor_Total', 'R001_Pedidos_Valor_Total_Produtos');
	}
	
	// Pedidos Itens
	if ($D049_Id == 0) {
		$R001_Pedidos_Itens = 'count(T006_Id) as R001_Pedidos_Itens,';
	} else {
		$R001_Pedidos_Itens = 'sum(T006_Quantidade) as R001_Pedidos_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Pedidos_Itens}
			count(distinct T006_D009_Id) as R001_Pedidos_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
		from T005
            left join T006 on T006_T005_Id=T005_Id     
	        left join D006 on D006_Id=T005_D006_Id
	        left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=T005_D024_Id
	        LEFT JOIN D009 ON D009_Id=T006_D009_Id
	        LEFT JOIN D049 ON D049_Id=D009_D049_Id
	    where 
            T005_Data_Emissao = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
			and D006_Flag_Venda_Compra_Outros='V'
			and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
EOT;
	// and T003_C004_Id='$Empresa_Atual'
	processaSQL1($sql, 'R001_Pedidos_Itens', 'R001_Pedidos_Itens_Diferentes');
	
	// NF Faturados Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R001_Faturados_Nr,
				count(distinct T007_D024_Id) as R001_Faturados_Clientes,
				sum(T007_Valor_Total) as R001_Faturados_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R001_Faturados_Valor_Total_Produtos,
				SUM(T007_Valor_Total_ICMS) as R001_Faturados_Tributos_ICMS,
				SUM(T007_Valor_ICMS_Substituicao_Retencao) as R001_Faturados_Tributos_ICMS_ST,
				SUM(T007_Valor_Total_IPI) as R001_Faturados_Tributos_IPI,
				SUM(T007_Valor_Total_PIS) as R001_Faturados_Tributos_PIS,
				SUM(T007_Valor_Total_COFINS) as R001_Faturados_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
			where 
				T007_Data_Emissao = '{$data}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
				and D006_Flag_Venda_Compra_Outros='V'

EOT;
		// and T007_Flag_Entrada_Saida = 'S'
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Faturados_Nr', 'R001_Faturados_Clientes', 'R001_Faturados_Valor_Total', 'R001_Faturados_Valor_Total_Produtos', 'R001_Faturados_Tributos_ICMS', 'R001_Faturados_Tributos_ICMS_ST', 'R001_Faturados_Tributos_IPI', 'R001_Faturados_Tributos_PIS', 'R001_Faturados_Tributos_COFINS');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R001_Faturados_Nr,
				count(distinct T007_D024_Id) as R001_Faturados_Clientes,
				sum(T008_Valor_Total_Preco) as R001_Faturados_Valor_Total,
				sum(T008_Valor_Total_Preco) as R001_Faturados_Valor_Total_Produtos,
				SUM(T008_Valor_ICMS) as R001_Faturados_Tributos_ICMS,
				SUM(T008_Valor_ICMS_Substituicao) as R001_Faturados_Tributos_ICMS_ST,
				SUM(T008_Valor_IPI) as R001_Faturados_Tributos_IPI,
				SUM(T008_Valor_PIS) as R001_Faturados_Tributos_PIS,
				SUM(T008_Valor_COFINS) as R001_Faturados_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
		        LEFT JOIN T008 ON T008_T007_Id=T007_Id
	            LEFT JOIN D009 ON D009_Id=T008_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
			where 
				T007_Data_Emissao = '{$data}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T007_Flag_Entrada_Saida = 'S'
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Faturados_Nr', 'R001_Faturados_Clientes', 'R001_Faturados_Valor_Total', 'R001_Faturados_Valor_Total_Produtos', 'R001_Faturados_Tributos_ICMS', 'R001_Faturados_Tributos_ICMS_ST', 'R001_Faturados_Tributos_IPI', 'R001_Faturados_Tributos_PIS', 'R001_Faturados_Tributos_COFINS');
	}
	
	// NF Faturados Itens
	if ($D049_Id == 0) {
		$R001_Faturados_Itens = 'count(T008_Id) as R001_Faturados_Itens,';
	} else {
		$R001_Faturados_Itens = 'sum(T008_Quantidade) as R001_Faturados_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Faturados_Itens}
			count(distinct T008_D009_Id) as R001_Faturados_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
	    from T007
            left join T008 on T008_T007_Id=T007_Id
            left join D006 on D006_Id=T007_D006_Id
            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
	        LEFT JOIN D009 ON D009_Id=T008_D009_Id
	        LEFT JOIN D049 ON D049_Id=D009_D049_Id
		where 
			T007_Data_Emissao = '{$data}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
	        and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T007_Flag_Entrada_Saida = 'S'
	// and T007_C004_Id='$Empresa_Atual'
	processaSQL1($sql, 'R001_Faturados_Itens', 'R001_Faturados_Itens_Diferentes');
	
	// NF IPV
	$sql = <<<EOT
		select 
			sum(T008_Valor_Custo_Unitario*T008_Quantidade) as Custo,
	        sum(T008_Valor_Custo_Compra_Unitario*T008_Quantidade) as Custo_Real,
	        sum(T008_Valor_Preco_Unitario*T008_Quantidade) as Preco,
	        Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
	    from T007
            left join D006 on D006_Id=T007_D006_Id
	        left join T008 on T008_T007_Id=T007_Id
	        left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
	        LEFT JOIN D009 ON D009_Id=T008_D009_Id
	        LEFT JOIN D049 ON D049_Id=D009_D049_Id
	    where 
            T007_Data_Emissao = '{$data}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
			and T008_Valor_Custo_Unitario>0
			and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T007_Flag_Entrada_Saida = 'S'
	// and T007_C004_Id='$Empresa_Atual'
	$result = mysql_query($sql);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	if (mysql_numrows($result) > 0) {
		$mT008 = mysql_fetch_assoc($result);
		$array['R001_Faturados_IPV'] = ($mT008['Custo'] == 0) ? 0 : $mT008['Preco']/$mT008['Custo'];
		$array['R001_Faturados_IPV_Real'] = ($mT008['Custo_Real'] == 0) ? 0 : $mT008['Preco']/$mT008['Custo_Real'];
	}
	
	// Devoluções
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				sum(T007_Valor_Total) as R001_Devolucoes_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R001_Devolucoes_Valor_Total_Produtos,
		        count(distinct T007_D024_Id) as R001_Devolucoes_Clientes,
		        count(distinct T007_Numero_Nota_Fiscal) as R001_Devolucoes_Nr,
		        Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
		    where 
                T007_Data_Emissao = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='DV'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		$mT007D = mysql_fetch_assoc($result);
		$sql = <<<EOT
			select 
				sum(T013_Valor_Total) as R001_Devolucoes_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R001_Devolucoes_Valor_Total_Produtos,
		        count(distinct T013_D024_Id) as R001_Devolucoes_Clientes,
		        count(distinct T013_Numero_Nota_Fiscal) as R001_Devolucoes_Nr,
		        SUM(T013_Valor_Total_ICMS) as R001_Devolucoes_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R001_Devolucoes_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R001_Devolucoes_Tributos_IPI,
				0 as R001_Devolucoes_Tributos_PIS,
				0 as R001_Devolucoes_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T013
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
		    where 
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T013_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='DV'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		$mT013D = mysql_fetch_assoc($result);
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				sum(T008_Valor_Total_Preco) as R001_Devolucoes_Valor_Total,
				sum(T008_Valor_Total_Preco) as R001_Devolucoes_Valor_Total_Produtos,
		        count(distinct T007_D024_Id) as R001_Devolucoes_Clientes,
		        count(distinct T007_Numero_Nota_Fiscal) as R001_Devolucoes_Nr,
		        Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
		        LEFT JOIN T008 ON T008_T007_Id=T007_Id
	            LEFT JOIN D009 ON D009_Id=T008_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where 
                T007_Data_Emissao = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='DV'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		$mT007D = mysql_fetch_assoc($result);
		$sql = <<<EOT
			select 
				sum(T014_Valor_Total_Custo) as R001_Devolucoes_Valor_Total,
				sum(T014_Valor_Total_Custo) as R001_Devolucoes_Valor_Total_Produtos,
		        count(distinct T013_D024_Id) as R001_Devolucoes_Clientes,
		        count(distinct T013_Numero_Nota_Fiscal) as R001_Devolucoes_Nr,
		        SUM(T014_Valor_ICMS) as R001_Devolucoes_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R001_Devolucoes_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R001_Devolucoes_Tributos_IPI,
				0 as R001_Devolucoes_Tributos_PIS,
				0 as R001_Devolucoes_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T013
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
		        LEFT JOIN T014 ON T014_T013_Id=T013_Id
	            LEFT JOIN D009 ON D009_Id=T014_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where 
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T013_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='DV'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		$mT013D = mysql_fetch_assoc($result);
	}
	$array['R001_Devolucoes_Nr'] = $mT007D['R001_Devolucoes_Nr'] + $mT013D['R001_Devolucoes_Nr'];
	$array['R001_Devolucoes_Clientes'] = $mT007D['R001_Devolucoes_Clientes'] + $mT013D['R001_Devolucoes_Clientes'];
	$array['R001_Devolucoes_Valor_Total'] = $mT007D['R001_Devolucoes_Valor_Total'] + $mT013D['R001_Devolucoes_Valor_Total'];
	$array['R001_Devolucoes_Valor_Total_Produtos'] = $mT007D['R001_Devolucoes_Valor_Total_Produtos'] + $mT013D['R001_Devolucoes_Valor_Total_Produtos'];
	$array['R001_Devolucoes_Tributos_ICMS'] = $mT013D['R001_Devolucoes_Tributos_ICMS'];
	$array['R001_Devolucoes_Tributos_ICMS_ST'] = $mT013D['R001_Devolucoes_Tributos_ICMS_ST'];
	$array['R001_Devolucoes_Tributos_IPI'] = $mT013D['R001_Devolucoes_Tributos_IPI'];
	$array['R001_Devolucoes_Tributos_PIS'] = $mT013D['R001_Devolucoes_Tributos_PIS'];
	$array['R001_Devolucoes_Tributos_COFINS'] = $mT013D['R001_Devolucoes_Tributos_COFINS'];
	
	// Compras
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R001_Compras_Nr,
				count(distinct T013_D024_Id) as R001_Compras_Clientes,
				sum(T013_Valor_Total) as R001_Compras_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R001_Compras_Valor_Total_Produtos,
				SUM(T013_Valor_Total_ICMS) as R001_Compras_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R001_Compras_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R001_Compras_Tributos_IPI,
				0 as R001_Compras_Tributos_PIS,
				0 as R001_Compras_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T013
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
		    where 
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Venda_Compra_Outros='C'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Compras_Nr', 'R001_Compras_Clientes', 'R001_Compras_Valor_Total', 'R001_Compras_Valor_Total_Produtos', 'R001_Compras_Tributos_ICMS', 'R001_Compras_Tributos_ICMS_ST', 'R001_Compras_Tributos_IPI', 'R001_Compras_Tributos_PIS', 'R001_Compras_Tributos_COFINS');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R001_Compras_Nr,
				count(distinct T013_D024_Id) as R001_Compras_Clientes,
				sum(T014_Valor_Total_Custo) as R001_Compras_Valor_Total,
				sum(T014_Valor_Total_Custo) as R001_Compras_Valor_Total_Produtos,
				SUM(T014_Valor_ICMS) as R001_Compras_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R001_Compras_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R001_Compras_Tributos_IPI,
				0 as R001_Compras_Tributos_PIS,
				0 as R001_Compras_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T013
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
		        LEFT JOIN T014 ON T014_T013_Id=T013_Id
	            LEFT JOIN D009 ON D009_Id=T014_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Venda_Compra_Outros='C'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Compras_Nr', 'R001_Compras_Clientes', 'R001_Compras_Valor_Total', 'R001_Compras_Valor_Total_Produtos', 'R001_Compras_Tributos_ICMS', 'R001_Compras_Tributos_ICMS_ST', 'R001_Compras_Tributos_IPI', 'R001_Compras_Tributos_PIS', 'R001_Compras_Tributos_COFINS');
	}
	
	// NF Saida Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R001_Saida_Nr,
				count(distinct T007_D024_Id) as R001_Saida_Clientes,
				sum(T007_Valor_Total) as R001_Saida_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R001_Saida_Valor_Total_Produtos,
				SUM(T007_Valor_Total_ICMS) as R001_Saida_Tributos_ICMS,
				SUM(T007_Valor_ICMS_Substituicao_Retencao) as R001_Saida_Tributos_ICMS_ST,
				SUM(T007_Valor_Total_IPI) as R001_Saida_Tributos_IPI,
				SUM(T007_Valor_Total_PIS) as R001_Saida_Tributos_PIS,
				SUM(T007_Valor_Total_COFINS) as R001_Saida_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
			where 
				T007_Data_Emissao = '{$data}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Entrada_Saida='S'
EOT;
		// and T007_Flag_Entrada_Saida = 'S'
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Saida_Nr', 'R001_Saida_Clientes', 'R001_Saida_Valor_Total', 'R001_Saida_Valor_Total_Produtos', 'R001_Saida_Tributos_ICMS', 'R001_Saida_Tributos_ICMS_ST', 'R001_Saida_Tributos_IPI', 'R001_Saida_Tributos_PIS', 'R001_Saida_Tributos_COFINS');
		// Podemos ter NF de Saída na T013
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R001_Entrada_Nr,
				count(distinct T013_D024_Id) as R001_Entrada_Clientes,
				sum(T013_Valor_Total) as R001_Entrada_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R001_Entrada_Valor_Total_Produtos,
				SUM(T013_Valor_Total_ICMS) as R001_Entrada_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R001_Entrada_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R001_Entrada_Tributos_IPI,
				0 as R001_Entrada_Tributos_PIS,
				0 as R001_Entrada_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T013
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
		    where 
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Entrada_Saida='S'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$array['R001_Saida_Nr'] += $row['R001_Entrada_Nr'];
		$array['R001_Saida_Clientes'] += $row['R001_Entrada_Clientes'];
		$array['R001_Saida_Valor_Total'] += $row['R001_Entrada_Valor_Total'];
		$array['R001_Saida_Valor_Total_Produtos'] += $row['R001_Entrada_Valor_Total_Produtos'];
		$array['R001_Saida_Tributos_ICMS'] += $row['R001_Entrada_Tributos_ICMS'];
		$array['R001_Saida_Tributos_ICMS_ST'] += $row['R001_Entrada_Tributos_ICMS_ST'];
		$array['R001_Saida_Tributos_IPI'] += $row['R001_Entrada_Tributos_IPI'];
		$array['R001_Saida_Tributos_PIS'] += $row['R001_Entrada_Tributos_PIS'];
		$array['R001_Saida_Tributos_COFINS'] += $row['R001_Entrada_Tributos_COFINS'];
	} else {
		// Para Produtos
		$sql = <<<EOT
			select
				count(distinct T007_Numero_Nota_Fiscal) as R001_Saida_Nr,
				count(distinct T007_D024_Id) as R001_Saida_Clientes,
				sum(T008_Valor_Total_Preco) as R001_Saida_Valor_Total,
				sum(T008_Valor_Total_Preco) as R001_Saida_Valor_Total_Produtos,
				SUM(T008_Valor_ICMS) as R001_Saida_Tributos_ICMS,
				SUM(T008_Valor_ICMS_Substituicao) as R001_Saida_Tributos_ICMS_ST,
				SUM(T008_Valor_IPI) as R001_Saida_Tributos_IPI,
				SUM(T008_Valor_PIS) as R001_Saida_Tributos_PIS,
				SUM(T008_Valor_COFINS) as R001_Saida_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
		        LEFT JOIN T008 ON T008_T007_Id=T007_Id
	            LEFT JOIN D009 ON D009_Id=T008_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
			where 
				T007_Data_Emissao = '{$data}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Entrada_Saida='S'
EOT;
		// and T007_Flag_Entrada_Saida = 'S'
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Saida_Nr', 'R001_Saida_Clientes', 'R001_Saida_Valor_Total', 'R001_Saida_Valor_Total_Produtos', 'R001_Saida_Tributos_ICMS', 'R001_Saida_Tributos_ICMS_ST', 'R001_Saida_Tributos_IPI', 'R001_Saida_Tributos_PIS', 'R001_Saida_Tributos_COFINS');
		// Podemos ter Saida na T013
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R001_Entrada_Nr,
				count(distinct T013_D024_Id) as R001_Entrada_Clientes,
				sum(T014_Valor_Total_Custo) as R001_Entrada_Valor_Total,
				sum(T014_Valor_Total_Custo) as R001_Entrada_Valor_Total_Produtos,
				SUM(T014_Valor_ICMS) as R001_Entrada_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R001_Entrada_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R001_Entrada_Tributos_IPI,
				0 as R001_Entrada_Tributos_PIS,
				0 as R001_Entrada_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T013
		    	LEFT JOIN T014 ON T014_T013_Id=T013_Id
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
	            LEFT JOIN D009 ON D009_Id=T014_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Entrada_Saida='S'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$array['R001_Saida_Nr'] += $row['R001_Entrada_Nr'];
		$array['R001_Saida_Clientes'] += $row['R001_Entrada_Clientes'];
		$array['R001_Saida_Valor_Total'] += $row['R001_Entrada_Valor_Total'];
		$array['R001_Saida_Valor_Total_Produtos'] += $row['R001_Entrada_Valor_Total_Produtos'];
		$array['R001_Saida_Tributos_ICMS'] += $row['R001_Entrada_Tributos_ICMS'];
		$array['R001_Saida_Tributos_ICMS_ST'] += $row['R001_Entrada_Tributos_ICMS_ST'];
		$array['R001_Saida_Tributos_IPI'] += $row['R001_Entrada_Tributos_IPI'];
		$array['R001_Saida_Tributos_PIS'] += $row['R001_Entrada_Tributos_PIS'];
		$array['R001_Saida_Tributos_COFINS'] += $row['R001_Entrada_Tributos_COFINS'];
	}
	
	// NF Saida Itens (T007)
	if ($D049_Id == 0) {
		$R001_Saida_Itens = 'count(T008_Id) as R001_Saida_Itens,';
	} else {
		$R001_Saida_Itens = 'sum(T008_Quantidade) as R001_Saida_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Saida_Itens}
			count(distinct T008_D009_Id) as R001_Saida_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
	    from T007
            left join T008 on T008_T007_Id=T007_Id
            left join D006 on D006_Id=T007_D006_Id
            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
	        LEFT JOIN D009 ON D009_Id=T008_D009_Id
	        LEFT JOIN D049 ON D049_Id=D009_D049_Id
		where 
			T007_Data_Emissao = '{$data}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
	        and D006_Flag_Entrada_Saida='S'
EOT;
	// and T007_Flag_Entrada_Saida = 'S'
	// and T007_C004_Id='$Empresa_Atual'
	processaSQL1($sql, 'R001_Saida_Itens', 'R001_Saida_Itens_Diferentes');
	// NF Saida Itens (T013)
	if ($D049_Id == 0) {
		$R001_Entrada_Itens = 'count(T014_Id) as R001_Entrada_Itens,';
	} else {
		$R001_Entrada_Itens = 'sum(T014_Quantidade) as R001_Entrada_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Entrada_Itens}
			count(distinct T014_D009_Id) as R001_Entrada_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
	    from T013
	    	LEFT JOIN T014 ON T014_T013_Id=T013_Id
           	left join D006 on D006_Id=T013_D006_Id
           	left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		   	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		   	left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
	       	LEFT JOIN D009 ON D009_Id=T014_D009_Id
	       	LEFT JOIN D049 ON D049_Id=D009_D049_Id
		where 
			T013_Data_Entrada = '{$data}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T013_Flag_Cancelada!='S'
	        and D006_Flag_Entrada_Saida='S'
EOT;
	// and T007_C004_Id='$Empresa_Atual'
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$array['R001_Saida_Itens'] += $row['R001_Entrada_Itens'];
	$array['R001_Saida_Itens_Diferentes'] += $row['R001_Entrada_Itens_Diferentes'];
	
	// NF Entrada Totais
	// Compras
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R001_Entrada_Nr,
				count(distinct T013_D024_Id) as R001_Entrada_Clientes,
				sum(T013_Valor_Total) as R001_Entrada_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R001_Entrada_Valor_Total_Produtos,
				SUM(T013_Valor_Total_ICMS) as R001_Entrada_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R001_Entrada_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R001_Entrada_Tributos_IPI,
				0 as R001_Entrada_Tributos_PIS,
				0 as R001_Entrada_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T013
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
		    where 
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Entrada_Saida='E'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Entrada_Nr', 'R001_Entrada_Clientes', 'R001_Entrada_Valor_Total', 'R001_Entrada_Valor_Total_Produtos', 'R001_Entrada_Tributos_ICMS', 'R001_Entrada_Tributos_ICMS_ST', 'R001_Entrada_Tributos_IPI', 'R001_Entrada_Tributos_PIS', 'R001_Entrada_Tributos_COFINS');
		// Podemos ter Entrada na T007
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R001_Saida_Nr,
				count(distinct T007_D024_Id) as R001_Saida_Clientes,
				sum(T007_Valor_Total) as R001_Saida_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R001_Saida_Valor_Total_Produtos,
				SUM(T007_Valor_Total_ICMS) as R001_Saida_Tributos_ICMS,
				SUM(T007_Valor_ICMS_Substituicao_Retencao) as R001_Saida_Tributos_ICMS_ST,
				SUM(T007_Valor_Total_IPI) as R001_Saida_Tributos_IPI,
				SUM(T007_Valor_Total_PIS) as R001_Saida_Tributos_PIS,
				SUM(T007_Valor_Total_COFINS) as R001_Saida_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	0 as D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
			where 
				T007_Data_Emissao = '{$data}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Entrada_Saida='E'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$array['R001_Entrada_Nr'] += $row['R001_Saida_Nr'];
		$array['R001_Entrada_Clientes'] += $row['R001_Saida_Clientes'];
		$array['R001_Entrada_Valor_Total'] += $row['R001_Saida_Valor_Total'];
		$array['R001_Entrada_Valor_Total_Produtos'] += $row['R001_Saida_Valor_Total_Produtos'];
		$array['R001_Entrada_Tributos_ICMS'] += $row['R001_Saida_Tributos_ICMS'];
		$array['R001_Entrada_Tributos_ICMS_ST'] += $row['R001_Saida_Tributos_ICMS_ST'];
		$array['R001_Entrada_Tributos_IPI'] += $row['R001_Saida_Tributos_IPI'];
		$array['R001_Entrada_Tributos_PIS'] += $row['R001_Saida_Tributos_PIS'];
		$array['R001_Entrada_Tributos_COFINS'] += $row['R001_Saida_Tributos_COFINS'];
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R001_Entrada_Nr,
				count(distinct T013_D024_Id) as R001_Entrada_Clientes,
				sum(T014_Valor_Total_Custo) as R001_Entrada_Valor_Total,
				sum(T014_Valor_Total_Custo) as R001_Entrada_Valor_Total_Produtos,
				SUM(T014_Valor_ICMS) as R001_Entrada_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R001_Entrada_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R001_Entrada_Tributos_IPI,
				0 as R001_Entrada_Tributos_PIS,
				0 as R001_Entrada_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T013
		    	LEFT JOIN T014 ON T014_T013_Id=T013_Id
                left join D006 on D006_Id=T013_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
	            LEFT JOIN D009 ON D009_Id=T014_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
		    where
                T013_Data_Entrada = '{$data}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Entrada_Saida='E'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL1($sql, 'R001_Entrada_Nr', 'R001_Entrada_Clientes', 'R001_Entrada_Valor_Total', 'R001_Entrada_Valor_Total_Produtos', 'R001_Entrada_Tributos_ICMS', 'R001_Entrada_Tributos_ICMS_ST', 'R001_Entrada_Tributos_IPI', 'R001_Entrada_Tributos_PIS', 'R001_Entrada_Tributos_COFINS');
		// Podemos ter Entrada na T007
		$sql = <<<EOT
			select
				count(distinct T007_Numero_Nota_Fiscal) as R001_Saida_Nr,
				count(distinct T007_D024_Id) as R001_Saida_Clientes,
				sum(T008_Valor_Total_Preco) as R001_Saida_Valor_Total,
				sum(T008_Valor_Total_Preco) as R001_Saida_Valor_Total_Produtos,
				SUM(T008_Valor_ICMS) as R001_Saida_Tributos_ICMS,
				SUM(T008_Valor_ICMS_Substituicao) as R001_Saida_Tributos_ICMS_ST,
				SUM(T008_Valor_IPI) as R001_Saida_Tributos_IPI,
				SUM(T008_Valor_PIS) as R001_Saida_Tributos_PIS,
				SUM(T008_Valor_COFINS) as R001_Saida_Tributos_COFINS,
				Vendedor.C007_Id as Vendedor_C007_Id,
            	Supervisor.C007_Id as Supervisor_C007_Id,
            	Empresa.D024_Id,
            	D049_Id,
            	D006_Id
		    from T007
                left join D006 on D006_Id=T007_D006_Id
                left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
		        LEFT JOIN T008 ON T008_T007_Id=T007_Id
	            LEFT JOIN D009 ON D009_Id=T008_D009_Id
	            LEFT JOIN D049 ON D049_Id=D009_D049_Id
			where 
				T007_Data_Emissao = '{$data}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Entrada_Saida='E'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$array['R001_Entrada_Nr'] += $row['R001_Saida_Nr'];
		$array['R001_Entrada_Clientes'] += $row['R001_Saida_Clientes'];
		$array['R001_Entrada_Valor_Total'] += $row['R001_Saida_Valor_Total'];
		$array['R001_Entrada_Valor_Total_Produtos'] += $row['R001_Saida_Valor_Total_Produtos'];
		$array['R001_Entrada_Tributos_ICMS'] += $row['R001_Saida_Tributos_ICMS'];
		$array['R001_Entrada_Tributos_ICMS_ST'] += $row['R001_Saida_Tributos_ICMS_ST'];
		$array['R001_Entrada_Tributos_IPI'] += $row['R001_Saida_Tributos_IPI'];
		$array['R001_Entrada_Tributos_PIS'] += $row['R001_Saida_Tributos_PIS'];
		$array['R001_Entrada_Tributos_COFINS'] += $row['R001_Saida_Tributos_COFINS'];
	}
	
	// NF Entrada Itens (T013)
	if ($D049_Id == 0) {
		$R001_Entrada_Itens = 'count(T014_Id) as R001_Entrada_Itens,';
	} else {
		$R001_Entrada_Itens = 'sum(T014_Quantidade) as R001_Entrada_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Entrada_Itens}
			count(distinct T014_D009_Id) as R001_Entrada_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
	    from T013
	    	LEFT JOIN T014 ON T014_T013_Id=T013_Id
           	left join D006 on D006_Id=T013_D006_Id
           	left join C007 as Vendedor on Vendedor.C007_Id=T013_C007_Id_Devolucao
		   	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		   	left join D024 as Empresa on Empresa.D024_Id=T013_D024_Id
	       	LEFT JOIN D009 ON D009_Id=T014_D009_Id
	       	LEFT JOIN D049 ON D049_Id=D009_D049_Id
		where 
			T013_Data_Entrada = '{$data}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T013_Flag_Cancelada!='S'
	        and D006_Flag_Entrada_Saida='E'
EOT;
	// and T007_C004_Id='$Empresa_Atual'
	processaSQL1($sql, 'R001_Entrada_Itens', 'R001_Entrada_Itens_Diferentes');
	// NF Entrada Itens (T007)
	if ($D049_Id == 0) {
		$R001_Saida_Itens = 'count(T008_Id) as R001_Saida_Itens,';
	} else {
		$R001_Saida_Itens = 'sum(T008_Quantidade) as R001_Saida_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R001_Saida_Itens}
			count(distinct T008_D009_Id) as R001_Saida_Itens_Diferentes,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            Empresa.D024_Id,
            D049_Id,
            D006_Id
	    from T007
            left join T008 on T008_T007_Id=T007_Id
            left join D006 on D006_Id=T007_D006_Id
            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	        left join D024 as Empresa on Empresa.D024_Id=T007_D024_Id
	        LEFT JOIN D009 ON D009_Id=T008_D009_Id
	        LEFT JOIN D049 ON D049_Id=D009_D049_Id
		where 
			T007_Data_Emissao = '{$data}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
	        and D006_Flag_Entrada_Saida='E'
EOT;

	// and T007_C004_Id='$Empresa_Atual'
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$array['R001_Entrada_Itens'] += $row['R001_Saida_Itens'];
	$array['R001_Entrada_Itens_Diferentes'] += $row['R001_Saida_Itens_Diferentes'];
	
	// Não inserir no banco se todos os valores forem em branco
	$doIt = true;
	foreach ($array as $key => $value) {
		if ($key == 'R001_Data' || $key == 'R001_C007_Id_Vendedor' || $key == 'R001_C007_Id_Supervisor' || $key == 'R001_D024_Id' || $key == 'R001_D049_Id' || $key == 'R001_D006_Id') { continue; }
		if (!empty($value)) { $doIt = true; }
	}	
	if ($array['R001_C007_Id_Vendedor'] == 0 && $array['R001_C007_Id_Supervisor'] == 0 && $array['R001_D024_Id'] == 0 && $array['R001_D049_Id'] == 0 && $array['R001_D006_Id'] == 0) {
		if (!$doIt) { $continue = true; }
		//$doIt = true; (reprocessado todos os dias)
	}
	
	$R001 = mysql_query("SELECT R001_Data FROM R001 
									 	 WHERE R001_Data               = '{$array['R001_Data']}' 
										   AND R001_C007_Id_Vendedor   = '{$array['R001_C007_Id_Vendedor']}' 
										   AND R001_C007_Id_Supervisor = '{$array['R001_C007_Id_Supervisor']}'
										   AND R001_D024_Id            = '{$array['R001_D024_Id']}'
										   AND R001_D049_Id            = '{$array['R001_D049_Id']}'
										   AND R001_D006_Id            = '{$array['R001_D006_Id']}'
									  GROUP BY R001_Data");

	//UPDATE
	if (mysql_num_rows($R001) > 0) {

		$sql = "UPDATE R001 SET
		(R001_Clientes_Nr                        ='{$array['R001_Clientes_Nr']}',
		 R001_Clientes_Contatos                  ='{$array['R001_Clientes_Contatos']}',
		 R001_Clientes_Novos                     ='{$array['R001_Clientes_Novos']}',
		 R001_Clientes_Ativos                    ='{$array['R001_Clientes_Ativos']}',
		 R001_Clientes_Inativos                  ='{$array['R001_Clientes_Inativos']}',
		 R001_Orcamentos_Nr                      ='{$array['R001_Orcamentos_Nr']}',
		 R001_Orcamentos_Clientes                ='{$array['R001_Orcamentos_Clientes']}',
		 R001_Orcamentos_Itens                   ='{$array['R001_Orcamentos_Itens']}',
		 R001_Orcamentos_Itens_Diferentes        ='{$array['R001_Orcamentos_Itens_Diferentes']}',
		 R001_Orcamentos_Valor_Total             ='{$array['R001_Orcamentos_Valor_Total']}',
		 R001_Orcamentos_Valor_Total_Produtos    ='{$array['R001_Orcamentos_Valor_Total_Produtos']}',
		 R001_Pedidos_Nr                         ='{$array['R001_Pedidos_Nr']}',
		 R001_Pedidos_Clientes                   ='{$array['R001_Pedidos_Clientes']}',
		 R001_Pedidos_Itens                      ='{$array['R001_Pedidos_Itens']}',
		 R001_Pedidos_Itens_Diferentes           ='{$array['R001_Pedidos_Itens_Diferentes']}',
		 R001_Pedidos_Valor_Total                ='{$array['R001_Pedidos_Valor_Total']}',
		 R001_Pedidos_Valor_Total_Produtos       ='{$array['R001_Pedidos_Valor_Total_Produtos']}',
		 R001_Faturados_Nr                       ='{$array['R001_Faturados_Nr']}',
		 R001_Faturados_Clientes                 ='{$array['R001_Faturados_Clientes']}',
		 R001_Faturados_Itens                    ='{$array['R001_Faturados_Itens']}',
		 R001_Faturados_Itens_Diferentes         ='{$array['R001_Faturados_Itens_Diferentes']}',
		 R001_Faturados_Valor_Total              ='{$array['R001_Faturados_Valor_Total']}',
		 R001_Faturados_Valor_Total_Produtos     ='{$array['R001_Faturados_Valor_Total_Produtos']}',
		 R001_Faturados_IPV                      ='{$array['R001_Faturados_IPV']}',
		 R001_Faturados_IPV_Real                 ='{$array['R001_Faturados_IPV_Real']}',
		 R001_Faturados_Tributos_ICMS            ='{$array['R001_Faturados_Tributos_ICMS']}',
		 R001_Faturados_Tributos_ICMS_ST         ='{$array['R001_Faturados_Tributos_ICMS_ST']}',
		 R001_Faturados_Tributos_IPI             ='{$array['R001_Faturados_Tributos_IPI']}', 
		 R001_Faturados_Tributos_PIS             ='{$array['R001_Faturados_Tributos_PIS']}', 
		 R001_Faturados_Tributos_COFINS          ='{$array['R001_Faturados_Tributos_COFINS']}',
		 R001_Devolucoes_Nr                      ='{$array['R001_Devolucoes_Nr']}',
		 R001_Devolucoes_Clientes                ='{$array['R001_Devolucoes_Clientes']}',
		 R001_Devolucoes_Valor_Total             ='{$array['R001_Devolucoes_Valor_Total']}',
		 R001_Devolucoes_Valor_Total_Produtos    ='{$array['R001_Devolucoes_Valor_Total_Produtos']}',
		 R001_Devolucoes_Tributos_ICMS           ='{$array['R001_Devolucoes_Tributos_ICMS']}',
		 R001_Devolucoes_Tributos_ICMS_ST        ='{$array['R001_Devolucoes_Tributos_ICMS_ST']}', 
		 R001_Devolucoes_Tributos_IPI            ='{$array['R001_Devolucoes_Tributos_IPI']}', 
		 R001_Devolucoes_Tributos_PIS            ='{$array['R001_Devolucoes_Tributos_PIS']}',
		 R001_Devolucoes_Tributos_COFINS         ='{$array['R001_Devolucoes_Tributos_COFINS']}',
		 R001_Compras_Nr                         ='{$array['R001_Compras_Nr']}',
		 R001_Compras_Clientes                   ='{$array['R001_Compras_Clientes']}',
		 R001_Compras_Valor_Total                ='{$array['R001_Compras_Valor_Total']}',
		 R001_Compras_Valor_Total_Produtos       ='{$array['R001_Compras_Valor_Total_Produtos']}',
		 R001_Compras_Tributos_ICMS              ='{$array['R001_Compras_Tributos_ICMS']}', 
		 R001_Compras_Tributos_ICMS_ST           ='{$array['R001_Compras_Tributos_ICMS_ST']}', 
		 R001_Compras_Tributos_IPI               ='{$array['R001_Compras_Tributos_IPI']}',
		 R001_Compras_Tributos_PIS               ='{$array['R001_Compras_Tributos_PIS']}', 
		 R001_Compras_Tributos_COFINS            ='{$array['R001_Compras_Tributos_COFINS']}',
		 R001_Entrada_Nr                         ='{$array['R001_Entrada_Nr']}', 
		 R001_Entrada_Clientes                   ='{$array['R001_Entrada_Clientes']}', 
		 R001_Entrada_Itens                      ='{$array['R001_Entrada_Itens']}', 
		 R001_Entrada_Itens_Diferentes           ='{$array['R001_Entrada_Itens_Diferentes']}',
		 R001_Entrada_Valor_Total                ='{$array['R001_Entrada_Valor_Total']}', 
		 R001_Entrada_Valor_Total_Produtos       ='{$array['R001_Entrada_Valor_Total_Produtos']}',
		 R001_Entrada_Tributos_ICMS              ='{$array['R001_Entrada_Tributos_ICMS']}', 
		 R001_Entrada_Tributos_ICMS_ST           ='{$array['R001_Entrada_Tributos_ICMS_ST']}', 
		 R001_Entrada_Tributos_IPI               ='{$array['R001_Entrada_Tributos_IPI']}', 
		 R001_Entrada_Tributos_PIS               ='{$array['R001_Entrada_Tributos_PIS']}', 
		 R001_Entrada_Tributos_COFINS            ='{$array['R001_Entrada_Tributos_COFINS']}',
		 R001_Saida_Nr                           ='{$array['R001_Saida_Nr']}', 
		 R001_Saida_Clientes                     ='{$array['R001_Saida_Clientes']}', 
		 R001_Saida_Itens                        ='{$array['R001_Saida_Itens']}', 
		 R001_Saida_Itens_Diferentes             ='{$array['R001_Saida_Itens_Diferentes']}',
		 R001_Saida_Valor_Total                  ='{$array['R001_Saida_Valor_Total']}', 
		 R001_Saida_Valor_Total_Produtos         ='{$array['R001_Saida_Valor_Total_Produtos']}',
		 R001_Saida_Tributos_ICMS                ='{$array['R001_Saida_Tributos_ICMS']}', 
		 R001_Saida_Tributos_ICMS_ST             ='{$array['R001_Saida_Tributos_ICMS_ST']}', 
		 R001_Saida_Tributos_IPI                 ='{$array['R001_Saida_Tributos_IPI']}',
		 R001_Saida_Tributos_PIS                 ='{$array['R001_Saida_Tributos_PIS']}', 
		 R001_Saida_Tributos_COFINS              ='{$array['R001_Saida_Tributos_COFINS']}')
		 WHERE R001_Data                         ='{$array['R001_Data']}' 
				AND R001_C007_Id_Vendedor        ='{$array['R001_C007_Id_Vendedor']}' 
				AND R001_C007_Id_Supervisor      ='{$array['R001_C007_Id_Supervisor']}'
				AND R001_D024_Id                 ='{$array['R001_D024_Id']}'
				AND R001_D049_Id                 ='{$array['R001_D049_Id']}'
				AND R001_D006_Id                 ='{$array['R001_D006_Id']}'";

	    
	} else {

		$sql = <<<EOT
			INSERT INTO R001 
			(
				R001_Data,R001_C007_Id_Vendedor,R001_C007_Id_Supervisor,R001_D024_Id,R001_D049_Id,R001_D006_Id,R001_Clientes_Nr,R001_Clientes_Contatos,R001_Clientes_Novos,R001_Clientes_Ativos,R001_Clientes_Inativos,R001_Orcamentos_Nr,R001_Orcamentos_Clientes,R001_Orcamentos_Itens,R001_Orcamentos_Itens_Diferentes,R001_Orcamentos_Valor_Total,R001_Orcamentos_Valor_Total_Produtos,R001_Pedidos_Nr,R001_Pedidos_Clientes,R001_Pedidos_Itens,R001_Pedidos_Itens_Diferentes,R001_Pedidos_Valor_Total,R001_Pedidos_Valor_Total_Produtos,R001_Faturados_Nr,R001_Faturados_Clientes,R001_Faturados_Itens,R001_Faturados_Itens_Diferentes,R001_Faturados_Valor_Total,R001_Faturados_Valor_Total_Produtos,R001_Faturados_IPV,R001_Faturados_IPV_Real,R001_Faturados_Tributos_ICMS, R001_Faturados_Tributos_ICMS_ST, R001_Faturados_Tributos_IPI, R001_Faturados_Tributos_PIS, R001_Faturados_Tributos_COFINS,R001_Devolucoes_Nr,R001_Devolucoes_Clientes,R001_Devolucoes_Valor_Total,R001_Devolucoes_Valor_Total_Produtos, R001_Devolucoes_Tributos_ICMS, R001_Devolucoes_Tributos_ICMS_ST, R001_Devolucoes_Tributos_IPI, R001_Devolucoes_Tributos_PIS, R001_Devolucoes_Tributos_COFINS,R001_Compras_Nr,R001_Compras_Clientes,R001_Compras_Valor_Total,R001_Compras_Valor_Total_Produtos, R001_Compras_Tributos_ICMS, R001_Compras_Tributos_ICMS_ST, R001_Compras_Tributos_IPI, R001_Compras_Tributos_PIS, R001_Compras_Tributos_COFINS, R001_Entrada_Nr, R001_Entrada_Clientes, R001_Entrada_Itens, R001_Entrada_Itens_Diferentes,R001_Entrada_Valor_Total, R001_Entrada_Valor_Total_Produtos, R001_Entrada_Tributos_ICMS, R001_Entrada_Tributos_ICMS_ST, R001_Entrada_Tributos_IPI, R001_Entrada_Tributos_PIS, R001_Entrada_Tributos_COFINS, R001_Saida_Nr, R001_Saida_Clientes, R001_Saida_Itens, R001_Saida_Itens_Diferentes, R001_Saida_Valor_Total, R001_Saida_Valor_Total_Produtos, R001_Saida_Tributos_ICMS, R001_Saida_Tributos_ICMS_ST, R001_Saida_Tributos_IPI, R001_Saida_Tributos_PIS, R001_Saida_Tributos_COFINS
			) VALUES 
			(
				'{$array['R001_Data']}','{$array['R001_C007_Id_Vendedor']}','{$array['R001_C007_Id_Supervisor']}','{$array['R001_D024_Id']}','{$array['R001_D049_Id']}','{$array['R001_D006_Id']}','{$array['R001_Clientes_Nr']}','{$array['R001_Clientes_Contatos']}','{$array['R001_Clientes_Novos']}','{$array['R001_Clientes_Ativos']}','{$array['R001_Clientes_Inativos']}','{$array['R001_Orcamentos_Nr']}','{$array['R001_Orcamentos_Clientes']}','{$array['R001_Orcamentos_Itens']}','{$array['R001_Orcamentos_Itens_Diferentes']}','{$array['R001_Orcamentos_Valor_Total']}','{$array['R001_Orcamentos_Valor_Total_Produtos']}','{$array['R001_Pedidos_Nr']}','{$array['R001_Pedidos_Clientes']}','{$array['R001_Pedidos_Itens']}','{$array['R001_Pedidos_Itens_Diferentes']}','{$array['R001_Pedidos_Valor_Total']}','{$array['R001_Pedidos_Valor_Total_Produtos']}','{$array['R001_Faturados_Nr']}','{$array['R001_Faturados_Clientes']}','{$array['R001_Faturados_Itens']}','{$array['R001_Faturados_Itens_Diferentes']}','{$array['R001_Faturados_Valor_Total']}','{$array['R001_Faturados_Valor_Total_Produtos']}','{$array['R001_Faturados_IPV']}','{$array['R001_Faturados_IPV_Real']}', '{$array['R001_Faturados_Tributos_ICMS']}', '{$array['R001_Faturados_Tributos_ICMS_ST']}', '{$array['R001_Faturados_Tributos_IPI']}', '{$array['R001_Faturados_Tributos_PIS']}', '{$array['R001_Faturados_Tributos_COFINS']}','{$array['R001_Devolucoes_Nr']}','{$array['R001_Devolucoes_Clientes']}','{$array['R001_Devolucoes_Valor_Total']}','{$array['R001_Devolucoes_Valor_Total_Produtos']}', '{$array['R001_Devolucoes_Tributos_ICMS']}', '{$array['R001_Devolucoes_Tributos_ICMS_ST']}', '{$array['R001_Devolucoes_Tributos_IPI']}', '{$array['R001_Devolucoes_Tributos_PIS']}', '{$array['R001_Devolucoes_Tributos_COFINS']}','{$array['R001_Compras_Nr']}','{$array['R001_Compras_Clientes']}','{$array['R001_Compras_Valor_Total']}','{$array['R001_Compras_Valor_Total_Produtos']}', '{$array['R001_Compras_Tributos_ICMS']}', '{$array['R001_Compras_Tributos_ICMS_ST']}', '{$array['R001_Compras_Tributos_IPI']}', '{$array['R001_Compras_Tributos_PIS']}', '{$array['R001_Compras_Tributos_COFINS']}', '{$array['R001_Entrada_Nr']}', '{$array['R001_Entrada_Clientes']}', '{$array['R001_Entrada_Itens']}', '{$array['R001_Entrada_Itens_Diferentes']}', '{$array['R001_Entrada_Valor_Total']}', '{$array['R001_Entrada_Valor_Total_Produtos']}', '{$array['R001_Entrada_Tributos_ICMS']}', '{$array['R001_Entrada_Tributos_ICMS_ST']}', '{$array['R001_Entrada_Tributos_IPI']}', '{$array['R001_Entrada_Tributos_PIS']}', '{$array['R001_Entrada_Tributos_COFINS']}', '{$array['R001_Saida_Nr']}', '{$array['R001_Saida_Clientes']}', '{$array['R001_Saida_Itens']}', '{$array['R001_Saida_Itens_Diferentes']}', '{$array['R001_Saida_Valor_Total']}', '{$array['R001_Saida_Valor_Total_Produtos']}', '{$array['R001_Saida_Tributos_ICMS']}', '{$array['R001_Saida_Tributos_ICMS_ST']}', '{$array['R001_Saida_Tributos_IPI']}', '{$array['R001_Saida_Tributos_PIS']}', '{$array['R001_Saida_Tributos_COFINS']}'
			);
EOT;
		
	}

	$result = mysqli_query($sql);

	if (!$result) {
		return false;
	} else {
		return true;
	}
}
	


