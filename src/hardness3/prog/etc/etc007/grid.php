<?php
namespace hardness;
// Carrega funções callback
require_once('prog/etc/etc007/grid_func-callback.php');

/**
 * Ações
 */
global $_buscarBancoAuditoria;
$_buscarBancoAuditoria=true;

switch($g['r'][3]){
	case 'etc007GridAuditoria':
		if (!$r_id) {
			$filtroObrigatorio = true;
		}

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "T162_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => "T162_Acao", 'colunaTitulo' => 'Ação', 'callback' => 'acaoTipos'),
			array('colunaValor' => "T162_Tabela", 'colunaTitulo' => 'Tabela', 'colunaAlinhar'=>'center'),
			array('colunaValor' => "T162_C007_Id as C007_Id", 'colunaTitulo' => 'Nome','callback' => 'retornaC007Nome'),
			array('colunaValor' => "T162_Registro_Id", 'colunaTitulo' => 'ID Registro'),
			array('colunaValor' => "UNCOMPRESS(T162_Registro) as T162_Registro", 'colunaTitulo' => 'T162_Registro', 'colunaExibir' => false),
			array('colunaValor' => "Registro", 'colunaTitulo' => 'Registro', 'callback' => 'acoesRegistro', 'colunaTipo' => 'livre'),
			array('colunaValor' => "T162_DataHora", 'colunaTitulo' => 'Data/Hora', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T162_IP", 'colunaTitulo' => 'IP'),
			array('colunaValor' => "T162_Query", 'colunaTitulo' => 'Extra'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T162';
		if($r_tabela=='D001'){
			
			$r_id = mysql_real_escape_string($r_id);

			$D009 = mysql_query("SELECT D049_Id, 
										D009_Id 
								   FROM D001 
							  LEFT JOIN D049 ON D049_D001_Id=D001_Id 
							  LEFT JOIN D009 ON D009_D049_Id=D049_Id 
							      WHERE D001_Id='{$r_id}' 
								    AND D009_C004_Id = '{$g['empresaAtual']}'",$g['conexaoBanco']);

			$mD009 = mysql_fetch_array($D009);
			$extra = <<<EOT
				LEFT JOIN C007 ON C007_Id=T162_C007_Id
				WHERE T162_Acao != 'S' 
				AND (
					   (T162_Tabela = 'D001' AND T162_Registro_Id='$r_id') 
					OR (T162_Tabela = 'D049' AND T162_Registro_Id='{$mD009['D049_Id']}') 
					OR (T162_Tabela = 'D009' AND T162_Registro_Id='{$mD009['D009_Id']}')
				)
				ORDER BY T162_DataHora DESC
EOT;
		} else {
			$T162_Registro_Id = (isset($r_id) && !empty($r_id)) ? " AND T162_Registro_Id = '".mysql_real_escape_string($r_id)."'" : '';
			$extra = <<<EOT
				WHERE (T162_Acao != 'S' AND T162_Tabela = '{$r_tabela}'{$T162_Registro_Id})
				ORDER BY T162_DataHora DESC
EOT;
		}

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$gridFiltro = array(
			'T162_Acao' => array('titulo' => 'Ação', 'tipo' => 'texto'),
			'C007_Login' => array('titulo' => 'Login', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'T162_Registro_Id' => array('titulo' => 'ID Registro', 'tipo' => 'numero'),
			'T162_DataHora' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T162_IP' => array('titulo' => 'IP', 'tipo' => 'numero'),
			'T162_Tabela' => array('titulo' => 'Tabela', 'tipo' => 'texto'),
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		/*echo <<<EOT
			<script type="text/javascript">
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'SPAN') { return true; } 
					if (data.target.nodeName == 'SPAN' && $(data.target).hasClass('ui-icon')) { return true; }
					var id = $(this).closest('tr').attr('id').replace('T139_Id-', '');
					$(this).closest('tr').find('span').css('font-weight', 'normal');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=E&acaoId=' + encodeURIComponent(id), [1000,700]);
				});
			</script>
EOT;*/
	
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100);
		
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

	case 'etc007GridAuditoriaGeral':

		if (!$r_id) {
			$filtroObrigatorio = true;
		}

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "T162_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => "T162_C007_Id as C007_Id", 'colunaTitulo' => 'Nome','callback' => 'retornaC007Nome'),
			array('colunaValor' => "T162_Acao", 'colunaTitulo' => 'Ação', 'callback' => 'acaoTipos'),
			array('colunaValor' => "T162_Tabela", 'colunaTitulo' => 'Tabela', 'colunaAlinhar'=>'center'),
			array('colunaValor' => "T162_Registro_Id", 'colunaTitulo' => 'ID Registro'),
			array('colunaValor' => "UNCOMPRESS(T162_Registro) as T162_Registro", 'colunaTitulo' => 'T162_Registro', 'colunaExibir' => false),
			array('colunaValor' => "Registro", 'colunaTitulo' => 'Registro', 'callback' => 'acoesRegistro', 'colunaTipo' => 'livre'),
			array('colunaValor' => "T162_DataHora", 'colunaTitulo' => 'Data/Hora', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "T162_IP", 'colunaTitulo' => 'IP'),
			array('colunaValor' => "T162_Query", 'colunaTitulo' => 'Extra'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T162';
			$extra = <<<EOT
				WHERE (T162_Acao != 'S')
				ORDER BY T162_DataHora DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$gridFiltro = array(
			'T162_Acao' => array('titulo' => 'Ação', 'tipo' => 'texto'),
			'C007_Login' => array('titulo' => 'Login', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'T162_Registro_Id' => array('titulo' => 'ID Registro', 'tipo' => 'numero'),
			'T162_DataHora' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T162_IP' => array('titulo' => 'IP', 'tipo' => 'numero'),
			'T162_Tabela' => array('titulo' => 'Tabela', 'tipo' => 'texto'),
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100);
		
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
?>