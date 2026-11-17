<?php
namespace hardness;
/**
 * CAD - CAD064 - Cadastro Veículos
*/
// Carrega funções callback
require_once('prog/cad/cad064/grid_func-callback.php');

switch($g['r'][3]){
	/**
	* @author Lígia Nakazato
	*/
	case 'cad064GridCadastroVeiculos':
		global $g;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D170_Id', $g['divId'])),
			array('colunaValor' => 'D170_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D170_Identificacao_Veiculo', 'colunaTitulo' => 'Identificação'),
            array('colunaValor' => 'D170_Placa', 'colunaTitulo' => 'Placa'),
            array('colunaValor' => 'D170_Renavam', 'colunaTitulo' => 'Renavam'),
            array('colunaValor' => 'D170_Tara', 'colunaTitulo' => 'Tara'),
            array('colunaValor' => 'D170_Capacidade_Kg', 'colunaTitulo' => 'Capacidade(Kg)'),
            array('colunaValor' => 'D170_Capacidade_M3', 'colunaTitulo' => 'Capacidade(M³)'),
            array('colunaValor' => 'D170_Flag_Rodado', 'colunaTitulo' => 'Tipo de rodado', 'callback' => 'validaCampoTipoRodado'),
            array('colunaValor' => 'D170_Flag_Carroceria', 'colunaTitulo' => 'Carroceria', 'callback' => 'validaCampoCarroceria'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D170_Categoria', 'colunaTitulo' => 'Categoria', 'callback' => 'validaCampoCategoria'),
            array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Motorista'),
            array('colunaValor' => 'D170_Flag_Ativo', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
        );

        /**
		* Definição da SQL
		*/
		$from = 'D170';
		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id = D170_D018_Id
			LEFT JOIN D109 ON D109_Id = D170_D109_Id
			WHERE D170_Flag_Ativo = 'S'
			AND D170_C004_Id = '{$g['empresaAtual']}'
			ORDER BY D170_Id
EOT;


		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
			<button type="button" id="cad064BtnCadastrarVeiculos">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad064BtnCadastrarVeiculos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Veículos', '/cad/cad064/content/cad064ContentFormCadastrarVeiculos/', '&tab=geral',  [550,550]);
				});
			} </script></div>
EOT;
				// $('#cad064BtnCadastrarMotorista').unbind('click').bind('click', function(data) {
				// 	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de Motorista', '/cad/cad010/content/cad010content02/', '&tab=geral&typeForm=cad064GridCadastroVeiculos',  [550,550]);
				// });
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D170_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
			'D170_Identificacao_Veiculo' => array('titulo' => 'Identificação', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D170_Placa' => array('titulo' => 'Placa', 'tipo' => 'texto'),
			'D170_Renavam' => array('titulo' => 'Renavam', 'tipo' => 'texto'),
            'D170_Categoria' => array('titulo' => 'Categoria', 'tipo' => 'select', 'select' => array(
                	array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Reboque', 'value' => '1'),
					array('title' => 'Tração', 'value' => '2'),
              	),
            ),
            'D170_Flag_Rodado' => array('titulo' => 'Tipo rodado', 'tipo' => 'select', 'select' => array(
                	array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Truck', 'value' => '01'),
					array('title' => 'Toco', 'value' => '02'),
					array('title' => 'Cavalo Mecânico', 'value' => '03'),
					array('title' => 'Van', 'value' => '04'),
					array('title' => 'Utilitário', 'value' => '05'),
					array('title' => 'Outros', 'value' => '06'),
              	),
            ),
            'D170_Flag_Carroceria' => array('titulo' => 'Tipo Carroceria', 'tipo' => 'select', 'select' => array(
                	array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Não aplicável', 'value' => '00'),
					array('title' => 'Aberta', 'value' => '01'),
					array('title' => 'Fechada/Baú', 'value' => '02'),
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),
              	),
            ),
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
					var chavePrimaria = 'D170_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Cadastro Veículos: ' + acaoId, '/cad/cad064/content/cad064ContentFormCadastrarVeiculos/', '&acaoId=' + encodeURIComponent(acaoId), [600,500]);
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

	case 'cad064GridVeiculosInativos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D170_Id', $g['divId'])),
			array('colunaValor' => 'D170_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D170_Identificacao_Veiculo', 'colunaTitulo' => 'Identificação'),
            array('colunaValor' => 'D170_Placa', 'colunaTitulo' => 'Placa'),
            array('colunaValor' => 'D170_Renavam', 'colunaTitulo' => 'Renavam'),
            array('colunaValor' => 'D170_Tara', 'colunaTitulo' => 'Tara'),
            array('colunaValor' => 'D170_Capacidade_Kg', 'colunaTitulo' => 'Capacidade(Kg)'),
            array('colunaValor' => 'D170_Capacidade_M3', 'colunaTitulo' => 'Capacidade(M³)'),
            array('colunaValor' => 'D170_Flag_Rodado', 'colunaTitulo' => 'Tipo de rodado', 'callback' => 'validaCampoTipoRodado'),
            array('colunaValor' => 'D170_Flag_Carroceria', 'colunaTitulo' => 'Carroceria', 'callback' => 'validaCampoCarroceria'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'D170_Categoria', 'colunaTitulo' => 'Categoria', 'callback' => 'validaCampoCategoria'),
            array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Motorista'),
            array('colunaValor' => 'D170_Flag_Ativo', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
        );

        /**
		* Definição da SQL
		*/
		$from = 'D170';
		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id=D170_D018_Id
			LEFT JOIN D109 ON D109_Id=D170_D109_Id
			WHERE D170_Flag_Ativo='N'
			ORDER BY D170_Id
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
			'D170_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
			'D170_Identificacao_Veiculo' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D170_Placa' => array('titulo' => 'Placa', 'tipo' => 'texto'),
			'D170_Renavam' => array('titulo' => 'Renavam', 'tipo' => 'texto'),
			'D170_Categoria' => array('titulo' => 'Categoria', 'tipo' => 'select', 'select' => array(
                	array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Reboque', 'value' => '1'),
					array('title' => 'Tração', 'value' => '2'),
              	),
            ),
            'D170_Flag_Rodado' => array('titulo' => 'Tipo rodado', 'tipo' => 'select', 'select' => array(
                	array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Truck', 'value' => '01'),
					array('title' => 'Toco', 'value' => '02'),
					array('title' => 'Cavalo Mecânico', 'value' => '03'),
					array('title' => 'Van', 'value' => '04'),
					array('title' => 'Utilitário', 'value' => '05'),
					array('title' => 'Outros', 'value' => '06'),
              	),
            ),
            'D170_Flag_Carroceria' => array('titulo' => 'Tipo Carroceria', 'tipo' => 'select', 'select' => array(
                	array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Não aplicável', 'value' => '00'),
					array('title' => 'Aberta', 'value' => '01'),
					array('title' => 'Fechada/Baú', 'value' => '02'),
					array('title' => 'Graneleira', 'value' => '03'),
					array('title' => 'Porta-conteiner', 'value' => '04'),
					array('title' => 'Sider', 'value' => '05'),
              	),
            ),
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
					var chavePrimaria = 'D170_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Cadastro Veículos: ' + acaoId, '/cad/cad064/content/cad064ContentFormCadastrarVeiculos/', '&acaoId=' + encodeURIComponent(acaoId), [600,500]);
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