<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: campoValorCotacaoMaisQUantidade()
*/
function campoValorCotacaoMaisQUantidade ($array){
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		//$sql2 = "SELECT SUM(T174_Valor_Custo_Unitario + (T174_Valor_Custo_Unitario*(IFNULL(T174_IPI,0)/100)) + IFNULL(T174_Valor_ST,0)) * T174_Quantidade as total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}'";
		$sql2 = "SELECT SUM((T059_Quantidade * T174_Valor_Custo_Unitario) + (T174_Valor_Custo_Unitario * (IFNULL(T174_IPI,0)/100)*T059_Quantidade) + IFNULL(T174_Valor_ST,0)) as total FROM T174 
				LEFT JOIN T059 ON T059_Id=T174_T059_Id
				WHERE T174_T173_Id='{$linha['T173_Id']}'";
		
		$result2 = mysql_query($sql2);
		$linhaT174 = mysql_fetch_array($result2);
		//echo 'totalForn='.$totalForn.'<br />';
		$totalForn = gCorrigeNumero($linhaT174['total']);
		$retorno .= $totalForn."<br />";
	}
	
	return $retorno;
}
