<?php

namespace hardness;

switch ($g['r'][3]) {	
	case 'cont003GridSaldos':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T301_Id', $g['divId'])),
			array('colunaValor' => 'T301_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D193_Codigo', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D193_Nome_Conta', 'colunaTitulo' => 'Conta'),			
			array('colunaValor' => '(select SUM(T301_Total) from T301 where T301_C004_Id = "$empresaAtual" and T301_D193_Id = D193_Id and (concat(T301_Ano,T301_Mes) >= "$dataInicial") and (concat(T301_Ano,T301_Mes) <= "$dataFinal")) as Total', 'colunaTitulo' => 'Total','colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),											
		);

		/**
		 * Definição da SQL
		 */
		$from = "T301";
		$extra = <<<EOT
			LEFT JOIN D193 ON D193_Id = T301_D193_Id			
			WHERE T301_C004_Id = '{$g['empresaAtual']}'
			GROUP BY T301_D193_Id
			ORDER BY D193_Codigo
	EOT;
	
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cont003FormSaldoBtnIncluir'),
		);
		echo Botoes::processar($botoes);
		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#cont003FormSaldoBtnIncluir').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Saldo', '/cont/cont003/content/cont003ContentCadastroSaldo/', '&tab=geral', [630,290]);
					});
				} </script></div>
	EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D193_Codigo' => array('titulo' => 'Código Conta', 'tipo' => 'texto'),	
			'D193_Nome_Conta' => array('titulo' => 'Conta', 'tipo' => 'texto'),						
			'Periodo' => array('titulo' => 'Período', 'tipo' => 'data', 'naoGerarExtra' => true),																	
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
					
		$periodo = $g['ui']->getGridFiltroCampoBusca('Periodo');
		$dataInicial = "0000-00-00";
		$dataFinal = "0000-00-00";

		$primeiraLinhaGridHead = "<tr><th colspan=\"4\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">Contas</th>";
		$primeiraLinhaGridHead .= "<th colspan=\"1\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">Total</th>";

		if(!empty($periodo[0]) && !empty($periodo[1])) {						
			$dataInicial = date("Y-m-1", strtotime(gCorrigeDataInverte($periodo[0])));
			$dataFinal = date("Y-m-1", strtotime(gCorrigeDataInverte($periodo[1])));
								
			$inicio = strtotime($dataInicial);
			$fim = strtotime($dataFinal);

			$dataInicial = date('Ym', $inicio);
			$dataFinal  = date('Ym', $fim);

			while($inicio <= $fim) {

				$mes = date('m', $inicio);
				$ano = date('Y', $inicio);
	
				$subSqlTotal = "IFNULL((select T301_Total from T301 where T301_C004_Id = '{$g['empresaAtual']}' and T301_Mes = '$mes' and T301_Ano = '$ano' and T301_D193_Id = D193_Id),0)";

				$subSqlSaldo = "IFNULL((select T301_Saldo from T301 where T301_C004_Id = '{$g['empresaAtual']}' and T301_Mes = '$mes' and T301_Ano = '$ano' and T301_D193_Id = D193_Id),0)";

				$grid[] = array('colunaValor' => $subSqlTotal, 'colunaTitulo' => "Total", 'colunaAlinhar' => 'right','forcarExibirImpressao' => true, 'colunaCasasDecimais' => 2);				
				$grid[] = array('colunaValor' => $subSqlSaldo, 'colunaTitulo' => "Saldo", 'colunaAlinhar' => 'right','forcarExibirImpressao' => true, 'colunaCasasDecimais' => 2);				

				$primeiraLinhaGridHead .= "<th colspan=\"2\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">". date('m/Y', $inicio) ."</th>";

				$inicio = strtotime("+1 month", $inicio);
			}						
		}		
		// $grid[] = array('colunaValor' => '', 'colunaTitulo' => '', 'style' => 'width:90%', 'colunaTipo' => 'livre');
		$primeiraLinhaGridHead .= "<th colspan=\"1\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">&nbsp;</th>";
			
		
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
						var chavePrimaria = 'T301_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');						
						// Ação: abrir nova janela
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Saldo - ID: ' + acaoId, '/cont/cont003/content/cont003ContentCadastroSaldo/', '&acaoId=' + encodeURIComponent(acaoId), [630,290]);
					});
					$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
				} </script></div>
	EOT;
		/**
		 * Totais Almir
		 */
		$totais = array();

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		
		$mesInicial = 	date('m',	strtotime($dataInicial));
		$anoInicial = 	date('Y', 	strtotime($dataInicial));
		$mesFinal =   	date('m', 	strtotime($dataFinal));
		$anoFinal = 	date('Y', 	strtotime($dataFinal));
	    $empresaAtual = $g['empresaAtual'];
	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		


		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) {
			ob_end_clean();
			ob_start();
			$g['smarty']->display('gridHTML.tpl');
		} else {
			$g['smarty']->display('grid.tpl');
		}
	break;
}
