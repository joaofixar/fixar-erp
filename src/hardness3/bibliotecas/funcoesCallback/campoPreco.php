<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: campoPreco()
*/
function campoPreco ($array){
	$id = $array['todosCampos']['T001_Id'];
	$valor = gCorrigeNumero($array['todosCampos']['T001_Valor_Preco_Unitario'],5);
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness/empresas/E003/externos/T001_demonstracao_custo.php3?T001_Id={$id}',0,0,750,500,'yes','no','no','no','yes','no','Teste')\">{$valor}</a>";
	return $retorno;
}
