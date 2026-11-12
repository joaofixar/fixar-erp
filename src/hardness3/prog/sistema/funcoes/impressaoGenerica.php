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
		// Impressão generica de qualquer texto, basta enviar o "impressaoArquivo-base64_encode(nomeDoArquivo)" (arquivo fica no servidor, geralmente no /tmp/)
		if (strpos($r_id, 'impressaoArquivo-') !== false) {
			$r_id = str_replace('impressaoArquivo-', '', $r_id);
			$r_id = base64_decode($r_id);
			echo file_get_contents($r_id);
		}
	break;
}
?>
