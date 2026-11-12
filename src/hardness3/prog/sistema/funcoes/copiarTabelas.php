<?php
namespace hardness;
// para ter acesso as outras bases, é necessário acesso root no servidor
mysqli_close($g['conexaoBanco']);
$con=@mysqli_connect($confUsuario['dbHost'], 'root', base64_decode('PSFIMnI0bjZzOCE9'),$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) {
	echo $Error;
	die();
}
$g['conexaoBanco'] = $con;
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');

switch($g['r'][3]){
	case 'content':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/copiarTabelas/tab/', '')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'tab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/copiarTabelas/grid/', 'Importar', '', false, true),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'grid':
        
		$filtroObrigatorio = true;

		$grid = array(
			array('colunaValor' => 'ID', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'Db', 'colunaTitulo' => 'DB', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Empresa', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'Tabela', 'colunaTitulo' => 'Tabela'),
		);

		/**
		* Definição da SQL
		*/
		$from = "C021";
		$extra = <<<EOT
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Db' => array('titulo' => 'Base de Dados', 'tipo' => 'texto'),
			'Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'Tabela' => array('titulo' => 'Tabela', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);	
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/ID-/, '');
					var buttons = { 
						"Sobreescrever": function() { 
							$('#{$g['divId']}').showLoading();
							$.getJSON('/sistema/funcoes/copiarTabelas/copiar/?ajax=true&id=' + encodeURIComponent(id) + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divIdParent']}');
								dialogConfirm('Ok', 'Copiado!');
							});
							$(this).dialog("destroy").remove();
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); } 
					}
					dialogConfirm('Copiar', '<h2 style="color: red; font-weight: bold;">Atenção!!!</h2><br />Esta ação irá sobreescrever a tabela selecionada nesta empresa!<br />Continuar?', buttons);
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		$Empresa = $g['ui']->getGridFiltroCampoBusca('Empresa');
		$Db = $g['ui']->getGridFiltroCampoBusca('Db');
		$Tabela = $g['ui']->getGridFiltroCampoBusca('Tabela');
		$geradoSql = '';
		$geradoDados = array();
		$res = mysql_query('SHOW DATABASES;');
		while ($db = mysql_fetch_array($res)) {
			$C004 = mysql_query("SELECT C004_Nome_Abreviado FROM `{$db[0]}`.`C004`;");
			if (!$C004) { continue; }
			$C004 = mysql_fetch_assoc($C004);
			$C004 = $C004['C004_Nome_Abreviado'];
			$tables = mysql_query("SHOW TABLES;");
			while ($table = mysql_fetch_array($tables)) {
				if (!empty($Empresa) && stripos($C004, $Empresa) === false) { continue; }
				if (!empty($Db) && stripos($db[0], $Db) === false) { continue; }
				if (!empty($Tabela) && stripos($table[0], $Tabela) === false) { continue; }
				$geradoDados[] = array(
					"{$db[0]}|{$table[0]}",
					$db[0],
					$C004,
					$table[0]
				);
			}
		}
		//list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'copiar':
		$resposta = array('code' => true, 'data' => array());
		// vars
		$ids = explode('|', $r_id);
		$db = $ids[0];
		$tabela = $ids[1];
		// truncate
		$sql = "TRUNCATE {$tabela};";
		//echo "{$sql}\n";
		mysql_query($sql);
		// copia
		$copia = mysql_query("SELECT * FROM `{$db}`.`{$tabela}`;");
		while ($row = mysql_fetch_row($copia)) {
			$values = '';
			foreach ($row as $key => $value) {
				$value = mysql_real_escape_string($value);
				$values .= ($values) ? ", '{$value}'" : "'{$value}'";
			}
			$sql = "INSERT INTO `{$tabela}` VALUES ({$values});";
			//echo "{$sql}\n";
			mysql_query($sql);
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}