<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/CAD001.php');
//$CAD001 = new CAD001();
// Carrega funções callback
require_once('prog/est/est005/grid_func-callback.php');
require_once('prog/est/est005/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est005GridConhecimentos':
		global $g;
		
		$grid = array(
            array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'font-size:10px'),
            array('colunaValor' => 'T084A_Id', 'colunaTitulo' => 'T084A_Id', 'chavePrimaria' => true, 'style' => 'font-size:10px', 'colunaExibir' => false),
            array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Conhecimento', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T084_Tipo_Nota','colunaTitulo' => 'Tipo Nota','colunaAlinhar' => 'center'),
			array('colunaValor' => 'T084A_Flag_Empresa_Tomadora','colunaTitulo' => 'Tomadora','colunaAlinhar' => 'center', 'callback' => 'verificaTomadora'),
			array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T084_Data_Lancamento', 'colunaTitulo' => 'Lançamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'IF(agrupado.T015_Id>0,agrupado.T015_Data_Vencimento,original.T015_Data_Vencimento) as T015_Data_Vencimento', 'colunaTitulo' => 'Financeiro', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'Cód'),
            array('colunaValor' => 'substr(D022_Nome_Empresa,1,20)', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor/Cliente'),
            array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T084_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T084_Aliquota_ICMS', 'colunaTitulo' => 'Aliq.ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T084_Valor_ICMS', 'colunaTitulo' => 'Valor ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T084_Valor_ICMS_ST', 'colunaTitulo' => 'Valor ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
            array('colunaValor' => 'T084_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
            array('colunaValor' => 'T084_Aliquota_PIS', 'colunaTitulo' => 'Aliq.PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
            array('colunaValor' => 'T084_Valor_PIS', 'colunaTitulo' => 'Valor PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
			array('colunaValor' => 'T084_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
            array('colunaValor' => 'T084_Aliquota_COFINS', 'colunaTitulo' => 'Aliq.COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
            array('colunaValor' => 'T084_Valor_COFINS', 'colunaTitulo' => 'Valor COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
            array('colunaValor' => 'T084_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            // array('colunaValor' => 'T084_Flag_Fechado', 'colunaTitulo' => 'Notas', 'callback' => 'validaNotasLancadas','colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(ifnull(T007_Numero_Nota_Fiscal,""),ifnull(T013_Numero_Nota_Fiscal,"") order by T085_Id separator ", ")', 'colunaTitulo' => 'Notas','colunaAlinhar' => 'center'),
            array('colunaValor' => 'SUM(T007_Valor_Frete_Calculado)', 'colunaTitulo' => 'Frete calculado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'colunaTipo' => 'livre', 'callback' => 'validaCampoXMLConhecimento'),
            array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'colunaTipo' => 'livre', 'callback' => 'validaCampoCancelar'),
            array('colunaValor' => 'Excluir', 'colunaTitulo' => 'Excluir', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExcluir'),
            array('colunaValor' => 'T084_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_C007_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_D022_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_D006_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T085_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_Chave_Acesso_Cte', 'colunaTitulo' => 'Chave de Acesso', 'colunaExibir' => false),
            array('colunaValor' => 'T084_Numero_Protocolo_Cte', 'colunaTitulo' => 'Protocolo', 'colunaExibir' => false),
            array('colunaValor' => 'T084_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaExibir' => false),
			array('colunaValor' => '(SELECT T084_Numero_Conhecimento FROM T084 WHERE T084_Id = T084A_Cte_Original_Id)', 'colunaTitulo' => 'Conhec. Original'),
			array('colunaValor' => 'if(T084A_Cte_Original_Id > 0,"SIM","NÃO")', 'colunaTitulo' => 'CT-e Compl.'),
			array('colunaValor' => 'T084A_Peso_Base_Calculo', 'colunaTitulo' => 'Peso Base Calculo'),
			array('colunaValor' => 'T084A_Peso_Cubado', 'colunaTitulo' => 'Peso Cubado'),
			array('colunaValor' => 'T084A_Peso_Real', 'colunaTitulo' => 'Peso Real'),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T084";
		
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id=T084_C007_Id
		    LEFT JOIN D022 ON D022_Id=T084_D022_Id
		    LEFT JOIN D024 ON D024_Id=T084_D024_Id
		    LEFT JOIN D020 ON D020_Id=D024_D020_Id
		    LEFT JOIN D018 ON D018_Id=D020_D018_Id
		    LEFT JOIN T015 AS original ON original.T015_T084_Id=T084_Id
		    LEFT JOIN T015 AS agrupado ON original.T015_T015_Id_Agrupado=agrupado.T015_Id AND agrupado.T015_Flag_Cancelada!='S'
		    LEFT JOIN D006 ON D006_Id=T084_D006_Id
		    LEFT JOIN T085 ON T084_Id=T085_T084_Id
		    LEFT JOIN T013 ON T013_Id=T085_T013_Id
		    LEFT JOIN T007 ON T007_Id=T085_T007_Id
			LEFT JOIN T084A ON T084_Id = T084A_T084_Id
			WHERE (T084_C004_Id='{$g['empresaAtual']}')   
				AND T084_Flag_Entrada_Saida = 'E'
		    GROUP BY T084_Id
EOT;

		function gridLinha($array) {
			if($array['todosCampos']['T084_Flag_Cancelado'] == 'S') {
				return 'linhaVermelho';
			} 
		}

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!--<div class="buttonsBottomTop">
				<button type="button" id="est005IncluirConhecimentoBtn">Incluir</button>
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est005IncluirConhecimentoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir conhecimento', '/est/est005/content/est005ContentCadastroConhecimento/', '&tab=geral', [800,500]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T084_Numero_Conhecimento' => array('titulo' => 'Conhecimento', 'tipo' => 'numero'),
            'T013_Numero_Nota_Fiscal,T007_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'numero'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T084_Data_Lancamento' => array('titulo' => 'Lançamento', 'tipo' => 'data'),
            'IF(agrupado.T015_Id>0[[virgula]]agrupado.T015_Data_Vencimento[[virgula]]original.T015_Data_Vencimento)' => array('titulo' => 'Financeiro', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T084_Tipo_Nota' => array('titulo' => 'Tipo Nota','tipo' => 'select','select' => array(
								array('title' => 'TODAS','value' => ''),
								array('title' => 'EMITIDAS','value' => 'EMITIDA'),
								array('title' => 'RECEBIDAS','value' => 'RECEBIDA'),
										),
       						 		),
		'T084A_Flag_Empresa_Tomadora' => array('titulo' => 'Tomadora','tipo' => 'select','select' => array(
													array('title' => 'TODAS','value' => ''),
													array('title' => 'SIM','value' => 'S'),
													array('title' => 'NÃO','value' => 'N'),
													),
												),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total', 'valor' => 'T084_Valor_Conhecimento', 'titulo' => 'Valor total'),
			array('nome' => 'Valor_ICMS', 'valor' => 'T084_Valor_ICMS', 'titulo' => 'Valor ICMS'),
			array('nome' => 'Valor_ST', 'valor' => 'T084_Valor_ICMS_ST', 'titulo' => 'Valor ST'),
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est005IncluirConhecimentoBtn'),
		);
		echo Botoes::processar($botoes);
	
		$novoIdTelaForm = uniqid();	
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {            
			acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Conhecimento - ' + array['todosCampos']['T084_Id'], '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T084_Id']) + '&tabela={$from}', [920,590]);
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
	
	
	case 'est004GridProdutosNota':
		
		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'validaCampoExcluir'), // fazer um callback para exibir ou não o campo exclusão
 			array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
			array('colunaValor' => 'T014_Codigo_Produto', 'colunaTitulo' => 'Cód Fornecedor'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso Cód'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'callback'=>'validaCampoCFOP', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar'=>true),
			array('colunaValor' => 'T014_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Unit', 'colunaEditar'=>true),
			array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Custo Total'),
			array('colunaValor' => 'T014_Valor_Total_Desconto', 'colunaTitulo' => 'Desconto Total'),
			array('colunaValor' => 'T014_Aliquota_ICMS', 'colunaTitulo' => 'Aliq. ICMS', 'colunaEditar'=>true),
			array('colunaValor' => 'T014_Percentual_Reducao_ICMS', 'colunaTitulo' => 'Red. ICMS', 'colunaEditar'=>true),
			array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'Valor ICMS', 'colunaEditar'=>true),
			array('colunaValor' => 'T014_Aliquota_IPI', 'colunaTitulo' => 'Aliq. IPI', 'colunaEditar'=>true),
			array('colunaValor' => 'T014_Valor_IPI', 'colunaTitulo' => 'Total IPI'),
			array('colunaValor' => 'T014_Classificacao_Fiscal', 'colunaTitulo' => 'Class. Fiscal', 'colunaEditar'=>true),
			array('colunaValor' => 'T012_T011_Id', 'colunaTitulo' => 'OC'),
			array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => 'OC', 'colunaExibir'=>false),
     );
		
		/**
		 * Definição da SQL
		 */
		$from = "T014";
		
		$extra = <<<EOT
            left join T013 on T013_Id=T014_T013_Id
			left join T012 on T012_Id=T014_T012_Id
			left join D009 on D009_Id=T014_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D037 on D037_Id=D001_D037_Id
			left join D082 on D082_Id=D049_D082_Id
			left join T001 on T001_T014_Id=T014_Id
			where T014_T013_Id='$r_acaoId'
			order by T014_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);


		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		/**
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est004/content/est004ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
			});
		} </script></div>
EOT;
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
	
	case 'est004GridPagamentoNota':
			$acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T154_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T154_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T154_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar'=>true),
				array('colunaValor' => 'T154_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
				array('colunaValor' => 'T154_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar'=>true),
				array('colunaValor' => 'T154_D027_Id', 'colunaTitulo' => 'Portador', 'colunaExibir'=>false),
				//AGUARDAR O CAMPO EDITÁVEL TIPO CAMBOBOX QUE O TIAGO VAI FAZER
				array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T154';

			$extra = <<<EOT
				left join D027 on D027_Id=T154_D027_Id
				WHERE T154_T013_Id = {$acao_Id}
EOT;

			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T152_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
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

			$novoIdTelaForm = uniqid();
	break;

	case 'est005GridCadastroNotasRelacionadas':
		$T084_Id =  (isset($r_T084_Id)) ? $r_T084_Id : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T085_Id', $g['divId'])),
            array('colunaValor' => 'T085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T085_Flag_Tipo_Nota', 'colunaTitulo' => 'Tipo', 'colunaExibir' => true),
            array('colunaValor' => 'T085_Numero_Nota_Fiscal', 'colunaTitulo' => 'Nota Fiscal'),
            array('colunaValor' => 'T085_Valor_Total_Nota', 'colunaTitulo' => 'Valor NF'),
            array('colunaValor' => 'T085_Valor_Rateio', 'colunaTitulo' => 'Valor Rateio', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T085_Flag_Fechado', 'colunaTitulo' => 'Nota Lançada'),
            array('colunaValor' => 'T007_Valor_Frete_Calculado', 'colunaTitulo' => 'Frete calculado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T085';
		$extra = <<<EOT
		LEFT JOIN T007 ON T085_T007_Id=T007_Id
		LEFT JOIN T013 ON T085_T013_Id=T013_Id
		WHERE T085_T084_Id='{$T084_Id}'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

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
					var chavePrimaria = 'T085_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: recarregar FORM (com novo acaoId)
					divSetarAcaoId('{$r_divReload}', acaoId);
					divRefresh('{$r_divReload}');
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

	case 'est005gridCadastroContasPagar':
		
		$grid = array(			
            array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T015_Id', $g['divId']), 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'style' => 'width:1%', 'colunaExibir' => true),
            array('colunaValor' => 'T015_Flag_Boleto', 'colunaTitulo' => 'Ok', 'callback' => 'validaCampoFlagBoleto', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D024_Cnpj,D024_Cpf)', 'colunaTitulo' => 'CNPJ/CPF', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
            array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T015_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
            array('colunaValor' => 'concat(T015_Numero_Parcela,\'/\',T015_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
            array('colunaValor' => 'T015_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T015_Codigo_Barras', 'colunaTitulo' => 'Boleto'),
            array('colunaValor' => 'T015_T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Observacao_Reparcelamento', 'colunaTitulo' => 'T015_Observacao_Reparcelamento', 'colunaExibir' => false),
            array('colunaValor' => 'T015_T015_Id_Agrupado', 'colunaTitulo' => 'T015_T015_Id_Agrupado', 'colunaExibir' => false),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT
				LEFT JOIN D024 ON D024_Id=T015_D024_Id
				LEFT JOIN D073 ON D073_Id=T015_D073_Id
				LEFT JOIN D031 ON D031_Id=T015_D031_Id
				LEFT JOIN D014 ON D014_Id=T015_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				LEFT JOIN T007 ON T007_Id=T015_T007_Id
				LEFT JOIN C004 ON C004_Id=T015_C004_Id
				LEFT JOIN T084 ON T084_Id=T015_T084_Id
				LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
				WHERE (1=1) AND IF('{$g['C031']['contasPagarUnificado']}' != 'S', T015_C004_Id = '{$g['empresaAtual']}', '1=1')
				AND IF('{$g['C031']['exibirTitulosPagarAgrupados']}' = 'S',1=1,(T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL))
				AND T084_Id = '{$r_acaoId}'
EOT;
		log($extra);
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [850,500]);
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
		$g['smarty']->assign('totais', $totais);
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