<?php
namespace hardness;

function campoPrecoFornecedorPedido($array, $param){
	global $g;
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T006_T219_Id = $array['todosCampos']['T006_T219_Id'];
	$T219_Id = $array['todosCampos']['T219_Id'];
	$T006_Flag_Comprar = $array['todosCampos']['T006_Flag_Comprar'];
	$D009_Quantidade_OC = $array['todosCampos']['D009_Quantidade_OC'];
	$D009_Quantidade_Estoque = $array['todosCampos']['D009_Quantidade_Estoque'];
	$D009_Valor_Custo_Tabela = $array['todosCampos']['D009_Valor_Custo_Tabela'];
	$T006_Quantidade = $array['todosCampos']['T006_Quantidade'];
	$D049_Flag_Tipo = $array['todosCampos']['D049_Flag_Tipo'];
	$D070_Data_Vencimento = $array['todosCampos']['D070_Data_Vencimento'];
	$T006_Flag_Transferencia = $array['todosCampos']['T006_Flag_Transferencia'];

	$T219 = mysql_query("SELECT T219_Valor_Preco_Venda FROM T219 WHERE T219_Id = '{$T006_T219_Id}'");
	$T219 = mysql_fetch_array($T219);

	require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
	$precoTabela = abrirNotificacao($array, $param);

	if(!isset($g['c029Ids'][208])){
		return gCorrigeNumero($array['campoAtual'], 4);
	} else {	
		return $precoTabela;
	}

	//Aguarde, Cotando, Preço Retornado em Cor Diferente
}
