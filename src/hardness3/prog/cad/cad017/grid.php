<?php
namespace hardness;

// Carrega funções callback
require_once('prog/cad/cad016/grid_func-callback.php');
require_once('prog/cad/cad017/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/2011
	*/                 
	case 'cad017GridPortos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D118_Id', $g['divId'])),
			array('colunaValor' => 'D118_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D118_Nome', 'colunaTitulo' => 'Porto'),
			array('colunaValor' => 'D118_Cidade', 'colunaTitulo' => 'Cidade'),
			array('colunaValor' => 'D118_Estado', 'colunaTitulo' => 'Estado'),
			array('colunaValor' => 'D118_D030_Id', 'colunaTitulo' => 'País', 'colunaExibir'=>false),
			array('colunaValor' => 'D030_Nome_Pais', 'colunaTitulo' => 'Pais'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D118';
		
		$extra = <<<EOT
			left join D030 on D030_Id=D118_D030_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad017FormPortosBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad017FormPortosBtnIncluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad017FormPortosBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Portos', '/cad/cad017/content/cad017ContentPortosIncluir/', '&tab=geral', [500,250]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D118_Nome' => array('titulo' => 'Porto', 'tipo' => 'texto'),
			'D118_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D118_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
			'D030_Nome_Pais' => array('titulo' => 'País', 'tipo' => 'texto'),
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
					var chavePrimaria = 'D118_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Portos - ID: ' + acaoId, '/cad/cad017/content/cad017ContentPortosIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [500,250]);
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

	case 'cad017GridTipoDespesas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D190_Id', $g['divId'])),
			array('colunaValor' => 'D190_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => "width: 1%;"),
			array('colunaValor' => 'D190_Tipo_Despesa', 'colunaTitulo' => 'Pacote'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D190';
		
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad017FormTipoDespesasBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad017FormTipoDespesasBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pacote', '/cad/cad017/content/cad017ContentTipoDespesasComexIncluir/', '&tab=geral');
			});
		} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D190_Tipo_Despesa' => array('titulo' => 'Pacote', 'tipo' => 'texto'),

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
					var chavePrimaria = 'D190_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pacote - ID: ' + acaoId, '/cad/cad017/content/cad017ContentTipoDespesasComexIncluir/', '&acaoId=' + encodeURIComponent(acaoId));
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

	case 'cad017GridDespesas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D191_Id', $g['divId'])),
			array('colunaValor' => 'D191_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => "width: 1%;"),
			array('colunaValor' => 'D191_Descricao_Despesa', 'colunaTitulo' => 'Despesa'),
			array('colunaValor' => 'D191_Tipo_Despesa', 'colunaTitulo' => 'Tipo Despesa', 'callback' => 'tipoDespesaComex'),
			array('colunaValor' => 'D191_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'tipoComex'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D191';
		
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad017FormDespesasBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad017FormDespesasBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Despesa', '/cad/cad017/content/cad017ContentDespesasComexIncluir/', '&tab=geral', [600,200]);
			});
		} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D191_Descricao_Despesa' => array('titulo' => 'Despesa', 'tipo' => 'texto'),

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
					var chavePrimaria = 'D191_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Despesa - ID: ' + acaoId, '/cad/cad017/content/cad017ContentDespesasComexIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [600,200]);
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

	case 'cad017GridPacoteDespesas':

		$D190_Id = isset($r_acaoId) ? $r_acaoId : false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D192_Id', $g['divId'])),
			array('colunaValor' => 'D192_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => "width: 1%;"),
			array('colunaValor' => 'D191_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D190_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D192_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaEditarJSCallback' => 'colunaEditarCallbackJS_D192_Ordem', 'colunaEditar' => true),
			array('colunaValor' => 'D190_Tipo_Despesa', 'colunaTitulo' => 'Pacote'),
			array('colunaValor' => 'D191_Descricao_Despesa', 'colunaTitulo' => 'Despesa','colunaAlinhar' => 'left'),
			array('colunaValor' => 'D192_Flag_Tipo_Rateio', 'colunaTitulo' => 'Rateio','colunaAlinhar' => 'center','callback' => 'tipoRateio'),
			array('colunaValor' => 'D192_Flag_Base_Rateio', 'colunaTitulo' => 'Base rateio','colunaAlinhar' => 'center','callback' => 'baseRateio'),
			array('colunaValor' => 'D192_Flag_ME', 'colunaTitulo' => 'ME','colunaAlinhar' => 'center'),
			array('colunaValor' => 'D192_Flag_Tipo', 'colunaTitulo' => 'Tipo','callback' => 'tipoDespesa'),
			array('colunaValor' => 'D192_Aliquota', 'colunaTitulo' => 'Alíquota', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Valor', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Base_IPI', 'colunaTitulo' => 'Base IPI','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Base_ICMS', 'colunaTitulo' => 'Base ICMS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Base_PIS', 'colunaTitulo' => 'Base PIS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Base_COFINS', 'colunaTitulo' => 'BAse COFINS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Base_II', 'colunaTitulo' => 'Base II','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Custo', 'colunaTitulo' => 'Custo','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'D192_Flag_Soma_Preco_NFe', 'colunaTitulo' => 'Preço NFe','colunaAlinhar' => 'center','colunaEditar' => true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D192';
		
		$extra = <<<EOT
			LEFT JOIN D190 ON D190_Id=D192_D190_Id
			LEFT JOIN D191 ON D191_Id=D192_D191_Id
			WHERE D192_D190_Id = '{$D190_Id}'
			ORDER BY D192_Ordem
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad017FormPacoteDespesasBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad017FormPacoteDespesasBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pacote despesa', '/cad/cad017/content/cad017ContentPacoteDespesasComexIncluir/', '&tab=geral&D190_Id={$D190_Id}', [650,490]);
			});

			function colunaEditarCallbackJS_D192_Ordem(D192_Id, campoEditado, valorAntigo, novoValor) {
				D192_Id = D192_Id.split('|');
				D192_Id = D192_Id[0];
				D192_Id = D192_Id.replace(/D192_Id-/, '');
				$.getJSON('/cad/cad017/grid_func-ajax/processarOrdemDespesa/?ajax=true&D192_Id='    + encodeURIComponent(D192_Id) + 
																				  '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				  '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				  '&novoValor='			+ encodeURIComponent(novoValor) +  
																				  '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}');
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			}
		} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D191_Descricao_Despesa' => array('titulo' => 'Despesa', 'tipo' => 'texto'),
			'D192_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
                array('title' => 'Valor fixo', 'value' => 'V'),
                array('title' => 'Fórmula', 'value' => 'F'),
              ),
              'naoGerarExtra' => ''),
			'D192_Flag_Tipo_Rateio' => array('titulo' => 'Rateio', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Custo', 'value' => 'C'),
				array('title' => 'Peso', 'value' => 'P'),
				),
			'naoGerarExtra' => ''),
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
					var chavePrimaria = 'D192_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pacote despesas - ID: ' + acaoId, '/cad/cad017/content/cad017ContentPacoteDespesasComexIncluir/', '&acaoId=' + encodeURIComponent(acaoId) + '&D190_Id={$D190_Id}', [650,490]);
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
}

?>