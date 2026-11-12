<?php
namespace hardness;

// Variavel global utilizada pelas funções
$array = array();

$sql = "SELECT * FROM R002";
$result = mysql_query($sql);
if (!$result) {
	return false;
}

/**
* R002
*/
$sql = "TRUNCATE R002;";
$result = mysql_query($sql);
if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }

// for's
for ($fAno=date("Y"); $fAno >= 2000; $fAno--) {
	for ($fMes=1; $fMes <= 12; $fMes++) {
		for ($fDia=1; $fDia <= 31; $fDia++) {
			if (!checkdate($fMes, $fDia, $fAno)) { continue; }	// datas invalidas
			//if (mktime(23, 59, 59, $fMes, $fDia, $fAno) > time()) { continue; } // datas maiores que ontem
			$data = "{$fAno}-{$fMes}-{$fDia}";
			//if ("{$fAno}-{$fMes}-{$fDia}" != '2011-10-25') { continue; }
			echo $data . "\n";
			
			$sql = "SELECT R002_Id FROM R002 WHERE R002_Data = '{$data}' AND R002_C004_Id = '0';";
			$result = mysql_query($sql);
			if (mysql_numrows($result) > 0) {
				continue;
			}
			
			$continue = false;

// Empresas
$C004Ids = array();
$sql = "SELECT C004_Id FROM C004;";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$C004Ids[$row['C004_Id']] = true;
}

// Geral
processaR002(0);
// Para cada Empresa
foreach ($C004Ids as $C004_Id => $value) {
	if ($continue) { continue; }
	processaR002($C004_Id);
}


		}
	}
}

/**
* Processa a SQL e insere no array de insert
* 
* @param mixed $sql
*/
function processaSQL2($sql) {
	global $array;
	global $C004Ids;
	
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
}

function processaR002($C004_Id) {
	global $array, $continue, $data;
	global $C004Ids;
	
	
	$Filtro_Empresa1 = '';
	$Filtro_Empresa2 = '';
	$Filtro_Empresa3 = '';
	$Filtro_Empresa4 = '';
	$Filtro_Empresa5 = '';
	$Filtro_Empresa6 = '';
	if ($C004_Id > 0)
	{
		$Filtro_Empresa1 = "and T002_C004_Id='$C004_Id'";
		$Filtro_Empresa2 = "and T015_C004_Id='$C004_Id'";
		$Filtro_Empresa3 = "and T018_C004_Id='$C004_Id'";
		$Filtro_Empresa4 = "and T005_C004_Id='$C004_Id'";
		$Filtro_Empresa5 = "and T019_C004_Id='$C004_Id'";
		$Filtro_Empresa6 = "and D007_C004_Id='$C004_Id'";
	}
	
	// Array utilizado no INSERT
	$array = array(
		'R002_Data' => $data,
		'R002_C004_Id' => $C004_Id,
		'R002_Valor_Receber' => 0.00,
		'R002_Valor_Receber_Projecao' => 0.00,
		'R002_Valor_Pagar' => 0.00,
		'R002_Valor_Pagar_Projecao' => 0.00,
		'R002_Valor_Saldo_Projecao' => 0.00,
		'R002_Valor_Saldo_Real' => 0.00,
		'R002_Valor_Recebido' => 0.00,
		'R002_Valor_Pago' => 0.00,
		'R002_Valor_Saldo' => 0.00,
	);
	
	
	// Valor Receber
	$sql = <<<EOT
		select
			sum(T002_Valor_Total) as R002_Valor_Receber,
			if(T117_Data_Vencimento is not NULL,T117_Data_Vencimento,T002_Data_Vencimento) as T002_Data_Vencimento
        from T002
   			left join T117 on T117_Id=T002_T117_Id     
       	where 
       		T002_Data_Vencimento='{$data}'
         	and T002_Data_Recebimento='0000-00-00'
         	and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
         	and T002_Flag_Status!='2'
            {$Filtro_Empresa1}
EOT;
	processaSQL2($sql, 'R002_Valor_Receber');
	$sql = <<<EOT
		select
			sum(T005_Valor_Total-ifnull((select sum(T019_Valor_Lancamento) from T019 where T019_T005_Id=T005_Id),0)) as T005_Valor_Romaneio
        from T005
        where
        	T005_Data_Emissao = '{$data}'
            and T005_Flag_Status=7
            {$Filtro_Empresa4}
EOT;
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$array['R002_Valor_Receber'] += $row['T005_Valor_Romaneio'];
	
	// Valor Receber Projeção
	$sql = <<<EOT
		select
			sum(T018_Valor_Saldo) as R002_Valor_Receber_Projecao,
			concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) as T018_Data_Vencimento
        from T018
		left join D014 on D014_Id=T018_D014_Id
        where 
        	concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) = '{$data}'
        	and D014_Flag_Tipo='RC'
            {$Filtro_Empresa3}
			and T018_Valor_Saldo>0
			and D014_Flag_Plano='M'
EOT;
	processaSQL2($sql, 'R002_Valor_Receber_Projecao');
	
	// Valor Pagar
	$sql = <<<EOT
		select
			sum(T015_Valor_Total) as R002_Valor_Pagar
		from T015
    	where 
    		T015_Data_Vencimento = '{$data}'
         	and T015_Data_Pagamento='0000-00-00'
            {$Filtro_Empresa2}
EOT;
	processaSQL2($sql, 'R002_Valor_Pagar');
	
	// Valor Pagar Projeção
	$sql = <<<EOT
		select
			sum(T018_Valor_Saldo) as R002_Valor_Pagar_Projecao,
			concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) as T018_Data_Vencimento
		from T018
        left join D014 on D014_Id=T018_D014_Id
        where
        	concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) = '{$data}'
            and D014_Flag_Tipo='PG'
            {$Filtro_Empresa3}
			and T018_Valor_Saldo>0
            and D014_Flag_Plano='M'
EOT;
	processaSQL2($sql, 'R002_Valor_Pagar_Projecao');
	
	// Valor Recebido
	$sql = <<<EOT
		select
             sum(T019_Valor_Lancamento) as R002_Valor_Recebido
    	from T019
		where 
			T019_Data_Lancamento = '{$data}'
			and T019_Valor_Lancamento > 0
	   		{$Filtro_Empresa5}
EOT;
	processaSQL2($sql, 'R002_Valor_Recebido');
	
	// Valor Pago
	$sql = <<<EOT
		select
			sum(T019_Valor_Lancamento) as R002_Valor_Pago
        from T019
    	where 
    		T019_Data_Lancamento = '{$data}'
            and T019_Valor_Lancamento<0
            {$Filtro_Empresa5}
EOT;
	processaSQL2($sql, 'R002_Valor_Pago');
	
	// Não inserir no banco se todos os valores forem em branco
	$doIt = false;
	foreach ($array as $key => $value) {
		if ($key == 'R002_Data' || $key == 'R002_C004_Id') { continue; }
		if (!empty($value)) { $doIt = true; }
	}	
	if ($array['R002_C004_Id'] == 0) { // && ...
		if (!$doIt) { $continue = true; }
		//$doIt = true; (reprocessado todos os dias)
	}
	// INSERT
	if ($doIt) {
		$sql = <<<EOT
			INSERT INTO R002
			(
				`R002_Data`,`R002_C004_Id`,`R002_Valor_Receber`,`R002_Valor_Receber_Projecao`,`R002_Valor_Pagar`,`R002_Valor_Pagar_Projecao`,`R002_Valor_Recebido`,`R002_Valor_Pago`
			) VALUES 
			(
				'{$array['R002_Data']}','{$array['R002_C004_Id']}','{$array['R002_Valor_Receber']}','{$array['R002_Valor_Receber_Projecao']}','{$array['R002_Valor_Pagar']}','{$array['R002_Valor_Pagar_Projecao']}','{$array['R002_Valor_Recebido']}','{$array['R002_Valor_Pago']}'
			);
EOT;
		$result = mysqli_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
	}
}
