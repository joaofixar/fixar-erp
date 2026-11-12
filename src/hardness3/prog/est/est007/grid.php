<?php
namespace hardness;

// Carrega funções callback
require_once('prog/est/est007/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 03/01/2012
	*/                 
	case 'est007GridMontagens':

		// Mudando T047_Flag_Status para 'N' quando for em branco, agora 'N' = Pendente
		mysqli_query("update T047 set T047_Flag_Status='N' where T047_Flag_Status='' or T047_Flag_Status is NULL");

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T047_Id', $g['divId'])),
			array('colunaValor' => 'T047_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T047_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%' ),
			array('colunaValor' => 'T047_Observacao', 'colunaTitulo' => 'Observação'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
			array('colunaValor' => 'T047_Flag_Status', 'colunaTitulo' => 'Finalizado', 'callback'=>'validaCampoFinalizado', 'style' => 'width:1%'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T047';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		left join C007 on T047_C007_Id=C007_Id
		where T047_C004_Id={$g['empresaAtual']}
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad046FormCadastroMotivosCanecelamento'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!--<div class="buttonsBottomTop">
				<button type="button" id="cad046FormCadastroMotivosCanecelamento">Incluir</button>
				
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad046FormCadastroMotivosCanecelamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Montagens', '/est/est007/content/est007ContentMontagensIncluir/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T047_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T047_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T047_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
            'T047_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Finalizado', 'value' => 'A'),
                array('title' => 'Pendente', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
// 		echo <<<EOT
// 			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// 				var dbclickPrevent = false;
// 				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
// 					// Previne duplo click (abrir duas janelas)
// 					if (dbclickPrevent) { return false; }
// 					dbclickPrevent = true;
// 					setTimeout(function() { dbclickPrevent = false; }, 500);
// 					// (substituir pelo mesmo ID da chave primária do grid)
// 					var chavePrimaria = 'T047_Id';	
// 					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
// 					var id = $(this).closest('tr').attr('id').split('|');
// 					var acaoId = id[0].replace(chavePrimaria + '-', '');
// 					// Ação: abrir nova janela
// 	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Operação - Nº: ' + acaoId, '/est/est007/content/est007ContentMontagensEditarNovo/', '&acaoId=' + encodeURIComponent(acaoId), [850,500]);
// 		        });
// 				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
// 			} </script></div>
// EOT;

		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Operação - Nº: ' + encodeURIComponent(array['todosCampos']['T047_Id']), '/est/est007/content/est007ContentMontagensEditarNovo/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T047_Id']), [850,500]);
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
	
	case 'est007FormEditarMontagens':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExcluirItens'),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T048_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T047_Flag_Status', 'colunaTitulo' => 'T047_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'T048_Quantidade', 'colunaTitulo' => 'Qnt', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T048_Flag_Tipo_Montagem', 'colunaTitulo' => 'Tipo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoTipoMontagem', 'colunaEditarGeraSelect' => array(
            		array('title' => '...', 'value' => '', 'selected' => true),
            		array('title' => 'MONTAGEM', 'value' => 'M'),
            		array('title' => 'DESMONTAGEM', 'value' => 'D'),
            	),
            ),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
			array('colunaValor' => 'T048_T066_Id', 'colunaTitulo' => 'Locação','callback' => 'T048_T066_Id_Local', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Montagem(D009_Id) as Disp', 'colunaTitulo' => 'Disp.Mont.', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Disp.Desm.', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T048_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true),
            array('colunaValor' => 'atalhoDocumentoProduto', 'colunaTitulo' => '', 'callback' => 'atalhoDocumentoProduto', 'style' => 'width:1px', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T048';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		
				left join T047 on T047_Id=T048_T047_Id
				left join D049 on D049_Id=T048_D049_Id
				left join D009 on D049_Id=D009_D049_Id and T048_C004_Id=D009_C004_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D082 on D082_Id=D049_D082_Id
				left join T066 on T066_Id=T048_T066_Id
				left join D004 on D004_Id=T066_D004_Id
				where T048_T047_Id='$r_acaoId'
				and T048_D049_Id>0
				order by T048_Id desc 
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad046FormCadastroMotivosCanecelamento">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad046FormCadastroMotivosCanecelamento').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Montagens', '/est/est007/content/est007ContentMontagensIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
// 		echo <<<EOT
// 			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// 				var dbclickPrevent = false;
// 				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
// 					// Previne duplo click (abrir duas janelas)
// 					if (dbclickPrevent) { return false; }
// 					dbclickPrevent = true;
// 					setTimeout(function() { dbclickPrevent = false; }, 500);
// 					// (substituir pelo mesmo ID da chave primária do grid)
// 					var chavePrimaria = 'T048_Id';	
// 					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
// 					var id = $(this).closest('tr').attr('id').split('|');
// 					var acaoId = id[0].replace(chavePrimaria + '-', '');
// 					// Ação: abrir nova janela
// 	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Sub-Itens', '/est/est007/content/est007ContentMontagensIncluirSubItem/', '&acaoId=' + encodeURIComponent(acaoId), [1000,500]);
// 		        });
// 				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
// 			} </script></div>
// EOT;

		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				acaoClickLinha('{$g['divId']}', function(data, array) {
 	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Sub-Itens', '/est/est007/content/est007ContentMontagensIncluirSubItem/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T048_Id']), [1000,500]);
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
	
	case 'est007FormEditarMontagensSubItens':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExlcluirSubItem'),
            array('colunaValor' => 'T071_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'T071_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T047_Flag_Status', 'colunaTitulo' => 'T047_Flag_Status', 'colunaExibir' => true),
            array('colunaValor' => 'T071_Quantidade', 'colunaTitulo' => 'Qtd', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T071_Codigo_Produto', 'colunaTitulo' => 'Referência', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
			array('colunaValor' => 'T071_T066_Id', 'colunaTitulo' => 'Locação','callback' => 'T071_T066_Id_Local', 'colunaAlinhar' => 'center'),
		    array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq.Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qte.OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
			array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Fís.Locação', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'gCorrigeNumero'),
            array('colunaValor' => 'T066_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estq.Líq.Locação','colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%','callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueFora'),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo R$', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => 'Ult.Compra', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'gCorrigeData', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T071_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T048_Quantidade', 'colunaTitulo' => 'Qnt', 'colunaExibir' => false),
            array('colunaValor' => 'T047_Flag_Status', 'colunaTitulo' => 'Quantidade', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T071';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		
				left join T048 on T048_Id=T071_T048_Id
				left join T047 on T047_Id=T048_T047_Id
				left join D009 on D009_Id=T071_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D082 on D082_Id=D049_D082_Id
				left join T066 on T066_Id=T071_T066_Id
				left join D004 on D004_Id=T066_D004_Id
				where T071_T048_Id='$r_acaoId'
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad046FormCadastroMotivosCanecelamento">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad046FormCadastroMotivosCanecelamento').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Montagens', '/est/est007/content/est007ContentMontagensIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D131_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
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
					var chavePrimaria = 'T048_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagens - Nº: ' + acaoId, '/est/est007/content/est007ContentMontagensIncluirSubItem/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
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
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
}

?>
