<?php
namespace hardness;
/**
* Atualização estatísticas produtos
*/

// Variavel global utilizada pelas funções
$array = array();

$sql = "SELECT * FROM R004";
$result = mysql_query($sql);
if (!$result) {
	return false;
}

/**
* R004
*/
/*$sql = "TRUNCATE R004;";
$result = mysql_query($sql);
if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }*/

// for's
for ($fAno=date("Y"); $fAno >= 2000; $fAno--) {
			$fDia = 1;
			$fMes = 1;
			if (!checkdate($fMes, $fDia, $fAno)) { continue; }	// datas invalidas
			if (mktime(23, 59, 59, $fMes, $fDia, $fAno) > time()) { continue; } // datas maiores que ontem
			$data = "{$fAno}-{$fMes}-{$fDia}";
			$data2 = "{$fAno}-12-31";
			//if ("{$fAno}-{$fMes}-{$fDia}" != '2011-10-25') { continue; }
			echo $data . "\n";
			
			$sql = "SELECT R004_Id FROM R004 WHERE R004_Data >= '{$data}' AND R004_Data <= '{$data2}' AND R004_C007_Id_Vendedor = '0' AND R004_C007_Id_Supervisor = '0' AND R004_D024_Id = '0' AND R004_D049_Id = '0';";
			$result = mysql_query($sql);
			if (mysql_numrows($result) > 0) {
				$sql = "DELETE FROM R004 WHERE R004_Data >= '{$data}' AND R004_Data <= '{$data2}'";
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
processaR004(0, 0, 0, 0, 0);
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
					
					//processaR004($C007_Id_Vendedor, $C007_Id_Supervisor, $D024_Id, $D049_Id, $D006_Id);
					echo "$C007_Id_Vendedor, $C007_Id_Supervisor, $D024_Id, $D049_Id, $D006_Id\n";
				
} // CFOP
} // Produto
} // Cliente/Fornecedor
} // Supervisor
} // Vendedor
die();
*/

// Geral
processaR004(0, 0, 0, 0, 0);
// Para cada vendedor
foreach ($C007IdsVendedores as $C007_Id_Vendedor => $value) {
	if ($continue) { continue; }
	processaR004($C007_Id_Vendedor, 0, 0, 0, 0);
}
// Para cada supervisor
foreach ($C007IdsSupervisores as $C007_Id_Supervisor => $value) {
	if ($continue) { continue; }
	processaR004(0, $C007_Id_Supervisor, 0, 0, 0);
}
// Para cada cliente/fornecedor
foreach ($D024Ids as $D024_Id => $value) {
	if ($continue) { continue; }
	processaR004(0, 0, $D024_Id, 0, 0);
}
// Para cada produto
foreach ($D049Ids as $D049_Id => $value) {
	if ($continue) { continue; }
	processaR004(0, 0, 0, $D049_Id, 0);
}
// Para cada CFOP
foreach ($D006Ids as $D006_Id => $value) {
	if ($continue) { continue; }
	processaR004(0, 0, 0, 0, $D006_Id);
}

}

/**
* Processa a SQL e insere no array de insert
* 
* @param mixed $sql
*/
function processaSQL4($sql) {
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

function processaR004($C007_Id_Vendedor, $C007_Id_Supervisor, $D024_Id, $D049_Id, $D006_Id) {
	global $array, $continue, $data, $data2;
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
		'R004_Data' => $data,
		'R004_C007_Id_Vendedor' => $C007_Id_Vendedor,
		'R004_C007_Id_Supervisor' => $C007_Id_Supervisor,
		'R004_D024_Id' => $D024_Id,
		'R004_D049_Id' => $D049_Id,
		'R004_D006_Id' => $D006_Id,
		'R004_Clientes_Nr' => 0,
		'R004_Clientes_Contatos' => 0,
		'R004_Clientes_Novos' => 0,
		'R004_Clientes_Ativos' => 0,
		'R004_Clientes_Inativos' => 0,
		'R004_Orcamentos_Nr' => 0,
		'R004_Orcamentos_Clientes' => 0,
		'R004_Orcamentos_Itens' => 0,
		'R004_Orcamentos_Itens_Diferentes' => 0,
		'R004_Orcamentos_Valor_Total' => 0.00,
		'R004_Orcamentos_Valor_Total_Produtos' => 0.00,
		'R004_Pedidos_Nr' => 0,
		'R004_Pedidos_Clientes' => 0,
		'R004_Pedidos_Itens' => 0,
		'R004_Pedidos_Itens_Diferentes' => 0,
		'R004_Pedidos_Valor_Total' => 0.00,
		'R004_Pedidos_Valor_Total_Produtos' => 0.00,
		'R004_Faturados_Nr' => 0,
		'R004_Faturados_Clientes' => 0,
		'R004_Faturados_Itens' => 0,
		'R004_Faturados_Itens_Diferentes' => 0,
		'R004_Faturados_Valor_Total' => 0.00,
		'R004_Faturados_Valor_Total_Produtos' => 0.00,
		'R004_Faturados_IPV' => 0.00,
		'R004_Faturados_IPV_Real' => 0.00,
		'R004_Faturados_Tributos_ICMS' => 0.00,
		'R004_Faturados_Tributos_ICMS_ST' => 0.00,
		'R004_Faturados_Tributos_IPI' => 0.00,
		'R004_Faturados_Tributos_PIS' => 0.00,
		'R004_Faturados_Tributos_COFINS' => 0.00,
		'R004_Devolucoes_Nr' => 0,
		'R004_Devolucoes_Clientes' => 0,
		'R004_Devolucoes_Valor_Total' => 0.00,
		'R004_Devolucoes_Valor_Total_Produtos' => 0.00,
		'R004_Devolucoes_Tributos_ICMS' => 0.00,
		'R004_Devolucoes_Tributos_ICMS_ST' => 0.00,
		'R004_Devolucoes_Tributos_IPI' => 0.00,
		'R004_Devolucoes_Tributos_PIS' => 0.00,
		'R004_Devolucoes_Tributos_COFINS' => 0.00,
		'R004_Compras_Nr' => 0,
		'R004_Compras_Clientes' => 0,
		'R004_Compras_Valor_Total' => 0.00,
		'R004_Compras_Valor_Total_Produtos' => 0.00,
		'R004_Compras_Tributos_ICMS' => 0.00,
		'R004_Compras_Tributos_ICMS_ST' => 0.00,
		'R004_Compras_Tributos_IPI' => 0.00,
		'R004_Compras_Tributos_PIS' => 0.00,
		'R004_Compras_Tributos_COFINS' => 0.00,
		'R004_Entrada_Nr' => 0,
		'R004_Entrada_Clientes' => 0,
		'R004_Entrada_Itens' => 0,
		'R004_Entrada_Itens_Diferentes' => 0,
		'R004_Entrada_Valor_Total' => 0.00,
		'R004_Entrada_Valor_Total_Produtos' => 0.00,
		'R004_Entrada_Tributos_ICMS' => 0.00,
		'R004_Entrada_Tributos_ICMS_ST' => 0.00,
		'R004_Entrada_Tributos_IPI' => 0.00,
		'R004_Entrada_Tributos_PIS' => 0.00,
		'R004_Entrada_Tributos_COFINS' => 0.00,
		'R004_Saida_Nr' => 0,
		'R004_Saida_Clientes' => 0,
		'R004_Saida_Itens' => 0,
		'R004_Saida_Itens_Diferentes' => 0,
		'R004_Saida_Valor_Total' => 0.00,
		'R004_Saida_Valor_Total_Produtos' => 0.00,
		'R004_Saida_Tributos_ICMS' => 0.00,
		'R004_Saida_Tributos_ICMS_ST' => 0.00,
		'R004_Saida_Tributos_IPI' => 0.00,
		'R004_Saida_Tributos_PIS' => 0.00,
		'R004_Saida_Tributos_COFINS' => 0.00
	);
	
	
	
	
	
	// D085 - Contatos
	$sql = <<<EOT
		select 
			count(distinct D085_D024_Id) as R004_Clientes_Nr,
            count(distinct D085_Id) as R004_Clientes_Contatos,
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
            D085_Data >= '{$data}' AND D085_Data <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
	        and D085_Mensagem not like 'Orçamento%'
	        and D085_Mensagem not like 'Pedido%'
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL4($sql, 'R004_Clientes_Nr', 'R004_Clientes_Contatos');
	}
	
	// Clientes Novos
	$sql = <<<EOT
		select 
			count(*) as R004_Clientes_Novos,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            D024_Id,
            0 as D049_Id,
            0 as D006_Id
        from D024 
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where
            D024_Data_Cadastro >= '{$data}' AND D024_Data_Cadastro <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa2}
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL4($sql, 'R004_Clientes_Novos');
	}
	
	// Clientes Ativos
	$sql = <<<EOT
		select 
			count(*) as R004_Clientes_Ativos,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            D024_Id,
            0 as D049_Id,
            0 as D006_Id
        from D024 
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where
            D024_Data_Ativo >= '{$data}' AND D024_Data_Ativo <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa2}
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL4($sql, 'R004_Clientes_Ativos');
	}
	
	// Clientes Inativos
	$sql = <<<EOT
		select 
			count(*) as R004_Clientes_Inativos,
			Vendedor.C007_Id as Vendedor_C007_Id,
            Supervisor.C007_Id as Supervisor_C007_Id,
            D024_Id,
            0 as D049_Id,
            0 as D006_Id 
        from D024 
            left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where
            D024_Data_Inativo >= '{$data}' AND D024_Data_Inativo <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa2}
EOT;
	if ($D049_Id == 0 && $D006_Id == 0) {
		processaSQL4($sql, 'R004_Clientes_Inativos');
	}
	
	// Orçamentos Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select
				count(distinct T003_Id) as R004_Orcamentos_Nr,
				count(distinct T003_D024_Id) as R004_Orcamentos_Clientes,
				sum(T003_Valor_Total) as R004_Orcamentos_Valor_Total,
				sum(T003_Valor_Total_Produtos) as R004_Orcamentos_Valor_Total_Produtos,
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
                T003_Data_Emissao >= '{$data}' AND T003_Data_Emissao <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T003_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Orcamentos_Nr', 'R004_Orcamentos_Clientes', 'R004_Orcamentos_Valor_Total', 'R004_Orcamentos_Valor_Total_Produtos');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select
				count(distinct T003_Id) as R004_Orcamentos_Nr,
				count(distinct T003_D024_Id) as R004_Orcamentos_Clientes,
				sum(T004_Valor_Total_Preco) as R004_Orcamentos_Valor_Total,
				sum(T004_Valor_Total_Preco) as R004_Orcamentos_Valor_Total_Produtos,
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
                T003_Data_Emissao >= '{$data}' AND T003_Data_Emissao <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T003_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Orcamentos_Nr', 'R004_Orcamentos_Clientes', 'R004_Orcamentos_Valor_Total', 'R004_Orcamentos_Valor_Total_Produtos');
	}
	
	// Orçamentos Itens
	if ($D049_Id == 0) {
		$R004_Orcamentos_Itens = 'count(T004_Id) as R004_Orcamentos_Itens,';
	} else {
		$R004_Orcamentos_Itens = 'sum(T004_Quantidade) as R004_Orcamentos_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Orcamentos_Itens}
			count(distinct T004_D009_Id) as R004_Orcamentos_Itens_Diferentes,
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
            T003_Data_Emissao >= '{$data}' AND T003_Data_Emissao <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T003_C004_Id='$Empresa_Atual'
	processaSQL4($sql, 'R004_Orcamentos_Itens', 'R004_Orcamentos_Itens_Diferentes');
	
	// Pedidos Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T005_Id) as R004_Pedidos_Nr,
				count(distinct T005_D024_Id) as R004_Pedidos_Clientes,
				sum(T005_Valor_Total) as R004_Pedidos_Valor_Total,
				sum(T005_Valor_Total_Produtos) as R004_Pedidos_Valor_Total_Produtos,
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
                T005_Data_Emissao >= '{$data}' AND T005_Data_Emissao <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T005_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Pedidos_Nr', 'R004_Pedidos_Clientes', 'R004_Pedidos_Valor_Total', 'R004_Pedidos_Valor_Total_Produtos');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T005_Id) as R004_Pedidos_Nr,
				count(distinct T005_D024_Id) as R004_Pedidos_Clientes,
				sum(T006_Valor_Total_Preco) as R004_Pedidos_Valor_Total,
				sum(T006_Valor_Total_Preco) as R004_Pedidos_Valor_Total_Produtos,
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
                T005_Data_Emissao >= '{$data}' AND T005_Data_Emissao <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T005_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Pedidos_Nr', 'R004_Pedidos_Clientes', 'R004_Pedidos_Valor_Total', 'R004_Pedidos_Valor_Total_Produtos');
	}
	
	// Pedidos Itens
	if ($D049_Id == 0) {
		$R004_Pedidos_Itens = 'count(T006_Id) as R004_Pedidos_Itens,';
	} else {
		$R004_Pedidos_Itens = 'sum(T006_Quantidade) as R004_Pedidos_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Pedidos_Itens}
			count(distinct T006_D009_Id) as R004_Pedidos_Itens_Diferentes,
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
            T005_Data_Emissao >= '{$data}' AND T005_Data_Emissao <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T003_C004_Id='$Empresa_Atual'
	processaSQL4($sql, 'R004_Pedidos_Itens', 'R004_Pedidos_Itens_Diferentes');
	
	// NF Faturados Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R004_Faturados_Nr,
				count(distinct T007_D024_Id) as R004_Faturados_Clientes,
				sum(T007_Valor_Total) as R004_Faturados_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R004_Faturados_Valor_Total_Produtos,
				SUM(T007_Valor_Total_ICMS) as R004_Faturados_Tributos_ICMS,
				SUM(T007_Valor_ICMS_Substituicao_Retencao) as R004_Faturados_Tributos_ICMS_ST,
				SUM(T007_Valor_Total_IPI) as R004_Faturados_Tributos_IPI,
				SUM(T007_Valor_Total_PIS) as R004_Faturados_Tributos_PIS,
				SUM(T007_Valor_Total_COFINS) as R004_Faturados_Tributos_COFINS,
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
				T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Faturados_Nr', 'R004_Faturados_Clientes', 'R004_Faturados_Valor_Total', 'R004_Faturados_Valor_Total_Produtos', 'R004_Faturados_Tributos_ICMS', 'R004_Faturados_Tributos_ICMS_ST', 'R004_Faturados_Tributos_IPI', 'R004_Faturados_Tributos_PIS', 'R004_Faturados_Tributos_COFINS');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R004_Faturados_Nr,
				count(distinct T007_D024_Id) as R004_Faturados_Clientes,
				sum(T008_Valor_Total_Preco) as R004_Faturados_Valor_Total,
				sum(T008_Valor_Total_Preco) as R004_Faturados_Valor_Total_Produtos,
				SUM(T008_Valor_ICMS) as R004_Faturados_Tributos_ICMS,
				SUM(T008_Valor_ICMS_Substituicao) as R004_Faturados_Tributos_ICMS_ST,
				SUM(T008_Valor_IPI) as R004_Faturados_Tributos_IPI,
				SUM(T008_Valor_PIS) as R004_Faturados_Tributos_PIS,
				SUM(T008_Valor_COFINS) as R004_Faturados_Tributos_COFINS,
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
				T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Venda_Compra_Outros='V'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Faturados_Nr', 'R004_Faturados_Clientes', 'R004_Faturados_Valor_Total', 'R004_Faturados_Valor_Total_Produtos', 'R004_Faturados_Tributos_ICMS', 'R004_Faturados_Tributos_ICMS_ST', 'R004_Faturados_Tributos_IPI', 'R004_Faturados_Tributos_PIS', 'R004_Faturados_Tributos_COFINS');
	}
	
	// NF Faturados Itens
	if ($D049_Id == 0) {
		$R004_Faturados_Itens = 'count(T008_Id) as R004_Faturados_Itens,';
	} else {
		$R004_Faturados_Itens = 'sum(T008_Quantidade) as R004_Faturados_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Faturados_Itens}
			count(distinct T008_D009_Id) as R004_Faturados_Itens_Diferentes,
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
			T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
	        and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T007_C004_Id='$Empresa_Atual'
	processaSQL4($sql, 'R004_Faturados_Itens', 'R004_Faturados_Itens_Diferentes');
	
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
            T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
            {$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
			and T008_Valor_Custo_Unitario>0
			and D006_Flag_Venda_Compra_Outros='V'
EOT;
	// and T007_C004_Id='$Empresa_Atual'
	$result = mysql_query($sql);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	if (mysql_numrows($result) > 0) {
		$mT008 = mysql_fetch_assoc($result);
		$array['R004_Faturados_IPV'] = ($mT008['Custo'] == 0) ? 0 : $mT008['Preco']/$mT008['Custo'];
		$array['R004_Faturados_IPV_Real'] = ($mT008['Custo_Real'] == 0) ? 0 : $mT008['Preco']/$mT008['Custo_Real'];
	}
	
	// Devoluções
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				sum(T007_Valor_Total) as R004_Devolucoes_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R004_Devolucoes_Valor_Total_Produtos,
		        count(distinct T007_D024_Id) as R004_Devolucoes_Clientes,
		        count(distinct T007_Numero_Nota_Fiscal) as R004_Devolucoes_Nr,
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
                T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
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
				sum(T013_Valor_Total) as R004_Devolucoes_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R004_Devolucoes_Valor_Total_Produtos,
		        count(distinct T013_D024_Id) as R004_Devolucoes_Clientes,
		        count(distinct T013_Numero_Nota_Fiscal) as R004_Devolucoes_Nr,
		        SUM(T013_Valor_Total_ICMS) as R004_Devolucoes_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R004_Devolucoes_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R004_Devolucoes_Tributos_IPI,
				0 as R004_Devolucoes_Tributos_PIS,
				0 as R004_Devolucoes_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
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
				sum(T008_Valor_Total_Preco) as R004_Devolucoes_Valor_Total,
				sum(T008_Valor_Total_Preco) as R004_Devolucoes_Valor_Total_Produtos,
		        count(distinct T007_D024_Id) as R004_Devolucoes_Clientes,
		        count(distinct T007_Numero_Nota_Fiscal) as R004_Devolucoes_Nr,
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
                T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
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
				sum(T014_Valor_Total_Custo) as R004_Devolucoes_Valor_Total,
				sum(T014_Valor_Total_Custo) as R004_Devolucoes_Valor_Total_Produtos,
		        count(distinct T013_D024_Id) as R004_Devolucoes_Clientes,
		        count(distinct T013_Numero_Nota_Fiscal) as R004_Devolucoes_Nr,
		        SUM(T014_Valor_ICMS) as R004_Devolucoes_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R004_Devolucoes_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R004_Devolucoes_Tributos_IPI,
				0 as R004_Devolucoes_Tributos_PIS,
				0 as R004_Devolucoes_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
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
	$array['R004_Devolucoes_Nr'] = $mT007D['R004_Devolucoes_Nr'] + $mT013D['R004_Devolucoes_Nr'];
	$array['R004_Devolucoes_Clientes'] = $mT007D['R004_Devolucoes_Clientes'] + $mT013D['R004_Devolucoes_Clientes'];
	$array['R004_Devolucoes_Valor_Total'] = $mT007D['R004_Devolucoes_Valor_Total'] + $mT013D['R004_Devolucoes_Valor_Total'];
	$array['R004_Devolucoes_Valor_Total_Produtos'] = $mT007D['R004_Devolucoes_Valor_Total_Produtos'] + $mT013D['R004_Devolucoes_Valor_Total_Produtos'];
	$array['R004_Devolucoes_Tributos_ICMS'] = $mT013D['R004_Devolucoes_Tributos_ICMS'];
	$array['R004_Devolucoes_Tributos_ICMS_ST'] = $mT013D['R004_Devolucoes_Tributos_ICMS_ST'];
	$array['R004_Devolucoes_Tributos_IPI'] = $mT013D['R004_Devolucoes_Tributos_IPI'];
	$array['R004_Devolucoes_Tributos_PIS'] = $mT013D['R004_Devolucoes_Tributos_PIS'];
	$array['R004_Devolucoes_Tributos_COFINS'] = $mT013D['R004_Devolucoes_Tributos_COFINS'];
	
	// Compras
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R004_Compras_Nr,
				count(distinct T013_D024_Id) as R004_Compras_Clientes,
				sum(T013_Valor_Total) as R004_Compras_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R004_Compras_Valor_Total_Produtos,
				SUM(T013_Valor_Total_ICMS) as R004_Compras_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R004_Compras_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R004_Compras_Tributos_IPI,
				0 as R004_Compras_Tributos_PIS,
				0 as R004_Compras_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Venda_Compra_Outros='C'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Compras_Nr', 'R004_Compras_Clientes', 'R004_Compras_Valor_Total', 'R004_Compras_Valor_Total_Produtos', 'R004_Compras_Tributos_ICMS', 'R004_Compras_Tributos_ICMS_ST', 'R004_Compras_Tributos_IPI', 'R004_Compras_Tributos_PIS', 'R004_Compras_Tributos_COFINS');
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R004_Compras_Nr,
				count(distinct T013_D024_Id) as R004_Compras_Clientes,
				sum(T014_Valor_Total_Custo) as R004_Compras_Valor_Total,
				sum(T014_Valor_Total_Custo) as R004_Compras_Valor_Total_Produtos,
				SUM(T014_Valor_ICMS) as R004_Compras_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R004_Compras_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R004_Compras_Tributos_IPI,
				0 as R004_Compras_Tributos_PIS,
				0 as R004_Compras_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Venda_Compra_Outros='C'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Compras_Nr', 'R004_Compras_Clientes', 'R004_Compras_Valor_Total', 'R004_Compras_Valor_Total_Produtos', 'R004_Compras_Tributos_ICMS', 'R004_Compras_Tributos_ICMS_ST', 'R004_Compras_Tributos_IPI', 'R004_Compras_Tributos_PIS', 'R004_Compras_Tributos_COFINS');
	}
	
	// NF Saida Totais
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R004_Saida_Nr,
				count(distinct T007_D024_Id) as R004_Saida_Clientes,
				sum(T007_Valor_Total) as R004_Saida_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R004_Saida_Valor_Total_Produtos,
				SUM(T007_Valor_Total_ICMS) as R004_Saida_Tributos_ICMS,
				SUM(T007_Valor_ICMS_Substituicao_Retencao) as R004_Saida_Tributos_ICMS_ST,
				SUM(T007_Valor_Total_IPI) as R004_Saida_Tributos_IPI,
				SUM(T007_Valor_Total_PIS) as R004_Saida_Tributos_PIS,
				SUM(T007_Valor_Total_COFINS) as R004_Saida_Tributos_COFINS,
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
				T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Entrada_Saida='S'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Saida_Nr', 'R004_Saida_Clientes', 'R004_Saida_Valor_Total', 'R004_Saida_Valor_Total_Produtos', 'R004_Saida_Tributos_ICMS', 'R004_Saida_Tributos_ICMS_ST', 'R004_Saida_Tributos_IPI', 'R004_Saida_Tributos_PIS', 'R004_Saida_Tributos_COFINS');
		// Podemos ter NF de Saída na T013
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R004_Entrada_Nr,
				count(distinct T013_D024_Id) as R004_Entrada_Clientes,
				sum(T013_Valor_Total) as R004_Entrada_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R004_Entrada_Valor_Total_Produtos,
				SUM(T013_Valor_Total_ICMS) as R004_Entrada_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R004_Entrada_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R004_Entrada_Tributos_IPI,
				0 as R004_Entrada_Tributos_PIS,
				0 as R004_Entrada_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
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
		$array['R004_Saida_Nr'] += $row['R004_Entrada_Nr'];
		$array['R004_Saida_Clientes'] += $row['R004_Entrada_Clientes'];
		$array['R004_Saida_Valor_Total'] += $row['R004_Entrada_Valor_Total'];
		$array['R004_Saida_Valor_Total_Produtos'] += $row['R004_Entrada_Valor_Total_Produtos'];
		$array['R004_Saida_Tributos_ICMS'] += $row['R004_Entrada_Tributos_ICMS'];
		$array['R004_Saida_Tributos_ICMS_ST'] += $row['R004_Entrada_Tributos_ICMS_ST'];
		$array['R004_Saida_Tributos_IPI'] += $row['R004_Entrada_Tributos_IPI'];
		$array['R004_Saida_Tributos_PIS'] += $row['R004_Entrada_Tributos_PIS'];
		$array['R004_Saida_Tributos_COFINS'] += $row['R004_Entrada_Tributos_COFINS'];
	} else {
		// Para Produtos
		$sql = <<<EOT
			select
				count(distinct T007_Numero_Nota_Fiscal) as R004_Saida_Nr,
				count(distinct T007_D024_Id) as R004_Saida_Clientes,
				sum(T008_Valor_Total_Preco) as R004_Saida_Valor_Total,
				sum(T008_Valor_Total_Preco) as R004_Saida_Valor_Total_Produtos,
				SUM(T008_Valor_ICMS) as R004_Saida_Tributos_ICMS,
				SUM(T008_Valor_ICMS_Substituicao) as R004_Saida_Tributos_ICMS_ST,
				SUM(T008_Valor_IPI) as R004_Saida_Tributos_IPI,
				SUM(T008_Valor_PIS) as R004_Saida_Tributos_PIS,
				SUM(T008_Valor_COFINS) as R004_Saida_Tributos_COFINS,
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
				T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
				{$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
		        and T007_Flag_Cancelada!='S'
		        and D006_Flag_Entrada_Saida='S'
EOT;
		// and T007_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Saida_Nr', 'R004_Saida_Clientes', 'R004_Saida_Valor_Total', 'R004_Saida_Valor_Total_Produtos', 'R004_Saida_Tributos_ICMS', 'R004_Saida_Tributos_ICMS_ST', 'R004_Saida_Tributos_IPI', 'R004_Saida_Tributos_PIS', 'R004_Saida_Tributos_COFINS');
		// Podemos ter Saida na T013
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R004_Entrada_Nr,
				count(distinct T013_D024_Id) as R004_Entrada_Clientes,
				sum(T014_Valor_Total_Custo) as R004_Entrada_Valor_Total,
				sum(T014_Valor_Total_Custo) as R004_Entrada_Valor_Total_Produtos,
				SUM(T014_Valor_ICMS) as R004_Entrada_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R004_Entrada_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R004_Entrada_Tributos_IPI,
				0 as R004_Entrada_Tributos_PIS,
				0 as R004_Entrada_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
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
		$array['R004_Saida_Nr'] += $row['R004_Entrada_Nr'];
		$array['R004_Saida_Clientes'] += $row['R004_Entrada_Clientes'];
		$array['R004_Saida_Valor_Total'] += $row['R004_Entrada_Valor_Total'];
		$array['R004_Saida_Valor_Total_Produtos'] += $row['R004_Entrada_Valor_Total_Produtos'];
		$array['R004_Saida_Tributos_ICMS'] += $row['R004_Entrada_Tributos_ICMS'];
		$array['R004_Saida_Tributos_ICMS_ST'] += $row['R004_Entrada_Tributos_ICMS_ST'];
		$array['R004_Saida_Tributos_IPI'] += $row['R004_Entrada_Tributos_IPI'];
		$array['R004_Saida_Tributos_PIS'] += $row['R004_Entrada_Tributos_PIS'];
		$array['R004_Saida_Tributos_COFINS'] += $row['R004_Entrada_Tributos_COFINS'];
	}
	
	// NF Saida Itens (T007)
	if ($D049_Id == 0) {
		$R004_Saida_Itens = 'count(T008_Id) as R004_Saida_Itens,';
	} else {
		$R004_Saida_Itens = 'sum(T008_Quantidade) as R004_Saida_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Saida_Itens}
			count(distinct T008_D009_Id) as R004_Saida_Itens_Diferentes,
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
			T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T007_Flag_Cancelada!='S'
	        and D006_Flag_Entrada_Saida='S'
EOT;
	// and T007_C004_Id='$Empresa_Atual'
	processaSQL4($sql, 'R004_Saida_Itens', 'R004_Saida_Itens_Diferentes');
	// NF Saida Itens (T013)
	if ($D049_Id == 0) {
		$R004_Entrada_Itens = 'count(T014_Id) as R004_Entrada_Itens,';
	} else {
		$R004_Entrada_Itens = 'sum(T014_Quantidade) as R004_Entrada_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Entrada_Itens}
			count(distinct T014_D009_Id) as R004_Entrada_Itens_Diferentes,
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
			T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
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
	$array['R004_Saida_Itens'] += $row['R004_Entrada_Itens'];
	$array['R004_Saida_Itens_Diferentes'] += $row['R004_Entrada_Itens_Diferentes'];
	
	// NF Entrada Totais
	// Compras
	if ($D049_Id == 0) {
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R004_Entrada_Nr,
				count(distinct T013_D024_Id) as R004_Entrada_Clientes,
				sum(T013_Valor_Total) as R004_Entrada_Valor_Total,
				sum(T013_Valor_Total_Produtos) as R004_Entrada_Valor_Total_Produtos,
				SUM(T013_Valor_Total_ICMS) as R004_Entrada_Tributos_ICMS,
				SUM(T013_Valor_ICMS_Substituicao) as R004_Entrada_Tributos_ICMS_ST,
				SUM(T013_Valor_Total_IPI) as R004_Entrada_Tributos_IPI,
				0 as R004_Entrada_Tributos_PIS,
				0 as R004_Entrada_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Entrada_Saida='E'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Entrada_Nr', 'R004_Entrada_Clientes', 'R004_Entrada_Valor_Total', 'R004_Entrada_Valor_Total_Produtos', 'R004_Entrada_Tributos_ICMS', 'R004_Entrada_Tributos_ICMS_ST', 'R004_Entrada_Tributos_IPI', 'R004_Entrada_Tributos_PIS', 'R004_Entrada_Tributos_COFINS');
		// Podemos ter Entrada na T007
		$sql = <<<EOT
			select 
				count(distinct T007_Numero_Nota_Fiscal) as R004_Saida_Nr,
				count(distinct T007_D024_Id) as R004_Saida_Clientes,
				sum(T007_Valor_Total) as R004_Saida_Valor_Total,
				sum(T007_Valor_Total_Produtos) as R004_Saida_Valor_Total_Produtos,
				SUM(T007_Valor_Total_ICMS) as R004_Saida_Tributos_ICMS,
				SUM(T007_Valor_ICMS_Substituicao_Retencao) as R004_Saida_Tributos_ICMS_ST,
				SUM(T007_Valor_Total_IPI) as R004_Saida_Tributos_IPI,
				SUM(T007_Valor_Total_PIS) as R004_Saida_Tributos_PIS,
				SUM(T007_Valor_Total_COFINS) as R004_Saida_Tributos_COFINS,
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
				T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
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
		$array['R004_Entrada_Nr'] += $row['R004_Saida_Nr'];
		$array['R004_Entrada_Clientes'] += $row['R004_Saida_Clientes'];
		$array['R004_Entrada_Valor_Total'] += $row['R004_Saida_Valor_Total'];
		$array['R004_Entrada_Valor_Total_Produtos'] += $row['R004_Saida_Valor_Total_Produtos'];
		$array['R004_Entrada_Tributos_ICMS'] += $row['R004_Saida_Tributos_ICMS'];
		$array['R004_Entrada_Tributos_ICMS_ST'] += $row['R004_Saida_Tributos_ICMS_ST'];
		$array['R004_Entrada_Tributos_IPI'] += $row['R004_Saida_Tributos_IPI'];
		$array['R004_Entrada_Tributos_PIS'] += $row['R004_Saida_Tributos_PIS'];
		$array['R004_Entrada_Tributos_COFINS'] += $row['R004_Saida_Tributos_COFINS'];
	} else {
		// Para Produtos
		$sql = <<<EOT
			select 
				count(distinct T013_Numero_Nota_Fiscal) as R004_Entrada_Nr,
				count(distinct T013_D024_Id) as R004_Entrada_Clientes,
				sum(T014_Valor_Total_Custo) as R004_Entrada_Valor_Total,
				sum(T014_Valor_Total_Custo) as R004_Entrada_Valor_Total_Produtos,
				SUM(T014_Valor_ICMS) as R004_Entrada_Tributos_ICMS,
				SUM(T014_Valor_ICMS_Substituicao) as R004_Entrada_Tributos_ICMS_ST,
				SUM(T014_Valor_IPI) as R004_Entrada_Tributos_IPI,
				0 as R004_Entrada_Tributos_PIS,
				0 as R004_Entrada_Tributos_COFINS,
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
                T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
                {$Filtro_Vendedor}
                {$Filtro_Supervisor}
                {$Filtro_Empresa}
                {$Filtro_Produto}
                {$Filtro_CFOP}
                AND T013_Flag_Cancelada!='S' 
                and D006_Flag_Entrada_Saida='E'
EOT;
		// and T013_C004_Id='$Empresa_Atual'
		processaSQL4($sql, 'R004_Entrada_Nr', 'R004_Entrada_Clientes', 'R004_Entrada_Valor_Total', 'R004_Entrada_Valor_Total_Produtos', 'R004_Entrada_Tributos_ICMS', 'R004_Entrada_Tributos_ICMS_ST', 'R004_Entrada_Tributos_IPI', 'R004_Entrada_Tributos_PIS', 'R004_Entrada_Tributos_COFINS');
		// Podemos ter Entrada na T007
		$sql = <<<EOT
			select
				count(distinct T007_Numero_Nota_Fiscal) as R004_Saida_Nr,
				count(distinct T007_D024_Id) as R004_Saida_Clientes,
				sum(T008_Valor_Total_Preco) as R004_Saida_Valor_Total,
				sum(T008_Valor_Total_Preco) as R004_Saida_Valor_Total_Produtos,
				SUM(T008_Valor_ICMS) as R004_Saida_Tributos_ICMS,
				SUM(T008_Valor_ICMS_Substituicao) as R004_Saida_Tributos_ICMS_ST,
				SUM(T008_Valor_IPI) as R004_Saida_Tributos_IPI,
				SUM(T008_Valor_PIS) as R004_Saida_Tributos_PIS,
				SUM(T008_Valor_COFINS) as R004_Saida_Tributos_COFINS,
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
				T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
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
		$array['R004_Entrada_Nr'] += $row['R004_Saida_Nr'];
		$array['R004_Entrada_Clientes'] += $row['R004_Saida_Clientes'];
		$array['R004_Entrada_Valor_Total'] += $row['R004_Saida_Valor_Total'];
		$array['R004_Entrada_Valor_Total_Produtos'] += $row['R004_Saida_Valor_Total_Produtos'];
		$array['R004_Entrada_Tributos_ICMS'] += $row['R004_Saida_Tributos_ICMS'];
		$array['R004_Entrada_Tributos_ICMS_ST'] += $row['R004_Saida_Tributos_ICMS_ST'];
		$array['R004_Entrada_Tributos_IPI'] += $row['R004_Saida_Tributos_IPI'];
		$array['R004_Entrada_Tributos_PIS'] += $row['R004_Saida_Tributos_PIS'];
		$array['R004_Entrada_Tributos_COFINS'] += $row['R004_Saida_Tributos_COFINS'];
	}
	
	// NF Entrada Itens (T013)
	if ($D049_Id == 0) {
		$R004_Entrada_Itens = 'count(T014_Id) as R004_Entrada_Itens,';
	} else {
		$R004_Entrada_Itens = 'sum(T014_Quantidade) as R004_Entrada_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Entrada_Itens}
			count(distinct T014_D009_Id) as R004_Entrada_Itens_Diferentes,
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
			T013_Data_Entrada >= '{$data}' AND T013_Data_Entrada <= '{$data2}'
			{$Filtro_Vendedor}
            {$Filtro_Supervisor}
            {$Filtro_Empresa}
            {$Filtro_Produto}
            {$Filtro_CFOP}
	        and T013_Flag_Cancelada!='S'
	        and D006_Flag_Entrada_Saida='E'
EOT;
	// and T007_C004_Id='$Empresa_Atual'
	processaSQL4($sql, 'R004_Entrada_Itens', 'R004_Entrada_Itens_Diferentes');
	// NF Entrada Itens (T007)
	if ($D049_Id == 0) {
		$R004_Saida_Itens = 'count(T008_Id) as R004_Saida_Itens,';
	} else {
		$R004_Saida_Itens = 'sum(T008_Quantidade) as R004_Saida_Itens,';
	}
	$sql = <<<EOT
		select 
			{$R004_Saida_Itens}
			count(distinct T008_D009_Id) as R004_Saida_Itens_Diferentes,
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
			T007_Data_Emissao >= '{$data}' AND T007_Data_Emissao <= '{$data2}'
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
	$array['R004_Entrada_Itens'] += $row['R004_Saida_Itens'];
	$array['R004_Entrada_Itens_Diferentes'] += $row['R004_Saida_Itens_Diferentes'];
	
	// Não inserir no banco se todos os valores forem em branco
	$doIt = false;
	foreach ($array as $key => $value) {
		if ($key == 'R004_Data' || $key == 'R004_C007_Id_Vendedor' || $key == 'R004_C007_Id_Supervisor' || $key == 'R004_D024_Id' || $key == 'R004_D049_Id' || $key == 'R004_D006_Id') { continue; }
		if (!empty($value)) { $doIt = true; }
	}	
	if ($array['R004_C007_Id_Vendedor'] == 0 && $array['R004_C007_Id_Supervisor'] == 0 && $array['R004_D024_Id'] == 0 && $array['R004_D049_Id'] == 0 && $array['R004_D006_Id'] == 0) {
		if (!$doIt) { $continue = true; }
		//$doIt = true; (reprocessado todos os dias)
	}
	// INSERT
	if ($doIt) {
		$sql = <<<EOT
			INSERT INTO R004 
			(
				`R004_Data`,`R004_C007_Id_Vendedor`,`R004_C007_Id_Supervisor`,`R004_D024_Id`,`R004_D049_Id`,`R004_D006_Id`,`R004_Clientes_Nr`,`R004_Clientes_Contatos`,`R004_Clientes_Novos`,`R004_Clientes_Ativos`,`R004_Clientes_Inativos`,`R004_Orcamentos_Nr`,`R004_Orcamentos_Clientes`,`R004_Orcamentos_Itens`,`R004_Orcamentos_Itens_Diferentes`,`R004_Orcamentos_Valor_Total`,`R004_Orcamentos_Valor_Total_Produtos`,`R004_Pedidos_Nr`,`R004_Pedidos_Clientes`,`R004_Pedidos_Itens`,`R004_Pedidos_Itens_Diferentes`,`R004_Pedidos_Valor_Total`,`R004_Pedidos_Valor_Total_Produtos`,`R004_Faturados_Nr`,`R004_Faturados_Clientes`,`R004_Faturados_Itens`,`R004_Faturados_Itens_Diferentes`,`R004_Faturados_Valor_Total`,`R004_Faturados_Valor_Total_Produtos`,`R004_Faturados_IPV`,`R004_Faturados_IPV_Real`,`R004_Faturados_Tributos_ICMS`, `R004_Faturados_Tributos_ICMS_ST`, `R004_Faturados_Tributos_IPI`, `R004_Faturados_Tributos_PIS`, `R004_Faturados_Tributos_COFINS`,`R004_Devolucoes_Nr`,`R004_Devolucoes_Clientes`,`R004_Devolucoes_Valor_Total`,`R004_Devolucoes_Valor_Total_Produtos`, `R004_Devolucoes_Tributos_ICMS`, `R004_Devolucoes_Tributos_ICMS_ST`, `R004_Devolucoes_Tributos_IPI`, `R004_Devolucoes_Tributos_PIS`, `R004_Devolucoes_Tributos_COFINS`,`R004_Compras_Nr`,`R004_Compras_Clientes`,`R004_Compras_Valor_Total`,`R004_Compras_Valor_Total_Produtos`, `R004_Compras_Tributos_ICMS`, `R004_Compras_Tributos_ICMS_ST`, `R004_Compras_Tributos_IPI`, `R004_Compras_Tributos_PIS`, `R004_Compras_Tributos_COFINS`, `R004_Entrada_Nr`, `R004_Entrada_Clientes`, `R004_Entrada_Itens`, `R004_Entrada_Itens_Diferentes`,`R004_Entrada_Valor_Total`, `R004_Entrada_Valor_Total_Produtos`, `R004_Entrada_Tributos_ICMS`, `R004_Entrada_Tributos_ICMS_ST`, `R004_Entrada_Tributos_IPI`, `R004_Entrada_Tributos_PIS`, `R004_Entrada_Tributos_COFINS`, `R004_Saida_Nr`, `R004_Saida_Clientes`, `R004_Saida_Itens`, `R004_Saida_Itens_Diferentes`, `R004_Saida_Valor_Total`, `R004_Saida_Valor_Total_Produtos`, `R004_Saida_Tributos_ICMS`, `R004_Saida_Tributos_ICMS_ST`, `R004_Saida_Tributos_IPI`, `R004_Saida_Tributos_PIS`, `R004_Saida_Tributos_COFINS`
			) VALUES 
			(
				'{$array['R004_Data']}','{$array['R004_C007_Id_Vendedor']}','{$array['R004_C007_Id_Supervisor']}','{$array['R004_D024_Id']}','{$array['R004_D049_Id']}','{$array['R004_D006_Id']}','{$array['R004_Clientes_Nr']}','{$array['R004_Clientes_Contatos']}','{$array['R004_Clientes_Novos']}','{$array['R004_Clientes_Ativos']}','{$array['R004_Clientes_Inativos']}','{$array['R004_Orcamentos_Nr']}','{$array['R004_Orcamentos_Clientes']}','{$array['R004_Orcamentos_Itens']}','{$array['R004_Orcamentos_Itens_Diferentes']}','{$array['R004_Orcamentos_Valor_Total']}','{$array['R004_Orcamentos_Valor_Total_Produtos']}','{$array['R004_Pedidos_Nr']}','{$array['R004_Pedidos_Clientes']}','{$array['R004_Pedidos_Itens']}','{$array['R004_Pedidos_Itens_Diferentes']}','{$array['R004_Pedidos_Valor_Total']}','{$array['R004_Pedidos_Valor_Total_Produtos']}','{$array['R004_Faturados_Nr']}','{$array['R004_Faturados_Clientes']}','{$array['R004_Faturados_Itens']}','{$array['R004_Faturados_Itens_Diferentes']}','{$array['R004_Faturados_Valor_Total']}','{$array['R004_Faturados_Valor_Total_Produtos']}','{$array['R004_Faturados_IPV']}','{$array['R004_Faturados_IPV_Real']}', '{$array['R004_Faturados_Tributos_ICMS']}', '{$array['R004_Faturados_Tributos_ICMS_ST']}', '{$array['R004_Faturados_Tributos_IPI']}', '{$array['R004_Faturados_Tributos_PIS']}', '{$array['R004_Faturados_Tributos_COFINS']}','{$array['R004_Devolucoes_Nr']}','{$array['R004_Devolucoes_Clientes']}','{$array['R004_Devolucoes_Valor_Total']}','{$array['R004_Devolucoes_Valor_Total_Produtos']}', '{$array['R004_Devolucoes_Tributos_ICMS']}', '{$array['R004_Devolucoes_Tributos_ICMS_ST']}', '{$array['R004_Devolucoes_Tributos_IPI']}', '{$array['R004_Devolucoes_Tributos_PIS']}', '{$array['R004_Devolucoes_Tributos_COFINS']}','{$array['R004_Compras_Nr']}','{$array['R004_Compras_Clientes']}','{$array['R004_Compras_Valor_Total']}','{$array['R004_Compras_Valor_Total_Produtos']}', '{$array['R004_Compras_Tributos_ICMS']}', '{$array['R004_Compras_Tributos_ICMS_ST']}', '{$array['R004_Compras_Tributos_IPI']}', '{$array['R004_Compras_Tributos_PIS']}', '{$array['R004_Compras_Tributos_COFINS']}', '{$array['R004_Entrada_Nr']}', '{$array['R004_Entrada_Clientes']}', '{$array['R004_Entrada_Itens']}', '{$array['R004_Entrada_Itens_Diferentes']}', '{$array['R004_Entrada_Valor_Total']}', '{$array['R004_Entrada_Valor_Total_Produtos']}', '{$array['R004_Entrada_Tributos_ICMS']}', '{$array['R004_Entrada_Tributos_ICMS_ST']}', '{$array['R004_Entrada_Tributos_IPI']}', '{$array['R004_Entrada_Tributos_PIS']}', '{$array['R004_Entrada_Tributos_COFINS']}', '{$array['R004_Saida_Nr']}', '{$array['R004_Saida_Clientes']}', '{$array['R004_Saida_Itens']}', '{$array['R004_Saida_Itens_Diferentes']}', '{$array['R004_Saida_Valor_Total']}', '{$array['R004_Saida_Valor_Total_Produtos']}', '{$array['R004_Saida_Tributos_ICMS']}', '{$array['R004_Saida_Tributos_ICMS_ST']}', '{$array['R004_Saida_Tributos_IPI']}'
, '{$array['R004_Saida_Tributos_PIS']}', '{$array['R004_Saida_Tributos_COFINS']}'
			);
EOT;
		$result = mysqli_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
	}
}
