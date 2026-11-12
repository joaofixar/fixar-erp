<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: campoCusto()
*/

function campoCusto ($array){
	$id = $array['todosCampos']['T001_Id'];
	$valor = gCorrigeNumero($array['todosCampos']['T001_Valor_Custo_Unitario'], 5);
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness/empresas/E003/externos/T001_demonstracao_custo.php3?Abre_Janela=N&T001_Id={$id}',150,300,450,400,'yes','no','no','no','yes','no','Teste')\">$valor</a>";
	return $retorno;
}
