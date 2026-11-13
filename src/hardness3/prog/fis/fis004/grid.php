<?php
namespace hardness;
	
	/**
	* @author RubiaYwata
	* @since 18/11/2011
	*/
	
	switch ($g['r'][3]) {
   		
		case 'fis004GridGrupo':
				
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T025_Id', $g['divId'])),
				array('colunaValor' => 'T025_Id', 'colunaTitulo' => 'Id','chavePrimaria' => true,'colunaExibir' =>false),
				array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'Class. Fiscal'),
				array('colunaValor' => 'T025_D002_Id', 'colunaTitulo' => 'IdSubgrupo','colunaExibir' => false),
				array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'Subgrupo'),
				array('colunaValor' => 'T025_D015_Id', 'colunaTitulo' => 'IdAplicação','colunaExibir' => false),
				array('colunaValor' => 'D015_Nome_Grupo', 'colunaTitulo' => 'Aplicação'),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T025';

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
						var chavePrimaria = 'D005_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('-');
						var acaoId = id[1].replace(chavePrimaria , '-', '');
						// Ação: abrir nova janela
	        			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Grupo/Aplicação/Classificação - ID: ' + acaoId, '/fis/fis004/content/fis004ContentNCMporGrupoIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
			        });
					$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
				} </script></div>
EOT;

			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			 
			 
			 $extra = <<<EOT
			 left join D005 on D005_Id=T025_D005_Id
			 left join D002 on D002_Id=T025_D002_Id
			 left join D015 on D015_Id=T025_D015_Id
			 order by D005_Classificacao_Fiscal
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis004GridGrupoBtnIncluir'),
		);
		echo Botoes::processar($botoes);

			 
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*
			<div class="buttonsBottomTop">
				<button type="submit" id="fis004GridGrupoBtnIncluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				  $('#fis004GridGrupoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Grupo/Aplicação/Classificação', '/fis/fis004/content/fis004ContentNCMporGrupoIncluir/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;

			/**
			* Filtro
			*/
			$gridFiltro = array(
				'D005_Classificacao_Fiscal' => array('titulo' => 'Classificação Fiscal', 'tipo' => 'texto'),
				'D002_Descricao_Produto' => array('titulo' => 'Subgrupo', 'tipo' => 'texto'),
				'D015_Nome_Grupo' => array('titulo' => 'Aplicação', 'tipo' => 'texto'),
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

			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">NCM's por Grupo<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

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
