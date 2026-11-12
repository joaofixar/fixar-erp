<?php
namespace hardness;

//--Carrega funções callback
require_once('prog/cad/cad034/grid_func-callback.php');
require_once('prog/cad/cad034/grid_func-js.php');

switch ($g['r'][3]) {
 /**
 * Weiberlan
 * 07/12/2011
 */
	case 'cad034GridPlanoDeContas':
	$importacaoDados = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D014_Id', $g['divId'])),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'callback' => 'campoGrupoNegrito', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D014_Flag_Ativo', 'colunaTitulo' => 'Ativo','callback' => 'validacheckAtivo', 'style'=>'width:3%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D014_Flag_Plano', 'colunaTitulo' => 'Tipo','callback' => 'campoGrupoNegrito', 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Balancete', 'colunaExibir' => false),
			array('colunaValor' => 'D014_Grupo_DRE', 'colunaTitulo' => 'DRE', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'style'=>'width:1%;'),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'ID Grupo', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => 'concat(D032_Tipo(D032_Tipo)," - ",D032_Descricao) as Grupo', 'colunaTitulo' => 'Tipo', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta','callback' => 'campoGrupoNegrito'),
			array('colunaValor' => 'D014_Dia_Vencimento', 'colunaTitulo' => 'Dia vencimento', 'callback' => 'campoGrupoNegrito', 'colunaAlinhar'=>'center', 'colunaEditar' => true),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'campoDebitoCredito'),
			array('colunaValor' => 'D014_Flag_Verificar_Saldo', 'colunaTitulo' => 'Verif.saldo?', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D014_Flag_Registrar_Conta', 'colunaTitulo' => 'Registrar conta?', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D014_Flag_Pagamento_Unico', 'colunaTitulo' => 'Pag.único?', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'D014_D032_Id', 'colunaTitulo' => 'Grupo', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$extra = <<<EOT
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN D024 ON D024_Id=D014_D024_Id
			LEFT JOIN D031 ON D031_Id=D014_D031_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			WHERE D014_Flag_Ativo='S'
			ORDER BY D014_Grupo
EOT;
		
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad034GridPlanoContasBtnIncluir')
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad034GridPlanoContasBtnIncluir">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridPlanoContasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [650,520]);
				});
			} </script></div>
EOT;
		function gridLinha($array) {
			if($array['todosCampos']['D014_Flag_Ativo']=='N'){
				return 'linhaCinza';
			}
		}
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D014_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Entradas', 'value' => 'RC'),
				array('title' => 'Saidas', 'value' => 'PG'),
			), 'naoGerarExtra' => false),
			
			//'D014_Grupo' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D154_Descricao' => array('titulo' => 'Sub-Conta', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_Dia_Vencimento' => array('titulo' => 'Dia Vencimento', 'tipo' => 'texto'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Grupo', 'campoAgrupar' => 'D014_D032_Id', 'campoMostrar' => 'Grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
            )),
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
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Planos de Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D014_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + acaoId, '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [1000,650]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	
	case 'cad034GridPlanoDeContasInativo':
	$importacaoDados = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D014_Id', $g['divId'])),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'callback' => 'campoGrupoNegrito', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D014_Flag_Ativo', 'colunaTitulo' => 'Ativo','callback' => 'validacheckAtivo', 'style'=>'width:3%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D014_Flag_Plano', 'colunaTitulo' => 'Tipo','callback' => 'campoGrupoNegrito', 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Balancete', 'colunaExibir' => false),
			array('colunaValor' => 'D014_Grupo_DRE', 'colunaTitulo' => 'DRE', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'style'=>'width:1%;'),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'ID Grupo', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => 'concat(D032_Tipo(D032_Tipo)," - ",D032_Descricao) as Grupo', 'colunaTitulo' => 'Tipo', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta','callback' => 'campoGrupoNegrito'),
			array('colunaValor' => 'D014_Dia_Vencimento', 'colunaTitulo' => 'Dia vencimento', 'callback' => 'campoGrupoNegrito', 'colunaAlinhar'=>'center', 'colunaEditar' => true),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'campoDebitoCredito'),
			array('colunaValor' => 'D014_Flag_Verificar_Saldo', 'colunaTitulo' => 'Verif.saldo?', 'colunaEditar' => true),
			array('colunaValor' => 'D014_Flag_Registrar_Conta', 'colunaTitulo' => 'Contas pagar?', 'colunaEditar' => true),
			array('colunaValor' => 'D014_Flag_Pagamento_Unico', 'colunaTitulo' => 'Pag.único?', 'colunaEditar' => true),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'D014_D032_Id', 'colunaTitulo' => 'Grupo', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$extra = <<<EOT
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN D024 ON D024_Id=D014_D024_Id
			LEFT JOIN D031 ON D031_Id=D014_D031_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			WHERE D014_Flag_Ativo!='S'
			ORDER BY D014_Grupo
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad034GridPlanoContasBtnIncluir'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad034GridPlanoContasBtnIncluir">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridPlanoContasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [650,520]);
				});
			} </script></div>
EOT;
		function gridLinha($array) {
			if($array['todosCampos']['D014_Flag_Ativo']=='N'){
				return 'linhaCinza';
			}
		}
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D014_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Entradas', 'value' => 'RC'),
				array('title' => 'Saidas', 'value' => 'PG'),
			), 'naoGerarExtra' => false),
			
			//'D014_Grupo' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D154_Descricao' => array('titulo' => 'Sub-Conta', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_Dia_Vencimento' => array('titulo' => 'Dia Vencimento', 'tipo' => 'texto'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Grupo', 'campoAgrupar' => 'D014_D032_Id', 'campoMostrar' => 'Grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
            )),
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
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Planos de Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D014_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + acaoId, '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [1000,650]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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

	case 'cad034GridGruposPlanoContas':
	$importacaoDados = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D032_Id', $g['divId']), 'style' => 'width:1%'),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%'),
			// array('colunaValor' => 'D032_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar'=>'center', 'colunaEditar' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%', 'colunaEditarTodos' => true, 'callback' => 'gCheckbox', 'callbackParameter' => array('D032_Flag_Ativo', 'N|S')), NÃO ESTÁ FUNCIONANDO CORRETAMENTE
			array('colunaValor' => 'D032_Flag_Ativo', 'colunaTitulo' => 'Ativo','callback' => 'validacheckAtivoGrupo', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D032_Tipo', 'colunaTitulo' => 'Cod', 'style' => 'width:1%', 'colunaExibir'=>false),
			array('colunaValor' => 'D032_Tipo(D032_Tipo)', 'colunaTitulo' => 'Tipo', 'style' => 'width:1%'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo'),
			//array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Plano de Contas'),
			//array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => '(+)/(-)', 'callback' => 'campoGrupoNegrito','callbackParameter' => 'D014_Flag_Tipo'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D032';
		$extra = <<<EOT
		WHERE D032_Flag_Ativo='S'
		ORDER BY D032_Id
EOT;
		
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad034IncluirGrupoPlanoContas'),
			);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad034IncluirGrupoPlanoContas">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034IncluirGrupoPlanoContas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir grupo plano de conta', '/cad/cad034/content/cad034ContentGrupoPlanoContasIncluir/', '&tab=geral', [400,150]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D032_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D032_Tipo(D032_Tipo)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
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
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Grupos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D032_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Grupo Plano de Contas - ID: ' + acaoId, '/cad/cad034/content/cad034ContentGrupoPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,150]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	
	case 'cad034GridGruposPlanoContasInativo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D032_Id', $g['divId']), 'style' => 'width:1%'),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%'),
			// array('colunaValor' => 'D032_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar'=>'center', 'colunaEditar' => true, 'colunaEditarTipo' => flag, 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%', 'colunaEditarTodos' => true, 'callback' => 'gCheckbox', 'callbackParameter' => array('D032_Flag_Ativo', 'N|S')), NÃO ESTÁ FUNCIONANDO CORRETAMENTE
			array('colunaValor' => 'D032_Flag_Ativo', 'colunaTitulo' => 'Ativo','callback' => 'validacheckAtivoGrupo', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D032_Tipo', 'colunaTitulo' => 'Cod', 'style' => 'width:1%', 'colunaExibir'=>false),
			array('colunaValor' => 'D032_Tipo(D032_Tipo)', 'colunaTitulo' => 'Tipo', 'style' => 'width:1%'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo'),
			//array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Plano de Contas'),
			//array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => '(+)/(-)', 'callback' => 'campoGrupoNegrito','callbackParameter' => 'D014_Flag_Tipo'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D032';
		$extra = <<<EOT
		WHERE D032_Flag_Ativo!='S'
		ORDER BY D032_Id
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad034IncluirGrupoPlanoContas'),
		);
		echo Botoes::processar($botoes);		

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad034IncluirGrupoPlanoContas">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034IncluirGrupoPlanoContas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir grupo plano de conta', '/cad/cad034/content/cad034ContentGrupoPlanoContasIncluir/', '&tab=geral', [400,150]);
				});
			} </script></div>
			
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D032_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D032_Tipo(D032_Tipo)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
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
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Grupos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D032_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Grupo Plano de Contas - ID: ' + acaoId, '/cad/cad034/content/cad034ContentGrupoPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,150]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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

	case 'cad034GridProjecaoOrcamentaria':
		$ano = (isset($r_ano)) ? $r_ano : date("Y");		
		$D014 = mysql_fetch_array(mysql_query("SELECT * FROM D014 WHERE D014_Id = '{$r_acaoId}'"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T018_Id', $g['divId'])),
			array('colunaValor' => 'T018_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_D024_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_D031_Id', 'colunaTitulo' => 'Id','colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => 'D014_Flag_Tipo','colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Pagamento_Unico', 'colunaTitulo' => 'Pagamento único','colunaExibir'=>false),
			array('colunaValor' => 'T018_Ano', 'colunaTitulo' => 'Ano', 'style'=>'width:1%;'),
			array('colunaValor' => 'T018_Mes', 'colunaTitulo' => 'Mês', 'style'=>'width:1%;', 'callback'=>'validaCampoMes'),
			array('colunaValor' => 'D014_Dia_Vencimento', 'colunaTitulo' => 'Dia', 'style'=>'width:1%;'),
			array('colunaValor' => 'T018_Valor', 'colunaTitulo' => 'Valor projeção', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true, 'colunaAlinhar'=>'right', 'colunaEditarJSCallback'=>'atualizaFlagConfirmado'),
			array('colunaValor' => 'T018_Flag_Confirmado', 'colunaTitulo' => 'Confirmado', 'callback'=>'campoConfirmado', 'style'=>'width:1%', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T018_Valor_Receber', 'colunaTitulo' => 'Receber', 'colunaCasasDecimais'=>'2', 'colunaCasasDecimais'=>'2',  'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais'=>'2', 'colunaCasasDecimais'=>'2',  'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Pagar', 'colunaTitulo' => 'Pagar', 'colunaCasasDecimais'=>'2', 'colunaCasasDecimais'=>'2',  'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaCasasDecimais'=>'2', 'colunaCasasDecimais'=>'2',  'colunaAlinhar'=>'right'),
			//array('colunaValor' => 'T018_Valor_Saldo_Projecao', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais'=>'2', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T018_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'callback'=>'ValidaCampoSaldo',  'colunaAlinhar'=>'right'),
		);

		if($D014['D014_Flag_Tipo'] == 'PG'){
			$grid[] = array('colunaValor' => 'incluirTituloProjetado', 'colunaTitulo' => '', 'callback' => 'incluirTituloProjetado', 'style' => 'width: 1%', 'colunaTipo' => 'livre');
		}

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$extra = <<<EOT
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_D014_Id='{$r_acaoId}' 
			  AND T018_Ano='$ano'
			  AND T018_C004_Id='{$g['empresaAtual']}'
			GROUP BY T018_Id
EOT;
		$extraTotal = <<<EOT
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_D014_Id='{$r_acaoId}' 
			  AND T018_Ano='$ano'
			  AND T018_C004_Id='{$g['empresaAtual']}'
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="cad034GridProjecaoOrcamentariaIncluir">Incluir</button>-->
				<button type="button" id="cad034RecalcularProjecao">Recalcular</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridProjecaoOrcamentariaIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Projeção orçamentária', '/cad/cad034/content/cad034ContantIncluirProjecao/', '&tab=geral&acaoId=' + {$r_acaoId}, [500,320]);
				});				
				$('#cad034RecalcularProjecao').unbind('click').bind('click', function(data) {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/cad/cad034/grid_func-ajax/cad034RecalcularProjecao/?ajax=true&D014_Id=' + encodeURIComponent('{$r_acaoId}') + '&ano=' + encodeURIComponent('{$ano}') + '&callback=?', function(request) {
						$('#' + '{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						} else {
							alert(request.data);
						}
					});
				});
				function atualizaFlagConfirmado(Ids, campoEditado, valorAntigo, novoValor){
					var T018_Id_array = Ids.split('|');
					var T018_Id = T018_Id_array[0].replace('T018_Id' + '-', '');
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin009/grid_func-ajax/fin009AtualizaFlagConfirmado/?ajax=true&callback=?&T018_Id=' + encodeURIComponent(T018_Id) + '&novoValor=' + encodeURIComponent(novoValor), function(request) {
						$('#{$g['divId']}').hideLoading();
						if(novoValor == "0,00"){
							$('#{$g['divId']} input[name=T018_Flag_Confirmado]:eq(' + $('#{$g['divId']} tr[id*="T018_Id-' + T018_Id + '"]').first().index() + ')').prop('checked', '');
						}else{
							$('#{$g['divId']} input[name=T018_Flag_Confirmado]:eq(' + $('#{$g['divId']} tr[id*="T018_Id-' + T018_Id + '"]').first().index() + ')').prop('checked', 'checked');
						}
					});
				}
			} </script></div>
EOT;

		$gridFiltro = array(
        );
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 20px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad034/outro/cad034CabecalhoProjecao/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($g['divId']) .  '&ano=' . urlencode($ano), true),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Totais (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraTotal);
		$Projecao_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor>0, T018_Valor, 0))", $from, $extraTotal));
		$Projecao_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0))", $from, $extraTotal));
		$T018_Valor_Receber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Receber)", $from, $extraTotal));
		$T018_Valor_Recebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Recebido)", $from, $extraTotal));
		$T018_Valor_Pagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pagar)", $from, $extraTotal));
		$T018_Valor_Pago = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pago)", $from, $extraTotal));
		$Saldo_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extraTotal));
		$Saldo_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extraTotal));
		$Saldo_Total = gCorrigeNumero(gCorrigeNumeroInverte($Saldo_Entrada) + 
									  gCorrigeNumeroInverte($T018_Valor_Receber) + 
									  gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($Saldo_Saida) - 
									  gCorrigeNumeroInverte($T018_Valor_Pagar) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                            );
		echo <<<EOT
			<div class="buttonsBottomTop" style="margin-bottom:5px;">
				<span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Projeção<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Saldo<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D014_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
			        		// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + acaoId, '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [900,700]);
				        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad034GridContasPagar':

		// Classes
		require_once('bibliotecas/classes/FIN002.php');
		$FIN002 = new FIN002();
		// Carrega funções callback
		require_once('prog/fin/fin002/grid_func-callback.php');

		// Carrega funções do ajax
		require_once('prog/fin/fin002/grid_func-js.php');
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	        array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	        array('colunaValor' => 'T015_Flag_Reparcelar', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T015_Id', $g['divId']), 'colunaExibir' => false),
	        array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
			array('colunaValor' => 'T015_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaExibir' => false),
	        array('colunaValor' => "campoRP", 'colunaTipo' => "livre", 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaExibir' => false),
			array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli', 'colunaAlinhar'=>'right'),
			array('colunaValor' => "concat(T015_Numero_Parcela,'/',T015_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
			array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
			array('colunaValor' => "T015_Observacao", 'colunaTitulo' => 'Observação'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT
				left join D024 on D024_Id=T015_D024_Id
				left join D073 on D073_Id=T015_D073_Id
				left join D031 on D031_Id=T015_D031_Id
				left join D014 on D014_Id=T015_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				left join T007 on T007_Id=T015_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				where 
				(T015_C004_Id={$g['empresaAtual']})
                and (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
                and T015_D014_Id='{$r_acaoId}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T015_Data_Pagamento']!='0000-00-00'){
				return 'linhaCinza';
			} 
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
			'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),

			// 'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			// 'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			// 'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'numero'),
			// 'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			// 'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda'),
			// 'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);			
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		* Botões (Versão Acima do GRID)
		*/
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="fin002Incluir">Incluir</button>-->
				<!-- <button type="button" id="fin002Baixar">Baixar</button> -->
                <!-- <button type="button" id="fin002Agrupar">Agrupar</button> -->
                <!-- <button type="button" id="fin002Reparcelar">Reparcelar</button> -->
                
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '', [600,500]);
				});			
				$('#fin002Baixar').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin002/grid_func-ajax/verificaBaixar/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
				    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin002/content/fin002content03/', '&extra={$extra_}', [1100,500]);
				    } else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Baixa de Títulos', request.data, buttons);							
						}
					});
				});
                
                $('#fin002Reparcelar').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fin/fin002/grid_func-ajax/verificaReparcelar/?ajax=true&extra={$extra_}&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code == 0) {
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Reparcelamento', '/fin/fin002/content/fin002ContentReparcelar/', '&extra={$extra_}', [950,700]);
                        } else {
                            var buttons = {
                                    "Ok": function() { $(this).dialog("destroy").remove(); }
                                }
                            dialogConfirm('Reparcelar', request.data, buttons);
                        }
                    });
                });
                
                $('#fin002Agrupar').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fin/fin002/grid_func-ajax/verificaAgrupar/?ajax=true&extra={$extra_}&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code==0) {
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Agrupar', '/fin/fin002/content/fin002ContentAgrupar/', '&extra={$extra_}', [700,500]);
                        } else {
                            var buttons = {
                                    "Ok": function() { $(this).dialog("destroy").remove(); }
                                }        
                            dialogConfirm('Agrupamento', request.data, buttons);                            
                        }
                    });
                });    
			} </script></div>
EOT;
        }
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Totalizadores
		*/
		$valorTotalPago= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Pago)", $from, $extra));
		$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T015_Valor_Saldo)", $from, $extra));
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>
				<!-- <span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>-->
				<span class="item" style="text-align:right">Pago<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalPago}" readonly></span>
				<span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span>
				<span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total pagar<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span>
                <!-- <span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar" size="11" readonly></span> -->
                <!-- <span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span> -->
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002ExtraBtns').data('extra', '{$extra__}');
				carregaInputBaixar();
                carregaInputReparcelar();
                carregaInputAgrupar();
			} </script></div>
EOT;
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad034GridContasReceber':

		// Classes
		require_once('bibliotecas/classes/FIN001.php');
		$FIN001 = new FIN001();
		// Carrega funções callback
		require_once('prog/fin/fin001/grid_func-callback.php');

		// Carrega funções do ajax
		require_once('prog/fin/fin001/grid_func-js.php');
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	        array('colunaValor' => 'T002_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
	        array('colunaValor' => 'T002_Flag_Reparcelar', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T002_Id', $g['divId']), 'colunaExibir' => false),
	        array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
			array('colunaValor' => 'T002_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaExibir' => false),
	        array('colunaValor' => "campoRP", 'colunaTipo' => "livre", 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaExibir' => false),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli', 'colunaAlinhar'=>'right'),
			array('colunaValor' => "concat(T002_Numero_Parcela,'/',T002_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'Observação'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T002';

		$extra = <<<EOT
				left join D024 on D024_Id=T002_D024_Id
				left join D073 on D073_Id=T002_D073_Id
				left join D027 on D027_Id=T002_D027_Id
				left join D014 on D014_Id=T002_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				left join T007 on T007_Id=T002_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				where 
				(T002_C004_Id={$g['empresaAtual']})
                and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
                and T002_D014_Id='{$r_acaoId}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T002_Data_Recebimento']!='0000-00-00'){
				return 'linhaCinza';
			} 
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);			
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Totalizadores
		*/
		$valorTotalRecebido= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T002_Valor_Recebido)", $from, $extra));
		$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0))", $from, $extra));
		$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0))", $from, $extra));
		$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T002_Valor_Saldo)", $from, $extra));
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>
				<!-- <span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>-->
				<span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalRecebido}" readonly></span>
				<span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span>
				<span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total receber<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span>
                <!-- <span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar" size="11" readonly></span> -->
                <!-- <span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span> -->
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002ExtraBtns').data('extra', '{$extra__}');
				carregaInputBaixar();
                carregaInputReparcelar();
                carregaInputAgrupar();
			} </script></div>
EOT;
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID : ' + array['todosCampos']['T002_Id'] , '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T002_Id']) + '&tabela={$from}', [800,500]);
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad034GridLancamentos':
		$D014_Id = $r_acaoId;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
		array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T019_Id', $g['divId'])),
		array('colunaValor'=>'T019_Id', 'colunaTitulo'=>'ID','chavePrimaria' => true),
		array('colunaValor'=>'D007_Id', 'colunaTitulo'=>'ID','chavePrimaria' => true),
		array('colunaValor'=>'T019_Data_Lancamento', 'colunaTitulo'=>'Data', 'callback'=>'gCorrigeData','colunaAlinhar' => 'center'),
		array('colunaValor'=>'T019_Referente', 'colunaTitulo'=>'Descrição'),
		array('colunaValor'=>'T019_Complemento', 'colunaTitulo'=>'Complemento'),
		array('colunaValor'=>'T019_Valor_Lancamento', 'colunaTitulo'=>'Valor', 'colunaCasasDecimais'=>'2','colunaAlinhar' => 'right'),
		array('colunaValor'=>'T019_Saldo', 'colunaTitulo'=>'Saldo', 'colunaCasasDecimais'=>'2','colunaAlinhar' => 'right'),
		array('colunaValor'=>'D027_Portador', 'colunaTitulo'=>'Portador'),
		array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Empresa'),
		array('colunaValor'=>'D014_SubConta', 'colunaTitulo'=>'Conta'),
		array('colunaValor'=>'D007_Nome_Banco', 'colunaTitulo'=>'Banco'),
		
		);
		
		$from = 'T019';
		$extra = <<<EOT
		    LEFT JOIN D024 ON D024_Id=T019_D024_Id
			LEFT JOIN D027 ON D027_Id=T019_D027_Id
			LEFT JOIN D014 ON D014_Id=T019_D014_Id
			LEFT JOIN D007 ON D007_Id=T019_D007_Id
			where (T019_D014_Id='{$D014_Id}')
			ORDER BY T019_Data_Lancamento DESC, T019_Id DESC
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin003BtnInserirLancamento">Incluir Lançamento</button>
				<button type="button" id="fin003BtnInserirTransferencia">Incluir Transferência</button>
				<button type="button" id="fin003BtnAtualizarSaldos">Atualizar Saldos</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin003BtnInserirLancamento').unbind('click').bind('click', function(event) {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Lançamentos' , '/fin/fin003/content/fin003ContentIncluirLancamento/', '&D007_Id=' + encodeURIComponent('$D007_Id') + '&acaoId=', [600,500]);
				});
				$('#fin003BtnInserirTransferencia').unbind('click').bind('click', function(event) {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transferência' , '/fin/fin003/content/fin003ContentIncluirTransferencia/', '&D007_Id=' + encodeURIComponent('$D007_Id') + '&acaoId=', [600,500]);
				});
				$('#fin003BtnAtualizarSaldos').unbind('click').bind('click', function(event) {
				var buttons = {
					"Sim": function() {
							$.getJSON('/fin/fin003/grid_func-ajax/atualizarSaldos/?ajax=true&D007_Id=' + encodeURIComponent('$D007_Id') + '&callback=?', function(request) {
							if(request.data){
								dialogConfirm('OK!', request.data);
							}else{
								dialogConfirm('Erro!', request.data);
							}
							divRefresh('{$g['divId']}');
							divRefresh('{$r_divReload}'); 
							});
							$(this).dialog("destroy").remove();
					     },
						 "Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmar', "ATENCAO! Deseja atualizar saldo?", buttons);
				});
			} </script></div>
EOT;*/

		 /**
		* Filtro
		*/
		$gridFiltro = array(
			'T019_Referente' => array('titulo' => 'Referente', 'tipo' => 'texto'),
			'T019_Complemento' => array('titulo' => 'Complemento', 'tipo' => 'texto'),
			'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T019_Valor_Lancamento' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T019_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotalLancamentos  = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$valorTotalRecebimentos = $g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0))", $from, $extra);
		$valorTotalPagamentos   = $g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0))", $from, $extra);
		$valorTotalResultado    = ($valorTotalRecebimentos + $valorTotalPagamentos);
		$valorTotalLancamentos  = $valorTotalLancamentos;
		$valorTotalRecebimentos = gCorrigeNumero($valorTotalRecebimentos);
		$valorTotalPagamentos   = gCorrigeNumero($valorTotalPagamentos);
		$ResultadoTotal         = gCorrigeNumero($valorTotalResultado);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Lançamentos<br><input type="text" value="{$valorTotalLancamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Recebimentos<br><input type="text" value="{$valorTotalRecebimentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pagamentos<br><input type="text" value="{$valorTotalPagamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Resultado<br><input type="text" value="{$ResultadoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define a abertura do form
		*/	
		
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var chavePrimaria = 'T019_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T019_Id = id[0].replace(/T019_Id-/, '');
					var D007_Id = id[1].replace(/D007_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Lançamento Bancário', '/fin/fin003/content/fin003ContentEditaLancamento/', '&acaoId=' + encodeURIComponent(T019_Id) + '&tabela={$from}&D007_Id=' + encodeURIComponent(D007_Id), [600,500]);
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

	case 'cad034GridSubGruposPlanoContas':
	$importacaoDados = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D154_Id', $g['divId']), 'style' => 'width:1%'),
			array('colunaValor' => 'D154_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%'),
			array('colunaValor' => 'D154_Descricao', 'colunaTitulo' => 'Sub-Grupo'),
			//array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Plano de Contas'),
			//array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => '(+)/(-)', 'callback' => 'campoGrupoNegrito','callbackParameter' => 'D014_Flag_Tipo'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D154';
		$extra = <<<EOT
			LEFT JOIN D014 ON D014_D154_Id=D154_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			GROUP BY D154_Id
			ORDER BY D154_Id
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad034IncluirSubGrupoPlanoContas'),
			);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad034IncluirSubGrupoPlanoContas">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034IncluirSubGrupoPlanoContas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir sub-grupo plano de conta', '/cad/cad034/content/cad034ContentSubGrupoPlanoContasIncluir/', '&tab=geral', [400,150]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D154_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'D154_Descricao' => array('titulo' => 'Sub-Grupo', 'tipo' => 'texto'),
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
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Botões (Versão Acima do GRID)
		*/
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(DISTINCT D154_Id)", $from, gRetirarGroupBy($extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Sub-Grupos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D154_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Grupo Plano de Contas - ID: ' + acaoId, '/cad/cad034/content/cad034ContentSubGrupoPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,150]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
