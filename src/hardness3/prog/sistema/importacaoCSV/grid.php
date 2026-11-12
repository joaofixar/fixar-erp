<?php
namespace hardness;
	
switch($g['r'][3]){
	// GRID
	case 'sistemaPImpCSVTabelas':
		$grid = array(
			array('colunaValor' => 'Tabelas', 'colunaTitulo' => 'Tabelas'),
		);
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Tabela' => array('titulo' => '', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		$alteracoes = $g['ui']->getGridFiltroCampoBusca('Alteracoes');
		$geradoSql = '';
		$geradoDados = array();
		$sql = "SHOW tables LIKE '%".$g['ui']->getGridFiltroCampoBusca('Tabela')."%';";
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res)) {
			// Gera o campo
			if ($alteracoes == 'S' && !$g['conf']->temAlteracoes(strtoupper($row[0]))) { continue; }
			if ($alteracoes == 'N' && $g['conf']->temAlteracoes(strtoupper($row[0]))) { continue; }
			$geradoDados[] = array(strtoupper($row[0]) . ($g['conf']->temAlteracoes(strtoupper($row[0])) ? ' *' : ''));
		}
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				$('#{$g['divId']} tr').css({'font-weight': 'normal', 'font-size': '1em'});
				$(this).closest('tr').css({'font-weight': 'bold', 'font-size': '1em'});
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var str = $(this).find('td').filter( ":nth-child(2n)" ).html();
				divSetarAcaoId('{$r_divReload}', str);
				divRefresh('{$r_divReload}');
			});
			
			// Correções para deixar o filtro correto em um espaço menor
			$('#{$g['divId']} .ui-icon-help').remove();
			$('#{$g['divId']} input:eq(0)').attr('size', '13');
			$('#{$g['divId']} select:eq(0)').css('width', '69px');
			$('#{$g['divId']} .gridFiltroAvancado td').css('padding', '0');
			$('#{$g['divId']} input:eq(1)').val('F');
			$('#{$g['divId']} input:eq(2)').val('L');
		} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;
	 
	 case 'sistemaPImpCSVRecuperar':
		$grid = array(
			array('colunaValor' => 'id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'excluir', 'colunaTitulo' => '', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'ordem', 'colunaTitulo' => 'Ordem', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'arquivos', 'colunaTitulo' => 'Nome'),
			array('colunaValor' => 'tabela', 'colunaTitulo' => 'Tabela', 'chavePrimaria' => true),
			array('colunaValor' => 'file', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'time', 'colunaTitulo' => 'Data/Hora'),
		);
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		$geradoSql = '';
		$geradoDados = array();
		$arquivos = scandir("{$g['pathDados']}importacaoCSV/");
		$Opcao = $g['ui']->getGridFiltroCampoBusca('Nome');
		foreach ($arquivos as $arquivo) {
			if ($arquivo == '.' || $arquivo == '..' || $arquivo == 'importacaoCSV.serialized' || strpos($arquivo, 'serialized') === false) { continue; }
			$array = unserialize(file_get_contents("{$g['pathDados']}importacaoCSV/" . $arquivo));
			$ordem = '<input onchange="ordem(this);" type="texte" style="width: 20px;" value="' . (isset($array['ordem']) ? $array['ordem'] : '0') . '" />';
			$excluir = '<span onclick="excluir(this);" title="Excluir" class="ui-icon ui-icon-circle-close"></span>';
			$geradoDados[] = array($arquivo, $excluir, $ordem, strtoupper($array['nome']), strtoupper($array['tabela']), (isset($array['arquivoNome']) ? $array['arquivoNome'] : '-'), date("d/m/Y H:i:s", filemtime("{$g['pathDados']}importacaoCSV/" . $arquivo)));
		}
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var str = id[0].replace(/id-/, '');
				divSetarAcaoId('{$g['divIdParent']}', str, 'arquivoConf');
				var acaoId = id[1].replace(/tabela-/, '');
				divSetarAcaoId('{$g['divIdParent']}', acaoId);
				divRefresh('{$g['divIdParent']}');
				fecharJanela('{$g['divId']}');
			});

			function ordem(elem) {
				var id = $(elem).closest('tr').attr('id').split('|');
				var str = id[0].replace(/id-/, '');
				$.ajax({
					url: '/sistema/importacaoCSV/grid_func-ajax/alterarOrdem/',
					dataType: 'html',
					data: 'ajax=true&arquivo=' + str + '&ordem=' + $(elem).val(),
					success: function(data) {
						//
					}
				});
			}

			function excluir(elem) {
				var id = $(elem).closest('tr').attr('id').split('|');
				var str = id[0].replace(/id-/, '');
				var buttons = { 
					"Sim": function() { 
						$.ajax({
							url: '/sistema/importacaoCSV/grid_func-ajax/excluir/',
							dataType: 'html',
							data: 'ajax=true&arquivo=' + str + '&ordem=' + $(elem).val(),
							success: function(data) {
								$(elem).closest('tr').remove();
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm("Atenção!", "Deseja excluir este item?", buttons)
			}
		} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;
	 
	 case 'sistemaPImpCSVAcoes':
	    if (empty($r_acaoId)) {
	    	die('<div style="margin: 10px;">Selecione uma tabela.</div>');
		}
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="sistemaPImpCSVAcoesEnviar">Enviar .CSV</button>
				<!--<button type="button" id="sistemaPImpCSVAcoesTruncate">Truncate</button>-->
				<button type="button" id="sistemaPImpCSVAcoesImp">Importar</button>
				<button type="button" id="sistemaPImpCSVAcoesImpSimula">Importar (Simulação)</button>
				<button type="button" id="sistemaPImpCSVAcoesImpSalvar">Salvar Conf.</button>
				<button type="button" id="sistemaPImpCSVAcoesImpRecuperar">Recuperar Conf.</button>
				<button type="button" id="sistemaPImpCSVAcoesListarColunas">Listar Colunas s/ Associações</button>
				<button type="button" id="sistemaPImpCSVAcoesEnviarZIP">Enviar .ZIP</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#sistemaPImpCSVAcoesImpSalvar').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Salvar": function() { 
							$.getJSON('/sistema/importacaoCSV/grid_func-ajax/salvar/?ajax=true&tabela=' + encodeURIComponent('{$r_acaoId}') + '&nome=' + encodeURIComponent($('#sistemaPImpCSVAcoesImpSalvarInput').val()) + '&callback=?', function(request) {
								dialogConfirm("Ok", "Informações salvas!");
							});
							$(this).dialog("destroy").remove(); 
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); } 
					};
    				dialogConfirm("Salvar referências das colunas", "Nome: <input type='text' id='sistemaPImpCSVAcoesImpSalvarInput' size='30'>", buttons)
				});
				$('#sistemaPImpCSVAcoesEnviar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar .CSV', '/sistema/importacaoCSV/content/sistemaPImpCSVEnviar/', '', [440,120]);
				});
				$('#sistemaPImpCSVAcoesEnviarZIP').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar .ZIP', '/sistema/importacaoCSV/content/sistemaPImpCSVEnviarZIP/', '', [440,120]);
				});
				$('#sistemaPImpCSVAcoesTruncate').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Sim": function() { 
							$.getJSON('/sistema/importacaoCSV/grid_func-ajax/truncate/?ajax=true&tabela=' + encodeURIComponent('{$r_acaoId}') + '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								dialogConfirm("Ok", "Tabela limpa!");
							});
							$(this).dialog("destroy").remove(); 
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
    				dialogConfirm("Atenção!", "Este comando irá limpar todos os registros da tabela!<br/>'TRUNCATE {$r_acaoId};'<br/><br/>Deseja continuar?", buttons)
				});
				$('#sistemaPImpCSVAcoesImp').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Insert/Update": function() { 
							acaoImportar('both');
							$(this).dialog("destroy").remove(); 
						},
						"Insert": function() { 
							acaoImportar('insert');
							$(this).dialog("destroy").remove(); 
						},
						"Update": function() { 
							acaoImportar('update');
							$(this).dialog("destroy").remove(); 
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
    				dialogConfirm("Atenção!", "Este comando irá importar (gerar um INSERT ou UPDATE) para cada linha do arquivo CSV!<br/><br/>Deseja continuar?", buttons)
				});
				$('#sistemaPImpCSVAcoesImpSimula').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Insert/Update": function() { 
							acaoImportar('both', true);
							$(this).dialog("destroy").remove(); 
						},
						"Insert": function() { 
							acaoImportar('insert', true);
							$(this).dialog("destroy").remove(); 
						},
						"Update": function() { 
							acaoImportar('update', true);
							$(this).dialog("destroy").remove(); 
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
    				dialogConfirm("Atenção!", "Este comando irá simular (imprimir os SQLs) para cada linha do arquivo CSV!<br/><br/>Deseja continuar?", buttons)
				});
				$('#sistemaPImpCSVAcoesImpRecuperar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Recuperar Configuração', '/sistema/importacaoCSV/content/sistemaPImpCSVRecuperar/', '');
				});
				function acaoImportar(tipo, simula) {
					var simula = (typeof(simula) == 'undefined') ? false : simula;
					$('#{$g['divId']}').showLoading();
					$.getJSON('/sistema/importacaoCSV/grid_func-ajax/importar/?ajax=true&tabela=' + encodeURIComponent('{$r_acaoId}') + '&tipo=' + tipo + '&simula=' + simula+ '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							//dialogConfirm("Ok", "CSV Importado!<br/>Registros OK ("+request.linhasOk+") Registros ERRO ("+request.linhasErro+")<br/><br/>Insert (caso houver):<br/>" + request.logInsert + "<br/>Update (caso houver):<br/>" + request.logUpdate + "<br/>Erros (caso houver):<br/>" + request.erros);
							dialogConfirm("Ok", "CSV Importado!<br/>Registros OK ("+request.linhasOk+") Registros ERRO ("+request.linhasErro+")<br/><br/>Erros (caso houver):<br/>" + request.erros);
						} else {
							dialogConfirm("Erro", "Ocorre um erro ao tentar importar o arquivo.<br /><br />Erros:<br />" + request.erros);
						}
					});
				}
				$('#sistemaPImpCSVAcoesListarColunas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Listar Colunas sem Associações', '/sistema/importacaoCSV/grid/sistemaPImpCSVListarColunas/', '', [800,400]);
				});

				// limpa arquivoConf (utilizado para recarregar)
				divSetarAcaoId('{$g['divId']}', '', 'arquivoConf');
			} </script></div>
EOT;

		// Verifica o último .CSV enviado
		$dir = "{$g['pathDados']}importacaoCSV/";
        is_dir($dir) or mkdir($dir, 0777, true);
		$arquivo = $dir . 'importacaoCSV-ultimoArquivo.csv';
		if (!file_exists($arquivo)) {
			die('<div style="margin: 10px;">Envie um arquivo .CSV para iniciar a importação.</div>');
		}
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (isset($r_arquivoConf) && $r_arquivoConf) {
			$array = unserialize(file_get_contents($dir . $r_arquivoConf));
			file_put_contents($arquivoInfo, serialize($array['importacaoCSV']));
			if (isset($array['arquivoNome']) && $array['arquivoNome']) {
				file_put_contents($arquivo, base64_decode($array['arquivoCSV']));
				file_put_contents("{$dir}importacaoCSV-ultimoArquivo.nome", $array['arquivoNome']);
			}
		}
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}
		
		$selectArray = array(array('title' => 'NENHUM', 'value' => ''));
		$sql = "SHOW COLUMNS FROM {$r_acaoId};";
		$res = mysql_query($sql);
		$tabelas = array();
		while ($row = mysql_fetch_array($res)) {
			//$tabela = str_replace($r_acaoId . '_', '', $row[0]);
			$tabela = $row[0];
			$title = "{$tabela} ({$row[1]})";
            $title = strlen($title) > 40 ? substr($title,0,40) . " ..." : $title;
            $selectArray[] = array('title' => $title, 'value' => $row[0]);
		}
		usort($selectArray, function($a, $b) {
			if ($a['value'] == $b['value']) {
		        return 0;
		    }
		    return ($a['value'] < $b['value']) ? -1 : 1;
		});
		// Limpa campos do .serialized (caso seja enviado um segundo .csv com menos colunas)
		if (($handle = fopen($arquivo, "r")) !== FALSE) {
			$data = fgetcsv($handle, 0, ";");
		    $cols = count($data);
			foreach ($info as $key => $value) {
		    	if ($key >= $cols) {
		    		unset($info[$key]);
				}
			}
			file_put_contents($arquivoInfo, serialize($info));
		}
		
		/**
		* Filtro
		*/
		/*
		$gridFiltro = array(
			'Busca' => array('titulo' => 'Busca', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		$busca = $g['ui']->getGridFiltroCampoBusca('Busca');
		
		$checked = (isset($info['pularLinha'])) ? ' checked="checked"' : '';
		$arquivoAtual = '';
		if (file_exists("{$dir}importacaoCSV-ultimoArquivo.nome")) {
			$arquivoAtual = file_get_contents("{$dir}importacaoCSV-ultimoArquivo.nome");
		}*/
		echo <<<EOT
<div>
<span style="padding: 5px;">Tabela/Arquivo: <b>{$r_acaoId} - {$arquivoAtual}</b></span> &nbsp; <input type="checkbox" id="pularLinha" {$checked}> Ignorar primeira linha
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
$('#pularLinha').click(function() {
	$.getJSON('/sistema/importacaoCSV/grid_func-ajax/pularLinha/?ajax=true' + '&callback=?', function(request) {
		//
	});
});
} </script></div>
EOT;
		
		// Processa o .CSV
		$grid = array();
		$rows = 0;
		$geradoDados = array();
		if (($handle = fopen($arquivo, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		    	$cols = count($data);
		    	// Cria as colunas no grid
		    	if (empty($grid)) {
		    		$rowsArray = array();
		    		for ($c=0; $c < $cols; $c++) {
		    			$checked = '';
						$grid[] = array('colunaValor' => 'Coluna' . ($c+1) , 'colunaTitulo' => 'Coluna ' . ($c+1));
						$selectArrayTmp = $selectArray;
						if (isset($info[$c])) {
							foreach ($selectArrayTmp as &$value) {
								if (isset($info[$c][0]) && ($value['value'] == $info[$c][0])) {
									$value['selected'] = true;
								}
								if (isset($info[$c][1]) && $info[$c][1]) {
									$checked = ' checked="checked"';
								}
							}
						}
						$rowsArray[] = "<div style='border:1px solid #DFE5EC; border-radius:5px;padding:0px'>PK: <input type=\"checkbox\" onChange=\"campoColunaPK('{$c}', \$(this)); return false;\"{$checked} /><br /><select class=\"formInput ui-state-default ui-corner-all\" onClick=\"campoColunaFKSelect('{$c}', \$(this));\" onChange=\"campoColuna('{$c}', \$(this)); return false;\">" . gProcessaSelect($selectArrayTmp) . "</select><div class=\"fkSelect\"></div></div>";
						// <button class=\"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only\"><span class=\"ui-button-text\" style=\"padding: 1px;\">Código Callback</span></button>
					}
					$geradoDados[] = $rowsArray;
				}
		    	$rows++;
		    	if ($rows > 50) { continue; }
		    	$rowsArray = array();
		    	$temBusca = false;
		    	foreach ($data as $value) {
		    		if (!empty($busca) && stripos($value, $busca) !== false) { $temBusca = true; }
		        	$rowsArray[] = utf8_encode($value);
		    	}
		    	if (empty($busca) || $temBusca) {
		    		$geradoDados[] = $rowsArray;
				}
			}
			fclose($handle);
		}
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function campoColuna(coluna, elem) {
				$.getJSON('/sistema/importacaoCSV/grid_func-ajax/campoColuna/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&campo=' + encodeURIComponent(elem.val()) + '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
					elem.closest('div').find('.fkSelect').html('');
					campoColunaFKSelect(coluna, elem);
				});
			}
			function campoColunaPK(coluna, elem) {
				$.getJSON('/sistema/importacaoCSV/grid_func-ajax/campoColunaPK/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&pk=' + encodeURIComponent(elem.prop('checked')) + '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
					//
				});
			}
			function campoColunaFKSelect(coluna, elem) {
				// o campo seleciona é uma FK? precisamos de mais um combo para selecionar o campo
				if (elem.val() != 'Id' && (elem.val().match(/_Id$/) || elem.val().match(/_Vendedor_Interno$/))) {
					$.getJSON('/sistema/importacaoCSV/grid_func-ajax/campoColunaFKSelect/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&campo=' + encodeURIComponent(elem.val()) + '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
						// e quando alterado, chamamos o campoColunaFK
						elem.closest('div').find('.fkSelect').html(request.data);
					});
				}
			}
			function campoColunaFK(coluna, elem) {
				$.getJSON('/sistema/importacaoCSV/grid_func-ajax/campoColunaFK/?ajax=true&coluna=' + encodeURIComponent(coluna) + '&campo=' + encodeURIComponent(elem.val()) + '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
					//
				});
			}
			setTimeout(function() {
				$('select').each(function() {
					var elem = $(this);
					if (elem.val() != 'Id' && (elem.val().match(/_Id$/) || elem.val().match(/_Vendedor_Interno$/))) {
						$(this).trigger('click');
					}
				});
			}, 500);
		} </script></div>
EOT;

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;

	 case 'sistemaPImpCSVListarColunas':
		// Verifica o último .CSV enviado
		$dir = "{$g['pathDados']}importacaoCSV/";
        is_dir($dir) or mkdir($dir, 0777, true);
		$arquivo = $dir . 'importacaoCSV-ultimoArquivo.csv';
		if (!file_exists($arquivo)) {
			die('<div style="margin: 10px;">Envie um arquivo .CSV para iniciar a importação.</div>');
		}
		//$arquivoInfo = "{$dir}importacaoCSV-{$r_acaoId}.serialized";
		$arquivoInfo = "{$dir}importacaoCSV.serialized";
		if (!file_exists($arquivoInfo)) {
			$info = array();
		} else {
			$info = unserialize(file_get_contents($arquivoInfo));
		}

		// Processa o .CSV
		$grid = array();
		$grid[] = array('colunaValor' => 'Coluna', 'colunaTitulo' => 'Coluna');
		$grid[] = array('colunaValor' => 'Primeira Linha', 'colunaTitulo' => 'Primeira Linha');
		$grid[] = array('colunaValor' => 'Campo', 'colunaTitulo' => 'Coluna');
		$rows = 0;
		$geradoDados = array();
		if (($handle = fopen($arquivo, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		    	if ($rows == 0) {
					$cols = count($data);
		    		$rowsArray = array();
		    		for ($c=0; $c < $cols; $c++) {
		    			//if (isset($info[$c][0]) && !empty($info[$c][0])) { continue; }
		    			$geradoDados[] = array(($c+1), utf8_encode($data[$c]),$info[$c][0]);
					}
				}
		    	$rows++;
			}
			fclose($handle);
		}
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	 break;
}