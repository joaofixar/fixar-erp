<?php
namespace hardness;
set_time_limit(0);

$resposta = array('code' => true, 'data' => array());

$dir = "{$g['pathDados']}importacaoCSV/";

switch($g['r'][3]) {
 	case 'campoColuna':
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		// array(CAMPO, PK, FK)
		$info[$r_coluna] = array($r_campo, isset($info[$r_coluna][1]) ? $info[$r_coluna][1] : false, false);
		
		file_put_contents($arquivoInfo, serialize($info));
	break;
	
	case 'campoColunaPK':
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		// Somente uma PK por tabela
		if ($r_pk == 'true') {
			foreach ($info as $value) {
				$value[1] = false;
			}
			$info[$r_coluna][1] = true;
		} else {
			$info[$r_coluna][1] = false;
		}
		
		file_put_contents($arquivoInfo, serialize($info));
	break;
	
	case 'campoColunaFKSelect':
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		$tabela = explode('_', $r_campo);
		$tabela = $tabela[1];
		
		// monta combo
		$selectArray = array(array('title' => 'NENHUM', 'value' => ''));
		$sql = "SHOW COLUMNS FROM {$tabela};";
		$res = mysqli_query($sql);
		$tabelas = array();
		while ($row = mysql_fetch_array($res)) {
			$tabela_ = preg_replace("/^{$tabela}_/", '', $row[0]);
            $title = "{$tabela_} ({$row[1]})";
            $title = strlen($title) > 40 ? substr($title,0,40) . " ..." : $title;
			$selectArray[] = array('title' => $title, 'value' => $row[0], 'selected' => ($info[$r_coluna][2] == $row[0]) ? true : false);
		}
		usort($selectArray, function($a, $b) {
			if ($a['value'] == $b['value']) {
		        return 0;
		    }
		    return ($a['value'] < $b['value']) ? -1 : 1;
		});
		
		if (count($selectArray) > 1) {
			$resposta['data'] = "<select class=\"formInput ui-state-default ui-corner-all\" onChange=\"campoColunaFK('{$r_coluna}', \$(this)); return false;\">" . gProcessaSelect($selectArray) . '</select>';
		}
		
	break;
	
	case 'campoColunaFK':
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		// array(CAMPO, PK, FK)
		$info[$r_coluna][2] = $r_campo;
		
		file_put_contents($arquivoInfo, serialize($info));
	break;
	
	case 'truncate':
		//if ($g['usuarioAtual'] != 63) { die('Somente o usuário root pode fazer esta operação.'); }
		//mysql_query("TRUNCATE {$r_tabela};");
	break;
	
	case 'importar':
		$cacheConversorCampos = array();
		$resposta['erros'] = '';
		$resposta['logInsert'] = '';
		$resposta['logUpdate'] = '';
		$arquivo = $dir . 'importacaoCSV-ultimoArquivo.csv';
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivo) || !file_exists($arquivoInfo)) {
			$resposta['code'] = false;
			$resposta['erros'] = 'Arquivo .CSV não foi carregado!';
		} else {
			// carrega arquivo
			$info = unserialize(file_get_contents($arquivoInfo));
			// gera INSERT/UPDATE base
			$infoCols = '';
			$infoValues = '';
			$infoSet = '';
			$infoPk = array();
			$verificaDuplicado = array();
			$functionPersonalizaLimparAcentos = false;
			$gLimpaAcentosImportarCSV = "dados_usuarios/{$confUsuario['dbDatabase']}/personalizacaoConf/gLimpaAcentosImportarCSV-alterarCallback.php";
			if (file_exists($gLimpaAcentosImportarCSV)) {
				// Apenas personalização, a função gLimpaAcentos é utilizada em outros locais, por isso não foi alterada para não dar nenhum problema
				// Personaliza essa funcção no cliente e trata os caracretes que ele quer manter na importacaoCSV
				require_once ($gLimpaAcentosImportarCSV);
				$functionPersonalizaLimparAcentos = true;
			} 
			
			foreach ($info as $key => $value) {
				//if ($key == 'pularLinha') { continue; }
				if (!isset($value[0]) || empty($value[0])) { continue; }
				if (in_array($value[0], $verificaDuplicado)) { 
					$resposta['code'] = false;
					$resposta['erros'] .= "Coluna da tabela selecionada mais de uma vez: {$value[0]}<br />";
				}
				$verificaDuplicado[] = $value[0];
				$infoCols .= empty($infoCols) ? $value[0] : ', ' . $value[0];
				$infoValues .= empty($infoValues) ? "'[[{$key}]]'" : ", '[[{$key}]]'";
				// PK
				if (isset($value[1]) && $value[1]) {
					/*if (!empty($infoPk)) {
						$resposta['code'] = false;
						$resposta['erros'] .= "Foi definido mais de uma chave primária (PK).";
					}*/
					$infoPk[] = $value;
					//continue;
				} else {
					if ($value[0] == 'D009_Preco_Loja') {
						// Especifico para Ceigon - está no código enquanto não existir uma opção na importação para fazer isso
						$infoSet .= empty($infoSet) ? "{$value[0]} = TRUNCATE('[[{$key}]]', 2)" : ", {$value[0]} = TRUNCATE('[[{$key}]]', 2)";
					} else {
						$infoSet .= empty($infoSet) ? "{$value[0]} = '[[{$key}]]'" : ", {$value[0]} = '[[{$key}]]'";
					}
				}
			}
			// verifica se foi definido uma PK
			if (($r_tipo == 'update' || $r_tipo == 'both') && empty($infoPk)) {
				$resposta['code'] = false;
				$resposta['erros'] .= "Não foi definido uma coluna de chave primária (PK).";
			}
			// não continuar em caso de erro
			if ($resposta['code']) {
				// Identificar chaves duplicadas
				$chavesPrimariasDuplicadas = array();
				foreach ($infoPk as $key => $value) {
					if (isset($value[2]) && $value[2]) {
						$chavesPrimariasDuplicadas[$key] = array('Nome' => $value[2], 'Chaves' => array());
					} else {
						$chavesPrimariasDuplicadas[$key] = array('Nome' => $value[0], 'Chaves' => array());
					}
				}
				// processa CSV e preenche linha por linha o INSERT/UPDATE
				$resposta['linhasOk'] = 0;
				$resposta['linhasErro'] = 0;
				if (($handle = fopen($arquivo, "r")) !== FALSE) {
					$count = 0;
					while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
						$count++;
						if (isset($info['pularLinha']) && $count == 1) { continue; }
						// Decide se vai fazer um INSERT ou um UPDATE
						$r_tipo_both = false;
						// PK e JOINS necessário para o SELECT e UPDATE
						$pk = '';
						$joins = '';
						foreach ($infoPk as $key => $value) {
							// Se for uma FK, é necessário fazer os joins para o update
							if (isset($value[2]) && $value[2]) {
								$fkTmp = explode('_', $value[2]);
								$joins .= "JOIN {$fkTmp[0]} ON {$fkTmp[0]}_Id = {$value[0]} ";
								//$joins .= "JOIN {$fkTmp[1]} ON {$fkTmp[1]}_Id = {$value[2]} ";
								$pk .= "AND {$value[2]} = '[[PK{$key}]]' ";
							} else {
								$pk .= "AND {$value[0]} = '[[PK{$key}]]' ";
							}
						}
						$pk = trim($pk, 'AND');
						$sql = "SELECT * FROM {$r_tabela} {$joins} WHERE {$pk};";
						$pkCount = 0;
						foreach ($data as $key => $value) {
							// Padronização de valores
							if (preg_match('#^[0-9]+,[0-9]+$#', $value)) {
								$value = str_replace(',', '.', $value);
							} else if (preg_match('#^([0-9]+\.)+[0-9]+,[0-9]+$#', $value)) {
								$value = str_replace('.', '', $value);
								$value = str_replace(',', '.', $value);
							} else if (preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $value)) {
								$value = explode('/', $value);
								$value = "{$value[2]}-{$value[1]}-{$value[0]}";
							} else {
								if($functionPersonalizaLimparAcentos){
									// Só entra aqui se existir arquivo personalizado
									$value = _gLimpaAcentosImportarCSV($value);
								} else {
									$value = gLimpaAcentos($value);
								}
							}
							$value = trim($value);
							//
							if (isset($info[$key]) && isset($info[$key][1]) && $info[$key][1]) {
								if (empty($value)) {
									continue 2;
								}
								$sql = str_replace("[[PK{$pkCount}]]", $value, $sql);
								$pkCount++;
							}
						}
						$resCheck = mysql_query($sql);
						$insertLinha = '';
						if (mysql_numrows($resCheck)) {
							$r_tipo_both = 'update';
							if ($r_tipo == 'update' || $r_tipo == 'both') {
								$insertLinha = "UPDATE {$r_tabela} {$joins} SET {$infoSet} WHERE {$pk};";
							}
						} else {
							if ($r_tipo == 'insert' || $r_tipo == 'both') {
								$insertLinha = "INSERT INTO {$r_tabela} ({$infoCols}) VALUE ({$infoValues});";
							}
						}
						if (empty($insertLinha)){
							continue;
						}
		    			$cols = count($data);
		    			$pkCount = 0;
		    			foreach ($data as $key => $value) {
		    				// Padronização de valores
							if (preg_match('#^[0-9]+,[0-9]+$#', $value)) {
								$value = str_replace(',', '.', $value);
							} else if (preg_match('#^([0-9]+\.)+[0-9]+,[0-9]+$#', $value)) {
								$value = str_replace('.', '', $value);
								$value = str_replace(',', '.', $value);
							} else if (preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $value)) {
								$value = explode('/', $value);
								$value = "{$value[2]}-{$value[1]}-{$value[0]}";
							} else {
								if($functionPersonalizaLimparAcentos){
									// Só entra aqui se existir arquivo personalizado
									$value = _gLimpaAcentosImportarCSV($value);
								} else {
									$value = gLimpaAcentos($value);
								}
							}
							$value = trim($value);
		    				// É FK! Tentar achar a chave primária
							$value = mysql_real_escape_string(utf8_encode($value));
							// Se for FK e não for PK
							if (isset($info[$key][2]) && !empty($info[$key][2]) && !(isset($info[$key]) && isset($info[$key][1]) && $info[$key][1])) {
								$campoFK = $info[$key][2];
								$tabelaFK = explode('_', $campoFK);
								$tabelaFK = $tabelaFK[0];
								$sql = "SELECT {$tabelaFK}_Id FROM {$tabelaFK} WHERE {$campoFK} = '{$value}';";
								$resFK = mysqli_query($sql);
								if (!$resFK || mysql_num_rows($resFK) == 0) {
									// Se não existir, insere a FK
									$sql = "INSERT INTO {$tabelaFK} ({$campoFK}) VALUES ('{$value}');";
									mysqli_query($sql);
									$value = mysql_insert_id();
								} else {
									$rowFK = mysql_fetch_array($resFK);
									$value = $rowFK[0];
								}
							}
							// conversões de campos
							//$cacheConversorCampos
							//SHOW COLUMNS FROM T002 WHERE `Field` = 'T002_Valor_Juros'
		        			// é a chave primária?
		        			if ($r_tipo == 'update' || $r_tipo_both == 'update') {
		        				if (isset($info[$key]) && isset($info[$key][1]) && $info[$key][1]) {
		        					if (empty($value)) {
										continue 2;
									}
									/*
									// Identificar chaves duplicadas
									if (in_array($value, $chavesPrimariasDuplicadas[$pkCount]['Chaves'])) {
										$resposta['erros'] .= "PK Dupicada: {$chavesPrimariasDuplicadas[$pkCount]['Nome']} - {$value}\n<br />";
										$resposta['linhasErro']++;
										continue 2;
									} else {*/
										$chavesPrimariasDuplicadas[$pkCount]['Chaves'][] = $value;
							/*		}
									*/
									
		        					$insertLinha = str_replace("[[PK{$pkCount}]]", $value, $insertLinha);
		        					$pkCount++;
								}
								$insertLinha = str_replace("[[{$key}]]", $value, $insertLinha);
							} else {
								$insertLinha = str_replace("[[{$key}]]", $value, $insertLinha);
							}
		    			}
		    			if ($r_simula == 'false') {
		    				$result = mysqli_query($insertLinha);
						}
		    			if (strpos($insertLinha, 'INSERT INTO') !== false && strlen($resposta['logInsert']) < 100000) {
		    				$resposta['logInsert'] .= $insertLinha . "\n<br />";
						} else if (strlen($resposta['logUpdate']) < 100000) {
		    				$resposta['logUpdate'] .= $insertLinha . "\n<br />";
						}
						if ($r_simula == 'false' && !$result) {
							if (strlen($resposta['erros']) < 500000) {
						    	$resposta['erros'] .= mysql_error() . ' - ' . $insertLinha . "<br />";
							} else {
								if (strlen($resposta['erros']) < 510000) {
									$resposta['erros'] .= '';
								}
							}
						    $resposta['linhasErro']++;
						} else {
							$resposta['linhasOk']++;
						}
					}
					fclose($handle);
				}
			}
		}
	break;
	
	/**
	* Salvar o arquivo com as referencias
	*/
	case 'salvar':
		$dir = "{$g['pathDados']}importacaoCSV/";
        is_dir($dir) or mkdir($dir, 0777, true);
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}

		// Procura se já existe um arquivo com a mesma Tabela e Nome do Arquivo
		$arquivoParaSalvar = '';
		$arquivos = scandir($dir);
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || $arquivo == 'importacaoCSV.serialized' || strpos($arquivo, 'serialized') === false) { continue; }
			$array = unserialize(file_get_contents($dir . $arquivo));
			if ($array['tabela'] == $r_tabela && isset($array['arquivoNome']) && $array['arquivoNome'] == file_get_contents("{$dir}importacaoCSV-ultimoArquivo.nome")) {
				$arquivoParaSalvar = $dir . $arquivo;
			}
		}
		if (empty($arquivoParaSalvar)) {
			$arquivoParaSalvar = $dir . time() . '.serialized';
		}
		$array = array(
			'tabela' => $r_tabela,
			'nome' => $r_nome,
			'arquivoNome' => file_get_contents("{$dir}importacaoCSV-ultimoArquivo.nome"),
			'arquivoCSV' => base64_encode(file_get_contents("{$dir}importacaoCSV-ultimoArquivo.csv")),
			'importacaoCSV' => $info
		);
		file_put_contents($arquivoParaSalvar, serialize($array));
	break;
	
	case 'pularLinha':
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		if (isset($info['pularLinha'])) {
			unset($info['pularLinha']);
		} else {
			$info['pularLinha'] = true;
		}
		
		file_put_contents($arquivoInfo, serialize($info));
	break;

	case 'alterarOrdem':
		$arquivoInfo = "{$dir}{$r_arquivo}";
		if (!file_exists($arquivoInfo)) {
			die('Erro - arquivo não existe.');
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		$info['ordem'] = $r_ordem;
		
		file_put_contents($arquivoInfo, serialize($info));
		die('Ok!');
	break;

	case 'excluir':
		$arquivoInfo = "{$dir}{$r_arquivo}";
		if (!file_exists($arquivoInfo)) {
			die('Erro - arquivo não existe.');
		} else {
			unlink($arquivoInfo);
		}
	break;
}
if (!isset($retornoImportar)) {
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
}
