<?php
namespace hardness;
/**
* Configurações
*/
set_time_limit(0);
$baseAtual = isset($_COOKIE['baseAtual']) ? $_COOKIE['baseAtual'] : 'K1';
//$baseAtual = 'sigmacar';
$bancoDestino = 'dev_e211';	// somente utilizado para listar as tabelas, campos e data

/**
* Origem
*/
$baseAtual = trim($baseAtual);
if ($baseAtual == 'eurosul') {
	// Eurosul
	$db = '/home/flycker/html/hardness3/prog/sistema/progs/db.eurosul.serialized';
	$bancoOrigem = 'ibase'; // ibase (Firebird/Interbase)
	$dbproc = ibase_connect("localhost:/var/lib/firebird/2.5/data/eurosul.fdb", "SYSDBA", "root");
	$bancoDestinoConversao = 'dev_e211';
	$updateFile = $confUsuario['pathRaiz'] . 'prog/sistema/progs/db.eurosul.update.php';
} if ($baseAtual == 'sigmacar') {
	// Sigmacar
	$db = '/home/flycker/html/hardness3/prog/sistema/progs/db.sigmacar.serialized';
	$bancoOrigem = 'sybase'; // sybase (SQL Server)
	$dbproc = sybase_connect("192.168.38.146:1433", 'sa', "root");
	$bancoSybase = 'Sigma';
	sybase_select_db($bancoSybase, $dbproc);
	$bancoDestinoConversao = 'dev_e212';
	$updateFile = $confUsuario['pathRaiz'] . 'prog/sistema/progs/db.sigmacar.update.php';
} else if ($baseAtual == 'K1' || $baseAtual == 'K2' || $baseAtual == 'K3' || $baseAtual == 'K4') {
	// K1
	$db = '/home/flycker/html/hardness3/prog/sistema/progs/db.koisas.serialized';
	$bancoOrigem = 'ibase'; // ibase (Firebird/Interbase)
	$dbproc = ibase_connect("localhost:/var/lib/firebird/2.5/data/{$baseAtual}.fdb", "SYSDBA", "root");
	$bancoDestinoConversao = 'dev_e216';
	$updateFile = $confUsuario['pathRaiz'] . 'prog/sistema/progs/db.koisas.update.php';
} else if ($baseAtual == 'fixar') {
	// Eurosul
	$db = '/home/flycker/html/hardness3/prog/sistema/progs/db.fixar.serialized';
	$bancoOrigem = 'ibase'; // ibase (Firebird/Interbase)
	$dbproc = ibase_connect("localhost:/var/lib/firebird/2.5/data/fixar.fdb", "SYSDBA", "root");
	$bancoDestinoConversao = 'dev_e218';
	$updateFile = $confUsuario['pathRaiz'] . 'prog/sistema/progs/db.fixar.update.php';
} else if ($baseAtual == 'azpr') {
	// Eurosul
	$db = '/home/flycker/html/hardness3/prog/sistema/progs/db.azpr.serialized';
	$bancoOrigem = 'ibase'; // ibase (Firebird/Interbase)
	$dbproc = ibase_connect("localhost:/var/lib/firebird/2.5/data/azpr.fdb", "SYSDBA", "root");
	$bancoDestinoConversao = 'dev_e218';
	$updateFile = $confUsuario['pathRaiz'] . 'prog/sistema/progs/db.azpr.update.php';
}
/*} if ($baseAtual == 'fixar') {
	// Sigmacar
	$db = '/home/flycker/html/hardness3/prog/sistema/progs/db.fixar.serialized';
	$bancoOrigem = 'sybase'; // sybase (SQL Server)
	$dbproc = sybase_connect("192.168.38.36:49811", "root", "root");
	$bancoSybase = 'fixar';
	sybase_select_db($bancoSybase, $dbproc);
	$bancoDestinoConversao = 'dev_e218';
	$updateFile = $confUsuario['pathRaiz'] . 'prog/sistema/progs/db.fixar.update.php';
}*/
$bancoDestino = $bancoDestinoConversao;

/**
* FIM Configurações
*/

// Muda banco destino (para listar dados)
$queryUse = mysqli_query("USE {$bancoDestino};");
if (!$queryUse) { die('Invalid query: ' . mysql_error()); }

// Array de relacionamentos (gravado no disco serializado)
if (!file_exists($db)) { $h = fopen($db, 'w'); fwrite($h, serialize(array())); fclose($h); }
$relArray = unserialize(file_get_contents($db));

// Cookies
$c_table = isset($_COOKIE['grid_atual_tabela']) ? $_COOKIE['grid_atual_tabela'] : 'empty';
$c_field = isset($_COOKIE['grid_atual_campo']) ? $_COOKIE['grid_atual_campo'] : '---';
$c_table_outro = isset($_COOKIE['grid_outro_tabela']) ? $_COOKIE['grid_outro_tabela'] : 'empty';
$c_field_outro = isset($_COOKIE['grid_outro_campo']) ? $_COOKIE['grid_outro_campo'] : '---';
$c_filtro_tabela = isset($_COOKIE['filtro_tabela']) ? $_COOKIE['filtro_tabela'] : '';
$c_filtro_campo = isset($_COOKIE['filtro_campo']) ? $_COOKIE['filtro_campo'] : '';

// Ações
isset($r_action) or ($r_action = null);
if ($r_action) {
	switch ($g['r'][3]) {
		case 'grid_atual':
			// Campos nas Tabelas
			$grid_atual_desc = array();
			$request = mysqli_query("DESC {$c_table};");
			while ($row = mysql_fetch_assoc($request)) {
				$grid_atual_desc[$row['Field']] = $row['Type'];
			}
			if (isset($grid_atual_desc[$c_field])) {
				$val = $grid_atual_desc[$c_field];
				$grid_atual_desc = array();
				$grid_atual_desc[$c_field] = $val;
			}
		
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => $c_table, 'colunaTitulo' => 'Tabela', 'colunaTipo' => 'livre')
			);
			foreach ($grid_atual_desc as $key => $value) {
				$grid[] = array(
					'colunaValor' => $key,
					'colunaTitulo' => $key
				);
			}
				
			/**
			* Definição da SQL
			*/
			$from = $c_table;
			
			$extra = <<<EOT
			ORDER BY {$c_field} DESC
EOT;

			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
			/**
			 * Geração: Grid
			 */
			 $gridTabela = $g['ui']->grid($grid, $geradoDados);
			 foreach ($gridTabela['tbody'] as &$value) {
			 	$value['dados'][0] = $c_table;
			 }
	         $g['smarty']->assign('thead', $gridTabela['thead']);
	         $g['smarty']->assign('tbody', $gridTabela['tbody']);
	         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			 $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	         $g['smarty']->assign('gridTable', '');
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	         
	         if (empty($dados)) {
				echo "Tabela ({$c_table}) não contém resultados.";
			}
		
		break;
		
		case 'table_fields':
			
			// Campos nas Tabelas
			$tables = array();
			$request = mysqli_query("SHOW TABLES;");
			while ($row = mysql_fetch_row($request)) {
				$tables[] = $row[0];
			}
			$count = 0;
			$tables_fields = array();
			foreach ($tables as $table) {
				if (!empty($c_filtro_tabela) && stripos($table, $c_filtro_tabela) === false) { continue; }
				// Campos nas Tabelas
				$request = mysqli_query("DESC {$table};");
				while ($row = mysql_fetch_assoc($request)) {
					if (!empty($c_filtro_campo) && stripos($row['Field'], $c_filtro_campo) === false) { continue; }
					$count++;
					if ($count == 300) {
						$tables_fields[] = array(
							'table' => '',
							'field' => 'Mostrando apenas 300 registros.',
							'type' => ''
						);
					} else if ($count > 300) {
						continue 2;
					} else {
						$tables_fields[] = array(
							'table' => $table,
							'field' => $row['Field'],
							'type' => $row['Type']
						);
					}
				}
			}
		
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "Tabela", 'colunaTitulo' => 'Tabela', 'chavePrimaria' => true),
				array('colunaValor' => "Campo", 'colunaTitulo' => 'Campo', 'chavePrimaria' => true),
				array('colunaValor' => "Tipo", 'colunaTitulo' => 'Tipo')
			);
				
			/**
			* Definição da SQL
			*/
			$from = $c_table;
			
			$extra = <<<EOT
EOT;

			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			$dados = array();
			foreach ($tables_fields as $value) {
				$dados[] = array($value['table'], $value['field'], $value['type']);
			}
			
			/**
			 * Geração: Grid
			 */
			 $gridTabela = $g['ui']->grid($grid, $dados);
			 foreach ($gridTabela['tbody'] as &$value) {
			 	if (@is_array($relArray['destino'][$value['dados'][0]][$value['dados'][1]]) && !empty($relArray['destino'][$value['dados'][0]][$value['dados'][1]])) {
			 		isset($relArray['destino'][$value['dados'][0]][$value['dados'][1]][0]['type']) or ($relArray['destino'][$value['dados'][0]][$value['dados'][1]][0]['type'] = 'cr1');
			 		if ($relArray['destino'][$value['dados'][0]][$value['dados'][1]][0]['type'] == 'cr1') {
			 			$color = 'green';
			 			$word = 'OK';
					} else if ($relArray['destino'][$value['dados'][0]][$value['dados'][1]][0]['type'] == 'cr2') {
						$color = 'red';
						$word = 'TMP';
					} else {
						$color = 'gray';
						$word = 'NO';
					}
					$extra = '';
					if (count($relArray['destino'][$value['dados'][0]][$value['dados'][1]]) > 1) {
						$extra = '<span style="font-size: 13px; font-weight: bold; color: var(--BGVermelho);">(possui mais de um relacionamento!)</span>';
					}
					$value['dados'][1] = '<span style="font-weight: bold; color: '.$color.';">***'.$word.'*** ' . $value['dados'][1] . '</span> ' . $extra;
				}
			 	@$value['dados'][0] = '<a href="#">(+)</a> ' . @$value['dados'][0];
			 }
	         $g['smarty']->assign('thead', $gridTabela['thead']);
	         $g['smarty']->assign('tbody', $gridTabela['tbody']);
	         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			 $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	         $g['smarty']->assign('gridTable', '');
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	         
	         echo <<<EOT
	<script type="text/javascript">
		$('#table_fields').find('tr').unbind('click').unbind('click').bind('click', function(data) {
			var id = $(this).closest('tr').attr('id').split('|');
			var tabela = id[0].replace('Tabela-', '');
			var campo = id[1].replace('Campo-', '');
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'SPAN') {  
				campo = '';
			}
			$.ajax({
				url: '/sistema/progs/db/grid_atual_filtro/',
				dataType: 'html',
				data: 'ajax=true&action=true&tabela=' + encodeURIComponent(tabela) + '&campo=' + encodeURIComponent(campo),
				success: function(data) {
					divRefresh('grid_atual');
					divRefresh('relacionamento');
					return false;
				}
			});
			return false;
		});
	</script>
EOT;
		break;
		
		case 'filtro':
			isset($r_tabela) or ($r_tabela = '');
			isset($r_campo) or ($r_campo = '');
			setcookie('filtro_tabela', $r_tabela, time()+3600*30, '/');
			setcookie('filtro_campo', $r_campo, time()+3600*30, '/');
		break;
		
		case 'grid_atual_filtro':
			isset($r_tabela) or ($r_tabela = '');
			isset($r_campo) or ($r_campo = '');
			setcookie('grid_atual_tabela', $r_tabela, time()+3600*30, '/');
			setcookie('grid_atual_campo', $r_campo, time()+3600*30, '/');
		break;
		
		case 'grid_outro_filtro_tabela':
			isset($r_tabela) or ($r_tabela = '');
			setcookie('grid_outro_tabela', $r_tabela, time()+3600*30, '/');
			setcookie('grid_outro_campo', '', time()+3600*30, '/');
		break;
		
		case 'grid_outro_filtro_campo':
			isset($r_campo) or ($r_campo = '');
			setcookie('grid_outro_campo', $r_tabela, time()+3600*30, '/');
		break;
		
		case 'changebaseAtual':
			isset($r_base) or ($r_base = '');
			setcookie('baseAtual', $r_base, time()+3600*30, '/');
		break;
		
		case 'grid_outro_tabelas':
			// Campos nas Tabelas
			$tables = array();
			if ($bancoOrigem == 'sybase') {
				$request = sybase_query("select name from [{$bancoSybase}]..sysobjects where xtype = 'U' order by name;", $dbproc);
				while ($row = sybase_fetch_row($request)) {
					$tables[$row[0]] = 0;
				}
			} else {
				$request = ibase_query($dbproc, 'select rdb$relation_name from rdb$relations where rdb$view_blr is null and (rdb$system_flag is null or rdb$system_flag = 0);');
				while ($row = ibase_fetch_row($request)) {
					$row[0] = trim($row[0]);
					$tables[$row[0]] = 0;
				}
			}
			$tables_fields = array();
			$table_tmp = array();
			foreach ($tables as $table => &$count) {
				// Campos nas Tabelas
				//$request = mysql_query("sp_help cad_Cep {$table};");
				if ($bancoOrigem == 'sybase') {
					$request = sybase_query("SELECT count(*) FROM {$table};");
					$row = sybase_fetch_row($request);
				} else {
					$request = ibase_query("SELECT count(*) FROM {$table};");
					$row = ibase_fetch_row($request);
				}
				$count = trim($row[0]);
				if ($count == 0) { $table_tmp[$table] = $count; unset($tables[$table]); }
			}
			unset($count);
			ksort($tables);
			ksort($table_tmp);
			$tables += $table_tmp;
		
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "TabelaOld", 'colunaTitulo' => 'Tabela', 'chavePrimaria' => true),
				array('colunaValor' => "Count", 'colunaTitulo' => 'count(*)'),
				array('colunaValor' => "Comment", 'colunaTitulo' => 'Comentário')
			);
			
			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			$dados = array();
			foreach ($tables as $table => $count) {
				$dados[] = array($table, $count, '<input type="text" class="campoComment" onblur="updateComment(\'' . $table . '\', this);" value="' . @$relArray['origemComments'][$table] . '" style="width: 500px;">');
			}
			
			/**
			 * Geração: Grid
			 */
			$gridTabela = $g['ui']->grid($grid, $dados);
			foreach ($gridTabela['tbody'] as &$value) {
				// remove registro vazios
				if (@is_array($relArray['origem'][$value['dados'][0]]) && !empty($relArray['origem'][$value['dados'][0]])) {
					foreach ($relArray['origem'][$value['dados'][0]] as $key__ => $value__) {
						if (empty($value__)) {
							unset($relArray['origem'][$value['dados'][0]][$key__]);
						}
					}
				}
				if (@is_array($relArray['origem'][$value['dados'][0]]) && !empty($relArray['origem'][$value['dados'][0]])) {
					$value['dados'][0] = '<span style="font-weight: bold; color: var(--BGVermelho);">*** ('.count($relArray['origem'][$value['dados'][0]]).') ***</span> ' . $value['dados'][0];
				}
			}
			
			// Super Ordenação! :)
			function funOrdTableOrigem($a, $b) {
				if (!isset($b['dados']) || !isset($a['dados'])) {
					return 0;
				}
			
				if (strpos($a['dados'][0], '***') !== false && !strpos($b['dados'][0], '***') !== false) {
					return -1;
				} else if (!strpos($a['dados'][0], '***') !== false && strpos($b['dados'][0], '***') !== false) {
					return 1;
				} else if ($a['dados'][1] < $b['dados'][1]) {
					return 1;         
				} else if ($a['dados'][1] > $b['dados'][1]) {
					return -1;
				} else {
					return 0;
				}
			}
			usort($gridTabela['tbody'], 'hardness\funOrdTableOrigem');
			function funOrdTableOrigemComment($a, $b) {
				preg_match('/ value="(.*)" style/Uis', $a['dados'][2], $aMatches, PREG_OFFSET_CAPTURE);
				preg_match('/ value="(.*)" style/Uis', $b['dados'][2], $bMatches, PREG_OFFSET_CAPTURE);
				$strCmp = strcmp($aMatches[1][0], $bMatches[1][0]);
				return $strCmp;
			}
			$array1 = array();
			foreach ($gridTabela['tbody'] as $value) {
				if (!isset($value['dados'])) { continue; }
				if (strpos($value['dados'][0], '***') !== false) { $array1[] = $value; }
			}
			$array2 = array();
			foreach ($gridTabela['tbody'] as $value) {
				if (!isset($value['dados'])) { continue; }
				if (strpos($value['dados'][0], '***') === false) { $array2[] = $value; }
			}
			usort($array1, 'hardness\funOrdTableOrigemComment');			
			$gridTabela['tbody'] = array_merge($array1, $array2);
			
	        $g['smarty']->assign('thead', $gridTabela['thead']);
	        $g['smarty']->assign('tbody', $gridTabela['tbody']);
	        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	        $g['smarty']->assign('gridTable', '');
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	        
	        echo <<<EOT
	<script type="text/javascript">
		$('#grid_outro_tabelas').find('tr').unbind('click').unbind('click').bind('click', function() {
			var id = $(this).closest('tr').attr('id').replace('TabelaOld-', '');
			$.ajax({
				url: '/sistema/progs/db/grid_outro_filtro_tabela/',
				dataType: 'html',
				data: 'ajax=true&action=true&tabela=' + encodeURIComponent(id),
				success: function(data) {
					divRefresh('grid_outro_campos');
					divRefresh('grid_outro');
					divRefresh('relacionamento');
					return false;
				}
			});
			return false;
		});
		$('#{$g['divId']} .grid .campoComment').unbind('click').bind('click', function(data) {
				return false;
			});
	</script>
EOT;
		break;
		break;
		
		case 'grid_outro':
			// Campos nas Tabelas
			$grid_outro_desc = array();
			if ($bancoOrigem == 'sybase') {
				$request = sybase_query("sp_columns {$c_table_outro};");
				while ($row = sybase_fetch_assoc($request)) {
					$grid_outro_desc[$row['COLUMN_NAME']] = $row['TYPE_NAME'];
				}
			} else {
				$sql = <<<EOT
				SELECT

RDB\$RELATION_FIELDS.RDB\$FIELD_NAME AS COLUMN_NAME,
RDB\$TYPES.RDB\$TYPE_NAME AS DATA_TYPE

FROM RDB\$RELATIONS
INNER JOIN RDB\$RELATION_FIELDS ON RDB\$RELATIONS.RDB\$RELATION_NAME = RDB\$RELATION_FIELDS.RDB\$RELATION_NAME
LEFT JOIN RDB\$FIELDS ON RDB\$RELATION_FIELDS.RDB\$FIELD_SOURCE = RDB\$FIELDS.RDB\$FIELD_NAME
LEFT JOIN RDB\$TYPES ON RDB\$FIELDS.RDB\$FIELD_TYPE = RDB\$TYPES.RDB\$TYPE

WHERE RDB\$RELATIONS.RDB\$RELATION_NAME = '{$c_table_outro}'
AND RDB\$RELATIONS.RDB\$SYSTEM_FLAG = 0
AND RDB\$TYPES.RDB\$FIELD_NAME='RDB\$FIELD_TYPE'

ORDER BY RDB\$RELATION_FIELDS.RDB\$FIELD_POSITION;
EOT;
				$request = ibase_query($sql);
				while ($row = ibase_fetch_assoc($request)) {
					$grid_outro_desc[trim($row['COLUMN_NAME'])] = trim($row['DATA_TYPE']);
				}
			}
			if (isset($grid_outro_desc[$c_field_outro])) {
				$val = $grid_outro_desc[$c_field_outro];
				$grid_outro_desc = array();
				$grid_outro_desc[$c_field_outro] = $val;
			}
			
			// CSV
			$file = $g['pathDados'] . 'tmpImportacaoCSV.csv';
			$fp = fopen($file, 'w');
			$csv = array();
		
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => $c_table, 'colunaTitulo' => 'Tabela', 'colunaTipo' => 'livre')
			);
			foreach ($grid_outro_desc as $key => $value) {
				$csv[] = $key;
				$grid[] = array(
					'colunaValor' => $key,
					'colunaTitulo' => $key
				);
			}
			fputcsv($fp, $csv, ';');

			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			$dados = array();
			if ($bancoOrigem == 'sybase') {
				if (isset($grid_outro_desc[$c_field_outro])) {
					$request = sybase_query("SELECT TOP 300 {$c_field_outro} FROM {$c_table_outro} ORDER BY {$c_field_outro} DESC;");
				} else {
					$request = sybase_query("SELECT TOP 10 * FROM {$c_table_outro};");
				}
				while ($row = sybase_fetch_row($request)) {
					$dados[] = $row;
				}
				// CSV
				$request = sybase_query("SELECT * FROM {$c_table_outro};");
				while ($row = sybase_fetch_row($request)) {
					fputcsv($fp, $row, ';');
				}
			} else {
				if (isset($grid_outro_desc[$c_field_outro])) {
					$request = ibase_query("SELECT FIRST 300 {$c_field_outro} FROM {$c_table_outro} ORDER BY {$c_field_outro} DESC;");
				} else {
					$request = ibase_query("SELECT FIRST 10 * FROM {$c_table_outro};");
				}
				while ($row = ibase_fetch_row($request)) {
					$dados[] = $row;
				}
				// CSV
				$request = ibase_query("SELECT * FROM {$c_table_outro};");
				while ($row = ibase_fetch_row($request)) {
					fputcsv($fp, $row, ';');
				}
			}
			
			// CSV
			echo <<<EOT
				<script type="text/javascript">
					$('.gridOptTop').remove();
				</script>
EOT;
			
			/**
			 * Geração: Grid
			 */
			 $gridTabela = $g['ui']->grid($grid, $dados);
			 foreach ($gridTabela['tbody'] as &$value) {
			 	$value['dados'][0] = $c_table_outro;
			 }
	         $g['smarty']->assign('thead', $gridTabela['thead']);
	         $g['smarty']->assign('tbody', $gridTabela['tbody']);
	         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			 $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	         $g['smarty']->assign('gridTable', '');
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
		break;
		
		case 'grid_outro_campos':		
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => $c_table_outro, 'colunaTitulo' => 'Tabela', 'colunaTipo' => 'livre'),
				array('colunaValor' => "Campo", 'colunaTitulo' => 'Campo', 'chavePrimaria' => true),
				array('colunaValor' => "Tipo", 'colunaTitulo' => 'Tipo'),
				array('colunaValor' => "Percent", 'colunaTitulo' => '%'),
			);

			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			$dados = array();
			if ($bancoOrigem == 'sybase') {
				// %
				$percent = array();
				$request = sybase_query("SELECT TOP 1000 * FROM {$c_table_outro};");
				while ($row = sybase_fetch_assoc($request)) {
					foreach ($row as $key => $value) {
						$value = trim($value);
						if (!isset($percent[$key])) {
							$percent[$key] = array(0 => 0, 1 => 0);
						}
						if (empty($value)) {
							$percent[$key][0]++;
						} else {
							$percent[$key][1]++;
						}
					}
				}
				// Campos
				$request = sybase_query("sp_columns {$c_table_outro};");
				while ($row = sybase_fetch_assoc($request)) {
					if ($percent[$row['COLUMN_NAME']][1] == 0) {
						$per = 0;
					} else {
						$per = round(($percent[$row['COLUMN_NAME']][1] * 100) / 1000, 2);
					}
					$old = $per;
					if ($per <= 20) { $per = '<span style="font-weight: bold; color: var(--BGVermelho);">' . $per . '%</span>'; }
					elseif ($per >= 80) { $per = '<span style="font-weight: bold; color: blue;">' . $per . '%</span>'; }
					else { $per = $per . '%'; }
					$dados[] = array($row['COLUMN_NAME'], $row['TYPE_NAME'], $per, $old);
				}
			} else {
				// %
				$percent = array();
				$request = ibase_query("SELECT FIRST 1000 * FROM {$c_table_outro};");
				while ($row = ibase_fetch_assoc($request)) {
					foreach ($row as $key => $value) {
						$value = trim($value);
						$key = trim($key);
						if (!isset($percent[$key])) {
							$percent[$key] = array(0 => 0, 1 => 0);
						}
						if (empty($value)) {
							$percent[$key][0]++;
						} else {
							$percent[$key][1]++;
						}
					}
				}
				// Campos
				$sql = <<<EOT
				SELECT

RDB\$RELATION_FIELDS.RDB\$FIELD_NAME AS COLUMN_NAME,
RDB\$TYPES.RDB\$TYPE_NAME AS DATA_TYPE

FROM RDB\$RELATIONS
INNER JOIN RDB\$RELATION_FIELDS ON RDB\$RELATIONS.RDB\$RELATION_NAME = RDB\$RELATION_FIELDS.RDB\$RELATION_NAME
LEFT JOIN RDB\$FIELDS ON RDB\$RELATION_FIELDS.RDB\$FIELD_SOURCE = RDB\$FIELDS.RDB\$FIELD_NAME
LEFT JOIN RDB\$TYPES ON RDB\$FIELDS.RDB\$FIELD_TYPE = RDB\$TYPES.RDB\$TYPE

WHERE RDB\$RELATIONS.RDB\$RELATION_NAME = '{$c_table_outro}'
AND RDB\$RELATIONS.RDB\$SYSTEM_FLAG = 0
AND RDB\$TYPES.RDB\$FIELD_NAME='RDB\$FIELD_TYPE'

ORDER BY RDB\$RELATION_FIELDS.RDB\$FIELD_POSITION;
EOT;
				$request = ibase_query($sql);
				while ($row = ibase_fetch_assoc($request)) {
					$row['COLUMN_NAME'] = trim($row['COLUMN_NAME']);
					if ($percent[$row['COLUMN_NAME']][1] == 0) {
						$per = 0;
					} else {
						$per = round(($percent[$row['COLUMN_NAME']][1] * 100) / 1000, 2);
					}
					$old = $per;
					if ($per <= 20) { $per = '<span style="font-weight: bold; color: var(--BGVermelho);">' . $per . '%</span>'; }
					elseif ($per >= 80) { $per = '<span style="font-weight: bold; color: blue;">' . $per . '%</span>'; }
					else { $per = $per . '%'; }
					$dados[] = array(trim($row['COLUMN_NAME']), trim($row['DATA_TYPE']), $per, $old);
				}
			}
			function mySortFunc($a, $b) {
				if ($a[3] == $b[3]) {
    				return 0;
    			}
				return($a[3] > $b[3] ? -1 : 1);
			}
			usort($dados, 'hardness\mySortFunc');
			
			/**
			 * Geração: Grid
			 */
			 $gridTabela = $g['ui']->grid($grid, $dados);
			 foreach ($gridTabela['tbody'] as &$value) {
			 	if (@is_array($relArray['origem'][$c_table_outro][$value['dados'][1]]) && !empty($relArray['origem'][$c_table_outro][$value['dados'][1]])) {
			 		isset($relArray['origem'][$c_table_outro][$value['dados'][1]][0]['type']) or ($relArray['origem'][$c_table_outro][$value['dados'][1]][0]['type'] = 'cr1');
			 		if ($relArray['origem'][$c_table_outro][$value['dados'][1]][0]['type'] == 'cr1') {
			 			$color = 'green';
			 			$word = 'OK';
					} else if ($relArray['origem'][$c_table_outro][$value['dados'][1]][0]['type'] == 'cr2') {
						$color = 'red';
						$word = 'TMP';
					} else {
						$color = 'gray';
						$word = 'NO';
					}
					$extra = '';
					if (count($relArray['origem'][$c_table_outro][$value['dados'][1]]) > 1) {
						$extra = '<span style="font-size: 13px; font-weight: bold; color: var(--BGVermelho);">(possui mais de um relacionamento!)</span>';
					}
					$value['dados'][1] = '<span style="font-weight: bold; color: '.$color.';">***'.$word.'*** ' . $value['dados'][1] . '</span> ' . $extra;
				}
			 	$value['dados'][0] = $c_table_outro;
			 }
	         $g['smarty']->assign('thead', $gridTabela['thead']);
	         $g['smarty']->assign('tbody', $gridTabela['tbody']);
	         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			 $g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	         $g['smarty']->assign('gridTable', '');
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	         
	         echo <<<EOT
				<script type="text/javascript">
					$('#grid_outro_campos').find('tr').unbind('click').unbind('click').bind('click', function() {
						var id = $(this).closest('tr').attr('id').replace('Campo-', '');
						$.ajax({
							url: '/sistema/progs/db/grid_outro_filtro_campo/',
							dataType: 'html',
							data: 'ajax=true&action=true&tabela=' + encodeURIComponent(id),
							success: function(data) {
								divRefresh('grid_outro');
								divRefresh('relacionamento');
								return false;
							}
						});
						return false;
					});
				</script>
EOT;
		
		break;
		
		case 'relacionamento':
			$tmpTO = quebraString($c_table_outro);
			$tmpT = quebraString($c_table);
			$tmpFO = quebraString($c_field_outro);
			$tmpF = quebraString($c_field);
			echo <<<EOT
				<br/>
				Novo Relacionamento:
				<table style="width: 100%; font-size: 14px;">
					<tr>
						<td style="font-size: 17px;">Origem</td>
						<td style="font-size: 17px;">Destino</td>
					</tr>
					
					<tr>
						<td style="padding-top: 10px;">Tabela:<br/><b>{$tmpTO}</></td>
						<td style="padding-top: 10px;">Tabela:<br/><b>{$tmpT}</></td>
					</tr>
					
					<tr>
						<td style="padding-top: 10px;">Campo:<br/><b>{$tmpFO}</></td>
						<td style="padding-top: 10px;">Campo:<br/><b>{$tmpF}</></td>
					</tr>
				</table>
				<br/>
				<button id="cr1" class="criarRelacionamento">Relac. OK!</button>
				<button id="cr2" class="criarRelacionamento">Relac. Temporário</button><br />
				<button id="cr3" class="criarRelacionamento" style="margin-top: 5px;">Não Possui Relac.</button>
				<script type="text/javascript">
				$('.criarRelacionamento').unbind('click').bind('click', function() {
					$.ajax({
						url: '/sistema/progs/db/criarRelacionamento/',
						dataType: 'html',
						data: 'ajax=true&action=true&id=' + $(this).attr('id'),
						success: function(data) {
							var \$buttons = {
								"Ok": function() { $(this).dialog("destroy").remove(); }
							}
							dialogConfirm('Mensagem', data, \$buttons);
							divRefresh('relacionamento');
							divRefresh('grid_outro_campos');
							divRefresh('table_fields');
							return false;
						}
					});
					return false;
				});
				</script>
EOT;
			echo '<br/><br/>Relacionamentos Existentes:';
			echo '<div style="clear: both;"></div>';
			echo '<div style="width: 49.9%; float: right;">';
			if (@is_array($relArray['destino'][$c_table][$c_field]) && !empty($relArray['destino'][$c_table][$c_field])) {
				echo '<br/><br/>';
				echo '<table style="width: 100%; font-size: 12px;">';
				foreach ($relArray['destino'][$c_table][$c_field] as $rel) {
					echo '<tr>';
						echo '<td style="padding-top: 6px; border: 1px dotted black;"><b>' . quebraString($rel['table']) . '</b><br/>-><br/><b>' . quebraString($rel['field']) . '</b></td>';
						echo "<td><a href=\"#\" onclick=\"dbRemoverRel('destino', '{$c_table}', '{$c_field}', '{$rel['table']}', '{$rel['field']}'); return false;\">X</a>";
					echo '</td>';
				}
				echo '</table>';
			}
			echo '</div>';
			echo '<div style="width: 49.9%; float: left;">';
			if (@is_array($relArray['origem'][$c_table_outro][$c_field_outro]) && !empty($relArray['origem'][$c_table_outro][$c_field_outro])) {
				echo '<br/><br/>';
				echo '<table style="width: 100%; font-size: 12px;">';
				foreach ($relArray['origem'][$c_table_outro][$c_field_outro] as $rel) {
					echo '<tr>';
						echo '<td style="padding-top: 6px; border: 1px dotted black;"><b>' . quebraString($rel['table']) . '</b><br/>-><br/><b>' . quebraString($rel['field']) . '</b></td>';
						echo "<td><a href=\"#\" onclick=\"dbRemoverRel('origem', '{$c_table_outro}', '{$c_field_outro}', '{$rel['table']}', '{$rel['field']}'); return false;\">X</a>";
					echo '</td>';
				}
				echo '</table>';
			}
			echo '</div>';
		break;
		
		case 'criarRelacionamento':
			if ($c_field_outro == '---') { echo '"Origem -> Campo" não selecionado.'; die(); }
			if ($c_field == '---' && $r_id != 'cr3') { echo '"Destino -> Campo" não selecionado.'; die(); }
			$origem = array(
				'table' => $c_table_outro,
				'field' => $c_field_outro,
				'type' => $r_id
			);
			$destino = array(
				'table' => $c_table,
				'field' => $c_field,
				'type' => $r_id
			);
			// verifica se este relacionamento já existe
			if ($r_id != 'cr3' && @is_array($relArray['destino'][$c_table][$c_field])) {
				foreach (@$relArray['destino'][$c_table][$c_field] as $value) {
					if ($origem == $value) {
						echo 'Relacionamento já existe!';
						die();
					}
				}
			}
			// cria o relacionamento
			@$relArray['origem'][$c_table_outro][$c_field_outro][] = $destino;
			//print_r($relArray['origem'][$c_table_outro]);
			if ($r_id != 'cr3') {
				@$relArray['destino'][$c_table][$c_field][] = $origem;
			}
			echo 'Relacionamento criado!';
		break;
		
		case 'dbRemoverRel':
			foreach($relArray[$r_rel][$r_table][$r_field] as $key => $value) {
				if ($value['table'] == $r_table2 && $value['field'] == $r_field2) {
					unset($relArray[$r_rel][$r_table][$r_field][$key]);
				}
			}
			$r_rel = ($r_rel == 'origem') ? 'destino' : 'origem';
			foreach($relArray[$r_rel][$r_table2][$r_field2] as $key => $value) {
				if ($value['table'] == $r_table && $value['field'] == $r_field) {
					unset($relArray[$r_rel][$r_table2][$r_field2][$key]);
				}
			}
		break;
		
		// Gera o DUMP da Conversão
		case 'iniciarUpdate':
			require($updateFile);
		break;
		
		// Gera o DUMP da Conversão
		case 'iniciarConversao':
			$totalTable = 0;
			$totalSql = 0;
			echo "Iniciando conversão...\n<br />";
			echo "Para: {$bancoDestinoConversao}\n<br /><br />";
			$result = mysqli_query("USE {$bancoDestinoConversao};");
			if (!$result) { die('Invalid query: ' . mysql_error()); }			
			//$result = mysql_query("TRUNCATE C004;");
			//$sql = "insert into `c004`(`C004_Id`,`C004_D020_Id`,`C004_D018_Id`,`C004_D024_Id`,`C004_Nome_Empresa`,`C004_Cidade`,`C004_Uf`,`C004_Cnpj`,`C004_Caminho_Sistema`,`C004_Matriz`,`C004_Banco_Dados`,`C004_Caminho_Dbf`,`C004_Importar`,`C004_Banco_Configuracao`,`C004_Nome_Abreviado`,`C004_Inscricao_Estadual`,`C004_Inscricao_Municipal`,`C004_Responsavel_Contato`,`C004_Telefone`,`C004_Fax`,`C004_Logradouro`,`C004_Numero`,`C004_Complemento`,`C004_Bairro`,`C004_Cep`,`C004_Estado`,`C004_Endereco_Impressora_Nf`,`C004_Percentual_Reducao_ICMS`,`C004_Aliquota_ICMS`,`C004_Ultimo_Numero_Nota_Fiscal`,`C004_Ultimo_Numero_Nota_Fiscal_Eletronica`,`C004_Ultimo_Numero_ACP`,`C004_Ultimo_Numero_Controle`,`C004_Flag_Isento_ICMS`,`C004_Flag_Isento_ISS`,`C004_Flag_Isento_IPI`,`C004_Flag_Empresa_Simples`,`C004_Aliquota_ISS`,`C004_Flag_IPI_Base_ICMS_Revenda`,`C004_Flag_IPI_Base_ICMS_Consumidor`,`C004_Site`,`C004_Logo_Marca`,`C004_Observacao_Fiscal`,`C004_Observacao_Fiscal_ICMS_Diferido`,`C004_Flag_Controla_Estoque`,`C004_Conta_Clientes`,`C004_Cojnta_Fornecedores`,`C004_Conta_Frete`,`C004_Meses_MMF`,`C004_CFOP_Venda_Dentro_Estado`,`C004_CFOP_Venda_Fora_Estado`,`C004_Emite_NF_Caixa`,`C004_Observacao_Promocao`,`C004_Observacao_Logo`,`C004_Observacao_Rodape_Orcamento`,`C004_Observacao_Cabecalho`,`C004_Flag_Decimais_Sugestao_Compra`,`C004_Observacao_Cotacao_Compra`,`C004_Ultimo_Numero_Cupom_Fiscal`,`C004_PEDVEN_Botao_Liberar_Almoxarifado`,`C004_PEDVEN_Botao_Liberar_Separacao`,`C004_Impressora_Pedido_Almox`,`C004_Linhas_Obs_Fiscal`,`C004_Obs_Reduzido`,`C004_Casas_Decimais_Preco`,`C004_Flag_Ativo`,`C004_CNAE_Fiscal`,`C004_Codigo_Regime_Tributario`,`C004_Senha_Certificados`,`C004_Localizacao_Certificados`,`C004_Certificado_Digital`,`C004_Impressora_Comercial`,`C004_Impressora_Balcao`,`C004_Percentual_PIS`,`C004_Percentual_COFINS`,`C004_Ambiente_NFe`,`C004_E_Mail`) values (1,9568,49,6641,'TESTE1','CURITIBA','PR','00.000.000/0000-00','{$bancoDestinoConversao}',1,'{$bancoDestinoConversao}','',0,'{$bancoDestinoConversao}','TESTE1','.','','FISCAL','41 3371-0000','41 3371-0000','RUA TESTE1','1','','HAUER','81630-000','PARANA','nota','0.00','0.00','2394','244','A1','839',2,2,2,2,'5.00','','','www.teste.com','logo_hardness.png','DOCUMENTO EMITIDO POR MICRO  EMPRESA OPTANTE PELO SIMPLES/PR\r\nPERMITE O APROVEITAMENTO DO CREDITO DO ICMS NO VALOR DE R$','','S',0,0,0,6,1,2,'1','','PRODUTOS ISO9001:2000','- Validade deste orçamento: 30 dias\r\n- Favor citar o número do nosso orçamento em seu pedido de compra',NULL,'S',NULL,0,NULL,'N','',5,'',2,'N','2649299',1,'1','C:\\\\Arquivos de programas\\\\VertrigoServ\\\\www\\\\hardness\\\\empresas\\\\{$bancoDestinoConversao}\\\\nfe\\\\certs\\\\',NULL,'','','0.65','3.00','2',''),(2,9568,49,0,'TESTE',NULL,NULL,'00.000.000/0000-00','{$bancoDestinoConversao}',1,'{$bancoDestinoConversao}',NULL,1,'{$bancoDestinoConversao}','TESTE','11111111','','.','41 3371-0000','41 3371-0000','RUA TESTE','1','','CIC','81630-000',NULL,'nota','0.00',NULL,'2394','11428','A343','4854',2,2,2,2,'0.00','N','N','www.teste.com','logo_crv.jpg','',NULL,NULL,0,0,0,6,22,13,'1','','','',NULL,'S',NULL,NULL,NULL,'N','',0,'',2,'S','',3,'1','/var/www/hardness/dados_usuarios/{$bancoDestinoConversao}/nfe/certs/','','','','0.65','3.00','1','');";
			//$result = mysql_query($sql);
			foreach ($relArray['destino'] as $destinoTabela => $destinoCampos) {
				$queryUse = mysqli_query("USE {$bancoDestino};");
				if (!$queryUse) { die('Invalid query: ' . mysql_error()); }
				/* Destino (nosso sistema, MySQL) */
				$camposDestinoTipo = array();
				$request = mysqli_query("DESC {$destinoTabela};");
				while ($row = mysql_fetch_assoc($request)) {
					$camposDestinoTipo[$row['Field']] = $row['Type'];
				}
				$queryUse = mysqli_query("USE {$bancoDestinoConversao};");
				if (!$queryUse) { die('Invalid query: ' . mysql_error()); }
				if (empty($destinoCampos)) { continue; }
				$camposDestino = '';
				$camposDestino_ = array();
				$tabelaOrigem = '';
				$camposOrigem = '';
				$camposOrigem_ = array();
				foreach ($destinoCampos as $campo => $campoValor) {
					if (empty($campoValor)) { continue; }
					$campo_ = $campoValor[0];
					if (!isset($campo_['table'])) { continue; } // caso antigo onde o registro foi removido mais ainda continua o dentro 'type'
					if (empty($tabelaOrigem)) {
						$tabelaOrigem = $campo_['table'];
					}
					if ($tabelaOrigem != $campo_['table']) {
						if (!isset($r_filtroTabela) || (isset($r_filtroTabela) && ($c_table_outro == $tabelaOrigem || $c_table_outro == $campo_['table']))) {
							echo "Erro ao identificar tabela origem popular a tabela destino: {$destinoTabela}.\n<br />";
							echo "Existe mais de uma tabela origem:\n<br />\n<br />";
							$print = print_r($destinoCampos, true);
							$print = str_replace("\n", "<br/>", $print);
							$print = str_replace(" ", "&nbsp;", $print);
							echo $print;
						}
					}
					$camposDestino .= (empty($camposDestino)) ? $campo : ', ' . $campo;
					$camposDestino_[] = $campo;
					$camposOrigem .= (empty($camposOrigem)) ? $campo_['field'] : ', ' . $campo_['field'];
					$camposOrigem_[] = $campo_['field'];
				}
				if (isset($r_filtroTabela) && $c_table_outro != $tabelaOrigem) { continue; }
				if (empty($camposOrigem)) { continue; }
				if (stripos($relArray['origemComments'][$tabelaOrigem], '[NAO]') !== false) { continue;	}
				mysqli_query("TRUNCATE {$destinoTabela};");
				$totalTable++;
				$totalSqlTable = 0;
				$sqlInsert = "INSERT INTO {$destinoTabela} ({$camposDestino}) VALUES (%s);";
				/* Origem (da onde pegamos os dados) */
				if ($bancoOrigem == 'sybase') {
					// Primeiro, pega o tipo de cada campo
					$camposOrigemTipo = array();
					$request = sybase_query("sp_columns {$tabelaOrigem};");
					while ($row = sybase_fetch_assoc($request)) {
						$camposOrigemTipo[$row['COLUMN_NAME']] = $row['TYPE_NAME'];
					}
					echo "Processando Tabela ({$tabelaOrigem}) -> ({$destinoTabela}) ... ";
					// Segundo, pega os dados e gera o insert
					$request = sybase_query("SELECT {$camposOrigem} FROM {$tabelaOrigem};", $dbproc);
					$count = 0;
					while ($row = sybase_fetch_row($request)) {
						if (isset($r_filtroTabela) && isset($r_filtroCount)) { 
							if (++$count == 1001) { break; }
						} elseif (isset($r_filtroCount)) {
							if (++$count == 101) { break; }
						}
						$values = '';
						foreach ($row as $key => $value) {
							// Processa valores dependendo do tipo do campo origem
							$value = trim($value);
							switch ($camposOrigemTipo[$camposOrigem_[$key]]) {
								// Tipo 'datetime' do SQL Server
								case 'datetime':
									$tmp = date_parse($value);
									switch ($camposDestinoTipo[$camposDestino_[$key]]) {
										case 'date':
											$value = "{$tmp['year']}-{$tmp['month']}-{$tmp['day']}";
										break;
										case 'time':
											$value = "{$tmp['hour']}:{$tmp['minute']}:{$tmp['second']}";
										break;
										case 'datetime':
											$value = "{$tmp['year']}-{$tmp['month']}-{$tmp['day']} {$tmp['hour']}:{$tmp['minute']}:{$tmp['second']}";
										break;
									}
								break;
							}
							$value = trim($value);
							$value = utf8_encode($value);
							$value = addslashes($value);
							$values .= empty($values) ? "'{$value}'" : ", '{$value}'";
						}
						$sql = sprintf($sqlInsert, $values);
						$result = mysqli_query($sql);
						$totalSql++;
						$totalSqlTable++;
						if (!$result) {
							echo "<br />\nErro inserindo a seguinte SQL:\n<br />{$sql}\n<br />" . mysql_error() . "<br />\n";
							//die("Erro inserindo a seguinte SQL:\n<br />{$sql}\n<br />" . mysql_error());
						}
					}
				} else {
					// Primeiro, pega o tipo de cada campo
					$camposOrigemTipo = array();
					$sql = <<<EOT
						SELECT

		RDB\$RELATION_FIELDS.RDB\$FIELD_NAME AS COLUMN_NAME,
		RDB\$TYPES.RDB\$TYPE_NAME AS DATA_TYPE

		FROM RDB\$RELATIONS
		INNER JOIN RDB\$RELATION_FIELDS ON RDB\$RELATIONS.RDB\$RELATION_NAME = RDB\$RELATION_FIELDS.RDB\$RELATION_NAME
		LEFT JOIN RDB\$FIELDS ON RDB\$RELATION_FIELDS.RDB\$FIELD_SOURCE = RDB\$FIELDS.RDB\$FIELD_NAME
		LEFT JOIN RDB\$TYPES ON RDB\$FIELDS.RDB\$FIELD_TYPE = RDB\$TYPES.RDB\$TYPE

		WHERE RDB\$RELATIONS.RDB\$RELATION_NAME = '{$tabelaOrigem}'
		AND RDB\$RELATIONS.RDB\$SYSTEM_FLAG = 0
		AND RDB\$TYPES.RDB\$FIELD_NAME='RDB\$FIELD_TYPE'

		ORDER BY RDB\$RELATION_FIELDS.RDB\$FIELD_POSITION;
EOT;
					$request = ibase_query($sql);
					while ($row = ibase_fetch_assoc($request)) {
						$camposOrigemTipo[trim($row['COLUMN_NAME'])] = trim($row['DATA_TYPE']);
					}
					echo "Processando Tabela ({$tabelaOrigem}) -> ({$destinoTabela}) ... ";
					// Segundo, pega os dados e gera o insert
					$request = ibase_query($dbproc, "SELECT {$camposOrigem} FROM {$tabelaOrigem};");
					$count = 0;
					while ($row = ibase_fetch_row($request)) {
						if (isset($r_filtroTabela) && isset($r_filtroCount)) { 
							if (++$count == 1001) { break; }
						} elseif (isset($r_filtroCount)) {
							if (++$count == 101) { break; }
						}
						$values = '';
						foreach ($row as $key => $value) {
							switch ($camposOrigemTipo[$camposOrigem_[$key]]) {
								// Tipo 'TIMESTAMP' do Firebird
								case 'TIMESTAMP':
									switch ($camposDestinoTipo[$camposDestino_[$key]]) {
										case 'date':
											$value = explode(' ', $value);
											$value = $value[0];
										break;
										case 'time':
											$value = explode(' ', $value);
											$value = $value[1];
										break;
										case 'datetime':
											// nothing
										break;
									}
								break;
							}
							$value = trim($value);
							$value = utf8_encode($value);
							$value = addslashes($value);
							$values .= empty($values) ? "'{$value}'" : ", '{$value}'";
						}
						$sql = sprintf($sqlInsert, $values);
						$result = mysqli_query($sql);
						$totalSql++;
						$totalSqlTable++;
						if (!$result) {
							echo "<br />\nErro inserindo a seguinte SQL:\n<br />{$sql}\n<br />" . mysql_error() . "<br />\n";
							//die("Erro inserindo a seguinte SQL:\n<br />{$sql}\n<br />" . mysql_error());
						}
					}
					echo "({$totalSqlTable}) registros incluídos.<br />\n";
				}
			}
			echo "<br />\nTerminado!<br />\n";
			echo "$totalSql comandos 'INSERT' executados em $totalTable tabelas diferentes.";
		break;
		
		case 'updateComment':
			$relArray['origemComments'][$r_c_tabela] = $r_c_value;
		break;
	}
	
	$h = fopen($db, 'w');
	fwrite($h, serialize($relArray));
	fclose($h);
	exit();
}

// Pagina principal
echo '<div style="width: 100%; height: 59.9%; float: left;">';
	
	echo '<div style="width: 64.9%; height: 100%; float: left;">';
	
		echo '<div id="grid_outro_tabelas" style="width: 33%; height: 100%; float: left; overflow: scroll; border: 1px solid #A6C9E2;">';
		echo '</div>';
		
		echo '<div id="relacionamento" style="width: 33%; height: 100%; float: right; overflow-y: scroll; overflow-x: hidden; border: 1px solid #A6C9E2; text-align: center;">';
		echo '</div>';
		
		echo '<div id="grid_outro_campos" style="width: 33%; height: 100%; float: right; overflow: scroll; border: 1px solid #A6C9E2;">';
		echo '</div>';
	
	echo '</div>';
	
	// Admin (Sistema Atual)
	echo '<div style="width: 34.9%; height: 100%; float: right; border: 1px solid #A6C9E2;">';
		
		echo <<<EOT
		<div style="clear: both; margin: 3px;">
			<form id="table_fields_filter">
			Tabela: <input type="text" name="tabela" value="{$c_filtro_tabela}">&nbsp;&nbsp;&nbsp;Campo: <input type="text" name="campo" value="{$c_filtro_campo}">&nbsp;&nbsp;&nbsp;<input type="submit" value="Filtrar" id="table_fields_filter_submit">
			</form>
		</div>
EOT;
		echo '<div id="table_fields" style="width: 98%; height: 90%; overflow: scroll; border: 1px solid #A6C9E2; margin: 3px;">';
		echo '</div>';
		
	echo '</div>';

	echo '</div>';
echo '<div style="width: 100%; height: 39.9%; float: right; border: 1px solid #A6C9E2;">';

	// Grid (Sistema Antigo)
	echo '<div id="grid_outro" style="width: 49.7%; height: 100%; float: left; overflow: scroll; border: 1px solid #A6C9E2;">';
	echo '</div>';
	
	// Grid (Sistema Atual)
	echo '<div id="grid_atual" style="width: 49.7%; height: 85%; float: right; overflow: scroll; border: 1px solid #A6C9E2;">';
	echo '</div>';
	
	echo '<div style="float: left; overflow: hidden; padding: 5px; text-align: left;">';
		echo "Origem: {$baseAtual} Destino (lista): {$bancoDestino} - Destino (dump): {$bancoDestinoConversao}";
	echo '</div>';
	echo '<div style="float: right; overflow: hidden; padding: 5px; text-align: right;">';
		echo '<div style="background-image: url(\'/hardness3/static/img/icone_csv.png\'); margin: 6px 10px" title="Gerar CSV" class="icone" onclick="window.location = \'/hardness3/' . $g['pathDados'] . 'tmpImportacaoCSV.csv\';"></div>';
		echo '<button id="changeSigmacar">Sigmacar</button>';
		//echo '<button id="changeEurosul">Eurosul</button>';
		echo '<button class="changeKoisas">K1</button>';
		echo '<button class="changeKoisas">K2</button>';
		echo '<button class="changeKoisas">K3</button>';
		echo '<button class="changeKoisas">K4</button>';
		echo '<button id="changeFixar">Fixar</button>';
		echo '<button id="changeAZPR">AZPR</button>';
		echo '<button id="btnUpdate">Update</button>';
		echo '<button id="btnGerarDump">C.</button>';
		echo '<button id="btnGerarDump3">C. Parc.</button>';
		echo '<button id="btnGerarDump2">C. Tab.</button>';
		echo '<button id="btnGerarDump4">C. Tab. Parc.</button>';
	echo '</div>';

echo '</div>';

$actions = array(
	// identificador, url, paramentros get (opcional)
	array('grid_atual', '/sistema/progs/db/grid_atual/', '&action=true'),
	array('table_fields', '/sistema/progs/db/table_fields/', '&action=true'),
	array('grid_outro_tabelas', '/sistema/progs/db/grid_outro_tabelas/', '&action=true'),
	array('grid_outro', '/sistema/progs/db/grid_outro/', '&action=true'),
	array('grid_outro_campos', '/sistema/progs/db/grid_outro_campos/', '&action=true'),
	array('relacionamento', '/sistema/progs/db/relacionamento/', '&action=true'),
);
echo gProcessaAcoes($actions);

$h = fopen($db, 'w');
fwrite($h, serialize($relArray));
fclose($h);

// Quebra uma string, mostra no máximo 15 chars por linha
function quebraString($string) {
	$new = $string;
	if (strlen($string) > 15) {
		$new = substr($string, 0, 15) . '<br/>' . substr($string, 15);
	} elseif (strlen($string) > 30) {
		$new = substr($string, 0, 15) . '<br/>' . substr($string, 15, 30) . '<br/>' . substr($string, 30);
	}
	return $new;
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#table_fields_filter_submit').unbind('click').bind('click', function() {
		$.ajax({
			url: '/sistema/progs/db/filtro/',
			dataType: 'html',
			data: 'ajax=true&action=true&' + $("#table_fields_filter").serialize(),
			success: function(data) {
				divRefresh('table_fields');
				return false;
			}
		});
		return false;
	});
	$('#btnGerarDump').unbind('click').bind('click', function(data) {
		abrirJanela(data, 'oneWindow', 'oneWindow', 'winGerarDump', '', 'Gerar DUMP de Conversão', '/sistema/progs/db/iniciarConversao/', '&action=true', [800,600]);
	});
	$('#btnGerarDump2').unbind('click').bind('click', function(data) {
		abrirJanela(data, 'oneWindow', 'oneWindow', 'winGerarDump', '', 'Gerar DUMP de Conversão', '/sistema/progs/db/iniciarConversao/', '&action=true&filtroTabela=true', [800,600]);
	});
	$('#btnGerarDump3').unbind('click').bind('click', function(data) {
		abrirJanela(data, 'oneWindow', 'oneWindow', 'winGerarDump', '', 'Gerar DUMP de Conversão', '/sistema/progs/db/iniciarConversao/', '&action=true&filtroCount=true', [800,600]);
	});
	$('#btnGerarDump4').unbind('click').bind('click', function(data) {
		abrirJanela(data, 'oneWindow', 'oneWindow', 'winGerarDump', '', 'Gerar DUMP de Conversão', '/sistema/progs/db/iniciarConversao/', '&action=true&filtroTabela=true&filtroCount=true', [800,600]);
	});
	$('#btnUpdate').unbind('click').bind('click', function(data) {
		abrirJanela(data, 'oneWindow', 'oneWindow', 'winExecutarUpdate', '', 'Script de Update', '/sistema/progs/db/iniciarUpdate/', '&action=true', [800,600]);
	});
	$('#changeEurosul').unbind('click').bind('click', function() {
		changeBaseAtual('eurosul');
	});
	$('#changeSigmacar').unbind('click').bind('click', function() {
		changeBaseAtual('sigmacar');
	});
	$('.changeKoisas').unbind('click').bind('click', function() {
		changeBaseAtual($(this).text());
	});
	$('#changeFixar').unbind('click').bind('click', function() {
		changeBaseAtual('fixar');
	});
	$('#changeAZPR').unbind('click').bind('click', function() {
		changeBaseAtual('azpr');
	});
});

// Remove um relacionamento
function dbRemoverRel(rel, table, field, table2, field2) {
	$.ajax({
		url: '/sistema/progs/db/dbRemoverRel/',
		dataType: 'html',
		data: 'ajax=true&action=true&rel=' + encodeURIComponent(rel) + '&table=' + encodeURIComponent(table) + '&field=' + encodeURIComponent(field) + '&table2=' + encodeURIComponent(table2) + '&field2=' + encodeURIComponent(field2),
		success: function(data) {
			divRefresh('relacionamento');
			divRefresh('grid_outro_campos');
			divRefresh('table_fields');
			return false;
		}
	});
	return false;
}

function changeBaseAtual(base) {
	$.ajax({
		url: '/sistema/progs/db/changebaseAtual/',
		dataType: 'html',
		data: 'ajax=true&action=true&base=' + base,
		success: function(data) {
			divRefresh('grid_outro_tabelas');
			divRefresh('grid_outro_campos');
			divRefresh('grid_outro');
			divRefresh('relacionamento');
			return false;
		}
	});
}

function updateComment(tabela, elem) {
	$.ajax({
		url: '/sistema/progs/db/updateComment/',
		dataType: 'html',
		data: 'ajax=true&action=true&c_tabela=' + encodeURIComponent(tabela) + '&c_value=' + encodeURIComponent($(elem).val()),
		success: function(data) {
			return false;
		}
	});
}
</script>
