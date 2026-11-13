<?php
namespace hardness;
/**
* Este código PHP será executado ao acessar: /fin-fin005-grid-fin005GridPrincipal/
* Ele irá substituir o original (que fica dentro do .PHP, no switch/case)
*
* Caso seja uma AJAX, é necessário acrescentar este código:
* No inicio: $resposta = array('code' => true, 'data' => array());
* No final: echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
*/
// Carrega funções callback
require_once('prog/fin/fin005/grid_func-callback.php');

// Carrega funções do JS
include('prog/fin/fin005/grid_func-js.php');


/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T117_T035_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Flag_Enviar_Email', 'callback' => 'campoMarcarBoletoEnvio', 'colunaTitulo' => 'Email', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T117_Flag_Atualizar', 'callback' => 'campoMarcarBoletoAtualizar', 'colunaTitulo' => 'Atualiz.', 'colunaEditarTodos' => 'S', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T117_Flag_Layout', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'T117_D024_Id', 'colunaTitulo' => 'IdCliente','colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaExibir' => false),
			array('colunaValor' => 'campoCancelar', 'colunaTipo' => 'livre', 'callback' => 'campoCancelar'),
			array('colunaValor' => 'campoAtualizarVencimento', 'colunaTipo' => 'livre', 'callback' => 'campoAtualizarVencimento'),
			array('colunaValor' => 'T117_Data_Envio_Email', 'colunaTitulo' => 'eMail', 'callback' => 'campoeMail', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_E_Mail', 'colunaTitulo' => 'E-mail'),
			array('colunaValor' => 'T117_T002_Id', 'colunaExibir' => false),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T117';
		$extra = <<<EOT
			left join D024 on D024_Id=T117_D024_Id
	 		left join T002 on T002_T117_Id=T117_Id
	 		left join D007 on D007_Id=T117_D007_Id
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
            'T117_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
            'T002_Data_Recebimento' => array('titulo' => 'Recebido', 'tipo' => 'data'),
            'D007_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
            'T117_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
            'D024_E_Mail' => array('titulo' => 'E-mail', 'tipo' => 'texto'),
            'T117_Flag_Cancelado' => array('titulo' => 'Cancelado? (S/N)', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin005GridPrincipalBtnIncluir">Incluir</button>
				<button type="button" id="fin005EnviarBoletosMarcados">Enviar Boletos Marcados</button>
				<button type="button" id="fin005AtualizarBoletosMarcados">Atualizar Boletos Marcados</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin005GridPrincipalBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de boleto', '/fin/fin005/content/fin005ContentAbreCadastroBoleto/', '&tab=geral&extra={$extra_}', [900,550]);
				});

				$('#fin005EnviarBoletosMarcados').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Boletos por Email', '/fin/fin005/content/fin005ContentBoletosParaEnvio/', '&tab=geral&extra={$extra_}', [900,550]);
				});

				$('#fin005AtualizarBoletosMarcados').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar Boletos', '/fin/fin005/content/fin005ContentAtualizarVencimentoMarcados/', '&tab=geral&extra={$extra_}', [300,250]);
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
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorRegistros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T117_Id', '', $extra));
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T117_Valor_Total)", $from, str_replace('GROUP BY T117_Id', '', $extra)));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Boletos<br><input type="text" value="{$valorRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total Geral<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		if (strpos($_SERVER['REMOTE_ADDR'], '192.168') !== false) {
                $urlRaiz = 'http://192.168.254.20/';
        }else{
                $urlRaiz = $confUsuario['urlRaiz'];
        }

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
				nova_janela('{$urlRaiz}/hardness/boleto/gerar_boleto.php?T117_Id='+encodeURIComponent(acaoId),0,0,800,500,'no','no','no','no','yes','yes',0);
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
		if ($g['debug']) { $g['smarty']->assign('gridSql', $geradoSql); }
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }


