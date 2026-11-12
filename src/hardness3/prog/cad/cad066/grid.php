<?php
namespace hardness;
/**
 * CAD - CAD066 - Cadastro Seguradoras
*/
// Carrega funções callback
require_once('prog/cad/cad066/grid_func-callback.php');

switch($g['r'][3]){
	/**
	* @author Lígia Nakazato
	*/
	case 'cad066GridCadastroSeguradoras':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
         	array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D172_Id', $g['divId'])),
			array('colunaValor' => 'D172_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D172_Nome', 'colunaTitulo' => 'Seguradora'),
            array('colunaValor' => 'D172_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D172_DDD_Telefone', 'colunaTitulo' => 'DDDTelefone', 'colunaExibir' => false),
            array('colunaValor' => 'D172_Telefone', 'colunaTitulo' => 'Telefone', 'colunaExibir' => false),
            array('colunaValor' => 'Telefone', 'colunaTitulo' => 'Telefone', 'colunaTipo' => 'livre', 'callback' => 'campoTelefone'),
            array('colunaValor' => 'D172_D020_Id', 'colunaTitulo' => 'consultaUF','colunaExibir' => false),
            array('colunaValor' => 'D172_D018_Id', 'colunaTitulo' => 'consultaCidade','colunaExibir' => false),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D172_Flag_Ativo', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
        );

        /**
		* Definição da SQL
		*/
		$from = 'D172';
		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id = D172_D018_Id
			LEFT JOIN D020 ON D020_Id = D172_D020_Id
			WHERE D172_Flag_Ativo = 'S'
			AND D172_C004_Id = '{$g['empresaAtual']}'
			ORDER BY D172_Id
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad066BtnCadastrarSeguradora">Incluir</button>
				</div>

				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
					$('#cad066BtnCadastrarSeguradora').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Seguradora', '/cad/cad066/content/cad066ContentFormCadastrarSeguradoras/', '&tab=geral&acao=novo',  [550,550]);
					});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D172_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
			'D172_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D172_Nome' => array('titulo' => 'Seguradora', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
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
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'D172_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Cadastro Seguradora: ' + acaoId, '/cad/cad066/content/cad066ContentFormCadastrarSeguradoras/', '&acao=alterar'+ '&acaoId=' + encodeURIComponent(acaoId), [600,300]);
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

	case 'cad066GridSeguradorasInativas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
         array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D172_Id', $g['divId'])),
			array('colunaValor' => 'D172_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D172_Nome', 'colunaTitulo' => 'Seguradora'),
            array('colunaValor' => 'D172_CNPJ', 'colunaTitulo' => 'CNPJ'),
            array('colunaValor' => 'D172_DDD_Telefone', 'colunaTitulo' => 'DDDTelefone', 'colunaExibir' => false),
            array('colunaValor' => 'D172_Telefone', 'colunaTitulo' => 'Telefone', 'colunaExibir' => false),
            array('colunaValor' => 'Telefone', 'colunaTitulo' => 'Telefone', 'colunaTipo' => 'livre', 'callback' => 'campoTelefone'),
            array('colunaValor' => 'D172_D020_Id', 'colunaTitulo' => 'consultaUF','colunaExibir' => false),
            array('colunaValor' => 'D172_D018_Id', 'colunaTitulo' => 'consultaCidade','colunaExibir' => false),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D172_Flag_Ativo', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
        );

        /**
		* Definição da SQL
		*/
		$from = 'D172';
		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id=D172_D018_Id
			LEFT JOIN D020 ON D020_Id=D172_D020_Id
			WHERE D172_Flag_Ativo='N'
			ORDER BY D172_Id
EOT;


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
			</script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D172_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
			'D172_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D172_Nome' => array('titulo' => 'Seguradora', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
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
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'D172_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Cadastro Seguradora: ' + acaoId, '/cad/cad066/content/cad066ContentFormCadastrarSeguradoras/', '&acao=alterar'+ '&acaoId=' + encodeURIComponent(acaoId), [600,300]);
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

	case 'gridCadastroApolices':
		$r_acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D173_Id', $g['divId'])),
			array('colunaValor' => 'D173_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D173_Identificacao', 'colunaTitulo' => 'Identificação'),
			array('colunaValor' => 'D173_Numero_Apolice', 'colunaTitulo' => 'Número Apólice'),
			array('colunaValor' => 'D173_Data_Inicio', 'colunaTitulo' => 'Data Início', 'callback' => 'gCorrigeData', 'colunaEditarOculto' => true),
			array('colunaValor' => 'D173_Data_Validade', 'colunaTitulo' => 'Válido até', 'callback' => 'gCorrigeData', 'colunaEditarOculto' => true),
			array('colunaValor' => 'D173_Ramo', 'colunaTitulo' => 'Ramo'),
			array('colunaValor' => 'D173_Flag_Ativo', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatusApolice'),
		  	array('colunaValor' => '(GROUP_CONCAT(DISTINCT concat(D174_Id,"|",D174_Descricao) SEPARATOR "<br>")) as averbacoes', 'colunaTitulo' => 'Averbações', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;', 'callback' => 'colunaAverbacoes'),
			array('colunaValor' => 'D173_D172_Id', 'colunaTitulo'=>'Seguradora', 'colunaExibir'=>false),
        );

        /**
		* Definição da SQL
		*/
		$from = 'D173';
		$extra = <<<EOT
			LEFT JOIN D172 on D172_Id=D173_D172_Id
			LEFT JOIN D174 on D173_Id=D174_D173_Id
			WHERE D173_D172_Id='{$r_acaoId}'
			GROUP BY D173_Id
			HAVING D173_Id IS NOT NULL
			ORDER BY D173_Id
EOT;


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
			<button type="button" id="cad066BtnCadastrarApolices">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad066BtnCadastrarApolices').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Apólices', '/cad/cad066/content/cad066ContentFormCadastrarApolices/', '&acao=novo&tab=geral&seguradora={$r_acaoId}',  [550,550]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D173_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
			'D173_Identificacao' => array('titulo' => 'Identificação', 'tipo' => 'texto'),
			'D173_Ramo' => array('titulo' => 'Ramo', 'tipo' => 'texto'),
			'D173_Numero_Apolice' => array('titulo' => 'Número Apólice', 'tipo' => 'texto'),
			'D173_Data_Validade' => array('titulo' => 'Data Validade', 'tipo' => 'data'),
			'D173_Data_Inicio' => array('titulo' => 'Data Início', 'tipo' => 'data'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		log("SQldas:".$geradoSql);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		log("valor total: ".$valorTotal);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'D173_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar cadastro apólice: ' + acaoId, '/cad/cad066/content/cad066ContentFormCadastrarApolices/', '&acao=alterar'+ '&acaoId=' + encodeURIComponent(acaoId), [930,583]);
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

	case 'cad066GridAverbacoes':
		$r_acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		log("acaoId= ".$r_acaoId);
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D174_Id', $g['divId'])),
			array('colunaValor' => 'D174_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D174_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D174_Numero_Averbacao', 'colunaTitulo' => 'Número Averbação'),
			array('colunaValor' => 'D174_D173_Id', 'colunaTitulo'=>'Apólice', 'colunaExibir'=>false),
        );
        /**
		* Definição da SQL
		*/
		$from = 'D174';
		$extra = <<<EOT
			LEFT JOIN D173 on D173_Id=D174_D173_Id
			LEFT JOIN D172 on D172_Id=D173_D172_Id
			WHERE D174_D173_Id='{$r_D173_Id}'
			ORDER BY D174_D173_Id
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
			<button type="button" id="cad066BtnCadastrarAverbacoes">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad066BtnCadastrarAverbacoes').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Averbações', '/cad/cad066/form/cad066FormCadastrarAverbacoes/', '&acao=novo&tab=geral&D173_Id={$r_D173_Id}',  [560,95]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D174_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D174_Numero_Averbacao' => array('titulo' => 'Número Averbação', 'tipo' => 'texto'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		log("SQldas:".$geradoSql);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		log("valor total: ".$valorTotal);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D174_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar cadastro averbação: ' + acaoId, '/cad/cad066/content/cad066ContentFormCadastrarAverbacoes/', '&acao=alterar'+ '&D173_Id={$r_D173_Id}&acaoId=' + encodeURIComponent(acaoId) + '&divRefresh={$g['divIdParent']}', [560,95]);
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
?>