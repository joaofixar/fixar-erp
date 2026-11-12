<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]){
	case 'gCheckbox':
		$tabela = substr($r_campo,0,strpos($r_campo, '_'));		

		$usuario = ($r_limparC007 == 'S') ? '0' : $g['usuarioAtual'];
		
		$chavesPrimarias = (empty($r_chavesPrimarias)) ? '' : $r_chavesPrimarias;

		// Ajusta SET do UPDATE para o campo que salva o valor da flag quando o tipo for JSON	
		if (strpos($r_campo, '->') !== false) 
		{
			$chave = explode('->', $r_campo);
			$campo = trim($chave[0]);
			$campoJson = trim($chave[1]);
			$setUpdateFlag = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', '{$r_selected}')";
		}
		else
		{
			$setUpdateFlag = "{$r_campo} = '{$r_selected}'";
		}

		// Ajusta SET do UPDATE para o campo que salva o usuário quando o tipo for JSON		
		if (!empty($r_campoC007))
		{
			if (strpos($r_campoC007, '->') !== false) 
			{
				$chave = explode('->', $r_campoC007);
				$campo = trim($chave[0]);
				$campoJson = trim($chave[1]);
				$setUpdateUsuario = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$usuario})";
			}
			else
			{
				$setUpdateUsuario = "{$r_campoC007} = {$usuario}";
			}
		}
		
		// Transforma a string de chaves primarias em um array associativo.
		// Ex.: T005_Id-1003|T005A_Id-342 -> $chaves['T005'] = 1003; $chaves['T005A'] = 342;
		$chavesPrimarias = explode('|', $chavesPrimarias);
		foreach ($chavesPrimarias as $chave)
		{
			$dados = explode('-', $chave);
			$dados[0] = trim($dados[0], '_Id');
			$chaves[$dados[0]] = $dados[1];
		}

		// Caso $tabela não seja estendida
		if (strlen($tabela) == 4)
		{
			$tabelaEstendida = $tabela . 'A';	
			$tabelaPrincipal = $tabela;		
		}
		elseif (strlen($tabela) == 5) // Caso seja estendida
		{
			$tabelaEstendida = $tabela;
			$tabelaPrincipal = trim($tabela, 'A');
		}

		// Caso não exista registro na estendida, insere
		if (!empty($chaves[$tabelaPrincipal]) AND (empty($chaves[$tabelaEstendida]) AND isset($chaves[$tabelaEstendida])))
		{
			$insert = "INSERT INTO {$tabelaEstendida} ({$tabelaEstendida}_{$tabelaPrincipal}_Id) VALUES ({$chaves[$tabelaPrincipal]})";
			log($insert);
			mysql_query($insert);
			$r_tabelaPk = $chaves[$tabelaPrincipal];
		}

        if (empty($r_tabelaPk)) {
            $r_tabelaPk = $chaves[$tabela];
        }

		if(!empty($r_campoC007)) 
		{	
			// Como não é feito LEFT JOIN, se o campo da flag for uma tabela e o do usuário outra o UPDATE irá falhar	
			$query = "UPDATE {$tabela} SET {$setUpdateFlag}, {$setUpdateUsuario} WHERE {$tabela}_Id = '{$r_tabelaPk}'";
		} 
		else 
		{			
			$query = "UPDATE {$tabela} SET {$setUpdateFlag} WHERE {$tabela}_Id = '{$r_tabelaPk}'";
		}
		log($query);
		mysql_query($query);
		
		$resposta['code'] = true;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>