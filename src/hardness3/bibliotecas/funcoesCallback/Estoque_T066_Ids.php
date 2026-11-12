<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: Estoque_T066_Ids()
*/

function Estoque_T066_Ids ($array) {
	$sql = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_Id IN ({$array['campoAtual']})";
	$res = mysql_query($sql);
	$retorno = '<table cellpadding=0 cellspacing=0>';
	while($row = mysql_fetch_assoc($res)) {
		$row['T066_Quantidade_Estoque'] = gCorrigeNumero($row['T066_Quantidade_Estoque']);
		$retorno .= '<tr>';
		$retorno .= "<td style='padding: 0px 7px 0px 7px;'>{$row['D004_Local']}</td>";
		$retorno .= "<td style='padding: 0px 7px 0px 7px;'>{$row['T066_Quantidade_Estoque']}</td>";
		$retorno .= '</tr>';
	}
	$retorno .= '</table>';
	return $retorno;
}
