<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * srv - srv002 - Pedidos
 */
// Classes
require_once('bibliotecas/classes/SRV002.php');
$srv002 = new SRV002();
// Carrega funções callback
require_once('prog/srv/srv002/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/srv/srv002/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
		* Grid Serviços Pendentes
		* Chamado em: srv002tab01
		*/
	case 'srv002grid01':
		global $g;
		/**
		* Definição do Grid (Tabela)
		*/
		// $sql = <<<EOT
		// select * from T147 left join D110 on D110_Id=T147_D110_Id WHERE T147_Prioridade='P'
// EOT;
		// $result = mysql_query($sql);
// 		
		// while($linha = mysql_fetch_assoc($result)){
			// echo $linha['T147_D110_Id']."<br />";
		// }
		// die();
		
		$grid = array(
			array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'Nº Pedido', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaEditar'=>true, 'callback'=>'validaCampoPrioridade99999', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T147_Prioridade'),
			array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEmp', 'colunaExibir' => false),
			array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
			array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'callback'=>'gCorrigeTempo', 'style' => 'font-weight:bold', 'colunaEditar'=>true),
			array('colunaValor' => 'T147_D110_Id', 'colunaTitulo' => 'IdCompetencia', 'colunaExibir' => false),
			array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
			//array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência'),
			array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Funcionário', 'style' => 'font-weight:bold', 'colunaExibir'=>false),
			array('colunaValor' => 'T147_D109_Id', 'colunaTitulo'=>'Funcionário', 'colunaEditarGeraSelect' => array('D109', 'D109_Id', 'D109_Nome','' ), 'colunaEditar' => true, 'callback' => 'mostrarNome', 'style' => 'width: 1%;'),
			//gGeraSelect('D109','D109_Id','D109_Nome','left join D115 on D115_D109_Id=D109_Id WHERE D115_D110_Id='.$D110_Id.' AND D109_Flag_Ativo=\'S\'', true),
			array('colunaValor' => 'T147_Flag_Novo', 'colunaTitulo' => 'IdFuncionario', 'colunaExibir' => false),
			array('colunaValor' => 'T147_Flag_Conferido', 'colunaTitulo' => 'Conferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T171_Hora_Combinada', 'colunaTitulo' => 'Hora Combinada', 'colunaTipo'=>'livre', 'callback'=>'validaCampoHoraCombinada'),
			//array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Responsável'),
			//array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Início', 'callback'=>'gCorrigeData'),
			//array('colunaValor' => 'T147_Hora_Fim', 'colunaTitulo' => 'Fim', 'callback'=>'gCorrigeData'),
		);

		/**
			*
			* Definição da SQL
			*/
		$from = 'T147';
		$extra = <<<EOT
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D110 on D110_Id=T147_D110_Id
				left join D109 on D109_Id=T147_D109_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				left join D116 on D116_Id=T147_D116_Id
				where (T147_Status='P')
				ORDER BY T147_Prioridade
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Serviço', '/srv/srv002/content/srv002ContentIncluir/', '&Status=P', [500,300]);">Incluir</button>
				<button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/srv/srv002/content/srv002content02/', '&Status=P', [950,600]);">Resumo</button>
				<button onclick="divRefresh('{$g['divId']}', true);">Atualizar</button>
			</div>
EOT;

		//Função que define a classe CSS para cada linha do GRID
		function gridLinha($array) {
			if ($array['todosCampos']['T147_Flag_Novo'] == "S") {
				return 'linhaFundoAmareloClaro';
			}
			if ($array['todosCampos']['T147_Flag_Conferido'] == "R") {
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
			'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
			// 'T147_Hora_Inicio' => array('titulo' => 'Hora Início', 'tipo' => 'tempo'),
			// 'T147_Hora_Fim' => array('titulo' => 'Hora Fim', 'tipo' => 'tempo'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				<span class="item" style="text-align:right">Total Pendentes<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
	   // setTimeout(function() {
	   		// if ($('#{$g['divId']}').is(":visible")) {
	   			// divRefresh('{$g['divId']}');
		 	// }
	   // }, 10000);
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			   
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Gerenciamento Pendentes - ID: ' + acaoId, '/srv/srv002/content/srv002content08/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
	           });
	           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
	           
	           // Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T147_Prioridade(T147_Id, campoEditado, valorAntigo, novoValor) {
					T147_Id = T147_Id.replace(/T147_Id-/, '');
					if (campoEditado == 'T147_Prioridade') {
						$.getJSON('/srv/srv002/grid_func-ajax/atualizarPrioridade/?ajax=true&T147_Id='      	+ encodeURIComponent(T147_Id) + 
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

	/**
	* Grid Serviços Em Execução
	*/
	case 'srv002grid02':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaAlinhar'=>'center'),
		array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'Nº Pedido', 'colunaAlinhar'=>'center'),
		array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaEditar'=>true, 'callback'=>'validaCampoPrioridade99999', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T147_Prioridade'),
		array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEmp', 'colunaExibir' => false),
		array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
		array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
		array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
		array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
		array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Funcionário', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'T147_D110_Id', 'colunaTitulo' => 'IdCompetencia', 'colunaExibir' => false),
		array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
		//array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência'),
		array('colunaValor' => 'T147_D109_Id', 'colunaTitulo' => 'IdFuncionario', 'colunaExibir' => false),		
		array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Início', 'callback'=>'gCorrigeData'),
		array('colunaValor' => 'PrevisaoFim', 'colunaTitulo' => 'Previsão conclusão', 'callback'=>'validaCampoPrevisao', 'colunaTipo'=>'livre'),
		array('colunaValor' => 'T147_Pausado', 'colunaTitulo' => 'Pausado', 'callback'=>'validaPausado'),
		// array('colunaValor' => 'T147_Hora_Pausa', 'colunaTitulo' => 'Hora Pausa', 'callback'=>'gCorrigeData'),
		// array('colunaValor' => 'T147_Hora_Reinicio', 'colunaTitulo' => 'Hora Reinício', 'callback'=>'gCorrigeData'),
		//array('colunaValor' => 'T147_Hora_Fim', 'colunaTitulo' => 'Fim', 'callback'=>'gCorrigeData'),
		);

		/**
			*
			* Definição da SQL
			*/
		$from = 'T147';
		$extra = <<<EOT
			left join T005 on T005_Id=T147_T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join D110 on D110_Id=T147_D110_Id
			left join D109 on D109_Id=T147_D109_Id
			left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			left join D116 on D116_Id=T147_D116_Id
			where
			(T147_Status='E')
			ORDER BY T147_Prioridade
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/srv/srv002/content/srv002content02/', '&Status=E', [950,600]);">Resumo</button>
				<button onclick="divRefresh('{$g['divId']}', true);">Atualizar</button>
			</div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
			'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Gerenciamento Execução - ID: ' + acaoId, '/srv/srv002/content/srv002content08/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
	           });
	           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
	           
	           // Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T147_Prioridade(T147_Id, campoEditado, valorAntigo, novoValor) {
					T147_Id = T147_Id.replace(/T147_Id-/, '');
					if (campoEditado == 'T147_Prioridade') {
						$.getJSON('/srv/srv002/grid_func-ajax/atualizarPrioridade/?ajax=true&T147_Id='      	+ encodeURIComponent(T147_Id) + 
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
					} 
				}
		} </script></div>
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total em Execução<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
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

	/**
	* Grid Serviços Concluídos
	*/
	case 'srv002grid03':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T147_Flag_Conferido', 'colunaTitulo' => 'Conferência', 'callback'=>'campoMarcarConferido', 'style' => 'width:50px;text-align:center'),
		array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaAlinhar'=>'center'),
		array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'Nº Pedido', 'colunaAlinhar'=>'center'),
		//array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prioridade', 'callback'=>'validaCampoPrioridade99999'),		
		array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEmp', 'colunaExibir' => false),
		array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
		array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
		array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
		array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
		array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'T147_D110_Id', 'colunaTitulo' => 'IdCompetencia', 'colunaExibir' => false),
		//array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência'),
		array('colunaValor' => 'T147_D109_Id', 'colunaTitulo' => 'IdFuncionario', 'colunaExibir' => false),
		array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Funcionário', 'style' => 'font-weight:bold'),
		array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Início', 'callback'=>'gCorrigeData'),
		array('colunaValor' => 'T147_Hora_Fim', 'colunaTitulo' => 'Fim', 'callback'=>'gCorrigeData'),
		array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
		//array('colunaValor' => 'T147_Hora_Pausa', 'colunaTitulo' => 'Hora Pausa', 'callback'=>'gCorrigeData'),
		//array('colunaValor' => 'T147_Hora_Reinicio', 'colunaTitulo' => 'Hora Reinício', 'callback'=>'gCorrigeData'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T147';
		$extra = <<<EOT
			left join T005 on T005_Id=T147_T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join D110 on D110_Id=T147_D110_Id
			left join D109 on D109_Id=T147_D109_Id
			left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			left join D116 on D116_Id=T147_D116_Id
			where
			(T147_Status='C')
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/srv/srv002/content/srv002content02/', '&Status=C', [950,600]);">Resumo</button>
				<button onclick="divRefresh('{$g['divId']}', true);">Atualizar</button>
			</div>
EOT;

		//Função que define a classe CSS para cada linha do GRID
		function gridLinha($array) {
			if ($array['todosCampos']['T147_Flag_Conferido'] == "S") {
				return 'linhaVerdeEscuro';
			}
		}		

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
			'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Concluídos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Gerenciamento Concluídos - ID: ' + acaoId, '/srv/srv002/content/srv002content08/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
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

	/**
	* Grid Processos do Serviço
	*/
	case 'srv002grid04':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		//array('colunaValor'=> 'T148_Id', 'colunaTitulo'=>'ID', 'chavePrimaria'=>true),
		array('colunaValor'=> 'T148_T147_Id', 'colunaTitulo'=>'IdServiço', 'colunaExibir' => false),
		array('colunaValor'=> 'T148_Titulo', 'colunaTitulo'=>'Título'),
		array('colunaValor'=> 'T148_Descricao', 'colunaTitulo'=>'Descrição'),
		);

		$from = "T148";
		$extra = <<<EOT
			left join T147 on T147_Id=T148_T147_Id
			where
			(T148_T147_id='$r_acaoId')
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T148_Titulo' => array('titulo' => 'Titulo', 'tipo' => 'texto'),
			'T148_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				<span class="item" style="text-align:right">Total Materiais<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
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

	/**
	* Grid Materiais do Serviço
	*/
	case 'srv002grid05':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		//array('colunaValor' => 'T149_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true),
		array('colunaValor' => 'T149_T147_Id', 'colunaTitulo' => 'IdServico', 'colunaExibir' => false),
		array('colunaValor' => 'T149_D009_Id', 'colunaTitulo' => 'IdMaterial', 'colunaExibir' => false),		
		array('colunaValor' => 'T149_Quantidade', 'colunaTitulo' => 'Quantidade'),
		array('colunaValor' => 'T149_D037_Id', 'colunaTitulo' => 'IdUnidade', 'colunaExibir' => false),
		array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
		array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T149';
		$extra = <<<EOT
			left join T147 on T147_Id=T149_T147_Id
			left join D009 on D009_Id=T149_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id	
			left join D037 on D037_Id=T149_D037_Id
			where
			(T149_T147_id='$r_acaoId')
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Descricao_Produto' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
			'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				<span class="item" style="text-align:right">Total Materiais<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
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

	/**
	* Grid Competências totais
	*/
	case 'srv002grid06':
		

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T147_D110_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
		array('colunaValor' => 'count(*) as Total', 'colunaTitulo' => 'Quantidade'),
		array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Titulo'),
		//array('colunaValor' => 'T149_Quantidade', 'colunaTitulo' => 'Quantidade'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T147';
		$extra = <<<EOT
			left join D110 on D110_Id=T147_D110_Id
			WHERE T147_Status='{$r_status}'
			GROUP BY T147_D110_Id
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D110_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T147_D110_Id', '', $extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Competências<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
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


	/**
	* Grid Materiais totais
	*/
	case 'srv002grid07':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T149_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
		array('colunaValor' => 'SUM(T149_Quantidade)', 'colunaTitulo' => 'Quantidade'),
		array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
		array('colunaValor' => 'T149_T147_Id', 'colunaTitulo' => 'IdServico', 'colunaExibir' => false),
		array('colunaValor' => 'T149_D009_Id', 'colunaTitulo' => 'IdMaterial', 'colunaExibir' => false),
		array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
		array('colunaValor' => 'T149_D037_Id', 'colunaTitulo' => 'IdUnidade', 'colunaExibir' => false),		
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T149';
		$extra = <<<EOT
			left join D037 on D037_Id=T149_D037_Id
			left join T147 on T147_Id=T149_T147_Id
			left join D009 on D009_Id=T149_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id			
			WHERE T147_Status='{$r_status}'
			GROUP BY T149_D009_Id, T149_D037_Id
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("SUM(T149_Quantidade)", $from, str_replace('GROUP BY T149_D009_Id, T149_D037_Id', '', $extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total de Materiais<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
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
	
	
	/**
	* Grid Materiais totais
	*/
	case 'srv002gridGerenciamento':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor' => 'D110_Id', 'colunaTitulo' => 'Id110', 'colunaExibir'=>false),
			array('colunaValor' => 'count(*) as Total', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência'),
			array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Total Restante', 'callback'=>'validaCampoTempoEstimadoRestante'),
			array('colunaValor' => 'D110', 'colunaTipo'=>'livre', 'colunaTitulo' => 'Carga Horária Disponível', 'callback'=>'validaCampoCargaHorariaDisponivel'),
			array('colunaValor' => 'D110', 'colunaTipo'=>'livre', 'colunaTitulo' => 'Previsão Conclusão', 'callback'=>'validaCampoCargaPrevisaoConclusao'),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T147';
		$extra = <<<EOT
			left join D110 on D110_Id=T147_D110_Id
			WHERE T147_Status='P' 
			OR T147_Status='E'
			GROUP BY T147_D110_Id
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D111_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T147_D110_Id', '', $extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Competências<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
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
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	/**
	* Grid Materiais totais
	*/
	case 'srv002gridFuncionarios':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D109_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Nome', 'callback'=>'abrirEdicaoFuncionario'),
			array('colunaValor' => 'Status', 'colunaTitulo' => 'Status', 'colunaTipo' => 'livre', 'callback'=>'validaCampoStatus'),
			array('colunaValor' => 'Status', 'colunaTitulo' => 'Pendentes', 'colunaTipo' => 'livre', 'callback'=>'validaCampoPendentes', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'Status', 'colunaTitulo' => 'Execução', 'colunaTipo' => 'livre', 'callback'=>'validaCampoExecucao', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'Status', 'colunaTitulo' => 'Pausados', 'colunaTipo' => 'livre', 'callback'=>'validaCampoPausados', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'Tempo Alocado', 'colunaTitulo' => 'Tempo Alocado', 'colunaTipo'=>'livre', 'callback'=>'validaCampoTempoAlocado'),
			array('colunaValor' => 'Estimativa de Liberação', 'colunaTitulo' => 'Estimativa de Liberação', 'colunaTipo'=>'livre', 'callback'=>'validaCampoEstimativaLiberacao'),
			array('colunaValor' => 'D109_Carga_Horaria', 'colunaTitulo' => 'Carga Horária', 'colunaEditar'=>true),
			array('colunaValor' => 'Competências', 'colunaTitulo' => 'Competências', 'colunaTipo'=>'livre', 'callback'=>'validaCampoCompetencias'),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'D109';
		$extra = <<<EOT
		WHERE D109_Flag_Ativo='S'
			
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D109_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Funcionários<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D109_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad010/content/cad010content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,600]);
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'srv002gridHistorico':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T169_Id', $g['divId'])),
			array('colunaValor' => 'T169_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T169_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T169_D109_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
			array('colunaValor' => 'T169_T147_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
			//array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Funcionario'),
			array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo'),
			array('colunaValor' => 'T169_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T169_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T169';

		$extra = <<<EOT
			left join T147 on T147_Id=T169_T147_Id
			left join T005 on T005_Id=T147_T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			left join D116 on D116_Id=T147_D116_Id
			left join D109 on D109_Id=T169_D109_Id
			where
			(T169_T147_Id = {$r_acaoId})
EOT;

			/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Serviço', 'tipo' => 'texto'),
			'T169_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T169_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D123_Id-/, '');
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

			$novoIdTelaForm = uniqid();
			
			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;
		
		case 'srv002gridVeiculos':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T169_Id', $g['divId'])),
			//array('colunaValor' => 'T169_Id', 'colunaTitulo' => 'OS', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaEditar'=>true, 'callback'=>'validaCampoPrioridade99999', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T147_Prioridade'),
			array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo'),
			array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Placa Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Cor Veíu', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
			array('colunaValor' => 'T147_Pausado', 'colunaTitulo' => 'Serviço', 'colunaExibir'=>false),
			array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatusVeiculos'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'callback'=>'gCorrigeTempo'),
			array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Início', 'callback'=>'gCorrigeData'),
			//array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Previsão Fim', 'callback'=>'validaCampoPrevisaoFimVeiculo'),
			array('colunaValor' => '', 'colunaTitulo' => 'Previsão Fim', 'callback'=>'validaCampoPrevisaoFimVeiculo', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T147_Hora_Fim', 'colunaTitulo' => 'Fim', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
			
	);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T147';

		$extra = <<<EOT
			left join D116 on D116_Id=T147_D116_Id
			left join T005 on T005_Id=T147_T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			order by D116_Placa
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D116_Modelo' => array('titulo' => 'Veículo', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T147_Titulo' => array('titulo' => 'Serviço', 'tipo' => 'texto'),
			'T147_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);
			
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Serviços<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Gerenciamento - ID: ' + acaoId, '/srv/srv002/content/srv002ContentHistVeiculos/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
	           });
	           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
	           
	           // Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T147_Prioridade(T147_Id, campoEditado, valorAntigo, novoValor) {
					T147_Id = T147_Id.replace(/T147_Id-/, '');
					if (campoEditado == 'T147_Prioridade') {
						$.getJSON('/srv/srv002/grid_func-ajax/atualizarPrioridade/?ajax=true&T147_Id='      	+ encodeURIComponent(T147_Id) + 
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
		
		case 'srv002gridHistVeiculo':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T169_Id', $g['divId'])),
				array('colunaValor' => 'T169_Id', 'colunaTitulo' => 'OS', 'chavePrimaria' => true, 'colunaExibir'=>false),
				array('colunaValor' => 'T169_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
				array('colunaValor' => 'T169_D109_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
				array('colunaValor' => 'T169_T147_Id', 'colunaTitulo' => 'IdFuncionário', 'colunaExibir' => false),
				array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo'),
				array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
				array('colunaValor' => 'T169_Descricao', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'T169_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T169';

			$extra = <<<EOT
				left join T147 on T147_Id=T169_T147_Id
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D116 on D116_Id=T147_D116_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				where
				(T169_T147_Id = {$r_acaoId})
EOT;

			/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Serviço', 'tipo' => 'texto'),
			'T169_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T169_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D123_Id-/, '');
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

			$novoIdTelaForm = uniqid();
			
			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;
		
	case 'srv002gridCancelados':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaAlinhar'=>'center'),
		array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'Nº Pedido', 'colunaAlinhar'=>'center'),
		array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prioridade'),
		array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEmp', 'colunaExibir' => false),
		array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço'),
		array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
		array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo'),
		array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
		array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
		array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
		array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado'),
		array('colunaValor' => 'T147_D110_Id', 'colunaTitulo' => 'IdCompetencia', 'colunaExibir' => false),
		//array('colunaValor' => 'D110_Titulo', 'colunaTitulo' => 'Competência'),
		array('colunaValor' => 'T147_D109_Id', 'colunaTitulo' => 'IdFuncionario', 'colunaExibir' => false),
		array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Funcionário'),
		array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
		// array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Início', 'callback'=>'gCorrigeData'),
		// array('colunaValor' => 'T147_Hora_Fim', 'colunaTitulo' => 'Fim', 'callback'=>'gCorrigeData'),
		//array('colunaValor' => 'T147_Hora_Pausa', 'colunaTitulo' => 'Hora Pausa', 'callback'=>'gCorrigeData'),
		//array('colunaValor' => 'T147_Hora_Reinicio', 'colunaTitulo' => 'Hora Reinício', 'callback'=>'gCorrigeData'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T147';
		$extra = <<<EOT
			left join T005 on T005_Id=T147_T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join D110 on D110_Id=T147_D110_Id
			left join D109 on D109_Id=T147_D109_Id
			left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			left join D116 on D116_Id=T147_D116_Id
			where
			(T147_Status='N' AND T147_Flag_Cancelado='S')
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/srv/srv002/content/srv002content02/', '&Status=C', [950,600]);">Resumo</button>
				<button onclick="divRefresh('{$g['divId']}', true);">Atualizar</button>
			</div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T147_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
			'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Cancelados<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cancelados - ID: ' + acaoId, '/srv/srv002/content/srv002content08/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
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
	break;
	
	case 'srv002GridPendentes':
		$acaoId  = (isset($r_acaoId)) ? $r_acaoId : false;
		$Status  = (isset($r_Status)) ? $r_Status : false;
		$Pausado = (isset($r_Pausado)) ? $r_Pausado : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prd', 'colunaAlinhar' => 'left', 'callback'=>'validaCampoPrioridade'),
			array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
			array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculoSrv002'),
			array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T147_Pausado', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center', 'callback'=>'validaCampoStatusSrv002'),
			array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'callback' => 'gCorrigeTempo', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T147_Hora_Inicio', 'colunaTitulo' => 'Hora Início', 'colunaExibir'=>false, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Hora Início', 'colunaTitulo' => 'Hora Início', 'callback' => 'validaCampoHoraInicio', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Previsão Fim', 'colunaTitulo' => 'Previsão Fim', 'colunaTipo'=>'livre', 'callback' => 'validaCampoPrevisao', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T147';
		$extra = <<<EOT
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on T005_D024_Id=D024_Id
				left join D116 on D116_Id=T147_D116_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				where (T147_Status <>'C' AND T147_Status <>'N') AND T147_D109_Id='{$acaoId}'
				
EOT;
		// if($Pausado == "S"){
		// $extra .=<<<EOT
				// AND T147_Pausado='S'
// EOT;
		// }else{
		// $extra .=<<<EOT
				// AND (T147_Pausado IS NULL OR T147_Pausado='N') 
// EOT;
		// }

		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/srv/srv002/content/srv002content02/', '&Status=C', [950,600]);">Resumo</button>
				// <button onclick="divRefresh('{$g['divId']}', true);">Atualizar</button>
			// </div>
// EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			// 'T147_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			// 'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			// 'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
			// 'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Atribuídos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Processos e Materiais - ID: ' + acaoId, '/srv/srv002/content/srv002content08/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
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
	break;
	
	case 'srv002gridProdutos':
		/**
		* Definição do Grid (Tabela)
		*/
		
		$sql = "SELECT T147_T005_Id FROM T147 WHERE T147_Id='$r_acaoId'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		
		$grid = array(
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtd', 'style' => 'font-weight:bold', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
			
		);

		/**
		* Definição da SQL
		*/
		$from = 'T006';
		$extra = <<<EOT
				left join D037 on D037_Id=T006_D037_Id
				left join D009 on D009_Id=T006_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				WHERE T006_T005_Id='{$linha['T147_T005_Id']}'
				
EOT;
		// if($Pausado == "S"){
		// $extra .=<<<EOT
				// AND T147_Pausado='S'
// EOT;
		// }else{
		// $extra .=<<<EOT
				// AND (T147_Pausado IS NULL OR T147_Pausado='N') 
// EOT;
		// }

		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/srv/srv002/content/srv002content02/', '&Status=C', [950,600]);">Resumo</button>
				// <button onclick="divRefresh('{$g['divId']}', true);">Atualizar</button>
			// </div>
// EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			// 'T147_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			// 'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			// 'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
			// 'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Produtos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/T147_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Processos e Materiais - ID: ' + acaoId, '/srv/srv002/content/srv002content08/', '&acaoId=' + encodeURIComponent(acaoId), [1000,600]);
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

/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
