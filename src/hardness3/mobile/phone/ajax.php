<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

switch($_REQUEST['acao']){
	case 'gravarDadosItemOrcamento':
		$D009_Id = $_REQUEST['D009_Id'];
		$D024_Id = $_REQUEST['D024_Id'];
		$C007_Id = $_REQUEST['C007_Id'];
		$T198_Quantidade = gCorrigeNumeroInverte($_REQUEST['T198_Quantidade']);
		$T198_Valor_Preco_Sem_Desconto_Unitario = gCorrigeNumeroInverte($_REQUEST['T198_Valor_Preco_Sem_Desconto_Unitario']);

		$resposta = false;
		if(!empty($D009_Id) && !empty($D024_Id) && !empty($C007_Id)){
			$sql = mysql_query("SELECT T198_Id FROM T198 WHERE T198_D009_Id = '{$D009_Id}' AND T198_C007_Id = '{$C007_Id}' AND T198_D024_Id = '{$D024_Id}'");
			$row = mysql_fetch_array($sql);

			if(isset($_REQUEST['T198_Quantidade'])){
				if(!empty($row['T198_Id'])){
					mysql_query("UPDATE T198 SET T198_Quantidade = '{$T198_Quantidade}' WHERE T198_Id = '{$row['T198_Id']}'");
				} else {
					mysql_query("INSERT INTO T198 (T198_D024_Id, T198_C007_Id, T198_D009_Id, T198_Quantidade) VALUES ('{$D024_Id}', '{$C007_Id}', '{$D009_Id}', '{$T198_Quantidade}')");
				}
			}

			if(isset($_REQUEST['T198_Valor_Preco_Sem_Desconto_Unitario'])){
				if(!empty($row['T198_Id'])){
					mysql_query("UPDATE T198 SET T198_Valor_Preco_Sem_Desconto_Unitario = '{$T198_Valor_Preco_Sem_Desconto_Unitario}' WHERE T198_Id = '{$row['T198_Id']}'");
				} else {
					mysql_query("INSERT INTO T198 (T198_D024_Id, T198_C007_Id, T198_D009_Id, T198_Valor_Preco_Sem_Desconto_Unitario) VALUES ('{$D024_Id}', '{$C007_Id}', '{$D009_Id}', '{$T198_Valor_Preco_Sem_Desconto_Unitario}')");
				}
			}
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}