<?php
namespace hardness;
/**
* Este código PHP será executado ao acessar: /cad-cad002-grid-historico/
* Ele irá substituir o original (que fica dentro do .PHP, no switch/case)
*
* Caso seja uma AJAX, é necessário acrescentar este código:
* No inicio: $resposta = array('code' => true, 'data' => array());
* No final: echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
*/

/**
		 * Definição do grid tabela
		 */

		$D049 = mysql_query("select D009_D049_Id from D009 where D009_Id='{$r_acaoId}'");
		$mD049 = mysql_fetch_array($D049);

		$r_acaoId=$mD049['D009_D049_Id'];

		$grid = array(
	        array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T001_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'CampoCadastro', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'editarHistorico', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T001_T048_Id', 'colunaTitulo' => 'T001_T048_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T001_T029_Id', 'colunaTitulo' => 'T001_T029_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T001_T030_Id', 'colunaTitulo' => 'T030_T030_Id', 'colunaExibir' => false, 'chavePrimaria' => true),

            array('colunaValor' => 'T001_Lote_Validade', 'colunaTitulo' => 'Sub-item', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
            array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'substr(if((T001_Flag_Operacao=\'E\' or T001_Flag_Operacao=\'B\') and T001_T008_Id is null,Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa),1,20) as Cliente', 'colunaTitulo' => 'Cliente/Fornecedor'),
            array('colunaValor' => 'if(Cli.D024_CNPJ is null,Forn.D024_CNPJ,Cli.D024_CNPJ)', 'colunaTitulo' => 'CNPJ', 'style' => 'font-size:10px', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
            array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T001_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo', '/cad/cad002/outro/demonstrativoCusto/', 'T001_Id', 'right', 5, '', '', '320'), 'colunaExibirNivel' => 2, 'style' => 'background:lightyellow;font-weight:normal'),
            array('colunaValor' => 'T001_Valor_Custo_Medio_Unitario as Medio', 'colunaTitulo' => 'Médio', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_Preco_Unitario as Preco', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'style' => 'background:lightyellow;font-weight:bold'),
            array('colunaValor' => 'IPV', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvHistorico', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '4', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) as T001_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;background:#B6F9B1', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T001_Quantidade_Estoque_Inicial', 'colunaTitulo' => 'Inicial', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Flag_Operacao', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:#B6F9B1;font-weight:bold'),
            array('colunaValor' => 'T001_Flag_MMF', 'colunaTitulo' => 'F', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Flag_Atualiza_Custo', 'colunaTitulo' => 'C', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
            array('colunaValor' => "CONCAT(D004_Local, ' - ', T066_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaAlinhar' => 'left', 'colunaExibirNivel' => 2),                       
            array('colunaValor' => 'T001_Flag_Devolucao', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Observacao', 'colunaTitulo' => 'Observacao', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Numero_Controle', 'colunaTitulo' => 'CNPJ', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_T014_Id', 'colunaTitulo' => 'T001_T014_Id', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Percentual_Frete', 'colunaTitulo' => '%Frete', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Percentual_Custo_Financeiro', 'colunaTitulo' => '%C.F.', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:lightyellow', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_Custo_Financeiro', 'colunaTitulo' => 'C.F. R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
	        array('colunaValor' => 'T001_Quantidade_Saldo_Mes_Anterior', 'colunaTitulo' => 'Saldo mês anterior', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaExibir' => true),
        	array('colunaValor' => 'T001_Valor_Custo_Mes_Anterior', 'colunaTitulo' => 'Custo mês anterior', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaExibir' => true),
        	array('colunaValor' => 'T001_Valor_Custo_Medio_Mes_Anterior', 'colunaTitulo' => 'Médio mês anterior', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaExibir' => true),
        );

		$from = 'T001';

		$extra = <<<EOT
			left join D024 as Forn on Forn.D024_Id=T001_D024_Id
			left join D024 as Cli on Cli.D024_Id=T001_D024_Id_Cliente
			left join D006 on D006_Id=T001_D006_Id
			left join D009 on D009_Id=T001_D009_Id
			left join C004 on C004_Id=D009_C004_Id
			left join T066 on T066_Id=T001_T066_Id
			left join D004 on D004_Id=T001_D004_Id
			left join T014 on T014_Id=T001_T014_Id
			left join T013 on T013_Id=T014_T013_Id
			left join T008 on T008_Id=T001_T008_Id
			left join T007 on T007_Id=T008_T007_Id
			left join T030 on T030_Id=T001_T030_Id
			left join T029 on T030_T029_Id=T029_Id
			where D009_D049_Id='{$r_acaoId}'
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Cli.D024_C007_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
			order by T001_Data_Lancamento desc,T001_Id desc
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
	 	    if ($array['todosCampos']['T001_Flag_Operacao']=='E') 
	 	    {
	  	       return 'linhaVerdeEscuro';
	 	    }
	 	    if ($array['todosCampos']['T001_Flag_Operacao']=='C') 
	 	    {
	  	       return 'linhaVermelho';
	 	    }
	 	    if ($array['todosCampos']['T001_Flag_Operacao']=='B') 
	 	    {
	  	       return 'linhaAzul';
	 	    }
	   }	

		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="cad002FormCadastroTabHistoricoBtnIncluir">Incluir</button>-->
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002FormCadastroTabHistoricoBtnIncluir').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Histórico', '/cad/cad002/content/cad002contentEditarHistorico/','&D009_Id={$r_acaoId}', [400,350]);
				});
			} </script></div>
EOT;

		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T007 = id[0].replace(/T001_T007_Id-/, '');
					var T013 = id[1].replace(/T001_T013_Id-/, '');
					//console.log(T007 + ' - ' + T013);
					if (id > 0) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,600]);
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,600]);
					}
				});
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;
*/

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T001_Id = id[0].replace(/T001_Id-/, '');
					var T013_Id = id[1].replace(/T013_Id-/, '');
					var T007_Id = id[2].replace(/T007_Id-/, '');
					var T048_Id = id[3].replace(/T001_T048_Id-/, '');
					var T029_Id = id[4].replace(/T001_T029_Id-/, '');
					var T030_Id = id[5].replace(/T001_T030_Id-/, '');
					if (T007_Id > 0) {
						nova_janela('/hardness/almoxarifado/gerar_danfe.php?T007_Id=' + T007_Id + '',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
					} else if(T013_Id > 0){
        				nova_janela('/hardness/nota_recebida/gerar_danfe.php?T013_Id=' + T013_Id + '',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
					} else if(T048_Id > 0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Montagem', '/cad/cad002/content/cad002ContentProdutosHistoricoMontagem/','&T001_Id=' + encodeURIComponent(T001_Id) + '&acaoId=' + encodeURIComponent(T048_Id), [750,500]);
					}else if(T030_Id > 0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Divergências de Estoque - ID: ' + T029_Id, '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&acaoId=' + encodeURIComponent(T029_Id) + '&T029_Id=' + encodeURIComponent(T029_Id), [950,600]);
					}
				});
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T001_Data_Lancamento' => array('titulo' => 'Data Lançamento', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
/*		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tr').bind('click', function(data) {
			nova_janela('/hardness/nota_emitida/nf_emitida.php?T007_Id={$array['todosCampos']['T001_T007_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
			});
		} </script></div>
EOT;
		}else{
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tr').bind('click', function(data) {
        	nova_janela('/hardness/nota_recebida/espelho_nf_entrada.php?T013_Id={$array['todosCampos']['T001_T013_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
        	});
		} </script></div>
EOT;
		} 
	*/
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
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}

