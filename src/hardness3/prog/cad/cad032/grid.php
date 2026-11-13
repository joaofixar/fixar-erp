<?php
namespace hardness;
//--Carrega funções do ajax
require_once('prog/cad/cad032/grid_func-js.php');
//--Carrega funções callback
require_once('prog/cad/cad032/grid_func-callback.php');


switch($g['r'][3]){
	/**
	* Definição do Grid (Tabela)
	*/
	case 'cad032GridPerfis':
		$grid = array(
            array('colunaValor' => 'C021_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%;'),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C021_Id', $g['divId'])),
            array('colunaValor' => 'C021_Nome_Perfil', 'colunaTitulo' => 'Perfil'),
        );

		/**
		* Definição da SQL
		*/
		$from = "C021";
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad032Incluir'),
			array('titulo' => 'Copia', 'id' => 'cad032Copia'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$btnCopia = $acaoCopia = '';
		$btnCopia = '<button type="button" id="cad032Copia">Copia</button>';
		$acaoCopia = <<<EOT
			$('#cad032Copia').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Perfil', '/cad/cad032/content/cad032ContentCopiaPerfil/','', [980,500]);
				});
			
EOT;*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad032Incluir">Incluir</button>
				{$btnCopia}
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad032Copia').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Perfil', '/cad/cad032/content/cad032ContentCopiaPerfil/','', [980,500]);
				});
				
				$('#cad032Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Perfil', '/cad/cad032/content/cad032contentCadastroPerfil/','', [500,300]);
				});
				{$acaoCopia}
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C021_Nome_Perfil' => array('titulo' => 'Perfil', 'tipo' => 'texto'),
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
					var id = $(this).closest('tr').attr('id').replace(/C021_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Perfil - ID: ' + id, '/cad/cad032/content/cad032contentCadastroPerfil/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [940,510]);
				});
			} </script></div>
EOT;
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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

	case 'cad032GridResumosRelatorios':
	
		$grid = array(
			array('colunaValor' => 'C028_Acesso_Liberado', 'colunaTitulo' => '','callback' => 'cad032CheckboxResumosRelatorios'),
			array('colunaValor' => 'C027_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true),
			array('colunaValor' => 'C027_Descricao_Acesso', 'colunaTitulo' => 'Opção'),
		);

		/**
		* Definição da SQL
		*/
		$from = "C027";
		//Foi retirada a clásula WHERE (C030_C021_Id={$r_acaoId})
		$extra = <<<EOT
			left join C028 on C028_C027_Id=C027_Id
			AND (C028_C021_Id={$r_acaoId}) 
			order by C027_Id
			
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'C029_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			//'C029_Grupo(C029_Grupo)' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			//'C029_Descricao_Acesso' => array('titulo' => 'Opção', 'tipo' => 'texto'),
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
				
	case 'cad032GridAcessos':
		$grid = array(
			array('colunaValor' => 'C030_Acesso_Liberado', 'colunaTitulo' => '','callback' => 'cad032CheckboxAcessos', 'style' => 'width: 1%;'),
			array('colunaValor' => 'C029_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'style' => 'width: 1%;'),
			array('colunaValor' => 'C029_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			array('colunaValor' => 'C029_Descricao_Acesso', 'colunaTitulo' => 'Opção'),

		);

		/**
		* Definição da SQL
		*/
		$from = "C029";
		$extra = <<<EOT
			left join C030 on C030_C029_Id=C029_Id AND (C030_C021_Id={$r_acaoId})
			left join C004 on C004_Id='{$g['usuarioAtual']}'
			WHERE (C029_Flag_Tipo = '{$r_C029_Flag_Tipo}')
			AND IF('{$g['C031']['tipoSistema']}' = 'M', C029_Flag_Modulo IN ({$g['C004']['C004_Modulos']}), 1=1)
			order by C029_Grupo,C029_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad032IncluirAcesso'),
		);
		echo Botoes::processar($botoes);


		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad032IncluirAcesso" {$disabled}>Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad032IncluirAcesso').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Acesso', '/cad/cad032/content/cad032ContentIncluirAcesso/', '', [320,140]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C029_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'C029_Grupo' => array('titulo' => 'Módulo', 'tipo' => 'texto'),
			'C029_Descricao_Acesso' => array('titulo' => 'Opção', 'tipo' => 'texto'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'campos' => array(array('title' => 'C029_Grupo', 'campoAgrupar' => 'C029_Grupo', 'campoMostrar' => 'C029_Grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),), 'colunaValor' => 'Agrupar', 'exibir' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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
				
	case 'cad032GridPerfisItem':
		$grid = array(
			array('colunaValor' => 'C021_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => "cad032CheckboxAcessos", 'colunaTipo' => "livre", 'callback' => 'cad032CheckboxAcessosItem'),
			array('colunaValor' => 'C021_Nome_Perfil', 'colunaTitulo' => 'Perfil'),
			array('colunaValor' => 'C030_Acesso_Liberado', 'colunaTitulo' => 'C030_Acesso_Liberado', 'colunaExibir' => false),
			array('colunaValor' => 'C030_C029_Id', 'colunaTitulo' => 'C030_C029_Id', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = "C021";
		$extra = <<<EOT
			LEFT JOIN C030 ON C030_C021_Id = C021_Id AND C030_C029_Id = '{$r_acaoId}'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
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
