<?php
namespace hardness;
/**
* EST - EST013
*/

// Carrega funções callback
require_once('prog/est/est013/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est013/grid_func-js.php');

/**
 * Ações
 */
switch($g['r'][3]){
	case 'est013GridFrequencia':
		$meses = $g['c020']['C020_Numero_Meses_Relatorio_Estoque'];
		$periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D001_Id', $g['divId'])),
			array('colunaValor' => 'nrLinha', 'colunaTitulo' => 'Nr', 'colunaTipo' => 'livre', 'callback' => 'nrLinha', 'style' => 'width: 1%'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'Empresa.D024_Id as D024_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'style' => 'width:1%;','colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código','style' => 'width:1%;'),
			//array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Código Cliente','style' => 'width:1%;'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%;'),
			array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Local', 'style' => 'width: 1%;', 'colunaExibir' => false),
			//array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque','colunaCasasDecimais' => '2','colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Quantidade_Consumo', 'colunaTitulo' => 'Consumido','colunaCasasDecimais' => '2','colunaAlinhar' => 'right','callback' => 'est009campoConsumido'),
			//array('colunaValor' => 'D009_Quantidade_Comprar_Calculada', 'colunaTitulo' => 'Qtd. Comprar', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'3'),
			array('colunaValor' => "T187_Nr_Meses_Count", 'colunaTitulo' => 'Meses c/ Saída', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;'),
			array('colunaValor' => "T187_Clientes_Total", 'colunaTitulo' => 'Clientes', 'callback'=>'corrigeNumero', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;'),
			
			array('colunaValor' => "T187_Operacoes", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'corrigeNumero'),
			array('colunaValor' => "T187_Operacoes_Media", 'colunaTitulo' => 'Pedidos (Média)', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'gCorrigeNumero'),
			
			array('colunaValor' => "T187_Quantidade", 'colunaTitulo' => 'Qtd', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'corrigeNumero'),
			array('colunaValor' => "(T187_Quantidade*D001_Peso_Unitario_Kg) as Peso", 'colunaTitulo' => 'Qtd Peso', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'corrigePeso'),
			array('colunaValor' => "T187_Quantidade_Media", 'colunaTitulo' => 'Qtd (Média)', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "((T187_Quantidade*D001_Peso_Unitario_Kg)/{$meses}) as PesoMedia", 'colunaTitulo' => 'Qtd (Média) Peso', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'corrigePeso'),
			
			array('colunaValor' => "T187_Estoque_Media", 'colunaTitulo' => 'Estoque (Média)', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "(T187_Estoque_Media*D001_Peso_Unitario_Kg) as EstoquePeso", 'colunaTitulo' => 'Estoque (Média) Peso', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'corrigePeso'),
			
			array('colunaValor' => "D009_Quantidade_Estoque_Real", 'colunaTitulo' => 'Estoque Atual', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "(D009_Quantidade_Estoque_Real*D001_Peso_Unitario_Kg) as PesoAtual", 'colunaTitulo' => 'Estoque Atual Peso', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight: bold;', 'callback' => 'corrigePeso'),
			
			array('colunaValor' => "D009_Valor_Custo_Ultima_Compra", 'colunaTitulo' => 'Custo Última Compra', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "UltimoForn.D024_Nome_Empresa as UltimoFornecedor", 'colunaTitulo' => 'Último Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D009';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		LEFT JOIN T187 on T187_D009_Id=D009_Id
		left join D049 on D049_Id=D009_D049_Id
		left join D001 on D001_Id=D049_D001_Id
		left join D082 on D082_Id=D049_D082_Id
		left join D083 on D083_D049_Id=D049_Id
		left join D024 as Empresa on Empresa.D024_Id=D083_D024_Id
		left join D037 on D037_Id=D001_D037_Id
		left join C008 on C008_Id=D001_C008_Id
		left join D003 on D003_Id=D001_D003_Id
		left join D060 on D001_D060_Id=D060_Id
		left join D002 on D001_D002_Id=D002_Id
		left join D015 on D001_D015_Id=D015_Id
		left join D024 as UltimoForn on UltimoForn.D024_Id=D049_D024_Id
		left join T066 on T066_D009_Id=D009_Id
		left join D004 on T066_D004_Id=D004_Id
		where (
			D009_C004_Id='{$g['empresaAtual']}'
			AND D001_Flag_Ativo='S'
			AND (T187_Nr_Meses = {$meses} or T187_Nr_Meses IS NULL or T187_Nr_Meses = '' or T187_Nr_Meses = 0)
		)
		group by D009_Id
		order by T187_Quantidade DESC
EOT;
		
		/**
		* Filtro
		*/
		$C008_Tipo = gGeraSelect('C008', 'C008_Id', 'C008_Tipo');
		$todos = array('title' => 'Todos', 'value' => '');
		array_unshift($C008_Tipo, $todos);
		$gridFiltro = array(
			//'C008_Id' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => $C008_Tipo),
			'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'T187_Nr_Meses_Count' => array('titulo' => 'Meses', 'tipo' => 'numero'),
			'T187_Clientes_Total' => array('titulo' => 'Clientes', 'tipo' => 'numero'),
			'T187_Quantidade' => array('titulo' => 'Qtd', 'tipo' => 'numero'),
			'T187_Quantidade_Media' => array('titulo' => 'Qtd (Média)', 'tipo' => 'numero'),
			'T187_Operacoes' => array('titulo' => 'Pedidos', 'tipo' => 'numero'),
			'T187_Operacoes_Media' => array('titulo' => 'Pedidos (Média)', 'tipo' => 'numero'),
			'D009_Quantidade_Estoque_Real' => array('titulo' => 'Estoque', 'tipo' => 'numero'),
			'T187_Estoque_Media' => array('titulo' => 'Estoque (Média)', 'tipo' => 'numero'),
			'T187_Flag_Prod_Anterior' => array('titulo' => 'Prod. Existe Antes', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Sim', 'value' => '1'),
				array('title' => 'Não', 'value' => '0')
			)),
			//'Peso' => array('titulo' => 'Peso', 'tipo' => 'moeda', 'having' => true),
			//'PesoMedia' => array('titulo' => 'Peso (Média)', 'tipo' => 'moeda', 'having' => true),
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
		//$extra_ = urlencode(base64_encode($extra));
		$sql_ = urlencode(base64_encode($geradoSql));
		$options = '';
		for ($i = 1; $i <= 24; $i++) {
			$options .= '<option value="'.$i.'"'.(($meses == $i) ? ' selected="selected"' : '').'>'.$i.'</option>';
		}
		/*$options .= '<option value="6"'.(($meses == 6) ? ' selected="selected"' : '').'>6</option>';
		$options .= '<option value="12"'.(($meses == 12) ? ' selected="selected"' : '').'>12</option>';
		$options .= '<option value="24"'.(($meses == 24) ? ' selected="selected"' : '').'>24</option>';*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				Período (meses): <select id="C020_Numero_Meses_Relatorio_Estoque" value="{$meses}">{$options}</select>&nbsp;&nbsp;<button type="button" id="est013AlterarPeriodo">Alterar</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est013AlterarPeriodo').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est013/grid_func-ajax/est013AlterarPeriodo/?ajax=true&periodo=' +  encodeURIComponent($('#C020_Numero_Meses_Relatorio_Estoque').val()) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}');
					});
				});
			} </script></div>
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
				   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
			   });
			   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	
	case 'est013GridSobras':
		$grid = array(
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código','style' => 'width:1%;'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição','align'=>'left'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca','align'=>'left'),
			array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Estoque'),
			array('colunaValor' => '(T066_Quantidade_Estoque%T066_Agrupamento) as Sobra', 'colunaTitulo' => 'Sobra', 'align'=>'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação','align'=>'left'),	
			
			
		);

		/**
		* Definição da SQL
		*/
		$from = 'T066';
		
		$extra = <<<EOT
		LEFT JOIN D009 ON T066_D009_Id=D009_Id
		LEFT JOIN D004 ON T066_D004_Id=D004_Id
		LEFT JOIN D001 ON D009_D001_Id=D001_Id
		LEFT JOIN D049 ON D009_D049_Id=D049_Id
		LEFT JOIN D082 ON D049_D082_Id=D082_Id
		WHERE (
			T066_Agrupamento > 1 AND (T066_Quantidade_Estoque%T066_Agrupamento) != '0'
		)
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			//'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto')
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
}
?>
