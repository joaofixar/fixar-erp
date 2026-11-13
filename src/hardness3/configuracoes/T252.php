<?php

$conf['restricaoIncluirUpdate']['T252_Descricao'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;

/**
* trigger
*/
$conf['triggers']['T252']['insertAfter'] = <<<'EOT'
    $sqlT252 = mysql_query("SELECT * FROM T252 LEFT JOIN C007 ON C007_Id=T252_C007_Id WHERE T252_Id = '{$chaveValor}'");
    $rowT252 = mysql_fetch_array($sqlT252);
    $sqlCheck = mysql_query("SELECT T252_Id FROM T252 WHERE T252_T234_Id = '{$rowT252['T252_T234_Id']}'");

    if($rowT252['T252_C007_Id'] > 0){
        mysqli_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Data_Hora, T254_Status) VALUES ('{$rowT252['T252_T234_Id']}', '{$rowT252['T252_C007_Id']}', CURRENT_TIMESTAMP, '{$rowT252['C007_Primeiro_Nome']} respondeu do chamado')");
    } else if($rowT252['T252_C007_Id_Cliente'] > 0 && mysql_num_rows($sqlCheck) > 1){
        mysqli_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Data_Hora, T254_Status) VALUES ('{$rowT252['T252_T234_Id']}', '999', CURRENT_TIMESTAMP, 'O cliente respondeu do chamado')");
    }
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T252'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $retorno = "Esta interação de suporte não pode alterada";
    }
    if($operacao == 'incluir'){
    	$sqlT234 = mysql_query("SELECT T234_Flag_Finalizado FROM T234 WHERE T234_Id = '{$valorPkParent}'");
    	$rowT234 = mysql_fetch_array($sqlT234);
    	if($rowT234['T234_Flag_Finalizado'] == 'S'){
    		$retorno = "Este chamado está finalizado!";
    	}
    }
EOT;
