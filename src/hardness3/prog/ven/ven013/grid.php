<?php
namespace hardness;
             /**
 * VEN - VEN001- Pedidos
 */
// Classes
//require_once('bibliotecas/classes/VEN013.php');
//$VEN013 = new VEN013();
// Carrega funções callback
require_once('prog/ven/ven013/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven013/grid_func-js.php');
// Imagem para exclusão da linha
/**
 * Ações
 */
switch($g['r'][3]){
	case 'ven013GridPrincipal':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'naoLiberar', 'colunaTipo' => 'livre', 'callback' => 'ven013NaoLiberar'),
            array('colunaValor' => 'Liberar', 'colunaTipo' => 'livre', 'callback' => 'ven013Liberar'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Alcada', 'colunaTitulo' => 'Alçada', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'group_concat(distinct T006_T005_Id) Pedido', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal) Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) T005_Status', 'colunaTitulo' => 'Situação', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'if(T005_Flag_ACP=\'1\',\'NF\',if(T005_Flag_ACP=\'2\',\'Romaneio\',if(T005_Flag_ACP=\'3\',\'ACP\',if(T005_Flag_ACP=\'5\',\'D1\',if(T005_Flag_ACP=\'6\',\'NFe\',\'CF\'))))) Tipo_Documento', 'colunaTitulo' => 'Doc'),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T005_Motivo_Pedido_Liberacao', 'colunaTitulo' => 'Motivo'),
            array('colunaValor' => 'T005_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo' => 'Supervisor'),
            array('colunaValor' => 'T005_Observacao_Comercial', 'colunaTitulo' => 'Observação Comercial'),
        );


		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T005';

		$extra = <<<EOT

				left join D024 on D024_Id=T005_D024_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T007 on T007_T005_Id=T005_Id
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
				where T005_C004_Id='{$g['empresaAtual']}'
EOT;

		if ($g['c007']['C007_Flag_Vendedor']!='D')
			$extra.=" and T005_Flag_Alcada='{$g['c007']['C007_Flag_Vendedor']}'";

		$extra.= <<<EOT
				and (T005_Flag_Status=13)
				group by T005_Id
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
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			"if(T005_Flag_ACP='1','NF',if(T005_Flag_ACP='2','Romaneio',if(T005_Flag_ACP='3','ACP',if(T005_Flag_ACP='5','D1',if(T005_Flag_ACP='6','NFe','CF'))))) Tipo_Document" => array('titulo' => 'Documento', 'tipo' => 'texto'),
			'T005_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			//'T005_Flag_Status_Pedido' => array('titulo' => 'Status(F/P/C/V)', 'tipo' => 'texto'),
			'T005_Flag_Status_Pedido' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Finalizado', 'value' => 'F'),
				array('title' => 'Pendente', 'value' => 'P'),
				array('title' => 'Cancelado', 'value' => 'C'),
				array('title' => 'Vencido', 'value' => 'V'),
			), 'naoGerarExtra' => false),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,605]);
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
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		break;
}
?>
