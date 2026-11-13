<?php
namespace hardness;

switch($g['r'][3]) {
	case 'dadosInfo':
		global $g;
		$campo = (isset($r_campo) && $r_campo) ? $r_campo : false;
		$acaoId = (isset($r_acaoId) && $r_acaoId) ? $r_acaoId : false;
		// $tabela = substr($campo,0,4);
		$tabela = substr($campo,0,strpos($campo, '_'));
		
		$resEntendida['campoInfoEstendida'] = '';
 		if(($tabela == 'T004' || $tabela == 'T006' || $tabela == 'T008') && stripos($campo, "D006_Id_Info") !== false){
			// Vai validar se tem demonstrativo CFOP na estendida para vendas, tabela principal não estava suportando
			// mostrar informação completa caso cliente tenha muita regra
			$campoEntendida = substr_replace($campo, 'A', 4, 0);
			$sql = mysql_query("SELECT {$campoEntendida} as campoInfoEstendida FROM {$tabela}A WHERE {$tabela}A_{$tabela}_Id = '{$acaoId}'");
			$resEntendida = mysql_fetch_array($sql);
		} 

		$sql = mysql_query("SELECT {$campo} FROM {$tabela} WHERE {$tabela}_Id = '{$acaoId}'");
		if(mysql_num_rows($sql) <= 0){
			echo '<b>Nenhum registro encontrado.</b>';
		} else {
			if(empty($resEntendida['campoInfoEstendida'])){
				$res = mysql_fetch_array($sql);
			} else {				
				$res[$campo] = $resEntendida['campoInfoEstendida'];
			}
			$retorno = str_replace('[[divId]]', $r_divId, $res[$campo]);
			echo str_replace('[[permissaoRegras]]', isset($g['c029Ids'][41]), $retorno);
		}
	break;
}