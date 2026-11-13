<?php
namespace hardness;
/**
* Grid de pesquisa para o campo do formulário tipo "pesquisaGrid"
*/
$resposta = array('code' => true, 'data' => array());
switch($g['r'][3]){
	case 'idAnterior':
		$chaveId = $_REQUEST['id'];
		$tabela = $_REQUEST['tabela'];
		$navSql = ($_REQUEST['navSql'] != '' && $_REQUEST['navSql'] != 'undefined') ? 'AND ' . $_REQUEST['navSql'] : '';
		$orderSql = ($_REQUEST['orderSql'] != '' && $_REQUEST['orderSql'] != 'undefined') ? $_REQUEST['orderSql'] : $tabela.'_Id DESC';
		$sql = mysql_query("SELECT {$tabela}_Id FROM {$tabela} WHERE {$tabela}_Id < {$chaveId} {$navSql} ORDER BY {$orderSql} LIMIT 1");
		if(mysql_num_rows($sql) > 0){
			$res = mysql_fetch_array($sql);
			$resposta['code'] = true;
			$resposta['data'] = $res[0];
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'idProximo':
		$chaveId = $_REQUEST['id'];
		$tabela = $_REQUEST['tabela'];
		$navSql = ($_REQUEST['navSql'] != '' && $_REQUEST['navSql'] != 'undefined') ? 'AND ' . $_REQUEST['navSql'] : '';
		$orderSql = ($_REQUEST['orderSql'] != '' && $_REQUEST['orderSql'] != 'undefined') ? $_REQUEST['orderSql'] : $tabela.'_Id ASC';
		$sql = mysql_query("SELECT {$tabela}_Id FROM {$tabela} WHERE {$tabela}_Id > {$chaveId} {$navSql} ORDER BY {$orderSql} LIMIT 1");
		if(mysql_num_rows($sql) > 0){
			$res = mysql_fetch_array($sql);
			$resposta['code'] = true;
			$resposta['data'] = $res[0];
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'idPrimeiro':
		$chaveId = $_REQUEST['id'];
		$tabela = $_REQUEST['tabela'];
		$navSql = ($_REQUEST['navSql'] != '' && $_REQUEST['navSql'] != 'undefined') ? 'WHERE ' . $_REQUEST['navSql'] : '';
		$orderSql = ($_REQUEST['orderSql'] != '' && $_REQUEST['orderSql'] != 'undefined') ? $_REQUEST['orderSql'] : $tabela.'_Id ASC';
		$sql = mysql_query("SELECT {$tabela}_Id FROM {$tabela} {$navSql} ORDER BY {$orderSql} LIMIT 1");
		if(mysql_num_rows($sql) > 0){
			$res = mysql_fetch_array($sql);
			$resposta['code'] = true;
			$resposta['data'] = $res[0];
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'idUltimo':
		$chaveId = $_REQUEST['id'];
		$tabela = $_REQUEST['tabela'];
		$navSql = ($_REQUEST['navSql'] != '' && $_REQUEST['navSql'] != 'undefined') ? 'WHERE ' . $_REQUEST['navSql'] : '';
		$orderSql = ($_REQUEST['orderSql'] != '' && $_REQUEST['orderSql'] != 'undefined') ? $_REQUEST['orderSql'] : $tabela.'_Id DESC';
		$sql = mysql_query("SELECT {$tabela}_Id FROM {$tabela} {$navSql} ORDER BY {$orderSql} LIMIT 1");
		if(mysql_num_rows($sql) > 0){
			$res = mysql_fetch_array($sql);
			$resposta['code'] = true;
			$resposta['data'] = $res[0];
		} else {
			$resposta['code'] = false;
		}	
	break;

	case 'novoRegistro':
		global $g;
		$classe = $_REQUEST['classe'];
		$case = $_REQUEST['case'];
		$divId = $_REQUEST['divId'];
		$divIdRoot = $_REQUEST['divIdRoot'];
		include_once('./bibliotecas/classes/'.$classe.'.php');
		$classe = "\hardness\\{$classe}";
		$CLASSE = new $classe();		
		$retorno = $CLASSE->$case($divId, $divIdRoot);
		$resposta['data'] = $retorno;
	break;

	case 'duplicarRegistro':
		$classe = $_REQUEST['classe']; 			//Classe a ser chamada
		$case = $_REQUEST['case'];  			//Function da classe a ser chamada
		$id = $_REQUEST['id'];					//Id do form atual
		$tabela = $_REQUEST['tabela'];			//Tabela do form atual
		$divId = $_REQUEST['divId'];			//DivId Atual
		$divIdRoot = $_REQUEST['divIdRoot']; 	//DivIdRoot Atual
		include_once('./bibliotecas/classes/'.$classe.'.php');
		$classe = "\hardness\\{$classe}";
		$CLASSE = new $classe();
		$case = 'duplicar_'.$case;
		log($case);
		$retorno = $CLASSE->$case($divId, $divIdRoot, $tabela, $id);
		$resposta['data'] = $retorno;
	break;

	case 'excluirRegistro':
		$classe = $_REQUEST['classe']; 			//Classe a ser chamada
		$case = $_REQUEST['case'];  			//Function da classe a ser chamada
		$id = $_REQUEST['id'];					//Id do form atual
		$tabela = $_REQUEST['tabela'];			//Tabela do form atual
		$divId = $_REQUEST['divId'];			//DivId Atual
		include_once('./bibliotecas/classes/'.$classe.'.php');
		$classe = "\hardness\\{$classe}";
		$CLASSE = new $classe();
		$case = 'excluir_'.$case;
		$retorno = $CLASSE->$case($divId, $tabela, $id);
		$resposta['data'] = $retorno;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>