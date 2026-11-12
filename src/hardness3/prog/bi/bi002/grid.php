<?php
namespace hardness;

switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'bi002GridConfBI':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D176_Id', $g['divId'])),
            array('colunaValor' => 'D176_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D176_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'D176_Titulo', 'colunaTitulo' => 'Título'),
            array('colunaValor' => 'D176_Link', 'colunaTitulo' => 'Link','style' => 'max-width:250px; overflow: hidden;'),
            array('colunaValor' => 'D176_Descricao', 'colunaTitulo' => 'Descrição','style' => 'max-width:250px; overflow: hidden;'),
        );

		/**
		 * Definição da SQL
		 */
		$from = "D176";

		$extra = <<<EOT
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="bi002IncluirRelatorio">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#bi002IncluirRelatorio').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Relatório', '/bi/bi002/content/bi002ContentCadastroRelatorio/','', [600,400]);
				});
			} </script></div>
EOT;
			/**
		* Filtro
		*/
		$gridFiltro = array(
			'D176_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D176_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D176_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Links<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D176_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar link - ID: ' + id, '/bi/bi002/content/bi002ContentCadastroRelatorio/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
				});
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
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  	break;

  	case 'bi002GridConfPerfil':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D177_Id', $g['divId'])),
            array('colunaValor' => 'D177_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true,'colunaExibir' => false),
            array('colunaValor' => 'D177_D176_Id', 'colunaTitulo' => 'D176_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D177_C021_Id', 'colunaTitulo' => 'C021_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D176_Titulo ', 'colunaTitulo' => 'Link'),
            array('colunaValor' => 'D176_Descricao', 'colunaTitulo' => 'Descrição','style' => 'max-width:250px; overflow: hidden;'),
			array('colunaValor' => 'C004_Nome_Abreviado ', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'C021_Nome_Perfil ', 'colunaTitulo' => 'Perfil'),
			array('colunaValor' => 'C007_Nome ', 'colunaTitulo' => 'Usuário'),
        );

		/**
		 * Definição da SQL
		 */
		$from = "D177";

		$extra = <<<EOT
			LEFT JOIN D176 ON D177_D176_Id=D176_Id
			LEFT JOIN C021 ON D177_C021_Id=C021_Id
			LEFT JOIN C004 ON C004_Id=D177_C004_Id
			LEFT JOIN C007 ON C007_Id=D177_C007_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="bi002VincularPerfil">Incluir</button>

			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#bi002VincularPerfil').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Perfil', '/bi/bi002/content/bi002ContentCadastroPerfil/','', [450,200]);
				});
			} </script></div>
EOT;

			/**
		* Filtro
		*/
		$gridFiltro = array(
			'D176_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D176_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'C021_Nome_Perfil' => array('titulo' => 'Perfil', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D177_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Perfil - ID: ' + id, '/bi/bi002/content/bi002ContentCadastroPerfil/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [450,200]);
				});
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
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  	break;

  	case 'bi002GridRelatorios':
  		global $g;

		$grid = array(
            array('colunaValor' => 'D177_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false),
            array('colunaValor' => 'D176_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
            array('colunaValor' => 'D177_C021_Id', 'colunaTitulo' => 'C021_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D176_Titulo', 'colunaTitulo' => 'Link', 'style' => 'width:1%'),
            array('colunaValor' => 'D176_Descricao', 'colunaTitulo' => 'Descrição','style' => 'max-width:250px; overflow: hidden;'),
            array('colunaValor' => 'D176_Link', 'colunaTitulo' => 'Link', 'extra' => 'coluna="colunaLink"', 'style' => 'max-width:50px; overflow: hidden;','colunaExibir' => false),
        );

		/**
		 * Definição da SQL
		 */
		$from = "D177";
		$extra = <<<EOT
			LEFT JOIN D176 ON D177_D176_Id=D176_Id
			LEFT JOIN C021 ON D177_C021_Id=C021_Id
			WHERE (D177_C021_Id='{$g['perfilUsuario']}' || IFNULL(D177_C021_Id, 0) = 0) 
			  AND (D177_C004_Id='{$g['empresaAtual']}' || IFNULL(D177_C004_Id, 0) = 0)
			  AND (D177_C007_Id='{$g['usuarioAtual']}' || IFNULL(D177_C007_Id, 0) = 0)
EOT;

			/**
		* Filtro
		*/
		$gridFiltro = array(
			'D176_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D176_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D176_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D177_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', '', '/bi/bi002/outro/bi002OutroTelaRelatorios/', '&D176_Id=' + encodeURIComponent(id) + '&tabela={$from}', 'full');
					// var link = $(this).closest('tr').find('td[coluna]').text();
				});
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
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  	break;

}
?>
