<?php
namespace hardness;
// Carrega funções callback
require_once('prog/fin/fin005/grid_func-callback.php');

// Carrega funções do JS
require_once('prog/fin/fin005/grid_func-js.php');

/**
* Ações
*/
switch($g['r'][3]){
	// GRID
	case 'fin005GridPrincipal':

		$importacaoDados = true;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T117_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'T117_T035_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false), //se necessário usar o T002_T035_Id
			//array('colunaValor' => 'T117_Flag_Enviar_Email', 'callback' => 'campoMarcarBoletoEnvio', 'colunaTitulo' => 'Email', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => 'T117_Flag_Atualizar', 'callback' => 'campoMarcarBoletoAtualizar', 'colunaTitulo' => '', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
			array('colunaValor' => 'T117_Flag_Layout', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'T117_D024_Id', 'colunaTitulo' => 'IdCliente','colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
			array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento Boleto', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento Título', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaExibir' => false),
			array('colunaValor' => 'campoCancelar', 'colunaTipo' => 'livre', 'callback' => 'campoCancelar', 'colunaTitulo' => 'Cancelar', 'colunaAlinhar' => 'center',),
			array('colunaValor' => 'campoAtualizarVencimento', 'colunaTipo' => 'livre', 'callback' => 'campoAtualizarVencimento', 'colunaTitulo' => 'Atualizar Vencimento', 'colunaAlinhar' => 'center',),
			array('colunaValor' => 'T117_Data_Envio_Email', 'colunaTitulo' => 'eMail', 'callback' => 'campoeMail', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Email_Cliente(D024_Id,"boleto")', 'colunaTitulo' => 'E-mail'),
			array('colunaValor' => 'T117_T002_Id', 'colunaExibir' => false),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T117';
		$extra = <<<EOT
			left join D024 on D024_Id=T117_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D007 on D007_Id=T117_D007_Id
			left join D062 on D062_Id=D007_D062_Id
			left join T002 on T002_T117_Id=T117_Id
			left join T117A on T117A_T117_Id=T117_Id
	 		where 
	 		(T117_C004_Id='{$g['empresaAtual']}')
	 		GROUP BY T117_Id
EOT;


		function gridLinha($array) {
			global $g;
			$dataRecebimento = $array['todosCampos']['T002_Data_Recebimento'];
			$cancelado = $array['todosCampos']['T117_Flag_Cancelado'];
			if($cancelado == 'S'){
				return 'linhaVermelho';
			} else if($dataRecebimento != '0000-00-00'){
				return 'linhaCinza';
			} 
		}
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T117_Nosso_Numero' => array('titulo' => 'Nosso Número', 'tipo' => 'numero'),
            'T002_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
            'T117_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T117_Data_Vencimento' => array('titulo' => 'Vencimento Boleto', 'tipo' => 'data'),
            'T002_Data_Vencimento' => array('titulo' => 'Vencimento Título', 'tipo' => 'data'),
            'T002_Data_Recebimento' => array('titulo' => 'Recebido', 'tipo' => 'data'),
            'D007_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
            'T117_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
            'D024_E_Mail' => array('titulo' => 'E-mail', 'tipo' => 'texto'),
            'T117_Flag_Cancelado' => array('titulo' => 'Cancelado? (S/N)', 'tipo' => 'texto'),
            'T117_Flag_Atualizar' => array('titulo' => 'Marcado? (S/N)', 'tipo' => 'texto'),
			'D024_Cnpj' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'T002_T035_Id' => array('titulo' => 'Remessa', 'tipo' => 'numero'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));

		/*echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="fin005GridPrincipalBtnIncluir">Incluir</button>
			<button type="button" id="fin005EnviarBoletosMarcados">Enviar Boletos Marcados</button>
			<button type="button" id="fin005AtualizarBoletosMarcados">Atualizar Boletos Marcados</button>
			<button type="button" id="fin005ImprimirBoletosMarcados">Imprimir Boletos Marcados</button>
		</div>
EOT;*/
		// Botões
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fin005GridPrincipalBtnIncluir', 'janelaParametros' => "&extra={$extra_}", 'janelaUrl' => '/fin/fin005/content/fin005ContentAbreCadastroBoleto/', 'janelaTamanho' => '[900,550]'),
			array('titulo' => 'Enviar Boletos Marcados', 'id' => 'fin005EnviarBoletosMarcados'),
			array('titulo' => 'Atualizar Boletos Marcados', 'id' => 'fin005AtualizarBoletosMarcados', 'janelaParametros' => "&extra={$extra_}", 'janelaUrl' => '/fin/fin005/content/fin005ContentAtualizarVencimentoMarcados/', 'janelaTamanho' => '[380,220]'),
			array('titulo' => 'Imprimir Boletos Marcados', 'id' => 'fin005ImprimirBoletosMarcados'),
		);
		echo Botoes::processar($botoes);
		
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#xfin005GridPrincipalBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de boleto', '/fin/fin005/content/fin005ContentAbreCadastroBoleto/', '&tab=geral&extra={$extra_}', [900,550]);
				});

				$('#fin005EnviarBoletosMarcados').unbind('click').bind('click', function(data) {
					$.getJSON('/fin/fin005/grid_func-ajax/validarValorMaximoBoletoSemRegistro/?ajax=true&enviarEmail=S&extra={$extra_}&callback=?', function(request) {
						if(request.code){
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Boletos por Email', '/fin/fin005/content/fin005ContentBoletosParaEnvio/', '&tab=geral&extra={$extra_}', [900,550]);
						} else {
							dialogConfirm('Operação cancelada', request.data);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});	

				$('#xfin005AtualizarBoletosMarcados').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar Boletos', '/fin/fin005/content/fin005ContentAtualizarVencimentoMarcados/', '&tab=geral&extra={$extra_}', [300,250]);
				});

				$('#fin005ImprimirBoletosMarcados').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin005/grid_func-ajax/validarValorMaximoBoletoSemRegistro/?ajax=true&callback=?', function(request) {
						if(request.code){
							$.getJSON('/fin/fin005/grid_func-ajax/gerarPDFboleto/?ajax=true&gerarVarios=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if(request.code){
									$('#{$g['divId']}').hideLoading();
									nova_janela('{$confUsuario['urlRaiz']}' + request.data,0,0,800,500,'no','no','no','no','yes','yes',0);
									setTimeout(function(){
										$.getJSON('/fin/fin005/grid_func-ajax/fin005DeletarPDFBoleto/?ajax=true&gerarVarios=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {});
									}, 5000);
								} else {
									dialogConfirm('Operação cancelada', request.data);
								}
							});
						} else {
							dialogConfirm('Operação cancelada', request.data);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005MarcarTodosBoletos/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Ok', request.data, \$buttons);
											divRefresh('{$g['divIdParent']}', true);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005DesmarcarTodosBoletos/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {												
											$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										}											
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(1)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005MarcarTodosBoletosAtualizar/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Ok', request.data, \$buttons);
											divRefresh('{$g['divIdParent']}', true);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    		dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/fin/fin005/grid_func-ajax/fin005DesmarcarTodosBoletosAtualizar/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request) {
										if (request.code) {												
											$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										}											
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
				}
			} </script></div>
EOT;
        }

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Total', 'valor' => 'T117_Valor_Total', 'titulo' => 'Valor Total Geral'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
	
		/*
		$valorRegistros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T117_Id', '', $extra));
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T117_Valor_Total)", $from, str_replace('GROUP BY T117_Id', '', $extra)));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Boletos<br><input type="text" value="{$valorRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total Geral<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;	*/
		

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
				var chavePrimaria = 'T117_Id';	
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var acaoId = id[0].replace(chavePrimaria + '-', '');
				// Ação: abrir nova janela do browser 
				// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
				$.getJSON('/fin/fin005/grid_func-ajax/validarValorMaximoBoletoSemRegistroComId/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
					if(request.code){
						$('#{$g['divId']}').showLoading();
						$.getJSON('/fin/fin005/grid_func-ajax/gerarPDFboleto/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
							if(request.code){
								$('#{$g['divId']}').hideLoading();
								var w = window.open('{$confUsuario['urlRaiz']}' + request.data,'_blank');
								w.addEventListener("load", function(event) {
									setTimeout(function(){
										$.getJSON('/fin/fin005/grid_func-ajax/fin005DeletarPDFBoleto/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {});
									}, 5000);
								});
							} else {
								dialogConfirm('Operação cancelada', request.data);
							}
						});
					} else {
						dialogConfirm('Operação cancelada', request.data);
					}
				});				
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

	case 'fin005GridTitulos':
		if(!empty($r_T002_Id)){
			$sqlT002 = mysql_query("SELECT * FROM T002 WHERE T002_Id = '{$r_T002_Id}'");
			$T002 = mysql_fetch_array($sqlT002);
			$r_acaoId = $T002['T002_D024_Id'];
			mysql_query("UPDATE T002 SET T002_Flag_Gerar_Boleto = '1' WHERE T002_Id = '{$r_acaoId}'");
		}

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
		array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Data Recebimento', 'colunaExibir' =>  false),
		array('colunaValor' => 'T007_Id', 'colunaExibir' => false),
		array('colunaValor' => 'T007_Flag_ACP', 'colunaExibir' => false),
		//array('colunaValor' => 'D062_Numero_Banco', 'colunaExibir' => false),
		array('colunaValor' => "campoGerar", 'colunaTipo' => "livre", 'colunaTitulo' => 'Gerar', 'callback' => 'campoGerar'),
		array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Título'),
		array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
		array('colunaValor' => 'datediff(T002_Data_Vencimento,current_date()) as Dias', 'colunaTitulo' => 'Dias'),
		array('colunaValor' => 'T002_Valor_Total', 'colunaTitulo' => 'Total','colunaCasasDecimais' => 2),
		array('colunaValor' => 'T002_Flag_Gerar_Boleto', 'colunaTitulo' => 'T002_Flag_Gerar_Boleto', 'colunaExibir' => false),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T002';
		$extra = <<<EOT
            left join D027 on D027_Id=T002_D027_Id
            left join T007 on T007_Id=T002_T007_Id
			left join T117 on T117_Id=T002_T117_Id
            where (
	            IF('{$r_T002_Id}' > 0, T002_Id = '{$r_T002_Id}', T002_D024_Id = '{$r_acaoId}')
	            and T002_Flag_Cancelada!='S'
	            and T002_C004_Id={$g['empresaAtual']}
	            and T117_Id is null
	            and T002_Data_Recebimento='0000-00-00'
	        )
            order by T002_Data_Vencimento
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		
		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		var dbclickPrevent = false;
		$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
		// Previne duplo click (abrir duas janelas)
		if (dbclickPrevent) { return false; }
		dbclickPrevent = true;
		setTimeout(function() { dbclickPrevent = false; }, 500);
		// (substituir pelo mesmo ID da chave primária do grid)
		var chavePrimaria = 'T117_Id';	
		if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
		var id = $(this).closest('tr').attr('id').split('|');
		var acaoId = id[0].replace(chavePrimaria + '-', '');
		// Ação: abrir nova janela do browser 
		// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
		nova_janela('/hardness3/hardness/boleto/gerar_boleto.php?T117_Id='+encodeURIComponent(acaoId),0,0,800,500,'no','no','no','no','yes','yes',0);
		});
		$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
		EOT;
		*/
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

	case 'fin005GridHistorico':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
		array('colunaValor' => 'T117_Flag_Cancelado', 'colunaExibir' => false),
		array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
		array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
		array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total'),
		);

		/*

		$cClausula .= " T117_Id, ";
		$cClausula .= " T117_Data_Emissao, ";
		$cClausula .= " T117_Data_Vencimento, ";
		$cClausula .= " T117_Valor_Total, ";
		$cClausula .= " T117_Nosso_Numero,";
		$cClausula .= " T117_Flag_Cancelado,";
		$cClausula .= " D007_Nome_Banco";
		$cClausula .= " from T117 ";
		$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
		$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
		$cClausula .= " where T117_D024_Id='$D024_Id' ";
		$cClausula .= "   and T117_C004_Id='$Empresa_Atual'";
		$cClausula .= " order by T117_Id desc";
		$cClausula .= " limit 10";
		*/

		/**
		*
		* Definição da SQL
		*/
		$from = 'T117';
		$extra = <<<EOT
			left join D024 on D024_Id=T117_D024_Id
			left join D007 on D007_Id=T117_D007_Id
			where (
				T117_D024_Id = '{$r_acaoId}'
				AND T117_C004_Id={$g['empresaAtual']}
			)
			order by T117_Id desc
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		var dbclickPrevent = false;
		$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
		// Previne duplo click (abrir duas janelas)
		if (dbclickPrevent) { return false; }
		dbclickPrevent = true;
		setTimeout(function() { dbclickPrevent = false; }, 500);
		// (substituir pelo mesmo ID da chave primária do grid)
		var chavePrimaria = 'T117_Id';	
		if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
		var id = $(this).closest('tr').attr('id').split('|');
		var acaoId = id[0].replace(chavePrimaria + '-', '');
		// Ação: abrir nova janela do browser 
		// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
		nova_janela('/hardness3/hardness/boleto/gerar_boleto.php?T117_Id='+encodeURIComponent(acaoId),0,0,800,500,'no','no','no','no','yes','yes',0);
		});
		$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
		EOT;
		*/
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

	case 'fin005GridBoletosParaEnvio':
		$r_extra = base64_decode($r_extra);
		$r_extra = gInsertExtraWhere($r_extra, "T117_Flag_Atualizar = 'S' AND T117_Flag_Cancelado != 'S' AND T002_Data_Recebimento = '0000-00-00'");
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Flag_Atualizar', 'callback' => 'campoMarcarBoletoEnvio', 'colunaTitulo' => 'Email', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T117_Flag_Layout', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'T117_D024_Id', 'colunaTitulo' => 'IdCliente','colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Data_Envio_Email', 'colunaTitulo' => 'Data Envio Email', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Email_Cliente(D024_Id,"boleto")', 'colunaTitulo' => 'E-mail','colunaTipo' => 'banco'),
			array('colunaValor' => 'T117_T002_Id', 'colunaExibir' => false),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T117';
		$extra = $r_extra;

		function gridLinha($array) {
			global $g;
			$dataEnvioEmail = $array['todosCampos']['T117_Data_Envio_Email'];
			if($dataEnvioEmail != '0000-00-00 00:00:00'){
				return 'linhaCinza';
			} 
		}

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$valorRegistros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T117_Id', '', $extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Boletos<br><input type="text" value="{$valorRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
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
}
