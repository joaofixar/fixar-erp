<?php
namespace hardness;
/**
* Weiberlan
* 12/11/2011
*/
// Classes
//require_once('bibliotecas/classes/CAD001.php');
//$CAD001 = new CAD001();
// Carrega funções callback
require_once('prog/est/est002/grid_func-callback.php');
require_once('prog/est/est002/grid_func-callback-retornarExpedicao.php');

// Carrega funções do ajax
require_once('prog/est/est002/grid_func-js.php');
require_once('prog/est/est002/grid_func-js-retornarExpedicao.php');
require_once('prog/ven/ven012/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est002GridAguardandoLiberacao':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções','callback' => 'campoOpcoes', 'style' => 'width:30px; padding: 3px 1px 3px 10px;' ),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true ),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente' ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e',if(T005_Flag_ACP='8','NFC-e',if(T005_Flag_ACP='4','CUPOM FISCAL','<font color=red>FALTA</font>')))))))) Tipo_Documento", 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação' ),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir'=>false),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'D006_Descricao_Comercial','colunaExibir' => false, 'colunaTitulo' => 'CFOP' ), 
			array('colunaValor' => 'D006_Codigo_CFOP','colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou' ),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio','colunaExibir'=>false),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'IdRepresentante', 'colunaExibir'=>false ),
			array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir'=>false ),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaExibir'=>false ),
			array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir'=>false ),
			array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Prazos(T005_Id) Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData', 'colunaExibir'=>false),
			array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir'=>false ),
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
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
			where (T005_Flag_Almoxarifado='S'
			and T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='6')
			GROUP BY T005_Id
			ORDER BY T005_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad001Incluir">Etiqueta Avulsa</button>
				
			</div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
	
		/*
		* Botões (Versão Acima do GRID)
		
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	
	case 'est002GridImpressao':
	$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => "T005_Flag_Marcado", 'colunaTitulo'=>'', 'callback' => 'validaCampoDuplicar', 'style'=>'width:1%;', 'colunaExibir' => true, 'colunaEditarTodosTriggerPHP' => false, 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
			array('colunaValor' => 'campoOpcoesImpressao1', 'colunaTipo' => 'livre','callback' => 'campoOpcoesImpressao1', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaTitulo' => 'IP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'campoOpcoesImpressao2', 'colunaTipo' => 'livre','callback' => 'campoOpcoesImpressao2', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaTitulo' => 'RP', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'campoOpcoesImpressao3', 'colunaTipo' => 'livre','callback' => 'campoOpcoesImpressao3', 'style' => 'width:30px; padding: 3px 1px 3px 10px;'),
			array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/pedido/', 'T005_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;', 'colunaTitulo' => 'IE','colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true ),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente' ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e',if(T005_Flag_ACP='8','NFC-e',if(T005_Flag_ACP='4','CUPOM FISCAL','<font color=red>FALTA</font>')))))))) as Tipo_Documento", 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);' ),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir'=>false),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'D006_Descricao_Comercial','colunaExibir' => false, 'colunaTitulo' => 'CFOP' ), 
			array('colunaValor' => 'D006_Codigo_CFOP','colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData'),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
										FROM T006 
										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
										WHERE T006_T005_Id=T005_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
			array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou' ),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor' ),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio','colunaExibir'=>false),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir'=>false),
			array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir'=>false),
			array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir'=>false ),
			array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir'=>false ),
			array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir'=>false ),
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
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
				where (T005_C004_Id={$g['empresaAtual']}
				and T005_Flag_Status='1')
				group by T005_Id 
				order by T005_Id
EOT;

        $extraTotal = <<<EOT
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
				where (T005_C004_Id={$g['empresaAtual']}
				and T005_Flag_Status='1')
EOT;

		//Botões
		$botoes = array(
			array('titulo' => 'Imprimir', 'id' => 'est002ImprimirPedido'),
			array('titulo' => 'Mobile', 'id' => 'est002AbrirSeparacaoCelular', 'janelaUrl' => '/hardness3/mobile/tablet_separacao/qr-login.php', 'janelaTamanho' => '[500,500]'),
		);
		echo Botoes::processar($botoes);	

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
										FROM T006 
										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
										WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto','exibir' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			if ($('#{$g['divId']}').data('timeout') != 1) {
			setInterval(function() {
				 divRefresh('{$g['divId']}');
			}, 60000);
			$('#{$g['divId']}').data('timeout', 1);
			}
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
		
		$novoIdTelaForm = uniqid();
		
		if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		}

		$ajaxExtra = base64_encode($extra);
 		
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) 
		{
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002ImprimirPedido').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();

					$.ajax({
						url: '/est/est002/grid_func-ajax/checaPedidosSelecionados/',
						type: 'POST',
						dataType: 'jsonp',
						data: {
							'ajax': 'true',
							'ajaxExtra': '{$ajaxExtra}'
						}
					}).done(function(data, textStatus, jqXHR){
						if(data.data.contagem > 0)
						{
							var buttons = {
								"Sim": function(){
									abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir pedidos', '/est/est002/content/est002ImprimirPedido/',"&selectT005=" + data.data.pedidos, [450,170]);
									$(this).dialog("destroy").remove();
								},
								"Não": function(){
									$(this).dialog("destroy").remove();
								}
							};
							dialogConfirm('Impressão pedidos', "Confirma impressão de <b style='color:red'>" + data.data.contagem + "</b> pedidos?", buttons);
						}
						else
						{
							dialogConfirm('Impressão pedidos', 'Nenhum pedido selecionado!');
						}
					});
					$('#{$g['divId']}').hideLoading();
                });
			} </script></div>
EOT;
        }


		 /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
		
		/*if (!personalizacaoTotais(false, true)) { 
			$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(distinct T005_Id)",
		        	        	                       $from,
		        	            	                   $extraTotal);

			list($numeroRegistros) = $sqlTotal;

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$numeroRegistros}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();*/

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
	
	case 'est002GridSeparacao':
		$grid = array(
            array('colunaValor' => 'campoOpcoesSeparacao1', 'colunaTipo' => 'livre', 'colunaTitulo' => 'SP','colunaAlinhar' => 'center', 'callback' => 'campoOpcoesSeparacao1', 'style' => 'width:30px; padding: 3px 1px 3px 10px;'),
            array('colunaValor' => 'campoOpcoesSeparacao2', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesSeparacao2', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaTitulo' => 'RP','colunaAlinhar' => 'center',),
            array('colunaValor' => 'campoRetornarExpedicao', 'colunaTipo' => 'livre', 'callback' => 'campoRetornarExpedicao', 'style' => 'width:30px; padding: 3px 1px 3px 10px; background-color: #ececec;', 'colunaTitulo' => 'RE','colunaAlinhar' => 'center', 'colunaExibirNivel' => 2, 'colunaExibirCallbackNivel' => 2),
            array('colunaValor' => 'campoOpcoesSeparacao3', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesSeparacao3', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaTitulo' => 'IP','colunaAlinhar' => 'center',),
			array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/pedido/', 'T005_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;', 'colunaTitulo' => 'IE','colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
            array('colunaValor' => 'campoOpcoesSeparacao', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesSeparacao', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Entrega', 'colunaTitulo' => 'Prazo entrega', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'if(T005_Flag_ACP=\'1\',\'NOTA FISCAL\',if(T005_Flag_ACP=\'3\',\'ACP\',if(T005_Flag_ACP=\'5\',\'NOTA D1\',if(T005_Flag_ACP=\'2\',\'NÃO FATURAR\',if(T005_Flag_ACP=\'6\',\'NF-e\',if(T005_Flag_ACP=\'7\',\'NFS-e\',if(T005_Flag_ACP=\'8\',\'NFC-e\',if(T005_Flag_ACP=\'4\',\'CUPOM FISCAL\',\'<font color=red>FALTA</font>\')))))))) as Tipo_Documento', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);max-width:200px;white-space:normal;'),
            array('colunaValor' => 'T005_D022_Id as Transportadora', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'D006_Descricao_Comercial', 'colunaExibir' => false, 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                  										FROM T006 
                  										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                  										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                  										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                  										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                  										WHERE T006_T005_Id=T005_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
            array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir' => false),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T006 WHERE T006_Flag_Divergencia = \'S\' AND T006_T005_Id=T005_Id) > 0, \'Sim\', \'Não\') as Divergencia', 'colunaTitulo' => 'Divergência', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
				left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where (T005_C004_Id={$g['empresaAtual']}
				and T005_Flag_Status='2')
				group by T005_Id 
				order by T005_Id
EOT;

		$extraTotal = <<<EOT
				left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where (T005_C004_Id={$g['empresaAtual']}
				and T005_Flag_Status='2')
EOT;

		/**
         * Botões Almir
         */
		$botoes = array(
			array('titulo' => 'Iniciar Conferencia', 'id' => 'est002LerCodigoBarrasBtn'),
		);

		echo Botoes::processar($botoes);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002LerCodigoBarrasBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Informe o numero do Box', '/est/est002/content/est002ContentIdSeparacaoBox/', '', [300,85]);
			});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                                  FROM T006 
                                                  LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['Divergencia']=='Sim'){
				return 'linhaVermelho';
			}
		}

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
		
		
		if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		}
		
		/**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
        
		/*if (!personalizacaoTotais(false, true)) { 
			$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(distinct T005_Id)",
		        	        	                       $from,
		        	            	                   $extraTotal);

			list($numeroRegistros) = $sqlTotal;

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$numeroRegistros}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();*/

		
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
	
	case 'est002GridConferencia':
		$grid = array(
            array('colunaValor' => 'campoOpcoesConferencia1', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesConferencia1', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'CP',  'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoOpcoesConferencia2', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesConferencia2', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'RP',  'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoOpcoesConferencia3', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesConferencia3', 'style' => 'width:30px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'IP',  'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoRetornarExpedicao', 'colunaTipo' => 'livre', 'callback' => 'campoRetornarExpedicao', 'style' => 'width:30px; padding: 3px 10px 3px 10px; background-color: #ececec;', 'colunaTitulo' => 'RE',  'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2, 'colunaExibirCallbackNivel' => 2),
            array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false, 'colunaTitulo' => 'AS',  'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'color:blue;min-width:150px;max-width:150px;white-space:bold;'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Entrega', 'colunaTitulo' => 'Prazo entrega', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'if(T005_Flag_ACP=\'1\',\'NOTA FISCAL\',if(T005_Flag_ACP=\'3\',\'ACP\',if(T005_Flag_ACP=\'5\',\'NOTA D1\',if(T005_Flag_ACP=\'2\',\'NÃO FATURAR\',if(T005_Flag_ACP=\'6\',\'NF-e\',if(T005_Flag_ACP=\'7\',\'NFS-e\',if(T005_Flag_ACP=\'8\',\'NFC-e\',if(T005_Flag_ACP=\'4\',\'CUPOM FISCAL\',\'<font color=red>FALTA</font>\')))))))) as Tipo_Documento', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);max-width:200px;white-space:normal;'),
            array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'D006_Descricao_Comercial', 'colunaExibir' => false, 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
        										FROM T006 
        										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
        										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
        										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
        										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
        										WHERE T006_T005_Id=T005_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir' => false),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir' => 's'),
            array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT count(T006_Id) FROM T006 WHERE T006_T005_Id=T005_Id AND T006_Flag_Divergencia="S") as TemDiv', 'colunaTitulo' => 'TemDiv', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
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
			left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
			where (T005_C004_Id='{$g['empresaAtual']}'
			and T005_Flag_Status='10')
			group by T005_Id 
			order by T005_Id
EOT;

		$extraTotal = <<<EOT
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
			left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
			where (T005_C004_Id='{$g['empresaAtual']}'
			and T005_Flag_Status='10')
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
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                                  FROM T006 
                                                  LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['TemDiv'] > 0) {
				return 'linhaVermelho';
			}
		}
		
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
		
		
		if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		}

		 /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
       /* if (!personalizacaoTotais(false, true)) { 
			$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(distinct T005_Id)",
		        	        	                       $from,
		        	            	                   $extraTotal);

			list($numeroRegistros) = $sqlTotal;

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$numeroRegistros}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();*/
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
	
	case 'est002GridEmissaoNF':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			//array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções','callback' => 'campoOpcoesEmissaoNF' ),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente' ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e',if(T005_Flag_ACP='8','NFC-e',if(T005_Flag_ACP='4','CUPOM FISCAL','<font color=red>FALTA</font>')))))))) Tipo_Documento", 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação' ),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora' ),
			array('colunaValor' => 'D006_Descricao_Comercial','colunaExibir' => false, 'colunaTitulo' => 'CFOP' ), 
			array('colunaValor' => 'D006_Codigo_CFOP','colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou' ),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor' ),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir'=>false, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio','colunaExibir'=>false),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'IdRepresentante', 'colunaExibir'=>false ),
			array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir'=>false ),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaExibir'=>false ),
			array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir'=>false ),
			array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Prazos(T005_Id) Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir'=>false ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData', 'colunaExibir'=>false),
			array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir'=>false ),
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
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
			where (T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='3')
			group by T005_Id 
			order by T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est002GridAguardandoEntrega':
		global $g;

		$exibirCorreios = false;
		if(!(empty($g['C004']['C004_Codigo_Agencia_Correios'])) && !(empty($g['C004']['C004_Login_Portal_Postal'])) && !(empty($g['C004']['C004_Senha_Portal_Postal']))){
			$exibirCorreios = true;
		}

		$habilitaMDFe = false;
		if(isset($g['c029Ids'][242])){
			$habilitaMDFe = true;
		}

		$grid = array(
            array('colunaValor' => 'campoOpcoesAguardandoEntrega1', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesAguardandoEntrega1', 'style' => 'width:18px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'CE', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'retornarPedidoViaCupom', 'colunaTitulo' => 'CF', 'colunaTipo' => 'livre', 'callback' => 'retornarPedidoViaCupom', 'style' => 'width:18px; padding: 3px 10px 3px 10px;'),
            array('colunaValor' => 'campoRetornarExpedicao', 'colunaTipo' => 'livre', 'callback' => 'campoRetornarExpedicao', 'style' => 'width:18px; padding: 3px 10px 3px 10px; background-color: #ececec;', 'colunaTitulo' => 'RE', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2, 'colunaExibirCallbackNivel' => 2),
            array('colunaValor' => "T007_Flag_Imprimir_Romaneio", 'colunaTitulo'=>'-', 'callback' => 'validaCampoCheckboxRomaneio', 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'colunaExibir' => true, 'colunaEditarTodosTriggerPHP' => false, 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'T284_T276_Id', 'colunaTitulo' => 'T284_T276_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'T276_Flag_Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Marcado', 'colunaTitulo' => 'MDF-e', 'callback' => 'validaFlagNotaMarcada', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'N|S','colunaExibir' => $habilitaMDFe,'colunaAlinhar' => 'center'),
            array('colunaValor' => 'campoOpcoesAguardandoEntrega2','colunaTitulo' => '', 'colunaTitulo' => 'Pedido','colunaTipo' => 'livre', 'callback' => 'campoOpcoesAguardandoEntrega2', 'style' => 'width:18px; padding: 3px 20px 3px 20px;'),
            array('colunaValor' => 'imprimirMinutaDeDespacho', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Minuta', 'callback' => 'imprimirMinutaDeDespacho', 'style' => 'width:30px; padding: 3px 10px 3px 20px;', 'colunaAlinhar' => 'center',),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/notafiscal/', 'T007_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 420), 'style' => 'width:18px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'IE', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'coletaEtiquetaCorreio', 'colunaTitulo' => 'Correio', 'colunaTipo' => 'livre', 'callback' => 'correioPostaEtiqueta', 'style' => 'width:18px; padding: 3px 17px;','colunaExibir' => $exibirCorreios, 'colunaAlinhar' => 'center'),
            array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções', 'callback' => 'campoOpcoesAguardandoEntrega', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T005_T275_Id', 'colunaTitulo' => 'Romaneio', 'callback' => 'retornaImpressaoRomaneio', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box'),
            array('colunaValor' => 'T007_Quantidade_Volumes', 'colunaTitulo' => 'Volumes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
        	array('colunaValor' => 'UFCliente.D018_UF', 'colunaTitulo' => 'UF','colunaExibir' => $habilitaMDFe),
        	array('colunaValor' => 'UFClienteEntrega.D018_UF', 'colunaTitulo' => 'UF Entrega', 'colunaAlinhar' => 'center','colunaExibir' => $habilitaMDFe),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Data_Entrega', 'colunaTitulo' => 'Prazo entrega', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'if(T005_Flag_ACP=\'1\',\'NOTA FISCAL\',if(T005_Flag_ACP=\'3\',\'ACP\',if(T005_Flag_ACP=\'5\',\'NOTA D1\',if(T005_Flag_ACP=\'2\',\'NÃO FATURAR\',if(T005_Flag_ACP=\'6\',\'NF-e\',if(T005_Flag_ACP=\'7\',\'NFS-e\',if(T005_Flag_ACP=\'4\',\'CUPOM FISCAL\',\'<font color=red>FALTA</font>\'))))))) as Tipo_Documento', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'T276_Numero_MDFe', 'colunaTitulo' => 'MDF-e', 'colunaAlinhar' => 'left','colunaExibir' => $habilitaMDFe, 'callback' => 'abrirJanela', 'callbackParameter' => array('Editar MDF-e', '/fis/fis012/content/fis012MdfeCadastro/', 'T284_T276_Id', '1000,583'), 'colunaExibir' => false),
            array('colunaValor' => 'T276_Flag_Cancelada', 'colunaTitulo' => 'T276_Flag_Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);max-width:200px;white-space:normal;'),
            array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'D006_Descricao_Comercial', 'colunaExibir' => false, 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
        										FROM T006 
        										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
        										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
        										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
        										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
        										WHERE T006_T005_Id=T005_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
            array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir' => false),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir' => false),
            array('colunaValor' => 'Conferiu.C007_Primeiro_Nome as Nome_Conferiu', 'colunaTitulo' => 'Conferiu', 'colunaExibir' => 's'),
            array('colunaValor' => 'T005_Flag_Expedicao', 'colunaTitulo' => 'T005_Flag_Expedicao', 'colunaExibir' => false),
            array('colunaValor' => 'sum(T006_Quantidade)', 'colunaTitulo' => 'Qtde', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo', 'colunaExibir' => false),
			array('colunaValor' => 'CCe', 'colunaTitulo' => 'CC-e', 'colunaTipo' => 'livre', 'callback' => 'enviarCCe', 'colunaExibir' => false),  			  
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			left join T006 on T005_Id=T006_T005_Id
			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
			left join D024 on D024_Id=T005_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 as UFCliente on UFCliente.D018_Id=D024_D018_Id
			left join D021 on D021_Id=T005_D021_Id
			left join D022 on D022_Id=T007_D022_Id
			left join D006 on D006_Id=T005_D006_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C004 on C004_Id=T005_C004_Id
			left join D036 on T005_D036_Id=D036_Id 
			left join D148 on D148_Id=T007_D148_Id_Entrega
            left join D018 as UFClienteEntrega on UFClienteEntrega.D018_Id=D148_D018_Id
			LEFT JOIN T268 ON T268_T007_Id=T007_Id
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
			left join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas
			left join C007 as Conferiu on Conferiu.C007_Id=T005_C007_Id_Conferiu
			left join T284 on T284_T007_Id=T007_Id
			left join T276 on T276_Id=T284_T276_Id
			where (T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='4')
			group by T005_Id 
			order by T005_Id
EOT;

		$extraTotal = <<<EOT
			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
			left join D024 on D024_Id=T005_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D021 on D021_Id=T005_D021_Id
			left join D022 on D022_Id=T007_D022_Id
			left join D006 on D006_Id=T005_D006_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C004 on C004_Id=T005_C004_Id
			left join D036 on T005_D036_Id=D036_Id 
			LEFT JOIN T268 ON T268_T007_Id=T007_Id
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
			left join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas
			left join C007 as Conferiu on Conferiu.C007_Id=T005_C007_Id_Conferiu
			where (T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='4')
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'UFCliente.D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'UFClienteEntrega.D018_UF' => array('titulo' => 'UF Entrega', 'tipo' => 'texto'),
            'T276_Numero_MDFe' => array('titulo' => 'Número MDF-e', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'T007_Flag_Marcado' => array('titulo' => 'Gerar MDFe', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              	),
        	),
            'Liberou.C007_Primeiro_Nome' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                        FROM T006 
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'T005_T275_Id' => array('titulo' => 'Romaneio', 'tipo' => 'texto'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Nota Fiscal', 'tipo' => 'numero'),			
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		$extra_ = base64_encode(gRetirarGroupBy(gRetirarOrderBy($extra)));

		/**
		* Botões (Versão Acima do GRID)
		*/
/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est002GridRomaneioImprimir">Gerar romaneio</button-->				
			
				<button type="button" id="est002GridEntregaPedidosSelecionados">Confirmar coleta</button>
EOT;

		if($exibirCorreios){
			echo <<<EOT
				<button type="button" id="est002GridPostarEtiquetaCorreio">Imprimir correio</button>
EOT;
		}
		if($habilitaMDFe){
			echo <<<EOT
				<button type="button" id="est002GerarMdfe">Gerar MDF-e</button>
EOT;
		}
*/
	

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est002GridEntregaPedidosSelecionados').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							idProgressBar = dialogAlert('Coletar pedidos',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
				            ven002ColetarPedidosLoop(idProgressBar,1); 
				            $(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Confirmar entrega', 'Confirma a coleta dos pedidos selecionados?? ', \$buttons);
				});

				$('#est002GerarMdfe').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fis/fis012/grid_func-ajax/gerarMDFeCabecalho/?ajax=true&callback=?', function(request) {
                        if (request.code) {
                            $('#{$g['divId']}').hideLoading();
                            var T276_Id=request.T276_Id;
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar MDF-e - ID: ' + T276_Id, '/fis/fis012/content/fis012MdfeCadastro/', '&tab=geral&acaoId=' + encodeURIComponent(T276_Id), [1000,550]);
                            divRefresh('{$g['divIdParent']}', true);
                            divRefresh('{$g['divId']}', true);
                        } else {
                            $('#{$g['divId']}').hideLoading();
                            dialogConfirm('Erro', request.data);
                        }                                           
                    });
                });

				function ven002ColetarPedidosLoop(idProgressBar, inicio){
                	$.getJSON('/est/est002/grid_func-ajax/confirmarEntregaVariosPedidos/?ajax=true&extra=' + encodeURIComponent('{$extra_}') + '&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {

	                    	if(request.erroIntegracao){
	                    		$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos coletados: <b>' + request.ok + '</b><br/>Pedidos não coletados: <b> ' + request.erro + '</b><br/><br/><b><font color="red"> ' + request.erroSistema + '</font></br>Os pedidos abaixo<font color="blue"> foram coletados</font>, mas houveram erros ao enviar os dados de faturamento.<br/></br></b>' + request.erroIntegracao);
	                    	}else{
	                    		$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + 'Pedidos coletados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos coletados: <b>' + request.ok + '</b><br/>Pedidos não coletados: <b> ' + request.erro + '</b><br/><br/><font color="red"><b>' + request.erroSistema + '</b></font>');
	                    	}
	                    	ven002ColetarPedidosLoop(idProgressBar,0);
	                    }else{
	                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                    	
	                    	if(request.erroIntegracao){
	                    		dialogConfirm('Pedidos coletados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos coletados: <b>' + request.ok + '</b><br/>Pedidos não coletados: <b> ' + request.erro + '</b><br/><br/><b><font color="red"> ' + request.erroSistema + '</font></br>Os pedidos abaixo<font color="blue"> foram coletados</font>, mas houveram erros ao enviar os dados de faturamento.<br/></br></b>' + request.erroIntegracao);
	                    	}else{
	                    		dialogConfirm('Pedidos coletados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos coletados: <b>' + request.ok + '</b><br/>Pedidos não coletados: <b> ' + request.erro + '</b><br/><br/><font color="red"><b>' + request.erroSistema + '</b></font>');
	                    	}

	                    	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                			$(this).dialog("destroy").remove();     
	                        } 
	                    } 
	                });	                  
		        }

				$('#est002GridRomaneioImprimir').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
						    $.getJSON('/est/est002/grid_func-ajax/gerarNumeroRomaneio/?ajax=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { 
										$(this).dialog("destroy").remove(); 
										nova_janela('/est/est002/outro/est002ImprimirRomaneioDeColeta/?ajax=true&T275_Id=' + encodeURIComponent(request.data) + '&extra=' + encodeURIComponent('{$extra_}') ,0,0,850,700,'yes','no','no','no','yes','no','Impressao_Romaneio');
									} }
						    		dialogConfirm('Ok', 'Número de romaneio gerado com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Gerar romaneio', 'Gerar número de romaneio de coleta?  ', \$buttons);
				});

				$('#est002GridPostarEtiquetaCorreio').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b> Processando 0% </b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            est002GridPostarEtiquetaCorreioLoop(idProgressBar,1);
					            $(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Correio', 'Confirmar a postagem e impressão das etiquetas dos pedidos selecionados?<br><br><font color="red"><b>**Pedidos com código de rastreio preenchido não serão postados novamente.</b></font>', \$buttons);
				});

				function est002GridPostarEtiquetaCorreioLoop(idProgressBar, inicio){
                	$.getJSON('/est/est002/grid_func-ajax/postarEtiquetasCorreiosPedidosMarcados/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {
	                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos postado: <b>' + request.ok + '</b><br/>Pedidos com código de rastreio: <b>' + request.possuiCodigoRastreio + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br/>Pedidos com transportadora errada: <b> ' + request.transportadoraErrada + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                    	est002GridPostarEtiquetaCorreioLoop(idProgressBar,0);
	                    }else{
	                    	var ok = request.ok;
	                    	var percentual = request.percentual;
	                    	var erro = request.erro;
	                    	var erros = request.erros;
	                    	var transportadoraErrada = request.transportadoraErrada;
	                    	var codigosRastreio = request.codigosRastreio;
	                    	var possuiCodigoRastreio = request.possuiCodigoRastreio;

	                    	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                			$.getJSON('/est/est002/grid_func-ajax/gerarEtiquetasCorreio/?ajax=true&codigosRastreio=' + encodeURIComponent(codigosRastreio) + '&callback=?', function(request) {
									dialogConfirm('Pedidos atualizados','<b>Processado '+ percentual + '%</b>' + '</b><br/><br/>Pedidos postados: <b>' + ok + '</b><br/>Pedidos com código de rastreio: <b>' + possuiCodigoRastreio + '</b><br/>Pedidos com erro: <b> ' + erro + '</b><br/>Pedidos com transportadora errada: <b> ' + transportadoraErrada + '</b><br /><br/><b> <font color="red">' + erros + '</font></b>' + '</b><br/>' + request.retornoEtiquetas);
	                    			$('#dc-' + idProgressBar).dialog("destroy").remove();				                
	                    		}); 
	                        } 
	                    } 
	                });	                  
		        }

			} </script></div>
EOT;
        }


		// Modificações no extra para suportar um SELECT T007 em vez de T005
		$modExtra = str_replace("left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'", "", $extra);
		$modExtra = "left join T005 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'" . $modExtra;
		$modExtra = gRetirarGroupBy($modExtra);
		$modExtra = gRetirarOrderBy($modExtra);

		$filtroExtra_ = base64_encode($modExtra);

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
		
		// Botões
		$botoes = array(
			array('titulo' => 'Gerar romaneio', 'id' => 'est002GridRomaneioImprimir'),
			array('titulo' => 'Confirmar coleta', 'id' => 'est002GridEntregaPedidosSelecionados'),
			array('titulo' => 'Imprimir correio', 'id' => 'est002GridPostarEtiquetaCorreio', 'exibirCallback' => "return $exibirCorreios;"),
			array('titulo' => 'Gerar MDF-e', 'id' => 'est002GerarMdfe', 'exibirCallback' => 'return isset($g["c029Ids"][242]);'),
			array('titulo' => 'Imprimir nota fiscal', 'id' => 'est002BtnImprimirNotaFiscal')
		);
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		$novoIdTelaForm = uniqid();
		
		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais(false, true)) { 
			$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(distinct T005_Id)",
		        	        	                       $from,
		        	            	                   $extraTotal);

			list($numeroRegistros) = $sqlTotal;

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$numeroRegistros}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();*/
		
/* 		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T005_Id = id[0].replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + T005_Id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [700,500]);
			});
		} </script></div>
EOT; */

		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + array['todosCampos']['T005_Id'], '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T005_Id']) + '&tabela=T005_Id', [700,500]);
				});
			} </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) 
		{
			$flagType = base64_encode("T007_Flag_Imprimir_Romaneio = 'S'");

            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002BtnImprimirNotaFiscal').unbind('click').bind('click', function(data) {
					nova_janela('/ven/ven003/grid_func-ajax/ven003ImprimirMarcadas/?ajax=true&flagType={$flagType}&extra=' + encodeURIComponent('{$filtroExtra_}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao Danfe');
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
	
	case 'est002GridEntregaInterna':
		$grid = array(
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido', 'style'=>'width:35px;', 'colunaAlinhar' => 'center' , 'chavePrimaria' => true),
			array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaAlinhar' => 'center'),			
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte','colunaCasasDecimais' => 0, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado','colunaCasasDecimais' => 0, 'colunaAlinhar' => 'center', 'style'=>'width:50px;'),
			array('colunaValor' => 'est002Entregue','colunaTipo'=>'livre', 'colunaTitulo' => 'Entregue', 'callback'=>'calculaQteEntregue', 'colunaAlinhar' => 'center', 'style'=>'width:50px;'),
			array('colunaValor' => 'T006_Quantidade_Entregar_Temp', 'colunaTitulo' => 'Entregar', 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T006_Quantidade_Entregar_Temp", 'style'=>'width:50px;'),
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T006";
		
		$extra = <<<EOT
			left join T005 on T006_T005_Id=T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join T147 on T147_T005_Id=T005_Id            
			left join D116 on T147_D116_Id=D116_Id
			where (T006_T006_Id is null or T006_T006_Id<=0)
			and (T006_Flag_Divergencia!='S')
			AND ( (T006_Quantidade_Separacao > (SELECT SUM(T179_Quantidade_Entregue) FROM T179 WHERE T179_T006_Id=T006_Id)) OR ((SELECT SUM(T179_Quantidade_Entregue) FROM T179 WHERE T179_T006_Id=T006_Id) IS NULL))
			GROUP BY T006_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est002GridEntregaInterna">Concluir Entrega</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est002GridEntregaInterna').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Confirmar Entrega', '/est/est002/content/est002ContentEntregaInterna/', '', [650,450]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T006_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'D116_Placa' => array('titulo' => 'Veículo', 'tipo' => 'texto'),
			//'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			//'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			//'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			//'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
echo <<<EOT
   <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		 $('#{$g['divId']} .grid').css('width', '1%');
	} </script></div>
EOT;
				
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);	
		
		$novoIdTelaForm = uniqid();
		
		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			
		$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
			var id = $(this).closest('tr').attr('id').split('|');
			var acaoId = id[1].replace(/T006_T005_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entregas do pedido - ID: '+acaoId, '/est/est002/content/est002ContentHistoricoEntregaInterna/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela={$from}', [650,450]);
		});
		$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
								
			// Função JS executada como callback do editar campo (colunaEditarJSCallback)
			function colunaEditarCallbackJS_T006_Quantidade_Entregar_Temp(T006_Id, campoEditado, valorAntigo, novoValor) {
				T006_Id = T006_Id.replace(/T006_Id-/, '');
				if (campoEditado == 'T006_Quantidade_Entregar_Temp') {
					$.getJSON('/est/est002/grid_func-ajax/verificarQteEntrega/?ajax=true&T006_Id='     		+ encodeURIComponent(T006_Id) + 
																					  '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					  '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					  '&novoValor='			+ encodeURIComponent(novoValor) +  
																					  '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
							//alert("NFe Excluída");
						} else {
							dialogConfirm('Erro', request.data);
							divRefresh('{$g['divId']}');
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
	
	case 'est002GridProdutosEntregaInterna':
		$grid = array(
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido'),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto' ),
			array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo'),			
			array('colunaValor' => 'T006_Quantidade_Entregar_Temp', 'colunaTitulo' => 'Quantidade'),
			 
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T006";
		
		$extra = <<<EOT
			left join T005 on T006_T005_Id=T005_Id
			left join T147 on T147_T005_Id=T005_Id            
			left join D116 on T147_D116_Id=D116_Id
			where (T006_T006_Id is null or T006_T006_Id<=0)
			and (T005_Flag_Status=10)
			and (T006_Flag_Divergencia!='S')
			and (T006_Quantidade_Entregar_Temp>0)
			order by 
			T006_T005_Id
EOT;
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
	
	case 'est002GridHistoricoEntregaInterna':
		$grid = array(			
			array('colunaValor' => 'T179_Id','chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T179_Data', 'colunaTitulo' => 'Data e Hora','callback'=>'gCorrigeData'),
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto' ), 			
			array('colunaValor' => 'C007_Login', 'colunaTitulo' => 'Entregador'),
			array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Retirante'),					
			array('colunaValor' => 'T179_Quantidade_Entregue', 'colunaTitulo' => 'Qte'),
			 
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T179";
		
		$extra = <<<EOT
			left join T006 on T179_T006_Id=T006_Id
			left join T005 on T006_T005_Id=T005_Id                       
			left join C007 on T179_C007_Id_Entregou=C007_Id            
			left join D109 on T179_D109_Id_Recebeu=D109_Id
			where T005_Id={$r_acaoId}
			order by T179_Data DESC
EOT;

		
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
	
	case 'est002GridProdutosEmSeparacao':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			//array('colunaValor'=>'campoImpTag', 'colunaTitulo'=>'-', 'colunaAlinhar' => 'center', 'style' => 'width: 1%', 'callback' => 'campoImpTag', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
			array('colunaValor' => 'T185_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
			array('colunaValor' => 'T185_Qtd_Para_Separar', 'colunaTitulo' => 'Qte','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq'),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id) as OC', 'colunaTitulo' => 'OC', 'callback' => 'campoOC'),
			array('colunaValor' => 'T185_Qtd_Separado', 'colunaTitulo' => 'Separado' ,'colunaCasasDecimais' => 2, 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Flag_Divergencia', 'colunaTitulo' => 'T006_Flag_Divergencia', 'colunaExibir' => false),
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
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			left join T066 ON T066_Id=T185_T066_Id
			left join D004 ON D004_Id=T066_D004_Id
			WHERE (T006_T005_Id = '{$r_acaoId}' AND T185_Qtd_Para_Separar != 0)
EOT;
		// AND T006_Flag_Divergencia = 'N' AND (T185_Qtd_Separado IS NULL OR T185_Qtd_Separado != 0 OR T006_Quantidade_Separacao != 0)

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
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['T006_Flag_Divergencia'] == 'S') {
				return 'linhaVermelho';
			}
		}
		
		/*
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
		
	case 'est002GridSepararProdutos':

		$idBox = $_REQUEST['idBox'];

		$grid = array(
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center',),
			array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'Item', 'colunaAlinhar' => 'center',),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Codigo do Produto', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição do produto', 'colunaAlinhar' => 'center',),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde Pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Qtde Separada', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "";

		$extra = <<<EOT
			 (
				(select
					t006_t005_id,
					t006_item,
					d001_codigo_produto,
					t006_descricao_produto,
					t006_quantidade,
					coalesce(t006_quantidade_separacao,0) as t006_quantidade_separacao,
					'T006' as tipo_registro
				from T006   
				join t005 on t006_t005_id = t005_id
				join d009 on d009_id = t006_d009_id
				join d049 on d049_id = d009_d049_id
				join d001 on d049_d001_id = d001_id     
				where (t006_t006_id is null or t006_t006_id <= 0)
				and t006_quantidade > 0
				and t005_flag_status = '2'
				and t005_c004_id = {$g['empresaAtual']}
				and t005_estoque_box = '{$idBox}'
				and t005_c007_id_separou = {$g['usuarioAtual']})
				
				union all
				
				(select
					t006_t005_id,
					t006_item,
					d001_codigo_produto,
					d001_descricao_produto as t006_descricao_produto,
					t006_quantidade * d074_quantidade,
					coalesce(t311_quantidade_separada,0) as t006_quantidade_separacao,
					'D074' as tipo_registro
				from T006   
				join t005 on t006_t005_id = t005_id
				join d009 on d009_id = t006_d009_id
				join d049 on d049_id = d009_d049_id
				join D074 on d074_d001_Id = d049_d001_id
				join d001 on d001_id = d074_d001_id_composicao
				left join t311 on t311_d074_id = d074_id
				where (t006_t006_id is null or t006_t006_id <= 0)
				and t006_quantidade > 0
				and t005_flag_status = '2'
				and t005_c004_id = {$g['empresaAtual']}
				and t005_estoque_box = '{$idBox}'
				and t005_c007_id_separou = {$g['usuarioAtual']})
			) as temp_grid_table
			
			order by t006_t005_id, t006_item, tipo_registro desc, d001_codigo_produto
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T006_Quantidade_Separacao'] <> $array['todosCampos']['T006_Quantidade']){
				return 'linhaLaranja';
			}
		}

		/*
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
		if (isset($r_generateHTML)) {
			ob_end_clean();
			ob_start();
			$g['smarty']->display('gridHTML.tpl');
		} else {
			$g['smarty']->display('grid.tpl');
		}

	break;
	
	case 'est002GridSepararProdutosEtiqueta':

		$grid = array(
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NFE'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Data NF', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T007_Hora_Emissao', 'colunaTitulo' => 'Hora NF', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Id_Pedido_Externo', 'colunaTitulo' => 'Pedido Tray', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir' => false),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Marketplace'),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome', 'colunaTitulo' => 'Separou', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id NF', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NF', 'colunaExibir' => false),
			array('colunaValor' => 'ImpEtiqDANFE', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Danfe','callback' => 'imprimirEtiquetaDanfe'),
			array('colunaValor' => 'ImpEtiqTransp', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Etiqueta Transp','callback' => 'imprimirEtiquetaTransporte'),
			array('colunaValor' => 'EnviarXMLTray', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Enviar Tray','callback' => 'enviarDadosNFTray'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "T005";

		$extra = <<<EOT
			join T005A on T005A_T005_Id = T005_Id
			join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
			join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
			join D022 on D022_Id=T005_D022_Id
			where T005_C004_Id = {$g['empresaAtual']}
			and T005_C007_Id_Separou = {$g['usuarioAtual']}
			and T005A_Flag_Etiqueta_Transp_Impressa = 'N'

EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if(empty($array['todosCampos']['T007_Numero_Protocolo_Nfe'])){
				return 'linhaLaranja';
			}
		}

		/*
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$sqlEtiqueta  = " SELECT D175.D175_Nome ";
		$sqlEtiqueta .= "   FROM D175 ";
		$sqlEtiqueta .= "  WHERE D175.D175_Tipo_Impressora = 6 ";
		$sqlEtiqueta .= "  ORDER BY D175.D175_Id ASC ";
		$sqlEtiqueta .= "  LIMIT 1 ";
		$fetchEtiqueta = mysql_query($sqlEtiqueta);
		$dadosEtiqueta = mysql_fetch_array($fetchEtiqueta);
		$impressoraEtiqueta = $dadosEtiqueta['D175_Nome'];

		echo <<<EOT

		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			
			function enviarDadosTray_grid2(T007_Id) {

				$.getJSON('/ven/ven003/grid_func-ajax/ven003EnviarNFTray?ajax=true&T007_Id=' + T007_Id + '&callback=?', function(request) {
					if (request.sucesso) {
						alert('Dados enviados para tray');
						console.log(request.mensagem);
					} else {
						alert('Dados não enviados. Mensagem: ' + request.mensagem);
					}
				});
			}

			function impEtiquetaDanfe_grid2(T007_Id) {
				$('#{$g['divId']}').showLoading();
				$.ajax({
					url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
					type: 'GET',
					dataType: 'jsonp',
					data: {
						'ajax': 'true',
						'acaoId': '900af79255d13da53e5e04418e52a3c5',
						'link': '/sistema/personalizacao/tela/900af79255d13da53e5e04418e52a3c5/?ajax=true&acaoId=' + T007_Id,
					},
					success: function(resposta) {
						printSMT(resposta.data, '{$impressoraEtiqueta}');
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					}
				});
			}

			function impEtiquetaTransporte_grid2(T005_Id, T007_Id) {
				idProgressBar = dialogAlert('Buscando a etiqueta do pedido: ' + T005_Id, "<span id='progressBar'><b><br>Aguarde, isto pode demorar até 2 minutos!</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
				$.getJSON('/ven/ven012/form_func-ajax/getEtiquetaTransporteBoxLeitor?ajax=true&T005_Id=' + T005_Id + '&T007_Id=' + T007_Id + '&callback=?', function(request) {
					$('#dc-' + idProgressBar).dialog("destroy").remove();
					console.log(request.semCodRastreio, request);

					const tpEtiqueta = request.tpEtiqueta;
					switch (tpEtiqueta) {
						case 'ML':
							impEtiquetaTranspML_grid2(request.urlEtiquetaML, T005_Id, request.T007_Quantidade_Volumes, request.semEtiqueta);
							break;
						case 'PD':
							impEtiquetaTranspPadrao_grid2(T005_Id, request.T007_Quantidade_Volumes);
							break;
						default:
							impEtiquetaControleInterno_grid2(T007_Id, T005_Id);
					}
				});
			}

			function impEtiquetaTranspPadrao_grid2(T005_Id, T007_Quantidade_Volumes) {
			
				$.ajax({
					url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
					type: 'GET',
					dataType: 'jsonp',
					data: {
						'ajax': 'true',
						'acaoId': 'ab71e3720825c0214e7e11861a3e4f29',
						'link': '/sistema/personalizacao/tela/ab71e3720825c0214e7e11861a3e4f29/?ajax=true&T005_Id=' + T005_Id,
					},
					success: function(resposta) {
			
						execImpPad_grid2(1);
			
						function execImpPad_grid2(count) {
							if (count <= T007_Quantidade_Volumes) {
								setTimeout(function() {
									printSMT(resposta.data, '{$impressoraEtiqueta}');
									execImpPad_grid2(count + 1);
								}, 500);
							} else {
								setPedidoFlagEtiquetaTransporteImpressa_grid2(T005_Id);
							}
						}
					}
				});
			}

			function impEtiquetaTranspML_grid2(urlEtiquetaML, T005_Id, T007_Quantidade_Volumes, semEtiqueta) {
			
				execImpML_grid2(1);
			
				function execImpML_grid2(count) {
					if (count <= T007_Quantidade_Volumes) {
						setTimeout(function() {
							console.log(urlEtiquetaML);
							if (semEtiqueta == '') {
								printSMT(urlEtiquetaML, '{$impressoraEtiqueta}');
								execImpML_grid2(count + 1);
							} else {
								alert('Mercado Livre: Não foi encontrada a etiqueta no sistema do Mercado Livre');
							}
						}, 500);
					} else {
						if (semEtiqueta == '') {
							setPedidoFlagEtiquetaTransporteImpressa_grid2(T005_Id);
						}
					}
				}
			}

			function impEtiquetaControleInterno_grid2(T007_Id, T005_Id) {
				$.ajax({
					url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
					type: 'GET',
					dataType: 'jsonp',
					data: {
						'ajax': 'true',
						'acaoId': 'c505635934c183fe30c0f3fbd90662cd',
						'link': '/sistema/personalizacao/tela/c505635934c183fe30c0f3fbd90662cd/?ajax=true&T007_Id=' + T007_Id,
					},
					success: function(resposta) {
						// window.open(resposta.data, '_blank');
						printSMT(resposta.data, '{$impressoraEtiqueta}');
						setPedidoFlagEtiquetaTransporteImpressa_grid2(T005_Id);
					}
				});
			}
	
			function enviarDadosTray_grid2(T007_Id) {
			
				$.getJSON('/ven/ven003/grid_func-ajax/ven003EnviarNFTray?ajax=true&T007_Id=' + T007_Id + '&callback=?', function(request) {
					if (request.sucesso) {
						alert('Dados enviados para tray');
						console.log(request.mensagem);
					} else {
						alert('Dados não recebidos com sucesso na tray. Mensagem: ' + request.mensagem);
					}
				});
			}

			function setPedidoFlagEtiquetaTransporteImpressa_grid2(T005_Id) {
			
				$.getJSON('/est/est002/form_func-ajax/setPedidoFlagEtiquetaTransporteImpressa?ajax=true&T005_Id=' + T005_Id + '&callback=?', function(request) {
					console.log('Baixando status de impressao de etiqueta para o pedido ' + T005_Id);
					console.log(request);
					divRefresh('{$g['divId']}');
				});
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
		if (isset($r_generateHTML)) {
			ob_end_clean();
			ob_start();
			$g['smarty']->display('gridHTML.tpl');
		} else {
			$g['smarty']->display('grid.tpl');
		}

	break;

	case 'est002GridProdutosPedido':
	 	$grid = array(
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'T006_Id', 'right', false, 'T006', '<span class="ui-icon ui-icon-tag"></span>', 530), 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto','callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaConfCegaDescricao' => 'S'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte', 'colunaCasasDecimais' => '4', 'colunaConfCegaQtd' => 'S'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_Quantidade_OC(D009_Id) as OC', 'colunaTitulo' => 'OC', 'callback' => 'campoOC', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S'),
            array('colunaValor' => 'group_concat(distinct D083_Codigo_Barras separator "")', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Destino_Produto', 'colunaTitulo' => 'T006_Flag_Destino_Produto', 'colunaExibir' => false, 'colunaConfCegaFlag' => 'S'),
			array('colunaValor' => 'D001_Flag_Etiqueta_Pedido', 'colunaTitulo' => 'Etq.Pedido', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtd Embalagem', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'IF(D001_Flag_Etiqueta_Pedido="S",IF(D001_Qte_Embalagem>0 AND T006_Quantidade>D001_Qte_Embalagem,round(T006_Quantidade/D001_Qte_Embalagem),1),0)', 'colunaTitulo' => 'Qtd Etiqueta', 'colunaAlinhar' => 'center'),
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
            left join D083 on D083_D001_Id=D001_Id
            WHERE T006_T005_Id='{$r_acaoId}'
            AND (T006_T006_Id is null or T006_T006_Id<=0)
            AND T006_Quantidade > 0
            AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
            group by T006_Id
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

		// Botões
		$botoes = array(
		);
		echo Botoes::processar($botoes);
		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		/*
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est002GridProdutosPedidoConfirmarSeparacao':
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido'),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte', 'colunaConfCegaQtd' => 'S', 'colunaExibir' => true, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'T006_Quantidade_Separacao_Callback', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaConfCegaDescricao' => 'S'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_OC(D009_Id) as OC', 'colunaTitulo' => 'OC', 'callback' => 'campoOC', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S'),
            array('colunaValor' => 'group_concat(distinct D083_Codigo_Barras separator "")', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Destino_Produto', 'colunaTitulo' => 'T006_Flag_Destino_Produto', 'colunaExibir' => false, 'colunaConfCegaFlag' => 'S'),
        );

		/**
		 * Definição da SQL
		 */
		$from = "T006";
		
		$extra = <<<EOT
			left join D009 on D009_Id=T006_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D082 on D082_Id=D049_D082_Id
            left join T005 on T006_T005_Id=T005_Id
            left join D006 on D006_Id=T006_D006_Id
            left join D037 on D037_Id=D001_D037_Id
            left join D083 on D083_D001_Id=D001_Id
            where T006_T005_Id='{$r_acaoId}'
            and 
            (T006_T006_Id is null or T006_T006_Id<=0)
            and 
            (T006_Quantidade>0)
            group by T006_Id
            order by T006_Flag_Tipo_Produto,
            T006_D009_Id,
            T006_Id
EOT;


		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T006_Codigo_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'T006_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
			'T006_Quantidade_Separacao' => array('titulo' => 'Separado', 'tipo' => 'numero'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);


		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		/*
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
			});
		} </script></div>
EOT;
*/		
		echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	// desabilitar o refresh automatico deste grid
	$('#{$g['divId']} .grid').data('lastTimeUpdate', 'never');
	//
	function T006_Quantidade_Separacao_Callback(T006_Id, campoEditado, valorAntigo, novoValor) {
		T006_Id = T006_Id.replace(/T006_Id-/, '');
		// atualizar linha do grid
		divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id');
		// gravar alteração
		$.getJSON('/est/est002/grid_func-ajax/est002LogAlteracoesT006QuantidadeSeparacao/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + 
																							'&campoEditado=' +  encodeURIComponent(campoEditado) +
																							'&valorAntigo=' + encodeURIComponent(valorAntigo) +
																							'&novoValor=' + encodeURIComponent(novoValor) +  
																							'&local=' + encodeURIComponent('{$g['r_']}') +  
																							'&callback=?', function(request) {
			//
		});
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
	
	case 'est002GridEmprestimo':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T175_Id', $g['divId'])),
			array('colunaValor' => 'T175_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo'=>'livre', 'callback'=>'validaCampoConfirmarDevolucao'),
			array('colunaValor' => 'T175_C007_Id', 'colunaTitulo' => 'Retirante(Usuário)', 'colunaExibir'=>false),
			array('colunaValor' => 'T175_D109_Id', 'colunaTitulo' => 'Funcionário', 'colunaExibir'=>false),
			array('colunaValor' => 'Retirante', 'colunaTitulo' => 'Retirante', 'callback'=>'validaCampoRetirante', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T175_Quantidade', 'colunaTitulo' => 'Qtd'),
			array('colunaValor' => 'T175_D009_Id', 'colunaTitulo' => 'Produto', 'colunaExibir'=>false),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'T175_Data_Saida', 'colunaTitulo' => 'Saída', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T175_Data_Retorno', 'colunaTitulo' => 'Retorno', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T175_Flag_Operacao', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatusEmprestimo'),
			array('colunaValor' => 'T175_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
			array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Funcionário', 'style'=>'width:1%;'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T175";
		
		$extra = <<<EOT
		left join D009 on D009_Id=T175_D009_Id
		LEFT JOIN D049 on D049_Id=D009_D049_Id
		LEFT JOIN D001 on D001_Id=D049_D001_Id
		LEFT JOIN D109 on D109_Id=T175_D109_Id
		order by T175_Flag_Operacao
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est002ContentCadastroEmprestimo">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002ContentCadastroEmprestimo').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Empréstimo', '/est/est002/content/est002ContentCadastroEmprestimo/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T175_Quantidade' => array('titulo' => 'Quantidade', 'tipo' => 'numero'),
			'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'T175_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'T175_Data_Retorno' => array('titulo' => 'Retorno', 'tipo' => 'data'),
			//'T175_Flag_Operacao' => array('titulo' => 'Operacao(E/D)', 'tipo' => 'texto'),
			'T175_Flag_Operacao' => array('titulo' => 'Operacao', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Empréstimo', 'value' => 'E'),
				array('title' => 'Devolução', 'value' => 'D'),
			), 'naoGerarExtra' => false),
			'T175_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'D109_Nome' => array('titulo' => 'Funcionário', 'tipo' => 'texto'),
			//'Retirante' => array('titulo' => 'Retirante', 'tipo' => 'texto'),
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
		 * Totalizadores
		 */
		$extraTotalE = " WHERE T175_Flag_Operacao='E'";
		$extraTotalD = " WHERE T175_Flag_Operacao='D'";
		$emprestado = $g['sqlAuto']->pegarSqlCampo("SUM(T175_Quantidade)", $from, $extraTotalE);
		$devolvido = $g['sqlAuto']->pegarSqlCampo("SUM(T175_Quantidade)", $from, $extraTotalD);
		$saldo = ($emprestado - $devolvido);
		//$valorTotalPendentes = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T005_Valor_Total)", $from, $extraTotal));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Emprestado<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$emprestado}" readonly></span>
				<span class="item" style="text-align:right">Devolvido<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$devolvido}" readonly></span>
				<span class="item" style="text-align:right">Saldo<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$saldo}" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est002GridEtiquetas':
	$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true ),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto' ),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição' ),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
			array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoque','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte Pedido','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_Quantidade_Itens_Etiqueta', 'colunaTitulo' => 'Quantidade Itens','colunaCasasDecimais' => 2,'colunaEditar' => true),
			array('colunaValor' => 'T006_Quantidade_Etiqueta', 'colunaTitulo' => 'Quantidade por Etiqueta','colunaCasasDecimais' => 2,'colunaEditar' => true),
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
			left join D005 on D005_Id=D009_D005_Id(D009_Id)
			where T006_T005_Id='{$r_acaoId}'
			and (T006_T006_Id is null or T006_T006_Id<=0)
			and (T006_Quantidade>0)
			and (T006_Flag_Status!=3)
			and (T006_Flag_Tipo_Produto='AA' or T006_Flag_Tipo_Produto='' or T006_Flag_Tipo_Produto is null)
			order by (select group_concat(D004_Local) from T066 left join D004 on D004_Id=T066_D004_Id where T066_D009_Id=D009_Id),T006_D009_Id
EOT;

		/*
		* Filtro
		
		$gridFiltro = array(
			'' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		*/
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est002GridGeral':
		$grid = array(
            array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções', 'callback' => 'campoOpcoesGeral', 'style' => 'width:40px;'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'style' => 'max-width:200px;white-space:normal;'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'max-width:200px;white-space:normal;'),
            array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);max-width:200px;white-space:normal;'),
            array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D022_Nome_Empresa, " - (", D022_DDD_Telefone_1, ") ", D022_Telefone_1) as Transportadora', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
        										FROM T006 
        										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
        										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
        										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
        										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
        										WHERE T006_T005_Id=T005_Id) as Tipos', 'colunaTitulo' => 'Tipos'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NFe', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir' => false),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Responsável'),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Coleta', 'colunaTitulo' => 'Coleta', 'callback' => 'comboColeta'),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
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
			where T005_C004_Id={$g['empresaAtual']} AND (T005_Flag_Status IN(1,2,3,4,10,23))
			group by T005_Id 
			order by T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                                  FROM T006 
                                                  LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T005_Flag_Coleta' => array('titulo' => 'Coleta', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),
            'T005_Status_Pedido(T005_Flag_Status,1)' => array('titulo' => 'Status', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			if ($('#{$g['divId']}').data('timeout') != 1) {
			setInterval(function() {
				 divRefresh('{$g['divId']}');
			}, 60000);
			$('#{$g['divId']}').data('timeout', 1);
			}
		} </script></div>
EOT;
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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

	case 'est002GridControleProduto':
			
		 /**
		 * Definição do Grid (Tabela)
		 */			
		$grid = array(									
			array('colunaValor' => "T207_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;', 'callback' => 'gCorrigeData'),			
			array('colunaValor' => "T207_T005_Id", 'colunaTitulo' => 'Pedido', 'style' => 'width: 1px;'),
			array('colunaValor' => "count(T207_D009_Id) as D009_Id", 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'center', 'style' => 'width: 1px;'),
			array('colunaValor' => "D001_Descricao_Produto", 'colunaTitulo' => 'Produto'),			
			array('colunaValor' => "C007_Primeiro_Nome", 'colunaTitulo' => 'Responsável'),						
			array('colunaValor' => "T207_Flag_Status", 'colunaTitulo' => 'Status', 'callback' => "statusControleProduto"),
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'T207';
		$extra = <<<EOT
			left join C007 on T207_C007_Id=C007_Id
			left join D009 on T207_D009_Id=D009_Id
			left join D049 on D009_D049_Id=D049_Id
			left join D001 on D049_D001_Id=D001_Id
			WHERE D009_C004_Id='{$g['empresaAtual']}'
			GROUP BY T207_Flag_Status, T207_D009_Id
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T207_Flag_Status']=='2'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T207_Flag_Status']=='5'){
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(			
			'C007_Primeiro_Nome' => array('titulo' => 'Responsável', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'T207_T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'texto'),
			'T207_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T207_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
				array('title' => 'TODOS', 'value' => ''),
				array('title' => 'NÃO ENTREGUE', 'value' => '1'),
				array('title' => 'ENTREGUE', 'value' => '2',),
				array('title' => 'AGUARDANDO OS', 'value' => '3'),
				array('title' => 'DEVOLVIDO', 'value' => '4'),
				array('title' => 'CANCELADO', 'value' => '5'),
			)),
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

	case 'est002GridSeparacaoProduto':
		/**
		* Definição do Grid (Tabela)
		*/			
		$grid = array(									
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
			array('colunaValor' => 'T006_T206_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtd', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T006_Hora_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado','colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'colunaExibir' => false), 
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'T006';
		$extra = <<<EOT
			left join T005 on T006_T005_Id=T005_Id
			left join D009 on T006_D009_Id=D009_Id
			left join D049 on D009_D049_Id=D049_Id
			left join D001 on D049_D001_Id=D001_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D037 on D001_D037_Id=D037_Id
			WHERE T005_C004_Id='{$g['empresaAtual']}' AND T005_Flag_Status = '2'
			GROUP BY T006_Id
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(			
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
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

	case 'est002GridFaturamentoPedido':

		// --------------------------------------------------------------------------------------------------------
		// Data: 29/08/2018
		// Programador: OSNI
		// Método: est002GridFaturamentoPedido
		// Chamado: 31347
		// Descricao: Ocultar colunas “Separado” e “EAN” para demais perfis, deixando visível somente para perfil de “supervisor” e “diretor”.
		// Se configuração global configurada em Configuração->opções->Estoque->expedição->“Utilizar conferência cega” estiver como “Sim” e o perfil do usuário não for “supervisor” e “diretor” não mostrar as colunas.
		// Realizar essa modificação no código padrão.
		// --------------------------------------------------------------------------------------------------------
		global $g;
		$mostrarEAN = false;
		$mostrarSeparado = false;

		if($g['C031']['conferenciaCegaVendas'] == 'S' AND ($g['nivelAlcada'] == 4 OR $g['nivelAlcada'] == 2)){
				$mostrarEAN = true;
				$mostrarSeparado = true;
		}

		$grid = array(
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;'),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaConfCegaDescricao' => 'S'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte', 'colunaConfCegaQtd' => 'S', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC(D009_Id) as OC', 'colunaTitulo' => 'OC', 'callback' => 'campoOC', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'T006_Quantidade_Separacao_Callback', 'colunaAlinhar' => 'right', 'colunaExibir' => $mostrarSeparado),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S', 'colunaExibir' => $mostrarEAN),
            array('colunaValor' => 'group_concat(distinct D083_Codigo_Barras separator "|") as D083_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Destino_Produto', 'colunaTitulo' => 'T006_Flag_Destino_Produto', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Conf_Cega', 'colunaTitulo' => 'Conf. Cega', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'colunaConfCegaFlag' => 'S'),
            array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso unit Kg', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D001_Peso_Unitario_Bruto', 'colunaTitulo' => 'Peso unit bruto kg', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right'),
        );

		// --------------------------------------------------------------------------------------------------------
		// FIM - OSNI - 31347 - case 'est002GridFaturamentoPedido'
		// --------------------------------------------------------------------------------------------------------

		/**
		 * Definição da SQL
		 */
		$from = "T006";

		/***
		ALTERAÇÕES NESTE GRID PRECISAM SER FEITAS TAMBÉM NO SELECT MANUAL EM 'MENU.PHP' em 'Conferência Cega'
		***/
		
		$extra = <<<EOT
			left join D009 on D009_Id=T006_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D049_D001_Id=D001_Id
            left join T005 on T006_T005_Id=T005_Id
            left join D006 on D006_Id=T006_D006_Id
            left join D037 on D037_Id=D001_D037_Id
            left join D083 on D083_D001_Id=D001_Id
            where T006_T005_Id='{$r_acaoId}'
            and  (T006_T006_Id is null or T006_T006_Id<=0)
            AND T006_Quantidade>0
            AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
            group by T006_Id
            order by T006_Flag_Tipo_Produto,
            T006_D009_Id,
            T006_Id
EOT;

		$extraTotal = <<<EOT
			left join D009 on D009_Id=T006_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D049_D001_Id=D001_Id
            left join T005 on T006_T005_Id=T005_Id
            left join D006 on D006_Id=T006_D006_Id
            left join D037 on D037_Id=D001_D037_Id
            where T006_T005_Id='{$r_acaoId}'
            and  (T006_T006_Id is null or T006_T006_Id<=0)
            AND T006_Quantidade>0
            AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $grid, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Totais(Versão Acima do GRID)
		*/
		if (!personalizacaoTotais()) {
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(*),
			        		                           SUM(T006_Quantidade),
		        		                               sum(D001_Peso_Unitario_Kg*T006_Quantidade),
		        	    	                           sum(D001_Peso_Unitario_Bruto*T006_Quantidade)",
		        	        	                       $from,
		        	            	                   $extraTotal);

			list($numeroRegistros, 
				 $quantidadeTotal, 
 				 $pesoTotal,  
				 $pesoTotalBruto) = $sqlTotal;			 

			$quantidadeTotal = gCorrigeNumero($quantidadeTotal);
			$pesoTotal = gCorrigeNumero($pesoTotal);
			$pesoTotalBruto = gCorrigeNumero($pesoTotalBruto);

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Quantidade<br><input type="text" class="whiteBg" size="7" value="{$quantidadeTotal}" readonly></span>
					<span class="item" style="text-align:right">Peso total<br><input type="text" class="whiteBg" size="11" value="{$pesoTotal}" readonly></span>
					<span class="item" style="text-align:right">Peso total bruto<br><input type="text" class="whiteBg" size="11" value="{$pesoTotalBruto}" readonly></span>
				    <div style="clear:both;"></div>
				</div>
EOT;
		}

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		/*
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
			});
		} </script></div>
EOT;
*/	

		echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	// desabilitar o refresh automatico deste grid
	$('#{$g['divId']} .grid').data('lastTimeUpdate', 'never');
	//
	function T006_Quantidade_Separacao_Callback(T006_Id, campoEditado, valorAntigo, novoValor) {
		T006_Id = T006_Id.replace(/T006_Id-/, '');
		// atualizar linha do grid
		divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id');
		// gravar alteração
		$.getJSON('/est/est002/grid_func-ajax/est002LogAlteracoesT006QuantidadeSeparacao/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + 
																							'&campoEditado=' +  encodeURIComponent(campoEditado) +
																							'&valorAntigo=' + encodeURIComponent(valorAntigo) +
																							'&novoValor=' + encodeURIComponent(novoValor) +  
																							'&local=' + encodeURIComponent('{$g['r_']}') +  
																							'&callback=?', function(request) {
			//
		});
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

	case 'est002SeparacaoPorLocacao':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T238_Id', $g['divId']), 'style' => 'width: 1%;'),
			array('colunaValor' => 'T238_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Cod. Produto'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => "CONCAT(D004_Local, ' - ', T238_T066_Id)", 'colunaTitulo' => 'Locação', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T238_Quantidade_Separacao', 'colunaTitulo' => 'Qtd Separação', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
		);
		/**
		 * Definição da SQL
		 */
		$from = "T238";
		
		$extra = <<<EOT
		-- JOIN
			LEFT JOIN D004 ON D004_Id = T238_D004_Id
			LEFT JOIN T005 ON T005_Id = T238_T005_Id
			LEFT JOIN T006 ON T006_Id = T238_T006_Id
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
		-- WHERE
			WHERE
				T238_T005_Id = '{$r_T005_Id}'
EOT;

		// Botões
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
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		/*
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
			});
		} </script></div>
EOT;
*/	

		echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	// desabilitar o refresh automatico deste grid
	$('#{$g['divId']} .grid').data('lastTimeUpdate', 'never');
	//
	function T006_Quantidade_Separacao_Callback(T006_Id, campoEditado, valorAntigo, novoValor) {
		T006_Id = T006_Id.replace(/T006_Id-/, '');
		// atualizar linha do grid
		divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id');
		// gravar alteração
		$.getJSON('/est/est002/grid_func-ajax/est002LogAlteracoesT006QuantidadeSeparacao/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + 
																							'&campoEditado=' +  encodeURIComponent(campoEditado) +
																							'&valorAntigo=' + encodeURIComponent(valorAntigo) +
																							'&novoValor=' + encodeURIComponent(novoValor) +  
																							'&local=' + encodeURIComponent('{$g['r_']}') +  
																							'&callback=?', function(request) {
			//
		});
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

	case 'est002GridPedidosRetornados':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T238_Id', $g['divId']), 'colunaTitulo' => 'EP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T238_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Número Pedido' ),
			//array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque Atual', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtd Para Separar', 'callback' => 'gCorrigeNumero', 'style' => 'background-color: var(--celula-amarelo-claro); width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T238_Quantidade_Separacao', 'colunaTitulo' => 'Qtd Separado', 'callback' => 'gCorrigeNumero', 'style' => 'background-color: var(--celula-verde); width: 1%;', 'colunaEditar' => false, 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Quantidade_Separado_3(D009_Id) as Separado','colunaTitulo' => 'Separado Geral','colunaCasasDecimais' => '2'),
			//array('colunaValor' => 'campoSeparadoGeral', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Divergência Estoque', 'callback' => 'campoSeparadoGeral'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Local'),
			//array('colunaValor' => 'T006_Observacao_Divergencia', 'colunaTitulo' => 'Observação'),
			//array('colunaValor' => 'T006_Flag_Tipo_Produto', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus'),
			array('colunaValor' => 'Comprar', 'colunaTitulo' => 'Comprar', 'callback' => 'validaCampoComprar', 'colunaTipo'=>'livre', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => 'CONCAT("Pedido: ", T005_Id, " - ", D024_Nome_Empresa) as TituloPedido', 'colunaTitulo' => 'TituloPedido', 'colunaExibir' => false),
		);
		$from = "T238";
		$extra = <<<EOT
			LEFT JOIN D004 ON D004_Id = T238_D004_Id
			LEFT JOIN T006 ON T006_Id = T238_T006_Id 
			LEFT JOIN T005 ON T005_Id = T006_T005_Id
			left join D009 on D009_Id=T006_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			left join D024 on D024_Id=T005_D024_Id
			left join D006 on D006_Id=T006_D006_Id
			left join D037 on D037_Id=D001_D037_Id
			where 
			T005_Flag_Status != 1
			AND T005_Flag_Status != 2
			AND T005_Flag_Status != 10
			AND T005_Flag_Status != 25
			AND T005_Flag_Status != 3
			AND T005_Flag_Status != 4
			AND T005_Flag_Status != 5
			AND T005_Flag_Status != 7
			AND T005_Flag_Status != 8
			AND T005_Flag_Status != 26
			AND T006_Quantidade_Separacao > 0
			order by T006_T005_Id,T006_Codigo_Produto
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!--<div class="buttonsBottomTop">
				 <button type="button" id="est003GridDivImp">Imprimir</button> 
				
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est003GridDivImp').unbind('click').bind('click', function(data) {
					nova_janela('/hardness3/hardness/almoxarifado/imprimir_divergencias.php',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T006_T005_Id' => array('titulo' => 'Número Pedido', 'tipo' => 'numero'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T006_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'T006_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'Agrupar' => array(
				'titulo' => 'Agrupar',
				'tipo' => 'agrupar',
				'exibir' => false,
				'campos' => array(array('title' => 'T005_Id', 'campoAgrupar' => 'T005_Id', 'campoMostrar' => 'TituloPedido', 'campoOrdem' => 'ASC', 'campoSelecionado' => true))
			)
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Produtos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		*/
		
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var id = id[0].replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
        //$g['smarty']->assign('gridColunasFixo', 2);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	case 'etiquetaSeparacao':
		global $confUsuario;

		$sqlT005 = mysql_query("SELECT * FROM T005 
		LEFT JOIN C004 ON C004_Id = T005_C004_Id 
		LEFT JOIN D024 ON D024_Id = T005_D024_Id
		LEFT JOIN D018 ON D018_Id = C004_D018_Id
		LEFT JOIN D020 ON D020_Id = C004_D020_Id
		LEFT JOIN D022 ON D022_Id = T005_D022_Id
		LEFT JOIN T007 ON T005_Id = T007_T005_Id
		WHERE T005_Id = '{$r_T005}'");
		$T005 = mysql_fetch_array($sqlT005);

		$sqlD024 = mysql_query("SELECT * FROM D024 
		LEFT JOIN D018 ON D018_Id = D024_D018_Id
		LEFT JOIN D020 ON D020_Id = D024_D020_Id
		WHERE D024_Id = '{$T005['D024_Id']}'");
		$D024 = mysql_fetch_array($sqlD024);

		$file = '/tmp/est002EtiquetaSeparacao'.$confUsuario['dbDatabase'].'.txt';
		$txt = '';
		$volumes = ($T005['T005_Quantidade_Volumes'] > 0) ? $T005['T005_Quantidade_Volumes'] : 1;

		for($volume = 1; $volume <= $volumes; $volume++){
		if($volume > 1){
			$txt.= "---------";
		}

$txt .= <<<EOT

N
q800
Q480,24
GG60,10,"GUIMEP00"
A20,100,0,1,1,1,N,"{$T005['C004_Nome_Empresa']} CNPJ:{$T005['C004_Cnpj']} IE: {$T005['C004_Inscricao_Estadual']}"
A20,120,0,1,1,1,N,"{$T005['C004_Logradouro']}, {$T005['C004_Numero']} {$T005['C004_Complemento']}, CEP:{$T005['C004_Cep']}, {$T005['C004_Bairro']}, {$T005['D020_Nome_Cidade']} - {$T005['D018_UF']}"
A20,180,0,3,1,1,N,"CLIENTE: {$T005['D024_Nome_Empresa']}"
A20,230,0,3,1,1,N,"ENDERECO: {$T005['D024_Endereco']}, {$T005['D024_Numero']}"
A20,280,0,3,1,1,N,"BAIRRO: {$T005['D024_Bairro']}"
A20,330,0,3,1,1,N,"MUNICIPIO: {$D024['D020_Nome_Cidade']}"
A490,330,0,3,1,1,N,"UF: {$D024['D018_UF']}  CEP: {$T005['D024_Cep']}"
A20,380,0,3,1,1,N,"TRANSPORTADORA: {$T005['D022_Nome_Empresa']}"
A20,430,0,3,1,1,N,"VOLUME(S): {$volume}/{$volumes}"
A500,430,0,3,1,1,N,"NOTA FISCAL: {$T005['T007_Numero_Nota_Fiscal']}"
P1,1

EOT;
}
	  
		file_put_contents($file, $txt);

		$resposta['data'] = 'impressaoArquivo-' . base64_encode($file);
	break;

	case 'est002GridSeparacaoBalcao':
		$grid = array(
			array('colunaValor' => "campoOpcoesImpressao2", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesImpressao2', 'style' => 'width: 1%; padding: 3px 1px;'),
			array('colunaValor' => "campoOpcoesImpressao3", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesImpressao3', 'style' => 'width: 1%; padding: 3px 1px;'),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Observacao_Separacao', 'colunaTitulo' => 'Observação', 'style' => 'width: 1%', 'colunaEditar' => true),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => 'T005_Flag_Dividido', 'colunaTitulo' => 'Div', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'style' => 'width: 1%'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => '(select (sum(T006_Quantidade_Separacao)/sum(T006_Quantidade))*100 from T006 where T006_T005_Id=T005_Id) as Separado', 'colunaTitulo' => '% Sep', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => '(select sum(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao) from T006 where T006_T005_Id=T005_Id) as Valor_Separado', 'colunaTitulo' => 'Valor separado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%'),
			//array('colunaValor' => 'T005_Peso_Liquido', 'colunaTitulo' => 'Peso Líquido', 'colunaCasasDecimais' => '3', 'style' => 'width: 1%'),
			//array('colunaValor' => 'T005_Peso_Bruto', 'colunaTitulo' => 'Peso Bruto', 'colunaCasasDecimais' => '3', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Obs.Almox', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005A_JSON->Quantidade_Impressao', 'colunaTitulo' => 'Qtd Impressão', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			-- JOIN
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				left join T005A ON T005_Id=T005A_T005_Id
			-- WHERE
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='21'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=2	
			-- GROUP BY
                group by T005_Id 
			-- ORDER BY
				order by T005_Id
EOT;

		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T005_Flag_Dividido'] == 'S'){
				return 'linhaAzul';
			}
			if($array['todosCampos']['Separado']<=0){
				return 'linhaVermelho';
			} 
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'D002_Descricao_Produto,D001_Codigo_Produto,D001_Descricao_Produto,D002_Grupo(D002_Id)' => array('titulo' => 'Produto', 'tipo' => 'texto'),
            'T005_Separacao_Lote' => array('titulo' => 'Lote', 'tipo' => 'texto'),
			'if(T005A_JSON->Quantidade_Impressao != ""[[virgula]]"S"[[virgula]] "N")' => array('titulo' => 'Impresso? (S/N)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Confirmar Separação do Pedido', '/est/est002/content/est002SeparacaoPedido/','&acaoId=' + encodeURIComponent(id) + '&T005_Id=' + encodeURIComponent(id), [950,550]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002FaturamentoPedido/','&acaoId='+ encodeURIComponent(id) + '&T005_Id=' +  encodeURIComponent(id), [500,400]);
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

	case 'est002GridSeparacaoBalcaoProduto':

		$filtroObrigatorio = true;

		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
		    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
 			//array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'callback' => 'abrirJanela', 'callbackParameter' =>array('Confirmar Sepração do Pedido', '/est/est002/content/est002SeparacaoPedido/', 'T005_Id', '990,605'), 'style' => 'width: 1%;'),
			//array('colunaValor' => 'if(T005_Flag_Dividido="N","","S") as T005_Flag_Dividido', 'colunaTitulo' => 'Div'),

			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Cod. Prod.', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Desc. Prod.', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'style' => 'width: 1%;'),

			array('colunaValor' => 'SUM(T006_Quantidade) as T006_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde); width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'SUM(T006_Quantidade_Separacao) as T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo-claro); width: 1%', 'colunaAlinhar' => 'right'),

			// Saldo

			//array('colunaValor' => '((T006_Quantidade_Separacao/T006_Quantidade)*100) as Separado', 'colunaTitulo' => '% Sep', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => '(SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
			array('colunaValor' => 'GROUP_CONCAT(DISTINCT T005_Id SEPARATOR ",") as Pedidos', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'left', 'callback' => 'separacaoPedidosPorProduto'),
			//array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),

			//array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
			

			//array('colunaValor' => 'T005_Observacao_Separacao', 'colunaTitulo' => 'Observação'),
			//array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Obs.Almox'),

			array('colunaValor' => 'CONCAT(T006_Codigo_Produto," - ",T006_Descricao_Produto) as CodDesc', 'colunaTitulo' => 'CodDesc', 'colunaExibir' => false),
			array('colunaValor' => 'CONCAT(T005_Id," - ",D024_Nome_Empresa) as PedCliente', 'colunaTitulo' => 'CodPedido', 'colunaExibir' => false),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join D009 on D009_Id=T006_D009_Id
            	left join D049 on D049_Id=D009_D049_Id
            	left join D082 on D082_Id=D049_D082_Id
            	left join D001 on D049_D001_Id=D001_Id
				left join D037 on D037_Id=D001_D037_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='21'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=2	
                  AND T006_Codigo_Produto IS NOT NULL
                  AND T006_Codigo_Produto != ''
				GROUP BY T006_Codigo_Produto
				HAVING Saldo > 0
				order by T006_Codigo_Produto ASC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T006_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'T006_Descricao_Produto' => array('titulo' => 'Desc. Produto', 'tipo' => 'texto'),
			'T005_Separacao_Lote' => array('titulo' => 'Lote', 'tipo' => 'numero'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                        FROM T006 
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto','exibir' => false),
			/*'Agrupar' => array('titulo' => 'Classificar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
            	array('title' => 'Produto', 'campoAgrupar' => 'CodDesc', 'campoMostrar' => 'CodDesc', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
            	array('title' => 'Pedido', 'campoAgrupar' => 'PedCliente', 'campoMostrar' => 'PedCliente', 'campoOrdem' => 'ASC')
        	)),*/
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
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
					var D001_Id = id[0].replace(/D001_Id-/, '');
					var D024_Id = id[1].replace(/D024_Id-/, '');
				   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&tabela=$from&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
			   });
			   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });

				//$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
//					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					//var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002FaturamentoPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Confirmar Sepração do Pedido', '/est/est002/content/est002SeparacaoPedido/','&acaoId=' + encodeURIComponent(id) + '&T005_Id=' + encodeURIComponent(id), [990,605]);
//				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
				});
			} </script></div>
EOT;
		}

		/**
		 * Totalizadores
		 */
		if (!personalizacaoTotais(false, true)) { 
			//list($qteProdutos,$qtePedidos,$valorTotal,$valorFaturar) = $g['sqlAuto']->pegarSqlCampos("COUNT(DISTINCT T006_Codigo_Produto),COUNT(DISTINCT T005_Id),SUM(T006_Valor_Preco_Unitario*T006_Quantidade),SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao)", $from, preg_replace('#HAVING +Saldo +> +0#s', '', preg_replace('#GROUP +BY +T006_Codigo_Produto#s', '', $extra)));
			$qteProdutos = $g['sqlAuto']->pegarSqlCampo('T006_Codigo_Produto, (SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', $from, $extra, true);
			$qtePedidos = $g['sqlAuto']->pegarSqlCampo('COUNT(DISTINCT T005_Id)', $from, preg_replace('#HAVING +Saldo +> +0#s', '', preg_replace('#GROUP +BY +T006_Codigo_Produto#s', '', $extra)));
			//$valorTotal = $g['sqlAuto']->pegarSqlCampo('SUM(T006_Valor_Preco_Unitario*T006_Quantidade), (SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', $from, $extra, false, true, true);
			//$valorFaturar = $g['sqlAuto']->pegarSqlCampo('SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao), (SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', $from, $extra, false, true, true);
			$valorTotal = gCorrigeNumero($valorTotal);
			$valorFaturar = gCorrigeNumero($valorFaturar);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Produtos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$qteProdutos}" readonly></span>
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$qtePedidos}" readonly></span>
					<!--<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Faturar<br><input type="text" class="whiteBg" id="inputValorFaturar" size="11" value="{$valorFaturar}" readonly></span>-->
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();
		
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

	case 'est002GridSeparacaoComercial':
		$grid = array(
			array('colunaValor' => "campoOpcoesImpressao2", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesImpressao2', 'style' => 'width: 1%; padding: 3px 1px;'),
			array('colunaValor' => 'campoOpcoesImpressao3', 'colunaTipo' => 'livre','callback' => 'campoOpcoesImpressao3', 'style' => 'width:30px; padding: 3px 1px 3px 10px;'),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Observacao_Separacao', 'colunaTitulo' => 'Observação', 'style' => 'width: 1%', 'colunaEditar' => true),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => 'T005_Separacao_Lote', 'colunaTitulo' => 'Lote', 'style' => 'width: 45px;', 'colunaEditar' => true, 'colunaEditarTodos' => true),
			array('colunaValor' => 'T005_Flag_Dividido', 'colunaTitulo' => 'Div', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'style' => 'width: 1%'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => '(select (sum(T006_Quantidade_Separacao)/sum(T006_Quantidade))*100 from T006 where T006_T005_Id=T005_Id) as Separado', 'colunaTitulo' => '% Sep', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => '(select sum(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao) from T006 where T006_T005_Id=T005_Id) as Valor_Separado', 'colunaTitulo' => 'Valor separado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Peso_Liquido', 'colunaTitulo' => 'Peso Líquido', 'colunaCasasDecimais' => '3', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Peso_Bruto', 'colunaTitulo' => 'Peso Bruto', 'colunaCasasDecimais' => '3', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Obs.Almox', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005A_JSON->Quantidade_Impressao', 'colunaTitulo' => 'Qtd Impressão', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
		-- JOIN
			left join D024 on D024_Id=T005_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D021 on D021_Id=T005_D021_Id
			left join D022 on D022_Id=T005_D022_Id
			left join D006 on D006_Id=T005_D006_Id
			left join T006 on T005_Id=T006_T005_Id
			left join T055 on T055_D009_Id=T006_D009_Id
			left join T075 on T075_Id=T055_T075_Id 
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C004 on C004_Id=T005_C004_Id
			left join D036 on T005_D036_Id=D036_Id 
			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
			left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
			left join D009 on D009_Id=T006_D009_Id 
			left join D049 on D049_Id=D009_D049_Id 
			left join D001 on D001_Id=D049_D001_Id 
			left join D002 on D002_Id=D001_D002_Id
			left join T005A on T005_Id=T005A_T005_Id
		-- WHERE
			where T005_C004_Id={$g['empresaAtual']}
              and T005_Flag_Separacao='S'  
              and T005_Flag_Status='21'  
              and T005_Flag_Separacao_Liberar='N'  
              and T005_Flag_Expedicao=1	
		-- GROUP BY	
            group by T005_Id 
		-- ORDER BY	
			order by T005_Id
EOT;


$extraTotal = <<<EOT
left join D024 on D024_Id=T005_D024_Id
left join D020 on D020_Id=D024_D020_Id
left join D018 on D018_Id=D024_D018_Id
left join D021 on D021_Id=T005_D021_Id
left join D022 on D022_Id=T005_D022_Id
left join D006 on D006_Id=T005_D006_Id
left join T006 on T005_Id=T006_T005_Id
left join T055 on T055_D009_Id=T006_D009_Id
left join T075 on T075_Id=T055_T075_Id 
left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
left join C004 on C004_Id=T005_C004_Id
left join D036 on T005_D036_Id=D036_Id 
left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
left join D009 on D009_Id=T006_D009_Id 
left join D049 on D049_Id=D009_D049_Id 
left join D001 on D001_Id=D049_D001_Id 
left join D002 on D002_Id=D001_D002_Id
where T005_C004_Id={$g['empresaAtual']}
  and T005_Flag_Separacao='S'  
  and T005_Flag_Status='21'  
  and T005_Flag_Separacao_Liberar='N'  
  and T005_Flag_Expedicao=1	
EOT;

		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T005_Flag_Dividido'] == 'S'){
				return 'linhaAzul';
			}
			error_log("tenho acesso aqui Separado ".$array['todosCampos']['Separado']);
			if($array['todosCampos']['Separado']<=0){
				return 'linhaVermelho';
			} 
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'D002_Descricao_Produto,D001_Codigo_Produto,D001_Descricao_Produto,D002_Grupo(D002_Id)' => array('titulo' => 'Produto', 'tipo' => 'texto'),
            'T005_Separacao_Lote' => array('titulo' => 'Lote', 'tipo' => 'texto'),
			'if(T005A_JSON->Quantidade_Impressao != ""[[virgula]]"S"[[virgula]] "N")' => array('titulo' => 'Impresso? (S/N)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'valor_total', 'valor' => 'T005_Valor_Total', 'titulo' => 'Valor Total'),
		);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				  	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Confirmar Separação do Pedido', '/est/est002/content/est002SeparacaoPedido/','&acaoId=' + encodeURIComponent(id) + '&T005_Id=' + encodeURIComponent(id) + '&tabela=T005', [950,550]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
				});
			} </script></div>
EOT;
		}

		/**
		 * Totalizadores
		 */
/* 		if (!personalizacaoTotais(false, true)) { 
			//list($qtePedidos,$valorTotal,$valorFaturar) = $g['sqlAuto']->pegarSqlCampos("COUNT(DISTINCT T005_Id),SUM(T006_Valor_Preco_Unitario*T006_Quantidade),SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao)", $from, str_replace('group by T005_Id', '', $extra));
			$qtePedidos = $g['sqlAuto']->pegarSqlCampo('T005_Id', $from, $extra, true);
			$valorTotal = $g['sqlAuto']->pegarSqlCampo('SUM(T006_Valor_Preco_Unitario*T006_Quantidade)', $from, $extra, false, true, true);
			$valorFaturar = $g['sqlAuto']->pegarSqlCampo('SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao)', $from, $extra, false, true, true);
			//$qtePedidos = $g['sqlAuto']->pegarSqlCampo('COUNT(DISTINCT T005_Id)', $from, preg_replace('#HAVING +Saldo +> +0#s', '', preg_replace('#GROUP +BY +T006_Codigo_Produto#s', '', $extra)));
			$valorTotal = gCorrigeNumero($valorTotal);
			$valorFaturar = gCorrigeNumero($valorFaturar);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$qtePedidos}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Faturar<br><input type="text" class="whiteBg" id="inputValorFaturar" size="11" value="{$valorFaturar}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais(); */



		
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

	case 'est002GridSeparacaoComercialProduto':
		
		$filtroObrigatorio = true;

		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
		    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
 			//array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'callback' => 'abrirJanela', 'callbackParameter' =>array('Confirmar Sepração do Pedido', '/est/est002/content/est002SeparacaoPedido/', 'T005_Id', '990,605'), 'style' => 'width: 1%;'),
			//array('colunaValor' => 'if(T005_Flag_Dividido="N","","S") as T005_Flag_Dividido', 'colunaTitulo' => 'Div'),

			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Cod. Prod.', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Desc. Prod.', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1)', 'colunaTitulo' => 'Vendido', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id)', 'colunaTitulo' => 'OC', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'SUM(T006_Quantidade) as T006_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde); width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'SUM(T006_Quantidade_Separacao) as T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo-claro); width: 1%', 'colunaAlinhar' => 'right'),

			// Saldo

			//array('colunaValor' => '((T006_Quantidade_Separacao/T006_Quantidade)*100) as Separado', 'colunaTitulo' => '% Sep', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => '(SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'GROUP_CONCAT(DISTINCT T005_Id SEPARATOR ",") as Pedidos', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'left', 'callback' => 'separacaoPedidosPorProduto'),
			//array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),

			//array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
			

			//array('colunaValor' => 'T005_Observacao_Separacao', 'colunaTitulo' => 'Observação'),
			//array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Obs.Almox'),

			array('colunaValor' => 'CONCAT(T006_Codigo_Produto," - ",T006_Descricao_Produto) as CodDesc', 'colunaTitulo' => 'CodDesc', 'colunaExibir' => false),
			array('colunaValor' => 'CONCAT(T005_Id," - ",D024_Nome_Empresa) as PedCliente', 'colunaTitulo' => 'CodPedido', 'colunaExibir' => false),
			array('colunaValor' => 'separacaoPedidosPorProduto', 'colunaTitulo' => 'Pedidos', 'callback' => 'separacaoPedidosPorProduto', 'colunaTipo' => 'livre'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join D009 on D009_Id=T006_D009_Id
            	left join D049 on D049_Id=D009_D049_Id
            	left join D082 on D082_Id=D049_D082_Id
            	left join D001 on D049_D001_Id=D001_Id
				left join D037 on D037_Id=D001_D037_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='21'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=1	
                  AND T006_Codigo_Produto IS NOT NULL
                  AND T006_Codigo_Produto != ''
				GROUP BY T006_Codigo_Produto
				HAVING Saldo > 0
				order by T006_Codigo_Produto ASC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T006_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'T006_Descricao_Produto' => array('titulo' => 'Desc. Produto', 'tipo' => 'texto'),
			'T005_Separacao_Lote' => array('titulo' => 'Lote', 'tipo' => 'numero'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                        FROM T006 
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto','exibir' => false),
			/*'Agrupar' => array('titulo' => 'Classificar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
            	array('title' => 'Produto', 'campoAgrupar' => 'CodDesc', 'campoMostrar' => 'CodDesc', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
            	array('title' => 'Pedido', 'campoAgrupar' => 'PedCliente', 'campoMostrar' => 'PedCliente', 'campoOrdem' => 'ASC')
        	)),*/
			'D049_Flag_Tipo_Origem' => array('titulo' => 'Origem', 'tipo' => 'select', 
				'select' => array(
								array('title' => 'Todos', 'value' => ''),
								array('title' => 'Comprado', 'value' => 'C'),
								array('title' => 'Produzido', 'value' => 'P'),
							)
			)
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
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
				var D001_Id = id[0].replace(/D001_Id-/, '');
				var D024_Id = id[1].replace(/D024_Id-/, '');
			   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&tabela=$from&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
		   });
		   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });

			//$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
//					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				//var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002FaturamentoPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Confirmar Sepração do Pedido', '/est/est002/content/est002SeparacaoPedido/','&acaoId=' + encodeURIComponent(id) + '&T005_Id=' + encodeURIComponent(id), [990,605]);
//				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
				});
			} </script></div>
EOT;
		}

		/**
		 * Totalizadores
		 */
		if (!personalizacaoTotais(false, true)) { 
			//list($qteProdutos,$qtePedidos,$valorTotal,$valorFaturar) = $g['sqlAuto']->pegarSqlCampos("COUNT(DISTINCT T006_Codigo_Produto),COUNT(DISTINCT T005_Id),SUM(T006_Valor_Preco_Unitario*T006_Quantidade),SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao)", $from, preg_replace('#HAVING +Saldo +> +0#s', '', preg_replace('#GROUP +BY +T006_Codigo_Produto#s', '', $extra)));
			$qteProdutos = $g['sqlAuto']->pegarSqlCampo('T006_Codigo_Produto, (SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', $from, $extra, true);
			$qtePedidos = $g['sqlAuto']->pegarSqlCampo('COUNT(DISTINCT T005_Id)', $from, preg_replace('#HAVING +Saldo +> +0#s', '', preg_replace('#GROUP +BY +T006_Codigo_Produto#s', '', $extra)));
			//$valorTotal = $g['sqlAuto']->pegarSqlCampo('SUM(T006_Valor_Preco_Unitario*T006_Quantidade), (SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', $from, $extra, false, true, true);
			//$valorFaturar = $g['sqlAuto']->pegarSqlCampo('SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao), (SUM(IFNULL(T006_Quantidade,0))-SUM(IFNULL(T006_Quantidade_Separacao,0))) as Saldo', $from, $extra, false, true, true);
			$valorTotal = gCorrigeNumero($valorTotal);
			$valorFaturar = gCorrigeNumero($valorFaturar);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Produtos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$qteProdutos}" readonly></span>
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$qtePedidos}" readonly></span>
					<!--<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Faturar<br><input type="text" class="whiteBg" id="inputValorFaturar" size="11" value="{$valorFaturar}" readonly></span>-->
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();

		
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

	case 'est002GridConferenciaBalcao':
		$grid = array(
			array('colunaValor' => "campoOpcoesSeparacaoSelecionaAcao", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesSeparacaoSelecionaAcao', 'style' => 'width:1%; padding: 3px 1px;'),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
 			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
			array('colunaValor' => 'if(T005_Flag_Dividido="N","","S") as T005_Flag_Dividido', 'colunaTitulo' => 'Div'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			//array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => '(select sum(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao) from T006 where T006_T005_Id=T005_Id) as Valor_Separado', 'colunaTitulo' => 'Valor faturar', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'T005_Quantidade_Volumes', 'colunaTitulo' => 'Volumes'),
			array('colunaValor' => 'T005_Especie', 'colunaTitulo' => 'Espécie'),
			array('colunaValor' => 'T005_Peso_Bruto', 'colunaTitulo' => 'Peso', 'colunaCasasDecimais' => '3'),
     	);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
		 	-- JOIN
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
			-- WHERE
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='10'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=2	
			-- GROUP BY
                group by T005_Id 
			-- ORDER BY
				order by T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                        FROM T006 
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto','exibir' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002FaturamentoPedido/','&acaoId='+ encodeURIComponent(id) + '&T005_Id=' +  encodeURIComponent(id), [700,500]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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

	case 'est002GridConferenciaComercial':
		$grid = array(
			array('colunaValor' => "campoOpcoesSeparacaoSelecionaAcao", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesSeparacaoSelecionaAcao', 'style' => 'width:1%; padding: 3px 1px;'),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
 			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
			array('colunaValor' => 'if(T005_Flag_Dividido="N","","S") as T005_Flag_Dividido', 'colunaTitulo' => 'Div'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			//array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => '(select sum(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao) from T006 where T006_T005_Id=T005_Id) as Valor_Separado', 'colunaTitulo' => 'Valor faturar', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'T005_Quantidade_Volumes', 'colunaTitulo' => 'Volumes'),
			array('colunaValor' => 'T005_Especie', 'colunaTitulo' => 'Espécie'),
			array('colunaValor' => 'T005_Peso_Bruto', 'colunaTitulo' => 'Peso Bruto', 'colunaCasasDecimais' => '3', 'colunaEditar' => true),
			array('colunaValor' => 'T005_Peso_Liquido', 'colunaTitulo' => 'Peso Líquido', 'colunaCasasDecimais' => '3', 'colunaEditar' => true),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
		 	-- JOIN 
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
			-- WHERE
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='10'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=1	
			-- GROUP BY
                group by T005_Id 
			-- ORDER BY
				order by T005_Id
EOT;

		$extraTotal = <<<EOT
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='10'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=1	
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                        FROM T006 
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto','exibir' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'valor_total', 'valor' => 'T005_Valor_Total', 'titulo' => 'Valor Total'),
		);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002FaturamentoPedido/','&acaoId='+ encodeURIComponent(id) + '&T005_Id=' +  encodeURIComponent(id), [700,500]);
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				});
			} </script></div>
EOT;
		}

		/**
		 * Totalizadores
		 */
/* 		if (!personalizacaoTotais(false, true)) { 
			list($qtePedidos,$valorTotal,$valorFaturar) = $g['sqlAuto']->pegarSqlCampos("COUNT(DISTINCT T005_Id),SUM(T006_Valor_Preco_Unitario*T006_Quantidade),SUM(T006_Valor_Preco_Unitario*T006_Quantidade_Separacao)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($valorTotal);
			$valorFaturar = gCorrigeNumero($valorFaturar);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$qtePedidos}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Faturar<br><input type="text" class="whiteBg" id="inputValorFaturar" size="11" value="{$valorFaturar}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais(); */

		
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

	case 'est002GridColetados':
		$grid = array(
            //array('colunaValor' => 'campoOpcoesAguardandoEntrega1', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesAguardandoEntrega1', 'style' => 'width:18px; padding: 3px 10px 3px 10px;'),
            //array('colunaValor' => 'retornarPedidoViaCupom', 'colunaTipo' => 'livre', 'callback' => 'retornarPedidoViaCupom', 'style' => 'width:18px; padding: 3px 10px 3px 10px;'),
            //array('colunaValor' => 'campoOpcoesAguardandoEntrega2', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesAguardandoEntrega2', 'style' => 'width:18px; padding: 3px 10px 3px 10px;'),
            array('colunaValor' => "T007_Flag_Enviar_XML_Transportadora", 'colunaTitulo'=>'-', 'callback' => 'validaCampoCheckboxEnvioEmail', 'style'=>'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'colunaEditarTodosTriggerPHP' => false, 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'imprimirMinutaDeDespacho', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Minuta', 'callback' => 'imprimirMinutaDeDespacho', 'style' => 'width:30px; padding: 3px 10px 3px 20px;', 'colunaAlinhar' => 'center',),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/notafiscal/', 'T007_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 330), 'style' => 'width:18px; padding: 3px 10px 3px 10px;', 'colunaTitulo' => 'IE', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções', 'callback' => 'campoOpcoesAguardandoEntrega', 'colunaExibir' => false),
            array('colunaValor' => 'campoRetornarExpedicao', 'colunaTipo' => 'livre', 'callback' => 'campoRetornarExpedicao', 'style' => 'width:30px; padding: 3px 1px 3px 10px; background-color: #ececec;', 'colunaTitulo' => 'RE', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box'),
            array('colunaValor' => 'T005_T275_Id', 'colunaTitulo' => 'Romaneio', 'callback' => 'retornaImpressaoRomaneio', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Data_Entrega', 'colunaTitulo' => 'Prazo entrega', 'callback' => 'gCorrigeData'),
            array('colunaValor' => '(SELECT C007_Primeiro_Nome FROM T178 LEFT JOIN C007 ON T178_C007_Id=C007_Id WHERE T178_T005_Id=T005_Id AND T178_Descricao="Coletado" ORDER BY T178_Id DESC LIMIT 1)', 'colunaTitulo' => 'Coletou'),
            array('colunaValor' => '(SELECT IF(T178_Data="0000-00-00","",T178_Data) FROM T178 WHERE T178_T005_Id=T005_Id AND T178_Descricao="Coletado" ORDER BY T178_Id DESC LIMIT 1)', 'colunaTitulo' => 'Coleta', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'if(T005_Flag_ACP=\'1\',\'NOTA FISCAL\',if(T005_Flag_ACP=\'3\',\'ACP\',if(T005_Flag_ACP=\'5\',\'NOTA D1\',if(T005_Flag_ACP=\'2\',\'NÃO FATURAR\',if(T005_Flag_ACP=\'6\',\'NF-e\',if(T005_Flag_ACP=\'7\',\'NFS-e\',if(T005_Flag_ACP=\'4\',\'CUPOM FISCAL\',\'<font color=red>FALTA</font>\'))))))) as Tipo_Documento', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação', 'style' => 'color: var(--BGVermelho);max-width:200px;white-space:normal;'),
            array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'Id_Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'D006_Descricao_Comercial', 'colunaExibir' => false, 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
        										FROM T006 
        										LEFT JOIN D009 ON D009_Id=T006_D009_Id 
        										LEFT JOIN D049 ON D049_Id=D009_D049_Id 
        										LEFT JOIN D001 ON D001_Id=D049_D001_Id 
        										LEFT JOIN C008 ON C008_Id=D001_C008_Id 
        										WHERE T006_T005_Id=T005_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
            array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio', 'colunaExibir' => false),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir' => false),
            array('colunaValor' => 'Separou.C007_Primeiro_Nome as Nome_Separou', 'colunaTitulo' => 'Separou', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Total', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id) as Prazo', 'colunaTitulo' => 'Prazo', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Emite_NF_Caixa', 'colunaTitulo' => 'Emite NFE', 'colunaExibir' => false),
            array('colunaValor' => 'Conferiu.C007_Primeiro_Nome as Nome_Conferiu', 'colunaTitulo' => 'Conferiu', 'colunaExibir' => 's'),
            array('colunaValor' => 'T005_Flag_Expedicao', 'colunaTitulo' => 'T005_Flag_Expedicao', 'colunaExibir' => false),
             array('colunaValor' => 'T007_Codigo_Rastreio', 'colunaTitulo' => 'Código Rastreio', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'color:blue;min-width:150px;max-width:150px;white-space:bold;'),
            array('colunaValor' => 'campoOpcoesColetadoRetornar', 'colunaTipo' => 'livre','callback' => 'campoOpcoesColetadoRetornar', 'style' => 'width:30px; padding: 3px 1px 3px 10px;'),
            array('colunaValor' => 'T007_Data_Envio_XML_Transportadora', 'colunaTitulo' => 'Envio email', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
			left join D024 on D024_Id=T005_D024_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D021 on D021_Id=T005_D021_Id
			left join D022 on D022_Id=T007_D022_Id
			left join D006 on D006_Id=T005_D006_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C004 on C004_Id=T005_C004_Id
			left join D036 on T005_D036_Id=D036_Id 
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
			left join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas
			left join C007 as Conferiu on Conferiu.C007_Id=T005_C007_Id_Conferiu
			where (T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='5')
			order by T005_Id
EOT;

		$extraTotal = <<<EOT
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
			left join C007 as Conferiu on Conferiu.C007_Id=T005_C007_Id_Conferiu
			where (T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='5')
EOT;
		$extra_ = base64_encode(gRetirarOrderBy($extraTotal));

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
            '(SELECT T178_Data FROM T178 WHERE T178_T005_Id=T005_Id AND T178_Descricao="Coletado" ORDER BY T178_Id DESC LIMIT 1)' => array('titulo' => 'Coleta', 'tipo' => 'data'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) AS Tipo
                                        FROM T006 
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'T005_T275_Id' => array('titulo' => 'Romaneio', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array ('titulo' => 'Nota Fiscal', 'tipo' => 'numero'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extra__ = $extra;

/*		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est002GridEnvioXMLEmailTransportadora">Enviar XML Transportadora</button>	
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
		$extra__ = base64_encode(gRetirarOrderBy($extra__));

		//Botões
		$botoes = array(
			array('titulo' => 'Enviar XML Transportadora', 'id' => 'est002GridEnvioXMLEmailTransportadora'),
		);
		echo Botoes::processar($botoes);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$novoIdTelaForm = uniqid();

		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais(false, true)) { 
			$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		    $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(distinct T005_Id)",
		        	        	                       $from,
		        	            	                   $extraTotal);

			list($numeroRegistros) = $sqlTotal;

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputQtePedidos" size="11" value="{$numeroRegistros}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		fimTotais();*/

        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T005_Id = id[0].replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + T005_Id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [700,500]);
			});
		} </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est002GridEnvioXMLEmailTransportadora').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						idProgressBar = dialogAlert('Enviando e-mails',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						$.getJSON('/est/est002/grid_func-ajax/validacaoEnvioXMLTransportadoras/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&callback=?', function(request)  {
	                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
							if (request.code) {
								idProgressBar = dialogAlert('Enviando e-mails',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            ven002EnviarXMLEmailTransportadoraLoop(idProgressBar,1); 
					            $(this).dialog("destroy").remove();
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); }
				};
				dialogConfirm('Confirmar envio', 'Deseja enviar o XML por email para as transportadoras dos pedidos selecionados? ', \$buttons);
			});

			function ven002EnviarXMLEmailTransportadoraLoop(idProgressBar, inicio){
            	$.getJSON('/est/est002/grid_func-ajax/envioXMLEmailTransportadoraPedidosMarcados/?ajax=true&extra=' + encodeURIComponent('{$extra__}') + '&inicio=' + inicio + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Envios realizados: <b>' + request.ok + '</b><br/>Envios com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	ven002EnviarXMLEmailTransportadoraLoop(idProgressBar,0);
                    }else{
                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
                    	dialogConfirm('XML enviados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Envios realizados: <b>' + request.ok + '</b><br/>Envios com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	if (request.code) {
                            divRefresh('{$g['divId']}');
                			$(this).dialog("destroy").remove();     
                        }
                    }
                });
	        }
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

	case 'est002GridPortalPostal':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T268_Id', $g['divId']), 'colunaTitulo' => 'E', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T268_Id', 'colunaTitulo' => 'Chave', 'chavePrimaria' => true, 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T268_Flag_Postagem', 'colunaTitulo' => 'P', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array('T268_Flag_Postagem', 'N|S', false, true), 'colunaEditarTodos' => true, 'colunaEditarUsuario' => 'T268_C007_Id_Postagem', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Destinatário'),
            array('colunaValor' => 'T268_T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Nota fiscal', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T268_D024_Id', 'colunaTitulo' => 'Cód.', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Destinatário'),
            array('colunaValor' => 'T268_Recebedor', 'colunaTitulo' => 'Recebedor'),
            array('colunaValor' => 'IF(IFNULL(T268_D148_Id,0)>0,D148_Rua,D024_Endereco) as D024_Endereco', 'colunaTitulo' => 'Endereco'),
            array('colunaValor' => 'IF(IFNULL(T268_D148_Id,0)>0,D148_Numero,D024_Numero) as D024_Numero', 'colunaTitulo' => 'Nº', 'style' => 'width:1%'),
            array('colunaValor' => 'IF(IFNULL(T268_D148_Id,0)>0,D148_Complemento,D024_Complemento) as D024_Complemento', 'colunaTitulo' => 'Complemento', 'style' => 'width:1%'),
            array('colunaValor' => 'IF(IFNULL(T268_D148_Id,0)>0,D148_Bairro,D024_Bairro) as D024_Bairro', 'colunaTitulo' => 'Bairro', 'style' => 'width:1%'),
            array('colunaValor' => 'IF(IFNULL(T268_D148_Id,0)>0,D148_Cep,D024_Cep) as D024_Cep', 'colunaTitulo' => 'Cep', 'style' => 'width:1%'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'style' => 'width:1%'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width:1%'),
            array('colunaValor' => "CONCAT(REPLACE(REPLACE(REPLACE(FORMAT(T268_Comprimento,2), ',', ':'), '.', ','), ':', '.'), ' x ',
            							   REPLACE(REPLACE(REPLACE(FORMAT(T268_Largura,2), ',', ':'), '.', ','), ':', '.'), ' x ',
            							   REPLACE(REPLACE(REPLACE(FORMAT(T268_Altura,2), ',', ':'), '.', ','), ':', '.'))", 'colunaTitulo' => 'Tamanho'),
            array('colunaValor' => 'T268_Peso', 'colunaTitulo' => 'Peso', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T268_Valor_Declarado', 'colunaTitulo' => 'Valor declarado', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T268_Centro_Custo', 'colunaTitulo' => 'Centro de custo', 'style' => 'width:1%'),
            array('colunaValor' => 'T268_Servico', 'colunaTitulo' => 'Serviço', 'callback' => 'retornaServicoCorreios', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'Serviço', 'style' => 'width:1%', 'colunaExibir' => true),
            array('colunaValor' => 'T268_Codigo_Rastreio', 'colunaTitulo' => 'Cód. de rastreio', 'style' => 'width:1%'),
            array('colunaValor' => 'T268_Data_Hora_Postagem', 'colunaTitulo' => 'Data postagem', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T268_Detalhes_Postagem', 'colunaTitulo' => 'Retorno postagem', 'style' => 'width:1%'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'colunaExibir' => false),
            array('colunaValor' => 'T268_Sigla_Triagem', 'colunaTitulo' => 'T268_Sigla_Triagem', 'colunaExibir' => false),
            array('colunaValor' => 'T268_Flag_Status', 'colunaTitulo' => 'T268_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário postagem', 'colunaExibir' => true, 'colunaAlinhar' => 'left'),
        );

		/**
		 * Definição da SQL
		 */
		$from = "T268";

		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T268_D024_Id
			LEFT JOIN D148 ON D148_Id=T268_D148_Id
			LEFT JOIN D036 ON D036_Id=T268_D036_Id
			LEFT JOIN D018 ON IF(IFNULL(T268_D148_Id,0) > 0, D018_Id=D148_D018_Id, D018_Id=D024_D018_Id)
			LEFT JOIN D020 ON IF(IFNULL(T268_D148_Id,0) > 0, D020_Id=D148_D020_Id, D020_Id=D024_D020_Id)
			LEFT JOIN T007 ON T007_Id=T268_T007_Id
			LEFT JOIN D022 ON D022_Id=T007_D022_Id
			LEFT JOIN C007 as Vendedor on Vendedor.C007_Id=T268_C007_Id_Postagem 
			WHERE T268_C004_Id = '{$g['empresaAtual']}'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est002CadastrarPostagem'),
			array('titulo' => 'Postar', 'id' => 'cad002PrePostagem'),
			);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*echo <<<EOT
			<div class="buttonsBottomTop">
                <button type="button" id="est002CadastrarPostagem">Incluir</button>
                <button type="button" id="cad002PrePostagem">Postar</button>
			</div>
EOT;*/
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T268_Id' => array('titulo' => 'Chave', 'tipo' => 'numero'),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = base64_encode($extra);

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

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#est002CadastrarPostagem').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar pacote', '/est/est002/content/est002ContentCadastroPacote/','', [550,300]);
				});

				$('#cad002PrePostagem').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est002/grid_func-ajax/verificarPacotesPrePostagem/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
					    $('#{$g['divId']}').hideLoading();
						if (request.code) {
							var buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est002/grid_func-ajax/prePostagemPacotes/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
										if (request.code) {
											var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    					dialogConfirm('Ok', request.data, buttons);
										} else {
											var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    					dialogConfirm('Erro', request.data, buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							dialogConfirm('Atenção', request.data, buttons);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    	dialogConfirm('Erro', request.data, buttons);
						}
				    });
				});
			} </script></div>
EOT;
        }

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T268_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pacote ' + id, '/est/est002/content/est002ContentCadastroPacote/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [550,300]);
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

	case 'est002GridConferenciaVolumes':
		$campoSeparada  = " IF(D002A_Utiliza_Conversao = 'S', T006A_Quantidade_Conversao_Separacao, T006_Quantidade_Separacao) AS QTDE_SEPARACAO ";
		$campoPedido    = " IF(D002A_Utiliza_Conversao = 'S', T006A_Quantidade_Conversao,           T006_Quantidade)           AS QTDE_PEDIDO ";

		$grid = array(
            array('colunaValor' => 'T313_Id', 'colunaTitulo' => 'Chave', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Item Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D002A_Utiliza_Conversao', 'colunaTitulo' => 'Utiliza Conversao', 'chavePrimaria' => false, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D109_Primeiro.D109_Nome', 'colunaTitulo' => 'D109_Nome_Primeiro', 'colunaExibir' => false),
            array('colunaValor' => 'D109_Segundo.D109_Nome',  'colunaTitulo' => 'D109_Nome_Segundo',  'colunaExibir' => false),

            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'Item', 'colunaAlinhar' => 'center', 'style' => 'width:30px;'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:30px;'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D001_Peso_Unitario_Bruto', 'colunaTitulo' => 'Peso Bruto',   'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:100px;'),

            array('colunaValor' => 'T313_Quantidade', 'colunaTitulo' => 'Qtde Conferida', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'width:100px; background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T1313_Quantidade', 'colunaEditarInsert' => true),
			array('colunaValor' => 'T313_Nr_Volume', 'colunaExibir'=>false, 'colunaTitulo' => 'Qtde Volumes', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'width:100px; background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T1313_Nr_Volume', 'colunaEditarInsert' => true),
			array('colunaValor' => 'T313_Lote', 'colunaExibir'=>false, 'colunaTitulo' => 'Lote Produção', 'colunaAlinhar' => 'right','colunaEditarUppercase' => true, 'colunaEditar' => true, 'style' => 'width:100px; background: var(--celula-amarelo);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T1313_Lote', 'colunaEditarInsert' => true),
            array('colunaValor' => 'T256_T255_Id', 'colunaTitulo' => 'Op Produção','colunaExibir' => false, 'colunaAlinhar' => 'right'),


            array('colunaValor' => $campoSeparada, 'colunaTitulo' => 'Qtde Separada', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:100px;'),
            array('colunaValor' => $campoPedido,   'colunaTitulo' => 'Qtde Pedido',   'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:100px;'),
			array('colunaValor' => 'T313_D109_Id_Primeiro', 'colunaTitulo' => 'Separador 1', 'callback' => 'T313_D109_Id_Primeiro', 'colunaAlinhar' => 'left', 'style' => 'width:100px;'),
			array('colunaValor' => 'T313_D109_Id_Segundo',  'colunaTitulo' => 'Separador 2', 'callback' => 'T313_D109_Id_Segundo',  'colunaAlinhar' => 'left', 'style' => 'width:100px;')
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T006";

		$extra  = " left join T313  on T006_Id = T313_T006_Id ";
		$extra .= " left join T005  on T005_Id = T006_T005_Id ";
		$extra .= " left join T006A on T006_Id = T006A_T006_Id ";
		$extra .= " left join T256  on T256.T256_T006_Id = T006_Id ";
	    $extra .= " left join D009  on D009_Id = T006_D009_Id ";
		$extra .= " left join D049  on D049_Id = D009_D049_Id ";
		$extra .= " left join D001  on D001_Id = D049_D001_Id ";
		$extra .= " left join D002  on D002_Id = D001_D002_Id ";
		$extra .= " left join D002A on D002_Id = D002A_D002_Id ";
		$extra .= " left join D109 as D109_Primeiro on D109_Primeiro.D109_Id = T313_D109_Id_Primeiro ";
		$extra .= " left join D109 as D109_Segundo  on D109_Segundo.D109_Id = T313_D109_Id_Segundo ";
		$extra .= " WHERE T005_Id = '{$r_T005_Id}' ";

		/**
         * Botões
         */
        $botoes = array();
		echo Botoes::processar($botoes);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
				if ($array['todosCampos']['D002A_Utiliza_Conversao'] == 'S') {
					return 'linhaVerdeEscuro';
				} else {
					return 'linhaAzul';
				}
			}
		}

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function colunaEditarCallbackJS_T1313_Quantidade(pks, campoEditado, valorAntigo, novoValor) {
				chave = pks.split('|');
				var T313_Id = chave.length >= 1 ? chave[0].replace(/T313_Id-/, '') : '';
				var T005_Id = chave.length >= 2 ? chave[1].replace(/T005_Id-/, '') : '';
				var T006_Id = chave.length >= 3 ? chave[2].replace(/T006_Id-/, '') : '';
				var D009_Id = chave.length >= 4 ? chave[3].replace(/D009_Id-/, '') : '';
				$.getJSON('/est/est002/grid_func-ajax/gravarT313/?ajax=true&T313_Id='    	+ encodeURIComponent(T313_Id) +
																		  '&T005_Id='    	+ encodeURIComponent(T005_Id) +
																		  '&T006_Id='    	+ encodeURIComponent(T006_Id) +
																		  '&D009_Id='    	+ encodeURIComponent(D009_Id) +
																		  '&campoEditado='	+ encodeURIComponent(campoEditado) +
																		  '&valorAntigo='	+ encodeURIComponent(valorAntigo) +
																		  '&novoValor='		+ encodeURIComponent(novoValor) +
																		  '&callback=?', function(request) {
					if (!request.code) {
						divRefresh('{$g['divId']}'); // grid
						dialogConfirm('Erro', request.data);
					}
				divRefresh('{$g['divId']}'); // grid
				});
			}
		} </script></div>
EOT;

/****************************************** CARLOS CARVALHO *****************************************************/
echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function colunaEditarCallbackJS_T1313_Nr_Volume(pks, campoEditadoNrVolume, valorAntigoNrVolume, novoValorNrVolume) {
		chave = pks.split('|');
		var T313_Id = chave.length >= 1 ? chave[0].replace(/T313_Id-/, '') : '';
		var T005_Id = chave.length >= 2 ? chave[1].replace(/T005_Id-/, '') : '';
		var T006_Id = chave.length >= 3 ? chave[2].replace(/T006_Id-/, '') : '';
		var D009_Id = chave.length >= 4 ? chave[3].replace(/D009_Id-/, '') : '';
		$.getJSON('/est/est002/grid_func-ajax/gravarT313/?ajax=true&T313_Id='    	       + encodeURIComponent(T313_Id) +
																  '&T005_Id='    	       + encodeURIComponent(T005_Id) +
																  '&T006_Id='    	       + encodeURIComponent(T006_Id) +
																  '&D009_Id='    	       + encodeURIComponent(D009_Id) +
																  '&campoEditadoNrVolume=' + encodeURIComponent(campoEditadoNrVolume) +
																  '&valorAntigoNrVolume='  + encodeURIComponent(valorAntigoNrVolume) +
																  '&novoValorNrVolume='	   + encodeURIComponent(novoValorNrVolume) +
																  '&callback=?', function(request) {
			if (!request.code) {
				divRefresh('{$g['divId']}'); // grid
				dialogConfirm('Erro', request.data);
			}
		divRefresh('{$g['divId']}'); // grid
		});
	}
} </script></div>
EOT;


echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function colunaEditarCallbackJS_T1313_Lote(pks, campoEditadoLote, valorAntigoLote, novoValorLote) {
				chave = pks.split('|');
				var T313_Id = chave.length >= 1 ? chave[0].replace(/T313_Id-/, '') : '';
				var T005_Id = chave.length >= 2 ? chave[1].replace(/T005_Id-/, '') : '';
				var T006_Id = chave.length >= 3 ? chave[2].replace(/T006_Id-/, '') : '';
				var D009_Id = chave.length >= 4 ? chave[3].replace(/D009_Id-/, '') : '';
				$.getJSON('/est/est002/grid_func-ajax/gravarT313/?ajax=true&T313_Id='    	    + encodeURIComponent(T313_Id) +
																		  '&T005_Id='    	    + encodeURIComponent(T005_Id) +
																		  '&T006_Id='    	    + encodeURIComponent(T006_Id) +
																		  '&D009_Id='    	    + encodeURIComponent(D009_Id) +
																		  '&campoEditadoLote='	+ encodeURIComponent(campoEditadoLote) +
																		  '&valorAntigoLote='   + encodeURIComponent(valorAntigoLote) +
																		  '&novoValorLote='	    + encodeURIComponent(novoValorLote) +
																		  '&callback=?', function(request) {
					if (!request.code) {
						divRefresh('{$g['divId']}'); // grid
						dialogConfirm('Erro', request.data);
					}
				divRefresh('{$g['divId']}'); // grid
				});
			}
		} </script></div>
EOT;

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

	case 'est002VolumesConsultaCapa':
		$grid = array(
            array('colunaValor' => 'T314_Id', 'colunaTitulo' => 'Chave', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirVolume', 'callbackParameter' => array('divJanela' => $_REQUEST['divJanela']), 'style' => 'width:15px;'),

            array('colunaValor' => 'T314_Identificador', 'colunaTitulo' => 'Identificador', 'colunaExibir' => false),
            array('colunaValor' => 'T314_Sequencia',     'colunaTitulo' => 'Sequência',     'colunaExibir' => false),
            array('colunaValor' => 'T314_Volume',        'colunaTitulo' => 'Volume',        'colunaAlinhar' => 'left'),

            array('colunaValor' => 'T314_Peso_Bruto',   'colunaTitulo' => 'Peso Total Bruto',   'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:130px;'),
            array('colunaValor' => 'T314_Peso_Liquido', 'colunaTitulo' => 'Peso Total Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:130px;'),
        );
		
		/**
		 * Definição da SQL
		 */
		$from  = "T314";
		$extra = " WHERE T314_T005_Id = '{$r_T005_Id}' ";

		/**
         * Botões
         */
        $botoes = array();
		echo Botoes::processar($botoes);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		

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
					// Deixa a linha em destaque
					var grid = $('#{$g['divId']} .grid');
					var classCss = 'linhaAzul linhaNegrito linhaFundoAzulClaro';
					grid.find('tbody > tr').removeClass(classCss);
					$(this).addClass(classCss);

					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T314_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('-');
					var acaoId = id[1].replace(chavePrimaria , '-', '');
					// Ação: recarregar GRID (com novo acaoId)
					divSetarAcaoId('{$r_divReload}', acaoId);
					divRefresh('{$r_divReload}');
			    });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

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

	case 'est002VolumesConsultaItems':
		$quantidade = " IF(D002A_Utiliza_Conversao = 'S', T315_Quantidade_Conversao, T315_Quantidade) AS Quantidade ";

		$grid = array(
            array('colunaValor' => 'T315_Id', 'colunaTitulo' => 'Chave', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T315_T314_Id', 'colunaTitulo' => 'Chave', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D002A_Utiliza_Conversao', 'colunaTitulo' => 'Utiliza Conversao', 'chavePrimaria' => false, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Codigo'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),

            array('colunaValor' => $quantidade,         'colunaTitulo' => 'Quantidade',   'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:90px;'),
			array('colunaValor' => 'T315_Lote',         'colunaExibir' =>  false,         'colunaTitulo' => 'Lote Produção',   'colunaAlinhar' => 'right', 'style' => 'width:90px;'),
            array('colunaValor' => 'T315_Peso_Bruto',   'colunaTitulo' => 'Peso Bruto',   'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:90px;'),
            array('colunaValor' => 'T315_Peso_Liquido', 'colunaTitulo' => 'Peso Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:90px;'),
        );
		
		/**
		 * Definição da SQL
		 */
		$from  = "T315";
		$extra  = " left join T314  on T314_Id = T315_T314_Id ";
		$extra .= " left join T006  on T006_Id = T315_T006_Id ";
		$extra .= " left join D009  on D009_Id = T315_D009_Id ";
		$extra .= " left join D049  on D049_Id = D009_D049_Id ";
		$extra .= " left join D001  on D001_Id = D049_D001_Id ";
		$extra .= " left join D002  on D002_Id = D001_D002_Id ";
		$extra .= " left join D002A on D002_Id = D002A_D002_Id ";
		$extra .= " WHERE T314_Id = '{$r_acaoId}' ";

		/**
         * Botões
         */
        $botoes = array();
		echo Botoes::processar($botoes);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
				if ($array['todosCampos']['D002A_Utiliza_Conversao'] == 'S') {
					return 'linhaVerdeEscuro';
				} else {
					return 'linhaAzul';
				}
			}
		}

		echo <<<EOT
EOT;

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
