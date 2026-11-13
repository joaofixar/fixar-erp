<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaDescricaoProdutoB2B()
*/

function retornaDescricaoProdutoB2B ($array) {
	global $g;

	$sql = mysql_query("SELECT D001_Descricao_Produto,D003_Nome_Linha,D082_Marca 
						  FROM D009 
					 left join D049 on D049_Id=D009_D049_Id 
					 left join D082 on D082_Id=D049_D082_Id 
					 left join D001 on D001_Id=D049_D001_Id 
					 left join D003 on D003_Id=D001_D003_Id
					 where D009_Id='{$array['todosCampos']['D009_Id']}'");
	$D001 = mysql_fetch_assoc($sql);

	return $D001['D001_Descricao_Produto']; 
}
