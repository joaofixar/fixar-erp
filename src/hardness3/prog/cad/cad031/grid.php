<?php
namespace hardness;

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');
// Carrega funções callback
require_once('prog/cad/cad031/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'cad031GridEmpresas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C004_Id', $g['divId'])),
			array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'C004_Nome_Empresa', 'colunaTitulo' => 'Nome Empresa'),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Nome Abreviado'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'C004_CEP', 'colunaTitulo' => 'Cep'),
			array('colunaValor' => 'C004_Cnpj', 'colunaTitulo' => 'Cnpj'),
			array('colunaValor' => 'C004_Inscricao_Estadual', 'colunaTitulo' => 'IE'),
			array('colunaValor' => 'C004_Ordem_Empresa', 'colunaTitulo' => 'Ordem acesso', 'colunaEditar' => true, 'colunaAlinhar' => 'center'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C004';
		$extra = <<<EOT
		left join D018 on D018_Id=C004_D018_Id
		left join D020 on D020_Id=C004_D020_Id
		where C004_Flag_Ativo="S"
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad031FormCadastroEmpresasBtnIncluir'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad031FormCadastroEmpresasBtnIncluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad031FormCadastroEmpresasBtnIncluir').unbind('click').bind('click', function(data) {
					if ("{$g['usuarioAtual']}"!="63") {
						dialogConfirm('Erro', 'Para inclusão de novas empresas favor entrar em contato com a Hardness no telefone 41 2626-1243, obrigado!');
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Empresas', '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&tab=geral', [930,700]);
					}
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C004_Nome_Empresa' => array('titulo' => 'Nome Empresa', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
					var chavePrimaria = 'C004_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Empresas - ID: ' + acaoId, '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela={$from}', [930,600]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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


	case 'cad031GridEmpresasInativas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C004_Id', $g['divId'])),
			array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'C004_Nome_Empresa', 'colunaTitulo' => 'Nome Empresa'),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Nome Abreviado'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'C004_CEP', 'colunaTitulo' => 'Cep'),
			array('colunaValor' => 'C004_Cnpj', 'colunaTitulo' => 'Cnpj'),
			array('colunaValor' => 'C004_Inscricao_Estadual', 'colunaTitulo' => 'IE'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C004';
		$extra = <<<EOT
		left join D018 on D018_Id=C004_D018_Id
		left join D020 on D020_Id=C004_D020_Id
		where C004_Flag_Ativo="N"
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad031FormCadastroEmpresasInativasBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad031FormCadastroEmpresasInativasBtnIncluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad031FormCadastroEmpresasInativasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Empresas', '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&tab=geral', [830,560]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C004_Nome_Empresa' => array('titulo' => 'Nome Empresa', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
					var chavePrimaria = 'C004_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Empresas - ID: ' + acaoId, '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela={$from}', [830,560]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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

	case 'cad031GridAvisosIndex':

		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;	
		$C004_Id = isset($_REQUEST['C004_Id']) ? $_REQUEST['C004_Id'] : false;
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C032_Id', $g['divId'])),
			array('colunaValor' => 'C032_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:30px;'),
			array('colunaValor' => 'Imagem', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'colunaTitulo' => 'Imagem', 'style' => 'width:50px;text-align: center;', 'callback' => 'mostrarImagemAvisoLinhas', 'colunaExibir' => false),
			array('colunaValor' => 'aviso', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Aviso', 'callback' => 'campoAviso', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'perfil', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Perfil', 'callback' => 'campoPerfil', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'C032_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'style' => 'width:2%;'),
			array('colunaValor' => 'abrirAviso', 'colunaTitulo' => 'Imagem', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoAbrirAviso', 'colunaAlinhar' => 'center', 'style' => 'width:2%;', 'colunaExibir' => false),
			array('colunaValor' => 'C032_Imagem', 'colunaTitulo' => 'Imagem', 'colunaExibir' => false),
			array('colunaValor' => 'C032_Aviso', 'colunaTitulo' => 'Aviso', 'colunaExibir' => false),
			array('colunaValor' => 'C032_Descricao', 'colunaTitulo' => 'Descricao','colunaAlinhar' => 'left', 'colunaEditar' => true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C032';
		$extra = <<<EOT
		WHERE C032_Flag_Ativo='S'	

		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad031FormAvisosIndex'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad031FormAvisosIndex">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad031FormAvisosIndex').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Avisos', '/cad/cad031/content/cad031ContentCadastroAvisosIndex/', '&tab=geral' + '&C004_Id=' + '{$C004_Id}', [830,500]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C032_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'C032_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Avisos- ID: ' + acaoId, '/cad/cad031/content/cad031ContentCadastroAvisosIndex/', '&tab=geral&acaoId=' + encodeURIComponent(acaoId) + '&C004_Id=' + '{$C004_Id}', [800,400]);
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

	case 'cad031GridAvisosPerfil':

		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;	
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C036_Id', $g['divId'])),
            array('colunaValor' => 'C036_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true,'colunaExibir' => false),
            array('colunaValor' => 'C036_C032_Id', 'colunaTitulo' => 'C032_Id', 'colunaExibir' => false),
            array('colunaValor' => 'C036_C021_Id', 'colunaTitulo' => 'C021_Id', 'colunaExibir' => false),
			array('colunaValor' => 'C004_Nome_Abreviado ', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'C021_Nome_Perfil ', 'colunaTitulo' => 'Perfil'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'C036_Data_Inicial', 'colunaTitulo' => 'Data inicial', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C036_Data_Final', 'colunaTitulo' => 'Data final', 'callback' => 'gCorrigeData'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'C036';
		$extra = <<<EOT
			LEFT JOIN C032 ON C036_C032_Id=C032_Id
			LEFT JOIN C021 ON C036_C021_Id=C021_Id
			LEFT JOIN C004 ON C004_Id=C036_C004_Id
			LEFT JOIN C007 ON C007_Id=C036_C007_Id
			WHERE C036_C032_Id = '$acaoId'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad031FormAvisosPerfil'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad031FormAvisosPerfil">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad031FormAvisosPerfil').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Perfil avisos', '/cad/cad031/content/cad031ContentFormAvisosPerfil/', '&tab=geral' + '&C032_Id=' + '{$acaoId}', [550,250]);
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'C036_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Aviso Perfil - ID: ' + acaoId, '/cad/cad031/content/cad031ContentFormAvisosPerfil/', '&tab=geral&C032_Id={$acaoId}&acaoId=' + encodeURIComponent(acaoId) , [550,250]);
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

	case 'cad031GridAvisosIndexInativos':

		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;	
		$C004_Id = isset($_REQUEST['C004_Id']) ? $_REQUEST['C004_Id'] : false;
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C032_Id', $g['divId'])),
			array('colunaValor' => 'C032_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:30px;'),
			array('colunaValor' => 'Imagem', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'colunaTitulo' => 'Imagem', 'style' => 'width:50px;text-align: center;', 'callback' => 'mostrarImagemAvisoLinhas', 'colunaExibir' => false),
			array('colunaValor' => 'aviso', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Aviso', 'callback' => 'campoAviso', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'perfil', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Perfil', 'callback' => 'campoPerfil', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'C032_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'style' => 'width:2%;'),
			array('colunaValor' => 'abrirAviso', 'colunaTitulo' => 'Imagem', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoAbrirAviso', 'colunaAlinhar' => 'center', 'style' => 'width:2%;', 'colunaExibir' => false),
			array('colunaValor' => 'C032_Imagem', 'colunaTitulo' => 'Imagem', 'colunaExibir' => false),
			array('colunaValor' => 'C032_Aviso', 'colunaTitulo' => 'Aviso', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C032';
		$extra = <<<EOT
		WHERE C032_Flag_Ativo='N'	

		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad031FormAvisosIndex'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad031FormAvisosIndex">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad031FormAvisosIndex').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Avisos', '/cad/cad031/content/cad031ContentCadastroAvisosIndex/', '&tab=geral' + '&C004_Id=' + '{$C004_Id}', [830,500]);
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'C032_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Avisos- ID: ' + acaoId, '/cad/cad031/content/cad031ContentCadastroAvisosIndex/', '&tab=geral&acaoId=' + encodeURIComponent(acaoId) + '&C004_Id=' + '{$C004_Id}', [800,400]);
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

}

?>