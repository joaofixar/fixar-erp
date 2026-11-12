<?php
namespace hardness;
function campoProdutosPedido($array){
	global $g;
	$T005_Id = $array['todosCampos']['T005_Id'];
	$T006 = mysql_query("SELECT * FROM T006 LEFT JOIN T005 ON T005_Id = T006_T005_Id WHERE T005_Id = '{$T005_Id}' ORDER BY T006_Item ASC LIMIT 1");
	if(mysql_num_rows($T006) > 0){
		$T006 = mysql_fetch_array($T006);
		return $T006['T006_Descricao_Produto'];
	} else {
		return '-';
	}
}