<?php
namespace hardness;

require_once('prog/crm/crm005/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/crm/crm005/grid_func-js.php');
// Imagem para exclusão da linha


$data = (isset($r_data)) ? $r_data : false;
$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: ven001TabPrincipal
	 */
	case 'crm005GridPrePedidoCRM':
		/**
		* Definição do Grid (Tabela)
		* N.Pedido	Peso PCT	Qtd PCT	Peso UND	Class.Fiscal	Status do pedido
		*/
		$D037 = array('D037', 'D037_Id', 'D037_Unidade');
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusao', 'style' => 'width: 1px;'),
			array('colunaValor' => 'T099_Id', 'colunaTitulo' => 'T099_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D037_Id', 'colunaTitulo' => 'D037_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T099_Horario_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;', 'colunaEditar' => true, 'colunaEditarGeraSelect' => $D037),
			array('colunaValor' => 'T099_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeNumero'),
			//array('colunaValor' => 'T099_Qte_KLG', 'colunaTitulo' => 'KLG', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			//array('colunaValor' => 'T099_Qte_PCT', 'colunaTitulo' => 'PCT', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			//array('colunaValor' => 'T099_Qte_UND', 'colunaTitulo' => 'UND', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			array('colunaValor' => 'T099_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unit', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true),
			array('colunaValor' => 'precoTotal','colunaTitulo' => 'Preço Total', 'callback'=>'precoTotal', 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'N° Pedido', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
			//array('colunaValor' => 'D001_Peso_Total_Kg', 'colunaTitulo' => 'Peso PCT', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtde PCT', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'T099_Peso_Unitario', 'colunaTitulo' => 'Peso UND', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Classificacao_Fiscal(D009_Id) as ClasFis', 'colunaTitulo' => 'Class. Fiscal', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status_Pedido', 'colunaTitulo' => 'Status Pedido', 'colunaAlinhar' => 'center'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T099';
		
		/**
		* Filtro
		*/
		/*$gridFiltro = array(
			'T099_Data' => array('titulo' => 'Data', 'tipo' => 'data'),	
		);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		// Gera o filtro e acrescenta ao $extra
		$data = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		
		$dataisset = $g['ui']->getGridFiltroCampoBusca('T099_Data');
		// Verifica uma data setada
		
		$data = $dataisset[0];*/
		
		if(!empty($data)) {	
			$data = gCorrigeDataInverte($data);
		} else {
			$data = date('Y-m-d');
		}

		$extra = <<<EOT
			LEFT JOIN D009 ON T099_D009_Id = D009_Id
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 on D001_Id = D049_D001_Id
			LEFT JOIN D037 ON T099_D037_Id = D037_Id
			LEFT JOIN T006 ON T006_T099_Id = T099_Id
			LEFT JOIN T005 ON T006_T005_Id = T005_Id
			WHERE T099_Data = '{$data}' AND T099_D024_Id = '{$D024_Id}'
EOT;
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Gera o filtro e acrescenta ao $extra (novamente, agora com o extra)
		
		$total = mysql_query("SELECT T099_Valor_Preco_Unitario * T099_Quantidade AS Total,
						 COUNT(T099_Id)
						 FROM T099
						 WHERE T099_Data = '{$data}' 
						 AND T099_D024_Id = '{$r_D024_Id}'");
		$total=mysql_fetch_array($total);
		$valorTotal = gCorrigeNumero($total[0]);
		$numeroProdutos = $total[1];
		
		$dataInversa = gCorrigeData($data);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Produtos<br><input type="text" value="{$numeroProdutos}" class="whiteBg" id="" size="3" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="9" readonly></span>
				
				<button type="button" id="gerarRomaneiosCRM" style="margin-left:15px;margin-top:5px;">Gerar Romaneios</button>
				<button type="button" id="gerarNFCRM" style="margin-top: 5px;">Gerar NF</button>
				<button type="button" id="imprimirRomaneiosCRM" style="margin-top: 5px;">Imprimir Romaneios</button>
				<button type="button" id="resumoCRM" style="margin-top: 5px;">Resumo Diário</button>
				<button type="button" id="programacaoCRM" style="margin-top: 5px;">Programação</button>
				<div style="clear:both;"></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	

					$('#gerarNFCRM').unbind('click').bind('click', function(data) {
						nova_janela('../hardness/pre_pedido/gerar_pedido.php?Data=$dataInversa&D024_Id=$D024_Id&Flag_ACP=6',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#gerarRomaneiosCRM').unbind('click').bind('click', function(data) {

						//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Programação', '/crm/crm005/content/crm005contentProgramacao/', '&D024_Id=$D024_Id&data=$data', [950,615]);		   

						nova_janela('../hardness/pre_pedido/gerar_pedido.php?Data=$dataInversa&D024_Id=$D024_Id&Flag_ACP=2',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#imprimirRomaneiosCRM').unbind('click').bind('click', function(data) {
						nova_janela('../hardness/pre_pedido/selecionar_romaneios_impressao.php?Data=$dataInversa',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#resumoCRM').unbind('click').bind('click', function(data) {
						nova_janela('../hardness/pre_pedido/total_produtos2.php?Data=$dataInversa',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#programacaoCRM').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Programação', '/crm/crm005/content/crm005contentProgramacao/', '&D024_Id=$D024_Id&data=$data', [950,615]);		   
					});
				} </script></div>
			</div>
EOT;
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
	break;
	
	case 'crm005GridPrePedido':
		/**
		* Definição do Grid (Tabela)
		* N.Pedido	Peso PCT	Qtd PCT	Peso UND	Class.Fiscal	Status do pedido
		*/
		$D037 = array('D037', 'D037_Id', 'D037_Unidade');
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusao', 'style' => 'width: 1px;'),
			array('colunaValor' => 'T099_Id', 'colunaTitulo' => 'T099_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D037_Id', 'colunaTitulo' => 'D037_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T099_Horario_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;', 'colunaEditar' => true, 'colunaEditarGeraSelect' => $D037),
			array('colunaValor' => 'T099_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeNumero'),
			//array('colunaValor' => 'T099_Qte_KLG', 'colunaTitulo' => 'KLG', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			//array('colunaValor' => 'T099_Qte_PCT', 'colunaTitulo' => 'PCT', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			//array('colunaValor' => 'T099_Qte_UND', 'colunaTitulo' => 'UND', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			array('colunaValor' => 'T099_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unit', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true),
			array('colunaValor' => 'precoTotal','colunaTitulo' => 'Preço Total', 'callback'=>'precoTotal', 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'N° Pedido', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
			//array('colunaValor' => 'D001_Peso_Total_Kg', 'colunaTitulo' => 'Peso PCT', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtde PCT', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'T099_Peso_Unitario', 'colunaTitulo' => 'Peso UND', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Classificacao_Fiscal(D009_Id) as ClasFis', 'colunaTitulo' => 'Class. Fiscal', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status_Pedido', 'colunaTitulo' => 'Status Pedido', 'colunaAlinhar' => 'center'),
			
		);

		/**
		* Definição da SQL
		*/
		$from = 'T099';
		
		/**
		* Filtro
		*/
		/*$gridFiltro = array(
			'T099_Data' => array('titulo' => 'Data', 'tipo' => 'data'),	
		);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		// Gera o filtro e acrescenta ao $extra
		$data = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		
		$dataisset = $g['ui']->getGridFiltroCampoBusca('T099_Data');
		// Verifica uma data setada
		
		$data = $dataisset[0];*/
		
		if(!empty($data)) {	
			$data = gCorrigeDataInverte($data);
		} else {
			$data = date('Y-m-d');
		}

		$extra = <<<EOT
			LEFT JOIN D009 ON T099_D009_Id = D009_Id
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 on D001_Id = D049_D001_Id
			LEFT JOIN D037 ON T099_D037_Id = D037_Id
			LEFT JOIN T006 ON T006_T099_Id = T099_Id
			LEFT JOIN T005 ON T006_T005_Id = T005_Id
			WHERE T099_Data = '{$data}' AND T099_D024_Id = '{$D024_Id}'
EOT;
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Gera o filtro e acrescenta ao $extra (novamente, agora com o extra)
		
		$total = mysql_query("SELECT SUM(T099_Valor_Preco_Unitario * T099_Quantidade) AS Total,
						 COUNT(T099_Id)
						 FROM T099
						 WHERE T099_Data = '{$data}' 
						 AND T099_D024_Id = '{$r_D024_Id}'");
		$total=mysql_fetch_array($total);
		$valorTotal = gCorrigeNumero($total[0]);
		$numeroProdutos = $total[1];
		
		$dataInversa = gCorrigeData($data);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Produtos<br><input type="text" value="{$numeroProdutos}" class="whiteBg" id="" size="3" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="9" readonly></span>
				
				<button type="button" id="gerarRomaneios" style="margin-left: 15px; margin-top: 5px;">Gerar Romaneios</button>
				<button type="button" id="gerarNF" style="margin-top: 5px;">Gerar NF</button>
				<button type="button" id="imprimirRomaneios" style="margin-top: 5px;">Imprimir Romaneios</button>
				<button type="button" id="resumo" style="margin-top: 5px;">Resumo Diário</button>
				<button type="button" id="programacao" style="margin-top: 5px;">Programação</button>
				<div style="clear:both;"></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
					$('#gerarNF').unbind('click').bind('click', function(data) {

						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Confirmar rota', '/crm/crm005/content/crm005contentSelecionarVendedorInterno/', '&D024_Id=$r_D024_Id&data=$dataInversa&Flag_ACP=6', [350,150]);
						//nova_janela('../hardness/pre_pedido/gerar_pedido_3.php?Data=$dataInversa&D024_Id=$D024_Id&Flag_ACP=6',0,0,700,600,'no','no','no','no','yes','yes',0);

					});
					$('#gerarRomaneios').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Confirmar rota', '/crm/crm005/content/crm005contentSelecionarVendedorInterno/', '&D024_Id=$r_D024_Id&data=$dataInversa&Flag_ACP=2', [350,150]);
						//nova_janela('../hardness/pre_pedido/gerar_pedido_3.php?Data=$dataInversa&D024_Id=$D024_Id&Flag_ACP=2',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#imprimirRomaneios').unbind('click').bind('click', function(data) {
						nova_janela('../hardness/pre_pedido/selecionar_romaneios_impressao.php?Data=$dataInversa',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#resumo').unbind('click').bind('click', function(data) {
						nova_janela('../hardness/pre_pedido/total_produtos2.php?Data=$dataInversa',0,0,700,600,'no','no','no','no','yes','yes',0);
					});
					
					$('#programacao').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Programação', '/crm/crm005/content/crm005contentProgramacao/', '&D024_Id=$r_D024_Id&data=$data', [950,615]);		   
					});
					setTimeout(function focar() {
						$('#' + '{$r_divOutro}' + '.itemForm-Procura_Cliente_Pesquisa').focus();
						}, 400);
				} </script></div>
			</div>
EOT;

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
	break;
	
	case 'crm005GridProgramacao':
		/**
		* Definição do Grid (Tabela)
		*/		
			$grid = array(
				array('colunaValor'=>'T099_Id', 'colunaTitulo'=>'Id', 'colunaExibir' => false),
				array('colunaValor'=>'T099_Data', 'colunaTitulo' => 'Data','chavePrimaria'=>true, 'colunaAgrupar' => true, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;background-color: white;', 'callback' => 'escolherPedido'), //  'colunaExibir' => false
				array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => "width: 1px;"),
				array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'left'),
				array('colunaValor'=>'D037_Unidade', 'colunaTitulo' => 'Unidade', 'colunaAlinhar' => 'center'),
				array('colunaValor'=>'T099_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'center'),
				array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Preço Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T099';
			
			$extra = <<<EOT
				LEFT JOIN D009 ON D009_Id = T099_D009_Id
				LEFT JOIN D001 ON D001_Id = D009_D001_Id
				LEFT JOIN D037 ON D037_Id = D001_D037_Id
				WHERE T099_D024_Id = '{$r_D024_Id}' 
				AND T099_Data != '{$r_data}'
				ORDER BY T099_Data DESC
						
EOT;
		/**
		* Filtro
		*/
		
		$gridFiltro = array(
			'T099_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Modelo', 'campoAgrupar' => 'T099_Data', 'campoMostrar' => 'T099_Data', 'campoOrdem' => 'DESC', 'campoSelecionado' => true)
			)),
		);	
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T099_Data = id[0].replace(/T099_Data-/, '');
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/crm/crm005/grid_func-ajax/preencherPedido/?ajax=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&dataPedido=' + T099_Data + '&data=' + encodeURIComponent('{$r_data}') + '&callback=?', function(request) {
						$('#' + '{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$r_divIdParent}', true);
							fecharJanela('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, buttons);
						}
					});
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
		
	case 'crm005GridProdutoCliente':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Código', 'align' => 'center','colunaExibir' => false),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),
							
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN D024 ON D024_Id=T005_D024_Id 
				LEFT JOIN T006 ON T006_T005_Id=T005_Id 
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN B001 ON B001_Id=D049_D001_Id 
				WHERE T005_Data_Emissao='{$data}'
				AND T005_Flag_Status!='8' 
				GROUP BY D024_Id, D001_Id 
				ORDER BY D001_Id, D024_Nome_Empresa
					
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Modelo', 'campoAgrupar' => 'D001_Id', 'campoMostrar' => 'D001_Descricao_Produto', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			)),
		);	
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		
	case 'crm005GridProduto':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'align' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),			
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN T006 ON T006_T005_Id=T005_Id 
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN B001 ON B001_Id=D049_D001_Id
				WHERE T005_Data_Emissao='{$data}' 
				AND T005_D024_Id = '{$D024_Id}' 
				AND T005_Flag_Status!='8' 
				GROUP BY D001_Id 
				ORDER BY D001_Descricao_Produto
					
EOT;
		/**
		* Filtro
		*/
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		
	case 'crm005GridTipo':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'B001_TIPPRO', 'colunaTitulo' => 'Código','colunaExibir' => false),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'align' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),			
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN T006 ON T006_T005_Id=T005_Id 
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN B001 ON B001_Id=D049_D001_Id 
				WHERE T005_Data_Emissao='{$data}' 
				AND T005_D024_Id = '{$D024_Id}' 
				AND T005_Flag_Status!='8' 
				GROUP BY D001_Id 
				ORDER BY D001_Descricao_Produto
					
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Modelo', 'campoAgrupar' => 'B001_TIPPRO', 'campoMostrar' => 'B001_TIPPRO', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			)),
		);	
			
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		
	case 'crm005GridVendedor':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'T005_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'align' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),			
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN T006 ON T006_T005_Id=T005_Id 
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN B001 ON B001_Id=D049_D001_Id
				LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno 
				WHERE T005_Data_Emissao='{$data}' 
				AND T005_D024_Id = '{$D024_Id}' 
				AND T005_Flag_Status!='8' 
				GROUP BY T005_C007_Id_Vendedor_Interno, D001_Id 
				ORDER BY T005_C007_Id_Vendedor_Interno, D001_Descricao_Produto				
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
		'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
			array('title' => 'Modelo', 'campoAgrupar' => 'T005_C007_Id_Vendedor_Interno', 'campoMostrar' => 'C007_Nome', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
		)),
	);	
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		
	case 'crm005GridVendedorCliente':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'align' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),			
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN T006 ON T006_T005_Id=T005_Id 
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN B001 ON B001_Id=D049_D001_Id
				LEFT JOIN D024 ON D024_Id=T005_D024_Id
				LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno 
				WHERE T005_Data_Emissao='{$data}' 
				AND T005_Flag_Status!='8' 
				GROUP BY T005_D024_Id, D001_Id 
				ORDER BY D024_Nome_Fantasia, D001_Descricao_Produto			
EOT;
		/**
		* Filtro
		*/
		$Todos = array('title' => 'Todos', 'value' => '');
		$C007_Id = gGeraSelect('C007','C007_Id','C007_Nome',"WHERE C007_Nome IS NOT NULL");
		array_unshift($C007_Id, $Todos);	
			
		$gridFiltro = array(
		'T005_C007_Id_Vendedor_Interno' => array('titulo' => 'Unidade', 'tipo' => 'select',	
				'select' => $C007_Id
			),
		'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
			array('title' => 'Modelo', 'campoAgrupar' => 'D024_Id', 'campoMostrar' => 'D024_Nome_Fantasia', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
		)),
	);	
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		
	case 'crm005GridCliente':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'align' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),			
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN T006 ON T006_T005_Id=T005_Id
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN D024 ON D024_Id=T005_D024_Id
				WHERE T005_Data_Emissao='{$data}'
				AND T005_Flag_Status!='8' 
				GROUP BY T005_D024_Id, D001_Id 
				ORDER BY D024_Nome_Fantasia, D001_Descricao_Produto
					
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Modelo', 'campoAgrupar' => 'D024_Id', 'campoMostrar' => 'D024_Nome_Fantasia', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			)),
		);	
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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
		
	case 'crm005GridHora':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor' => 'T099_Horario_Entrega', 'colunaTitulo' => 'Código', 'align' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'align' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'align' => 'left'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"UND")) as Qte_UND', 'colunaTitulo' => 'Unidades', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"PCT")) as Qte_PCT', 'colunaTitulo' => 'Pacotes', 'align' => 'center'),
				array('colunaValor' => 'SUM(Quantidade(T006_Quantidade,D037_Unidade,D001_Peso_Unitario_Kg,D001_Peso_Total_Kg,D001_Qte_Embalagem,"KLG")) as Qte_KLG', 'colunaTitulo' => 'Kilos', 'align' => 'center'),			
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T005';
			
			$extra = <<<EOT
				LEFT JOIN T006 ON T006_T005_Id=T005_Id
				LEFT JOIN T099 ON T099_Id=T006_T099_Id 
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D037 ON D037_Id=T006_D037_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN D024 ON D024_Id=T005_D024_Id
				WHERE T005_Data_Emissao='{$data}' 
				AND T005_D024_Id = '{$D024_Id}' 
				AND T005_Flag_Status!='8' 
				GROUP BY T099_Horario_Entrega, D001_Id 
				ORDER BY T099_Horario_Entrega, D001_Descricao_Produto
					
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Modelo', 'campoAgrupar' => 'T099_Horario_Entrega', 'campoMostrar' => 'T099_Horario_Entrega', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			)),
		);	
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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

	case 'crm005GridPrePedidoEmpresas':
		/**
		* Definição do Grid (Tabela)
		* N.Pedido	Peso PCT	Qtd PCT	Peso UND	Class.Fiscal	Status do pedido
		*/
		$data = isset($r_data) ? $r_data : false;
		$data_invertida = gCorrigeDataInverte($r_data);
		$D037 = array('D037', 'D037_Id', 'D037_Unidade');
		$grid = array(
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D024_Bairro', 'colunaTitulo' => 'Bairro', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'concat("(",D024_DDD_Telefone_1,") ", D024_Telefone_1) as Telefone', 'colunaTitulo' => 'Telefone', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'SUM((T099_Valor_Preco_Unitario * T099_Quantidade)) as total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T099_Data', 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'right', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Nº Pedido', 'colunaAlinhar' => 'center'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T099';
		
		/**
		* Filtro
		*/
		/*$gridFiltro = array(
			'T099_Data' => array('titulo' => 'Data', 'tipo' => 'data'),	
		);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		// Gera o filtro e acrescenta ao $extra
		$data = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		*/

		$extra = <<<EOT
			LEFT JOIN T006 ON T006_T099_Id = T099_Id
			LEFT JOIN T005 ON T006_T005_Id = T005_Id
			LEFT JOIN D024 on D024_Id = T099_D024_Id
			LEFT JOIN D020 on D020_Id = D024_D020_Id
			LEFT JOIN D018 on D018_Id = D024_D018_Id
			WHERE T099_Data = '{$data_invertida}'
			GROUP BY T099_D024_Id
EOT;
		
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Gera o filtro e acrescenta ao $extra (novamente, agora com o extra)
		
		// $total = mysql_query("SELECT T099_Valor_Preco_Unitario * T099_Quantidade AS Total,
		// 				 COUNT(T099_Id)
		// 				 FROM T099
		// 				 WHERE T099_Data = '{$data}' 
		// 				 AND T099_D024_Id = '{$r_D024_Id}'");
		// $total=mysql_fetch_array($total);
		// $valorTotal = gCorrigeNumero($total[0]);
		// $numeroProdutos = $total[1];
		
		// $dataInversa = gCorrigeData($data);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);	

		$extraValorTotal = str_replace('GROUP BY T099_D024_Id', '', $extra);

		$Total = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT T099_D024_Id)", $from, $extraValorTotal);	
		$ValorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM((T099_Valor_Preco_Unitario * T099_Quantidade)) as total", $from, $extraValorTotal));	
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Clientes<br><input type="text" value="{$Total}" class="whiteBg" id="" size="3" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$ValorTotal}" class="whiteBg" id="" size="9" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
// 				<!-- <button type="button" id="gerarRomaneiosCRM" style="margin-left: 15px; margin-top: 5px;">Gerar Romaneios</button> -->
// 				<!-- <button type="button" id="gerarNFCRM" style="margin-top: 5px;">Gerar NF</button> -->
// 				<!-- <button type="button" id="imprimirRomaneiosCRM" style="margin-top: 5px;">Imprimir Romaneios</button> -->
// 				<!-- <button type="button" id="resumoCRM" style="margin-top: 5px;">Resumo Diário</button> -->
// 				<!-- <button type="button" id="programacaoCRM" style="margin-top: 5px;">Programação</button> -->
// 				<div style="clear:both;"></div>
// 				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
// 					$('#gerarNFCRM').unbind('click').bind('click', function(data) {
// 						//nova_janela('../hardness/pre_pedido/gerar_pedido.php?Data=$dataInversa&D024_Id=$D024_Id&Flag_ACP=6',0,0,700,600,'no','no','no','no','yes','yes',0);
// 					});
					
// 					$('#gerarRomaneiosCRM').unbind('click').bind('click', function(data) {
// 						//nova_janela('../hardness/pre_pedido/gerar_pedido.php?Data=$dataInversa&D024_Id=$D024_Id&Flag_ACP=2',0,0,700,600,'no','no','no','no','yes','yes',0);
// 					});
					
// 					$('#imprimirRomaneiosCRM').unbind('click').bind('click', function(data) {
// 						//nova_janela('../hardness/pre_pedido/selecionar_romaneios_impressao.php?Data=$dataInversa',0,0,700,600,'no','no','no','no','yes','yes',0);
// 					});
					
// 					$('#resumoCRM').unbind('click').bind('click', function(data) {
// 						//nova_janela('../hardness/pre_pedido/total_produtos.php?Data=$dataInversa',0,0,700,600,'no','no','no','no','yes','yes',0);
// 					});
					
// 					$('#programacaoCRM').unbind('click').bind('click', function(data) {
// 						//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Programação', '/crm/crm005/content/crm005contentProgramacao/', '&D024_Id=$D024_Id&data=$data', [950,615]);		   
// 					});
// 				} </script></div>
// 			</div>


		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var D024_Id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
				divSetarAcaoId('{$r_divRefresh}', D024_Id, 'D024_Id');
				//divSetarAcaoId('{$r_divRefresh}', $data_invertida, 'data');
				divRefresh('{$r_divRefresh}');
				$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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