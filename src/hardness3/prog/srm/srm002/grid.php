<?php
namespace hardness;

require_once('prog/srm/srm001/grid_func-callback.php');
require_once('prog/srm/srm001/grid_func-js.php');

switch($g['r'][3]){
	case 'srm002GridContasPagarFornecedor':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T015_Id', $g['divId'])),
            array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
            array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D024_Cnpj,D024_Cpf)', 'colunaTitulo' => 'CNPJ/CPF'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta', 'colunaTitulo' => 'Conta'),
            array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ',')),
            array('colunaValor' => 'concat(T015_Numero_Parcela,\'/\',T015_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc'),
            array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo'),
            array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
            array('colunaValor' => 'T015_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T015_T013_Id', 'colunaTitulo' => 'T013_Id'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT
				left join D024 on D024_Id=T015_D024_Id
				left join D073 on D073_Id=T015_D073_Id
				left join D031 on D031_Id=T015_D031_Id
				left join D014 on D014_Id=T015_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				left join T007 on T007_Id=T015_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				where 
				(T015_C004_Id={$g['empresaAtual']})
				and D024_Id='{$r_D024_Id}'
                and (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T015_Data_Pagamento']!='0000-00-00'){
				return 'linhaCinza';
			} 
		}
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
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
            'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
            'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
            'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
            'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda'),
            'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
            'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
            'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);
		
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
		 * Totalizadores
		*/
		$T015_Data_Vencimento = $g['ui']->getGridFiltroCampoBusca('T015_Data_Vencimento');
		$T015_Data_Vencimento0 = gCorrigeDataInverte($T015_Data_Vencimento[0]);
		$T015_Data_Vencimento1 = gCorrigeDataInverte($T015_Data_Vencimento[1]);
		$extraProjetado = ''; 
		if(!empty($T015_Data_Vencimento0) && empty($T015_Data_Vencimento1)){
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}'";
		} else if(empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}'";
		} else if(!empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}' AND STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}'";
		}
		if($extraProjetado != ''){
			$extraProjetado = gInsertExtraWhere($extraProjetado, "T018_C004_Id = '{$g['empresaAtual']}' AND T018_Valor_Saldo > 0 AND (T018_Ano > 0 AND T018_Mes > 0 AND D014_Dia_Vencimento > 0)");
		}
		$sqlProjetado = mysql_query("SELECT SUM(T018_Valor_Saldo) as Saldo FROM T018 LEFT JOIN D014 ON T018_D014_Id=D014_Id {$extraProjetado}");
		$projetado = mysql_fetch_array($sqlProjetado);
		if($extraProjetado == ''){ $projetado['Saldo'] = 0; }
		$valorProjetado = gCorrigeNumero($projetado['Saldo']);
		$extraProjetado = base64_encode($extraProjetado);
		if($projetado['Saldo'] > 0){
			$valorProjetadoAttr = <<<EOT
				style="background-color: red; color: white; cursor: pointer;"
				onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Projeção Orçamentária', '/fin/fin002/content/fin002ContentProjecoes/', '&extra=' + encodeURIComponent('{$extraProjetado}'), [800,500]);"
EOT;
		}
		$valorTotalPago= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Pago)", $from, $extra));
		$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T015_Valor_Saldo)", $from, $extra) + $projetado['Saldo']);
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		/**
		* Botões (Versão Acima do GRID)
		*/
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>-->
				<span class="item" style="text-align:right">Pago<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalPago}" readonly></span>
				<span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span>
				<span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span>
				<span class="item" style="text-align:right">Projetado<br><input type="text" class="whiteBg" id="inputProjetado" size="11" value="{$valorProjetado}" readonly {$valorProjetadoAttr}></span>
				<span class="item" style="text-align:right">Total pagar<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002ExtraBtns').data('extra', '{$extra__}');
			} </script></div>
EOT;
        }
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
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
}
