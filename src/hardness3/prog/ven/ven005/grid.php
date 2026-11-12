<?php
namespace hardness;
/**
* VEN - VEN001- Orçamentos
*/
// Classes
//require_once('bibliotecas/classes/VEN005.php');
//$VEN005 = new VEN005();
// Carrega funções callback
require_once('prog/ven/ven005/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven005/grid_func-js.php');
// Imagem para exclusão da linha
/**
 * Ações
 */
switch($g['r'][3]){
	case 'ven005GridPrincipal':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'naoLiberar', 'colunaTipo' => 'livre', 'callback' => 'ven005NaoLiberar', 'colunaTitulo' => 'Negar', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Liberar', 'colunaTipo' => 'livre', 'callback' => 'ven005Liberar', 'colunaTitulo' => 'Aprovar', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T006_T005_Id) as Pedido', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Situação', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T003_Flag_Alcada', 'colunaTitulo' => 'Alçada', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'if(T003_Flag_ACP=\'1\',\'NF\',if(T003_Flag_ACP=\'2\',\'Romaneio\',if(T003_Flag_ACP=\'3\',\'ACP\',if(T003_Flag_ACP=\'5\',\'D1\',if(T003_Flag_ACP=\'6\',\'NFe\',\'CF\'))))) as Tipo_Documento', 'colunaTitulo' => 'Doc', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T003_Valor_Desconto', 'colunaTitulo' => 'Desconto total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T003_Valor_Total_Confirmado', 'colunaTitulo' => 'Valor confirmado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T003_Valor_Desconto_Confirmado', 'colunaTitulo' => 'Desconto confirmado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T003_Motivo_Pedido_Liberacao', 'colunaTitulo' => 'Motivo', 'style' => 'font-weight:bold;color: var(--BGVermelho)'),
            array('colunaValor' => 'T003_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaExibir' => false),
            array('colunaValor' => 'concat(\'<font style="background:yellow;font-weight:bold">\',T003_Observacao_Comercial,\'</font>\')', 'colunaTitulo' => 'Observação Comercial', 'style' => 'max-width:200px;white-space:normal;'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T003';

		$extra = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
			left join T005 on T005_Id=T006_T005_Id
			left join T007 on T007_T005_Id=T005_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where T003_C004_Id='{$g['empresaAtual']}'
EOT;

		if ($g['c007']['C007_Flag_Vendedor']!='D')
			$extra.=" and T003_Flag_Alcada='{$g['c007']['C007_Flag_Vendedor']}'";
		
		$extra.= <<<EOT
			and T003_Flag_Perdido!='S'
			and T003_Flag_Status_Liberacao='1'
			group by T003_Id
				
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
		/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven005LiberacaoAtivarMsg">Ativar Mensagens</button>
				<button type="button" id="ven005LiberacaoDesativarMsg">Desativar Mensagens</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#ven005LiberacaoAtivarMsg').unbind('click').bind('click', function(data) {
					setCookie('liberacaoCreditoFlag', 2, 0, true);
					dialogConfirm('Notificações', 'Ativado!<br />Você irá receber notificações de análise pendente em todo o sistema.');
				});
				$('#ven005LiberacaoDesativarMsg').unbind('click').bind('click', function(data) {
					setCookie('liberacaoCreditoFlag', 1, 0, true);
					dialogConfirm('Notificações', 'Desativado!<br />Você não irá mais receber notificações de análise pendente em todo o sistema.');
				});
			} </script></div>
EOT;
		*/
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T003_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T003_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'T003_Flag_Status_Orcamento' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
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
				var id = $(this).closest('tr').attr('id').replace(/T003_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [970,605]);
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
