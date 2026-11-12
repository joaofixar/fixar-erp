<?php
namespace hardness;

function campoDescricaoItemPedido($array, $param){
	global $g;
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T006_T219_Id = $array['todosCampos']['T006_T219_Id'];
	$T004_T219_Id = $array['todosCampos']['T004_T219_Id'];
	$T006_Flag_Comprar = $array['todosCampos']['T006_Flag_Comprar'];
	$T006_Descricao_Produto = $array['todosCampos']['T006_Descricao_Produto'];
	$T006_Quantidade_Estoque = $array['todosCampos']['T006_Quantidade_Estoque'];
	$T006_Quantidade = $array['todosCampos']['T006_Quantidade'];

	$sql = <<<EOT
SELECT
	T225_Id, T013_Flag_Travar_Nota
FROM T219
LEFT JOIN T225 ON T225_T219_Id = T219_Id
LEFT JOIN T014 ON T014_T225_Id = T225_Id
LEFT JOIN T013 ON T013_Id=T014_T013_Id
WHERE
	T219_Id = '{$T006_T219_Id}' OR T219_Id = '{$T004_T219_Id}'
EOT;
	$T219 = mysql_query($sql);
	$T219 = mysql_fetch_array($T219);

	if($T006_T219_Id > 0 || $T004_T219_Id > 0){
		if($T219['T225_Id'] <= 0){
			return $array['campoAtual'] . '<br / ><font color="red">Aguardando Compra</font>';
		} else if($T219['T225_Id'] > 0 && $T006_Quantidade_Estoque < $T006_Quantidade){
			if ($T219['T013_Flag_Travar_Nota'] == 'S') {
				return $array['campoAtual'] . '<br / ><font color="blue">XML Recebido, aguardando mercadorias</font>';
			} else {
				return $array['campoAtual'] . '<br / ><font color="blue">Comprado</font>';
			}
		} else if($T219['T225_Id'] > 0 && $T006_Quantidade_Estoque >= $T006_Quantidade){
			return $array['campoAtual'] . '<br / ><font color="green">Mercadoria Recebida</font>';
		}
	} else {
		return $array['campoAtual'];
	}

/*
	if($T006_Quantidade_Estoque < $T006_Quantidade && ($T006_T219_Id <= 0 && $T004_T219_Id <= 0)){
		return "<span style='background-color:#F7F774; color: black;'>Aguarde</span>";
	} else if(($T006_T219_Id > 0 || $T004_T219_Id > 0) && $T219['T219_Valor_Preco_Venda'] <= 0){
		return "<span style='background-color:#8AF774; color: blue;'>Cotando</span>";
	} else if($T219['T219_Valor_Preco_Venda'] > 0){
		$retorno = str_replace("<a ", "<a style='background-color: #BADCFC; color: blue; font-weight: bold;' ", $retorno);
		if(empty($param)){
			return "<span style='color: blue;'>".gCorrigeNumero($array['campoAtual'],4)."</span>";
		} else {
			return $retorno;
		}
	}*/
}
