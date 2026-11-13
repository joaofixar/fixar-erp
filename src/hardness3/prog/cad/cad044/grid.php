<?php
namespace hardness;
require_once('prog/cad/cad044/grid_func-callback.php');

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author Weiberlan
	* @since 30/11/2011
	*/                 
	case 'cad044GridTextosDeDocumentosGeral':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D130_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D130_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D130_Conteudo', 'colunaTitulo' => 'Conteúdo','callback' => 'exibirCodigoHTML'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D130';
		$extra = <<<EOT
		where D130_Grupo='GERAL'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad044FormCadastroTextosDeDocumentosBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad044FormCadastroTextosDeDocumentosBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Textos De Documentos', '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&tab=geral', [500,500]);
				});
			} </script></div>
EOT;
*/
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D130_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
			'D130_Conteudo' => array('titulo' => 'Conteúdo', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Textos De Documentos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
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
					var chavePrimaria = 'D130_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Textos De Documentos - ID: ' + acaoId, '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	
	case 'cad044GridTextosDeDocumentosReciboLocacao':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D130_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D130_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D130_Conteudo', 'colunaTitulo' => 'Conteúdo','callback' => 'exibirCodigoHTML'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D130';
		$extra = <<<EOT
		WHERE D130_Grupo='RECIBO_LOCACAO'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad044FormCadastroTextosDeDocumentosBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad044FormCadastroTextosDeDocumentosBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Textos De Documentos', '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&tab=geral', [500,500]);
				});
			} </script></div>
EOT;
*/
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D130_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
			'D130_Conteudo' => array('titulo' => 'Conteúdo', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Textos De Documentos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D130_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Textos De Documentos - ID: ' + acaoId, '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	
	case 'cad044GridTextosDeDocumentosCertificadoInspecao':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D130_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D130_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D130_Conteudo', 'colunaTitulo' => 'Conteúdo','callback' => 'exibirCodigoHTML'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D130';
		$extra = <<<EOT
		WHERE D130_Grupo='CERT_INSPECAO'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad044FormCadastroTextosDeDocumentosBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad044FormCadastroTextosDeDocumentosBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Textos De Documentos', '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&tab=geral', [500,500]);
				});
			} </script></div>
EOT;
*/
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D130_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
			'D130_Conteudo' => array('titulo' => 'Conteúdo', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Textos De Documentos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D130_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Textos De Documentos - ID: ' + acaoId, '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	
	case 'cad044GridTextosDeDocumentosCertificadoConformidade':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D130_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D130_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D130_Conteudo', 'colunaTitulo' => 'Conteúdo','callback' => 'exibirCodigoHTML'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D130';
		$extra = <<<EOT
		WHERE D130_Grupo='CERT_CONFORMIDADE'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad044FormCadastroTextosDeDocumentosBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad044FormCadastroTextosDeDocumentosBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Textos De Documentos', '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&tab=geral', [500,500]);
				});
			} </script></div>
EOT;
*/
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D130_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
			'D130_Conteudo' => array('titulo' => 'Conteúdo', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Textos De Documentos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D130_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Textos De Documentos - ID: ' + acaoId, '/cad/cad044/content/cad044ContentCadastroTextosDeDocumentos/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
}

?>
