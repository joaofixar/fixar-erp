<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'imprimirEtiquetas':
		global $g;
		// Se receber $param : é um teste, impressão direto na tela de edição de etiquetas
		// Se receber $D138_Id: foi seleciona um modelo para imprimir
		$impQuantidadeEstoque = isset($r_impQuantidadeEstoque) ? $r_impQuantidadeEstoque : false;
		if (isset($r_param)) {
			$D138 = array(
				'D138_Flag_Editor' => 'S',
				'D138_Codigo' => $r_param
			);
		} else {
			$D138_Id = $r_D138_Id;
			$sql = "SELECT * FROM D138 WHERE D138_Id = '{$D138_Id}';";
			$res = mysql_query($sql);
			$D138 = mysql_fetch_assoc($res);
		}
		
		if ($D138['D138_Flag_Editor'] == 'S') {
			// É uma etiqueta criada pelo editor, gerar o código da etiqueta
			$param = unserialize($D138['D138_Codigo']);
			
			// Tamanho das fontes para a linguagem PPLA (número => codigo, tamanho horizontal)
			$fontPPLA = array(
				4 => array('000', 20),
				6 => array('001', 20),
				8 => array('002', 26),
				10 => array('003', 33),
				12 => array('004', 36),
				14 => array('005', 40),
				18 => array('006', 52),
				24 => array('007', 60)
			);
			
			$print = <<<EOT
	[[stx]]m
	[[stx]]c0000
	[[stx]]L
	H12
	PC
	A2
	D11
	[[linhas]]
	[[codBarra]]
	^01
	Q0001
	E
EOT;
			
			// processa as linhas (imprimi de cima para baixo, que é o inverso do que a impressora trabalha)
			$linhas = '';
			$eixoY = $param['altura'];
			for ($i = 1; $i <= 8; $i++) {
				$txt = isset($param["txt_{$i}"]) ? $param["txt_{$i}"] : '';
				$font = isset($param["font_{$i}"]) ? $param["font_{$i}"] : '';
				$font1 = !empty($font) ? $fontPPLA[$font][0] : '000';
				$font2 = !empty($font) ? $fontPPLA[$font][1] : 10;
				$space = isset($param["space_{$i}"]) ? $param["space_{$i}"] : 1;
				$eixoY -= $font2;
				if ($eixoY < 0) {
					continue;
				}
				$linha = "1911" . $font1 . str_pad($eixoY, 4, "0", STR_PAD_LEFT) . "0030" . $txt;
				$linhas .= $linha . "\n";
				$eixoY -= $space;
			}
			$print = str_replace('[[linhas]]', trim($linhas), $print);
			$codBarra = '';
			if (!empty($param['BarraTexto'])) {
				$eixoY = $param['altura'] - 100 - $param['BarraLinha'];
				$codBarra = "1E00" . '100' . str_pad($eixoY, 4, "0", STR_PAD_LEFT) . str_pad($param['BarraColuna'], 4, "0", STR_PAD_LEFT) . $param['BarraTexto'];
			}
			$print = str_replace('[[codBarra]]', trim($codBarra), $print);
		} else {
			// É um modelo
			$print = $D138['D138_Codigo'];
		}
		
		/**
		* Substituições
		*/
		$enviarParaImp = '';
		$r_impQuantidade = (!isset($r_impQuantidade) || empty($r_impQuantidade)) ? 1 : $r_impQuantidade;
		// D004
		if (!empty($r_D004_Id) && $r_D004_Id != 'undefined') {
			// Se $r_D004_Id for a string 'true', é para imprimir todas as etiquetas selecionadas no checkbox
			if ($r_D004_Id == 'true') {
				$sql = "SELECT D004_Id FROM D004 WHERE D004_Flag_Imprimir_Etiqueta = 'S'";
			} else {
				$sql = "SELECT D004_Id FROM D004 WHERE D004_Id = '{$r_D004_Id}'";
			}
			$resLoop = mysql_query($sql);
			while ($rowLoop = mysql_fetch_assoc($resLoop)) {
				$printUnidade = str_replace('[[stx]]', chr(2), $print);
				$sql = "SELECT * FROM D004 WHERE D004_Id = '{$rowLoop['D004_Id']}';";
				$res = mysql_query($sql);
				$row = mysql_fetch_assoc($res);
				foreach ($row as $key => $value) {
					$printUnidade = str_replace('[['.$key.']]', $value, $printUnidade);
					// Opção de fazer substr 'on the fly'
					preg_match_all("/\[\[".preg_quote($key)."-([0-9]+)-([0-9]+)\]\]/", $printUnidade, $matches);
					if (!empty($matches[0])) {
						foreach ($matches[0] as $match) {
							$match = trim($match, '[]');
							$match = explode('-', $match);
							$length = $match[2] - $match[1];
							$printUnidade = str_replace("[[{$match[0]}-{$match[1]}-{$match[2]}]]", substr($value, $match[1], $length), $printUnidade);
						}
					}
				}
				// Qts Copias?
				for ($i = 1; $i <= $r_impQuantidade; $i++) {
					$enviarParaImp .= "\n" . $printUnidade . "\n---------\n";
				}
			}
		// D024
		} else if (!empty($r_D024_Id) && $r_D024_Id != 'undefined') {
			// Se $r_D024_Id for a string 'true', é para imprimir todas as etiquetas selecionadas no checkbox
			if ($r_D024_Id == 'true') {
				$sql = "SELECT D024_Id FROM D024 WHERE D024_Flag_Imprimir_Etiqueta = 'S'";
			} else {
				$sql = "SELECT D024_Id FROM D024 WHERE D024_Id = '{$r_D024_Id}'";
			}
			$resLoop = mysql_query($sql);
			while ($rowLoop = mysql_fetch_assoc($resLoop)) {
				$printUnidade = str_replace('[[stx]]', chr(2), $print);
				$sql = "SELECT * FROM D024 WHERE D024_Id = '{$rowLoop['D024_Id']}';";
				$res = mysql_query($sql);
				$row = mysql_fetch_assoc($res);
				foreach ($row as $key => $value) {
					$printUnidade = str_replace('[['.$key.']]', $value, $printUnidade);
					// Opção de fazer substr 'on the fly'
					preg_match_all("/\[\[".preg_quote($key)."-([0-9]+)-([0-9]+)\]\]/", $printUnidade, $matches);
					if (!empty($matches[0])) {
						foreach ($matches[0] as $match) {
							$match = trim($match, '[]');
							$match = explode('-', $match);
							$length = $match[2] - $match[1];
							$printUnidade = str_replace("[[{$match[0]}-{$match[1]}-{$match[2]}]]", substr($value, $match[1], $length), $printUnidade);
						}
					}
				}
				// Qts Copias?
				for ($i = 1; $i <= $r_impQuantidade; $i++) {
					$enviarParaImp .= "\n" . $printUnidade . "\n---------\n";
				}
			}
		// D009
		} else if (!empty($r_D009_Id) && $r_D009_Id != 'undefined') {
			// Se $r_D009_Id for a string 'true', é para imprimir todas as etiquetas selecionadas no checkbox
			if ($r_D009_Id == 'true') {
				$sql = "SELECT D009_Id FROM D009 WHERE D009_Flag_Imprimir_Etiqueta = 'S' AND D009_C004_Id = '{$g['empresaAtual']}'";
			} else {
				$sql = "SELECT D009_Id FROM D009 WHERE D009_Id = '{$r_D009_Id}'";
			}
			$resLoop = mysql_query($sql);
			while ($rowLoop = mysql_fetch_assoc($resLoop)) {
				$printUnidade = str_replace('[[stx]]', chr(2), $print);
				$sql = <<<EOT
					SELECT 
						* 
					FROM D009 
						LEFT JOIN D049 ON D049_Id=D009_D049_Id 
						LEFT JOIN D001 ON D001_Id=D049_D001_Id 
						LEFT JOIN D037 ON D037_Id=D001_D037_Id 
						LEFT JOIN D003 ON D003_Id=D001_D003_Id 
						LEFT JOIN D060 ON D060_Id=D001_D060_Id 
						LEFT JOIN D002 ON D002_Id=D001_D002_Id						 
						LEFT JOIN D015 ON D015_Id=D001_D015_Id
						LEFT JOIN D082 ON D082_Id=D049_D082_Id
						LEFT JOIN T066 ON D009_Id=T066_D009_Id 
						LEFT JOIN D004 ON D004_Id=T066_D004_Id
					WHERE 
						D009_Id = '{$rowLoop['D009_Id']}';
EOT;
				$res = mysql_query($sql);
				$row = mysql_fetch_assoc($res);
				foreach ($row as $key => $value) {
					$printUnidade = str_replace('[['.$key.']]', $value, $printUnidade);
					// Opção de fazer substr 'on the fly'
					preg_match_all("/\[\[".preg_quote($key)."-([0-9]+)-([0-9]+)\]\]/", $printUnidade, $matches);
					if (!empty($matches[0])) {
						foreach ($matches[0] as $match) {
							$match = trim($match, '[]');
							$match = explode('-', $match);
							$length = $match[2] - $match[1];
							$printUnidade = str_replace("[[{$match[0]}-{$match[1]}-{$match[2]}]]", substr($value, $match[1], $length), $printUnidade);
						}
					}
				}
				
				/**
				* Código especifico para CRV!
				* (os produtos deles são criados com base nas linhas, grupo, sub-grupo, etc...)
				* 
				* Linha 1: D001_D003_Id + D001_D060_Id + D001_D002_Id
				* Linha 2: D001_D015_Id
				* Linha 3: D001_Descricao_Produto (menos todos os outros)
				* Linha 4: D082_Marca
				*/
				$row['D003_Nome_Linha'] = ($row['D003_Nome_Linha'] == 'EM BRANCO') ? '' : $row['D003_Nome_Linha'];
				$row['D060_Descricao_Grupo'] = ($row['D060_Descricao_Grupo'] == 'EM BRANCO') ? '' : $row['D060_Descricao_Grupo'];
				$row['D002_Descricao_Produto'] = ($row['D002_Descricao_Produto'] == 'EM BRANCO') ? '' : $row['D002_Descricao_Produto'];
				$row['D015_Nome_Grupo'] = ($row['D015_Nome_Grupo'] == 'EM BRANCO') ? '' : $row['D015_Nome_Grupo'];
				$d1 = trim($row['D003_Nome_Linha'] . ' ' . $row['D060_Descricao_Grupo'] . ' ' . $row['D002_Descricao_Produto']);
				$d2 = trim($row['D015_Nome_Grupo']);
				$d3 = trim($row['D001_Descricao_Produto']);
				$d3 = str_replace($row['D003_Nome_Linha'], '', $d3);
				$d3 = str_replace($row['D060_Descricao_Grupo'], '', $d3);
				$d3 = str_replace($row['D002_Descricao_Produto'], '', $d3);
				$d3 = str_replace($row['D015_Nome_Grupo'], '', $d3);
				$d3 = trim(preg_replace('/ +/', ' ', $d3));
				$d1 = substr($d1, 0, 25);
				$d2 = substr($d2, 0, 25);
				$d3 = substr($d3, 0, 25);
				$printUnidade = str_replace('[[CRV-DESC-1]]', $d1, $printUnidade);
				$printUnidade = str_replace('[[CRV-DESC-2]]', $d2, $printUnidade);
				$printUnidade = str_replace('[[CRV-DESC-3]]', $d3, $printUnidade);
				
				if($impQuantidadeEstoque == 'S'){
					$copias = $row['D009_Quantidade_Estoque'];
					for ($i = 1; $i <= $copias; $i++) {
						$enviarParaImp .= "\n" . $printUnidade . "\n---------\n";
					}
				} else {
					// Qts Copias?
					for ($i = 1; $i <= $r_impQuantidade; $i++) {
						$enviarParaImp .= "\n" . $printUnidade . "\n---------\n";
					}
				}
			}
		//T005
		} else if (!empty($r_T005_Id) && $r_T005_Id != 'undefined') {
			$sqlT006 = mysql_query("SELECT T006_Id, T006_Quantidade, T006_Quantidade_Etiqueta FROM T006 WHERE T006_T005_Id = '{$r_T005_Id}'");
			//Produtos do pedido
			while($result = mysql_fetch_array($sqlT006)) {
				if($result['T006_Quantidade_Etiqueta'] <= 0){
					continue;
				}
				$qtdeEtiquetas = $result['T006_Quantidade'] / $result['T006_Quantidade_Etiqueta'];
	    		$qtdeEtiquetas = floor($qtdeEtiquetas);
	    		$resto = $result['T006_Quantidade'] - ($qtdeEtiquetas * $result['T006_Quantidade_Etiqueta']);	    	
	    		$printUnidade = str_replace('[[stx]]', chr(2), $print);
	    		//Item do pedido
				$sql = "SELECT T006_Codigo_Produto,
							T006_Codigo_Substituto,
							T006_Descricao_Produto,
							D037_Unidade,
							T006_Quantidade_Itens_Etiqueta,
							T006_Quantidade_Itens_Etiqueta T006_Quantidade,
							D082_Marca,
							T006_Quantidade_Etiqueta,
							D024_Nome_Empresa, 
							D024_Nome_Fantasia
					   FROM T006
				  LEFT JOIN T005 ON T006_T005_Id=T005_Id
				  LEFT JOIN D024 ON D024_Id=T005_D024_Id
				  LEFT JOIN D037 ON D037_Id=T006_D037_Id
				  LEFT JOIN D009 ON D009_Id=T006_D009_Id
				  LEFT JOIN D049 ON D049_Id=D009_D049_Id
				  LEFT JOIN D082 ON D082_Id=D049_D082_Id
					  WHERE T006_Id = '{$result['T006_Id']}'
						AND (T006_T006_Id is null or T006_T006_Id<=0)
						AND (T006_Flag_Status!=3)
				   ORDER BY (SELECT GROUP_CONCAT(D004_Local) FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id=D009_Id),T006_D009_Id";					
				$res = mysql_query($sql);
				$arrayPedido = mysql_fetch_array($res);
				foreach ($arrayPedido as $key => $value) {				
					$printUnidade = str_replace('[['.$key.']]', $value, $printUnidade);
					// Opção de fazer substr 'on the fly'
					preg_match_all("/\[\[".preg_quote($key)."-([0-9]+)-([0-9]+)\]\]/", $printUnidade, $matches);
					if (!empty($matches[0])) {
						foreach ($matches[0] as $match) {
							$match = trim($match, '[]');
							$match = explode('-', $match);
							$length = $match[2] - $match[1];
							$printUnidade = str_replace("[[{$match[0]}-{$match[1]}-{$match[2]}]]", substr($value, $match[1], $length), $printUnidade);
						}
					}
				}
				$enviarParaImp .= $printUnidade . "\n---------\n";
				//Repete as etiquetas com a mesma quantidade
				for ($i = 1; $i < $qtdeEtiquetas; $i++) {					
					$enviarParaImp .= "\n" . $printUnidade . "\n---------\n";					
				}
				//Caso haja resto, imprime uma etiqueta com a diferença
				if($resto > 0){
					$printUnidade = str_replace('[[stx]]', chr(2), $print);
					foreach ($arrayPedido as $key => $value) {
						$value = ($key == 'T006_Quantidade_Etiqueta') ? number_format($resto, 3, '.', '') : $value;
						$printUnidade = str_replace('[['.$key.']]', $value, $printUnidade);
						// Opção de fazer substr 'on the fly'
						preg_match_all("/\[\[".preg_quote($key)."-([0-9]+)-([0-9]+)\]\]/", $printUnidade, $matches);
						if (!empty($matches[0])) {
							foreach ($matches[0] as $match) {
								$match = trim($match, '[]');
								$match = explode('-', $match);
								$length = $match[2] - $match[1];
								$printUnidade = str_replace("[[{$match[0]}-{$match[1]}-{$match[2]}]]", substr($value, $match[1], $length), $printUnidade);
							}
						}
					}
					$enviarParaImp .= $printUnidade . "\n---------\n";
				}								
			}
		} else {
			$print = str_replace('[[stx]]', chr(2), $print);
			// Qts Copias?
			for ($i = 1; $i <= $r_impQuantidade; $i++) {
				$enviarParaImp .= "\n" . $print . "\n---------\n";
			}
		}
		// send to printer
		$array = explode('---------', $enviarParaImp);
		$enviarParaImp = '';
		foreach ($array as $value) {
			//$value = trim($value);
			$tmp = str_replace("\n", '', $value);
			if (empty($tmp)) { continue; }
			$enviarParaImp .= (!empty($enviarParaImp)) ? "---------\n" : '';
			$enviarParaImp .= "{$value}\n";
		}
		//$enviarParaImp = "\n" . $enviarParaImp . "\n";
		$file = '/tmp/cad051ImpressaoEtiquetas'.$confUsuario['dbDatabase'].'.txt';
		file_put_contents($file, $enviarParaImp);
		$etiquetasMetodoDeImpressao = $g['conf']->ini('etiquetasMetodoDeImpressao');
		if ($etiquetasMetodoDeImpressao[0] == 'windows') {
			$resposta['data'] = 'impressaoArquivo-' . base64_encode($file);
		} else {
			$resposta['code'] = false;
			eval("echo `lpr -P{$etiquetasMetodoDeImpressao[1]} {$file}`;");
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
