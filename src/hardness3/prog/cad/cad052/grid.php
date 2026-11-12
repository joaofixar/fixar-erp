<?php
namespace hardness;

require_once('prog/cad/cad052/grid_func-callback.php');

require_once('prog/cad/cad052/grid_func-js.php');

/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'cad052gridComposicao':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusaoItem', 'style' => 'width: 1px;'),
			array('colunaValor' => 'D074_Id', 'colunaTitulo' => 'D074_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D074_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'colunaCasasDecimais' => '5'),
			array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Unit.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%;'),
			array('colunaValor' => 'custoTotal', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'callback' => "custoTotal", 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%;'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D074';
		$extra = <<<EOT
		LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
		LEFT JOIN D049 ON D001_Id = D049_D001_Id
		LEFT JOIN D009 ON D049_Id = D009_D049_Id
		LEFT JOIN D037 ON D037_Id = D001_D037_Id
		WHERE D074_D001_Id = '{$r_D001_Id}'
		AND D009_C004_Id={$g['empresaAtual']}
EOT;

		$sql = "SELECT COUNT(D074_Id),
						SUM(D009_Valor_Custo_Unitario * D074_Quantidade) as Custo
						FROM D074
						LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
						LEFT JOIN D049 ON D001_Id = D049_D001_Id
						LEFT JOIN D009 ON D049_Id = D009_D049_Id
						LEFT JOIN D037 ON D037_Id = D001_D037_Id
						WHERE D074_D001_Id = '{$r_D001_Id}'
						AND D009_C004_Id={$g['empresaAtual']}";
		$sql = mysql_query($sql);
		$res = mysql_fetch_array($sql);

		$itens = $res[0];
		$custoTotal = gCorrigeNumero($res[1]);
		/**
		* Botões (Versão Acima do GRID)
		*/
		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$retorno = $CAD052->engenhariaLiberadaIncluir($r_D001_Id);
		if($retorno === true){
			$novoIdTelaForm = uniqid();
			$incluirProduto = uniqid();
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$itens}" class="whiteBg" id="" size="4" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$custoTotal}" class="whiteBg" id="" size="11" readonly></span>
					<input type="button" style="margin-top:12px;" id='{$incluirProduto}' value="Incluir Item">
					<div style="clear:both;"></div>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$incluirProduto}').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Itens Composição', '/cad/cad052/content/cad052contentItensComposicao/', '&D001_Id=' + {$r_D001_Id}, [360,110]);
					});
				} </script></div>
	EOT;
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(

		);
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$retorno = $CAD052->engenhariaLiberadaIncluir($r_D001_Id);
		if($retorno === true){
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D074_Id = id[0].replace(/D074_Id-/, '');
					var D001_Id = id[1].replace(/D001_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Itens Composição', '/cad/cad052/content/cad052contentItensComposicao/', '&acaoId=' + encodeURIComponent(D074_Id) + '&D001_Id=' + encodeURIComponent(D001_Id), [360,110]);
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
		$g['smarty']->display('grid.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cad052gridProcessos':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusaoProcesso", 'colunaTipo' => "livre", 'callback' =>'campoExclusaoProcesso', 'style' => 'width: 1px;'),
			array('colunaValor' => 'D075_Id', 'colunaTitulo' => 'id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D075_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D079_Nome', 'colunaTitulo' => 'Máquina', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D079_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D078_Nome', 'colunaTitulo' => 'Função', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D078_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D075_Descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D075_Lead_Time', 'colunaTitulo' => 'Lead Time', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D075_D001_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D075_Numero_Funcionarios', 'colunaTitulo' => 'Funcionários', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'custoTotalMateriais', 'colunaTitulo' => 'Material consumo', 'colunaAlinhar' => 'right', 'callback' => "custoTotalMateriais", 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "campoConsumiveis", 'colunaTipo' => "livre", 'callback' =>'campoConsumiveis', 'style' => 'width: 1px;'),
			array('colunaValor' => 'custoTotalProcessos', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'callback' => "custoTotalProcessos", 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D075';
		$extra = <<<EOT
		LEFT JOIN D078 on D078_Id = D075_D078_Id
		LEFT JOIN D079 on D079_Id = D075_D079_Id
		WHERE D075_D001_Id = '{$r_D001_Id}'
		GROUP BY D075_Id
		ORDER BY D075_Ordem
EOT;
		$sql = "SELECT SUM(D009_Valor_Custo_Unitario * D076_Quantidade)
					FROM D076
					LEFT JOIN D009 ON D009_Id = D076_D009_Id
					LEFT JOIN D075 ON D075_Id = D076_D075_Id
					WHERE D075_D001_Id = '{$r_D001_Id}'";
		$res = mysql_query($sql);
		$res = mysql_fetch_array($res);
		$custoMateriais = $res[0];

		$sql = "SELECT COUNT(D075_Id),
						SUM(D075_Lead_Time * (D079_Custo_Minuto + D078_Custo_Minuto)) as Custo
						FROM D075
						LEFT JOIN D078 on D078_Id = D075_D078_Id
						LEFT JOIN D079 on D079_Id = D075_D079_Id
						WHERE D075_D001_Id = '{$r_D001_Id}'";
		$sql = mysql_query($sql);
		$res = mysql_fetch_array($sql);

		$processos = $res[0];
		$custoTotal = gCorrigeNumero($res[1] + $custoMateriais);

		/**
		* Botões (Versão Acima do GRID)
		*/
		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$retorno = $CAD052->engenhariaLiberadaProcessoIncluir($r_D001_Id);
		if($retorno === true){
			$novoIdTelaForm = uniqid();
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Processos<br><input type="text" value="{$processos}" class="whiteBg" id="" size="4" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$custoTotal}" class="whiteBg" id="" size="11" readonly></span>
					<input type="button" style="margin-top: 12px;" id="incluirProcesso" value="Incluir Processo">
					<div style="clear:both;"></div>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#incluirProcesso').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Processo', '/cad/cad052/content/cad052contentProcessos/', '&D001_Id=' + encodeURIComponent('{$r_D001_Id}'), [355,250]);
					});
				} </script></div>
	EOT;
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(

		);
		// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$retorno = $CAD052->engenhariaLiberadaProcessoIncluir($r_D001_Id);
		if($retorno === true){
			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D075_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Processo', '/cad/cad052/content/cad052contentProcessos/', '&acaoId=' + encodeURIComponent(id) + '&D001_Id=' + encodeURIComponent('{$r_D001_Id}'), [355,250]);
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
		$g['smarty']->display('grid.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	case 'cad052gridConsumiveis':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D076_Id', $g['divId'])),
			array('colunaValor' => 'D076_Id', 'colunaTitulo' => 'D076_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'style' => 'width: 1%'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D076_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Unit.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%'),
			array('colunaValor' => 'custoTotalMaterial', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'callback' => "custoTotalMaterial", 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D076';
		$extra = <<<EOT
		LEFT JOIN D009 on D009_Id = D076_D009_Id
		LEFT JOIN D049 ON D049_Id = D009_D049_Id
		LEFT JOIN D001 ON D001_Id = D049_D001_Id
		LEFT JOIN D037 ON D037_Id = D001_D037_Id
		WHERE D076_D075_Id = '{$r_D075_Id}' 
		AND D009_C004_Id={$g['empresaAtual']}
		GROUP BY D076_Id
EOT;
		$consumiveis = "SELECT COUNT(D076_Id), SUM(D009_Valor_Custo_Unitario * D076_Quantidade) as Total 
							FROM D076
							LEFT JOIN D009 ON D009_Id = D076_D009_Id
							WHERE D076_D075_Id = '{$r_D075_Id}'";
		$sql = mysql_query($consumiveis);
		$res = mysql_fetch_array($sql);
		$materiais = $res[0];
		$custoTotal = gCorrigeNumero($res['Total']);
		/**
		* Filtro
		*/
		$gridFiltro = array(

		);
		// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Materiais<br><input type="text" value="{$materiais}" class="whiteBg" id="" size="4" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$custoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<button type="button" id="inserirConsumivel" style="margin-top: 5px;">Inserir Material de Consumo</button>
				<div style="clear:both;"></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
					$('#inserirConsumivel').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Material de Consumo', '/cad/cad052/content/cad052ContentConsumivel/', '&D001_Id=' + encodeURIComponent('{$r_D001_Id}') + '&D075_Id=' + encodeURIComponent('{$r_D075_Id}'), [360,110]);
					});
				} </script></div>
			</div>
EOT;
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D076_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Material de Consumo', '/cad/cad052/content/cad052ContentConsumivel/', '&acaoId=' + encodeURIComponent(id), [360,110]);
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
		$g['smarty']->display('grid.tpl');
	break;
}
