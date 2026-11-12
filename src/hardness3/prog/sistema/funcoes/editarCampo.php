<?php
namespace hardness;
/**
* Funções AJAX para pesquisa
*/
incluirColunaEditarRestricoes();
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
	case 'editar':
		$resposta = array('code' => true, 'data' => array());

		// Variaveis
		$chave = (isset($_REQUEST['chave'])) ? $_REQUEST['chave'] : '';
		$campo = (isset($_REQUEST['campo'])) ? mysql_real_escape_string($_REQUEST['campo']) : '';
		$valor = (isset($_REQUEST['valor'])) ? mysql_real_escape_string($_REQUEST['valor']) : '';
		$tipo = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : '';
		$uppercase = (isset($_REQUEST['uppercase'])) ? $_REQUEST['uppercase'] : '';
		$insert = (isset($_REQUEST['insert'])) ? $_REQUEST['insert'] : '';
		// Tabela
		$tabela = explode('_', $campo);
		$tabela = $tabela[0];
		if ($tabela == 'T136') {
			mysql_query('USE global;');
		}
        // Tabelas auxiliares possuem tabela principal
        $tabelaPrincipal = '';
        if (preg_match('/^[A-Z][0-9]{3}A$/i', $tabela)) {
            $tabelaPrincipal = rtrim($tabela, 'A');
        }
		// Pode ser várias chaves (temos que identificar qual é a correta, com a mesma tabela)
		$pks = explode('|', $chave);
		$pk = $pk_valor = $pk_valorTabelaPrincipal = null;
		
		foreach ($pks as $pksValor) {
			$pkTmp = explode('-', $pksValor);

			if (preg_replace('/_Id$/', '', $pkTmp[0]) == $tabela) {
				$pk = $pkTmp[0];
				$pk_valor = $pkTmp[1];
			}

			// Edição na tabela estendida, pega a pk da tabela principal
			if ($tabelaPrincipal && (preg_replace('/_Id$/', '', $pkTmp[0]) == $tabelaPrincipal)) {
				$pk_valorTabelaPrincipal = $pkTmp[1];
			}
		}

		$tabela = mysql_real_escape_string($tabela);
		$pk = mysql_real_escape_string($pk);
		$pk_valor = mysql_real_escape_string($pk_valor);
		$pk_valorTabelaPrincipal = mysql_real_escape_string($pk_valorTabelaPrincipal);

		// Campos complexos são feitos manualmente
		if (strpos($campo, ' ') !== false) {
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		}

        // Suporte a campos JSON
        $campoJson = false;
        if (strpos($campo, '->') !== false) {
            $chave_ = explode('->', $campo);
            $campo = trim($chave_[0]);
            $campoJson = trim($chave_[1]);
        }

		// Verificações
		// Irá entrar nas restrições da tabela estendida - 23/02/2023
        //if (!$tabelaPrincipal) {
		if (empty($pk) && empty($insert)) {
			// Não é mais retornado o erro abaixo, assim é permitido continuar (sem alterar) e chamar o colunaEditarCallback
//			    $resposta['code'] = false;
//			    $resposta['data'] = "Não é possível editar este campo porque a chave primária (chavePrimária) dessa tabela não foi definida no GRID.";
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		} else if (($erro = $g['conf']->restricaoTravar('alterar', $pk, $pk_valor, false, false, $campo, $valor)) !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $erro;
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		} else if (!$campoJson && !in_array($campo, $g['restricaoCamposEditar'])) {
			$resposta['code'] = false;
			$resposta['data'] = 'Você não possui permissão para alterar esse campo. Contate o suporte técnico.<br />Campo: ' . $campo;
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		} else if (!is_numeric($pk_valor) && empty($insert) && !$tabelaPrincipal) {
			// Só valida se não for estendida (!$tabelaPrincipal) porque pode não ter o vínculo da tabela estendida com principal ainda, insert ou update realizado mais a baixo
			$resposta['code'] = false;
			$resposta['data'] = 'Erro ao alterar campo. Por favor contate o suporte técnico.';
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		}
       //}

		// Restrições (todo: efetuar um select e enviar todos os campos da linha que está para ser alterada no 3 parametro)
		$restricao = $g['conf']->restricaoIncluirUpdate($campo, $valor, array($campo => $valor), $pk_valor);
		if ($restricao !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $restricao;
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		}
		
		// Formatação
		if ($uppercase == true){
			$valor = mb_strtoupper($valor);
		}
		if (strpos($tipo, 'moeda') !== false) {
			$valor = gCorrigeNumeroInverte($valor);
			// formata casas decimais para gravar no banco
			//$casas = 2;
			$casasConf = $g['conf']->camposTipo($campo);
			if ($casasConf) {
				$casas = $casasConf[1];
			}
			if (isset($casas)) {
				$valor = number_format($valor, $casas, '.', '');
				$resposta['valor'] = number_format($valor, $casas, ',', '.');
			}
		}
		if (strpos($tipo, 'dataHora') !== false) {
			$valor = explode(' ',$valor);
			$valor = gCorrigeDataInverte($valor[0]).' '.$valor[1];
		} else if (strpos($tipo, 'data') !== false) {
			$valor = gCorrigeDataInverte($valor);
		} else if (strpos($tipo, 'tempo') !== false) {
			$valor = $valor . ':00';
		}

		// Modificar!
        if ($campoJson) {
			if($tabelaPrincipal)
			{
				$sql = "INSERT INTO {$tabela} ({$tabela}_{$tabelaPrincipal}_Id, {$campo}) VALUES ('{$pk_valorTabelaPrincipal}', JSON_SET('{}', '$.{$campoJson}', '{$valor}')) ON DUPLICATE KEY UPDATE {$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', '{$valor}')";
			}
			else
			{
				$sql = "UPDATE {$tabela} SET {$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', '{$valor}') WHERE {$pk} = '{$pk_valor}'";
			}
        } else if ($tabelaPrincipal) {
            //$sql = "INSERT INTO {$tabela} ({$tabela}_{$tabelaPrincipal}_Id, {$campo}) VALUES ('{$pk_valor}', '{$valor}') ON DUPLICATE KEY UPDATE {$campo} = '{$valor}'";
			$sql = "INSERT INTO {$tabela} ({$tabela}_{$tabelaPrincipal}_Id, {$campo}) VALUES ('{$pk_valorTabelaPrincipal}', '{$valor}') ON DUPLICATE KEY UPDATE {$campo} = '{$valor}'";
//            if (strlen($valor) == 0) {
//                $sql = "DELETE FROM {$tabela} WHERE {$tabela}_{$tabelaPrincipal}_Id = '{$pk_valor}'";
//            }
        } else if (empty($pk) && !empty($insert)) {
			// insert
			$sql = "INSERT INTO {$tabela} ($campo) VALUES ('{$valor}')";
		} else {
			// update
			$sql = "UPDATE {$tabela} SET $campo = '{$valor}' WHERE {$pk} = '{$pk_valor}'";
		}
		mysql_query($sql);
		if (mysql_error()) {
			$resposta['code'] = false;
			
			$erro = mysql_error();
			if (preg_match('/Duplicate entry .* for key .*/i', $erro)) {
				$erro = explode('\'', $erro);
				$resposta['data'] = "Já existe um registro com o seguinte valor (duplicado):<br/>{$erro[1]}<br/><br/>Campo:<br/>{$erro[3]}";
			} else {
				$resposta['data'] = 'Erro ao alterar campo. Por favor contate o suporte técnico.<br /><br />' . mysql_error();
			}
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
			die();
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'gGeraSelect':
		$resposta = array('code' => true, 'data' => array());
		$r_gGeraSelect = isset($r_gGeraSelect) ? unserialize(base64_decode($r_gGeraSelect)) : false;
		
		if (!is_array($r_gGeraSelect[0])) {
			$c1 = isset($r_gGeraSelect[0]) ? $r_gGeraSelect[0] : '';
			$c2 = isset($r_gGeraSelect[1]) ? $r_gGeraSelect[1] : '';
			$c3 = isset($r_gGeraSelect[2]) ? $r_gGeraSelect[2] : '';
			$c4 = isset($r_gGeraSelect[3]) ? $r_gGeraSelect[3] : '';
			$c5 = isset($r_gGeraSelect[4]) ? $r_gGeraSelect[4] : '';
			$resposta['code'] = true;
			$resposta['data'] = gProcessaSelect(gGeraSelect($c1, $c2, $c3, $c4, $c5));
		} else {
			$resposta['code'] = true;
			$resposta['data'] = gProcessaSelect($r_gGeraSelect);
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}
?>