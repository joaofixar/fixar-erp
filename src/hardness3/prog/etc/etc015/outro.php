<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc015CriarViaSql':
		echo <<<EOT
			<div style="margin: 10px 10px 0 10px; overflow: hidden; height: 90%;">
				<textarea id="etc015QuerySQL" style="height: 80%; width: 90%; text-transform: none;" placeholder="Digite uma Query SQL"></textarea>
				
				<div class="buttonsBottomTop">
					<button type="button" id="etc015BtnGerarGrid">Testar SQL</button>
					<button type="button" id="etc015Personalizar">Salvar/Personalizar</button>
				</div>
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc015BtnGerarGrid').unbind('click').bind('click', function(data) {
					var sql = $('#etc015QuerySQL').val();
					sql = sql.replace(/\\n/g, ' ');
					divSetarAcaoId('{$r_divReload}', sql, 'sql');
					divRefresh('{$r_divReload}');
				});
				$('#etc015Personalizar').unbind('click').bind('click', function(data) {
					var buttons = {
						"Salvar": function() { 
							// Verifica nome
							var nome = $('#etc015PersonalizarNomeGrid').val();
							$.getJSON('/etc/etc015/ajax/verificarNome/?ajax=true&nome=' + encodeURIComponent(nome) + '&callback=?', function(request) {
								if (request.code) {
									// Salvar
									$.getJSON('/etc/etc015/ajax/salvarGrid/?ajax=true&nome=' + encodeURIComponent(nome) + '&sql=' + encodeURIComponent($('#etc015QuerySQL').val()) + '&callback=?', function(request) {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Visualizar Grid', '/etc/etc015/content/etc015VerRelatorio/', '&acaoId=' + encodeURIComponent(request.data) + '&personalizar=true', [950,450]);
									});
									$(this).dialog("destroy").remove();
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    								dialogConfirm('Erro', request.data, buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); }
					};
    				dialogConfirm("Salvar/Personalizar este Grid", 'Nome único para este grid:<br /><input id="etc015PersonalizarNomeGrid" type="text" style="width: 333px;">', buttons);
				});
			} </script></div>
EOT;
	break;
	
	case 'etc015GerarGrid':
		
		// não é verificado a necessidade de alias em todos os campos (utilizado somente na criação dinamica de relatórios)
		if (isset($r_pegarColunas)) {
			$r_pegarColunas = 'ignorar';
		}
		if (isset($r_pegarHTMLColunas)) {
			$r_pegarHTMLColunas = 'ignorar';
		}
		if (!empty($r_acaoId)) {
			$sql = "SELECT * FROM D140 WHERE D140_Id = '{$r_acaoId}';";
			$res = mysql_query($sql);
			$g['D140'] = $row = mysql_fetch_assoc($res);
			$sql = $row['D140_SQL'];
			$pesquisaGridId = 'D140_' . $row['D140_Nome_Md5'];
		} else {
			$sql = $r_sql;
			$pesquisaGridId = $g['divId'];
		}
		
		// Personalização do GRID: por padrão o ID do grid é a sua propria URL (md5), aqui setamos o 'pesquisaGridId' para utilizar um ID diferente
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('pesquisaGridId', '{$pesquisaGridId}');
			} </script></div>
EOT;

		if (!empty($sql)) {
			// Troca quebras de linha e espaços extras para espaços simples
			// Retirando alterações que Thomas realizou para a tela de query do B.I que quebrou outros relatórios
			//$sql = preg_replace('/\s+/', ' ', $sql);
			/* PROBLEMA NÃO CORRIGIDO 1:
			  	Se uma subquery, nome do campo, fórmula e afins possuir mais de 256 caracters, ela será cortada automaticamente para
				caber dentro de uma chave de array php pela função mysql_fetch_assoc()
				Ex.: $row['( SELECT T001_Valor_ICMS FROM T001 [...] AND D006_Flag_Venda_Comp'] 
				O final deveria ser assim: "[...] AND D006_Flag_Venda_Compra_Outros = 'C' ORDER BY T001_Id desc LIMIT 1)"
				Isso fará com que essa coluna não esteja bem formatada, assim causando erro de syntax
			*/	
			// Fim - Retirando alterações que Thomas realizou para a tela de query do B.I que quebrou outros relatórios


			// Testar a query
			$result = mysqli_query($sql);
			if (!$result) {
			    die('<div style="margin: 10px;">Erro na Query SQL: ' . mysql_error() . '</div>');
			}
			$row = mysql_fetch_assoc($result);
			
			// Dividir a SQL em partes
			/*require_once('bibliotecas/PHP-SQL-Parser/php-sql-parser.php');
			$parser = new \PHPSQLParser($sql, true);
			print_r($parser->parsed);
			die();*/

			// FROM			
			// preg_match('/FROM.*(\(.*\) A)/Uims', $sql, $matches);
			// //print_r($matches);
			// if (isset($matches[1])) {
			// 	$from = $matches[1]; 
			// 	$extra = '';
			// } else {
			
			// Retirando alterações que Thomas realizou para a tela de query do B.I que quebrou outros relatórios
			/*preg_match_all("/(?:FROM|UPDATE|INTO)\s+([a-zA-Z0-9_]+)/i", $sql, $matches);
			log('<pre>' . print_r($matches, true) . '</pre>');

			$fromString = end($matches[0]);			
			$from = explode(' ', $fromString)[1];				
			// EXTRA
			$extra = explode($fromString, $sql);
			$extra = $extra[1];				
			// }*/
			// Fim - Retirando alterações que Thomas realizou para a tela de query do B.I que quebrou outros relatórios
			
			// From
			preg_match('/FROM.*(\(.*\) A)/Uims', $sql, $matches);
			//print_r($matches);
			if (isset($matches[1])) {
				$from = $matches[1]; 
				$extra = '';
			} else {
				$from = preg_match_all("/FROM ([a-z]|[0-9])+/ism", $sql, $matches);
				$fromString = $matches[0][count($matches[0])-1];
				$from = explode(' ', $fromString);
				$from = $from[1];
				
				// Extra
				$extra = explode($fromString, $sql);
				$extra = $extra[1];
			}						

			/**
			* Definição do Grid (Tabela)
			*/			
			$grid = array();			
			foreach ($row as $key => $value) {		
				$grid[] = array('colunaValor' => $key, 'colunaTitulo' => $key);
			}

			// Retirando alterações que Thomas realizou para a tela de query do B.I que quebrou outros relatórios
			/* PROBLEMA NÃO CORRIGIDO 2:
			  	Caso a query possua alias em alguma coluna, o colunaValor será o valor do alias e não o campo na tabela
			   	Ex.: SELECT T005_Id AS `Id Pedido` FROM T005
			   	colunaValor => 'Id Pedido' quando deveria ser 'T005_Id'
			   	O colunaTitulo não é afetado 
			//preg_match('/FROM.*(\(.*\) A)/Uims', $sql, $matches);
			// Fim - Retirando alterações que Thomas realizou para a tela de query do B.I que quebrou outros relatórios
			*/
			/**
			* Filtro
			*/
			
			$gridFiltro = array();
			if (!isset($matches[1])) { 
				$filtros = preg_split("/where/i", $sql);
				if (isset($filtros[1])) {
					preg_match_all("/(([[a-z]|[0-9])+\_([a-z]|[0-9]|\_)+)[^[a-z]|[0-9]|\_]/i", $filtros[1], $matches);
					if (isset($matches[1])) {
						foreach ($matches[1] as $value) {
							$gridFiltro[$value] = array('titulo' => $value, 'tipo' => 'texto');
						}
					}
				}
			}
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, $pesquisaGridId);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			$g['D140']['D140-Filtro_Texto'] = '';
			if (!empty($gridFiltro)) {				
				$g['D140']['D140-Filtro_Texto'] = $g['ui']->gridFiltroPrint($gridFiltro, $pesquisaGridId);
				echo $g['D140']['D140-Filtro_Texto'];
				//$g['D140']['D140-Filtro_Texto'] = preg_replace('/<span class="gridFiltroBotoes gridFiltroBotoesAdv\"\>.+\<\/span\>/imsU', '', $g['D140']['D140-Filtro_Texto']);
				$g['D140']['D140-Filtro_Texto'] = preg_replace('/\<div title=\"Busca.+\<\/div\>/imsU', '', $g['D140']['D140-Filtro_Texto']);
				$g['D140']['D140-Filtro_Texto'] = preg_replace('/\<div title=\"Limpar.+\<\/div\>/imsU', '', $g['D140']['D140-Filtro_Texto']);
				$g['D140']['D140-Filtro_Texto'] = preg_replace('/\<input/imsU', "<input disabled='disabled'", $g['D140']['D140-Filtro_Texto']);
			}
			
			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 500, $pesquisaGridId);
			/*if (isset($r_personalizar)) {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var grid = $('.content:visible:last');
						if (grid.find('.ui-tabs-panel:visible:last').size() > 0) {
							grid = grid.find('.ui-tabs-panel:visible:last');
						}
						var paramGrid = {"0": grid.data('href'), "1": grid.data('divIdRoot'), "2": grid.data('divId'), "3": '{$pesquisaGridId}'};
						gridPersonalizacao(paramGrid, true);
					} </script></div>
EOT;
			}*/
			/**
			* Geração: Grid
			*/

			$gridTabela = $g['ui']->grid($grid, $geradoDados, $pesquisaGridId);
			$g['smarty']->assign('thead', $gridTabela['thead']);
			$g['smarty']->assign('tbody', $gridTabela['tbody']);
			$g['smarty']->assign('halfElements', (count($gridTabela['tbody'])/2));
			$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			$g['smarty']->assign('gridId', $pesquisaGridId);
			$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $pesquisaGridId), JSON_FORCE_OBJECT));
			gBotaoAuditoria($from);

			if (isset($r_modoFiltro)) { 
				echo '<div style="display: none;">';
			}
			if (isset($r_generateHTML)) {
				ob_end_clean(); ob_start();
				personalizacaoTotais($pesquisaGridId);
				$g['smarty']->assign('rodapeHTML', ob_get_contents());
				if(isset($r_cabecalhoSimples)){
					$g['dataHoje'] = date('d/m/Y H:i:s');
					$tituloHTML = <<<EOT
						<table style='width:100%'>
							<tr>
								<td colspan="10" align="center">
									<span style='font-size:18px;font-weight:bold'>[[D140_Nome]] - Gerado em: {$g['dataHoje']}</span><br />
								</td>
							</tr>
							<tr>
								<td colspan="10">
									[[D140-Filtro_Texto]] 
								</td>							
							</tr>
						</table>
EOT;
					foreach ($arquivos as $arquivo) {
						if (file_exists($arquivo)) {
							$tituloHTML = file_get_contents($arquivo);
						}
					}
					if(!empty($g['c004']['C004_Logo_Marca'])){
						$pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
						$caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
					}else{
						$caminhoImagem="";
					}
					$tituloHTML = str_replace('[[logoMarca]]', $caminhoImagem, $tituloHTML);
					// faz a substituicao de todas as variaveis (grid HTML)
					if (!isset($g['D140'])) {
						$g['D140']['D140_Nome'] = 'Relatório';
						$g['D140']['D140-Filtro_Texto'] = '';
					}
					foreach ($GLOBALS as $key => $value) {
						if (is_array($value) || is_object($value) || substr($key,0,5) == 'C004_') { continue; }
						$tituloHTML = str_replace('[['.$key.']]', $value, $tituloHTML);
					}
					foreach ($GLOBALS['g'] as $key => $value) {
						if (is_array($value) || is_object($value) || substr($key,0,5) == 'C004_') { continue; }
						$tituloHTML = str_replace('[['.$key.']]', $value, $tituloHTML);
					}
					foreach ($g['C004'] as $key => $value) {
						if (is_array($value) || is_object($value)) { continue; }
						$tituloHTML = str_replace('[['.$key.']]', $value, $tituloHTML);
					}
					foreach ($g['D140'] as $key => $value) {
						if (is_array($value) || is_object($value)) { continue; }
						$tituloHTML = str_replace('[['.$key.']]', $value, $tituloHTML);
					}
					$g['smarty']->assign('tituloHTML', $tituloHTML);
				}
				if(isset($r_columns)){
					ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML-2columns.tpl');
				} else {
					ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl');
				}
			} else { 
				$g['smarty']->display('grid.tpl'); 
			}
			if (isset($r_modoFiltro) && !isset($r_generateHTML)) { 
				echo '</div>';
				/**
				* Botões
				*/
				// Definição dos botões
				$botoesDir = '';
				$botoesDir .= '<button type="submit" id="etc015Imprimir">Imprimir</button>';
				$botoesDir .= '<button type="submit" id="etc015Email">E-mail</button>';
				$botoesDir .= '<button type="submit" id="etc015Pdf">PDF</button>';
				$botoesDir .= '<button type="submit" id="etc015Csv">CSV</button>';
				//if ($g['isRoot']) {
					$botoesDir .= '<button type="submit" id="etc015Grid">Grid</button>';
				//}
				$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
				$g['smarty']->assign('botoesId', $g['divId']);
				$g['smarty']->assign('botoesDir', $botoesDir);
				$g['smarty']->display('buttons-floatBottom.tpl');
				echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	$('#etc015Imprimir').click(function() {
		divSetarAcaoId('{$g['divId']}', 'imprimir', 'executar');
		divSetarAcaoId('{$g['divId']}', 'true', 'generateHTMLTodos');
		$('#{$g['divId']} button[type=submit]:first').trigger('click');
	});
	$('#etc015Email').click(function() {
		divSetarAcaoId('{$g['divId']}', 'email', 'executar');
		divSetarAcaoId('{$g['divId']}', 'true', 'generateHTMLTodos');
		$('#{$g['divId']} button[type=submit]:first').trigger('click');
	});
	$('#etc015Csv').click(function() {
		divSetarAcaoId('{$g['divId']}', 'csv', 'executar');
		$('#{$g['divId']} button[type=submit]:first').trigger('click');
	});
	$('#etc015Pdf').click(function() {
		divSetarAcaoId('{$g['divId']}', 'pdf', 'executar');
		divSetarAcaoId('{$g['divId']}', 'true', 'generateHTMLTodos');
		$('#{$g['divId']} button[type=submit]:first').trigger('click');
	});
	$('#etc015Grid').click(function() {
		abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Grid do Relatório', '/sistema/funcoes/imprimirRelatorio/modoGrid/', '&grid=' + encodeURIComponent('{$r_grid}'), 'auto');
	});
}</script></div>
EOT;
				if (isset($r_executar) && $r_executar == 'imprimir') {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							acoesGrid($('#{$g['divId']} .acao-grid-imprimir'), false, false, true, false, false);
							//$('#{$g['divId']} .acao-grid-imprimir').trigger('click')
						}</script></div>
EOT;
				}
				if (isset($r_executar) && $r_executar == 'email') {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							acoesGrid('#{$g['divId']} .acao-grid-email', false, false, false, true, false);
							//$('#{$g['divId']} .acao-grid-email').trigger('click')
						}</script></div>
EOT;
				}
				if (isset($r_executar) && $r_executar == 'csv') {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							acoesGrid('#{$g['divId']} .acao-grid-csv', false, true, false, false, false);
							//$('#{$g['divId']} .acao-grid-csv').trigger('click')
						}</script></div>
EOT;
				}
				if (isset($r_executar) && $r_executar == 'pdf') {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							acoesGrid('#{$g['divId']} .acao-grid-pdf', false, false, false, false, false, true, true);
							//$('#{$g['divId']} .acao-grid-pdf').trigger('click')
						}</script></div>
EOT;
				}
			}
		}
	break;
}
