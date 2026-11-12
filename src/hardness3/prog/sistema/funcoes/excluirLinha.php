<?php
namespace hardness;

$callback = (isset($_REQUEST['callback'])) ? $_REQUEST['callback'] : false;

switch ($g['r'][3]) {
	case 'excluir':
		$resposta = array('code' => true, 'data' => array());
		
		$pk = (isset($_REQUEST['pk'])) ? $_REQUEST['pk'] : '';
		$pkValor = (isset($_REQUEST['pkValor'])) ? $_REQUEST['pkValor'] : '';
		$divId = (isset($_REQUEST['divId'])) ? $_REQUEST['divId'] : '';
		$campoParent = (isset($_REQUEST['campoParent'])) ? $_REQUEST['campoParent'] : '';
		$valorParent = (isset($_REQUEST['valorParent'])) ? $_REQUEST['valorParent'] : '';
		
		// Restrições de exclusão
		$restricao = $g['conf']->restricaoTravar('excluir', $pk, $pkValor, $campoParent, $valorParent);
		if ($restricao !== true) {
			$verifica = $restricao;
		} else {
			// Verifica integridade referencial
			$g['restricaoExcluir'] = array();
			$g['restricaoExcluirTabelas'] = array();
			$verifica = $g['conf']->restricaoExcluir($pk, $pkValor);
			if (!empty($g['restricaoExcluir'])) {
				$g['restricaoExcluir'] = array_reverse($g['restricaoExcluir']);
				$relacao = '';
				foreach ($g['restricaoExcluir'] as $value) {
                    $tabela = trim(substr($value, 0, 6), '_');
                    $descricao = gDicionarioTabelas($tabela);
                    if (!$descricao || $descricao == $tabela) {
                        global $confUsuario;
                        $query = <<<EOT
SELECT table_comment FROM INFORMATION_SCHEMA.TABLES 
WHERE table_schema = '{$confUsuario["dbDatabase"]}' AND table_name = '{$tabela}'
EOT;
                        $comment = mysqli_fetch_array(mysqli_query($query));
                        $comment = $comment[0];
                        if ($comment) {
                            $descricao = $comment;
                        }
                    }
					$relacao .= $tabela.' - ' . $descricao . "<br/>";
				}
				$verifica = "<strong>Não é possível excluir este registro!</strong><br/><br/>Existem referências para este registro em outras tabelas:<br/><br>{$relacao}<br><strong>Necessário excluir os registros nas tabelas acima, antes de tentar excluir esse registro novamente.</strong><br/>";
			}
		}
		//$verifica = true;
		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			$g['restricaoExcluirTabelas'] = array();
			excluirRecursivo($pk, $pkValor);
			$resposta['data'] = $pk . '-' . $pkValor;
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}

function excluirRecursivo($campo, $valor, $space = '') {
	global $g;
	$tabela = explode('_', $campo);
	$tabela = $tabela[0];
	/*// Excluir em cascata FKs
	$relacionamentos = gPegaRelacionamento2($tabela);
	foreach ($relacionamentos as $fk) {
		$tabela2 = explode('_', $fk);
		$tabela2 = $tabela2[0];
		$sql = "DELETE FROM {$tabela2} WHERE {$fk} = '{$valor}'";
		$result = mysql_query($sql);
		//echo $space . $sql . "\n";
		/*
		$sql = "SELECT {$tabela2}_Id, {$fk} FROM {$tabela2} WHERE {$fk} = '{$valor}'";
		$result = mysql_query($sql);
		if (mysql_error()) { continue; }
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				if (!in_array($tabela2 . '-' . $row[0], $g['restricaoExcluirTabelas'])) {
					// apenas 1 nivel de recursividade
					//if ($space != '') { 
					//	$sql = "DELETE FROM {$tabela2} WHERE {$tabela2}_Id = '{$row[0]}'";
					//	//$result = mysql_query($sql);
					//	echo $space . $sql . "\n";
					//} else {
						$g['restricaoExcluirTabelas'][] = $tabela2 . '-' . $row[0];
						excluirRecursivo($tabela2 . '_Id', $row[0], $space . '    ');
					//}
				}
			}
		}
		
	}*/

	//Excluir tabela estendida
	$tabelaEstendida = $tabela . 'A';
	$id = "{$tabelaEstendida}_Id";
	$fk = "{$tabelaEstendida}_{$tabela}_Id";
	
	$sql = "SELECT {$id} FROM {$tabelaEstendida} WHERE {$fk} = {$valor}";
	$result  = mysql_query($sql);	
	if(mysql_num_rows($result) > 0)  {
		$estendida = mysql_fetch_array($result);
		$sql = "DELETE FROM {$tabelaEstendida} WHERE {$id} = {$estendida[$id]}";
		mysql_query($sql);		
	}
	// Excluir registro	
	$sql = "DELETE FROM {$tabela} WHERE {$campo} = '{$valor}'";	
	$result = mysql_query($sql);
	//echo $space . $sql . "\n";

	return true;
}