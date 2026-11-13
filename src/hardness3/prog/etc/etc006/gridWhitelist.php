<?php
namespace hardness;

switch($g['r'][3]) {
  	case 'etc006GridWhitelist':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T258_Id', $g['divId'])),
 			array('colunaValor' => 'T258_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => 'T258_Registro', 'colunaTitulo' => 'Registro'),
 			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
 			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
 			array('colunaValor' => 'D024_Flag_Cliente', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'center'),
 			array('colunaValor' => 'D024_Flag_Fornecedor', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'center'),
 			array('colunaValor' => 'D024_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaAlinhar' => 'center'),
 			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
 			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
		);
		/**
		 * Definição da SQL
		 */
		$from = "T258";
		
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T258_D024_Id
			LEFT JOIN D020 ON D020_Id=D024_D020_Id
			LEFT JOIN D018 ON D018_Id=D024_D018_Id
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T258_Registro' => array('titulo' => 'Registro', 'tipo' => 'texto'),
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D024_Flag_Cliente' => array('titulo' => 'Cliente?', 'tipo' => 'texto'),
			'D024_Flag_Fornecedor' => array('titulo' => 'Fornecedor?', 'tipo' => 'texto'),
			'D024_Flag_Ativo' => array('titulo' => 'Ativo?', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'T258_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'EMAIL', 'value' => 'E'),
                array('title' => 'DOMÍNIO', 'value' => 'D'),
            )),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006ProcessarWhitelist">Processar</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006ProcessarWhitelist').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$(this).dialog("destroy").remove();
							$.getJSON('/etc/etc006/grid_func-ajax/processarWhitelist/?ajax=true&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
		    						dialogConfirm('Erro', 'Whitelist processada com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
		    						dialogConfirm('Erro', 'Erro ao processar whitelist', \$buttons);
								}
							});
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
		    		dialogConfirm('Atenção', 'Deseja processar a whitelist?', \$buttons);
				});

				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T258_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar registro whitelist', '/etc/etc006/content/etc006ContentEditarWhitelist/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,180]);
				});
			} </script></div>
EOT;

		if (!personalizacaoTotais()) {
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);			
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
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
