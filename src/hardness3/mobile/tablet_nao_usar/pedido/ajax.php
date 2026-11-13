<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

switch($_REQUEST['acao']){
	case 'gravarDadosItemPedido':
		$D009_Id = $_REQUEST['D009_Id'];
		$D024_Id = $_REQUEST['D024_Id'];
		$C007_Id = $_REQUEST['C007_Id'];
		$T266_Quantidade = gCorrigeNumeroInverte($_REQUEST['T266_Quantidade']);
		$T266_Valor_Preco_Sem_Desconto_Unitario = gCorrigeNumeroInverte($_REQUEST['T266_Valor_Preco_Sem_Desconto_Unitario']);

		$resposta = false;
		if(!empty($D009_Id) && !empty($D024_Id) && !empty($C007_Id)){
			$sql = mysql_query("SELECT T266_Id FROM T266 WHERE T266_D009_Id = '{$D009_Id}' AND T266_C007_Id = '{$C007_Id}' AND T266_D024_Id = '{$D024_Id}'");
			$row = mysql_fetch_array($sql);

			if(isset($_REQUEST['T266_Quantidade'])){
				if(!empty($row['T266_Id'])){
					mysql_query("UPDATE T266 SET T266_Quantidade = '{$T266_Quantidade}' WHERE T266_Id = '{$row['T266_Id']}'");
				} else {
					mysql_query("INSERT INTO T266 (T266_D024_Id, T266_C007_Id, T266_D009_Id, T266_Quantidade) VALUES ('{$D024_Id}', '{$C007_Id}', '{$D009_Id}', '{$T266_Quantidade}')");
				}
			}

			if(isset($_REQUEST['T266_Valor_Preco_Sem_Desconto_Unitario'])){
				if(!empty($row['T266_Id'])){
					mysql_query("UPDATE T266 SET T266_Valor_Preco_Sem_Desconto_Unitario = '{$T266_Valor_Preco_Sem_Desconto_Unitario}' WHERE T266_Id = '{$row['T266_Id']}'");
				} else {
					mysql_query("INSERT INTO T266 (T266_D024_Id, T266_C007_Id, T266_D009_Id, T266_Valor_Preco_Sem_Desconto_Unitario) VALUES ('{$D024_Id}', '{$C007_Id}', '{$D009_Id}', '{$T266_Valor_Preco_Sem_Desconto_Unitario}')");
				}
			}
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}