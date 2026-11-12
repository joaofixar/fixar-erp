<?php
namespace hardness;

switch($g['r'][3]) {

	case 'API001GridConfApi':
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C033_Id', $g['divId']), 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C033_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C033_Nome', 'colunaTitulo' => 'API'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => 'C033_Login', 'colunaTitulo' => 'Login', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('colunaValor' => 'C033_Senha', 'colunaTitulo' => 'Senha', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('colunaValor' => 'C033_Token', 'colunaTitulo' => 'Token', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('colunaValor' => 'C033_Metodo', 'colunaTitulo' => 'Metodo', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('colunaValor' => 'C033_Url', 'colunaTitulo' => 'URL', 'colunaAlinhar' => 'left'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "C033";
		$extra = <<<EOT
		LEFT JOIN D024 ON D024_Id=C033_D024_Id
EOT;


		// Botões
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'API001Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
    	echo <<<EOT
			<div class="buttonsBottomTop">
				<!--button type="button" id="API001Incluir">Incluir</button-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#API001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'API', '/api/api001/content/API001ContentConfApiForm/','', [800,400]);
				});
			} </script></div>
EOT;

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'C033_Login' => array('titulo' => 'Login', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').replace(/C033_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar API', '/api/api001/content/API001ContentConfApiForm/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,400]);
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

	case 'API001GridAPIProcessos':
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C037_Id', $g['divId']), 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C037_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C037_Nome', 'colunaTitulo' => 'Nome', 'style' => 'width:90%'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "C037";
		$extra = <<<EOT
			WHERE C037_C033_Id = '$r_C033_Id'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
    	echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="API001IncluirProcesso">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#API001IncluirProcesso').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processo', '/api/api001/content/API001ContentConfApiProcessoForm/',"&C033_Id=$r_C033_Id", [1200,550]);
				});
			} </script></div>
EOT;

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'C037_Nome' => array('titulo' => 'Login', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').replace(/C037_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar API', '/api/api001/content/API001ContentConfApiProcessoForm/', '&acaoId=' + encodeURIComponent(id) + '&C033_Id=' + encodeURIComponent("$r_C033_Id")  + '&tabela={$from}', [800,400]);
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

	case 'API001GridAPICallback':
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C038_Id', $g['divId']), 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C038_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C038_Nome', 'colunaTitulo' => 'Nome', 'style' => 'width:90%'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "C038";
		$extra = <<<EOT
			WHERE C038_C033_Id = '$r_C033_Id'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
    	echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="API001IncluirCallback">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#API001IncluirCallback').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Callback', '/api/api001/content/API001ContentCallbackForm/',"&C033_Id=$r_C033_Id", [1200,550]);
				});
			} </script></div>
EOT;

        /**
		* Filtro
		*/
		$gridFiltro = array(
			'C037_Nome' => array('titulo' => 'Login', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').replace(/C038_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Callback', '/api/api001/content/API001ContentCallbackForm/', '&acaoId=' + encodeURIComponent(id) + '&C033_Id=' + encodeURIComponent("$r_C033_Id")  + '&tabela={$from}', [800,400]);
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
