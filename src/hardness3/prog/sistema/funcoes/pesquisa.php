<?php
namespace hardness;
/**
* Funções AJAX para pesquisa
*/

$callback = (isset($_REQUEST['callback'])) ? $_REQUEST['callback'] : false;

/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Faz um select para o campo 'pesquisa' de um form
	* 
	* @param string $tabela
	* @param string $campoId
	* @param string $campoTxt
	* @param string $sqlExtra	// opcional
	*/
	case 'campoPesquisa':
		$tabela = (isset($_REQUEST['tabela'])) ? $_REQUEST['tabela'] : '';
		$campoId = (isset($_REQUEST['campoId'])) ? $_REQUEST['campoId'] : '';
		$campoTxt = (isset($_REQUEST['campoTxt'])) ? $_REQUEST['campoTxt'] : '';
		$sqlExtra = (isset($_REQUEST['sqlExtra'])) ? $_REQUEST['sqlExtra'] : '';
		$formato = (isset($_REQUEST['formato'])) ? mysql_real_escape_string($_REQUEST['formato']) : '';
		$pesquisa = (isset($_REQUEST['pesquisa'])) ? mysql_real_escape_string($_REQUEST['pesquisa']) : '';
		$todosCampos = (isset($_REQUEST['todosCampos'])) ? json_decode($_REQUEST['todosCampos']) : '';
		
		
		// mais campos?
		$like = '';
		$campoTxt_ = explode(',', $campoTxt);
		if (count($campoTxt_) > 1) {
			foreach ($campoTxt_ as $ct) {
				$ct = str_replace('[[virgula]]', ',', $ct);
				$like .= (empty($like)) ? "{$ct} like '%{$pesquisa}%' " : "OR {$ct} like '%{$pesquisa}%' ";
			}
		} else {
			$like .= "{$campoTxt} like '%{$pesquisa}%' ";
		}

		if ($todosCampos) {
			foreach ($todosCampos as $key => $value) {
				$sqlExtra = str_replace("[[{$key}]]", $value, $sqlExtra);
			}
		}
		if (!empty($pesquisa)) { 
			//$sqlExtra = "WHERE {$like} {$sqlExtra}";
			$sqlExtra = gInsertExtraWhere($sqlExtra, $like);
		}
		// SELECT T084_Id, T084_Id,D022_Nome_Empresa FROM T084 WHERE T084_Id like '%a%' OR D022_Nome_Empresa like '%a%' LEFT JOIN D022 ON D022_Id=T084_D022_Id LIMIT 100;
		
		$sqlExtra .= " LIMIT 100";
	
		$retorno = gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra, false, false, $formato);
		
		echo $callback . '(' . json_encode($retorno) . ')';
		die();
	break;
	
	case 'pesquisaEmail':
	
	function cmp($retorno1, $retorno2) {
    	return strcmp($retorno1['title'], $retorno2['title']);
	}


		if (strpos($r_pesquisa, ',') !== false) {
			$r_pesquisa = preg_split('/(,|;)/Uis', $r_pesquisa);
			$r_pesquisa = trim($r_pesquisa[count($r_pesquisa)-1]);
		}
		if (empty($r_pesquisa)) {
			$retorno = false;
			echo $callback . '(' . json_encode($retorno) . ')';
		}
		$retorno1 = gGeraSelect('D024', 'D024_E_Mail', 'D024_E_Mail', "WHERE D024_E_Mail like '%{$r_pesquisa}%' GROUP BY D024_E_Mail ORDER BY D024_E_Mail LIMIT 100");
		$retorno2 = gGeraSelect('D013', 'D013_E_Mail', 'D013_E_Mail', "WHERE D013_E_Mail like '%{$r_pesquisa}%' GROUP BY D013_E_Mail ORDER BY D013_E_Mail LIMIT 100");
		$retorno = array_merge($retorno1, $retorno2);
		$duplicados = array();
		foreach ($retorno as $key => $value) {
			if (isset($duplicados[$value['title']])) {
				unset($retorno[$key]);
			} else {
				$duplicados[$value['title']] = 1;
				$retorno[$key]['title'] = strtolower(trim($retorno[$key]['title']));
				$retorno[$key]['value'] = strtolower(trim($retorno[$key]['value']));
			}
		}
		usort($retorno, function($a, $b) {
			return ($a['title'] < $b['title']) ? -1 : 1;
		});
		
		usort($retorno, "\hardness\cmp");
		echo $callback . '(' . json_encode($retorno) . ')';
		die();
	break;
}
?>
