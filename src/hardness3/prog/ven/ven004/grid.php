<?php
namespace hardness;
//Fazer os outros Grids do módulo CRM.

/**
 * Testes - Weiber - Programa Teste Grids CRM.
 */

/**
 * Area para funções locais
 */

//funções do grid orcamentos.
function nomeEmpresa($array) {
	return substr($array['todosCampos']['D024_Nome_Fantasia'],0,25);
}

//funções do grid pedidos.
function campoValorTotalSeparado($array){
	if($array['todosCampos']['T005_Valor_Total_Produtos']>0){
		return number_format(($array['todosCampos']['Valor_Separado']/$array['todosCampos']['T005_Valor_Total_Produtos'])*100,2,',','.');
	}
	else {
		return '&nbsp;';
	}
}

function campoValorSeparado($array) {
	return number_format($mT005[Valor_Separado],2,',','.');
}

/**
 * Ações
 */
switch($g['r'][3]){
	// PADRÃO
	case 'divPadrao':
		$g['conteudo']['jsTexto'] = <<<EOT
$(document).ready(function() {
	// Carrega Grid
	$.ajax({
		url: '/ven/ven004/grid/pedidos/',
		dataType: 'html',
		data: 'ajax=true',
		success: function(data) {
			$('#divPadrao').html(data);
			return false;
		}
	});
});
EOT;
		break;
		// GRID -> select pego de: hardness\crm_orcamentos\orcamentos.php
	case 'orcamentos':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir' => false),
		array('colunaValor' => 'Liberou.C007_Primeiro_Nome Analista', 'colunaTitulo' => 'Liberou.C007_Primeiro_Nome Analista', 'colunaExibir' => false),
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'D024_Nome_Empresa', 'colunaExibir' => false),
		array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'T007_Numero_Nota_Fiscal', 'colunaExibir' => false),
		array('colunaValor' => 'T003_Liberou_Alcada_IPV', 'colunaTitulo' => 'T003_Liberou_Alcada_IPV', 'colunaExibir' => false),
		array('colunaValor' => 'T003_Flag_Status_Liberacao', 'colunaTitulo' => 'T003_Flag_Status_Liberacao', 'colunaExibir' => false),
		array('colunaValor' => 'D047_Descricao_Motivo', 'colunaTitulo' => 'D047_Descricao_Motivo', 'colunaExibir' => false),
		array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir' => false),
		array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir' => false),
		array('colunaValor' => "group_concat(distinct T007_Id  SEPARATOR ',') T007_Id", 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
		array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Número'),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'callback' => 'nomeEmpresa'),
		array('colunaValor' => "if(T003_Flag_ACP='1','NF',if(T003_Flag_ACP='2','Romaneio',if(T003_Flag_ACP='3','ACP',if(T003_Flag_ACP='5','D1',if(T003_Flag_ACP='6','NFe','CF'))))) Tipo_Documento", 'colunaTitulo' => 'Doc'),
		array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor'),
		array('colunaValor' => "group_concat(distinct concat(T006_T005_Id,'-',T005_Status_Pedido(T005_Flag_Status,1),'-',REPLACE(REPLACE(REPLACE(FORMAT((select sum(T006_Valor_Total_Preco+T006_Valor_IPI) from T006 left join T004 on T004_Id=T006_T004_Id where T006_T005_Id=T005_Id and T004_T003_Id=T003_Id), 2), '.', '|'), ',', '.'), '|', ',')) SEPARATOR '<br>') Pedido", 'colunaTitulo' => 'Pedido'),
		array('colunaValor' => "group_concat(distinct T007_Numero_Nota_Fiscal  SEPARATOR ',') Nota_Fiscal", 'colunaTitulo' => 'NF'),
		array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
		array('colunaValor' => 'Supervisor.C007_Primeiro_Nome', 'colunaTitulo' => 'Supervisor'),
		array('colunaValor' => 'Analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),


		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T003';

		$extra = <<<EOT

				left join D024 on D024_Id=T003_D024_Id
				left join T004 on T004_T003_Id=T003_Id
				left join D047 on D047_Id=T003_D047_Id
				left join T006 on T006_T004_Id=T004_Id and T006_Flag_Status!='3'
				left join T005 on T005_Id=T006_T005_Id and T005_Flag_Status!='8'
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
				left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
				left join C007 as Analista on Analista.C007_Id=Vendedor.C007_C007_Id
				left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
				left join D021 on D021_Id=T003_D021_Id
				where 
				T003_C004_Id='1'
		
				group by T003_Id
				
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

	case'pedidos':
		//GRID -> select pego de: hardness/crm_pedidos/pedidos.php

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir' => false),
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'D024_Nome_Empresa', 'colunaExibir' => false),
		array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
		array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
		array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Número'),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'callback' => 'nomeEmpresa'),
		array('colunaValor' => "if(T005_Flag_ACP='1','NF',if(T005_Flag_ACP='2','Romaneio',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','D1',if(T005_Flag_ACP='6','NFe','CF'))))) Tipo_Documento", 'colunaTitulo' => 'Doc'),
		array('colunaValor' => 'T005_Valor_Total_Produtos', 'colunaTitulo' => 'Valor'),
		array('colunaValor' => '(select sum(T006_Quantidade_Separacao*T006_Valor_Preco_Unitario) from T006 where T006_T005_Id=T005_Id) Valor_Separado', 'colunaTitulo' => 'Separado', 'callback' => 'campoValorSeparado'),
		array('colunaValor' => 'campoValorTotalSeparado', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Separado', 'callback'=> 'campoValorTotalSeparado'),
		array('colunaValor' => '(select T006_Data_Separacao from T006 where T006_T005_Id=T005_Id order by T006_Data_Separacao desc limit 1) T006_Data_Separacao' , 'colunaTitulo' => 'Separado'),
		array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF'),
		array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) T005_Status', 'colunaTitulo' => 'Situação'),
		array('colunaValor' => 'T005_Observacao', 'colunaTitulo' => 'Observação'),
		array('colunaValor' => 'Vendedor.C007_Primeiro_Nome C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
		array('colunaValor' => 'Separou.C007_Primeiro_Nome Separou', 'colunaTitulo' => 'Separou'),
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
				left join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas
				where 
					T005_C004_Id='1'
				and 
					Vendedor.C007_Id='1'
				and 
					Supervisor.C007_Id='1'
				and 
					T005_Valor_Total>0
				group by T005_Id			
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
}
?>