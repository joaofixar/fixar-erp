<?php
namespace hardness;
// Carrega funções callback
require_once('prog/ven/ven012/grid_func-callback.php');
require_once('prog/est/est002/grid_func-callback-retornarExpedicao.php');

// Carrega funções do ajax
require_once('prog/ven/ven012/grid_func-js.php');
require_once('prog/est/est002/grid_func-js-retornarExpedicao.php');
require_once('old/lib/funcoes.php');


switch($g['r'][3]){
	/**
	* Definição do Grid (Tabela)
	*/
	case 'ven012GridPrincipal':
		$grid = array(
            array('colunaValor' => 'campoOpcoesGerarNota1', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesGerarNota1', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'FP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoOpcoesGerarNota2', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesGerarNota2', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'RP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoOpcoesGerarNota3', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesGerarNota3', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'IP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'campoOpcoesGerarNota5', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesGerarNota5', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'IP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoRetornarExpedicao', 'colunaTipo' => 'livre', 'callback' => 'campoRetornarExpedicao', 'style' => 'width:30px; padding: 3px 10px 3px 10px; background-color: #ececec;', 'colunaTitulo' => 'RE', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2, 'colunaExibirCallbackNivel' => 2),
            array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
            array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções', 'callback' => 'campoOpcoes', 'colunaExibir' => false),
	            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
	            array('colunaValor' => 'T005_Flag_Marcado', 'colunaTitulo' => '', 'callback' => 'validaCampoDuplicar', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T005A_Conferido', 'colunaTitulo' => 'Conferido', 'colunaEditar' => true, 'style' => 'min-width:100px;max-width:180px;white-space:normal;'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'if(T005_Flag_ACP=\'1\',\'NOTA FISCAL\',if(T005_Flag_ACP=\'3\',\'ACP\',if(T005_Flag_ACP=\'5\',\'NOTA D1\',if(T005_Flag_ACP=\'2\',\'NÃO FATURAR\',if(T005_Flag_ACP=\'6\',\'NF-e\',if(T005_Flag_ACP=\'7\',\'NFS-e\',if(T005_Flag_ACP=\'4\',\'CUPOM FISCAL\',if(T005_Flag_ACP=\'8\',\'NFC-e\',\'<font color=red>FALTA</font>\')))))))) as Tipo_Documento', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'exibir' => ''),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaAlinhar' => 'center', 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'T005_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total_Servicos', 'colunaTitulo' => 'Servicos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Troca', 'colunaTitulo' => 'Trocas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Prazo_Entrega', 'colunaTitulo' => 'Prazo entrega'),
            array('colunaValor' => 'CONCAT(T005_Observacao_1, " ", IFNULL((SELECT "NOTA CANCELADA!" FROM T007 WHERE T005_T007_Id = T007_Id  AND T007_Flag_Cancelada = "S" LIMIT 1), "")) as Observacao', 'colunaTitulo' => 'Observação', 'style' => 'color:#fc5a5a;max-width:200px;white-space:normal;'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D006_Descricao_Comercial', 'colunaExibir' => false, 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'IdRepresentante', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir' => false),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir' => false),
            array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status Serviços', 'colunaTipo' => 'livre', 'callback' => 'validaCampoStatusServicos', 'colunaExibir' => false),
        );
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
		    if($array['todosCampos']['T007_Flag_Cancelada'] == 'S') {
		        return 'linhaVermelho';
		    }
		}
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$pedidoGerarNFEmQualquerStatus = $g['C031']['pedidoGerarNFEmQualquerStatus'];
		if ($pedidoGerarNFEmQualquerStatus == 'S') {
			$extraStatus = "and (T005_Flag_Status is not NULL AND T005_Flag_Status != 0 AND T005_Flag_Status != '' AND T005_Flag_Status != 4 AND T005_Flag_Status != 5 and T005_Flag_Status != 8)";
		} else {
			$extraStatus = "and T005_Flag_Status='3'";
		}
		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D021 on D021_Id=T005_D021_Id
			left join D022 on D022_Id=T005_D022_Id
			left join D006 on D006_Id=T005_D006_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C004 on C004_Id=T005_C004_Id
			left join D036 on T005_D036_Id=D036_Id 
			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
			left join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas
			left join T005A on T005A_T005_Id=T005_Id
			where T005_C004_Id='{$g['empresaAtual']}'
			{$extraStatus}
			and ifnull(T005_T005_Id_Agrupado,0)<=0
			GROUP BY T005_Id 
			ORDER BY T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Data_Entrega' => array('titulo' => 'Entrega', 'tipo' => 'data'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'T005A_Conferido' => array('titulo' => 'Conferido', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                                  FROM T006 
                                                  LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        );
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		if (empty($r_linhaGridId)) {
			$extraSelecao = gInsertExtraWhere($extra, " T005_Flag_Marcado='S' AND T005_C004_Id = '{$g['empresaAtual']}' AND T005_Flag_Status != '8'");
			$sqlSelecao   = gComprimeSql("SELECT T005_Id, T005_Flag_Status, T007_Numero_Nota_Fiscal, T005_T005_Id_Agrupado, T005_Id_Pedido_Externo, T005_D024_Id FROM T005 $extraSelecao");

			/**
	         * Botões Almir
	         */
			$botoes = array(
				array('titulo' => 'Agrupar Pedidos', 'id' => 'ven002AgruparPedidos'),
				array('titulo' => 'Faturar', 'id' => 'ven012FaturarPedidos'),
			);
			echo Botoes::processar($botoes);
			if ($g['C031']['agruparPedidosEmEtapas'] == 'S') {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						function ven012AgruparPedidosEmEtapasLoop(idProgressBar, processoKey) {
							$.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidosEmEtapas/?ajax=true&processoKey=' + encodeURIComponent(processoKey) + '&callback=?', function(request) {
								if (!request.code) {
									$('#dc-' + idProgressBar).dialog("destroy").remove();
									dialogConfirm('Erro', request.data, false);
									$('#{$g['divId']}').hideLoading();
									return;
								}

								$('#progressBar').html('<b>Processado ' + request.percentual + '%</b><br/><br/>Itens processados: <b>' + request.ok + '</b><br/>Itens restantes: <b>' + request.restante + '</b>');

								if (request.concluido == 0) {
									ven012AgruparPedidosEmEtapasLoop(idProgressBar, processoKey);
									return;
								}

								$('#dc-' + idProgressBar).dialog("destroy").remove();
								dialogConfirm('Ok', 'Pedidos agrupados com sucesso!', false);
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							}).fail(function() {
								$('#dc-' + idProgressBar).dialog("destroy").remove();
								$('#{$g['divId']}').hideLoading();
								dialogConfirm('Erro', 'Ocorreu um erro ao processar o agrupamento em etapas.', false);
							});
						}

						$('#ven002AgruparPedidos, #{$g['divId']} button[botaoid="ven002AgruparPedidos"]').unbind('click.agruparPedidosEtapas').bind('click.agruparPedidosEtapas', function(data) {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidosEmEtapas/?ajax=true&sqlSelecao=' + encodeURIComponent('{$sqlSelecao}') + '&divIdParent=' + encodeURIComponent('{$g['divId']}') + '&callback=?', function(request) {
								if (request.code && request.processoKey) {
									var idProgressBar = dialogAlert('Agrupar pedidos', "<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
									ven012AgruparPedidosEmEtapasLoop(idProgressBar, request.processoKey);
									return;
								}

								if (request.code) {
									dialogConfirm('Ok', request.data, false);
									divRefresh('{$g['divId']}');
								} else {
									dialogConfirm('Erro', request.data, false);
								}
								$('#{$g['divId']}').hideLoading();
							}).fail(function() {
								$('#{$g['divId']}').hideLoading();
								dialogConfirm('Erro', 'Ocorreu um erro ao iniciar o agrupamento em etapas.', false);
							});
						});
					} </script></div>
EOT;
			} else {
				echo gProcessaEtapas('Agrupar pedidos','VEN002', "VEN002VerificarAgruparPedidosSelecionados();VEN002AgruparPedidosSelecionados()|false", "$sqlSelecao",'ven002AgruparPedidos');
			}
		}
        
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/*if (!personalizacaoBotoes()) { 
			echo "<div class='buttonsBottomTop'>";
				// Botão Agrupar
				echo gGeraBotao("Agrupar pedidos","ven002AgruparPedidos");
				echo gProcessaEtapas('Agrupar pedidos','VEN002', "VEN002VerificarAgruparPedidosSelecionados();VEN002AgruparPedidosSelecionados()|false", "$sqlSelecao",'ven002AgruparPedidos');
	        echo "</div>";
		}*/

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
		$sqlGridFiltro = base64_encode($geradoSql);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T005_Id', '', $extra));

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		        if ($('#{$g['divId']}').data('timeout') != 1) {
				    setInterval(function() {
				         if ($('#{$g['divId']}').data('faturamentoLoteAtivo') != 1) {
				             divRefresh('{$g['divId']}');
				         }
				    }, 60000);
			    $('#{$g['divId']}').data('timeout', 1);
			    }

					$('#ven012FaturarPedidos').unbind('click').bind('click', function(data) {
						var gridFaturamento = $('#{$g['divId']}');
						if (gridFaturamento.data('faturamentoLoteAtivo') == 1 || gridFaturamento.data('faturamentoLoteConfirmando') == 1) {
							return false;
						}
						gridFaturamento.data('faturamentoLoteConfirmando', 1);
						var verificacao = $.getJSON('/ven/ven012/grid_func-ajax/ven012VerificarFaturamentoLote/?ajax=true&callback=?', function(request) {
							if (request.code) {
								var \$buttons = {
								"Sim": function() {
									var pedidos = request.pedidos || [];
									var selecionados = pedidos.length;
									var processados = 0;
									var erros = [];
									var interrompido = false;
									var botaoFaturar = $('#ven012FaturarPedidos');
									gridFaturamento.data('faturamentoLoteConfirmando', 0).data('faturamentoLoteAtivo', 1).showLoading();
									botaoFaturar.prop('disabled', true);
									if (botaoFaturar.hasClass('ui-button')) {
										botaoFaturar.button('option', 'disabled', true);
									}
									var progressoHtml = '<div style="width:420px;max-width:80vw;">';
									progressoHtml += '<div id="ven012FaturamentoLoteTexto" style="margin-bottom:8px;">Preparando faturamento...</div>';
									progressoHtml += '<div style="height:16px;border:1px solid #aaa;background:#eee;"><div id="ven012FaturamentoLoteBarra" style="height:100%;width:0;background:#4f8fbd;"></div></div>';
									progressoHtml += '<div id="ven012FaturamentoLoteResumo" style="margin-top:8px;color:#555;">0 de ' + selecionados + ' processados</div></div>';
									var dialogoProgresso = dialogAlert('Faturamento em lote', progressoHtml);

									var atualizarProgresso = function(indice, pedido, mensagem) {
										var percentual = selecionados > 0 ? Math.round((indice / selecionados) * 100) : 0;
										$('#ven012FaturamentoLoteBarra').css('width', percentual + '%');
										$('#ven012FaturamentoLoteTexto').text(mensagem || ('Processando pedido ' + pedido + '...'));
										$('#ven012FaturamentoLoteResumo').text(indice + ' de ' + selecionados + ' processados');
									};

									var finalizarLote = function() {
										var pendentes = selecionados - processados;
										gridFaturamento.data('faturamentoLoteAtivo', 0).data('faturamentoLoteConfirmando', 0).hideLoading();
										botaoFaturar.prop('disabled', false);
										if (botaoFaturar.hasClass('ui-button')) {
											botaoFaturar.button('option', 'disabled', false);
										}
										if (dialogoProgresso) {
											$('#dc-' + dialogoProgresso).dialog('destroy').remove();
										}
										divRefresh('{$g['divId']}');
											var mensagem = '<div style="width:720px;max-width:calc(90vw - 60px);">';
											mensagem += '<div style="margin-bottom:12px;color:#555;">' + (interrompido ? 'O processamento foi interrompido. Os pedidos não iniciados continuam marcados.' : 'O processamento dos pedidos marcados foi finalizado.') + '</div>';
											mensagem += '<div style="display:flex;gap:10px;margin-bottom:14px;">';
											mensagem += '<div style="flex:1;padding:10px;border:1px solid #c8c8c8;background:#f5f5f5;"><div style="font-size:11px;color:#666;">SELECIONADOS</div><div style="font-size:20px;font-weight:bold;">' + selecionados + '</div></div>';
											mensagem += '<div style="flex:1;padding:10px;border:1px solid #9ccc9c;background:#eef8ee;"><div style="font-size:11px;color:#427142;">GERADOS</div><div style="font-size:20px;font-weight:bold;color:#267326;">' + processados + '</div></div>';
											mensagem += '<div style="flex:1;padding:10px;border:1px solid #dda5a5;background:#fff1f1;"><div style="font-size:11px;color:#8b3b3b;">PENDENTES</div><div style="font-size:20px;font-weight:bold;color:#a32626;">' + pendentes + '</div></div>';
											mensagem += '</div>';

										if (erros.length) {
											mensagem += '<div style="font-weight:bold;margin:0 0 6px 0;">Pedidos que precisam de correção</div>';
											mensagem += '<div style="max-height:320px;overflow:auto;border:1px solid #c8c8c8;">';
											mensagem += '<table style="width:100%;border-collapse:collapse;table-layout:fixed;">';
											mensagem += '<thead><tr style="background:#e8e8e8;"><th style="width:90px;padding:7px;text-align:left;border-bottom:1px solid #c8c8c8;">Pedido</th><th style="padding:7px;text-align:left;border-bottom:1px solid #c8c8c8;">Motivo</th></tr></thead><tbody>';
											$.each(erros, function(indice, erro) {
												mensagem += '<tr style="background:' + (indice % 2 ? '#fafafa' : '#fff') + ';">';
												mensagem += '<td style="padding:7px;vertical-align:top;border-bottom:1px solid #e1e1e1;font-weight:bold;">' + erro.pedido + '</td>';
												mensagem += '<td style="padding:7px;vertical-align:top;border-bottom:1px solid #e1e1e1;white-space:normal;word-break:break-word;">' + erro.mensagem + '</td>';
												mensagem += '</tr>';
											});
											mensagem += '</tbody></table></div>';
												mensagem += '<div style="margin-top:8px;color:#666;font-size:11px;">Corrija os pedidos acima e marque-os novamente para faturar.</div>';
												if (interrompido && pendentes > erros.length) {
													mensagem += '<div style="margin-top:8px;color:#8b3b3b;">' + (pendentes - erros.length) + ' pedido(s) não foram iniciados e permanecem marcados.</div>';
												}
											} else {
											mensagem += '<div style="padding:10px;border:1px solid #9ccc9c;background:#eef8ee;color:#267326;">Todas as notas foram geradas e estão disponíveis na fila de envio do Sefaz.</div>';
										}
										mensagem += '</div>';
											dialogConfirm(pendentes ? 'Faturamento concluído com pendências' : 'Faturamento concluído', mensagem);
									};

									var chamarEtapaPadrao = function(acao, dados, concluido, falha) {
										dados.ajax = true;
										$.ajax({
											url: '/ven/ven012/form_func-ajax/' + acao + '/?callback=?',
											data: dados,
											dataType: 'jsonp',
											timeout: 330000
										}).done(concluido).fail(falha);
									};

									var processarProximo = function(indice) {
										if (indice >= selecionados) {
											atualizarProgresso(selecionados, '', 'Processamento concluído.');
											finalizarLote();
											return;
										}
										var pedido = pedidos[indice];
										atualizarProgresso(indice, pedido);

										var finalizarPedido = function(sucesso, mensagem) {
											$.ajax({
												url: '/ven/ven012/grid_func-ajax/ven012FinalizarFaturamentoLote/?callback=?',
												data: { ajax: true, T005_Id: pedido },
												dataType: 'jsonp',
												timeout: 30000
											}).always(function() {
												if (sucesso) {
													processados++;
												} else {
													erros.push({ pedido: pedido, mensagem: mensagem || 'Não foi possível concluir o faturamento.' });
												}
												processarProximo(indice + 1);
											});
										};

										var interromperPorComunicacao = function() {
											interrompido = true;
											erros.push({ pedido: pedido, mensagem: 'A comunicação foi interrompida. O pedido não foi processado novamente por segurança.' });
											finalizarLote();
										};

										var totalizarNota = function(inicio) {
											chamarEtapaPadrao('gerarNFTotalizar', { T005_Id: pedido, inicio: inicio }, function(resultado) {
												if (resultado.code && resultado.concluido == 0) {
													totalizarNota(0);
												} else if (resultado.code) {
													finalizarPedido(true);
												} else {
													finalizarPedido(false, resultado.data || 'Erro ao totalizar os produtos da nota.');
												}
											}, interromperPorComunicacao);
										};

										var gerarNotaFiscal = function(formulario) {
											chamarEtapaPadrao('gerarNotaFiscal', formulario, function(resultado) {
												if (resultado.code) {
													totalizarNota(1);
												} else {
													finalizarPedido(false, resultado.data || 'Não foi possível gerar a nota fiscal.');
												}
											}, interromperPorComunicacao);
										};

										var totalizarPedido = function(formulario, inicio) {
											chamarEtapaPadrao('gerarNFTotalizarPedido', { T005_Id: pedido, inicio: inicio }, function(resultado) {
												if (resultado.code && resultado.concluido == 0) {
													totalizarPedido(formulario, 0);
												} else if (resultado.code) {
													gerarNotaFiscal(formulario);
												} else {
													finalizarPedido(false, resultado.data || 'Erro ao totalizar os produtos do pedido.');
												}
											}, interromperPorComunicacao);
										};

										$.ajax({
											url: '/ven/ven012/grid_func-ajax/ven012PrepararFaturamentoLote/?callback=?',
											data: { ajax: true, T005_Id: pedido },
											dataType: 'jsonp',
											timeout: 30000
										}).done(function(resultado) {
											if (resultado.code === false) {
												finalizarPedido(false, resultado.data || 'Não foi possível preparar o pedido para faturamento.');
												return;
											}
											if (resultado.erro) {
												finalizarPedido(false, resultado.erro);
												return;
											}
											if (resultado.recuperado) {
												finalizarPedido(true);
												return;
											}
											var formulario = {};
											$.each(resultado.form || {}, function(campo, valor) {
												formulario['ven012FormPedido[' + campo + ']'] = valor;
											});
											chamarEtapaPadrao('gerarNFValidacaoFormulario', formulario, function(validacao) {
												if (validacao.code) {
													totalizarPedido(formulario, 1);
												} else {
													finalizarPedido(false, validacao.data || 'Erro nas validações do pedido.');
												}
											}, interromperPorComunicacao);
										}).fail(interromperPorComunicacao);
									};
									processarProximo(0);
										$(this).dialog("destroy").remove();
									},
									"Não": function() {
										gridFaturamento.data('faturamentoLoteConfirmando', 0);
										$(this).dialog("destroy").remove();
									}
								};
								gridFaturamento.data('faturamentoLoteConfirmando', 0);
								dialogConfirm('Confirmar faturamento', 'Gerar ' + request.quantidade + ' nota(s) para os pedidos marcados?', \$buttons);
							}else{
								gridFaturamento.data('faturamentoLoteConfirmando', 0);
								dialogConfirm('Erro', request.data);
							}
						});
						verificacao.fail(function() {
							gridFaturamento.data('faturamentoLoteConfirmando', 0);
							dialogConfirm('Erro', 'Não foi possível verificar os pedidos marcados. Tente novamente.');
						});
						return false;
					});

				$('#ven012Marcar').unbind('click').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001MarcarTodosPedidos/?ajax=true&extra=' + encodeURIComponent('{$sqlGridFiltro}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						}
					});
				});

				$('#ven012Desmarcar').unbind('click').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001DesmarcarTodosPedidos/?ajax=true&extra=' + encodeURIComponent('{$sqlGridFiltro}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						}
					});
				});
		    } </script></div>
EOT;
        }

		/*if (!personalizacaoTotais()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}*/
	
		$novoIdTelaForm = uniqid();
		
		/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			$novoIdTelaForm = uniqid();
			if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
							if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
							var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');						
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,550]);            
						});
					} </script></div>
EOT;
			} else {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
							if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
							var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,550]);            
						});
					} </script></div>
EOT;
			}
		
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
	
	case 'ven012GridUltimasNotasEmitidas':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T007_Data_Envio_Email','colunaExibir' => false),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo'=>'livre', 'callback'=>'ValidaCampoDanfe', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Recibo', 'colunaTitulo' => 'Emitir Recibdo', 'colunaTipo'=>'livre', 'callback'=>'validaCampoRecibo', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'XML/Danfe', 'colunaTipo'=>'livre', 'callback'=>'validaCampoEmail', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
			array('colunaValor' => 'Boletos', 'colunaTitulo' => 'Boletos', 'colunaTipo'=>'livre', 'callback'=>'campoBoletosNF', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'ImprimirBoletos', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Impressão', 'callback' => 'campoImprimirBoletosNF', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback'=>'validaCampoCancelar', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false, 'chavePrimaria' => true),
			array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => 'Impressão', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir'=>false),
			array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
  			left join T005 on T005_Id=T007_T005_Id
			left join D024 on D024_Id=T007_D024_Id
			where (T007_C004_Id='{$g['empresaAtual']}'
			and T007_Numero_Nota_Fiscal!=''
			and ifnull(T007_Flag_Emitida_Recebida,'')!='R'
			and (T007_Numero_Protocolo_Nfe is not null
			or T007_Protocolo_Inutilizacao is not null))
EOT;
		if ($g['c020']['C020_Utiliza_Modulo_Orcamento']=='S')		{
			$extra .= "AND (T007_Flag_Orcamento!='S' or (T007_Flag_Orcamento='S' and T007_Numero_Nota_Fiscal!=''))";
		}
		// Definição da SQL
		$extra .= "order by T007_Data_Emissao desc,T007_Numero_Nota_Fiscal desc";
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven012/content/ven012ContentTotais/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [980,450]);
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
         
         /*
		 * Botões
		
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="button" id="ven012IncluirNotaFiscal">Incluir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven012IncluirNotaFiscal').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro Nota Fiscal', '/ven/ven012/content/ven012contentIncluirNotaFiscal/','', [900,550]);
			});
		} </script></div>
EOT;
 */   
		
	break;
	
	case 'ven012GridTotalDiariaEmitidas':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
 			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
 			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
 			array('colunaValor' => 'count(*) as Qte_Notas ', 'colunaTitulo' => 'Qte'),
 			array('colunaValor' => 'sum(T007_Valor_Total) as Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => 2),
     );
		
		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
	  		left join T005 on T005_Id=T007_T005_Id
		    left join D024 on D024_Id=T007_D024_Id
		    where (T007_C004_Id='{$g['empresaAtual']}' 
		    and T007_Flag_Cancelada!='S'
		    and ifnull(T007_Flag_Emitida_Recebida,'')!='R')
		    
EOT;
		if ($g['c020']['C020_Utiliza_Modulo_Orcamento']=='S')
		{
			   $extra .= "and (T007_Flag_Orcamento!='S' or (T007_Flag_Orcamento='S' and T007_Numero_Nota_Fiscal!=''))";
		}
		$extra .= "group by T007_Data_Emissao order by T007_Data_Emissao desc";
		
    	/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
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
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven012/content/ven012ContentTotais/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [980,450]);
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
         
         /**
		 * Botões
		 
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="button" id="ven012IncluirNotaFiscal">Incluir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven012IncluirNotaFiscal').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro Nota Fiscal', '/ven/ven012/content/ven012contentIncluirNotaFiscal/','', [900,550]);
			});
		} </script></div>
EOT;
*/     
		
	break;
	
		case 'ven012GridPagamento':
			$sqlT005 = "SELECT T005_Pgto_Data, T005_Pgto_C007_Id, C007_Login FROM T005 LEFT JOIN C007 ON C007_Id=T005_Pgto_C007_Id WHERE T005_Id='$r_acaoId'";
			$resultT005 = mysql_query($sqlT005);
			$linhaT005 = mysql_fetch_array($resultT005);
			$dataGerada = gCorrigeData($linhaT005['T005_Pgto_Data']);
			$user = strtoupper($linhaT005['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por ".$user." em ".$dataGerada."";
			}
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T090_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T090_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T090_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias"),
				array('colunaValor' => 'T090_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias"),
				array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "ven012_Dia_Semana"),
				array('colunaValor' => 'T090_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias"),
				array('colunaValor' => 'T090_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias", 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'order by D027_Portador', true), 'callback' => 'T090_D027_Id_Portador'),
				array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven012_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T090';

			$extra = <<<EOT
				left join D027 on D027_Id=T090_D027_Id
				WHERE T090_T005_Id = {$r_acaoId}
EOT;

			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T090_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T090_Prazos_Dias(T090_Id, campoEditado, valorAntigo, novoValor) {
					T090_Id = T090_Id.replace(/T090_Id-/, '');
					if (campoEditado == 'T090_Prazos_Dias') {
						$.getJSON('/ven/ven012/grid_func-ajax/atualizarParcelas/?ajax=true&T090_Id='      		+ encodeURIComponent(T090_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T090_Data_Vencimento') {
						$.getJSON('/ven/ven012/grid_func-ajax/atualizarParcelas/?ajax=true&T090_Id='      		+ encodeURIComponent(T090_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T090_Valor_Parcela') {
						$.getJSON('/ven/ven012/grid_func-ajax/atualizarParcelas/?ajax=true&T090_Id='      		+ encodeURIComponent(T090_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T090_D027_Id') {
						divRefresh('{$g['divId']}');						
					}
				}
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
   
   	case 'ven012GridFilaDeEnvio':
   		/*
   		*         ----EXIBE VENCIMENTO DO CERTIFICADO DIGITAL----	
   		*/
   		global $g;
   		$cSql_Empresa=@mysql_query("select * from C004 left join D018 on D018_Id=C004_D018_Id where C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=@mysql_fetch_array($cSql_Empresa);
			 if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S')
                    {
                       	   if (!empty($mEmpresa['C004_Certificado_Digital']))
                       	   {	
	                           $mkt_Hoje=time();
	                           $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
                       	   }	                         
                       	   elseif (!empty($mEmpresa['C004_Localizacao_Certificados']))
                       	   {	
                       	   	   $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
                       	  
	                           if (file_exists($cArquivo_Certificado_Digital))
	                           {
	                               $mkt_Hoje=time();
	                               $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
	                           }
                       	   }	  
                           
                           if (empty($mktVencimentoCertificado))
                           {
                              echo "<span style='padding-left:600px;'><span style='color:#fc5a5a;font-size:14px;'>Não foi possivel ler o certificado digital</span>
                              		</span>";
                           }
                           else
                           {
                               $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
                               if ($mktVencimentoCertificado>$mkt_Hoje)
                               {
                               	  $isCertificadoValido=true; 
                                  echo "<span style='padding-left:600px;'>
                                  			<span style='font-size:11px;'>Seu certificado digital vence em: <BR></span>
                                  			<span style='padding-left:615px;color:var(--BGAzul);font-size:14px;'>{$VencimentoCertificado}</span>
                                  		</span>"; 
                               }
                               else
                               {
                                  echo "<span style='padding-left:600px;'>
                                  			<span style='color:#fc5a5a;font-size:11px;'>CERTIFICADO DIGITAL VENCIDO EM:<BR></span>
                                  			<span style='padding-left:615px;color:yellow;font-size:14px;'>{$VencimentoCertificado}</span>
                                  		</span>";
                               }
                           }
                    }
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'campoInutilizar','colunaTipo' => 'livre', 'callback'=>'campoInutilizar'),
				array('colunaValor' => 'campoGerarXML', 'colunaTipo' => 'livre','callback'=>'campoGerarXML'),
				array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
				array('colunaValor' => 'T007_Data_Saida', 'colunaTitulo' => 'Saída', 'callback'=>'gCorrigeData'),
				array('colunaValor' => 'T007_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e'),
				array('colunaValor' => 'T007_Cliente_Razao', 'colunaTitulo' => 'Destinatário'),
				array('colunaValor' => 'T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
				array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'style'=>'font-size:14px;'),
				array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave de Acesso'),
				array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo'),
				array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão'),
				array('colunaValor' => '(select T127_Retorno_Sefaz from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id and T127_Retorno_Sefaz not like "Gerou Chave Acesso%" limit 1) as T127_Id', 'colunaTitulo' => 'Retorno Sefaz'),
				array('colunaValor' => 'T007_Data_Envio_Email_Boleto', 'colunaTitulo' => 'T007_Data_Envio_Email_Boleto', 'colunaExibir'=>false),
			);
			
			/**
			*
			* Definição da SQL
			*/
			$from = 'T007';

			$extra = <<<EOT
				where T007_Flag_ACP=6
				and T007_C004_Id={$g['empresaAtual']}
				and T007_Numero_Nota_Fiscal!=''
				and T007_Numero_Nota_Fiscal is not null
				and T007_Flag_Cancelada='N'
				and T007_Flag_Almoxarifado='S'
				and (T007_Numero_Recibo_Nfe is null or T007_Numero_Protocolo_Nfe is null)
				order by T007_Numero_Nota_Fiscal
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
			
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
			});
		} </script></div>
EOT;
   break;

   	
    case 'ven012GridProdutosPedido':
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Item', 'chavePrimaria' => true),
            array('colunaValor' => 'T006_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaExibir' => false),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Flag_Situacao_Tributaria', 'colunaTitulo' => 'Sit.Trib', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_D006_Id', 'colunaTitulo' => 'CFOP', 'colunaEditarGeraSelect' => array('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', ' ORDER BY D006_Codigo_CFOP ', true), 'colunaEditar' => true, 'callback' => 'T006_D006_Id_CFOP'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaExibir' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto'),
            array('colunaValor' => 'T006_Valor_Total_Custo_Final', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'ultimoPrecoVenda', 'colunaTitulo' => 'Ultimo Preço', 'colunaAlinhar' => 'right', 'callback' => 'atualizarPrecoProduto', 'colunaTipo' => 'livre', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Aliquota_ICMS', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_ICMS', 'colunaTitulo' => 'Valor ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Descricao', 'colunaTitulo' => 'Descrição CFOP'),
            array('colunaValor' => 'T006_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
        );

		/**
		 * Definição da SQL
		 */
		$from = "T006";
		
		$extra = <<<EOT
            left join D009 on D009_Id=T006_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D049_D001_Id=D001_Id
            left join T005 on T006_T005_Id=T005_Id
            left join D006 on D006_Id=T006_D006_Id
            left join D037 on D037_Id=D001_D037_Id
            left join D005 on D005_Id=D001_D005_Id
            where (T006_T005_Id={$r_acaoId}
            and 
            (T006_T006_Id is null or T006_T006_Id<=0)
            and 
            (T006_Quantidade>0)
            and 
            T006_Flag_Divergencia!='S')
            order by T006_Flag_Tipo_Produto,
            T006_D009_Id,
            T006_Id
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
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'T006_Id';    
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
                    //abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/ven/ven002/content/ven002ContentEditarProduto/', '&acaoId=' + encodeURIComponent(acaoId) + '&T005_Id=' + encodeURIComponent('{$r_acaoId}'), [600,400]);
            });

			function colunaEditarCallbackJS_T006_Percentual_Desconto(T006_Id, campoEditado, valorAntigo, novoValor) {
				T006_Id = T006_Id.replace(/T006_Id-/, '');
				if(campoEditado == 'T006_Valor_Preco_Sem_Desconto_Unitario'){
					$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				}
			}
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
	
	case 'ven012GridServicosPedido':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'excluirServico'),
			array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
			array('colunaValor' => "concat(T147_Titulo, ' ', T147_Descricao) as Servico", 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'T147_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'T147_Quantidade', 'colunaTitulo' => 'Quantidade'),
			//array('colunaValor' => 'T147_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
			array('colunaValor' => 'T147_T145_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
			//array('colunaValor' => 'T147_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'ven002Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
			array('colunaValor' => 'T147_D116_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'colunaExibir' => false),
			//array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status'),
			array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
			);
	
			/**
				*
				* Definição da SQL
				*/
			$from = 'T147';
	
			$extra = <<<EOT
					LEFT JOIN T006 ON T006_Id=T147_T006_Id
					LEFT JOIN D009 ON D009_Id=T006_D009_Id
					LEFT JOIN D049 ON D009_D049_Id=D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					WHERE T147_T005_Id='{$r_acaoId}'
EOT;
	

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
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
	
	case 'ven012GridTrocasPedido':
		
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T138_Id', $g['divId'])),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => false,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => false,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
			//array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback'=>'retornaOperacaoTroca'),						
			array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'colunaEditar' => true,'colunaEditarGeraSelect' => array(array('title' => 'PARTE PAGAMENTO', 'value' => 'P', 'selected' => true),array('title' => 'TROCA', 'value' => 'T')), 'callback' => 'retornaOperacaoTroca'),
			array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);
		
		
								

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T138';

		$extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
			WHERE T138_T005_Id = '{$r_acaoId}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		/*
		function gridLinha($array) {
			if($array['todosCampos']['T006_T005_Id']>0){
				return 'linhaCinza';
			}
		}
		*/

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
			//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
			function colunaEditarCallbackJS_Grid_Refresh() {
				divRefresh('{$g['divId']}');
			}
		} </script></div>
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
    
    case 'van012GridProdutosDividir':
        /**
            * Definição do Grid (Tabela)
            */
            
        //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Produtos</div>';
        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'excluirProduto'),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T006', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir','callbackParameter' => 'T006', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade_Dividir', 'colunaTitulo' => 'Qtd. Dividir', 'colunaAlinhar' => 'right','colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaExibir'=>false),
            //array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'Item', 'colunaAlinhar' => 'center' ),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            //array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Código Cli.', 'colunaAlinhar' => 'left'),
            //array('colunaValor' => 'T006_Numero_Pedido_Compra', 'colunaTitulo' => 'Num. OC', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T006_Item_Pedido_Compra', 'colunaTitulo' => 'Item OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3' ),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtd', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2','colunaEditar' => true),
            //array('colunaValor' => 'T006_Valor_Preco_Original', 'colunaTitulo' => 'Original', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T006_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Total_Preco', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            array('colunaValor' => 'Duplicar', 'colunaTitulo' => '','colunaTipo' => 'livre', 'style'=>'width:1%;', 'callback'=>'ven012DuplicarProduto'),            
            //array('colunaValor' => 'T006_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T006_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T006_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir'=>false),
            // array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T006_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade as T006_Valor_ICMS_Substituicao', 'colunaTitulo' => 'Valor ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
        );

        /**
        *
        * Definição da SQL
        */
        $from = 'T006';

        $extra = <<<EOT
                left join T005 on T005_Id=T006_T005_Id
                left join T008 on T008_T006_Id=T006_Id
                left join T007 on T007_Id=T008_T007_Id
                left join D024 on D024_Id=T006_D024_Id
                left join D037 on D037_Id=T006_D037_Id
                left join D009 on D009_Id=T006_D009_Id
                left join D049 on D049_Id=D009_D049_Id
                left join D001 on D001_Id=D049_D001_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D006 on D006_Id=T006_D006_Id
                left join D005 on D005_Id=T006_D005_Id
                left join C008 on C008_Id=D001_C008_Id
                where 
                T006_T005_Id = '{$r_acaoId}'
                
EOT;
        if (!empty($xConsulta_Clausula_Filtro)) {
            $extra .= <<<EOT
                      and $xConsulta_Clausula_Filtro
EOT;
        }
        
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
            
        //Define ação do click de abertura (ao clicar em uma linha do grid)
        $novoIdTelaForm = uniqid();
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'T006_Id';    
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
            });
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
            
            // Função JS executada como callback do editar campo (colunaEditarCallbackJS)
            function colunaEditarCallbackJS_T006_Percentual_Desconto(T006_Id, campoEditado, valorAntigo, novoValor) {
                    T006_Id = T006_Id.replace(/T006_Id-/, '');
                    if(campoEditado == 'T006_Percentual_Desconto'){
                        $.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
                                                                                          '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}');
                                //alert("NFe Excluída");
                            } else {
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    }else if(campoEditado == 'T006_Valor_Preco_Sem_Desconto_Unitario'){
                        $.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
                                                                                          '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}');
                                //alert("NFe Excluída");
                            } else {
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    }
                }

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
        
     case 'ven012GridServicosDividir':

            /**
            * Definição do Grid (Tabela)
            */
            //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Serviços</div>';
            $grid = array(
            array('colunaValor' => 'T147_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T147', 'style' => 'width:1%'),
            array('colunaValor' => 'T147_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir','callbackParameter' => 'T147', 'style' => 'width:1%'),
            array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            //array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => "concat(T147_Titulo, ' ', T147_Descricao) as Descricao", 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => 'T147_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'T147_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
            //array('colunaValor' => 'T147_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            array('colunaValor' => 'T147_T145_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            //array('colunaValor' => 'T147_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'ven002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
            array('colunaValor' => 'T147_D116_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'colunaExibir'=>false),
            array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NFSE', 'callback' => 'ven012CampoNFServicos','style' => 'width:1%;'),
            //array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir'=>false),
            );
    
            /**
                *
                * Definição da SQL
                */
            $from = 'T147';
    
            $extra = <<<EOT
                    LEFT JOIN T006 ON T006_Id=T147_T006_Id
                    left join T203 on T203_T147_Id=T147_Id
                	left join T007 on T007_Id=T203_T007_Id
                    LEFT JOIN D009 ON D009_Id=T006_D009_Id
                    LEFT JOIN D049 ON D009_D049_Id=D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    WHERE T147_T005_Id='{$r_acaoId}'
EOT;

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 		 $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
                //divSetarAcaoId('{$g['divId']}', id);
                //divRefresh('{$g['divId']}');
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
        
    case 'ven012GridTrocasDividir':
        
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
        /**
         * Definição do Grid (Tabela)
         */
         //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Trocas</div>';
        $grid = array(
            array('colunaValor' => 'T138_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T138', 'style' => 'width:1%'),
            array('colunaValor' => 'T138_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir','callbackParameter' => 'T138', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Troca'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
            array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
            array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
            array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação'),
        );
        
        
                                

        /**
         *
         * Definição da SQL
         */
        $from = 'T138';

        $extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            left join T008 on T008_T138_Id=T138_Id
            left join T007 on T007_Id=T008_T007_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
            WHERE T138_T005_Id = '{$r_acaoId}'
EOT;

        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        /*
        function gridLinha($array) {
            if($array['todosCampos']['T006_T005_Id']>0){
                return 'linhaCinza';
            }
        }
        */

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
            //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        	$('#{$g['divId']} .gridOptTop');
        } </script></div>
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


    case 'van012GridProdutosDividirEmpresa':

        /**
        * Definição do Grid (Tabela)
        */		

		$exibirQuantidadeSeparada = ($g['C031']['modeloExpedicao'] == 2) ?  true : false ;

		// Valida colunas exibir para conversão de unidade
		$colunaQuantidadeComercial 	         = false;	
		$colunaQuantidade                    = true;
		// Só valida conversão se for diferente de preço bruto, primeira versão da conversão de unidade trata apenas para liquido (Felipe 26/06/2023)
		if($g['C031']['utilizaUnidadeConversaoVenda'] == 'S'){
			// Colunas T004A que são utilizadas na conversão
			$colunaQuantidadeComercial 	         = true;
			$colunaQuantidade                    = false;
		}

		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_T006_Id_Dividir', 'colunaTitulo' => 'Id div', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao', 'callbackParameter' => 'T006', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir', 'callbackParameter' => 'T006', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte pedido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => ';background:lightvar(--BGAzul)', 'colunaExibir' => $colunaQuantidade),
			array('colunaValor' => 'T006A_Quantidade_Comercial', 'colunaTitulo' => 'Qte pedido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => ';background:lightvar(--BGAzul)', 'colunaExibir' => $colunaQuantidadeComercial),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separ.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => $exibirQuantidadeSeparada),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => ';background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'Qte OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade_Dividir', 'colunaTitulo' => 'Qte novo pedido', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Quantidade_Dividir'),
            //array('colunaValor' => 'T006_Flag_Reservar_Estoque', 'colunaTitulo' => 'Reservado', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array('T006_Flag_Reservar_Estoque', 'N|S'), 'colunaExibir' => false),
            array('colunaValor' => 'T006_Prioridade', 'colunaTitulo' => 'Prioridade', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Dividido', 'colunaTitulo' => 'Dividido', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descricao Produto'),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço unitário', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(T006_Valor_Preco_Sem_Desconto_Unitario*T006_Quantidade_Estoque) as Total_Disponivel', 'colunaTitulo' => 'Total disponível', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(T006_Valor_Preco_Sem_Desconto_Unitario*T006_Quantidade_Dividir) as Total_Dividir', 'colunaTitulo' => 'Total dividir', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'if(T006_Quantidade_Estoque<T006_Quantidade,T006_Quantidade_Estoque,T006_Quantidade)*T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Valor dispnível', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade_Dividir*T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Valor dividir', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#caffe4', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'background: var(--celula-amarelo-claro);'),
            array('colunaValor' => '(T005_Valor_Desconto/T005_Valor_Total_Produtos)*(T006_Valor_Preco_Unitario*T006_Quantidade)', 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '(T006_Valor_Total_Preco*(T006_Aliquota_IPI/100))', 'colunaTitulo' => 'Valor IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaExibir' => false),
            array('colunaValor' => 'Duplicar', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'style' => 'width:1%;', 'callback' => 'ven012DuplicarProduto', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T005A_Flag_Conversao_Unidade', 'colunaTitulo' => 'T005A_Flag_Conversao_Unidade', 'colunaExibir' => false),

        );


		$extraConversao = '';
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			array_push($grid, array('colunaValor' => 'T006A_Quantidade_Conversao', 'colunaTitulo' => 'Qtde Conv.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_QuantidadeConversao_Dividir'));
			array_push($grid, array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso Unitário KG', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'));
			array_push($grid, array('colunaValor' => 'D002A_Utiliza_Conversao', 'colunaTitulo' => 'D002A_Utiliza_Conversao', 'colunaExibir' => false));

			$extraConversao  = ' left join D002 on D002_Id = D001_D002_Id ';
			$extraConversao .= ' left join D002A on D002_Id = D002A_D002_Id ';
			$extraConversao .= ' left join T006A on T006_Id=T006A_T006_Id ';

		}

        /**
            *
            * Definição da SQL
            */
        $from = 'T006';

        $extra = <<<EOT
            left join T005 on T005_Id=T006_T005_Id
			left join T008 on T008_T006_Id=T006_Id
			left join T007 on T007_Id=T008_T007_Id
			left join D024 on D024_Id=T006_D024_Id
			left join D037 on D037_Id=T006_D037_Id
			left join D009 on D009_Id=T006_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=T006_D006_Id
			left join D005 on D005_Id=T006_D005_Id
			left join C008 on C008_Id=D001_C008_Id
			left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			left join T006A on T006A_T006_Id = T006_Id
			left join T005A on T005A_T005_Id = T005_Id
			{$extraConversao}
			where T005_D024_Id = '{$r_acaoId}'
			and (T008_Id is null or T007_Flag_Cancelada = 'S')
			and T005_C004_Id='{$g['empresaAtual']}'
			and (T005_Flag_Status='' or T005_Flag_Status='0' or T005_Flag_Status='26' or T005_Flag_Status='9' or T005_Flag_Status='14' or T005_Flag_Status ='22')
			and T006_Quantidade > 0 
			and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
EOT;

		$D024_Id = $r_acaoId;

/*         echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven012DividirPedidoEmpresaBtn">Dividir</button>
				<button type="button" id="ven012DefinirQuantidadeBtn">Preencher quantidade</button>
				<button type="button" id="ven012LimparQuantidadeBtn">Limpar quantidade</button>
				<!-- <button type="button" id="ven012GerarNFMarcados">Gerar NF</button> -->
				<!-- <button type="button" id="ven012ConcluirNF">Concluir</button> -->
				<!-- <button type="button" id="ven012MudarFilial">Trocar empresa</button> -->
				<!-- <button type="button" id="ven012DuplicarPedido">Duplicar</button> -->
				<div style="clear:both;"></div>
			</div>
EOT;
 */
		$botoes = array(
			array('titulo' => 'Dividir', 'id' => 'ven012DividirPedidoEmpresaBtn'),
			array('titulo' => 'Preencher quantidade', 'id' => 'ven012DefinirQuantidadeBtn'),
			array('titulo' => 'Limpar quantidade', 'id' => 'ven012LimparQuantidadeBtn'),
		);
		echo Botoes::processar($botoes);

        /**
        * Filtro
        */
		$gridFiltro = array(
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'T006_Codigo_Produto' => array('titulo' => 'Código Produto', 'tipo' => 'texto', 'exibir' => true),
            'T006_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto', 'exibir' => true),
            'T005_Data_Emissao' => array('titulo' => 'Data de Emissão', 'tipo' => 'data', 'exibir' => true),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Tabela', 'valor' => 'T006_Valor_Total_Preco', 'titulo' => 'Total R$'),
            array('nome' => 'Valor_Disponivel', 'valor' => 'Total_Disponivel', 'titulo' => 'Total disponível R$'),
            array('nome' => 'Valor_Dividir', 'valor' => 'Total_Dividir', 'titulo' => 'Total dividir R$'),
        );
            
        //Define ação do click de abertura (ao clicar em uma linha do grid)
        $novoIdTelaForm = uniqid();
        if (empty($r_linhaGridId)) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .gridOptTop');
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
						var chavePrimaria = 'T006_Id';    
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');
						// Ação: abrir nova janela
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
				function colunaEditarCallbackJS_T006_Percentual_Desconto(T006_Id, campoEditado, valorAntigo, novoValor) {
						T006_Id = T006_Id.replace(/T006_Id-/, '');
						if(campoEditado == 'T006_Percentual_Desconto'){
							$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																							'&campoEditado='        + encodeURIComponent(campoEditado) +
																							'&valorAntigo='        + encodeURIComponent(valorAntigo) +
																							'&novoValor='            + encodeURIComponent(novoValor) +  
																							'&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}');
									//alert("NFe Excluída");
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
						} else if (campoEditado == 'T006_Valor_Preco_Sem_Desconto_Unitario'){
							$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																							'&campoEditado='        + encodeURIComponent(campoEditado) +
																							'&valorAntigo='        + encodeURIComponent(valorAntigo) +
																							'&novoValor='            + encodeURIComponent(novoValor) +  
																							'&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}');
									//alert("NFe Excluída");
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
						}
					}
				}

				function colunaEditarCallbackJS_T006_Quantidade_Dividir(T006_Id, campoEditado, valorAntigo, novoValor) {
					T006_Id = T006_Id.replace(/T006_Id-/, '');
					$.getJSON('/ven/ven012/grid_func-ajax/ven012validaQuantidadeDividir/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) +
																							'&novoValor='       + encodeURIComponent(novoValor) +
																							'&valorAntigo='        + encodeURIComponent(valorAntigo) +  
																							'&callback=?', function(request) {
						if(request.code != true){
							dialogConfirm('Erro', request.data);
						}
						divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id');
						// gravar alteração
						$.getJSON('/ven/ven012/grid_func-ajax/est002LogAlteracoesT006QuantidadeDividir/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + 
																											'&campoEditado=' +  encodeURIComponent(campoEditado) +
																											'&valorAntigo=' + encodeURIComponent(valorAntigo) +
																											'&novoValor=' + encodeURIComponent(novoValor) +  
																											'&local=' + encodeURIComponent('{$g['r_']}') +  
																											'&callback=?', function(request) {
							divRefresh('{$g['divReload']}');
						});
					});     	
				}

				function colunaEditarCallbackJS_T006_QuantidadeConversao_Dividir(T006_Id, campoEditado, valorAntigo, novoValor) {
					T006_Id = T006_Id.replace(/T006_Id-/, '');
					$.getJSON('/ven/ven012/grid_func-ajax/ven012validaQuantidadeConversaoDividir/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) +
																							'&campoEditado=' +  encodeURIComponent(campoEditado) +
																							'&novoValor='       + encodeURIComponent(novoValor) +
																							'&valorAntigo='        + encodeURIComponent(valorAntigo) +  
																							'&callback=?', function(request) {
						if(request.code != true){
							dialogConfirm('Erro', request.data);
						}
						divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id');
					});     	
				}
				</script></div>
EOT;

			$novoIdTelaForm = uniqid();
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
					$('#ven012DividirPedidoEmpresaBtn').bind('click', function(data) {
						$.getJSON('/ven/ven012/grid_func-ajax/ven012VerificaItensDividirEmpresa/?sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
							if(request.code){
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Selecione a empresa', '/ven/ven012/content/ven012ContentSelecionarEmpresa/', '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&dividirPedidoEmpresa=true', [400,200]);
							}else{
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					});
				
					$('#ven012MudarFilial').bind('click', function(data) {
						$.getJSON('/ven/ven012/grid_func-ajax/ven012VerificaItensMudarFIlial/?ajax=true&callback=?', function(request) {
							if(request.code){
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven012/content/ven012ContentSelecionarEmpresa/', '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&mudarFilial=true', [400,200]);
							}else{
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', 'Não há itens neste pedido', \$buttons);
							}
						});
					});
						
					$('#ven012GerarNFMarcados').bind('click', function(data) {
						$.getJSON('/ven/ven012/grid_func-ajax/ven012VerificaItensGerarNF/?ajax=true&callback=?', function(request) {
							if(request.code){
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven012/content/ven012ContentSelecionarEmpresa/', '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&gerarNF=true', [400,200]);
							}else{
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', 'Não há itens neste pedido', \$buttons);
							}
						});
					});
					
					$('#ven012ConcluirNF').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/ven/ven012/grid_func-ajax/ven012ConcluirPedido/?ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
										divRefresh('{$g['divId']}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Conclusão', 'Você tem certeza?', \$buttons);
					});

					$('#ven012DefinirQuantidadeBtn').bind('click', function(data) {
						console.log('{$g['divId']}');
						var \$buttons = {
							"Quantidade pedido": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/ven/ven012/grid_func-ajax/ven012DefinirQuantidadePedido/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$r_divRefresh}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Quantidade estoque": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/ven/ven012/grid_func-ajax/ven012DefinirQuantidadeSeparada/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$r_divRefresh}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							...( '{$g['C031']['modeloExpedicao']}' == 2
							? {"Quantidade separada": function() {
									$('#' + '{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/grid_func-ajax/ven012PreencherQuantidadeSeparada/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
										if (request.code) {
											divRefresh('{$r_divRefresh}', true);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#' + '{$g['divId']}').hideLoading();
										reloadUI('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								}
							}
							: {}
						),
						"Fechar": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Definir quantidade', 'Deseja definir as quatidades á dividir com base na quantidade do pedido ou quantidade separada?', \$buttons);
					});

					$('#ven012DuplicarPedido').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/ven/ven012/grid_func-ajax/ven012DuplicarPedido/?ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
										divRefresh('{$g['divId']}', true);
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + request.T005_Id_Novo, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(request.T005_Id_Novo) + '&tabela=T005', [1000,615]);            
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Duplicar Pedido', 'Você tem certeza que deseja duplicar o pedido?', \$buttons);
					});
					
					$('#ven012LimparQuantidadeBtn').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/ven/ven012/grid_func-ajax/ven012LimparQuantidades/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', "Operação realizada com sucesso!", \$buttons);
										divRefresh('{$r_divRefresh}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Atanção!', 'Este processo ira zerar a quantidade á dividir. Você tem certeza?', \$buttons);
					});
			} </script></div>
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

    case 'ven012GridTrocasDividirEmpresa':
        
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;

        $D024 = mysql_query("SELECT T005_D024_Id from T005 WHERE T005_Id='{$r_acaoId}'");
        $mD024 = mysql_fetch_array($D024);
        $D024_Id = $mD024['T005_D024_Id'];
        /**
         * Definição do Grid (Tabela)
         */
         //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Trocas</div>';
        $grid = array(
            array('colunaValor' => 'T138_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T138', 'style' => 'width:1%'),
            array('colunaValor' => 'T138_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir','callbackParameter' => 'T138', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Troca'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
            array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
            array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
            array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação'),
        );

        /**
         *
         * Definição da SQL
         */
        $from = 'T138';

        $extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            left join T008 on T008_T138_Id=T138_Id
            left join T007 on T007_Id=T008_T007_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
            LEFT JOIN T005 on T005_Id=T138_T005_Id
            WHERE T005_D024_Id = '{$D024_Id}'
EOT;

        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        /*
        function gridLinha($array) {
            if($array['todosCampos']['T006_T005_Id']>0){
                return 'linhaCinza';
            }
        }
        */

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
            //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        	$('#{$g['divId']} .gridOptTop');
        } </script></div>
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


    case 'ven012GridServicosDividirEmpresa':

    	$D024 = mysql_query("SELECT T005_D024_Id from T005 WHERE T005_Id='{$r_acaoId}'");
        $mD024 = mysql_fetch_array($D024);
        $D024_Id = $mD024['T005_D024_Id'];

            /**
            * Definição do Grid (Tabela)
            */
            //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Serviços</div>';
		$grid = array(
            array('colunaValor' => 'T147_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T147', 'style' => 'width:1%'),
            array('colunaValor' => 'T147_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir','callbackParameter' => 'T147', 'style' => 'width:1%'),
            array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            //array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => "concat(T147_Titulo, ' ', T147_Descricao) as Descricao", 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => 'T147_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'T147_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
            //array('colunaValor' => 'T147_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            array('colunaValor' => 'T147_T145_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            //array('colunaValor' => 'T147_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'ven002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
            array('colunaValor' => 'T147_D116_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'colunaExibir'=>false),
            array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            //array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir'=>false),
        );
    
            /**
                *
                * Definição da SQL
                */
            $from = 'T147';
    
            $extra = <<<EOT
                    LEFT JOIN T006 ON T006_Id=T147_T006_Id
                    left join T203 on T203_T147_Id=T147_Id
                	left join T007 on T007_Id=T203_T007_Id
                    LEFT JOIN D009 ON D009_Id=T006_D009_Id
                    LEFT JOIN D049 ON D009_D049_Id=D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    LEFT JOIN T005 ON T005_Id='{$r_acaoId}'
                    WHERE T005_D024_Id='{$D024_Id}'
EOT;

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 		 $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
                //divSetarAcaoId('{$g['divId']}', id);
                //divRefresh('{$g['divId']}');
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

    case 'ven012GridAgrupamentoPedido':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoPedido', 'colunaExibir'=>false),
			//array('colunaValor' => "campoCancelamento", 'colunaTipo' => "livre", 'callback' => 'campoCancelamento', 'style'=>'width:1%;'),
			//array('colunaValor' => "T005_Flag_Marcado", 'colunaTitulo'=>'', 'callback' => 'validaCampoDuplicar', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => ';width:1%'),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status_Pedido', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'group_concat("<span style=\"\">",T005_Status_Pedido(T005_Flag_Status,1),"</span> ", ifnull((SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1),"")) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
			//array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF','callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),                                
			array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor'),
			//array('colunaValor' => 'Status as Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipal'),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) FROM T006 WHERE T006_T005_Id=T005_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
						FROM T006 
						LEFT JOIN D009 ON D009_Id = T006_D009_Id
						LEFT JOIN D049 ON D049_Id = D009_D049_Id
						LEFT JOIN D082 ON D082_Id = D049_D082_Id
						WHERE T006_T005_Id=T005_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
			array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback'=>'downloadXMLPedido', 'colunaTipo'=>'livre', 'colunaExibir'=>false),
			array('colunaValor' => 'DividirPedidoCliente', 'colunaTitulo' => '', 'callback'=>'dividirPedidoCliente', 'colunaTipo'=>'livre', 'colunaExibir'=>false, 'style'=>"width:1%;"),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T005';
		$extra = <<<EOT
				left join D024 on D024_Id=T005_D024_Id
    			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     		    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    			left join D006 on D006_Id=T005_D006_Id
    			where (T005_C004_Id={$g['empresaAtual']} AND (T005_T005_Id_Agrupado={$r_T005_Id}))
    			GROUP BY T005_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
				left join D024 on D024_Id=T005_D024_Id
    			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     		    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    			left join D006 on D006_Id=T005_D006_Id
    			where (T005_C004_Id={$g['empresaAtual']} AND (T005_T005_Id_Agrupado={$r_T005_Id}))
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T005_Flag_Status']=='5'){
				return 'linhaCinza';
			}
			else if($array['todosCampos']['T005_Flag_Status']<=0){
				return 'linhaAzul';
			}
		}

        echo <<<EOT
        <div class="buttonsBottomTop">
EOT;
		if($g['C031']['usarBotaoIncluirPedido'] == "S"){
		echo <<<EOT
		<!-- <button type="button" id="ven002IncluirPedido">Incluir</button> -->
EOT;
		}
		echo <<<EOT
            <!-- <button type="button" id="ven002ProcessarPedidosVencidos" onclick="processarPedidosVencidos('{$g['divId']}');">Processar Vencidos</button> -->
            <!-- <button type="button" id="ven002DuplicarPedidos">Duplicar pedidos</button> -->
            <!-- <button type="button" id="ven002AgruparPedidos">Agrupar pedidos</button> -->
        </div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0[[virgula]]"MP"[[virgula]]"PE"))) FROM T006 WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Estoque', 'tipo' => 'texto', 'exibir'=>false),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
									FROM T006 
									LEFT JOIN D009 ON D009_Id = T006_D009_Id
									LEFT JOIN D049 ON D049_Id = D009_D049_Id
									LEFT JOIN D082 ON D082_Id = D049_D082_Id
									WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Marca', 'tipo'=>'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		if (!personalizacaoTotais()) { 
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T005_Valor_Total)", $from, $extraTotal));
			$valorTotalPendentes = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T005_Valor_Total)", $from, $extraTotal));
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		//$msg = gGeraAlertMsg('Erro!', 'Não existem itens para serem duplicados!');
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T005_Id = id[0].replace(/T005_Id-/, '');
				var T007_Id = id[1].replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + T005_Id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [1000,615]);            
			});
			$('#ven002DuplicarPedidos').unbind('click').bind('click', function(data) {
				$.getJSON('/crm/crm001/grid_func-ajax/crm001DuplicarPedido/?ajax=true&callback=?', function(request) {
					if (request.code) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Duplicar Pedidos', '/crm/crm001/content/crm001ContentSelecionarDiasDuplicar/', '&T003_Id=' + encodeURIComponent('$r_acaoId'), [300,150]);
					}else{
						dialogConfirm('Erro', 'Não existem pedidos selecionados para serem duplicados!');
					}
				});
			});
			$('#ven002AgruparPedidos').unbind('click').bind('click', function(data) {
				$.getJSON('/crm/crm001/grid_func-ajax/crm001VerificaAgruparPedidos/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidos/?ajax=true&callback=?', function(request) {
									if (request.code) {
										dialogConfirm('Operação Ok!', 'Processo concluído com sucesso');
										divRefresh('{$g['divId']}');
										$('#' + '{$g['divId']}').hideLoading();
									}else{
										dialogConfirm('Erro', request.data);
										$('#' + '{$g['divId']}').hideLoading();
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Você tem certeza?', 'Atenção, este processo irá gerar um novo pedido com todos os produtos dos pedidos selecionados.<br /><br />Você tem certeza?', \$buttons);
					}else{
						dialogConfirm('Erro', 'Por favor, selecione pedidos do mesmo cliente');
					}
				});
			});
			$('#ven002IncluirPedido').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Pedido', '/ven/ven002/content/ven002contentIncluirPedido/', '', [1000,615]);            
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

	case 'ven012GridFilaDeEnviar':
		global $g;
   		
   		if (!isset($r_T003_Flag_Expedicao)) { $r_T003_Flag_Expedicao = 1; }
		
		
		$sqlSeries = "SELECT DISTINCT T007_Serie 
					    FROM T007
		               WHERE T007_C004_Id = '{$g['empresaAtual']}' 
					     AND T007_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
			             AND T007_Flag_ACP = '6'";
        $querySeries = mysql_query($sqlSeries);

		$array_series = array();
		while ($rowSeries = mysql_fetch_array($querySeries)) {

			$serie = $rowSeries['T007_Serie'];
			// VERIFICA SE PULOU A NUMERAÇÃO DE NF-E
			$sqlNotas = "SELECT T007_Numero_Nota_Fiscal, 
								T007_Serie,
							    T007_Protocolo_Inutilizacao
						   FROM T007
						  WHERE T007_C004_Id = '{$g['empresaAtual']}'
						 	AND T007_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
						    AND T007_Flag_ACP = '6' 
							AND T007_Serie = '{$serie}'
					   ORDER BY CAST(T007_Numero_Nota_Fiscal AS UNSIGNED) DESC 
						  LIMIT 50";
			$query = mysql_query($sqlNotas);
			while($row = mysql_fetch_array($query)){
				if ($row['T007_Numero_Nota_Fiscal']<=0){
					continue;
				}
				$array_series["{$serie}"][] = $row['T007_Numero_Nota_Fiscal'];
			}
		}
	
		$countNotasFaltante = 0;

		foreach ($array_series as $serie => $array_notas) {
			$notaAnterior = 0;
			arsort($array_notas);
			foreach ($array_notas as $nota) {
				if ($notaAnterior>0){
					$diferenca = $notaAnterior - $nota;
					if ($diferenca == 2){
						$countNotasFaltante+=($diferenca-1);
						$mensagemErro .= " Falta NF-e " . ($notaAnterior-1) . " série " . $serie;
					} else if ($diferenca > 2){
						$countNotasFaltante+=(($notaAnterior-$nota)-1);
						$mensagemErro .= " Falta NF-e " . ($nota + 1) . " a " . ($notaAnterior - 1) . " série " . $serie;
					}
				}
				$notaAnterior = $nota;
			}
		}

		$sqlSeries = "SELECT DISTINCT T007_Serie 
						FROM T007
					   WHERE T007_C004_Id = '{$g['empresaAtual']}' 
						 AND T007_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
						 AND T007_Flag_ACP = '8'";
		$querySeries = mysql_query($sqlSeries);

		$array_series = array();
		while ($rowSeries = mysql_fetch_array($querySeries)) {

			$serie = $rowSeries['T007_Serie'];

			// VERIFICA SE PULOU A NUMERAÇÃO DE NFC-e
			$sqlNotas = "SELECT T007_Numero_Nota_Fiscal, 
								T007_Serie,
								T007_Protocolo_Inutilizacao 
						   FROM T007
						  WHERE T007_C004_Id = '{$g['empresaAtual']}' 
							AND T007_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
							AND T007_Flag_ACP = '8' 
							AND T007_Serie = '{$serie}'
					   ORDER BY CAST(T007_Numero_Nota_Fiscal AS UNSIGNED) DESC 
						  LIMIT 50";
			$query = mysql_query($sqlNotas);
		
			$array_series = array();
			while($row = mysql_fetch_array($query)){
				if ($row['T007_Numero_Nota_Fiscal']<=0){
					continue;
				}		
				
				$array_series["{$serie}"][] = $row['T007_Numero_Nota_Fiscal'];
			}
		}

		foreach ($array_series as $serie => $array_notas) {
			$notaAnterior = 0;
			arsort($array_notas);
			foreach ($array_notas as $nota) {
				if ($notaAnterior>0){
					$diferenca = $notaAnterior - $nota;
					if ($diferenca == 2){
						$countNotasFaltante+=($diferenca-1);
						$mensagemErro .= " Falta NFC-e " . ($notaAnterior-1) . " série " . $serie;
					} else if ($diferenca > 2){
						
						$countNotasFaltante+=(($notaAnterior-$nota)-1);
						$mensagemErro .= " Falta NFC-e " . ($nota + 1) . " a " . ($notaAnterior - 1) . " série " . $serie;
					}
				}
				$notaAnterior = $nota;
			}
		}

		if (!empty($mensagemErro)){
			echo "<div style='margin:auto;text-align:center;padding:2px;border:1px solid red;background:#fc5a5a;color:white;width:99%;border-radius: 3px 3px 3px 3px;font-size:14px'>Atenção! " . $mensagemErro . "</div>";
			$quantidadeLiberada = floatval(str_replace('.', '', $g['C031']['qtdeFaltaNFTravarCliente']));
            $countNotasFaltante = floatval(str_replace('.', '', $countNotasFaltante));
			if($countNotasFaltante>=$quantidadeLiberada){
				die();
			}
		}

		// FIM VERIFICAÇÃO SE PULOU NOTA -----------------------

		// nivelAlcada
		$mostrarAlerta = false;

		$cert = '';
		// EXIBE VENCIMENTO DO CERTIFICADO DIGITAL
   		$alertaVencimentoCertificadoDigital1 = $g['C031']['alertaVencimentoCertificadoDigital1'];
   		$alertaVencimentoCertificadoDigital2 = $g['C031']['alertaVencimentoCertificadoDigital2'];
		if($alertaVencimentoCertificadoDigital2 > 0){
			$mostrarAlerta = true;
		}
	   		$cSql_Empresa=@mysql_query("select * from C004 left join D018 on D018_Id=C004_D018_Id where C004_Id='{$g['empresaAtual']}'");
			$mEmpresa=@mysql_fetch_array($cSql_Empresa);
			if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S') {
	            if (!empty($mEmpresa['C004_Certificado_Digital'])) {	
		            $mkt_Hoje=time();
		            $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
	            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {	
	                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
		            if (file_exists($cArquivo_Certificado_Digital)) {
		                $mkt_Hoje=time();
		                $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
		            }
	            }	  
	               
	            if (empty($mktVencimentoCertificado)) {
	            	$cert = "<span style='color:var(--BGVermelho);font-size:14px;'>Não foi possivel ler o certificado digital</span>";
	                //echo "<span style='padding-left:600px;'><span style='color:#fc5a5a;font-size:14px;'>Não foi possivel ler o certificado digital</span></span>";
	            } else {
	                $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
	                $diasVencimentoCertificado = $mktVencimentoCertificado - time();
	                if ($mktVencimentoCertificado>=$mkt_Hoje) {
	                	//Calculo arredondado da quantidade de dias que o certificado venceu ou está para vencer, 
	    				$diasVencimentoCertificado = floor($diasVencimentoCertificado / 60 / 60 / 24);	    				
	    				$mostraValidadeCertificado = true;
				        $isCertificadoValido=true; 
	    				if($diasVencimentoCertificado < 1){ 	
				            $tituloMensagem = 'white';
				        	$backgroundCert = 'var(--BGVermelho)';
				        	$corMensagem = 'white';	
				            if($mostrarAlerta){
					            echo <<<EOT
					            <script type="text/javascript">
						            var diasVencimentoCertificado = $diasVencimentoCertificado;
						            dialogConfirm("<span style='color:white;font-size:14px;'VENCIMENTO CERTIFICADO DIGITAL </span>", 
						            	" <span style='font-size:14px;'>SEU CERTIFICADO DIGITAL VENCE <span style='color:var(--BGVermelho);font-size:14px;'><strong>HOJE!</strong></span><BR><BR></span><span style='font-size:14px;'>Data de vencimento: <span style='color:var(--BGVermelho);'>{$VencimentoCertificado}</span></span> ");			            	
					            </script>
EOT;
							}
						}else if($diasVencimentoCertificado == 1){ 	
				            $tituloMensagem = 'white';
				        	$backgroundCert = 'var(--BGVermelho)';
				        	$corMensagem = 'white';	
				            if($mostrarAlerta){
					            echo <<<EOT
					            <script type="text/javascript">
						            dialogConfirm("<span style='color:white;font-size:14px;'VENCIMENTO CERTIFICADO DIGITAL </span>", 
						            	" <span style='font-size:14px;'>SEU CERTIFICADO DIGITAL VENCE <span style='color:var(--BGVermelho);font-size:14px;'><strong>AMANHÃ!</strong></span><BR><BR></span><span style='font-size:14px;'>Data de vencimento: <span style='color:var(--BGVermelho);'>{$VencimentoCertificado}</span></span> ");			            	
					            </script>
EOT;
							}
				        }else if(($diasVencimentoCertificado > 1) && ($diasVencimentoCertificado <= $alertaVencimentoCertificadoDigital2)){
				            $tituloMensagem = 'white';
				        	$backgroundCert = 'var(--BGVermelho)';
				        	$corMensagem = 'white';				        	
				        	if($mostrarAlerta){
					        	echo <<<EOT
					            <script type="text/javascript">
						            var diasVencimentoCertificado = $diasVencimentoCertificado;
						            dialogConfirm("<span style='color:white;font-size:14px;'VENCIMENTO CERTIFICADO DIGITAL </span>", 
						            	" <span style='font-size:14px;'>Seu certificado digital vence em <span style='color:var(--BGVermelho);'>{$diasVencimentoCertificado} dias! </span><BR><BR>Data de vencimento: <span style='color:var(--BGVermelho);'>{$VencimentoCertificado}</span></span> ");			            	
					            </script>
EOT;
							}
				        }else if(($diasVencimentoCertificado > 1) && ($diasVencimentoCertificado <= $alertaVencimentoCertificadoDigital1)){
				            $tituloMensagem = '';
				        	$backgroundCert = 'var(--celula-amarela)';
				        	$corMensagem = 'var(--BGVermelho)';

				        } else {
					 		$tituloMensagem = '';
				        	$backgroundCert = '';
					        $corMensagem = 'var(--BGAzul)';
				        }
	
	                    $cert = <<<EOT
						    <font style='font-size:11px;color:$tituloMensagem'>Seu certificado digital vence em:<BR></font>
							<font style='color:$corMensagem;font-size:14px;'>{$VencimentoCertificado}</font>
EOT;
	                      /*echo "<span style='padding-left:600px;'>
	                                <span style='font-size:11px;'>Seu certificado digital vence em:<BR></span>
	                                <span style='padding-left:615px;color:var(--BGVermelho);font-size:14px;'>{$VencimentoCertificado}</span>
	                            </span>";*/
	                } else {
	                	$corMensagem = 'var(--BGVermelho)';
	                   	$diasVencimentoCertificado = $diasVencimentoCertificado * (-1);
	                   	//Calculo arredondado da quantidade de dias que o certificado venceu ou está para vencer! 
	    				$diasVencimentoCertificado = floor($diasVencimentoCertificado / 60 / 60 / 24);
	                   	$cert = <<<EOT
							<span style='color:var(--BGVermelho);font-size:11px;'>Certificado digital vencido em:<BR></span>
							<span style='color:var(--BGVermelho);font-size:14px;'>{$VencimentoCertificado}</span>
							<script type="text/javascript">
					            var diasVencimentoCertificado = $diasVencimentoCertificado;
					            dialogConfirm("<span style='color:white;font-size:14px;'NÃO É POSSÍVEL ENVIAR NFe </span>", 
					            	" <span style='font-size:14px;color:var(--BGVermelho)'>SEU CERTIFICADO DIGITAL VENCEU HÁ  {$diasVencimentoCertificado} dias! <BR><BR><span style='font-size:14px;color:var(--letra1)'>Data de vencimento: </span>{$VencimentoCertificado}</span> ");			            	
				            </script>
EOT;
                    /*echo "<span style='padding-left:600px;'>
                            <span style='color:#fc5a5a;font-size:11px;'>CERTIFICADO DIGITAL VENCIDO EM:<BR></span>
                            <span style='padding-left:615px;color:yellow;font-size:14px;'>{$VencimentoCertificado}</span>
                        </span>";*/
                	}
            	}
        	}
        

        $status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$mensagemCert = <<<EOT
			<div style='margin:0px 0 0 800px;width:175px;background-color:$backgroundCert;align:center;padding:7px'>
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>-->
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">SVC:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>-->
					{$cert}
				</div>
EOT;
		
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Enviar NFs Marcadas', 'id' => 'ven012EnviarMarcados',  'exibirCallback' => 'return !isset($r_T007_Id);'),
			array('titulo' => 'Habilitar/Desabilitar envio SVC NFe', 'id' => 'ven012HabilitaEnvioSVC',  'exibirCallback' => 'return ($g["usuarioAtual"] == 63)? true : false;'),
			array('titulo' => 'Habilitar/Desabilitar envio offline NFCe', 'id' => 'ven012HabilitaEnvioSVCNfce',  'exibirCallback' => 'return ($g["usuarioAtual"] == 63)? true : false;'),
			array('titulo' => 'Enviar Lote', 'id' => 'ven012EnviarLote',  'exibirCallback' => 'return !isset($r_T007_Id) && $g["C031"]["habilitarEnvioNFeLote"] == "S";'),			
		);
		echo Botoes::processar($botoes);

		/*if($g['usuarioAtual'] == '63'){
			$botaoSVC = "<div style='float:left'><button type='button' id='ven012HabilitaEnvioSVC' style='margin: 3px 0 0 10px'>Habilitar/Desabilitar envio SVC</button></div>";
		}*/
		if (!isset($r_T007_Id)) {
			echo <<<EOT
			<div style='border:0px solid red'>
				<!--<div style='float:left'><button type="button" id="ven012EnviarMarcados" style="margin: 3px 0 0 10px">Enviar NFs Marcadas</button></div>-->
				{$botaoSVC}
				<div style='float:right;margin:7px 100px 0 0;font-size:14px;;'>Suporte {$sys['telefone']}</div>
				{$mensagemCert}
			</div>
EOT;
		}
		echo <<<EOT
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven012HabilitaEnvioSVC').unbind('click').click(function() {
					var \$buttons = {
						"Habilitar/Desabilitar SVC": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven012/grid_func-ajax/ven012HabilitaEnvioSVC/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', "Este processo irá habilitar/desabilitar o botão 'Enviar por SVC - contingência' da NFe para a empresa atual. Deseja continuar?",  \$buttons);
				});

				$('#ven012HabilitaEnvioSVCNfce').unbind('click').click(function() {
					var \$buttons = {
						"Habilitar/Desabilitar envio OFFLINE": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven012/grid_func-ajax/ven012HabilitaEnvioSVCNfce/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', "Este processo irá habilitar/desabilitar o botão 'Enviar offline' da NFCe para a empresa atual. Deseja continuar?",  \$buttons);
				});
				
				$('#ven012EnviarMarcados').unbind('click').click(function() {
					$('#{$g['divId']} .formInput[type=checkbox]:checked').each(function() {
					    $(this).closest('tr').find('.btnEnviarNFE:visible').trigger('click');
					});
				});

				$('#ven012MarcarTodas').unbind('click').click(function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012MarcarTodas/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});

				$('#ven012DesmarcarTodas').unbind('click').click(function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012DesmarcarTodas/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});

				$('#ven012FecharTodas').unbind('click').click(function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012FecharTodas/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});

				$('#ven012ImprimirNFeMarcada').bind('click', function(data) {
                	nova_janela('/ven/ven012/grid_func-ajax/ven012ImprimirMarcadas/?ajax=true',0,0,750,500,'yes','no','no','no','yes','no','Impressao Danfe');
                })

				$('#ven012EnviarPorEmail').bind('click', function(data) {
					var \$buttons = {
						"Enviar todas": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true&Opcao=&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Este processo irá enviar e-mail de todas as notas que estão nesta tela e já foram transmitidas. Deseja continuar?', \$buttons2);
						},
						"Enviar notas que o e-mail não foi enviado": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true&Opcao=naoEnviadas&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Este processo irá enviar somente os e-mails ainda não foram enviados. Deseja continuar?', \$buttons2);
						},
						"Enviar marcadas": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true&Opcao=marcadas&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja enviar e-mail das notas marcadas?', \$buttons2);
						},
						"Fechar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Atualizar', 'Selecione uma opção abaixo: <br /><br />Atenção, este processo irá enviar a DANFE e o XML', \$buttons);
				});

				$('#ven012EnviarLote').unbind('click').click(function() {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven012/grid_func-ajax/ven012EnviarLote/?ajax=true&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									dialogConfirm('Ok', request.data);																	
								} else {							
									dialogConfirm('Erro', request.data);											
								}

								divRefresh('{$g['divId']}', true);
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Você tem certeza que deseja enviar o lote?', \$buttons);
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'campoInutilizar', 'colunaTipo' => 'livre', 'callback' => 'campoInutilizar', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Enviar', 'callback' => 'campoMarcarEnviar', 'style' => 'width: 1%;', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaExibir' => false, 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/notafiscal/', 'T007_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 330), 'style' => 'width:2%;'),
            array('colunaValor' => 'T007_Data_Saida', 'colunaTitulo' => 'Saída', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão<br/>Saída', 'callback' => 'filaEnvioData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;'),
            array('colunaValor' => 'T007_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Lote', 'colunaTitulo' => 'Nº Lote','style' => 'width: 1%;vertical-align: top;font-weight:normal','colunaAlinhar' => 'left','colunaExibir' => $g["C031"]["habilitarEnvioNFeLote"] == "S"),
            array('colunaValor' => 'PedNfe', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Pedido<br/>NF-e', 'callback' => 'filaEnvioPedNfe', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;'),									
            array('colunaValor' => 'IF(T007_Flag_ACP=6,\'NF-e\', \'NFC-e\')', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:normal'),			
            array('colunaValor' => 'T007_Observacao_Nota_Fiscal_1', 'colunaTitulo' => 'Obs. Fiscal', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Observacao_Nota_Fiscal_2', 'colunaTitulo' => 'Obs. Fiscal2', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Cliente_Razao', 'colunaTitulo' => 'Destinatário', 'callback' => 'filaEnvioCliente', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;vertical-align: top;'),
            array('colunaValor' => 'T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style'=>'vertical-align: top;width:1%;'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'vertical-align: top; width:1%;'),
            array('colunaValor' => 'concat(T007_Chave_Acesso_Nfe,\'<br /> \',\'Rec: \',T007_Numero_Recibo_Nfe,\'  \',\'Prot: \',\'\',T007_Numero_Protocolo_Nfe,\'\')', 'colunaTitulo' => 'Chave de Acesso / Recibo / Protocolo', 'colunaExibir'=>false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'callback' => 'quebra10', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Resposta_Codigo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Resposta_Extra', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Retorno_Sefaz', 'colunaExibir' => false),
            array('colunaValor' => 'T007_D024_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Flag_Finalizada', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaExibir' => false),
            array('colunaValor' => 'txSefaz', 'colunaTitulo' => 'Transmissão NF-e Sefaz', 'callback' => 'txSefaz', 'colunaTipo' => 'livre', 'style' => 'width: 50%; vertical-align: top;'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Numero_Protocolo_Nfe'),
            array('colunaValor' => 'T007_Flag_Almoxarifado', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_Almoxarifado'),
            array('colunaValor' => 'T007_Flag_Almoxarifado', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_Almoxarifado'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_ACP'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaExibir' => false, 'colunaTitulo' => 'Transportadora', 'colunaExibir' => false),        
			array('colunaValor' => 'T007A_Flag_Contingencia_Offline', 'colunaExibir' => false, 'colunaTitulo' => 'T007A_Flag_Contingencia_Offline', 'colunaExibir' => false),   	
        );

		function gridLinha($array) {
			global $g;
			// ...
			if ($array['todosCampos']['T007_TX_Resposta_Codigo'] < 100) {
				if ($array['todosCampos']['T007_TX_Resposta_Codigo'] == 9) {
					return 'linhaSuccess';
				} else {
					return '';
				}
			} else {
				return 'linhaErrorNoColor';
			}
		}
		/**
		*
		* Definição da SQL
		*/
		$from = 'T007';

		// and (T007_Numero_Recibo_Nfe is null or T007_Numero_Protocolo_Nfe is null)
		$r_T007_Id = !isset($r_T007_Id) ? '' : "AND T007_Id = '" . mysql_real_escape_string($r_T007_Id) . "'";
		if($g['C031']['modeloExpedicao'] == '2' ){
			$extraFlagExpedicao = '';
		}elseif ($r_T003_Flag_Expedicao == '2'){
			$extraFlagExpedicao = "AND T007_Flag_Expedicao = '{$r_T003_Flag_Expedicao}'";
		} else {
			$extraFlagExpedicao = "AND T007_Flag_Expedicao != '2'";
		}

		$extra = <<<EOT
		    LEFT JOIN D024 ON D024_Id=T007_D024_Id
		    LEFT JOIN D022 ON D022_Id=T007_D022_Id
			LEFT JOIN T007A ON T007_Id = T007A_T007_Id
			where (T007_Flag_ACP=6 OR T007_Flag_ACP=8)
			and T007_C004_Id='{$g['empresaAtual']}'
			$extraFlagExpedicao
			AND T007_TX_Flag_Finalizada != 1
			AND T007_Protocolo_Inutilizacao is null
			AND T007_Numero_Protocolo_Nfe is null
			order by T007_Numero_Nota_Fiscal
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
			'T007_T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T007_Flag_Travar_Nota' => array('titulo' => 'Finalizadas?(S/N)', 'tipo' => 'texto'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'T007_TX_Resposta_Codigo' => array('titulo' => 'Pendente de transmissão', 'tipo' => 'select', 
				'select' => array(
					array('title' => 'Todas', 'value' => '', 'selected' => 'S'),
					array('title' => 'Sim', 'value' => '15'),
					array('title' => 'Não', 'value' => '0'),
				)
			),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		$g['smarty']->assign('class', 'naoProcessarThead');
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
			});
			if (typeof(outtime) != 'undefined') {
				clearInterval(outtime);
			}
			outtime = setTimeout(function() {
				divRefresh('{$g['divId']}');
			}, 300000);
		} </script></div>
EOT;
    break;

	case 'ven012GridFilaDeEnviadas':
   		if (!isset($r_T003_Flag_Expedicao)) { $r_T003_Flag_Expedicao = 1; }
   		$cert = '';
		// EXIBE VENCIMENTO DO CERTIFICADO DIGITAL
   		$cSql_Empresa=@mysql_query("select * from C004 left join D018 on D018_Id=C004_D018_Id where C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=@mysql_fetch_array($cSql_Empresa);
		if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S') {
            if (!empty($mEmpresa['C004_Certificado_Digital'])) {	
	            $mkt_Hoje=time();
	            $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {	
                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
	            if (file_exists($cArquivo_Certificado_Digital)) {
	                $mkt_Hoje=time();
	                $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
	            }
            }	  
               
            if (empty($mktVencimentoCertificado)) {
            	$cert = "<span style='color:#fc5a5a;font-size:14px;'>Não foi possivel ler o certificado digital</span>";
                //echo "<span style='padding-left:600px;'><span style='color:#fc5a5a;font-size:14px;'>Não foi possivel ler o certificado digital</span></span>";
            } else {
                $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
                if ($mktVencimentoCertificado>$mkt_Hoje) {
                    $isCertificadoValido=true; 
                    $cert = <<<EOT
                    	<font style='font-size:11px;'>Seu certificado digital vence em:<BR></font>
						<font style='color:var(--BGAzul);font-size:14px;'>{$VencimentoCertificado}</font>
EOT;
                      /*echo "<span style='padding-left:600px;'>
                                <span style='font-size:11px;'>Seu certificado digital vence em:<BR></span>
                                <span style='padding-left:615px;color:var(--BGVermelho);font-size:14px;'>{$VencimentoCertificado}</span>
                            </span>";*/
                } else {
                   	$cert = <<<EOT
						<span style='color:#fc5a5a;font-size:11px;'>Certificado digital vencido em:<BR></span>
						<span style='color:#fc5a5a;font-size:14px;'>{$VencimentoCertificado}</span>
EOT;
                    /*echo "<span style='padding-left:600px;'>
                            <span style='color:#fc5a5a;font-size:11px;'>CERTIFICADO DIGITAL VENCIDO EM:<BR></span>
                            <span style='padding-left:615px;color:yellow;font-size:14px;'>{$VencimentoCertificado}</span>
                        </span>";*/
                }
            }
        }

        $status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$mensagemCert = <<<EOT
			<div style='margin:0px 0 0 800px;width:175px;background-color:$backgroundCert;align:center;padding:7px'>
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>-->
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">SVC:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>-->
					{$cert}
			</div>
EOT;	
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Fechar Todas', 'id' => 'ven012FecharTodas',  'exibirCallback' => 'return !isset($r_T007_Id);'),
			array('titulo' => 'Imprimir Marcadas', 'id' => 'ven012ImprimirNFeMarcada',  'exibirCallback' => 'return !isset($r_T007_Id);'),
			array('titulo' => 'Enviar E-mail', 'id' => 'ven012EnviarPorEmail',  'exibirCallback' => 'return !isset($r_T007_Id);'),
		);
		echo Botoes::processar($botoes);

		if (!isset($r_T007_Id)) {
			echo <<<EOT
				<div style='border:0px solid red'>
					<!--<button type="button" id="ven012EnviarMarcados" style="margin: 3px 0 0 10px">Enviar NFs Marcadas</button>-->
					<!--<button type="button" id="ven012MarcarTodas" style="margin: 3px 0 0 0px">Marcar Todas</button>-->
					<!--<button type="button" id="ven012DesmarcarTodas" style="margin: 3px 0 0 0px">Desmarcar Todas</button>-->
					<!--<div style='float:left'>
						<button type="button" id="ven012FecharTodas" style="margin: 3px 0 0 0px">Fechar Todas</button>
						<button type="button" id="ven012ImprimirNFeMarcada" style="margin: 3px 0 0 0px">Imprimir Marcadas</button>
						<button type="button" id="ven012EnviarPorEmail" style="margin: 3px 0 0 0px">Enviar E-mail</button>
					</div>-->
					<span style='float:right;margin:7px 100px 0 0;font-size:14px;;'>Suporte {$sys['telefone']}</span>
					{$mensagemCert}
				</div>
EOT;
		}
		echo <<<EOT
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven012EnviarMarcados').unbind('click').click(function() {
					$('#{$g['divId']} .formInput[type=checkbox]:checked').each(function() {
					    $(this).closest('tr').find('.btnEnviarNFE:visible').trigger('click');
					});
				});

				$('#ven012MarcarTodas').unbind('click').click(function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012MarcarTodas/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});

				$('#ven012DesmarcarTodas').unbind('click').click(function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012DesmarcarTodas/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});

				$('#ven012FecharTodas').unbind('click').click(function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012FecharTodas/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});

				$('#ven012ImprimirNFeMarcada').bind('click', function(data) {
                	nova_janela('/ven/ven012/grid_func-ajax/ven012ImprimirMarcadas/?ajax=true',0,0,750,500,'yes','no','no','no','yes','no','Impressao Danfe');
                })

				$('#ven012EnviarPorEmail').bind('click', function(data) {
					var \$buttons = {
						"Enviar todas": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true&Opcao=&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Este processo irá enviar e-mail de todas as notas que estão nesta tela e já foram transmitidas. Deseja continuar?', \$buttons2);
						},
						"Enviar notas que o e-mail não foi enviado": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true&Opcao=naoEnviadas&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Este processo irá enviar somente os e-mails ainda não foram enviados. Deseja continuar?', \$buttons2);
						},
						"Enviar marcadas": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true&Opcao=marcadas&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
											$('#{$g['divId']}').hideLoading();
											divRefresh('{$g['divId']}');
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja enviar e-mail das notas marcadas?', \$buttons2);
						},
						"Fechar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Atualizar', 'Selecione uma opção abaixo: <br /><br />Atenção, este processo irá enviar a DANFE e o XML', \$buttons);
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'campoInutilizar', 'colunaTipo' => 'livre', 'callback' => 'campoInutilizar', 'colunaExibir' => false),           
            array('colunaValor' => 'T007_Flag_Enviar', 'callback' => 'campoMarcarEnviar', 'style' => 'width: 1%;', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
             array('colunaValor' => 'impressaoEtiqueta', 'colunaExibir' => false, 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/notafiscal/', 'T007_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 330), 'style' => 'width:2%;'),
            array('colunaValor' => 'T007_Data_Saida', 'colunaTitulo' => 'Saída', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão<br/>Saída', 'callback' => 'filaEnvioData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;'),
            array('colunaValor' => 'T007_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Lote', 'colunaTitulo' => 'Nº Lote','style' => 'width: 1%;vertical-align: top;font-weight:normal','colunaAlinhar' => 'left','colunaExibir' => $g["C031"]["habilitarEnvioNFeLote"] == "S"),
            array('colunaValor' => 'PedNfe', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Pedido<br/>NF-e', 'callback' => 'filaEnvioPedNfe', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;'),
            array('colunaValor' => 'IF(T007_Flag_ACP=6,\'NF-e\', \'NFC-e\')', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;'),
            array('colunaValor' => 'T007_Observacao_Nota_Fiscal_1', 'colunaTitulo' => 'Obs. Fiscal', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Observacao_Nota_Fiscal_2', 'colunaTitulo' => 'Obs. Fiscal2', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Cliente_Razao', 'colunaTitulo' => 'Cliente<br/>Transportadora<br/>Observação Fiscal', 'callback' => 'filaEnvioCliente', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;vertical-align: top;'),
            array('colunaValor' => 'T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style'=>'vertical-align: top;width:1%;'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'vertical-align: top; width:1%;'),
            array('colunaValor' => 'concat(T007_Chave_Acesso_Nfe,\'<br /> \',\'Rec: \',T007_Numero_Recibo_Nfe,\'  \',\'Prot: \',\'\',T007_Numero_Protocolo_Nfe,\'\')', 'colunaTitulo' => 'Chave de Acesso / Recibo / Protocolo', 'colunaExibir'=>false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'callback' => 'quebra10', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Resposta_Codigo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Resposta_Extra', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Retorno_Sefaz', 'colunaExibir' => false),
            array('colunaValor' => 'T007_D024_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Flag_Finalizada', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaExibir' => false),
            array('colunaValor' => 'txSefaz', 'colunaTitulo' => 'Transmissão NF-e Sefaz', 'callback' => 'txSefaz', 'colunaTipo' => 'livre', 'style' => 'width: 50%; vertical-align: top;'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Numero_Protocolo_Nfe'),
            array('colunaValor' => 'T007_Flag_Almoxarifado', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_Almoxarifado'),
            array('colunaValor' => 'T007_Flag_Almoxarifado', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_Almoxarifado'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_ACP'),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaExibir' => false, 'colunaTitulo' => 'Transportadora', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Data_Envio_Email_Boleto', 'colunaTitulo' => 'T007_Data_Envio_Email_Boleto', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Flag_Travar_Nota', 'colunaTitulo' => 'T007_Flag_Travar_Nota', 'colunaExibir' => false),
        );

		function gridLinha($array) {
			global $g;
			// ...
			if ($array['todosCampos']['T007_TX_Resposta_Codigo'] < 100) {
				if ($array['todosCampos']['T007_TX_Resposta_Codigo'] == 9) {
					return 'linhaSuccess';
				} else {
					return '';
				}
			} else {
				return 'linhaErrorNoColor';
			}
		}
		/**
		*
		* Definição da SQL
		*/
		$from = 'T007';

		// and (T007_Numero_Recibo_Nfe is null or T007_Numero_Protocolo_Nfe is null)
		$r_T007_Id = !isset($r_T007_Id) ? '' : "AND T007_Id = '" . mysql_real_escape_string($r_T007_Id) . "'";

		if($g['C031']['modeloExpedicao'] == '2' ){
			$extraFlagExpedicao = '';
		}elseif ($r_T003_Flag_Expedicao == '2'){
			$extraFlagExpedicao = "AND T007_Flag_Expedicao = '{$r_T003_Flag_Expedicao}'";
		} else {
			$extraFlagExpedicao = "AND IFNULL(T007_Flag_Expedicao, 1) != 2";
		}

		$extra = <<<EOT
		    LEFT JOIN D024 ON D024_Id=T007_D024_Id
		    LEFT JOIN D022 ON D022_Id=T007_D022_Id
			where (T007_Flag_ACP=6 OR T007_Flag_ACP=8)
			and T007_C004_Id='{$g['empresaAtual']}'
			AND T007_TX_Flag_Finalizada != 1
			$extraFlagExpedicao
			AND IFNULL(T007_Protocolo_Inutilizacao,'') = ''
			AND IFNULL(T007_Numero_Protocolo_Nfe,'') != ''
			order by T007_Numero_Nota_Fiscal
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
			'T007_T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T007_Flag_Travar_Nota' => array('titulo' => 'Finalizadas?(S/N)', 'tipo' => 'texto'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'T007_Numero_Lote' => array('titulo' => 'Nº Lote', 'tipo' => 'texto', 'exibir' => $g["C031"]["habilitarEnvioNFeLote"] == "S"),
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
		$g['smarty']->assign('class', 'naoProcessarThead');
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
			});
			if (typeof(outtime) != 'undefined') {
				clearInterval(outtime);
			}
			outtime = setTimeout(function() {
				divRefresh('{$g['divId']}');
			}, 300000);
		} </script></div>
EOT;
    break;

	case 'ven012GridLogNotas':
		$T007_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T127_Id', $g['divId']), 'colunaExibirNivel' => 4, 'colunaTitulo' => 'E', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T127_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'exibir' => false),
			array('colunaValor' => 'T127_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'exibir' => false),
			array('colunaValor' => 'T127_Data_Transmicao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'C004_Nome_Empresa', 'colunaTitulo' => 'Empresa Emitente'),
			array('colunaValor' => 'T127_Retorno_Sefaz', 'colunaTitulo' => 'Log'),
			array('colunaValor' => 'T127_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave acesso'),
			array('colunaValor' => 'T127_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo'),
		);

        /**
		 * Definição da SQL
		 */		
        $from = "T127";
		
		$extra = <<<EOT
		LEFT JOIN D024 ON T127_D024_Id=D024_Id
		LEFT JOIN C004 ON T127_C004_Id=C004_Id
        WHERE T127_C004_Id='{$g['empresaAtual']}' 
        ORDER BY T127_Id,T127_Data_Transmicao DESC				    
EOT;
		/**
        * Filtro
        */
        $gridFiltro = array(
            'T127_Numero_Nota_Fiscal' => array('titulo' => 'NF-e', 'tipo' => 'numero'),
            'T127_Data_Transmicao' => array('titulo' => 'Transmissão', 'tipo' => 'data'),
			'T127_Chave_Acesso_Nfe' => array('titulo' => 'Chave de Acesso', 'tipo' => 'texto'),
			'T127_Retorno_Sefaz' => array('titulo' => 'Log', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'C004_Nome_Empresa' => array('titulo' => 'Empresa Emitente', 'tipo' => 'texto'),
            //'T007_Flag_Travar_Nota' => array('titulo' => 'Finalizadas?(S/N)', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        log("EXTRA: ".$extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();

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
	
	case 'ven012GridErrosSefaz':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T136_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
			array('colunaValor' => 'T136_Numero_Erro', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'right', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T136_Descricao_Erro', 'colunaTitulo' => 'Erro'),
			array('colunaValor' => 'T136_Procedimento', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T136_Flag_Refazer_Chave_Acesso', 'colunaTitulo' => 'Refazer Chave', 'colunaAlinhar' => 'center'),
		);

        /**
		 * Definição da SQL
		 */		
        $from = "global.T136";
		
		$extra = <<<EOT

        ORDER BY T136_Numero_Erro 				    
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
            'T127_Numero_Nota_Fiscal' => array('titulo' => 'NF-e', 'tipo' => 'numero'),
            'T127_Data_Transmicao' => array('titulo' => 'Transmissão', 'tipo' => 'data'),
            'T127_Chave_Acesso_Nfe' => array('titulo' => 'Chave de Acesso', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'C004_Nome_Empresa' => array('titulo' => 'Empresa Emitente', 'tipo' => 'texto'),
            //'T007_Flag_Travar_Nota' => array('titulo' => 'Finalizadas?(S/N)', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        log("EXTRA: ".$extra);
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
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();

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
