<?php
namespace hardness;

// Carrega funções callback
require_once('prog/outros/layoutExportacao/grid_func-callback.php');

// Carrega funções do ajax
//require_once('prog/outros/layoutExportacao/grid_func-js.php');

/**
 * Ações
 */
switch($g['r'][3]) {

	 case 'layout':
	 
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "D150_Id", 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => "D150_Nome", 'colunaTitulo' => 'Nome', 'style' => 'width: 1%; font-weight: bold; font-size: 1.1em;'),
			array('colunaValor' => "D150_Descricao", 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => "D150_Link_Documentacao", 'colunaTitulo' => 'Documentação', 'callback' => 'docLink')
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D150';
		
		$extra = <<<EOT
EOT;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D150_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Layout', '/outros/layoutExportacao/content/sublayout/', '&D150_Id=' + encodeURIComponent(id) + '&tabela={$from}', [900,700]);
			});
		} </script></div>
EOT;
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
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

	case 'sublayout':
	 
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'campoOpcoes', 'colunaTipo' => 'livre', 'callback' => 'subLayoutOpcoes', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => "D151_Id", 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => "D151_Nome", 'colunaTitulo' => 'Nome', 'style' => 'width: 1%; font-weight: bold; font-size: 1.1em;'),
			array('colunaValor' => "D151_Descricao", 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => "D151_Select", 'colunaTitulo' => 'Select', 'callback' => 'subLayoutSelect'),
			//array('colunaValor' => "D151_Flag_Utiliza_Data", 'colunaTitulo' => 'Utiliza Datas?', 'callback' => 'checkboxUtilizaDatas'),
			array('colunaValor' => "D151_Campo_Datas", 'colunaTitulo' => 'Informe o campo das datas', 'colunaEditar'=>true),
			array('colunaValor' => "D151_Id_Pai", 'colunaTitulo' => 'Id pai', 'colunaEditar'=>true),
			array('colunaValor' => "D151_Where", 'colunaTitulo' => 'Where', 'colunaEditar'=>true),
			//array('colunaValor' => 'D151_Comentario', 'colunaTitulo' => 'Comentário', 'style' => 'width: 1%', 'callback' => 'subLayoutComentario', 'colunaLargura' => 500),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D151';
		
		$extra = <<<EOT
			WHERE
				D151_D150_Id = '{$r_D150_Id}'
			ORDER BY D151_Ordem ASC
EOT;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D151_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Itens', '/outros/layoutExportacao/content/itens/', '&D151_Id=' + encodeURIComponent(id) + '&tabela={$from}', [1100,600]);
			});
		} </script></div>
EOT;
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
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

	case 'itens':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'campoOpcoes', 'colunaTipo' => 'livre', 'callback' => 'subLayoutOpcoesItem', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => "D152_Id", 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => "D152_D151_Id", 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => "D152_Campo", 'colunaTitulo' => 'Campo', 'style' => 'width: 1%; font-weight: bold;'),
			array('colunaValor' => "D152_Formato", 'colunaTitulo' => 'Formato', 'style' => 'width: 1%;', 'callback' => 'subLayoutFormato', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D152_Tamanho', 'colunaTitulo' => 'Tamanho', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D152_Decimal', 'colunaTitulo' => 'Decimal', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D152_Posicao_Inicio', 'colunaTitulo' => 'Pos Inicial', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D152_Posicao_Fim', 'colunaTitulo' => 'Pos Final', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D152_Descricao', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%', 'style' => 'background-color: var(--celula-amarelo-claro)', 'callback' => 'subLayoutComentario'),
			array('colunaValor' => 'D152_Exemplo', 'colunaTitulo' => 'Exemplo', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D152_Campo_Select', 'colunaTitulo' => 'Select', 'style' => 'width: 1%', 'style' => 'background-color: var(--celula-verde); cursor: text;', 'extra' => "onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Campo Select', '/outros/layoutExportacao/content/campoSelect/', '&acaoId=' + $(this).closest('tr').attr('id').replace(/D152_Id-/, ''), [500,130]);\""),
			array('colunaValor' => 'D152_Valor_Fixo', 'colunaTitulo' => 'Fixo', 'style' => 'width: 1%', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-verde)'),
			array('colunaValor' => 'C1.D153_Nome', 'colunaTitulo' => 'Callback 1', 'style' => 'width: 1%', 'style' => 'background-color: var(--celula-verde); cursor: text;', 'extra' => "onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Campo Callback', '/outros/layoutExportacao/content/campoCallback/', '&c2=false&D152_Id=' + $(this).closest('tr').attr('id').replace(/D152_Id-/, ''), [780,500]);\""),
			array('colunaValor' => 'C2.D153_Nome', 'colunaTitulo' => 'Callback 2', 'style' => 'width: 1%', 'style' => 'background-color: var(--celula-verde); cursor: text;', 'extra' => "onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Campo Callback', '/outros/layoutExportacao/content/campoCallback/', '&c2=true&D152_Id=' + $(this).closest('tr').attr('id').replace(/D152_Id-/, ''), [780,500]);\""),
			array('colunaValor' => 'D152_Comentario', 'colunaTitulo' => 'Comentário', 'style' => 'width: 1%', 'callback' => 'subLayoutComentario', 'colunaLargura' => 500),
			array('colunaValor' => 'D152_Anotacoes', 'colunaTitulo' => 'Anotações', 'colunaEditar' => true),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D152';
		
		$extra = <<<EOT
			LEFT JOIN D153 C1 ON C1.D153_Id = D152_D153_Id
			LEFT JOIN D153 C2 ON C2.D153_Id = D152_D153_2_Id
			WHERE
				D152_D151_Id = '{$r_D151_Id}'
			ORDER BY D152_Posicao_Inicio ASC
EOT;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="itensUploadCSV">CSV Cordilheira</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#itensUploadCSV').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Upload CSV', '/outros/layoutExportacao/content/uploadCSV/', '&D151_Id={$r_D151_Id}', [550,130]);
				});
			} </script></div>
EOT;
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
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

	case 'callbacks':
	 
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D153_Id', $g['divId']), 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'campoOpcoes', 'colunaTipo' => 'livre', 'callback' => 'callbackOpcoes', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => "D153_Id", 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => "D153_Tipo", 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%;'),
			array('colunaValor' => "D153_Nome", 'colunaTitulo' => 'Nome', 'style' => 'font-weight: bold; font-size: 1.1em;')
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D153';
		
		$extra = <<<EOT
EOT;
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="layoutAddCallback">Incluir</button>
				<button type="button" id="layoutDelCallback">Limpar Associação</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#layoutAddCallback').unbind('click').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Callback', '/outros/layoutExportacao/content/adicionarCallback/', '', [650,550]);
				});
				$('#layoutDelCallback').unbind('click').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/outros/layoutExportacao/grid_func-ajax/callback/?ajax=true&c2={$r_c2}&remove=true&D152_Id=' + encodeURIComponent('{$r_D152_Id}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true, false, false, '{$r_D152_Id}', 'D152_Id');
					});
				});
			} </script></div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D153_Id-/, '');
				$('#{$g['divId']}').showLoading();
				$.getJSON('/outros/layoutExportacao/grid_func-ajax/callback/?ajax=true&c2={$r_c2}&D153_Id=' + encodeURIComponent(id) + '&D152_Id=' + encodeURIComponent('{$r_D152_Id}') + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					divRefresh('{$g['divIdParent']}', true, false, false, '{$r_D152_Id}', 'D152_Id');
				});
			});
		} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D153_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'D153_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto')
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
