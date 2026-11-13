<?php
namespace hardness;
//Carrega funções callback
require_once('prog/cad/cad030/grid_func-callback.php');
/**--Carrega funções do ajax
 require_once('prog/cad/cad030/grid_func-js.php');
 */
switch($g['r'][3]){
	/**
		* Definição do Grid (Tabela)
		*/
	case 'cad030GridUsuarios':

		$clausula = "";
		if($g['usuarioAtual']!="63"){
			$clausula = "AND usuario.C007_Id!='63'";
		}

		$grid = array(
            array('colunaValor' => 'excluirUsuário', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('usuario.C007_Id', $g['divId'])),
            array('colunaValor' => 'usuario.C007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'usuario.C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'usuario.C007_Nome', 'colunaTitulo' => 'Nome completo'),
            array('colunaValor' => 'usuario.C007_Flag_Vendedor', 'colunaTitulo' => 'Tipo', 'callback' => 'campoTipoUsuario'),
            array('colunaValor' => 'usuario.C021_Nome_Perfil', 'colunaTitulo' => 'Empresa - Perfil', 'callback' => 'validaCampoPerfil', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'supervisor.C007_Primeiro_Nome', 'colunaTitulo' => 'Supervisor'),
            array('colunaValor' => 'substituto.C007_Primeiro_Nome', 'colunaTitulo' => 'Substituto'),
			array('colunaValor' => 'usuario.C007_Data_Alteracao_Senha', 'colunaTitulo' => 'Alteração senha', 'callback' => 'gCorrigeData'),
			
        );

		/**
		 * Definição da SQL
		 */
		$from = "C007 as usuario";
		$extra = <<<EOT
		    LEFT JOIN C007 as supervisor ON supervisor.C007_Id=usuario.C007_C007_Id
		    LEFT JOIN C007 as substituto ON substituto.C007_Id=usuario.C007_Id_Vendedor_Substituto
			WHERE usuario.C007_Ativo = 'S'
			{$clausula}
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad030Incluir'),
			array('titulo' => 'Recriar Senha Banco', 'id' => 'cad030RecriarSenhaBancoTodos',  'exibirCallback' => 'return $g["usuarioAtual"] == 63;'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		/*$('#cad030RecriarSenhaBancoTodos').click(function(data) {
					var buttons = { 
						"Recriar para todos os usuários": function() { 
							$('#{$g['divId']}').showLoading();
							$.getJSON('/cad/cad030/form_func-ajax/recriarSenha/?ajax=true&callback=?', function(request) {
	      						$('#{$g['divId']}').hideLoading();
								dialogConfirm('Ok', request.data);
							});
							$(this).dialog("destroy").remove(); 
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Deseja continuar?', 'Este processo irá recriar a senha de acesso a nível de banco de dados.', buttons);
				});*/
		/*$btn = '';
		if ($g['usuarioAtual'] == 63) {
			$btn = '<button type="button" id="cad030RecriarSenhaBancoTodos">Recriar Senha Banco</button>';
		}
		
			<div class="buttonsBottomTop">
				<button type="button" id="cad030Incluir">Incluir</button>
				{$btn}
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad030Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário', '/cad/cad030/content/cad030contentCadastroUsuario/','', [1000,650]);
				});
				$('#cad030RecriarSenhaBancoTodos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Recriar Senha Banco', '/cad/cad030/content/cad030ContentRecriarSenha/','', [300,200]);
				});				
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'usuario.C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'usuario.C007_Flag_Vendedor' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),
				array('title' => 'Normal', 'value' => 'N'),
				array('title' => 'Vendedor', 'value' => 'V'),
				array('title' => 'Comprador', 'value' => 'C'),
				array('title' => 'Supervisor', 'value' => 'S'),
				array('title' => 'Diretor', 'value' => 'D'),
			  ),
		   ),
	    );	
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/usuario.C007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário - ID: ' + id, '/cad/cad030/content/cad030contentCadastroUsuario/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&C007_Id=' + encodeURIComponent(id), [1000,650]);
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

	case 'cad030GridUsuariosFaixaAcessoIP':

		$clausula = "";
		if($g['usuarioAtual']!="63"){
			$clausula = "AND C007_Id!='63'";
		}

		$grid = array(
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário','style' => 'width:1%;'),
            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Nome completo','','style' => 'width:1%;'),
			array('colunaValor' => 'C007_IP_Acesso', 'colunaTitulo' => 'IP Acesso', 'colunaEditar' => true, 'colunaEditarTodos' => true ,'style' => 'width:120px;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),			
        );

		/**
		 * Definição da SQL
		 */
		$from = "C007";
		$extra = <<<EOT
			WHERE C007_Ativo = 'S'
			{$clausula}
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		/*$('#cad030RecriarSenhaBancoTodos').click(function(data) {
					var buttons = { 
						"Recriar para todos os usuários": function() { 
							$('#{$g['divId']}').showLoading();
							$.getJSON('/cad/cad030/form_func-ajax/recriarSenha/?ajax=true&callback=?', function(request) {
	      						$('#{$g['divId']}').hideLoading();
								dialogConfirm('Ok', request.data);
							});
							$(this).dialog("destroy").remove(); 
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Deseja continuar?', 'Este processo irá recriar a senha de acesso a nível de banco de dados.', buttons);
				});*/
		/*$btn = '';
		if ($g['usuarioAtual'] == 63) {
			$btn = '<button type="button" id="cad030RecriarSenhaBancoTodos">Recriar Senha Banco</button>';
		}
		
			<div class="buttonsBottomTop">
				<button type="button" id="cad030Incluir">Incluir</button>
				{$btn}
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad030Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário', '/cad/cad030/content/cad030contentCadastroUsuario/','', [1000,650]);
				});
				$('#cad030RecriarSenhaBancoTodos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Recriar Senha Banco', '/cad/cad030/content/cad030ContentRecriarSenha/','', [300,200]);
				});				
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'C007_Flag_Vendedor' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),
				array('title' => 'Normal', 'value' => 'N'),
				array('title' => 'Vendedor', 'value' => 'V'),
				array('title' => 'Comprador', 'value' => 'C'),
				array('title' => 'Supervisor', 'value' => 'S'),
				array('title' => 'Diretor', 'value' => 'D'),
			  ),
		   ),
	    );	
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/C007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário - ID: ' + id, '/cad/cad030/content/cad030contentCadastroUsuario/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&C007_Id=' + encodeURIComponent(id), [1000,650]);
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

	case 'cad030GridPerfilUsuario':
		$grid = array(
			array('colunaValor' => "excluirUsuário", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C019_Id', $g['divId'])),
			array('colunaValor' => 'C019_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'C019_C004_Id', 'colunaTitulo' => 'IdEmpresa', 'colunaExibir' => false),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'C019_C021_Id', 'colunaTitulo' => 'IdPerfil', 'colunaExibir' => false),
			array('colunaValor' => 'C021_Nome_Perfil', 'colunaTitulo' => 'Perfil'),
			array('colunaValor' => 'C019_Ordem_Empresa', 'colunaTitulo' => 'Ordem Acesso', 'colunaEditar' => true, 'style' => 'width:1%', 'colunaAlinhar' => 'center'),			
		);

		/**
		 * Definição da SQL
		 */
		$from = "C019";

		$extra = <<<EOT
			left join C004 on C004_Id=C019_C004_Id
			left join C021 on C021_Id=C019_C021_Id
			WHERE C019_C007_Id={$r_acaoId}
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		 * Filtro
		 */
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/C019_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}', true);			});
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


	case 'cad030GridUsuariosApi':
		global $g;

		$grid = array(
			array('colunaValor' => 'D178_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D178_Nome_Usuario', 'colunaTitulo' => 'Nome'),
			array('colunaValor' => 'D178_Descricao_Usuario', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'D178_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D178_Permissao_Produto', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D178_Permissao_Estoque_Quantidade', 'colunaTitulo' => 'Estoque (quantidade)', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D178_Permissao_Estoque_Disponibilidade', 'colunaTitulo' => 'Estoque (disponibilidade)', 'colunaAlinhar' => 'center'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "D178";

		$extra = <<<EOT
			LEFT JOIN C004 ON D178_C004_Id=C004_Id
			
EOT;

		/**
		 * Botões (Versão Acima do GRID)
		 */
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad030IncluirAPI">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad030IncluirAPI').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário API', '/cad/cad030/content/cad030ContentUsuarioApi/','', [780,300]);
				});				
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D178_Nome_Usuario' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'D178_Descricao_Usuario' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D178_Permissoes_Usuario' => array('titulo' => 'Permissões', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D178_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário API - ID: ' + id, '/cad/cad030/content/cad030ContentUsuarioApi/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [780,300]);
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

	case 'cad030GridUsuariosB2B':
		global $g;

		$grid = array(
			array('colunaValor' => "excluirUsuário", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D179_Id', $g['divId'])),
			array('colunaValor' => 'D179_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D179_Login', 'colunaTitulo' => 'Email'),
			array('colunaValor' => 'D179_Senha', 'colunaTitulo' => 'Senha'),
			array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante'),
			array('colunaValor' => 'vendedorInterno.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor Interno'),
			array('colunaValor' => 'vendedorExterno.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor Externo'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "D179";

		$extra = <<<EOT
			LEFT JOIN C007 as vendedorInterno ON D179_C007_Id=vendedorInterno.C007_Id
			LEFT JOIN C007 as vendedorExterno ON D179_C007_Vendedor_Externo_Id=vendedorExterno.C007_Id
			LEFT JOIN D021 ON D179_D021_Id=D021_Id

EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad030IncluirAPI'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad030IncluirAPI">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad030IncluirAPI').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário B2B', '/cad/cad030/content/cad030ContentUsuarioB2B/','', [500,300]);
				});				
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D179_Login' => array('titulo' => 'Email', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D179_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário B2B - ID: ' + id, '/cad/cad030/content/cad030ContentUsuarioB2B/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
			
	case 'cad030GridTokensB2B':
		global $g;

		$D179_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		$grid = array(
			array('colunaValor' => "excluirUsuário", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D180_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor' => 'D180_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D013_Nome_Contato', 'colunaTitulo' => 'Nome'),
			array('colunaValor' => 'D179_Login', 'colunaTitulo' => 'Email'),
			array('colunaValor' => 'D180_Token', 'colunaTitulo' => 'Token'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "D180";

		$extra = <<<EOT
			LEFT JOIN D179 ON D180_D179_Id=D179_Id
			LEFT JOIN D013 ON D013_E_Mail=D179_Login
			WHERE D180_D179_Id={$D179_Id}
			GROUP BY D180_Token
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);


// 		/**
// 		 * Botões (Versão Acima do GRID)
// 		 */
// 		echo <<<EOT
// 			<div class="buttonsBottomTop">
// 				<button type="button" id="cad030IncluirAPI">Incluir</button>
// 			</div>
// 			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
// 				$('#cad030IncluirAPI').unbind('click').bind('click', function(data) {
// 					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário B2B', '/cad/cad030/content/cad030ContentUsuarioB2B/','', [500,150]);
// 				});				
// 			} </script></div>
// EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D179_Login' => array('titulo' => 'Email', 'tipo' => 'texto'),
			'D180_Token' => array('titulo' => 'Token', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
// 		echo <<<EOT
// 		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// 			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
// 				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
// 				var id = $(this).closest('tr').attr('id').replace(/D178_Id-/, '');
// 				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário API - ID: ' + id, '/cad/cad030/content/cad030ContentUsuarioB2B/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,150]);
// 			});
// 		} </script></div>
// EOT;
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
			
		/**
		 * Definição do Grid (Tabela)
		 */
	case 'cad030GridUsuariosInativos':
		$grid = array(
            array('colunaValor' => 'excluirUsuário', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C007_Id', $g['divId'])),
            array('colunaValor' => 'usuario.C007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'usuario.C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'usuario.C007_Nome', 'colunaTitulo' => 'Nome completo'),
            array('colunaValor' => 'usuario.C007_Flag_Vendedor', 'colunaTitulo' => 'Tipo', 'callback' => 'campoTipoUsuario'),
            array('colunaValor' => 'usuario.C021_Nome_Perfil', 'colunaTitulo' => 'Empresa - Perfil', 'callback' => 'validaCampoPerfil', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'supervisor.C007_Primeiro_Nome', 'colunaTitulo' => 'Supervisor'),
            array('colunaValor' => 'usuario.C007_Data_Alteracao_Senha', 'colunaTitulo' => 'Última alteração de senha', 'callback' => 'gCorrigeData'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "C007 as usuario";
		$extra = <<<EOT
		    LEFT JOIN C007 as supervisor ON supervisor.C007_Id=usuario.C007_C007_Id
			WHERE usuario.C007_Ativo = 'N'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'usuario.C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/usuario.C007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário - ID: ' + id, '/cad/cad030/content/cad030contentCadastroUsuario/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&C007_Id=' + encodeURIComponent(id), [800,600]);
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

	/**
	 * Definição do Grid (Tabela)
	 */
	case 'cad030Feriados':

		echo "<div style='margin:auto;text-align:center;padding:2px;border:1px solid var(--celula-azul);background:var(--celula-azul);color:dark;width:99%;border-radius: 3px 3px 3px 3px;font-size:14px'><b>Tabela pode ser vinculada ao usuário para definir se tem acesso ao sistema em dias de feriado</b></div>";

		$grid = array(
            array('colunaValor' => 'D206_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'style' => 'width:1%'),
			array('colunaValor' => 'D206_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),	
            array('colunaValor' => 'D206_Descricao', 'colunaTitulo' => 'Descricao')	
		);

		/**
		 * Definição da SQL
		 */
		$from = "D206";
		$extra = <<<EOT
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D206_Flag_Ativo'] == 'N'){
				return 'linhaVermelho';
			}
		}
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad030IncluirFeriado')
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad030IncluirFeriado').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário', '/cad/cad030/content/cad030FeriadosContent/','', [300,200]);
			});
	
		} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D206_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário - ID: ' + id, '/cad/cad030/content/cad030FeriadosContentEdicao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,600]);
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

		/**
	 * Definição do Grid (Tabela)
	 */
	case 'cad030DataFeriados':

		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D207_Id', $g['divId'])),
            array('colunaValor' => 'D207_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'style' => 'width:1%'),
			array('colunaValor' => 'D207_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width:1%'),
            array('colunaValor' => 'D207_Descricao_Feriado', 'colunaTitulo' => 'Feriado')	
		);

		/**
		 * Definição da SQL
		 */
		$from = "D207";
		$extra = <<<EOT
			WHERE D207_D206_Id = '{$r_D206_Id}'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad030IncluirDataFeriado')
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad030IncluirDataFeriado').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Usuário', '/cad/cad030/content/cad030DataFeriadosContent/','&D206_Id=' + encodeURIComponent({$r_D206_Id}), [400,200]);
			});
	
		} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D207_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Usuário - ID: ' + id, '/cad/cad030/content/cad030DataFeriadosContent/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&D206_Id=' + encodeURIComponent({$r_D206_Id}), [400,200]);
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

		case 'cad030PerfilEmail':
			$grid = array(
								array('colunaValor' => 'excluirPerfil', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D202_Id', $g['divId'])),
								array('colunaValor' => 'D202_Id',          'colunaTitulo' => 'Id', 'chavePrimaria' => true),
								array('colunaValor' => 'C044_Descricao',   'colunaTitulo' => 'Descrição'),
								array('colunaValor' => 'C044_Observacao',  'colunaTitulo' => 'Observação'),
								array('colunaValor' => 'C021_Nome_Perfil', 'colunaTitulo' => 'Perfil Usuário'),
								array('colunaValor' => 'C007_Nome',        'colunaTitulo' => 'Usuário'),
							);

			$from = " D202 ";
			$extra  = " LEFT JOIN C044 ON D202.D202_C044_Id = C044.C044_Id ";
			$extra .= " LEFT JOIN C021 ON D202.D202_C021_Id = C021.C021_Id ";
			$extra .= " LEFT JOIN C007 ON D202.D202_C007_Id = C007.C007_Id ";
			$extra .= " ORDER BY C044_Descricao, C007_Id, C021_Id  ";

			$botoes = array(array('titulo' => 'Incluir', 'id' => 'cad030PerfilEmailIncluir'));
			echo Botoes::processar($botoes);

			$botoesAcao  = " <div class='divHidden'> ";
			$botoesAcao .=   " <script type='text/javascript'> ";
			$botoesAcao .=     " if (typeof jQuery != 'undefined') { ";
			$botoesAcao .=       " $('#cad030PerfilEmailIncluir').unbind('click').bind('click', function(data) { ";
			$botoesAcao .=         " abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Perfil de Email', '/cad/cad030/content/cad030PerfilEmailContent/','', [450,210]); ";
			$botoesAcao .=       " }); ";
			$botoesAcao .=     " } ";
			$botoesAcao .=   " </script> ";
			$botoesAcao .= " </div> ";
			echo $botoesAcao;
	
			/**
			 * Filtro
			 */
			$gridFiltro = array(
											'C044_Descricao'   => array('titulo' => 'Descrição',      'tipo' => 'texto'),
											'C021_Nome_Perfil' => array('titulo' => 'Perfil Usuário', 'tipo' => 'texto'),
											'C007_Nome'        => array('titulo' => 'Usuário',        'tipo' => 'texto')
										);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
	
			/**
			 * Define ação do click de abertura (ao clicar em uma Usuário do grid)
			 */
			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-Usuário')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D202_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Perfil de Email - ID: ' + id, '/cad/cad030/content/cad030PerfilEmailContent/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&D202_Id=' + encodeURIComponent(id), [450,210]);
				});
			} </script></div>
EOT;
			/**
			 * Totais
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

}
?>
