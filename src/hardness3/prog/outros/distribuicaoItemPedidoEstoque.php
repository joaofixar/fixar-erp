<?php
namespace hardness;
/**
* Estoque por locação
* Definine quanto será retirado de cada locação (antigas e novas)
* 
* Parametro necessário: $T005_Id
* 
* Novo: hardness3/prog/outros/distribuicaoItemPedidoEstoque.php
* Antigo: hardness/pre_pedido_novo/distribuicaoItemPedidoEstoque.php
* 
* 23/05/2012 - tiago@hardness.com.br
*/

mysql_query("SET AUTOCOMMIT=1");

// Distribui a quantidade de produtos pedidos para as locações
// Executado duas vezes para contabilizar o estoque: VEN001 (ao virar pedido) - VEN002 (ao faturar pedido) - orcamento_venda_2/gerar_pedido.php3 pre_pedido_novo/gerar_pedido.php3 - pedido_venda_new_2/continuar_liberacao.php3
$sql = "SELECT T006_Id, T006_Quantidade FROM T006 WHERE T006_T005_Id='{$T005_Id}';";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	
	// Para cada produto, define quanto será retirado de cada locação
	// Obs.: para locações antigas não são gerados T185
	$sql = "DELETE FROM T185 WHERE T185_T006_Id = '{$row['T006_Id']}';";
	mysql_query($sql);
	$sql = "SELECT * FROM T006 LEFT JOIN T066 ON T066_D009_Id=T006_D009_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T006_Id = '{$row['T006_Id']}'";
	$return = mysql_query($sql);
	if (mysql_error()) {
		return 'Erro: ' . mysql_error();
	}
	
	// Quanto retirar de cada locação?
	$T006_Quantidade = $row['T006_Quantidade'];	// quantidade total para separar
	$arrayRetiradas = array();
	while($sepEstoque = mysql_fetch_array($return)) {
		// Verifica se estamos em uma locação antigo (onde o estoque não era definido por locação)
		if (empty($sepEstoque['D004_Id']) || empty($sepEstoque['D004_Flag_Tipo'])) {
			if (!isset($arrayRetiradas['antigo'])) {
				$arrayRetiradas['antigo'] = array($sepEstoque['T006_Id'], $sepEstoque['T066_Id'], 0);
			}
			continue;
		}
		if ($T006_Quantidade != 0) {
			/*
			if ($T006_Quantidade <= $row['T066_Quantidade_Estoque_Liquido']) {
				$arrayRetiradas[] = array($sepEstoque['T006_Id'], $sepEstoque['T066_Id'], $T006_Quantidade);
				$T006_Quantidade = 0;
			} else {
				$arrayRetiradas[] = array($sepEstoque['T006_Id'], $sepEstoque['T066_Id'], $row['T066_Quantidade_Estoque_Liquido']);
				$T006_Quantidade = $T006_Quantidade - $row['T066_Quantidade_Estoque_Liquido'];
			}*/
			$arrayRetiradas[] = array($sepEstoque['T006_Id'], $sepEstoque['T066_Id'], $T006_Quantidade);
			$T006_Quantidade = 0;
		}
	}
	if ($T006_Quantidade > 0) {
		$arrayRetiradas['antigo'][2] = $T006_Quantidade;
	}
	
	// Cria as T185 (tabela que indica de onde será retirado o que)
	foreach ($arrayRetiradas as $sepEstoque) {
		$sql = "INSERT INTO T185(
					T185_T006_Id,
				  	T185_T066_Id,
				  	T185_Qtd_Para_Separar
				  	)VALUES(
				  	'".mysql_real_escape_string($sepEstoque[0])."',
				  	'".mysql_real_escape_string($sepEstoque[1])."',
				  	'".mysql_real_escape_string($sepEstoque[2])."'
				  	)";
		$return2 = mysql_query($sql);
	}
}
/**
* FIM Estoque por locação
*/