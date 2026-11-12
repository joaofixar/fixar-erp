<?php
namespace hardness;

function precoTotal($array){
	$valorUnit = $array['todosCampos']['T099_Valor_Preco_Unitario'];
	$quantidade = $array['todosCampos']['T099_Quantidade'];
	return $valorUnit * $quantidade;
}

function escolherPedido($array){
	$data = $array['todosCampos']['T099_Data'];
	$data = gCorrigeData($data);
	return <<<EOT
		SELECIONAR PEDIDO<br />
		$data
EOT;
}

function campoExclusao($array) {
	global $g;
	$sql = mysql_query("SELECT * FROM T006 WHERE T006_T099_Id = '{$array['todosCampos']['T099_Id']}'");
	if(mysql_num_rows($sql) <= 0) {
		$resultado = mysql_query("select T099_Id from T099 where T099_Id='{$array['todosCampos']['T099_Id']}'");
	    $row=mysql_fetch_array($resultado);
	    if(!empty($row['T099_Id'])){	
			return <<<EOT
			<span onclick="crm005ExcluirProduto('{$array['todosCampos']['T099_Id']}', '{$g['divId']}');"> 
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</span>
EOT;
		}
	}
}

?>