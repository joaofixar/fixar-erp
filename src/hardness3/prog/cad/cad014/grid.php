<?php
namespace hardness;
// Carrega funções callback
require_once('prog/cad/cad014/grid_func-callback.php');

// Carrega funções do ajax
//require_once('prog/cad/cad014/grid_func-js.php');
switch($g['r'][3]){
  	  case 'cad014GridPrincipal':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D007_Id', $g['divId'])),
            array('colunaValor' => 'D007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Conta'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D007_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D007_Nome_Contato', 'colunaTitulo' => 'Contato'),
            array('colunaValor' => 'D007_DDD_Telefone', 'colunaTitulo' => 'D007_DDD_Telefone', 'colunaExibir' => false),
            array('colunaValor' => 'D007_Telefone', 'colunaTitulo' => 'D007_Telefone', 'colunaExibir' => false),
            array('colunaValor' => 'TelefoneContato', 'colunaTitulo' => 'TelefoneContato', 'colunaTipo' => 'livre', 'callback' => 'campoTelefone'),
            array('colunaValor' => 'D007_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'D007_Valor_Maximo_Boleto_Sem_Registro', 'colunaTitulo' => 'Valor Máx. Boleto Sem Registro','colunaCasasDecimais' => '2'),
        );
		/**
		* Definição da SQL
		*/
		$from = 'D007';
		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id=D007_C004_Id
			WHERE D007_Flag_Ativo='S'
			AND D007_C004_Id = '{$g['empresaAtual']}'
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad014GridPrincipalBtnIncluir')
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad014GridPrincipalBtnIncluir">Incluir</button>
			</div> -->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad014GridPrincipalBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de conta', '/cad/cad014/content/cad014ContentEditarConta/', '&tab=geral', [900,550]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D007_Nome_Banco' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D007_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D007_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
			'D007_Telefone' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
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
         * Totais
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
				<span class="item" style="text-align:right">Total Bancos/Caixas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'D007_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastro de Conta - ID: ' + acaoId, '/cad/cad014/content/cad014ContentEditarConta/', '&acaoId=' + encodeURIComponent(acaoId), [900,550]);
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
	
	case 'cad014GridContasInativas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Exclusão automática de elementos no GRID
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D007_Id', $g['divId'])),
			// Chave Primária
			array('colunaValor' => 'D007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			// Campos
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Contas'),
			array('colunaValor' => 'D007_CNPJ', 'colunaTitulo' => 'CNPJ'),
			array('colunaValor' => 'D007_Nome_Contato', 'colunaTitulo' => 'Contato'),
			array('colunaValor' => 'D007_DDD_Telefone', 'colunaTitulo' => 'D007_DDD_Telefone', 'colunaExibir' => false),
			array('colunaValor' => 'D007_Telefone', 'colunaTitulo' => 'D007_Telefone', 'colunaExibir' => false),
			array('colunaValor' => 'TelefoneContato', 'colunaTitulo' => 'TelefoneContato', 'colunaTipo' => 'livre', 'callback' => 'campoTelefone'),
			array('colunaValor' => 'D007_Observacao', 'colunaTitulo' => 'Observação'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D007';
		$extra = <<<EOT
			WHERE D007_Flag_Ativo='N'
			AND D007_C004_Id = '{$g['empresaAtual']}'
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
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad014GridPrincipalBtnIncluir">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// $('#cad014GridPrincipalBtnIncluir').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de conta', '/cad/cad014/content/cad014ContentEditarConta/', '&tab=geral', [900,550]);
				// });
			// } </script></div>
// EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D007_Nome_Banco' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D007_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D007_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
			'D007_Telefone' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
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
         * Totais
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
				<span class="item" style="text-align:right">Total Bancos/Caixas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D007_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Titulo da Janela - ID: ' + acaoId, '/cad/cad014/content/cad014ContentEditarConta/', '&acaoId=' + encodeURIComponent(acaoId), [900,550]);
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
  	
  	case 'cad014GridCadastroBanco':
  		$grid = array(
 			array('colunaValor' => 'D062_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false ),
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D062_Id', $g['divId'])),
			array('colunaValor' => 'D062_Nome_Banco', 'colunaTitulo' => 'Banco' ),
			array('colunaValor' => 'D062_Numero_Banco', 'colunaTitulo' => 'Número do Banco'),
     	);
     	/**
		 * Definição da SQL
		 */
		$from = "D062";
		
		$extra = <<<EOT
EOT;	
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad014Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad014Incluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad014Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Banco', '/cad/cad014/content/cad014contentCadastroBanco/','', [500,500]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D062_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
			'D062_Numero_Banco' => array('titulo' => 'Numero do Banco', 'tipo' => 'texto')
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
				var id = $(this).closest('tr').attr('id').replace(/D062_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Banco - ID: ' + id, '/cad/cad014/content/cad014contentCadastroBanco/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,170]);
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
  }

?>
