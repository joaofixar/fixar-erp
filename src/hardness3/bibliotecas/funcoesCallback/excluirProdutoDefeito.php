<?php
namespace hardness;
function excluirProdutoDefeito($array){
	global $g;
	$T206_Id = $array['todosCampos']['T206_Id'];
	$T006 = mysql_query("SELECT SUM(T006_Quantidade) as Pedidos FROM T006 WHERE T006_T206_Id = '{$T206_Id}'");
	$retorno = mysql_fetch_array($T006);   
	
	if($retorno['Pedidos'] > 0){
		return null;
	} else {	
		return <<<EOT
			<a href="#" onclick="excluirProdutoDefeito('{$array['todosCampos']['T206_Id']}','{$g['divId']}')">
		    	<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>    
EOT;
	}
	
}
