<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: danfeNFsDaOrdemDeCompra()
*/

function danfeNFsDaOrdemDeCompra ($array) {
	if (empty($array['campoAtual'])) {
		return '-';
	} else {
		$retorno = '';
		// pode ter vários T013...
		$T013s = explode('|', $array['campoAtual']);
		foreach ($T013s as $T013_Id) {
			if (empty($T013_Id)) { continue; }
			$sql = "SELECT T013_Numero_Nota_Fiscal FROM T013 WHERE T013_Id = '{$T013_Id}'";
			$res = mysql_query($sql);
			$row = mysql_fetch_assoc($res);
			$retorno .= "<a href=\"#\" onclick=\"nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id={$T013_Id}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>{$row['T013_Numero_Nota_Fiscal']}</a>, ";
		}
		return trim($retorno, ', ');
	}
}
