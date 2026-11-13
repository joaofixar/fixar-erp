<?php
namespace hardness;
require_once("bibliotecas/classes/FIS003.php");
$FIS003 = new FIS003();
switch ($g['r'][3]) {
	case 'fis003GridNcm':
		/**
		* Definição do Grid (Tabela)
		*/

        $importacaoDados=array('D149_C004_Id' => $g['empresaAtual']);
		
		$grid = array(
            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D149_Id', $g['divId']), 'colunaExibir' => true),
            array('colunaValor' => 'D149_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D005_Id', 'colunaTitulo' => 'D005_Id ', 'chavePrimaria' => true, 'colunaExibir' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false ),
            array('colunaValor' => 'D149_D005_Id', 'colunaTitulo' => 'Id NCM', 'colunaExibir' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D005_Flag_Aplicacao', 'colunaTitulo' => 'EX', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D149_Codigo_CEST', 'colunaTitulo' => 'CEST', 'style' => 'width: 1%', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D149_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Valor_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Flag_ST', 'colunaTitulo' => 'ST?', 'colunaAlinhar' => 'center','colunaEditarOculto' => true, 'colunaEditarTodos' => true,  'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Aliquota_IRPJ', 'colunaTitulo' => '%IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarOcultoTodos' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Aliquota_CSLL', 'colunaTitulo' => '%CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarOcultoTodos' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D149_Percentual_Trib_Transparencia', 'colunaTitulo' => '% Aprox. Trib.', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D149_Imposto_Importacao', 'colunaTitulo' => '%II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D149_Importacao_IPI', 'colunaTitulo' => '%IPI Imp', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D149_Importacao_PIS', 'colunaTitulo' => '%PIS Imp', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D149_Importacao_COFINS', 'colunaTitulo' => '%COFINS Imp', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D088_Codigo_ST', 'colunaTitulo' => 'CST', 'colunaExibir' => false),
            array('colunaValor' => 'D005_Observacao', 'colunaTitulo' => 'Descrição', 'colunaEditarOcultoUppercase' => 'true', 'style' => 'min-width:350px;max-width:350px;white-space:normal;', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D149_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditarOcultoUppercase' => 'true', 'style' => 'min-width:350px;max-width:350px;white-space:normal;', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D149_Regras_MVA', 'colunaTitulo' => 'Exceções venda', 'colunaEditarOculto' => true, 'colunaEditarOcultoTodos' => true, 'colunaEditarOcultoUppercase' => 'true', 'style' => 'max-width:450px;white-space:normal;'),
            array('colunaValor' => 'D149_Regras_MVA_Compra', 'colunaTitulo' => 'Exceções compra', 'colunaEditarOculto' => true, 'colunaEditarOcultoTodos' => true, 'colunaEditarOcultoUppercase' => 'true', 'style' => 'max-width:450px;white-space:normal;'),
            array('colunaValor' => 'D149_UF_ST', 'colunaTitulo' => 'UF ST Venda', 'colunaEditarOculto' => true, 'colunaEditarOcultoTodos' => true, 'colunaEditarOcultoUppercase' => 'true', 'style' => 'max-width:300px;white-space:normal;'),
            array('colunaValor' => 'D149_UF_ST_Compras', 'colunaTitulo' => 'UF ST Compra', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'colunaEditarUppercase' => 'true', 'style' => 'max-width:300px;white-space:normal;'),
            array('colunaValor' => 'D149_Indicador_Escala', 'colunaTitulo' => 'Ind. de Escala','colunaEditarTodos' => true, 'callback' => 'retornaIndEscala', 'colunaEditarUppercase' => 'true', 'style' => 'max-width:300px;white-space:normal;',
        	'colunaEditarGeraSelect' => array(
                array('title' => '...', 'value' => ''),
                array('title' => 'Relevante', 'value' => 'S'),
                array('title' => 'Não Relevante', 'value' => 'N')
            )),
            array('colunaValor' => 'D149_C004_Id', 'colunaTitulo' => 'Ativo', 'colunaExibir' => false),
        );
         
		/**
		* Definição da SQL
		*/
		$from = 'D149';

		$extra = <<<EOT
			LEFT JOIN D005 ON D005_Id=D149_D005_Id
			LEFT JOIN D088 ON D088_Id=D005_D088_Id
			WHERE D149_C004_Id = '{$g['empresaAtual']}'
			AND D005_Flag_Ativo!="N"
			ORDER BY D005_Classificacao_Fiscal
EOT;

        $retornoNCMDuplicada = $FIS003->verificarNCMDuplicadas();
		if ($retornoNCMDuplicada) {
        	echo "<div style='margin:auto;text-align:center;padding:2px;border:1px solid red;background:#fc5a5a;color:white;width:99%;border-radius: 3px 3px 3px 3px;font-size:14px'>" . $retornoNCMDuplicada . "</div>";
		}

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);


		//if (!personalizacaoBotoes()) {
			/**
			* Botões (Versão Acima do GRID)
			*/
/*			echo <<<EOT
				<div class="buttonsBottomTop" $float>
					<button type="button" id="fis003GridProcessar_btn">Aplicar regras ST nas NCM´s a partir dos grupos</button>
				</div>
				{$mensagem}
				
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			        $('#fis003GridProcessar_btn').unbind('click').bind('click', function() {
			            var buttons = {
			                "Sim": function() {
			                    $('#{$g['divId']}').showLoading();
			                    $.getJSON('/fis/fis003/grid_func-ajax/processarNCM/?ajax=true&callback=?', function(request) {
			                        $('#{$g['divId']}').hideLoading();
			                        if (request.code) {
			                            dialogConfirm('Ok!', '<b>Processo concluído</b> ');
			                            divRefresh('{$g['divId']}');
			                        } else {
			                            dialogConfirm('Erro!', request.data);
			                        }
			                    });
			                    $(this).dialog("destroy").remove();
			                },
			                "Não": function() { $(this).dialog("destroy").remove(); }
			            }
			            dialogConfirm('Confirmação', 'Iniciar processo?', buttons);
			        });
				} </script></div>
EOT;*/
		//}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'numero'),
            'D005_Flag_Aplicacao' => array('titulo' => 'EX', 'tipo' => 'texto'),
            'D005_Observacao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D149_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
            'D149_Aliquota_IPI' => array('titulo' => 'IPI', 'tipo' => 'numero'),
            'D149_Aliquota_PIS' => array('titulo' => 'PIS', 'tipo' => 'numero'),
            'D149_Aliquota_COFINS' => array('titulo' => 'COFINS', 'tipo' => 'numero'),
            'D149_Valor_VA' => array('titulo' => 'MVA', 'tipo' => 'numero'),
            'D149_Regras_MVA' => array('titulo' => 'Ex.Venda', 'tipo' => 'texto'),
            'D149_Regras_MVA_Compra' => array('titulo' => 'Ex.Compra', 'tipo' => 'texto'),
            'D149_UF_ST' => array('titulo' => 'UF ST Ven', 'tipo' => 'texto'),
            'D149_UF_ST_Compras' => array('titulo' => 'UF ST Com', 'tipo' => 'texto'),
            'D005_Flag_Ativo' => array('titulo' => 'Ativo?(S/N)', 'tipo' => 'texto'),
            'D149_Percentual_Trib_Transparencia' => array('titulo' => '%Aprox.Trib.', 'tipo' => 'texto'),
            'D149_Codigo_CEST' => array('titulo' => 'CEST', 'tipo' => 'texto'),
            'D149_Flag_ST' => array('titulo' => 'ST (S/N)', 'tipo' => 'texto'),

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
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">NCM's<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {            
				acaoClickLinha('{$g['divId']}', function(data, array) {
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Regra da NCM para esta empresa', '/fis/fis003/content/fis003ContentNCMAlterar/', '&acaoId=' + encodeURIComponent(array['todosCampos']['D149_Id']) + '&tabela={$from}', [950,630]);
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

	case 'fis003GridD005':
		/**
		* Definição do Grid (Tabela)
		*/

		$importacaoDados=array();
		
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D005_Id', $g['divId']), 'colunaExibir' => true),
            array('colunaValor' => 'D005_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D005_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D005_Flag_Aplicacao', 'colunaTitulo' => 'EX', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
			/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 03/12/2025 
			=============================================================================*/
            array('colunaValor' => 'D005_Situacao_Tributaria_CBS_IBS', 'colunaTitulo' => 'CST CBS/IBS', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D005_Classificacao_Tributaria_CBS_IBS', 'colunaTitulo' => 'Classificação Trib. <br>CBS/IBS', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D005_Situacao_Tributaria_CBS_IBS_Regular', 'colunaTitulo' => 'CST CBS/IBS Reg.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D005_Classificacao_Tributaria_CBS_IBS_Regular', 'colunaTitulo' => 'Classificação Trib. <br>CBS/IBS Reg.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
			/*=============================================================================*/
            array('colunaValor' => 'D005_Observacao', 'colunaTitulo' => 'Descrição', 'colunaEditarUppercase' => 'true', 'style' => 'min-width:350px;max-width:350px;white-space:normal;'),
        );
         
		/**
		* Definição da SQL
		*/
		$from = 'D005';

		$extra = <<<EOT
			LEFT JOIN D088 ON D088_Id=D005_D088_Id
			ORDER BY D005_Classificacao_Fiscal
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D005_Flag_Ativo']=='N'){
				return 'linhaCinza';
	        }
		}
        $retornoNCMDuplicada = $FIS003->verificarNCMDuplicadas();
		if ($retornoNCMDuplicada) {
			echo "<div style='margin:auto;text-align:center;padding:2px;border:1px solid red;background:#fc5a5a;color:white;width:99%;border-radius: 3px 3px 3px 3px;font-size:14px'>" . $retornoNCMDuplicada . "</div>";
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis003GridNcm_btn'),
		);
		echo Botoes::processar($botoes);

		//if (!personalizacaoBotoes()) { 
			/**			* Botões (Versão Acima do GRID)
			*/
			
				/*<div class="buttonsBottomTop" $float>
					<button type="button" id="fis003GridNcm_btn">Incluir</button>
				</div>*/
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
					$('#fis003GridNcm_btn').unbind('click').bind('click', function(data) {
					 	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro NCM - Valor para todas empresas', '/fis/fis003/content/fis003ContentNCMIncluir/', '&tab=geral', [600,250]);
					});
				} </script></div>
EOT;
		//}
	
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'D005_Flag_Aplicacao' => array('titulo' => 'EX', 'tipo' => 'texto'),
            'D005_Observacao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D005_Flag_Ativo' => array('titulo' => 'Ativo?(S/N)', 'tipo' => 'texto'),
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
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">NCM's<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
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
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastro NCM - Vale para todas empresas', '/fis/fis003/content/fis003ContentNCMIncluir/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela={$from}', [600,300]);
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
