<?php
namespace hardness;
ini_set('pcre.backtrack_limit', 100000000);
ini_set('pcre.recursion_limit', 100000000);

switch ($g['r'][3]) {
	case 'parser':
		$callback = (isset($r_callback)) ? $r_callback : false;
		$resposta = array('code' => true, 'data' => '');

		// Analizar um METODO
		if (strpos($r_acaoId, '-Metodo') !== false) {
			$r_acaoId = str_replace('-Metodo', '', $r_acaoId);
			$r_acaoId = explode('->', $r_acaoId);
			// TODO: pegar personalização
			$original = str_replace('.PHP', '.php', "bibliotecas/classes/{$r_acaoId[0]}");
			if (file_exists($original)) {
				$original = file_get_contents($original);
				preg_match_all('/function ' . $r_acaoId[1] . ' ?\((.*)(function|$)/sUu', $original, $matches);
				if (isset($matches[1]) && !empty($matches[1])) {
					$original = $matches[0][0];
				}
			} else {
				$original = '';
			}
		// Analizar um JS
		} else if (strpos($r_acaoId, 'JS|||') !== false) {
			$r_acaoId = explode('|||', $r_acaoId);
			$original = "bibliotecas/funcoesJS/{$r_acaoId[2]}.js";
			if (file_exists($original)) {
				$original = file_get_contents($original);
			} else {
				$original = '';
			}
		// Analizar um PROG (botão está dentro de um prog)
		} else {
			$botao = false;
			// Analizar um BOTAO
			if (strpos($r_acaoId, 'BOTAO|||') !== false) {
				$r_acaoId = explode('|||', $r_acaoId);
				$botao = $r_acaoId[2];
				$r_acaoId = $r_acaoId[1];
			}

			// Analizar um PROG
			$tabela = str_replace('/', '-', trim($r_acaoId, '/'));
			//$tabela = 'ven-ven001-content-ven001ContentEditarOrcamento';
			$prog = explode('-', $tabela);
			if (count($prog) > 4) {
				// Corrige o caso em que aparece o '-' dentro do prog (form_func-ajax)
				$prog = array(
					$prog[0],
					$prog[1],
					$prog[2] . '-' . $prog[3],
					$prog[4]
				);
			}
			$original = "prog/{$prog[0]}/{$prog[1]}/{$prog[2]}.php";
			if (file_exists($original)) {
				$original = file_get_contents($original);
				preg_match("/case '{$prog[3]}':(.*)break;/msUu", $original, $matches);
				if (isset($matches[1])) {
					$original = $matches[1];
				}
			} else {
				$original = '';
			}

			// Analizar um BOTAO dentro de um PROG
			if ($original && $botao) {
				/*preg_match('@\$\([\'"]#'.preg_quote($botao).'[\'"](.*?)\}\);@s', $original, $matches);
				if (isset($matches[1])) {
					$original = $matches[1];
					echo $original;
					die();
				}*/
				$srcLinhas = explode("\n", $original);
				$original = '';
				$inicioBotao = 0;
				foreach ($srcLinhas as $linha) {
					//if (strpos($linha, "\$('#{$botao}')") !== false && strpos($linha, '$g[\'divId\']') === false) { 
					if (preg_match('@\$\([\'"]#'.preg_quote($botao).'[\'"]\)@', $linha) && strpos($linha, '$g[\'divId\']') === false) { 
						$inicioBotao++;
						continue;
					} else if (preg_match('@getJSON.*function\(.*\) {@Ui', $linha)) { 
						if ($inicioBotao) {
							$inicioBotao++; 
						}
					} else if (trim($linha) == '});') { if ($inicioBotao) { $inicioBotao--; } }
					if ($inicioBotao) {
						$original .= trim($linha) . "\n";
					}
				}
			}
		}

		$resposta['data'] = identificarCodigos($original);
		
		//print_r($resposta['data']);
		//die();
				
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'debug':
		$resposta = array('code' => true, 'data' => '');

		if (file_exists("/tmp/debug-{$r_debug}")) {
			session_start();
			$resposta['data'] = "<b>Total de queries executadas: {$_SESSION['debugSqlCounter']}</b><br>".file_get_contents("/tmp/debug-{$r_debug}");
			$_SESSION['debugSqlCounter'] = 0;
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'debuglog':
		$resposta = array('code' => true, 'data' => '');
		
		if (file_exists("/tmp/debuglog-{$r_debuglog}")) {
			$resposta['data'] = file_get_contents("/tmp/debuglog-{$r_debuglog}");
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'codigoPersonalizado':
		$resposta = array('code' => true, 'grid' => '', 'filtro' => '');
		
		if (file_exists("/tmp/gridPersonalizado")) {
			$resposta['grid'] = file_get_contents("/tmp/gridPersonalizado-".$r_md5);
		}

		if (file_exists("/tmp/filtroPersonalizado")) {
			$resposta['filtro'] .= file_get_contents("/tmp/filtroPersonalizado-".$r_md5);
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'listaPersonalizacoes':
		global $g;
		$sqlC004 = mysql_query("SELECT C004_Id, SUBSTR(C004_Nome_Abreviado, 1, 20) as C004_Nome_Empresa FROM C004");
		$arrayC004 = array();
		while($resC004 = mysql_fetch_assoc($sqlC004)){
			$arrayC004[$resC004['C004_Id']] = $resC004['C004_Nome_Empresa'];
		}

		$resposta = array('code' => true, 'data' => '');
		// Tabelas
		$alteracoes = $g['ui']->getGridFiltroCampoBusca('Alteracoes');
		$sql = "SHOW tables LIKE '%".$g['ui']->getGridFiltroCampoBusca('Opcao')."%';";
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res)) {
			// Gera o campo
			//if ($alteracoes == 'S' && !$g['conf']->temAlteracoes($r_empresa . strtoupper($row[0]))) { continue; }
			//if ($alteracoes == 'N' && |$g['conf']->temAlteracoes($r_empresa . strtoupper($row[0]))) { continue; }
			if($g['conf']->temAlteracoes($r_empresa . strtoupper($row[0]))){
				$resposta['data'] .= ($i <= 0) ? "TABELAS\n" : ""; $i++;
				$resposta['data'] .= strtoupper($row[0]) . "\n";
			}
		}

		// Programas
		$arquivos = $g['modoDesenvolvedor'] ? scandir("ide/personalizacaoConf/") : scandir("{$g['pathDados']}personalizacaoConf/");
		$resposta['data'] .= (count($arquivos) > 0) ? "\nCLASSES\n" : "";
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Opcao');
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || strpos($arquivo, 'alterarMetodos.php') === false || (!empty($Opcao) && stripos($arquivo, $Opcao) === false)) { continue; }
			if (is_numeric($arquivo[0])) { 
				$classe = $arrayC004[substr($arquivo, 0,1)] . ' - ' . substr($arquivo, 1);
			} else {
				$classe = $arquivo;
			}
			$classe = str_replace('-alterarMetodos.php', '', $classe);
			$resposta['data'] .= $classe . "\n";

			$conteudo = $g['modoDesenvolvedor'] ? file_get_contents("./ide/personalizacaoConf/".$arquivo) : file_get_contents("./{$g['pathDados']}personalizacaoConf/".$arquivo);
			preg_match_all('/function ([a-zA-Z0-9_]+)/', $conteudo, $metodos);
			foreach ($metodos[1] as $metodo) {
				$resposta['data'] .= "     " . $metodo . "()\n";				
			}
		}

		// Programas
		$arquivos = $g['modoDesenvolvedor'] ? scandir("ide/personalizacaoConf/") : scandir("{$g['pathDados']}personalizacaoConf/");
		$resposta['data'] .= (count($arquivos) > 0) ? "\nPROGRAMAS\n" : "";
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Opcao');
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || strpos($arquivo, 'alterarPrograma.php') === false || (!empty($Opcao) && stripos($arquivo, $Opcao) === false)) { continue; }
			if (is_numeric($arquivo[0])) { $arquivo = substr($arquivo, 1); }
			$arquivo = str_replace('-alterarPrograma.php', '', $arquivo);
			$arquivo = str_replace('-', '/', $arquivo);
			$arquivo = "/{$arquivo}/";
			$resposta['data'] .= $arquivo . "\n";
		}

		// Callbacks
		$arquivos = $g['modoDesenvolvedor'] ? scandir("ide/personalizacaoConf/") : scandir("{$g['pathDados']}personalizacaoConf/");
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Opcao');
		$resposta['data'] .= (count($arquivos) > 0) ? "\nCALLBACKS\n" : "";
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || strpos($arquivo, 'alterarCallback.php') === false || (!empty($Opcao) && stripos($arquivo, $Opcao) === false)) { continue; }
			if (is_numeric($arquivo[0])) { $arquivo = substr($arquivo, 1); }
			$arquivo = str_replace('-alterarCallback.php', '', $arquivo);
			$arquivo = str_replace('-', '/', $arquivo);
			$arquivo = "{$arquivo}()";
			$resposta['data'] .= $arquivo . "\n";
		}

		// SQLs
		$arquivos = $g['modoDesenvolvedor'] ? scandir("ide/personalizacaoConf/") : scandir("{$g['pathDados']}personalizacaoConf/");
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Opcao');
		$resposta['data'] .= (count($arquivos) > 0) ? "\nSQL\n" : "";
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || strpos($arquivo, 'alterarSQL.php') === false || (!empty($Opcao) && stripos($arquivo, $Opcao) === false)) { continue; }
			if (is_numeric($arquivo[0])) { $arquivo = substr($arquivo, 1); }
			$arquivo = str_replace('-alterarSQL.php', '', $arquivo);
			$arquivo = str_replace('-', '/', $arquivo);
			$arquivo = "{$arquivo}";
			$resposta['data'] .= $arquivo . "\n";
		}

		// Formularios
		$arquivos = scandir("{$g['pathDados']}personalizacaoForm/");
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Opcao');
		$resposta['data'] .= (count($arquivos) > 0) ? "\nFORMULARIOS\n" : "";
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || $arquivo == '.svn' ) { continue; }
			if (is_numeric($arquivo[0])) { $arquivo = substr($arquivo, 1); }
			if(strpos($arquivo, 'formEditorData-') !== false){
				$arquivo = str_replace('formEditorData-', '', $arquivo);
				$arquivo = $arquivo . ' (Interface)';
			}
			if(strpos($arquivo, '-camposValorPadrao') !== false){
				$arquivo = str_replace('-camposValorPadrao', '', $arquivo);
				$arquivo = $arquivo . ' (Valor Padrão)';
			}
			if(strpos($arquivo, '-codigoFonte') !== false){
				$arquivo = str_replace('-codigoFonte', '', $arquivo);
				$arquivo = $arquivo . ' (Código)';
			}
			if(strpos($arquivo, '-camposTipo') !== false){
				$arquivo = str_replace('-camposTipo', '', $arquivo);
				$arquivo = $arquivo . ' (Tipo do Campo)';
			}
			if(strpos($arquivo, '-camposLabelForm') !== false){
				$arquivo = str_replace('-camposLabelForm', '', $arquivo);
				$arquivo = $arquivo . ' (Label)';
			}
			if(strpos($arquivo, '-restricaoIncluirUpdate') !== false){
				$arquivo = str_replace('-restricaoIncluirUpdate', '', $arquivo);
				$arquivo = $arquivo . ' (Restrição)';
			}
			$arquivo = preg_replace('/[\-]/', '/', $arquivo, 3);
			$arquivo = str_replace('-', '/ - ', $arquivo);
			$arquivo = "/{$arquivo}";
			$resposta['data'] .= $arquivo . "\n";
		}

		$sqlC021 = mysql_query("SELECT C021_Id, C021_Nome_Perfil FROM C021");
		$arrayC021 = array();
		while($resC021 = mysql_fetch_assoc($sqlC021)){
			$arrayC021[$resC021['C021_Id']] = $resC021['C021_Nome_Perfil'];
		}
		// Formularios
		$arquivos = scandir("{$g['pathDados']}personalizacaoGrid/");
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Opcao');
		$resposta['data'] .= (count($arquivos) > 0) ? "\nGRIDS\n" : "";
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || $arquivo == '.svn' ) { continue; }
			if(strpos($arquivo, 'modelo-') !== false){
				$arquivo = str_replace('modelo-', '', $arquivo);
				$arquivo = $arquivo . ' (Modelo)';
			}
			if(strpos($arquivo, '-gridLinha') !== false){
				$arquivo = str_replace('-gridLinha', '', $arquivo);
				$arquivo = $arquivo . ' (Cor da Linha)';
			}
			if(strpos($arquivo, '-filtros') !== false){
				$arquivo = str_replace('-filtros', '', $arquivo);
				$arquivo = $arquivo . ' (Filtro)';
			}
			if(strpos($arquivo, '-extra') !== false){
				$arquivo = str_replace('-extra', '', $arquivo);
				$arquivo = $arquivo . ' (Extra)';
			}
			if((strpos($arquivo, '-print-cabecalho') !== false) || (strpos($arquivo, '-print-colunas') !== false)){
				$arquivo = str_replace('-print-cabecalho', '', $arquivo);
				$arquivo = str_replace('-print-colunas', '', $arquivo);
				$arquivo = $arquivo . ' (Modo Impressão)';
			}
			if(strpos($arquivo, '-restricaoIncluirUpdate') !== false){
				$arquivo = str_replace('-restricaoIncluirUpdate', '', $arquivo);
				$arquivo = $arquivo . ' (Restrição)';
			}
			if(strpos($arquivo, '-js') !== false){
				$arquivo = str_replace('-js', '', $arquivo);
				$arquivo = $arquivo . ' (Javascript)';
			}
			if(strpos($arquivo, '-totais') !== false){
				$arquivo = str_replace('-totais', '', $arquivo);
				$arquivo = $arquivo . ' (Totais)';
			}
			if(strpos($arquivo, 'D140_') !== false){
				$arquivo = str_replace('D140_', '', $arquivo);
				$arquivo = $arquivo . ' (Relatório)';
			}
			$perfil = explode('-', $arquivo);
			if(is_numeric($perfil[0])){
				$arquivo = $perfil[1] . ' ('.$arrayC021[$perfil[0]].')';
			}
			$resposta['data'] .= $arquivo . "\n";
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}

/**
* Identifica callbacks, Progs e classes utilizadas
* 
* @param mixed $src
*/
function identificarCodigos($src, $nivel = 0) {
	global $r_acaoId;

	// Array de retorno
	$dados = array(
		'callbacks' => array(),
		'classes' => array(),
		'progs' => array(),
		'botoes' => array(),
		'js' => array()
	);

	/*
	* JS pesquisaGrid
	*/
	preg_match_all('#\$pesquisaGrid = array(\((?:(?>[^()]+)|(?1))*\))#msi', $src, $matches);
	if (isset($matches[0][0])) {
		foreach ($matches[0] as $value) {
			preg_match_all('#\'[^\']+\',+|\$\w#', $value, $matches2);
			if (isset($matches2[0][7])) {
				$dados['js'][] = array(trim($matches2[0][7], '\','), 'pesquisaGrid');
			}
			$src = str_replace($value, '', $src);
		}
	}
	
	/*
	* Callbacks
	*/
	preg_match_all("/(('|\")colunaTitulo('|\") ?=> ?('|\")([^'\"]+)('|\").*)?('|\")callback('|\") ?=> ?('|\")([^'\"]+)('|\")/mUu", $src, $matches);
	if (isset($matches[4])) {
		$dados['callbacks'] = $matches[10];
		$tmp = array();
		foreach ($dados['callbacks'] as $key => $value) {
			if (in_array($value, $tmp)) { continue; }
			$tmp[] = $value;
			$dados['callbacks'][$key] = array((empty($matches[5][$key]) ? $value : $matches[5][$key]), $value);
		}
	}
	
	/*
	* Classes
	*/
	//preg_match_all("/(\\\$[^\=;]+)= new ([a-z][^\(]+)\(/imsUu", $src, $matches);
	preg_match_all('#\$([a-z0-9]+)->#i', $src, $matches);
	if (isset($matches[1])) {
		$matches[1] = array_unique($matches[1]);
		foreach ($matches[1] as $key => $value) {
			if ($value == 'form') { continue; }
			$value .= '.PHP';
			$dados['classes'][$value] = (isset($dados['classes'][$value])) ? $dados['classes'][$value] : array();
			preg_match_all("/" . str_replace('/', '\/', preg_quote(trim($matches[1][$key]))) . "\-\>(.+)\(/imsUu", $src, $matches2);
			if (isset($matches2[1])) {
				$dados['classes'][$value] = array_merge($dados['classes'][$value], $matches2[1]);
			}
			$dados['classes'][$value] = array_unique($dados['classes'][$value]);
		}
	}
	// Se estivermos em um Metodo, precisamos interceptar a classe atual (THIS)
	if (isset($r_acaoId) && is_array($r_acaoId)) {
		preg_match_all("/" . str_replace('/', '\/', preg_quote('$this')) . "\-\>(.+)\(/imsUu", $src, $matches2);
		if (isset($matches2[1]) && !empty($matches2[1])) {
			$dados['classes'][$r_acaoId[0]] = array();
			$dados['classes'][$r_acaoId[0]] = array_merge($dados['classes'][$r_acaoId[0]], $matches2[1]);
		}
	}

	/*
	* Botões
	*/
	preg_match_all('@<button.*id="(.*)">(.*)</button>@U', $src, $matches);
	foreach ($matches[0] as $key => $value) {
		$dados['botoes'][] = array($matches[1][$key], $matches[2][$key]);
	}

	/*
	* Programas (que não estejam dentro de botões)
	*/
	$srcLinhas = explode("\n", $src);
	$inicioBotao = 0;
	foreach ($srcLinhas as $linha) {
		if (preg_match('@\$\([\'"]#.*[\'"]\)@Ui', $linha) && strpos($linha, '$g[\'divId\']') === false) { 
			$inicioBotao++; 
		} else if (preg_match('@getJSON.*function\(.*\) {@Ui', $linha)) { 
			if ($inicioBotao) {
				$inicioBotao++; 
			}
		} else if (trim($linha) == '});') { if ($inicioBotao) { $inicioBotao--; } }
		if (!$inicioBotao) {
			preg_match('@/(?:\w|-|_)+/(?:\w|-|_)+/(?:\w|-|_)+/(?:\w|-|_)+/@U', $linha, $matches);
			if (isset($matches[0])) {
				$dados['progs'][] = $matches[0];
			}
		}
	}
	// Tenta encontrar o nome da ação para cada chamada: botão, click de linha, etc
	$tmp = $dados['progs'];
	$dados['progs'] = array();
	foreach ($tmp as $key => $value) {
		$valueQuote = str_replace('/', '\/', preg_quote($value));
		$titulo = '';
		$subtipo = '';

		// tab
		if (!$titulo && (strpos($src, 'tab.tpl') !== false || strpos($src, 'tabCRM.tpl') !== false)) {
			// , '/cad/cad008/form/cad008form01/', 'Cadastro de Bem',
			preg_match_all('/, ?(\'|")'.$valueQuote.'(\'|"), ?(\'|")(((?!\').)*)/ims', $src, $acaoTab);
			if (isset($acaoTab[4]) && !empty($acaoTab[4])) {
				$titulo = $acaoTab[4];
				$subtipo = 'tab';
			}
		}

		// botão
		/*
		if (!$titulo) {
			// pega todos os nomes de ações JQuery (que são as ações dos botões)
			preg_match_all('/\$\((\'|")#(([a-z]|[0-9])+)(\'|")/ims', $src, $acaoJQuery);
			foreach ($acaoJQuery[2] as $acao) {
				// verifica se algum desses nomes tem esta ação
				preg_match('/\$\((\'|")#('.$acao.')(\'|")((?!}\);).)*'.$valueQuote.'/ims', $src, $acaoJQuery2);
				if (isset($acaoJQuery2[0])) {
					// verifica se este jquery tem um botão com o mesmo id
					preg_match('/id="'.$acao.'"[^>]*>([^<]*)/ims', $src, $matches);
					if (isset($matches[0])) {
						$titulo = $matches[1];
						$subtipo = 'botao';
					}
				}
			}
		}
		*/

		// click de linha
		if (!$titulo) {
			preg_match('@grid tbody tr(.*)}\);@Us', $src, $matches);
			if (isset($matches[0])) {
				$srcTr = $matches[0];
			} else {
				$srcTr = '';
			}
			preg_match('/'.$valueQuote.'/ims', $srcTr, $matches);
			if (isset($matches[0])) {
				$titulo = 'Click de Linha';
				$subtipo = 'clicklinha';
			}
		}

		// abrirJanela
		if (!$titulo) {
			// Fazer linha por linha para evitar o erro de limite de backtrack do preg
			$srcTmp = explode("\n", $src);
			foreach ($srcTmp as $srcTmpLinha) {
				preg_match('/[^\/]abrirJanela\(((?!;\)).)*(\'|")(((?!\').)*)(\'|"), ?(\'|")'.$valueQuote.'/i', $srcTmpLinha, $matches);
				if (isset($matches[3]) && !empty($matches[3])) {
					$titulo = $matches[3];
					$subtipo = 'abrirjanela';
				}
			}
		}

		// caso especial: existe um IF para montar as TAB, e as TAB são de um FORM multi-tab - vamos retirar os nomes duplicados pegos na regx
		/*if ($titulo && strpos($titulo, ',')) {
			// caso especial: existe um IF para montar as TAB, e as TAB são de um FORM multi-tab - vamos retirar os nomes duplicados pegos na regx
			$tmp = explode(',', $titulo);
			$titulo = '';
			foreach ($tmp as $value) {
				if (strpos($titulo, $value) === false) {
					$titulo .= ',' . $value;
				}
			}
			$titulo = trim($titulo, ',');
		}*/
		if (is_array($titulo)) {
			$titulo = array_unique($titulo);
		}

		$jaExiste = false;
		foreach ($dados['progs'] as $jaExisteValue) {
			if ($jaExisteValue[0] == $value) { $jaExiste = true; }
		}
		if (!$jaExiste) {
			$dados['progs'][] = array($value, $titulo, $subtipo);
		}
	}
	
	// Procurar dentro dos callbacks
	$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('bibliotecas/funcoesCallback/'));
	foreach ($iterator as $path) {
		$src = file_get_contents($path);
		foreach($dados['callbacks'] as $key => $value) {
			preg_match_all('/function ' . $value[1] . ' ?\((.*)(function|$)/sUu', $src, $matches);
			if (isset($matches[1]) && !empty($matches[1])) {
				// achou!
				if ($nivel == 0) {
					$dados['callbacks'][$key][2] = identificarCodigos($matches[1][0], 1);
				}
			}
		}
	}
	$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('prog/'));
	foreach ($iterator as $path) {
		if (strpos($path, '-callback.php') !== false) {
			$src = file_get_contents($path);
			foreach($dados['callbacks'] as $key => $value) {
				preg_match_all('/function ' . $value[1] . ' ?\((.*)(function|$)/sUu', $src, $matches);
				if (isset($matches[1]) && !empty($matches[1])) {
					// achou!
					if ($nivel == 0) {
						$dados['callbacks'][$key][2] = identificarCodigos($matches[1][0], 1);
					}
				}
			}
		}
	}
	
	return $dados;
}
?>
