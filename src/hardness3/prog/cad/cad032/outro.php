<?php
namespace hardness;
// para ter acesso as outras bases, é necessário acesso root no servidor
/*
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysqli_query('SET NAMES utf8;');
$g['conexaoBanco'] = $con;
*/

//
switch($g['r'][3]){
	case 'cad032OutroCopiaPerfil':
		$grid = array(
			array('colunaValor' => 'ID', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'Db', 'colunaTitulo' => 'DB', 'style' => 'width: 1%;', 'colunaExibir' => false),
			array('colunaValor' => 'Empresa', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%', 'colunaExibir' => false),
			array('colunaValor' => 'Perfil', 'colunaTitulo' => 'Perfil'),
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
			//'Db' => array('titulo' => 'Base de Dados', 'tipo' => 'texto'),
			//'Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'Perfil' => array('titulo' => 'Perfil', 'tipo' => 'texto'),
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
						"Copiar": function() { 
							$('#{$g['divId']}').showLoading();
							$.getJSON('/cad/cad032/outro/copiar/?ajax=true&id=' + encodeURIComponent(id) + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divIdParent']}');
								dialogConfirm('Ok', 'Copiado!');
							});
							$(this).dialog("destroy").remove();
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); } 
					}
					dialogConfirm('Copiar', 'Fazer uma cópia deste perfil para esta empresa?', buttons);
				});
			} </script></div>
EOT;
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		$Empresa = $g['ui']->getGridFiltroCampoBusca('Empresa');
		$Db = $g['ui']->getGridFiltroCampoBusca('Db');
		$Perfil = $g['ui']->getGridFiltroCampoBusca('Perfil');
		$geradoSql = '';
		$geradoDados = array();
		$tables = mysql_query("SELECT C021_Id, C021_Nome_Perfil FROM `C021`;");
		$C004 = mysql_query("SELECT C004_Nome_Abreviado FROM `C004`;");
		if ($C004) {
			$C004 = mysql_fetch_assoc($C004);
			$C004 = $C004['C004_Nome_Abreviado'];
		} else {
			$C004 = '';
		}
		while ($table = mysql_fetch_assoc($tables)) {
			if (!empty($Empresa) && stripos($C004, $Empresa) === false) { continue; }
			if (!empty($Db) && stripos($db[0], $Db) === false) { continue; }
			if (!empty($Perfil) && stripos($table['C021_Nome_Perfil'], $Perfil) === false) { continue; }
			$geradoDados[] = array(
				"{$db[0]}|{$table['C021_Id']}|{$table['C021_Nome_Perfil']}",
				$db[0],
				$C004,
				$table['C021_Nome_Perfil']
			);
		}
		/*
		$Empresa = $g['ui']->getGridFiltroCampoBusca('Empresa');
		$Db = $g['ui']->getGridFiltroCampoBusca('Db');
		$Perfil = $g['ui']->getGridFiltroCampoBusca('Perfil');
		$geradoSql = '';
		$geradoDados = array();
		$res = mysql_query('SHOW DATABASES;');
		while ($db = mysql_fetch_array($res)) {
			$tables = mysql_query("SELECT C021_Id, C021_Nome_Perfil FROM `{$db[0]}`.`C021`;");
			$C004 = mysql_query("SELECT C004_Nome_Abreviado FROM `{$db[0]}`.`C004`;");
			if ($C004) {
				$C004 = mysql_fetch_assoc($C004);
				$C004 = $C004['C004_Nome_Abreviado'];
			} else {
				$C004 = '';
			}
			if (!$tables) { continue; }
			while ($table = mysql_fetch_assoc($tables)) {
				if (!empty($Empresa) && stripos($C004, $Empresa) === false) { continue; }
				if (!empty($Db) && stripos($db[0], $Db) === false) { continue; }
				if (!empty($Perfil) && stripos($table['C021_Nome_Perfil'], $Perfil) === false) { continue; }
				$geradoDados[] = array(
					"{$db[0]}|{$table['C021_Id']}|{$table['C021_Nome_Perfil']}",
					$db[0],
					$C004,
					$table['C021_Nome_Perfil']
				);
			}
		}
		*/
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
		$C021_ID = $ids[1];
		$perfil = mysql_real_escape_string($ids[2]);
		// inserir C021
		$sql = "INSERT INTO C021 (C021_Nome_Perfil) VALUES ('{$perfil}');";
		mysql_query($sql);
		$lastId = $g['mysqlLastId'];
		//echo $g['mysqlLastId'] . "<br /><br />";
		//$C030 = mysql_query("SELECT * FROM `{$db}`.`C030` WHERE C030_C021_Id = '{$C021_ID}';");
		$C030 = mysql_query("SELECT * FROM C030 WHERE C030_C021_Id = '{$C021_ID}';");
		while ($row = mysql_fetch_array($C030)) {
			$sql = "INSERT INTO `c030`(`C030_C021_Id`,`c030_C029_Id`,`C030_Id_Perfil`,`C030_C007_Id`,`C030_Acesso_Liberado`)VALUES('{$lastId}','{$row[2]}','{$row[3]}','{$row[4]}','{$row[5]}');";
			mysql_query($sql);
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}