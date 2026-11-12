<?php
namespace hardness;

require_once('prog/fis/fis005/grid_func-callback.php');
switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'fis005GridCidades':
		$grid = array(
            array('colunaValor' => 'D020_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D020_Codigo_IBGE', 'colunaTitulo' => 'IBGE'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Trans. Padrão'),
        );

		/**
		 * Definição da SQL
		 */
		$from = "D020";
		
		$extra = <<<EOT
			left join D018 on D018_Id=D020_D018_Id
			left join D022 on D022_Id=D020_D022_Id
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis005Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="fis005Incluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis005Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Cidade', '/fis/fis005/content/fis005contentCadastroCidade/','', [400,200]);
				});
			} </script></div>
EOT;
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D020_Codigo_IBGE' => array('titulo' => 'Código IBGE', 'tipo' => 'numero'),
			'D022_Nome_Empresa' => array('titulo' => 'Trans. Padrão', 'tipo' => 'texto'),
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
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Cidades<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D020_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cidade - ID: ' + id, '/fis/fis005/content/fis005contentCadastroCidadeEdicao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,400]);
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

  	case 'fis005GridCep':
		global $g;
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		$grid = array(
			array('colunaValor' => 'campoExcluiCep', 'colunaTipo' => 'livre', 'callback' => 'excluirCep', 'style' => 'width:1%'),
            array('colunaValor' => 'W004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'W004_Cep', 'colunaTitulo' => 'CEP'),
			array('colunaValor' => 'W004_Logradouro', 'colunaTitulo' => 'Logradouro'),
			array('colunaValor' => 'W004_Bairro', 'colunaTitulo' => 'Bairro'),
			array('colunaValor' => 'W004_Cidade', 'colunaTitulo' => 'Cidade'),
			array('colunaValor' => 'W004_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'W004_IBGE_Cidade', 'colunaTitulo' => 'Cidade IBGE'),
			array('colunaValor' => 'W004_IBGE_UF', 'colunaTitulo' => 'UF IBGE'),
        );

		/**
		 * Definição da SQL
		 */
		$from = "Global.w004";
		
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis005IncluirCep'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="fis005IncluirCep">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis005IncluirCep').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir CEP', '/fis/fis005/content/fis005contentCadastroCep/','', [450,300]);
				});
			} </script></div>
EOT;
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'W004_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'W004_Cep' => array('titulo' => 'CEP', 'tipo' => 'texto'),
			'W004_Logradouro' => array('titulo' => 'Logradouro', 'tipo' => 'texto'),
			'W004_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'W004_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'W004_UF' => array('titulo' => 'UF', 'UF' => 'texto'),
			'W004_IBGE_Cidade' => array('titulo' => 'Cidade IBGE', 'tipo' => 'texto'),
			'W004_IBGE_UF' => array('titulo' => 'UF IBGE', 'tipo' => 'texto'),
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
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">CEP<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/W004_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar CEP - ID: ' + id, '/fis/fis005/content/fis005contentCadastroCepEdicao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [450,300]);
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
  	
	case 'fis005GridBairros':
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D139_Id', $g['divId'])),
 			array('colunaValor' => 'D139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false ),
			array('colunaValor' => 'D139_Bairro', 'colunaTitulo' => 'Nome do Bairro' ),
			array('colunaValor' => 'D084_Regiao', 'colunaTitulo' => 'Região'),
			array('colunaValor' => 'D139_D020_Id', 'colunaExibir' => false),
     );
		
		/**
		 * Definição da SQL
		 */
		$from = "D139";
		
		$extra = <<<EOT
				left join D084 on D084_Id=D139_D084_Id
			    where D139_D020_Id={$r_acaoId}
EOT;
	 		/**
			* Botões Almir
			*/
			$botoes = array(
				array('titulo' => 'Incluir', 'id' => 'fis005IncluirBairro'),
			);
			echo Botoes::processar($botoes);

			/**
			* Botões (Versão Acima do GRID)
			*/
			
			/*<div class="buttonsBottomTop">
				<button type="button" id="fis005IncluirBairro">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis005IncluirBairro').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Bairro', '/fis/fis005/content/fis005contentCadastroBairro/','&idCidade=' + encodeURIComponent({$r_acaoId}), [300,170]);
				});
			} </script></div>
EOT;

			/**
			* Filtro
			*/
			$gridFiltro = array(
				'D139_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
				'D084_Regiao' => array('titulo' => 'Região', 'tipo' => 'texto'),
			);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
				echo $g['ui']->gridFiltroPrint($gridFiltro);
				
			/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Bairros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;*/
						
			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			$novoIdTelaForm = uniqid();
			echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D139_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Bairro', '/fis/fis005/content/fis005contentCadastroBairro/', '&idCidade=&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [300,170]);
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
			* Botões (Versão Acima do GRID)
			*/

					
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
