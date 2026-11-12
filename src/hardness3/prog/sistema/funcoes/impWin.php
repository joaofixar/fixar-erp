<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {	
	case 'imprimir':
		$print = '';
		// Impressão generica de qualquer texto, basta enviar o "impressaoArquivo-base64_encode(nomeDoArquivo)" (arquivo fica no servidor, geralmente no /tmp/)
		$sql = "SELECT T233_Codigo FROM T233 WHERE T233_C007_Id = '{$r_C007_Id}'";
		$resT233 = mysql_query($sql);
		while ($rowT233 = mysql_fetch_assoc($resT233)) {
			$print .= empty($print) ? '' : '---------';
			$print .= "\n\n{$rowT233['T233_Codigo']}\n\n";
		}
		echo $print;
		file_put_contents('/tmp/ultimaEtiquetaImpressa', $print);
		$sql = "DELETE FROM T233 WHERE T233_C007_Id = '{$r_C007_Id}'";
		mysql_query($sql);
	break;

	case 'listaFilaDeImpressao':
		$retorno = '';
		$sql = <<<EOT
SELECT
	C007_Id,
	C007_Nome,
	COUNT(*) as Total
FROM 
	T233 
LEFT JOIN C007 ON C007_Id = T233_C007_Id
GROUP BY T233_C007_Id
EOT;
		$resT233 = mysql_query($sql);
		while ($rowT233 = mysql_fetch_assoc($resT233)) {
			$retorno .= empty($retorno) ? '' : "---------";
			$retorno .= "{$rowT233['C007_Id']}|{$rowT233['C007_Nome']}|{$rowT233['Total']}";
		}
		if (empty($retorno)) {
			$retorno = "VAZIO";
		}
		echo $retorno;
	break;
}
