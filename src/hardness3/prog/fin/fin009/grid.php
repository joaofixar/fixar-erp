<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/FIN007.php');
//$FIN007 = new FIN007();
// Carrega funções callback
require_once('prog/fin/fin009/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/fin/fin009/grid_func-js.php');

/**
 * Ações
 */
	switch($g['r'][3]){
	/**
	* Grid Principal
	*/
	case 'fin009GridProjecaoOrcamentaria':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D014_Id', $g['divId']), 'style'=>'width:10px'),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
            array('colunaValor' => 'D014_D032_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Ano', 'colunaTitulo' => 'Ano', 'colunaAlinhar'=>'center', 'style'=>'width:30px'),
			array('colunaValor' => 'T018_Mes', 'colunaTitulo' => 'Mês', 'colunaAlinhar'=>'center', 'style'=>'width:30px'),
			array('colunaValor' => 'D014_Dia_Vencimento', 'colunaTitulo' => 'Dia', 'colunaEditar'=>true, 'style'=>'width:30px', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D014_Grupo_DRE', 'colunaTitulo' => 'DRE', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'width:40px', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'D014_Flag_Plano', 'colunaTitulo' => 'Tipo', 'colunaExibir'=>false),
			//array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Código','callback' => 'campoGrupoNegrito', 'style'=>'width:1%;'),
			array('colunaValor' => 'T018_Valor', 'colunaTitulo' => 'Valor projeção', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true, 'colunaAlinhar'=>'right', 'colunaEditarJSCallback'=>'atualizaFlagConfirmado'),
			array('colunaValor' => 'T018_Flag_Confirmado', 'colunaTitulo' => 'Conf', 'callback'=>'campoConfirmado', 'style'=>'width:1%', 'colunaAlinhar'=>'center'),
			//array('colunaValor' => 'T018_Flag_Confirmado', 'colunaTitulo'=>'Confirmado','colunaEditar' => true, 'callback' => 'campoConfirmado','style'=>'width:1%;',
//		                'colunaEditarGeraSelect' => array(
//		                    array('title' => '...', 'value' => '', 'selected' => true),
//		                    array('title' => 'Sim', 'value' => 'S'),
//		                    array('title' => 'Não', 'value' => 'N'))
//		                ),
			array('colunaValor' => 'T018_Valor_Receber', 'colunaTitulo' => 'Receber', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Pagar', 'colunaTitulo' => 'Pagar', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T018_Valor_Pago', 'colunaTitulo' => 'Pago', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => '(+)/(-)', 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Pagamento_Unico', 'colunaTitulo' => 'Pagamento único', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Valor_Saldo', 'colunaTitulo' => 'Saldo projeção', 'callback'=>'ValidaCampoSaldo', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'concat(D032_Tipo(D032_Tipo)," - ",D032_Descricao) as Grupo', 'colunaTitulo' => 'Tipo', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => "IF(D014_Flag_Tipo='PG' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor_Saldo, 0) as SaldoSaida", 'colunaTitulo' => 'Tipo', 'style'=>'width:1%;', 'colunaExibir'=>false),

		);

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		
		$extra = <<<EOT
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_C004_Id='{$g['empresaAtual']}'
			ORDER BY D014_Grupo
EOT;
		// $('#fin009RecalcularProjecao').unbind('click').bind('click', function(data) {
		// 	$('#{$g['divId']}').showLoading();
		// 	$.getJSON('/fin/fin009/grid_func-ajax/fin009RecalcularProjecao/?ajax=true&callback=?', function(request) {
		// 		$('#{$g['divId']}').hideLoading();
		// 		if (request.code) {
		// 			{$alert}
		// 			divRefresh('{$g['divId']}', true);
		// 		} else {
		// 			alert(request.data);
		// 		}
		// 	});
		// });

		/**
         * Botões Almir
         */
		$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
        $botoes = array(
			array('titulo' => 'Recalcular', 'id' => 'fin009RecalcularProjecao'),
			);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
		
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad034GridPlanoContasBtnIncluir">Incluir</button> -->
				<button type="button" id="fin009RecalcularProjecao">Recalcular</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridPlanoContasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
				});
				
				$('#fin009RecalcularProjecao').unbind('click').bind('click', function() {
					idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
                    $('#{$g['divId']}').showLoading();
                    fin009RecalcularProjecaoLoop(idProgressBar,1,0,0,0,0);//id do alert, número etapa,qtde de etapas, qtde total de itens, qtde erro, qtde contas atualizadas 
				});
				function fin009RecalcularProjecaoLoop(idProgressBar, etapa, etapas, valorTotal, erro, ok){
                    $.getJSON('/fin/fin009/grid_func-ajax/fin009RecalcularProjecao/?ajax=true&callback=?'+'&valorTotal=' + valorTotal + '&etapas=' + etapas + '&etapa=' + etapa, function(request) {
                    	etapas = request.etapas;
                    	valorTotal = request.valorTotal;
                        if (request.etapa<=etapas && request.concluido == 0) {
                        	erro += request.erro;
                        	ok += request.ok;
                        	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>');
                        	fin009RecalcularProjecaoLoop(idProgressBar,request.etapa, etapas, valorTotal, erro, ok);
                        }else{
                        	erro += request.erro;
                        	ok += request.ok;
                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
                        	dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>');
                        	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                        } else {
	                            dialogConfirm('Erro!', request.data);
	                        }
                        } 
	                });	                    
		        }
				function atualizaFlagConfirmado(Ids, campoEditado, valorAntigo, novoValor){
					var T018_Id_array = Ids.split('|');
					var T018_Id = T018_Id_array[1].replace('T018_Id' + '-', '');
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin009/grid_func-ajax/fin009AtualizaFlagConfirmado/?ajax=true&callback=?&T018_Id=' + encodeURIComponent(T018_Id) + '&novoValor=' + encodeURIComponent(novoValor), function(request) {
						$('#{$g['divId']}').hideLoading();
						if(novoValor == "0,00"){
							$('#{$g['divId']} input[name=T018_Flag_Confirmado]:eq(' + $('#{$g['divId']} tr[id*="T018_Id-' + T018_Id + '"]').first().index() + ')').prop('checked', '');
						}else{
							$('#{$g['divId']} input[name=T018_Flag_Confirmado]:eq(' + $('#{$g['divId']} tr[id*="T018_Id-' + T018_Id + '"]').first().index() + ')').prop('checked', 'checked');
						}
					});
				}
				
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T018_Ano' => array('titulo' => 'Ano', 'tipo' => 'texto'),
			'T018_Mes' => array('titulo' => 'Mês', 'tipo' => 'texto'),
			'D014_Dia_Vencimento' => array('titulo' => 'Dia vencimento', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D014_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Saidas', 'value' => 'PG'),
				array('title' => 'Entradas', 'value' => 'RC'),
                array('title' => 'Todos', 'value' => ''),
			), 'naoGerarExtra' => false),
			'D014_Flag_Ativo' => array('titulo' => 'Contas', 'tipo' => 'select', 'select' => array(
                array('title' => 'Ativas', 'value' => 'S'),
				array('title' => 'Inativas', 'value' => 'N'),
                array('title' => 'Todas', 'value' => ''),
			), 'naoGerarExtra' => false),
			'T018_Valor_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Grupo', 'campoAgrupar' => 'D014_D032_Id', 'campoMostrar' => 'Grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
            )),
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
			array('nome' => 'Projecao_Saida', 'valor' => "IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0)", 'titulo' => 'Proj. Despesas'),
			array('nome' => 'T018_Valor_Pagar', 'valor' => 'T018_Valor_Pagar', 'titulo' => 'Pagar'),
			array('nome' => 'T018_Valor_Pago', 'valor' => 'T018_Valor_Pago', 'titulo' => 'Pago'),
			array('nome' => 'Saldo_Saida', 'valor' => 'SaldoSaida', 'titulo' => 'Saldo Projeção '),
			array('nome' => 'Projecao_Entrada', 'valor' => "IF(D014_Flag_Tipo='RC' AND T018_Valor>0, T018_Valor, 0)", 'titulo' => 'Projeção Receitas'),
			array('nome' => 'T018_Valor_Receber', 'valor' => 'T018_Valor_Receber', 'titulo' => 'Receber'),
			array('nome' => 'T018_Valor_Recebido', 'valor' => 'T018_Valor_Recebido', 'titulo' => 'Recebido'),
			array('nome' => 'Saldo_Entrada', 'valor' => "IF(D014_Flag_Tipo='RC' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0)", 'titulo' => 'Saldo Projeção'),
			array('nome' => 'Lucro_Projetado', 'tipo'=>'totais' , 'valor' => "( {Saldo_Entrada}+
																				{T018_Valor_Receber}+
																				{T018_Valor_Recebido}-
																				{Saldo_Saida}-
																				{T018_Valor_Pagar}-
																				{T018_Valor_Pago} )", 'titulo' => 'Lucro Proj'),
			array('nome' => 'Lucro_Realizado', 'tipo'=>'totais' , 'valor' => "( {T018_Valor_Recebido}-
																				{T018_Valor_Pago} )", 'titulo' => 'Lucro Real'),

        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Totais (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		log("FROM = ".$from);
		log("EXTRA= ".$extra);
		$Projecao_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor>0, T018_Valor, 0))", $from, $extra));
		$Projecao_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0))", $from, $extra));
		$T018_Valor_Receber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Receber)", $from, $extra));
		$T018_Valor_Recebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Recebido)", $from, $extra));
		$T018_Valor_Pagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pagar)", $from, $extra));
		$T018_Valor_Pago = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pago)", $from, $extra));
		$Saldo_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extra));
		$Saldo_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor_Saldo, 0))", $from, $extra));
		$lucroProjetado = gCorrigeNumero(gCorrigeNumeroInverte($Saldo_Entrada) + 
									  gCorrigeNumeroInverte($T018_Valor_Receber) + 
									  gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($Saldo_Saida) - 
									  gCorrigeNumeroInverte($T018_Valor_Pagar) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );
		$lucroRealizado =  gCorrigeNumero(gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );*/
        /*
        <span class="item" style="text-align:right">Projeção entradas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Entrada<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Projeção saidas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Saída<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Resultado<br><input type="text" value="{$Saldo_Total}" class="whiteBg" id="" size="11" readonly></span>
        */

		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="4" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
				<span class="item" style="text-align:right">Proj.Despesas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Saldo projeção<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
		        <span class="item" style="text-align:right">Proj.Receitas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Receber<br><input type="text" value="{$T018_Valor_Receber}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Recebido<br><input type="text" value="{$T018_Valor_Recebido}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Saldo projeção<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
		        <span class="item" style="text-align:right">Lucro Proj<br><input type="text" value="{$lucroProjetado}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Lucro Real<br><input type="text" value="{$lucroRealizado}" class="whiteBg" id="" size="10" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + array['todosCampos']['D014_Id'], '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(array['todosCampos']['D014_Id']), [1000,650]);
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
		
	
	case 'fin009GridProjecaoOrcamentariaGrupo':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D014_Id', $g['divId'])),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Ano', 'colunaTitulo' => 'Ano', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T018_Mes', 'colunaTitulo' => 'Mês', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Plano', 'colunaTitulo' => 'Tipo', 'colunaExibir'=>false),
			//array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Código','callback' => 'campoGrupoNegrito', 'style'=>'width:1%;'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'RC\' AND T018_Valor > 0, T018_Valor, 0)) as ProjEntrada', 'colunaTitulo' => 'Projeção entrada', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(T018_Valor_Receber) as Receber', 'colunaTitulo' => 'Receber', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(T018_Valor_Recebido) as Recebido', 'colunaTitulo' => 'Recebido', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'RC\' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))  as ProjetadoEntrada', 'colunaTitulo' => 'Saldo projeção', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'PG\' AND ((D014_Flag_Pagamento_Unico!=\'S\' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico=\'S\' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor, 0))', 'colunaTitulo' => 'Projeção saída', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
			array('colunaValor' => 'SUM(T018_Valor_Pagar) as Pagar', 'colunaTitulo' => 'Pagar', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
			array('colunaValor' => 'SUM(T018_Valor_Pago) as Pago', 'colunaTitulo' => 'Pago', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'PG\' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))', 'colunaTitulo' => 'Saldo projeção', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
            array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'RC\' AND T018_Valor_Saldo > 0, T018_Valor_Saldo, 0))+SUM(T018_Valor_Receber)+SUM(T018_Valor_Recebido)-SUM(IF(D014_Flag_Tipo=\'PG\' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))-SUM(T018_Valor_Pago)-SUM(T018_Valor_Pagar) as Resultado', 'colunaTitulo' => 'Resultado projetado', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'SUM(T018_Valor_Recebido)-SUM(T018_Valor_Pago) as ResultadoReal', 'colunaTitulo' => 'Resultado Realizado', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'font-weight:bold'),
			array('colunaValor' => "SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0)) as ProjDespesas", 'colunaTitulo' => 'Projeção despesas', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'font-weight:bold', 'colunaExibir'=>'false'),
			array('colunaValor' => "SUM(IF(D014_Flag_Tipo='PG' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor_Saldo, 0)) as ProjetadoSaida", 'colunaTitulo' => '', 'callback'=>'gCorrigeNumero', 'colunaExibir'=>'false'),
			
		);

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$extra = <<<EOT
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_C004_Id='{$g['empresaAtual']}'
			GROUP BY D032_Id
EOT;
		$extraTotal = <<<EOT
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_C004_Id='{$g['empresaAtual']}'
EOT;

		/**
         * Botões Almir
         */
		$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
		
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad034GridPlanoContasBtnIncluir">Incluir</button> -->
				<!--<button type="button" id="fin009RecalcularProjecaoTabGrupos">Recalcular</button> Botão retirado, utilizar apenas o recalcular de projeção orçamentária-->
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridPlanoContasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
				});
				// $('#fin009RecalcularProjecaoTabGrupos').unbind('click').bind('click', function(data) {
				// 	$('#{$g['divId']}').showLoading();
				// 	$.getJSON('/fin/fin009/grid_func-ajax/fin009RecalcularProjecao/?ajax=true&callback=?', function(request) {
				// 		$('#{$g['divId']}').hideLoading();
				// 		if (request.code) {
				// 			{$alert}
				// 			divRefresh('{$g['divId']}', true);
				// 		} else {
				// 			alert(request.data);
				// 		}
				// 	});
				// });
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T018_Ano' => array('titulo' => 'Ano', 'tipo' => 'texto'),
			'T018_Mes' => array('titulo' => 'Mês', 'tipo' => 'texto'),
			'D014_Dia_Vencimento' => array('titulo' => 'Dia vencimento', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D014_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Saidas', 'value' => 'PG'),
				array('title' => 'Entradas', 'value' => 'RC'),
                array('title' => 'Todos', 'value' => ''),
			), 'naoGerarExtra' => false),
			'D014_Flag_Ativo' => array('titulo' => 'Contas', 'tipo' => 'select', 'select' => array(
                array('title' => 'Ativas', 'value' => 'S'),
				array('title' => 'Inativas', 'value' => 'N'),
                array('title' => 'Todas', 'value' => ''),
			), 'naoGerarExtra' => false),
			'T018_Valor_Saldo' => array('titulo' => 'Saldo projeção', 'tipo' => 'moeda'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Projecao_Saida', 'valor' => 'ProjDespesas', 'titulo' => 'Proj. Despesas'),
			array('nome' => 'T018_Valor_Pagar', 'valor' => 'Pagar', 'titulo' => 'Pagar'),
			array('nome' => 'T018_Valor_Pago', 'valor' => 'Pago', 'titulo' => 'Pago'),
			array('nome' => 'Saldo_Saida', 'valor' => 'ProjetadoSaida', 'titulo' => 'Saldo Projeção'),
			array('nome' => 'Projecao_Entrada', 'valor' => 'ProjEntrada', 'titulo' => 'Proj. Receitas'),
			array('nome' => 'T018_Valor_Receber', 'valor' => 'Receber', 'titulo' => 'Receber'),
			array('nome' => 'T018_Valor_Recebido', 'valor' => 'Recebido', 'titulo' => 'Recebido'),
			array('nome' => 'Saldo_Entrada', 'valor' => 'ProjetadoEntrada', 'titulo' => 'Saldo Projeção'),
			array('nome' => 'Lucro_Projetado','tipo'=>'totais', 'valor' => '({Saldo_Entrada}+
																			 {T018_Valor_Receber}+
																			 {T018_Valor_Recebido}-
																			 {Saldo_Saida}-
																			 {T018_Valor_Pagar}-
																			 {T018_Valor_Pago})', 'titulo' => 'Lucro Proj.'),
			array('nome' => 'Lucro_Realizado','tipo'=>'totais', 'valor' => '({T018_Valor_Recebido}-
																			 {T018_Valor_Pago})', 'titulo' => 'Lucro Proj.'),


        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Totais (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraTotal);
		$Projecao_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor>0, T018_Valor, 0))", $from, $extraTotal));
		$Projecao_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0))", $from, $extraTotal));
		$T018_Valor_Receber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Receber)", $from, $extraTotal));
		$T018_Valor_Recebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Recebido)", $from, $extraTotal));
		$T018_Valor_Pagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pagar)", $from, $extraTotal));
		$T018_Valor_Pago = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pago)", $from, $extraTotal));
		$Saldo_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extraTotal));
		$Saldo_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor_Saldo, 0))", $from, $extraTotal));
		$lucroProjetado = gCorrigeNumero(gCorrigeNumeroInverte($Saldo_Entrada) + 
									  gCorrigeNumeroInverte($T018_Valor_Receber) + 
									  gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($Saldo_Saida) - 
									  gCorrigeNumeroInverte($T018_Valor_Pagar) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );
		$lucroRealizado =  gCorrigeNumero(gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );*/

        /*
        <span class="item" style="text-align:right">Projeção entradas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Entrada<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Projeção saidas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Saída<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Resultado<br><input type="text" value="{$Saldo_Total}" class="whiteBg" id="" size="11" readonly></span>
        */

		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Proj.Despesas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Saldo projeção<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
		        <span class="item" style="text-align:right">Proj.Receitas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Receber<br><input type="text" value="{$T018_Valor_Receber}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Recebido<br><input type="text" value="{$T018_Valor_Recebido}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Saldo projeção<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
		        <span class="item" style="text-align:right">Lucro Proj<br><input type="text" value="{$lucroProjetado}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Lucro Real<br><input type="text" value="{$lucroRealizado}" class="whiteBg" id="" size="10" readonly></span>
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
					var chavePrimaria = 'D014_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + acaoId, '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [900,550]);
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
	
	case 'fin009GridProjecaoOrcamentariaMes':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D014_Id', $g['divId'])),
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			//array('colunaValor' => 'D032_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Ano', 'colunaTitulo' => 'Ano', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T018_Mes', 'colunaTitulo' => 'Mês', 'colunaAlinhar'=>'center'),
			array('colunaValor' => '', 'colunaTitulo' => 'Operação', 'callback'=>'validaCampoOperacao', 'colunaTipo'=>'livre', 'colunaExibir'=>false),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'RC\' AND T018_Valor > 0, T018_Valor, 0)) as ProjReceitas', 'colunaTitulo' => 'Projeção entrada', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(T018_Valor_Receber)', 'colunaTitulo' => 'Receber', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(T018_Valor_Recebido)', 'colunaTitulo' => 'Recebido', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'RC\' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0)) as ProjetadoEntrada', 'colunaTitulo' => 'Saldo projeção', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-azul'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'PG\' AND T018_Valor>0, T018_Valor, 0))', 'colunaTitulo' => 'Projeção saída', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
			array('colunaValor' => 'SUM(T018_Valor_Pagar)', 'colunaTitulo' => 'Pagar', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
			array('colunaValor' => 'SUM(T018_Valor_Pago)', 'colunaTitulo' => 'Pago', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
			array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'PG\' AND ((D014_Flag_Pagamento_Unico!=\'S\' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico=\'S\' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor_Saldo, 0)) as ProjetadoSaida', 'colunaTitulo' => 'Saldo projeção', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'background:var(--celula-amarelo)'),
            array('colunaValor' => 'SUM(IF(D014_Flag_Tipo=\'RC\' AND T018_Valor_Saldo > 0, T018_Valor_Saldo, 0))+SUM(T018_Valor_Receber)+SUM(T018_Valor_Recebido)-SUM(IF(D014_Flag_Tipo=\'PG\' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))-SUM(T018_Valor_Pago)-SUM(T018_Valor_Pagar) as Resultado', 'colunaTitulo' => 'Lucro projetado', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'SUM(T018_Valor_Recebido)-SUM(T018_Valor_Pago) as ResultadoReal', 'colunaTitulo' => 'Lucro Realizado', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'style' => 'font-weight:bold'),
			//array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'style'=>'width:1%;'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Plano', 'colunaTitulo' => 'Tipo', 'colunaExibir'=>false),
			//array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Código','callback' => 'campoGrupoNegrito', 'style'=>'width:1%;'),
			array('colunaValor' => 'D014_Dia_Vencimento', 'colunaTitulo' => 'Dia vencimento', 'colunaExibir'=>false),
			array('colunaValor' => 'SUM(T018_Valor) as T018_Valor', 'colunaTitulo' => 'Valor projeção', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'SUM(T018_Valor_Receber) as T018_Valor_Receber', 'colunaTitulo' => 'Receber', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'SUM(T018_Valor_Recebido) as T018_Valor_Recebido', 'colunaTitulo' => 'Recebido', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'SUM(T018_Valor_Pagar) as T018_Valor_Pagar', 'colunaTitulo' => 'Pagar', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'SUM(T018_Valor_Pago) as T018_Valor_Pago', 'colunaTitulo' => 'Pago', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'D014_Flag_Tipo', 'colunaTitulo' => '(+)/(-)', 'colunaExibir'=>false),
			array('colunaValor' => 'T018_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'callback'=>'ValidaCampoSaldo', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D014';
		$extra = <<<EOT
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_C004_Id='{$g['empresaAtual']}'
			GROUP BY T018_Ano, T018_Mes
EOT;
		
		$extraTotal = <<<EOT
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN T018 ON T018_D014_Id=D014_Id
			WHERE T018_C004_Id='{$g['empresaAtual']}'
EOT;

		/**
         * Botões Almir
         */
		$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
        $botoes = array(
			//array('titulo' => 'Incluir', 'id' => 'cad001Incluir',  'exibirCallback' => 'return isset($g["c029Ids"][103]);'),
			//array('titulo' => 'Juntar', 'id' => 'cad001Juntar',  'exibirCallback' => 'return isset($g["c029Ids"][103]);'),
			//array('titulo' => 'Incluir Pré Cadastro', 'id' => 'cad001IncluirPreCadastro',  'exibirCallback' => 'return isset($g["c029Ids"][183]);'),
			//array('titulo' => 'Calcular Limite', 'id' => 'cad001CalcularLimite',  'exibirCallback' => 'return isset($g["c029Ids"][222]);'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$alert = gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!', false);
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad034GridPlanoContasBtnIncluir">Incluir</button> -->
				<!--<button type="button" id="fin009RecalcularProjecaoTabGrupos">Recalcular</button> Botão retirado, utilziar apenas o recalcular de projeção orçamentária--> 
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad034GridPlanoContasBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
				});
				// $('#fin009RecalcularProjecaoTabGrupos').unbind('click').bind('click', function(data) {
				// 	$('#{$g['divId']}').showLoading();
				// 	$.getJSON('/fin/fin009/grid_func-ajax/fin009RecalcularProjecao/?ajax=true&callback=?', function(request) {
				// 		$('#{$g['divId']}').hideLoading();
				// 		if (request.code) {
				// 			{$alert}
				// 			divRefresh('{$g['divId']}', true);
				// 		} else {
				// 			alert(request.data);
				// 		}
				// 	});
				// });
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T018_Ano' => array('titulo' => 'Ano', 'tipo' => 'texto'),
			'T018_Mes' => array('titulo' => 'Mês', 'tipo' => 'texto'),
			'D014_Dia_Vencimento' => array('titulo' => 'Dia vencimento', 'tipo' => 'texto'),
			'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D014_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Saidas', 'value' => 'PG'),
				array('title' => 'Entradas', 'value' => 'RC'),
                array('title' => 'Todos', 'value' => ''),
			), 'naoGerarExtra' => false),
			'D014_Flag_Ativo' => array('titulo' => 'Contas', 'tipo' => 'select', 'select' => array(
                array('title' => 'Ativas', 'value' => 'S'),
				array('title' => 'Inativas', 'value' => 'N'),
                array('title' => 'Todas', 'value' => ''),
			), 'naoGerarExtra' => false),
			'T018_Valor_Saldo' => array('titulo' => 'Saldo projeção', 'tipo' => 'moeda'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'T018_Valor_Pagar', 'valor' => 'T018_Valor_Pagar', 'titulo' => 'Pagar'),
			array('nome' => 'T018_Valor_Pago', 'valor' => 'T018_Valor_Pago', 'titulo' => 'Pago'),
			array('nome' => 'Saldo_Saida', 'valor' => 'ProjetadoSaida', 'titulo' => 'Saldo Projeção'),
			array('nome' => 'Projecao_Entrada', 'valor' => 'ProjReceitas', 'titulo' => 'Proj. Receitas'),
			array('nome' => 'T018_Valor_Receber', 'valor' => 'T018_Valor_Receber', 'titulo' => 'Receber'),
			array('nome' => 'T018_Valor_Recebido', 'valor' => 'T018_Valor_Recebido', 'titulo' => 'Recebido'),
			array('nome' => 'Saldo_Entrada', 'valor' => 'ProjetadoEntrada', 'titulo' => 'Saldo Projeção'),
			array('nome' => 'Lucro_Projetado','tipo'=>'totais', 'valor' => '({Saldo_Entrada}+
																			 {T018_Valor_Receber}+
																			 {T018_Valor_Recebido}-
																			 {Saldo_Saida}-
																			 {T018_Valor_Pagar}-
																			 {T018_Valor_Pago})', 'titulo' => 'Lucro Proj.'),
			array('nome' => 'Lucro_Projetado','tipo'=>'totais', 'valor' => '({T018_Valor_Recebido}-
																			 {T018_Valor_Pago})', 'titulo' => 'Lucro Proj.'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Totais (Versão Acima do GRID)
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraTotal);
		$Projecao_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor>0, T018_Valor, 0))", $from, $extraTotal));
		$Projecao_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND T018_Valor>0, T018_Valor, 0))", $from, $extraTotal));
		$T018_Valor_Receber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Receber)", $from, $extraTotal));
		$T018_Valor_Recebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Recebido)", $from, $extraTotal));
		$T018_Valor_Pagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pagar)", $from, $extraTotal));
		$T018_Valor_Pago = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T018_Valor_Pago)", $from, $extraTotal));
		$Saldo_Entrada = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='RC' AND T018_Valor_Saldo>0, T018_Valor_Saldo, 0))", $from, $extraTotal));
		$Saldo_Saida = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(IF(D014_Flag_Tipo='PG' AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0)), T018_Valor_Saldo, 0))", $from, $extraTotal));
		$lucroProjetado = gCorrigeNumero(gCorrigeNumeroInverte($Saldo_Entrada) + 
									  gCorrigeNumeroInverte($T018_Valor_Receber) + 
									  gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($Saldo_Saida) - 
									  gCorrigeNumeroInverte($T018_Valor_Pagar) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );
		$lucroRealizado =  gCorrigeNumero(gCorrigeNumeroInverte($T018_Valor_Recebido) - 
									  gCorrigeNumeroInverte($T018_Valor_Pago)
		                             );*/

        /*
        <span class="item" style="text-align:right">Projeção entradas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Entrada<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Contas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Projeção saidas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Saldo Saída<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="11" readonly></span>
        <span class="item" style="text-align:right">Resultado<br><input type="text" value="{$Saldo_Total}" class="whiteBg" id="" size="11" readonly></span>
        */

		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Proj.Despesas<br><input type="text" value="{$Projecao_Saida}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Pagar<br><input type="text" value="{$T018_Valor_Pagar}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Pago<br><input type="text" value="{$T018_Valor_Pago}" class="whiteBg" id="" size="10" readonly></span>
				<span class="item" style="text-align:right">Saldo projeção<br><input type="text" value="{$Saldo_Saida}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
		        <span class="item" style="text-align:right">Proj.Receitas<br><input type="text" value="{$Projecao_Entrada}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Receber<br><input type="text" value="{$T018_Valor_Receber}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Recebido<br><input type="text" value="{$T018_Valor_Recebido}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Saldo projeção<br><input type="text" value="{$Saldo_Entrada}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">|<br>|</span>
		        <span class="item" style="text-align:right">Lucro Proj<br><input type="text" value="{$lucroProjetado}" class="whiteBg" id="" size="10" readonly></span>
		        <span class="item" style="text-align:right">Lucro Real<br><input type="text" value="{$lucroRealizado}" class="whiteBg" id="" size="10" readonly></span>
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
					var chavePrimaria = 'D014_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Plano de Conta - ID: ' + acaoId, '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [900,550]);
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

	case 'fin009GridLancamentos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            //array('colunaValor' => 'T019_Conciliado', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoConciliado', 'colunaEditarTodos' => true, 'colunaEditarExtra' => '1|0', 'colunaEditarTipo' => 'flag'),
            array('colunaValor' => 'T019_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:11px'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%'),
            array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco', 'style' => 'width: 1%'),
            array('colunaValor' => 'T019_Data_Lancamento', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T019_D014_Id', 'colunaTitulo' => 'Conta', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D014_SubConta', 'colunaEditar' => true,
				'colunaEditarGeraSelect' => array('D014', 'D014_Id', 'D014_SubConta, D032_Descricao', "LEFT JOIN D032 ON D032_Id=D014_D032_Id ORDER BY D014_SubConta")
			),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaExibir' => false),
            //array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo'),
            array('colunaValor' => 'T019_Referente', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T019_Complemento', 'colunaTitulo' => 'Complemento'),
            array('colunaValor' => 'T019_Valor_Lancamento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T019_Saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'IF(D027_Id > 0, D027_Portador, D031_Portador)', 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente/Fornecedor'),
            array('colunaValor' => 'T019_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T216_T215_Id', 'colunaTitulo' => 'Extrato', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Extrato', '/fin/fin003/content/fin003ContentTransacoes/', 'T216_T215_Id', '1000,600') ),
            array('colunaValor' => 'T216_Id', 'colunaTitulo' => 'Id Extrato', 'colunaAlinhar' => 'center' ),
			);

		$from = 'T019';
		$extra = <<<EOT
		    left join D024 on D024_Id=T019_D024_Id
			left join D027 on D027_Id=T019_D027_Id
			left join D031 on D031_Id=T019_D031_Id
			left join D032 on D032_Id=T019_D032_Id
			left join D014 on D014_Id=T019_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join T216 on T216_T019_Id=T019_Id
			LEFT JOIN D007 ON D007_Id=T019_D007_Id
			LEFT JOIN C004 ON C004_Id=T019_C004_Id
			ORDER BY T019_Data_Lancamento DESC, T019_Id DESC
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
		$gridFiltro = array(
			'T019_Referente' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T019_Complemento' => array('titulo' => 'Complemento', 'tipo' => 'texto'),
			'T019_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'T019_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T019_Valor_Lancamento' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T019_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'moeda'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
			'D007_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
			'C004_Nome_Empresa,C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'T019_Conciliado' => array('titulo' => 'Conciliado', 'tipo' => 'select', 'select' =>
				array(
					array('title' => 'Todos', 'value' => ''),
					array('title' => 'Sim', 'value' => '1'),
					array('title' => 'Não', 'value' => '0'),
				)
			),
			'D014_Grupo_DRE' => array('titulo' => 'DRE', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorTotalLancamentos  = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		$valorTotalRecebimentos = $g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0))", $from, $extra);
		$valorTotalPagamentos   = $g['sqlAuto']->pegarSqlCampo("sum(if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0))", $from, $extra);
		$valorTotalResultado    = ($valorTotalRecebimentos + $valorTotalPagamentos);
		$valorTotalLancamentos  = $valorTotalLancamentos;
		$valorTotalRecebimentos = gCorrigeNumero($valorTotalRecebimentos);
		$valorTotalPagamentos   = gCorrigeNumero($valorTotalPagamentos);
		$ResultadoTotal         = gCorrigeNumero($valorTotalResultado);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Lançamentos<br><input type="text" value="{$valorTotalLancamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Recebimentos<br><input type="text" value="{$valorTotalRecebimentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Pagamentos<br><input type="text" value="{$valorTotalPagamentos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Resultado<br><input type="text" value="{$ResultadoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total_Recebimentos', 'valor' => 'if(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0)', 'titulo' => 'Recebimentos'),
			array('nome' => 'Valor_Total_Pagamentos', 'valor' => 'if(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0)', 'titulo' => 'Pagamentos'),
			array('nome' => 'Valor_Total_Resultado','tipo'=>'totais', 'valor' => '({Valor_Total_Recebimentos}+
																					{Valor_Total_Pagamentos})', 'titulo' => 'Resultado'),
			
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
}
