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
require_once('prog/est/est003/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est003/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est003GridNaoLiberado':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções','callback' => 'campoOpcoes' ),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true ),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente' ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e','CUPOM FISCAL')))))) Tipo_Documento", 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação' ),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora' ),
			array('colunaValor' => 'D006_Descricao_Comercial','colunaExibir' => false, 'colunaTitulo' => 'CFOP' ), 
			array('colunaValor' => 'D006_Codigo_CFOP','colunaTitulo' => 'CFOP', 'callback' => 'campoCFOP'),
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
			where (T005_C004_Id={$g['empresaAtual']}
			and T005_Flag_Status='9')
			GROUP BY T005_Id
			ORDER BY T005_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="">Etiqueta Avulsa</button>
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
		
	
		/**
		 Botões (Versão Acima do GRID)
		
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est003/content/est003ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	
	case 'est003GridAguardandoProdCompra':
	$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções','callback' => 'OpcoesAguardandoProdCompra' ),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true ),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente' ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e','CUPOM FISCAL')))))) as Tipo_Documento", 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação' ),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora' ),
			array('colunaValor' => 'D006_Descricao_Comercial','colunaExibir' => false, 'colunaTitulo' => 'CFOP' ), 
			array('colunaValor' => 'D006_Codigo_CFOP','colunaTitulo' => 'CFOP', 'callback' => 'campoCFOP'),
			array('colunaValor' => 'T005_Data_Hora_Almoxarifado', 'colunaTitulo' => 'Hora Almox', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Nome_Liberou', 'colunaTitulo' => 'Liberou' ),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,2) as T005_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'Reenvio','colunaExibir'=>false),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'IdCliente', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'IdRepresentante', 'colunaExibir'=>false ),
			array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'TipoTransp', 'colunaExibir'=>false),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Nome_Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaExibir'=>false ),
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
	and T005_Flag_Status='23')
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est003/content/est003ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est003GridDisponivelParaFaturar':
						$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Opções','callback' => 'campoOpcoes' ),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true ),
			array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente' ),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T005_Data_Saida', 'colunaTitulo' => 'Saída', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e','CUPOM FISCAL')))))) Tipo_Documento", 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => 'T005_Observacao_1', 'colunaTitulo' => 'Observação' ),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora' ),
			array('colunaValor' => 'D006_Descricao_Comercial','colunaExibir' => false, 'colunaTitulo' => 'CFOP' ), 
			array('colunaValor' => 'D006_Codigo_CFOP','colunaTitulo' => 'CFOP', 'callback' => 'campoCFOP'),
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
	where (T005_C004_Id={$g['empresaAtual']}
	and T005_Flag_Status='22')
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est003/content/est003ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est003GridDivergencias':
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T185_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T005_Estoque_BOX', 'colunaTitulo' => 'Box', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtd. Pedido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Qtd. Separado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque Atual', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background-color: lightblue;'),
            array('colunaValor' => 'group_concat(distinct T066_Id)', 'colunaTitulo' => 'Local', 'callback' => 'Estoque_T066_Ids', 'style' => 'width: 1%;'),
            array('colunaValor' => 'Campo DE', 'colunaTitulo' => 'D.E', 'colunaTipo' => 'livre', 'callback' => 'campoDE', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T185_Tipo_Divergencia', 'colunaTitulo' => 'Tipo', 'callback' => 'tipoDivergencia'),
            array('colunaValor' => 'Comprar', 'colunaTitulo' => 'Comprar', 'callback' => 'validaCampoComprar', 'colunaTipo' => 'livre', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT("Pedido: ", T005_Id, " - ", D024_Nome_Empresa) as TituloPedido', 'colunaTitulo' => 'TituloPedido', 'colunaExibir' => false),
        );

		/**
		 * Definição da SQL
		 */
		$from = "T006";
		
		if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
			$extra = <<<EOT
				left join D009 on D009_Id=T006_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D049_D001_Id=D001_Id
				left join T005 on T006_T005_Id=T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D006 on D006_Id=T006_D006_Id
				left join D037 on D037_Id=D001_D037_Id
				left join T066 on T066_D009_Id=D009_Id
				left join D004 on D004_Id=T066_D004_Id
				left join T001 on T001_T006_Id=T006_Id
				left join T185 on T185_T006_Id=T006_Id
				where (T006_Flag_Divergencia='S' and T006_Flag_Status!='3')
				AND (T006_C004_Id = '{$g['empresaAtual']}')
				AND T006_Quantidade > IFNULL(T006_Quantidade_Separacao,0)
				group by D009_Id
				order by T006_T005_Id,T006_Codigo_Produto
EOT;
		} else {
			$extra = <<<EOT
				left join D009 on D009_Id=T006_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D049_D001_Id=D001_Id
				left join T005 on T006_T005_Id=T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D006 on D006_Id=T006_D006_Id
				left join D037 on D037_Id=D001_D037_Id
				left join T185 on T185_T006_Id=T006_Id
				left join T066 on T066_Id=T185_T066_Id
				left join D004 on D004_Id=T066_D004_Id
				left join T001 on T001_T006_Id=T006_Id
				where (T006_Flag_Divergencia='S' 
				and T006_Flag_Status!='3'
				and	T185_Tipo_Divergencia > 0)
				group by D009_Id
				order by T006_T005_Id,T006_Codigo_Produto
EOT;
		}
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Imprimir', 'id' => 'est003GridDivImp',  'exibirCallback' => 'return isset($g["c029Ids"][103]);'),
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
            'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
            'T006_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'T006_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'D004_Local' => array('titulo' => 'Local', 'tipo' => 'texto'),
            'T006_Flag_Tipo_Produto' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                	array('title' => 'T005_Id', 'campoAgrupar' => 'T005_Id', 'campoMostrar' => 'TituloPedido', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
            	),
            ),
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
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		 gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'est003GridListaBox':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T005_Estoque_Box', 'colunaTitulo' => 'Box' ),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
			array('colunaValor' => '(select (sum(T006_Quantidade_Separacao)/sum(T006_Quantidade)*100) from T006 where T006_T005_Id=T005_Id) as Percentual_Separado', 'colunaTitulo' => 'Separado', 'callback' => 'campoSeparado'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => '', 'colunaExibir' => false ),
			array('colunaValor' => "T005_Status_Pedido(T005_Flag_Status,1) as Status", 'colunaTitulo' => 'Status' ),
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
		left join D024 on D024_Id=T005_D024_Id
		where T005_C004_Id={$g['empresaAtual']}
		and (T005_Flag_Status=25)
		and T005_Flag_Status!='4'
		and (T005_Estoque_Box!='' or T005_Flag_Status=3 or T005_Flag_Status=25)
		order by T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Estoque_BOX' => array('titulo' => 'Box', 'tipo' => 'texto'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Flag_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est003/content/est003ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
		
	case 'est003GridProdutosPedido':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true ),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto' ),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição' ),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte'),
			array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq' ),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id) as OC', 'colunaTitulo' => 'OC', 'callback' => 'campoOC' ),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado' ), 
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
			where T006_T005_Id={$r_acaoId}
			and 
			(T006_T006_Id is null or T006_T006_Id<=0)
			and 
			(T006_Quantidade>0)
			and 
			T006_Flag_Divergencia!='S'
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
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est003/content/est003ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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
	
	case 'est003GridCatalogo':
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		
		//mysql_query("DELETE FROM T198");
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'D003_Nome_Linha' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
            'ItensSelecionados' => array('titulo' => 'Marcados', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => ' AND T198_D009_Id IS NOT NULL AND T198_C007_Id=\'63\' '),
                array('title' => 'Não', 'value' => ' AND T198_D009_Id IS NULL OR T198_C007_Id!=\'63\' '),
              ),
              'naoGerarExtra' => '1', 'exibir' => 's'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estoque PE', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Modelo', 'campoAgrupar' => 'D001_D003_Id', 'campoMostrar' => 'D003_Nome_Linha', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
              ),
            ),
            '(SELECT 
                      IFNULL((
                          SELECT 
                              SUM(T055_Quantidade) 
                          FROM 
                              T055 
                          LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                          WHERE 
                              T055_D009_Id=D009_Id 
                              AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL 30 DAY))
                              AND T075_Data_Previsao != \'0000-00-00\'
                              AND IFNULL(T075_Data_Estoque[[VIRGULA]]\'0000-00-00\') = \'0000-00-00\'
                      ),0)-
                      IFNULL((
                          SELECT 
                              SUM(T006_Quantidade) 
                          FROM 
                              T006 
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                          WHERE T006_D009_Id=D009_Id                   
                              AND T005_Flag_Status!=\'5\'
                              AND T005_Flag_Status!=\'7\' 
                              AND T005_Flag_Status!=\'8\' 
                              AND T005_Flag_Status!=\'11\' 
                              AND T006_T075_Id IN 
                          (
                              SELECT 
                                  T075_Id 
                              FROM 
                                  T075 
                              LEFT JOIN T055 ON T075_Id=T055_T075_Id 
                              LEFT JOIN D009 ON T055_D009_Id=D009_Id
                              WHERE 
                                  T055_D009_Id=D009_Id 
                                  AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL 30 DAY))
                                  AND T075_Data_Previsao != \'0000-00-00\'
                                  AND IFNULL(T075_Data_Estoque[[VIRGULA]]\'0000-00-00\') = \'0000-00-00\'
                          ) 
                      ),0))' => array('titulo' => 'Estoque MP', 'tipo' => 'numero', 'exibir' => 's'),
            'D009_Quantidade_Pedido(D009_Id,1)' => array('titulo' => 'Pedidos', 'tipo' => 'numero'),
            'D009_Observacao_Venda' => array('titulo' => 'Observacao', 'tipo' => 'texto'),
        );
		$g['ui']->gridFiltroExtra(false, $gridFiltro, '');
	
		$C008_Id = $r_acaoId;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'Foto', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Modelo', 'colunaAgrupar' => '1', 'style' => 'width: 100px;text-align: center;background-color: white;', 'callback' => 'mostrarImagemD003', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'IDD009', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T198_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'D001_D003_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'C004_Id', 'chavePrimaria' => '', 'colunaExibir' => false),
            array('colunaValor' => 'CampoBx', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Orç', 'callback' => 'checkboxGerarOrcamentoT198', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'verPedidosDoProduto', 'style' => 'width:1%;'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'PE', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callback' => 'estoqueMP'),
            array('colunaValor' => 'estoqueLongoPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'LP', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueLP', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Estoque_Defeito', 'colunaTitulo' => 'DF', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Estoque_Mostruario', 'colunaTitulo' => 'DM', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'concat(C004_Id,\'-\',C004_Nome_Abreviado) as C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Observacao_Venda', 'colunaTitulo' => 'Observação', 'style' => 'width:25%;color: var(--BGVermelho);font-weight:normal;', 'colunaEditar' => true),
        );

		$from = 'D001';
		$extra = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			left join D041 on D041_D049_Id=D049_Id
			left join C004 on C004_Id=D009_C004_Id
			left join T198 on T198_D009_Id=D009_Id AND T198_C007_Id='{$g['usuarioAtual']}'
			where (D009_C004_Id='{$g['usuarioAtual']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			GROUP BY D009_Id ORDER BY D001_Descricao_Produto ASC
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
			<!--<button type="button" id="crm001GerarOrcamentoTabCatalogo">Gerar Orçamento</button>-->
			<!--<button type="button" id="crm001DesmarcarTodosCheck">Desmarcar todos</button>-->
			<!--<button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>-->
			<!--<button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#crm001DesmarcarTodosCheck').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/crm/crm001/grid_func-ajax/crm001checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									divRefresh('{$g['divId']}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#' + '{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Você tem certeza?', 'Você tem certeza que deseja desmacar todos os produtos selecionados?', \$buttons);
				});
				
				$('#crm001GerarOrcamentoTabCatalogo').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_CRM=' + encodeURIComponent('{$D024_Id}') + '&tabCatalogoCRM=true', [650,480]);
				});
			} </script></div>
EOT;
		
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
			   return 'linhaCinza';
			}
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
			   return 'linhaVermelho';
			}
		}	

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$filtroObrigatorio = true;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
					   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
				   });
				   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
	
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
		
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
	
	case 'est003GridProdutosDefeitos':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'excluirProdutoDefeito', 'style' => "width: 1%;"),
			array('colunaValor' => 'T206_Flag_Marcado', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'callback' => 'validaFlagMarcado', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%;'),
			array('colunaValor' => 'T206_Id', 'colunaTitulo' => 'T206_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T206_D009_Id', 'colunaTitulo' => 'T206_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T206_Flag_Tipo', 'colunaTitulo' => 'T206_Flag_Tipo', 'colunaExibir' => false),
			array('colunaValor' => 'T206_D169_Id', 'colunaTitulo' => 'T206_D169_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T206_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;','colunaDimensao' => 'S'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'style' => 'width: 1%;','colunaDimensao' => 'S'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%;','colunaDimensao' => 'S'),			
			array('colunaValor' => 'T206_Quantidade', 'colunaTitulo' => 'Quantidade', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center','colunaMetrica' => 'S'),		
			array('colunaValor' => 'T006_Quantidade_Pedidos(T206_Id, T206_D169_Id) as pedidos', 'colunaTitulo' => 'Pedidos','colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedidos', '/ven/ven002/content/ven002ContentProdutosDefeitos/', 'T206_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaMetrica' => 'S'),
			array('colunaValor' => 'D009_Quantidade_OE(T206_D009_Id, T206_Id, 0, T206_D169_Id)', 'colunaTitulo' => 'Saldo', 'style' => "width: 1%;", 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center','colunaMetrica' => 'S'),
			array('colunaValor' => 'T206_Descricao_Defeito', 'colunaTitulo' => 'Defeito'),
			array('colunaValor' => 'T206_D169_Id', 'colunaTitulo' => 'T206_D169_Id','colunaExibir' => false),			
			array('colunaValor' => 'D169_Descricao_Tipo_Produto', 'colunaTitulo' => 'Tipo','colunaDimensao' => 'S'),
			array('colunaValor' => 'D169_Sigla_Tipo_Produto', 'colunaTitulo' => 'Sigla','style' => 'width:1%',),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação', 'colunaExibir' => false),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Solicitante'),
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T206";
		
		$extra = <<<EOT
			left join T006 on T206_Id=T006_T206_Id
			left join T005 on T005_Id=T006_T005_Id
			left join D169 on D169_Id=T206_D169_Id
			left join D009 on D009_Id=T206_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			left join C007 on C007_Id=T206_C007_Id
			left join T066 on T066_Id = T206_T066_Id
			left join D004 on D004_Id = T066_D004_Id
			WHERE D009_C004_Id='{$g['empresaAtual']}'
			group by T206_Id
EOT;
	 	/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est003IncluirProduto'),
			array('titulo' => 'Gerar Pedido', 'id' => 'est003GerarPedido'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/

		echo <<<EOT
			<!--<div class="buttonsBottomTop">
				<button type="button" id="est003IncluirProduto">Incluir</button>
				<button type="button" id="est003GerarPedido">Gerar Pedido</button>				
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est003IncluirProduto').unbind('click').bind('click', function(data) {										
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Produto com Defeito', '/est/est003/content/est003ContentDefeito/', '', [460,365]);					
				});
				$('#est003GerarPedido').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar Pedido', '/est/est003/content/est003ContentGerarPedido/', '' , [500,300]);		
				
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Cód. Produto', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
			'T206_Data' => array('titulo' => 'Data', 'tipo' => 'data'),	
			'T206_Descricao_Defeito' => array('titulo' => 'Defeito', 'tipo' => 'texto'),
			'D169_Descricao_Tipo_Produto' => array('titulo' => 'Tipo', 'tipo' => 'texto'),

		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/** 
		* Totais
		*/
		$sqlT006 = mysql_query("SELECT SUM(T006_Quantidade) as Pedidos
				  FROM T006 
		     LEFT JOIN T206 ON T206_Id=T006_T206_Id 
			 LEFT JOIN D169 ON D169_Id=T206_D169_Id 
			     WHERE T206_D169_Id = D169_Id");
		$mT006 = mysql_fetch_array($sqlT006);
		$pedidos = round($mT006['Pedidos']);

		$extraTotal = gInsertExtraWhere($extra, "T206_D169_Id = D169_Id");
		$quantidade= $g['sqlAuto']->pegarSqlCampo("SUM(T206_Quantidade)", $from, $extraTotal);		
		$saldo = $g['sqlAuto']->pegarSqlCampo("SUM(D009_Quantidade_OE(T206_D009_Id, T206_Id, 0, T206_D169_Id))", $from, $extraTotal);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Saldo', 'valor' => "D009_Quantidade_OE(T206_D009_Id, T206_Id, 0, T206_D169_Id)", 'titulo' => 'Saldo'),
        );
		
		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<span class="item" style="text-align:right">Quantidade<br><input type="text" value="{$quantidade}" class="whiteBg" id="" size="11" readonly></span>-->
				<span class="item" style="text-align:right">Pedidos<br><input type="text" value="{$pedidos}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Saldo<br><input type="text" value="{$saldo}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
	
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
					
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T206_Id-/, '');
				$.getJSON('/est/est003/grid_func-ajax/est003ProdutosPedido/?ajax=true&T206_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
					if(request.code){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Produto: ' + id, '/est/est003/content/est003ContentDefeito/', '&acaoId=' + encodeURIComponent(id), [460,365]);
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
	break;
	
	case 'est003gridDefeito':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T206_Id', 'colunaTitulo' => 'T206_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T206_D009_Id', 'colunaTitulo' => 'T206_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T206_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%;'),			
			array('colunaValor' => 'T206_Quantidade', 'colunaTitulo' => 'Quantidade', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "Pedidos", 'colunaTitulo' => 'Pedidos', 'colunaTipo' => 'livre', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'callback' => "produtosDefeitoPedidos"),
			array('colunaValor' => "D009_Quantidade_DF(T206_D009_Id, T206_Id, 0) as Saldo", 'colunaTitulo' => 'Saldo', 'style' => "width: 1%;", 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T206_Descricao_Defeito', 'colunaTitulo' => 'Defeito'),			
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T206";
		
		$extra = <<<EOT
			left join D009 on D009_Id=T206_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			where T206_D009_Id = '{$r_acaoId}'
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
					
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T206_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Produto com Defeito: ' + id, '/est/est003/content/est003ContentDefeito/', '&acaoId=' + encodeURIComponent(id), [380,230]);
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

	case 'est003gridEstoqueOE':
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T206_Id', 'colunaTitulo' => 'T206_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T206_D009_Id', 'colunaTitulo' => 'T206_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T206_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%;'),			
			array('colunaValor' => 'T206_Quantidade', 'colunaTitulo' => 'Quantidade', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "Pedidos", 'colunaTitulo' => 'Pedidos', 'colunaTipo' => 'livre', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'callback' => "produtosDefeitoPedidos"),
			array('colunaValor' => "D009_Quantidade_OE(T206_D009_Id, T206_Id, 0, T206_D169_Id) as Saldo", 'colunaTitulo' => 'Saldo', 'style' => "width: 1%;", 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T206_Descricao_Defeito', 'colunaTitulo' => 'Defeito'),			
	 );
		
		/**
		 * Definição da SQL
		 */
		$from = "T206";
		
		$extra = <<<EOT
			left join D009 on D009_Id=T206_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			where T206_D009_Id = '{$r_acaoId}'
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
					
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T206_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Produto com Defeito: ' + id, '/est/est003/content/est003ContentDefeito/', '&acaoId=' + encodeURIComponent(id), [380,230]);
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

	case 'est003GridBalancoCiclico':
		/**
		 * Definição do grid tabela
		 */
		//configuração global de separar produto por empresa
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}
		$filtroObrigatorio = true;
		//$importacaoDados=array(''=>0);
		//$importacaoDadosFrom="T066";
		$acaoId = $r_acaoId;
	
		$grid = array(
			array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T066_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D083_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN int.', 'colunaEditar' => false),
            array('colunaValor' => 'D083_Codigo_Barras', 'colunaTitulo' => 'EAN Forn.', 'colunaEditar' => true),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T066_Quantidade_Balanco', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T066_Flag_Balanco', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'callback' => 'validaFlagBalanco', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'T066_Data_Balanco', 'colunaTitulo' => 'Ult. Balanço', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
            array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Loc', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal'),
		    array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Fís', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq.Líq', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueFora'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qte.OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'substr(D082_Marca,1,15)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Data_Fabricacao', 'colunaTitulo' => 'Fabricação', 'colunaEditar' => true, 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Data_Validade', 'colunaTitulo' => 'Validade', 'colunaEditar' => true, 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Lote', 'colunaTitulo' => 'Lote', 'colunaEditar' => true, 'style' => 'width:90px;', 'colunaExibir' => false),
            array('colunaValor' => 'D049_D024_Id', 'colunaTitulo' => 'Fornecedor', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D024_Nome_Empresa',
				'colunaEditarGeraSelect' => array('D024', 'D024_Id', 'D024_Nome_Empresa, D024_Id', "WHERE D024_Flag_Fornecedor = 'S' AND D024_Flag_Pre_Cadastro = 'N' AND D024_Flag_Ativo = 'S' ORDER BY D024_Nome_Empresa", true)
			),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => 'Freq. Venda', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Frequencia_Apos_Balanco', 'colunaTitulo' => 'Freq. após balanço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Data_Balanco', 'colunaTitulo' => 'Data balanço', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'T066_C007_Id_Marcou_Balanco', 'colunaTitulo' => 'T066_C007_Id_Marcou_Balanco', 'colunaExibir'=>false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'D004_Id', 'colunaTitulo' => 'D004_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T066_D009_Id', 'colunaTitulo' => 'T066_D009_Id', 'colunaExibir'=>false),
        );

		$from = 'D001';
		$extra = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id=D049_D024_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			left join C007 ON C007_Id=T066_C007_Id_Marcou_Balanco
			where
			(D009_C004_Id='{$g['empresaAtual']}'
			or 
			D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			{$filtro}
			group by T066_Id ORDER BY D009_Frequencia_Venda DESC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'D001_Flag_Ativo' => array('titulo' => 'Ativo?', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
            'D009_Frequencia_Venda' => array('titulo' => 'Freq.', 'tipo' => 'numero'),
            'D009_Frequencia_Apos_Balanco' => array('titulo' => 'Freq. balanço', 'tipo' => 'numero', 'exibir' => false),
            'T066_Data_Balanco' => array('titulo' => 'Ult. Balanço', 'tipo' => 'data'),
            'T066_Quantidade_Estoque' => array('titulo' => 'Estq.Locação', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Produto', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'T066_Quantidade_Balanco' => array('titulo' => 'Qte.Balanço', 'tipo' => 'numero'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto'),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto'),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'ABC', 'tipo' => 'texto'),
            'T066_Flag_Balanco' => array('titulo' => 'Marcado? (S/N)', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário marcou', 'tipo' => 'texto'),
        );    


		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
				return 'linhaCinza';
			}
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
				return 'linhaVermelho';
			}
		}
		
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

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
		$sql_ = base64_encode($geradoSql);

		$mostraBotao = true;
		if (!isset($g['c029Ids'][206]) || $acaoId > 0) {
			$mostraBotao = false;
		}

		// Botões
        $botoes = array(
            array('titulo' => 'Processar Balanço', 'id' => 'est003Balanco', 'exibirCallback' => "return $mostraBotao;"),
			array('titulo' => 'Mobile', 'id' => 'est003AbrirBalancoCelular', 'janelaUrl' => '/balanco/qr-login.php', 'janelaTamanho' => '[500,500]', 'exibirCallback' => "return $mostraBotao;"),
			array('titulo' => 'Importar CSV', 'id' => 'est003BalancoImportarCSV', 'janelaUrl' => '/est/est003/content/est003ContentBalancoImportarCSV/', 'janelaTamanho' => '[800,510]'),
		);
        echo Botoes::processar($botoes);


		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est003Balanco').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est003/grid_func-ajax/est003Balanco/?ajax=true&sql='+ encodeURIComponent("{$sql_}") +'&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Ok', request.data);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Você tem certeza que deseja processar o balanço?', \$buttons);
				});
				//$('#est003BalancoImportarCSV').bind('click', function(data) {
				//	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Balanço - Importar CSV', '/est/est003/content/est003ContentBalancoImportarCSV/?ajax=true', '&acaoId=' + encodeURIComponent('') + '', [800,550]);
				//});

			} </script></div>
EOT;
		/**
		* Totalizador (Versão Acima do GRID)
		*/
		/*$TotalItens = $g['sqlAuto']->pegarSqlCampo("count(DISTINCT D001_Id)", $from, gRetirarGroupBy($extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$TotalItens}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

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

	case 'est003GridPrecoLoja':
		/**
		 * Definição do grid tabela
		 */
		//configuração global de separar produto por empresa
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}
	
		$grid = array(
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Flag_Preco_Loja', 'colunaTitulo' => '', 'callback' => 'gCheckbox', 'callbackParameter' => array('D009_Flag_Preco_Loja', 'N|S'), 'style' => 'width: 1%', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal; width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'style' => 'font-weight:normal; width: 1%;'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D009_Preco_Loja', 'colunaTitulo' => 'Preço Loja', 'style' => 'width: 1%; background-color: lightblue;', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço Novo', '/ven/ven001/outros/demonstrativoPrecoVenda/', 'D009_Id', 'right', 2, 'D009', '', '450')),
            array('colunaValor' => 'D009_Preco_Loja_Real', 'colunaTitulo' => 'Novo Preço', 'style' => 'background-color: var(--celula-verde); width: 1%;', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço Novo', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'D009_Id', 'right', 2, 'D009_Real')),
            array('colunaValor' => '((D009_Preco_Loja_Real-D009_Preco_Loja)/D009_Preco_Loja)*100', 'colunaTitulo' => '%', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => '(select T066_Quantidade_Estoque from T066 where T066_D009_Id=D009_Id and T066_D004_Id=16383)', 'colunaTitulo' => 'Estq. Loja', 'style' => 'width: 1%;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'group_concat(distinct D004_Local)', 'colunaTitulo' => 'Locação','colunaExibir' => false),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),


        );

		$from = 'D001';
		$extra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D049_D082_Id=D082_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D060 on D001_D060_Id=D060_Id
			left join T239 on T239_D009_Id=D009_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			where (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			{$filtro}
			AND D009_C004_Id = '{$g['empresaAtual']}'
			GROUP BY D009_Id
EOT;
		/**
		* Filtro
		*/                                          
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto'),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D001_Codigo_Barras,D001_Codigo_Barra1' => array('titulo' => 'EAN', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            '((D009_Preco_Loja_Real-D009_Preco_Loja)/D009_Preco_Loja)*100' => array('titulo' => 'Percentual', 'tipo' => 'moeda'),
			'(select T066_Quantidade_Estoque from T066 where T066_D009_Id=D009_Id and T066_D004_Id=16383)' => array('titulo' => 'Estq.Loja', 'tipo' => 'moeda'),
			'D009_Flag_Preco_Loja' => array('titulo' => 'Marcado? (S/N)', 'tipo' => 'texto'),
			'T239_Data_Hora' => array('titulo' => 'Alteração preço', 'tipo' => 'data'),
			'D004_Local' => array ('titulo' => 'Locação', 'tipo' => 'texto'),
        );
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
				return 'linhaCinza';
			}
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
				return 'linhaVermelho';
			}
		}

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Pegar apenas a SQL
		$geradoSql = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 0, false, false, true);
		$sql = urlencode(base64_encode($geradoSql));

		// Sql utilizado em botões processa etapas
		$sqlSelecao = gComprimeSql("SELECT D001_Id, D009_Id FROM $from $extra");

		/**
		* Botões (Versão Acima do GRID)
		*/

		// Carregando combo com os dados das impressoras
		$impressaoJava = $g['C031']['impressaoJava'];
		if($impressaoJava == 2){
			$optionValue = "<option value=>[Escolha a impressora]</option>";
			$D175 = mysql_query("SELECT D175_Id, D175_Nome FROM D175 ");
			while($mD175 = mysql_fetch_array($D175)){

				$optionValue .= "<option value=" . $mD175['D175_Id'] . ">" . $mD175['D175_Nome'] . "</option>";
			}

			$select = '<select style="width:250px; height:24px" id="impressoras">' . $optionValue . '</select>';
		}else{
			$select = '';
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Imprimir Etiquetas Novo Preço', 'id' => 'est003ImpNovasEtiquetaPrecoNovo'),
			array('titulo' => 'Imprimir Etiquetas Preço Loja', 'id' => 'est003ImpNovasEtiqueta'),
			array('titulo' => 'Aplicar Novos Preços', 'id' => 'est003AplicarNovosPrecos'),
			array('titulo' => 'Histórico de Alterações', 'id' => 'est003HistoricoAlteracoes'),
			array('titulo' => 'Atualizar estoque rápido', 'id' => 'est003AtualizarEstoque'),
		);
		echo Botoes::processar($botoes);

		/*
		// Botão Atualizar Estoque
		$buttonAtualizaEstoque = gGeraBotao("Atualizar estoque rápido","est003AtualizarEstoque");

		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est003ImpNovasEtiquetaPrecoNovo">Imprimir Etiquetas Novo Preço</button>
				<button type="button" id="est003ImpNovasEtiqueta">Imprimir Etiquetas Preço Loja</button>
				<button type="button" id="est003AplicarNovosPrecos">Aplicar Novos Preços</button>
				<button type="button" id="est003HistoricoAlteracoes">Histórico de Alterações</button>
				$buttonAtualizaEstoque
			</div>
EOT;*/

		echo gProcessaEtapas('Reprocessa estoque','CAD002', "cad002AtualizaEstoque()", "$sqlSelecao",'est003AtualizarEstoque');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est003ImpNovasEtiquetaPrecoNovo').unbind('click').bind('click', function(data) {
					if($impressaoJava == 1){
						var buttons = { 
							"Fechar": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Imprimir Novas Etiquetas (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava"></div>', buttons);
						setTimeout(function() {
							impressaoEtiquetas($('#est003ImpNovasEtiqJava'), '/sistema/impressao/etiquetas/precolojaNovoPreco/', 0, 'precolojaNovoPreco', 1, false, '&sql={$sql}');
						}, 100);
						impressaoPreCarregarApplet();
					}else if($impressaoJava == 2){
						var buttons = { 
							"Imprimir": function() { 
								var impressora = $(this).parent().find('select').val();
								var sql = "$sql";
								$.ajax('/sistema/impressao/etiquetas/precolojaNovoPreco/?ajax=true&acaoId=0&imprimir=precolojaNovoPreco&sql="' + sql + '"&impressora=' + encodeURIComponent(impressora));
								$(this).dialog("destroy").remove();
								dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
							},
							"Fechar": function() {
								$(this).dialog("destroy").remove(); 
							}
						};
						dialogConfirm('Imprimir Etiquetas Preço Novo (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava">$select</div>', buttons);
					}
				});
				$('#est003ImpNovasEtiqueta').unbind('click').bind('click', function(data) {
					if($impressaoJava == 1){
						var buttons = { 
							"Fechar": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Imprimir Novas Etiquetas (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava"></div>', buttons);
						setTimeout(function() {
							impressaoEtiquetas($('#est003ImpNovasEtiqJava'), '/sistema/impressao/etiquetas/precoloja/', 0, 'precoloja', 1, false, '&sql={$sql}');
						}, 100);
						impressaoPreCarregarApplet();
					}else if($impressaoJava == 2){
						var buttons = { 
							"Imprimir": function() { 
								var impressora = $(this).parent().find('select').val();
								var sql = "$sql";
								$.ajax('/sistema/impressao/etiquetas/precoloja/?ajax=true&acaoId=0&imprimir=precoloja&sql="' + sql + '"&impressora=' + encodeURIComponent(impressora));
								$(this).dialog("destroy").remove();
								dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
							},
							"Fechar": function() {
								$(this).dialog("destroy").remove(); 
							}
						};
						dialogConfirm('Imprimir Etiquetas Preço Loja (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava">$select</div>', buttons);
					}
				});
				$('#est003AplicarNovosPrecos').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Sim": function() { 
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est003/grid_func-ajax/est003AplicarNovosPrecos/?ajax=true&sql={$sql}&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, buttons);
									divRefresh('{$g['divId']}');
									nova_janela('/est/est003/grid/est003GridPrecoLojaHistoricoItem/?ajax=true&generateHTML=true&generateHTMLTodos=true&acaoId=' + encodeURIComponent(request.uniqid) + '&D001_Codigo_Produto=1&D001_Descricao_Produto=1&T239_Preco_Loja_Antigo=1&T239_Preco_Loja_Novo=1&D001_Id=1',0,0,750,500,'yes','no','no','no','yes','no','Relatório de Conferência');
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, buttons);
								}
								$(this).dialog("destroy").remove();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Aplicar Novos Preços (Itens Selecionados)', 'Este processo irá aplicar nos novos preços para uso na Loja.<br/>Deseja continuar?', buttons);
				});
				$('#est003HistoricoAlteracoes').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Histórico de Alterações', '/est/est003/content/est003ContentHistoricoLojaPreco/', '&acaoId=' + encodeURIComponent('') + '', 'auto');
				});
			} </script></div>
EOT;

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*$totalItens = mysql_query("select D009_Id from $from $extra");
		$totalItens = mysql_num_rows($totalItens);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Itens<br><input type="text" value="{$totalItens}" class="whiteBg" id="" size="11" readonly></span>
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

	case 'est003GridPrecoLojaHistorico':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'T239_Processo_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T239_Data_Hora', 'colunaTitulo' => 'Data', 'style' => 'width: 1%;', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width: 1%;'),
			array('colunaValor' => 'COUNT(*) as Itens', 'colunaTitulo' => 'Itens Processados', 'style' => 'width: 1%;'),
		);

		$from = 'T239';
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id = T239_C007_Id
			LEFT JOIN D009 ON D009_Id = T239_D009_Id
			WHERE
				D009_C004_Id = '{$g['empresaAtual']}'
			GROUP BY T239_Processo_Id
			ORDER BY T239_Data_Hora DESC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T239_Data_Hora' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
		);                                            

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T239_Processo_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualização Preços Loja: ' + id, '/est/est003/content/est003ContentPrecoLojaHistoricoItem/', '&acaoId=' + encodeURIComponent(id) + '', [900,500]);
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

	case 'est003GridPrecoLojaHistoricoProdutos':
		$filtroObrigatorio = true;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'T239_Processo_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T239_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal;','callback' => 'abrirJanela', 'callbackParameter' => array(
				'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')
			),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T239_Preco_Loja_Antigo', 'colunaTitulo' => 'Preço Antigo', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'T239_Preco_Loja_Novo', 'colunaTitulo' => 'Preço Novo', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'T239_Data_Hora', 'colunaTitulo' => 'Data', 'style' => 'width: 1%;', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width: 1%;'),
		);

		$from = 'T239';
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id = T239_C007_Id
			LEFT JOIN D009 ON D009_Id = T239_D009_Id
			left join D049 on D049_Id = D009_D049_Id
			LEFT JOIN D001 on D001_Id = D049_D001_Id
			WHERE D009_C004_Id = '{$g['empresaAtual']}'
			ORDER BY T239_Data_Hora DESC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T239_Data_Hora' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);                                            

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T239_Processo_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualização Preços Loja: ' + id, '/est/est003/content/est003ContentPrecoLojaHistoricoItem/', '&acaoId=' + encodeURIComponent(id) + '', [900,500]);
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

	case 'est003GridPrecoLojaHistoricoItem':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'callback' => 'colunaCheckList', 'style' => 'width: 1%'),
			array('colunaValor' => 'T239_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D009_Flag_Preco_Loja', 'colunaTitulo' => '', 'callback' => 'gCheckbox', 'callbackParameter' => array('D009_Flag_Preco_Loja', 'N|S'), 'style' => 'width: 1%', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal;'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T239_Preco_Loja_Antigo', 'colunaTitulo' => 'Preço Antigo', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'T239_Preco_Loja_Novo', 'colunaTitulo' => 'Preço Novo', 'callback' => 'gCorrigeNumero'),
		);

		$from = 'T239';
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id = T239_C007_Id
			left join D009 on D009_Id=T239_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			WHERE T239_Processo_Id = '{$r_acaoId}'
			ORDER BY D001_Codigo_Produto ASC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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

	case 'est003GridReabastecimentoLoja':
		$filtroObrigatorio = true;

		$from = 'D009';
		$extra = <<<EOT
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join T066 on D009_Id=T066_D009_Id
			left join D004 on D004_Id=T066_D004_Id
			left join T001 on D009_Id=T001_D009_Id
			WHERE D009_C004_Id = '{$g['empresaAtual']}'
			AND D004_Local = 'LOJA'
			GROUP BY D009_Id
			ORDER BY D001_Codigo_Produto
EOT;

		// Botões
        $botoes = array(
			array('titulo' => 'Processar reposição', 'id' => 'est003ProcessarRepor'),
		);
        echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est003ProcessarRepor').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Sim": function() { 
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est003/grid_func-ajax/est003ReabastecimentoLoja/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var buttons = { 
										"Ver D.E.": function() {
											abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Divergências de Estoque - ID: ' + request.T029_Id, '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&acaoId=' + encodeURIComponent(request.T029_Id) + '&T029_Id=' + encodeURIComponent(request.T029_Id), [950,600]);
											$(this).dialog("destroy").remove();
										},
										"Fechar": function() { $(this).dialog("destroy").remove(); } 
									}

									if(request.totalErro > 0){
										var mensagem = '<div style="background-color: #FFF; width:600px; height:300px; text-transform: none; font-size: 13px;">' +
													   '<b><font color="green">' + request.data + '</font></b><br><br>' +
													   '<b><font color="red">' + request.erro + '</font></b></div>';										
			   							dialogConfirm('Operação Ok', mensagem, buttons);

									} else {
										dialogConfirm('Operação Ok', request.data, buttons);

									}
									divRefresh('{$g['divId']}');
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, buttons);
								}
								$(this).dialog("destroy").remove();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Processar Estoque', 'Este processo irá transferir do Estoque para a Loja as quantidades selecionadas.<br/>Deseja continuar?', buttons);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T001_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
            'D001_Codigo_Produto' => array('titulo' => 'Cod. Prod.', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Desc. Prod.', 'tipo' => 'texto'),
            'D009_Qtde_Reabastecimento_Loja' => array('titulo' => 'Qtd Repor', 'tipo' => 'numero'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		// Pega a data do filtro para contar a quantidade de venda no produto no periodo
		$T001_Data_Lancamento = $g['ui']->getGridFiltroCampoBusca('T001_Data_Lancamento');
		$dataInicial		  = ($T001_Data_Lancamento[0] == '') ? "0000-00-00" : gCorrigeDataInverte($T001_Data_Lancamento[0]);
		$dataFinal			  = ($T001_Data_Lancamento[1] == '') ? "0000-00-00" : gCorrigeDataInverte($T001_Data_Lancamento[1]);
		
		if($dataInicial != '0000-00-00' && $dataFinal == '0000-00-00'){
			// se estiver preenchido apenas a dataInicial a dataFinal recebe o mesmo valor
			$dataFinal = $dataInicial;
		} else if ($dataFinal != '0000-00-00' && $dataInicial == '0000-00-00'){
			// se estiver preenchido apenas a dataFinal a dataInicial recebe o mesmo valor
			$dataInicial = $dataFinal;
		}
		
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => "(SELECT SUM(T001_Quantidade) FROM T001 LEFT JOIN T066 ON T066_Id=T001_T066_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T001_D009_Id = D009_Id AND D004_Local = 'LOJA' AND (T001_Data_Lancamento >= '{$dataInicial}' AND T001_Data_Lancamento <= '{$dataFinal}') AND T001_Flag_Operacao = 'S')", 'colunaTitulo' => 'Qtde Vendido', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),            //array('colunaValor' => 'SUM(T001_Quantidade)', 'colunaTitulo' => 'Qtde Vendido', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
            array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Estoque Loja', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D009_Qtde_Reabastecimento_Loja', 'colunaTitulo' => 'Qtde Repor', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'colunaEditar' => true),
            array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),
            array('colunaValor' => '(select group_concat(distinct D004_Local) from T066 left join D004 on D004_Id=T066_D004_Id where T066_D009_Id=D009_Id)', 'colunaTitulo' => 'Locação'),
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

	case 'est003GridMovimentacao':
		$filtroObrigatorio=true;
  	  	/**
		* Filtro
		*/
		$gridFiltro = array(
			'Data' => array('titulo' => 'Data', 'tipo' => 'data', 'naoGerarExtra' => true),
			'D001_Codigo_Produto' => array('titulo' => 'Cód Produto', 'tipo' => 'text'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'text'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'text'),
			'C008_Tipo,C008_Id' => array('titulo' => 'Departamento', 'tipo' => 'text'),
			'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'text')
		);
		$g['ui']->gridFiltroExtra(false, $gridFiltro, false); // somente para ter acesso a função getGridFiltroCampoBusca(): carregar o que foi filtrado
 		$data = $g['ui']->getGridFiltroCampoBusca('Data');
		$dataInicial="0000-00-00";
		$dataFinal="0000-00-00";
 		$dataInicial=gCorrigeDataInverte($data[0]);
 		$dataFinal=gCorrigeDataInverte($data[1]);

		if(empty($dataInicial) || $dataInicial=='0000-00-00'){
			$dataInicial = '2000-01-01';
		}
		if(empty($dataFinal) || $dataFinal=='0000-00-00'){
			$dataFinal = date("Y-m-d");
		}
  		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Cód', 'colunaExibir' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaExibir' => true, 'colunaAlinhar' => 'left'),
			array('colunaValor' => "(SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento<='{$dataInicial}' ORDER BY T001_Id DESC LIMIT 1) as Anterior",'colunaTitulo' => 'Saldo Anterior','colunaExibir' => true,'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 0),
			array('colunaValor' => "(SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_Flag_Operacao='E' AND T001_D009_Id=D009_Id AND (T001_Data_Lancamento>='{$dataInicial}' AND T001_Data_Lancamento<='{$dataFinal}')) as Entrada", 'colunaTitulo' => 'Qnt Entrada', 'colunaExibir' => true, 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 0),
			array('colunaValor' => "(SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_Flag_Operacao='S' AND T001_D009_Id=D009_Id AND (T001_Data_Lancamento>='{$dataInicial}' AND T001_Data_Lancamento<='{$dataFinal}')) as Saida", 'colunaTitulo' => 'Qnt Saída', 'colunaExibir' => true, 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 0),
			array('colunaValor' => "(SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <='{$dataFinal}' ORDER BY T001_Id DESC LIMIT 1) as Saldo", 'colunaTitulo' => 'Saldo Posterior', 'colunaExibir' => true, 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 0),
			array('colunaValor' => "(SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento <='{$dataFinal}' ORDER BY T001_Id DESC LIMIT 1)-(SELECT T001_Quantidade_Saldo FROM T001 WHERE T001_D009_Id=D009_Id AND T001_Data_Lancamento<='{$dataInicial}' ORDER BY T001_Id DESC LIMIT 1) as Diferenca", 'colunaTitulo' => 'Diferença', 'callback' => 'diferencaMovimentacao', 'colunaAlinhar' => 'right'),

		);

		$from = "D001";

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN C008 ON D001_C008_Id=C008_Id
			LEFT JOIN D082 ON D049_D082_Id=D082_Id
			LEFT JOIN D060 ON D001_D060_Id=D060_Id
			WHERE 
			((SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_Flag_Operacao='E' AND T001_D009_Id=D009_Id AND (T001_Data_Lancamento>='{$dataInicial}' AND T001_Data_Lancamento<='{$dataFinal}')) >0 OR
			(SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_Flag_Operacao='S' AND T001_D009_Id=D009_Id AND (T001_Data_Lancamento>='{$dataInicial}' AND T001_Data_Lancamento<='{$dataFinal}')) >0)
			GROUP BY D009_Id
EOT;

		
	/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

  	  	
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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
					//if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D001_Id = id[0].replace('D001_Id-', '');
					var D024_Id='';
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&tabela=$from&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
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
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 500);
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

	case 'est003GridColetar':	
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoOpcoesRetornarColeta', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesRetornarColeta', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'callbackParameter' => '3'),
            array('colunaValor' => 'campoOpcoesColeta', 'colunaTipo' => 'livre','callback' => 'campoOpcoesColeta', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'callbackParameter' => '6'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id)) as valorPendente', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%'),
            //array('colunaValor' => '(sum(distinct concat(ifnull(T014_Quantidade,\'0.00000\'),T014_Id))/sum(distinct concat(T225_Quantidade,T225_Id)))*100', 'colunaTitulo' => '%Receb', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => "(SELECT COUNT(DISTINCT T225_Id) FROM T225 WHERE T225_T224_Id=T224_Id)", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'concat(T224_Nome_Status,\'<br><font style="font-weight:normal;font-size:10px"><i>\',ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\'))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;font-weight:normal'),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T224_Flag_Transporte_Coleta', 'colunaTitulo' => 'Transporte', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'transporteColeta',
				'colunaEditarGeraSelect' => array(
					array('title' => '...', 'value' => ''),
					array('title' => 'MOTO', 'value' => '1'),
					array('title' => 'CARRO', 'value' => '2')
				)
			),
			array('colunaValor' => '(IF(T224_Flag_Transporte_Coleta = 1, "MOTO", IF(T224_Flag_Transporte_Coleta = 2, "CARRO", ""))) as transporteColeta', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Hora_Coleta', 'colunaTitulo' => 'Horario Coleta', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeTempo'),
			array('colunaValor' => 'T224_D109_Id', 'colunaTitulo' => 'Motorista', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D109_Nome',
				'colunaEditarGeraSelect' => array('D109', 'D109_Id', 'D109_Nome', "WHERE D109_Funcao = 'MOTORISTA'",true)
			),
            //array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
            //array('colunaValor' => 'T224_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Transportadora', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Observacao_1', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left',),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'Transporte', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Prazos_Pagamentos', 'colunaTitulo' => 'Forma de Pagamento', 'colunaExibir' => false),
            array('colunaValor' => "GROUP_CONCAT(DISTINCT D031_Portador SEPARATOR ', ')", 'colunaTitulo' => 'Forma de Pagamento'),
            array('colunaValor' => "if(T224_Flag_Coleta = '1', 'COLETAR', if(T224_Flag_Coleta = '2', 'EM COLETA', 'COLETADO')) as statusColeta", 'colunaTitulo' => 'statusColeta', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T224';
	
		$extra = <<<EOT
			left join D024 on D024_Id=T224_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			left join C007 as analista on analista.C007_Id=T224_Liberou_Alcada_C007_Id
			left join C004 on C004_Id = T224_C004_Id
			left join D116 on D116_Id = T224_D116_Id
			left join D021 on D021_Id = T224_D021_Id
			left join D109 on D109_Id = T224_D109_Id
			left join D013 on D013_Id=T224_D013_Id
			left join T139 on T139_T224_Id=T224_Id
			left join T225 on T225_T224_Id=T224_Id AND T225_Flag_Cancelada != 'S'
			left join D006 on D006_Id = T225_D006_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D036 on D036_Id=T224_D036_Id
			left join T226 on T224_Id=T226_T224_Id
			left join D031 on D031_Id=T226_D031_Id
			where (T224_C004_Id={$g['empresaAtual']} AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			AND T224_Flag_Status = '5'
			GROUP BY T224_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
				left join D024 on D024_Id=T224_D024_Id
     		    left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
    			left join D006 on D006_Id=T224_D006_Id
    			left join D116 on D116_Id=T224_D116_Id			
    			left join D021 on D021_Id=T224_D021_Id
    			left join T225 on T225_T224_Id=T224_Id 
    			left join T014 on T014_T225_Id=T225_Id
    			left join T013 on T013_Id=T014_T013_Id 
    			left join D009 on D009_Id=T225_D009_Id
    			left join D049 on D049_Id=D009_D049_Id
    			left join D001 on D001_Id=D049_D001_Id
    			left join D036 on D036_Id=T224_D036_Id
    			left join T226 on T224_Id=T226_T224_Id
    			left join D031 on D031_Id=T226_D031_Id
    			where (T224_C004_Id={$g['empresaAtual']} AND T224_Flag_Status!=8 AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
    			AND T224_Flag_Status = '5'
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
		    'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		    'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
		    'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
		    'D006_Codigo_CFOP,D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
		    'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
		    'T224_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
		    'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'texto'),
		    'D001_Codigo_Produto,D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
		    'T224_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
		    	array('title' => 'Todos', 'value' => ''),
		    	array('title' => 'COMPRA', 'value' => '1'),
		    	array('title' => 'TRANSFERÊNCIA', 'value' => '2')
		    )),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totais Almir
		*/
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total', 'valor' => 'T224_Valor_Total', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_Pendente', 'valor' => 'valorPendente', 'titulo' => 'Valor Pendente'),				
		);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais()) { 
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(distinct T224_Id)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(distinct concat(T224_Valor_Total,',',T224_Id))", $from, $extraTotal));
			$valorPendente = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id))", $from, $extraTotal));
						
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Pendente<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorPendente}" readonly></span>
					<div style="clear:both;"></div>             
				</div>
EOT;
			

		}*/

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T224_Id = id[0].replace(/T224_Id-/, '');
			 	$('#{$g['divId']}').showLoading();
			 	$.getJSON('/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					if(request.code) {
						window.open(request.data);
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    					dialogConfirm('Erro', request.data, \$buttons);
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
	break;

	case 'est003GridEmColeta':	
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'campoOpcoesRetornarColeta', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesRetornarColeta', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'callbackParameter' => '5'),
			array('colunaValor' => 'campoOpcoesColeta', 'colunaTipo' => 'livre','callback' => 'campoOpcoesColeta', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'callbackParameter' => '7'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'style' => 'width:1%;font-weight:normal'),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id)) as valorPendente', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%'),
            //array('colunaValor' => '(sum(distinct concat(ifnull(T014_Quantidade,\'0.00000\'),T014_Id))/sum(distinct concat(T225_Quantidade,T225_Id)))*100', 'colunaTitulo' => '%Receb', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => "(SELECT COUNT(DISTINCT T225_Id) FROM T225 WHERE T225_T224_Id=T224_Id)", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'concat(T224_Nome_Status,\'<br><font style="font-weight:normal;font-size:10px"><i>\',ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\'))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;font-weight:normal'),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T224_Flag_Transporte_Coleta', 'colunaTitulo' => 'Transporte', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'transporteColeta',
				'colunaEditarGeraSelect' => array(
					array('title' => '...', 'value' => ''),
					array('title' => 'MOTO', 'value' => '1'),
					array('title' => 'CARRO', 'value' => '2')
				)
			),
			array('colunaValor' => '(IF(T224_Flag_Transporte_Coleta = 1, "MOTO", IF(T224_Flag_Transporte_Coleta = 2, "CARRO", ""))) as transporteColeta', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Hora_Coleta', 'colunaTitulo' => 'Horario Coleta', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeTempo'),
			array('colunaValor' => 'T224_D109_Id', 'colunaTitulo' => 'Motorista', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D109_Nome',
				'colunaEditarGeraSelect' => array('D109', 'D109_Id', 'D109_Nome', "WHERE D109_Funcao = 'MOTORISTA'",true)
			),
            //array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
            //array('colunaValor' => 'T224_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Transportadora', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Observacao_1', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left',),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'Transporte', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => "GROUP_CONCAT(DISTINCT D031_Portador SEPARATOR ', ')", 'colunaTitulo' => 'Forma de Pagamento'),
            array('colunaValor' => 'T224_Prazos_Pagamentos', 'colunaTitulo' => 'Forma de Pagamento', 'colunaExibir' =>  false),
            array('colunaValor' => "if(T224_Flag_Coleta = '1', 'COLETAR', if(T224_Flag_Coleta = '2', 'EM COLETA', 'COLETADO')) as statusColeta", 'colunaTitulo' => 'statusColeta', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T224';
	
		$extra = <<<EOT
			left join D024 on D024_Id=T224_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			left join C007 as analista on analista.C007_Id=T224_Liberou_Alcada_C007_Id
			left join C004 on C004_Id = T224_C004_Id
			left join D116 on D116_Id = T224_D116_Id
			left join D021 on D021_Id = T224_D021_Id
			left join D109 on D109_Id = T224_D109_Id
			left join D013 on D013_Id=T224_D013_Id
			left join T139 on T139_T224_Id=T224_Id
			left join T225 on T225_T224_Id=T224_Id AND T225_Flag_Cancelada != 'S'
			left join D006 on D006_Id = T225_D006_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D036 on D036_Id=T224_D036_Id
			left join T226 on T224_Id=T226_T224_Id
			left join D031 on D031_Id=T226_D031_Id
			where (T224_C004_Id={$g['empresaAtual']} AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			AND T224_Flag_Status = '6'
			GROUP BY T224_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
				left join D024 on D024_Id=T224_D024_Id
     		    left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
    			left join D006 on D006_Id=T224_D006_Id
    			left join D116 on D116_Id=T224_D116_Id			
    			left join D021 on D021_Id=T224_D021_Id
    			left join T225 on T225_T224_Id=T224_Id 
    			left join T014 on T014_T225_Id=T225_Id
    			left join T013 on T013_Id=T014_T013_Id 
    			left join D009 on D009_Id=T225_D009_Id
    			left join D049 on D049_Id=D009_D049_Id
    			left join D001 on D001_Id=D049_D001_Id
    			left join D036 on D036_Id=T224_D036_Id
    			left join T226 on T224_Id=T226_T224_Id
				left join D031 on D031_Id=T226_D031_Id
    			where (T224_C004_Id={$g['empresaAtual']} AND T224_Flag_Status!=8 AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
    			AND T224_Flag_Status = '6'
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
		    'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		    'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
		    'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
		    'D006_Codigo_CFOP,D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
		    'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
		    'T224_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
		    'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'texto'),
		    'D001_Codigo_Produto,D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
		    'T224_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
		    	array('title' => 'Todos', 'value' => ''),
		    	array('title' => 'COMPRA', 'value' => '1'),
		    	array('title' => 'TRANSFERÊNCIA', 'value' => '2')
		    )),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totais Almir
		*/
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total', 'valor' => 'T224_Valor_Total', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_Pendente', 'valor' => 'valorPendente', 'titulo' => 'Valor Pendente'),				
		);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais()) { 
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(distinct T224_Id)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(distinct concat(T224_Valor_Total,',',T224_Id))", $from, $extraTotal));
			$valorPendente = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id))", $from, $extraTotal));
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Pendente<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorPendente}" readonly></span>
					<div style="clear:both;"></div>             
				</div>
EOT;
		}*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T224_Id = id[0].replace(/T224_Id-/, '');
			 	$('#{$g['divId']}').showLoading();
			 	$.getJSON('/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					if(request.code) {
						window.open(request.data);
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    					dialogConfirm('Erro', request.data, \$buttons);
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
	break;

	case 'est003GridColetado':	
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'campoOpcoesRetornarColeta', 'colunaTipo' => 'livre', 'callback' => 'campoOpcoesRetornarColeta', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'callbackParameter' => '6'),
			//array('colunaValor' => 'campoOpcoesColeta', 'colunaTipo' => 'livre','callback' => 'campoOpcoesColeta', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'callbackParameter' => '8'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'style' => 'width:1%;font-weight:normal'),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id)) as valorPendente', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%'),
            //array('colunaValor' => '(sum(distinct concat(ifnull(T014_Quantidade,\'0.00000\'),T014_Id))/sum(distinct concat(T225_Quantidade,T225_Id)))*100', 'colunaTitulo' => '%Receb', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => "(SELECT COUNT(DISTINCT T225_Id) FROM T225 WHERE T225_T224_Id=T224_Id)", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'concat(T224_Nome_Status,\'<br><font style="font-weight:normal;font-size:10px"><i>\',ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\'))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;font-weight:normal'),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T224_Flag_Transporte_Coleta', 'colunaTitulo' => 'Transporte', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'transporteColeta',
				'colunaEditarGeraSelect' => array(
					array('title' => '...', 'value' => ''),
					array('title' => 'MOTO', 'value' => '1'),
					array('title' => 'CARRO', 'value' => '2')
				)
			),
			array('colunaValor' => '(IF(T224_Flag_Transporte_Coleta = 1, "MOTO", IF(T224_Flag_Transporte_Coleta = 2, "CARRO", ""))) as transporteColeta', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Hora_Coleta', 'colunaTitulo' => 'Horario Coleta', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeTempo'),
			array('colunaValor' => 'T224_D109_Id', 'colunaTitulo' => 'Motorista', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D109_Nome',
				'colunaEditarGeraSelect' => array('D109', 'D109_Id', 'D109_Nome', "WHERE D109_Funcao = 'MOTORISTA'",true)
			),
            //array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
            //array('colunaValor' => 'T224_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D109_Nome', 'colunaTitulo' => 'Transportadora', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Observacao_1', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left',),
            array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'Transporte', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Prazos_Pagamentos', 'colunaTitulo' => 'Forma de Pagamento', 'colunaExibir' => false),
            array('colunaValor' => "GROUP_CONCAT(DISTINCT D031_Portador SEPARATOR ', ')", 'colunaTitulo' => 'Forma de Pagamento'),
            array('colunaValor' => "if(T224_Flag_Coleta = '1', 'COLETAR', if(T224_Flag_Coleta = '2', 'EM COLETA', 'COLETADO')) as statusColeta", 'colunaTitulo' => 'statusColeta', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T224';
	
		$extra = <<<EOT
			left join D024 on D024_Id=T224_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			left join C007 as analista on analista.C007_Id=T224_Liberou_Alcada_C007_Id
			left join C004 on C004_Id = T224_C004_Id
			left join D116 on D116_Id = T224_D116_Id
			left join D021 on D021_Id = T224_D021_Id
			left join D109 on D109_Id = T224_D109_Id
			left join D013 on D013_Id=T224_D013_Id
			left join T139 on T139_T224_Id=T224_Id
			left join T225 on T225_T224_Id=T224_Id AND T225_Flag_Cancelada != 'S'
			left join D006 on D006_Id = T225_D006_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D036 on D036_Id=T224_D036_Id
			left join T226 on T224_Id=T226_T224_Id
			left join D031 on D031_Id=T226_D031_Id
			where (T224_C004_Id={$g['empresaAtual']} AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			AND T224_Data_Hora_Coleta != '0000-00-00 00:00:00'
			GROUP BY T224_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
				left join D024 on D024_Id=T224_D024_Id
     		    left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
    			left join D006 on D006_Id=T224_D006_Id
    			left join D116 on D116_Id=T224_D116_Id			
    			left join D021 on D021_Id=T224_D021_Id
    			left join T225 on T225_T224_Id=T224_Id 
    			left join T014 on T014_T225_Id=T225_Id
    			left join T013 on T013_Id=T014_T013_Id 
    			left join D009 on D009_Id=T225_D009_Id
    			left join D049 on D049_Id=D009_D049_Id
    			left join D001 on D001_Id=D049_D001_Id
    			left join D036 on D036_Id=T224_D036_Id
    			left join T226 on T224_Id=T226_T224_Id
				left join D031 on D031_Id=T226_D031_Id
    			where (T224_C004_Id={$g['empresaAtual']} AND T224_Flag_Status!=8 AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
    			AND T224_Data_Hora_Coleta != '0000-00-00 00:00:00'
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
		    'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		    'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
		    'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    'T224_Data_Hora_Coleta' => array('titulo' => 'Coleta', 'tipo' => 'data'),
		    'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
		    'D006_Codigo_CFOP,D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
		    'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
		    'T224_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
		    'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'texto'),
		    'D001_Codigo_Produto,D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
		    'T224_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
		    	array('title' => 'Todos', 'value' => ''),
		    	array('title' => 'COMPRA', 'value' => '1'),
		    	array('title' => 'TRANSFERÊNCIA', 'value' => '2')
		    )),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totais Almir
		*/
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total', 'valor' => 'T224_Valor_Total', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_Pendente', 'valor' => 'valorPendente', 'titulo' => 'Valor Total'),				
		);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais()) { 
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(distinct T224_Id)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(distinct concat(T224_Valor_Total,',',T224_Id))", $from, $extraTotal));
			$valorPendente = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id))", $from, $extraTotal));
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Pendente<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorPendente}" readonly></span>
					<div style="clear:both;"></div>             
				</div>
EOT;
		}*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T224_Id = id[0].replace(/T224_Id-/, '');
			 	$('#{$g['divId']}').showLoading();
			 	$.getJSON('/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					if(request.code) {
						window.open(request.data);
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    					dialogConfirm('Erro', request.data, \$buttons);
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
	break;

	case 'est003GridEstoqueDeTerceiros':

		// $filtroObrigatorio = true;
		
		$grid = array(						           
			array('colunaValor'=> 'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'IF( T001_Flag_Operacao = \'E\' and T001_T008_Id is null, Forn.D024_Id, Cli.D024_Id) as Participante', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal; width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'style' => 'font-weight:normal; width: 1%;'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;','colunaAlinhar' => 'left'),                        			
			array('colunaValor'=> 'D037_Unidade', 'colunaTitulo'=>'Unidade', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'IF( T001_Flag_Operacao = \'E\' and T001_T008_Id is null, Forn.D024_Id, Cli.D024_Id) as Código', 'colunaTitulo' => 'Código Participante', 'style' => 'width: 1%;','colunaAlinhar' => 'center'),                        			
            array('colunaValor' => 'IF( T001_Flag_Operacao = \'E\' and T001_T008_Id is null, Forn.D024_Nome_Empresa, Cli.D024_Nome_Empresa) as Nome', 'colunaTitulo' => 'Participante', 'style' => 'width: 1%;' ,'colunaAlinhar' => 'left'),                        			
			array('colunaValor' => "sum(T001_Quantidade * if(T001_Flag_Operacao = 'S', -1, 1))", 'colunaTitulo' => 'Estoque de Terceiros','colunaCasasDecimais' => '2', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),                        						
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T001";
		$extra = <<<EOT
			left join T013 on T013_Id = T001_T013_Id
			left join D024 as Forn on Forn.D024_Id = IFNULL(T013_D024_Id_Participante, T001_D024_Id)
			left join D024 as Cli on Cli.D024_Id = T001_D024_Id_Cliente
			left join D009 on D009_Id = T001_D009_Id
			left join D049 on D049_Id = D009_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D037 on D001_D037_Id = D037_Id
			left join T066 on T066_Id = T001_T066_Id
			left join D004 on D004_Id = T066_D004_Id
		where
			D009_C004_Id = {$g['empresaAtual']}
			and D004_Indicador_Propriedade = 2 
			and (T001_Flag_Operacao = 'S'or T001_Flag_Operacao = 'E')    
		group by D009_Id, IF( T001_Flag_Operacao = 'E' and T001_T008_Id is null, Forn.D024_Id, Cli.D024_Id)						
		order by D001_Codigo_Produto	
EOT;
		$botoes = array();
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */		

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),			
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),						
			'Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa' => array('titulo' => 'Participante', 'tipo' => 'texto'),
			'sum(T001_Quantidade * CASE WHEN T001_Flag_Operacao = "S" THEN  -1 ELSE 1 END)' => array('titulo' => 'Estoque de Terceiros', 'tipo' => 'numero', 'having' => true, 'valor' => '>0'),			
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);		
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		echo <<<EOT

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



		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'est003GridEstoqueEmTerceiros':		
		// $filtroObrigatorio = true;
		
		$grid = array(						           
			array('colunaValor'=> 'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'IF( T001_Flag_Operacao = \'E\' and T001_T008_Id is null, Forn.D024_Id, Cli.D024_Id) as Participante', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal; width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'style' => 'font-weight:normal; width: 1%;'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;','colunaAlinhar' => 'left'),                        			
			array('colunaValor'=> 'D037_Unidade', 'colunaTitulo'=>'Unidade', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'IF( T001_Flag_Operacao = \'E\' and T001_T008_Id is null, Forn.D024_Id, Cli.D024_Id) as Código', 'colunaTitulo' => 'Código Participante', 'style' => 'width: 1%;','colunaAlinhar' => 'center'),                        			
            array('colunaValor' => 'IF( T001_Flag_Operacao = \'E\' and T001_T008_Id is null, Forn.D024_Nome_Empresa, Cli.D024_Nome_Empresa) as Nome', 'colunaTitulo' => 'Participante', 'style' => 'width: 1%;' ,'colunaAlinhar' => 'left'),                        			
			array('colunaValor' => "sum( T001_Quantidade	* if(T001_Flag_Operacao = 'S', 1, -1)) as Estoque", 'colunaTitulo' => 'Estoque em Terceiros','colunaCasasDecimais' => '2', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),                        						
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T001";
		$extra = <<<EOT
				left join D024 as Forn on Forn.D024_Id = T001_D024_Id
				left join D024 as Cli on Cli.D024_Id = T001_D024_Id_Cliente
				left join D009 on D009_Id = T001_D009_Id
				left join D049 on D049_Id = D009_D049_Id
				left join D001 on D001_Id = D049_D001_Id
				left join D037 on D001_D037_Id = D037_Id
				left join T066 on T066_Id = T001_T066_Id
				left join D004 on D004_Id = T066_D004_Id
				left join D006 on D006_Id = T001_D006_Id
			where
				D009_C004_Id = '{$g['empresaAtual']}'
				and D004_Indicador_Propriedade = 1  
				and (T001_Flag_Operacao = 'S' or T001_Flag_Operacao = 'E')  
			group by
				D009_Id,
				IF( T001_Flag_Operacao = 'E' and T001_T008_Id is null, Forn.D024_Id, Cli.D024_Id)						
			order by D001_Codigo_Produto
EOT;
		$botoes = array();
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */		

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),			
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),			
			'Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa' => array('titulo' => 'Participante', 'tipo' => 'texto'),						
			'(sum(T001_Quantidade * CASE WHEN T001_Flag_Operacao = "S" THEN  1 ELSE -1 END))' => array('titulo' => 'Estoque em Terceiros', 'tipo' => 'numero', 'having' => true,'valor' => '>0'),											
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		echo <<<EOT

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
