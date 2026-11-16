<?php
namespace hardness;
/**
* Weiberlan
* 29/11/2011
*/
// Classes
//require_once('bibliotecas/classes/CAD001.php');
//$CAD001 = new CAD001();
// Carrega funções callback
require_once('prog/est/est004/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est004/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est004GridNotasFiscaisRecebidas':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		
		$grid = array(
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
			array('colunaValor' => 'T013A_Id', 'colunaTitulo' => 'T013A_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Flag_Estoque_Finalizado', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Numero_Controle', 'colunaTitulo' => 'Contr', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'N.F.', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT T225_T224_Id) FROM T014 LEFT JOIN T225 ON T225_Id = T014_T225_Id WHERE T014_T013_Id = T013_Id)', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'group_concat(distinct substr(Comprador.C007_Primeiro_Nome,1,10))', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Data_Entrada', 'colunaTitulo' => 'Entrada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarNivel' => '4', 'colunaEditarOculto' => true),
            array('colunaValor' => 'T013_Data_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'callback' => 'gCorrigeData', 'callbackParameter' => '1'),
            array('colunaValor' => 'T013_Data_Financeiro', 'colunaTitulo' => 'Financeiro', 'callback' => 'gCorrigeData', 'callbackParameter' => '1'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'callback' => 'coloreLinha', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'validaCampoTipo'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T013_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T013_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T013_Valor_ICMS_Substituicao', 'colunaTitulo' => 'ICMS ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T013_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T013_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T013_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'Almoxarifado', 'colunaTitulo' => 'Estoque', 'callback' => 'validaCampoAlmoxarifado', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'DANFE', 'colunaTitulo' => 'DANFE', 'callback' => 'validaCampoDanfe', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso'),
            array('colunaValor' => 'T013_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocoloo NFe'),
            array('colunaValor' => 'T013_Retorno_Nfe', 'colunaTitulo' => 'Retorno SEFAZ'),
            array('colunaValor' => 'Comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T013_Flag_Cancelada', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Flag_Financeiro_Finalizado', 'colunaTitulo' => 'Financeiro', 'colunaAlinhar' => 'center', 'colunaExibir'=> ''),
			array('colunaValor' => 'T013A_Codigo_Situacao_Documento', 'colunaTitulo' => 'Cód.Situação', 'colunaEditar' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true,'colunaEditarGeraSelect' => array(
					// Esse campo vai ser utilizado apenas no SPED FISCAL
					array('title' => '...', 'value' => ''),
					array('title' => '00 - DOCUMENTO REGULAR', 'value' => '00'),
					array('title' => '01 - ESCRITURAÇÃO EXTEMPORÂNEA DE DOCUMENTO REGULAR', 'value' => '01'),
					array('title' => '02 - DOCUMENTO CANCELADO', 'value' => '02'),
					array('title' => '03 - ESCRITURADAÇÃO EXTEMPORÂNEA DE DOCUMENTO CANCELADO', 'value' => '03'),
					array('title' => '04 - NFe, NFCe OU CTe DENEGADO', 'value' => '04'),
					array('title' => '05 - NFe, NFCe OU CTe NUMERAÇÃO INUTILIZADA', 'value' => '05'),
					array('title' => '06 - DOCUMENTO FISCAL COMPLEMENTAR', 'value' => '06'),
					array('title' => '07 - ESCRITURADAÇÃO EXTEMPORÂNEA DE DOCUMENTO COMPLEMENTAR', 'value' => '07'),
					array('title' => '08 - DOCUMENTO FISCAL EMITIDO COM BASE EM REGIME ESPECIAL OU NORMAL ESPECÍFICA', 'value' => '08'),
					
				),
			),
			array('colunaValor' => 'T013A_Valor_Total_Base_ICMS_Mono_Retido', 'colunaTitulo' => 'Base ICMS Mono Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right','colunaExibir' => false),
			array('colunaValor' => 'T013A_Valor_Total_ICMS_Mono_Retido', 'colunaTitulo' => 'ICMS Mono Ret', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'T013A_Flag_Destaca_Frete_SPED', 'colunaTitulo' => 'Destaca Frete no SPED', 'colunaExibir' => true, 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true,'colunaEditarGeraSelect' => array(
					// Esse campo vai ser utilizado apenas no SPED FISCAL
					array('title' => '...', 'value' => ''),
					array('title' => 'SIM', 'value' => 'S'),
					array('title' => 'NÃO', 'value' => 'N'),
				),
			),

        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T013";
		
		$extra = <<<EOT
		-- JOIN
			left join D024 on D024_Id=T013_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D006 on D006_Id=T013_D006_Id
			left join C007 as Comprador on Comprador.C007_Id=T013_C007_Id_Vendedor_Retorno
			left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
			left join T013A on T013_Id = T013A_T013_Id
		-- WHERE
			where T013_C004_Id='{$g['empresaAtual']}'
			and (IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1')) OR (Comprador.C007_Id IS NULL OR Comprador.C007_Id=0 OR Comprador.C007_Id='')) 
		-- GROUP BY	
			GROUP BY T013_Id
EOT;

		$extraTotais = <<<EOT
			left join D024 on D024_Id=T013_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 as Comprador on Comprador.C007_Id=T013_C007_Id_Vendedor_Retorno
			left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
			where T013_C004_Id='{$g['empresaAtual']}'
			and (IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1')) OR (Comprador.C007_Id IS NULL OR Comprador.C007_Id=0 OR Comprador.C007_Id='')) 
EOT;


		/**
		* Botões (Versão Acima do GRID)
		*/
	
		$botaoIncluir = '';
		/*if(isset($g['c029Ids'][188])){
			$botaoIncluir = '<button type="button" id="est004IncluirNotaFiscal_Btn">Incluir</button>';
		}*/

		echo <<<EOT
			<div class="buttonsBottomTop">
				{$botaoIncluir}		
				<!-- <button type="button" onclick="nova_janela('/hardness/nota_recebida_lancamento/atualizar_pedidos_venda.php',0,0,820,600,'no','no','no','no','yes','yes',0);">Atualizar Pedidos Venda</button> -->
				<!--<button type="button" id="est004AtualizarPedidosVenda_Btn">Atualizar Pedidos Venda</button>-->
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est004IncluirNotaFiscal_Btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir nota Fiscal', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&tab=geral', [990,550]);
				});
				$('#est004AtualizarPedidosVenda_Btn').unbind('click').bind('click', function(data) {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/est/est004/grid_func-ajax/atualizarPedidosVenda/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Sucesso!", request.data, buttons)
			    		divRefresh('{$g['divId']}'); 
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Erro!", request.data, buttons)
					}
				});
			});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
		    'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		    'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'texto'),
		    'T013_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    'Comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
		    'T013_Numero_Controle' => array('titulo' => 'Controle', 'tipo' => 'texto'),
		    'T013_Data_Entrada' => array('titulo' => 'Entrada', 'tipo' => 'data'),
		    'T013_Data_Entrada_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'data'),
		    'T013_Data_Financeiro' => array('titulo' => 'Financeiro', 'tipo' => 'data'),
		    'T013_Flag_Cancelada' => array('titulo' => 'NF Cancelada', 'tipo' => 'select', 'select' => array(
		        array('title' => 'TODAS', 'value' => ''),
		        array('title' => 'SIM', 'value' => 'S'),
		        array('title' => 'NÃO', 'value' => 'N'),
		      ),
		    ),
		    'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		    'T013_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo NFe', 'tipo' => 'texto'),
		    'T013_Chave_Acesso_Nfe' => array('titulo' => 'Chave NFe', 'tipo' => 'texto'),
		    'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
		    'T013_Valor_ICMS_Substituicao' => array('titulo' => 'ICMS ST', 'tipo' => 'numero'),
			'T013_Valor_Frete' => array('titulo' => 'Frete', 'tipo' => 'numero'),
			'T013_Valor_Total_IPI' => array('titulo' => 'IPI', 'tipo' => 'numero'),
			'T013_Valor_Total_ICMS' => array('titulo' => 'ICMS', 'tipo' => 'numero'),
			'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
		    'T013_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
		        array('title' => 'TODAS', 'value' => ''),
		        array('title' => 'NF-e', 'value' => '1'),
		        array('title' => 'ACP', 'value' => '2'),
				array('title' => 'NF CONSUMIDOR', 'value' => '3'),
				array('title' => 'Cupom Fiscal', 'value' => '4'),
				array('title' => 'NF', 'value' => '5'),
				array('title' => 'NF/Conta de Energia Elétrica', 'value' => '6'),
				array('title' => 'NFS de Comunicação', 'value' => '7'),
				array('title' => 'NFS de Telecomunicação', 'value' => '8'),		
				array('title' => 'NFS', 'value' => '10'),		
				array('title' => 'Outros', 'value' => '9')
		      ),
		    ),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			} else  if($array['todosCampos']['T013_Flag_Estoque_Finalizado']=='S'){
				return 'linhaCinza';
			}
		}
		/**
		 * Totais Almir
		 */
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Produtos', 'valor' => 'T013_Valor_Total_Produtos', 'titulo' => 'Valor Produtos'),
			array('nome' => 'Valor_Total', 'valor' => 'T013_Valor_Total', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_ICMS_ST', 'valor' => 'T013_Valor_ICMS_Substituicao', 'titulo' => 'Total ICMS ST'),
			array('nome' => 'Valor_Frete', 'valor' => 'T013_Valor_Frete', 'titulo' => 'Total Frete'),
			array('nome' => 'Valor_IPI', 'valor' => 'T013_Valor_Total_IPI', 'titulo' => 'IPI R$'),
			array('nome' => 'Valor_ICMS', 'valor' => 'T013_Valor_Total_ICMS', 'titulo' => 'ICMS R$'),

		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est004IncluirNotaFiscal_Btn',  'exibirCallback' => 'return isset($g["c029Ids"][188]);'),
		);
		echo Botoes::processar($botoes);
	
		//if (!personalizacaoTotais()) {
			/**
			* Totais (Versão Acima do GRID)
			*/
			//RETIRADO POIS ESTAVA FORA DO PADRÃO E NÃO ESTAVA FUNCIONANDO PARA ALGUNS CLIENTES POR CONTA DO C004_Id
			// $C004_Id = false;
	  //       preg_match("/T013_C004_Id=\'([^\'])+\'/", $extra, $matches);
	  //       if (isset($matches[1])) { $C004_Id = $matches[1]; }
			// O extra utilizados para o total é diferente do utilizado para montar o grid
// 			$extraTotais1 = <<<EOT
// 			    left join D024 on D024_Id=T013_D024_Id
// 			    left join D018 on D018_Id=D024_D018_Id
// 			    left join D006 on D006_Id=T013_D006_Id
// EOT;

			// $extraTotais1 .= ($C004_Id > 0 ? " WHERE T013_C004_Id='{$C004_Id}' AND T013_Flag_Cancelada!='S'" : "");
	  //       $extraTotais1 = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais1);
			// $extraTotais = <<<EOT
			//     left join T014 on T014_T013_Id=T013_Id
			//     left join T012 on T012_Id=T014_T012_Id
			//     left join D009 on D009_Id=T014_D009_Id
			//     left join D049 on D049_Id=D009_D049_Id
			//     left join D001 on D001_Id=D049_D001_Id
			//     left join D024 on D024_Id=T013_D024_Id
			//     left join D018 on D018_Id=D024_D018_Id
			//     left join D006 on D006_Id=T014_D006_Id
   //              left join T225 on T225_Id=T014_T225_Id
   //              left join T224 on T224_Id=T225_T224_Id
   //              left join C007 as Comprador on Comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			// 	left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
// EOT;
			// $extraTotais .= ($C004_Id > 0 ? " WHERE T013_C004_Id='{$C004_Id}' AND T013_Flag_Cancelada!='S' and (IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1')) OR (Comprador.C007_Id IS NULL OR Comprador.C007_Id=0 OR Comprador.C007_Id=''))" : "");

	       /* $extraTotais = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais);
			$valorTotal  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(concat(T013_Valor_Total,T013_Id))", $from, $extraTotais)));
			$valorTotal1 = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_Total_Produtos)", $from, $extraTotais)));
			$valorTotal2 = $g['sqlAuto']->pegarSqlCampo("count(T013_Id)", $from, $extraTotais);
			$totalICMSST = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_ICMS_Substituicao)", $from, $extraTotais)));
			$totalFrete  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_Frete)", $from, $extraTotais)));
			$totalIPI    = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_Total_IPI)", $from, $extraTotais)));
			$totalICMS   = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_Total_ICMS)", $from, $extraTotais)));

			
			
			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Notas Recebidas<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS ST<br><input type="text" value="{$totalICMSST}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total Frete<br><input type="text" value="{$totalFrete}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total IPI<br><input type="text" value="{$totalIPI}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS<br><input type="text" value="{$totalICMS}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);

			

			
		}*/
			
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var id = id[0].replace(/T013_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal: ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + id + '&tabela=T013', 'auto', false, false);
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
  	
	case 'est004GridNotasFiscaisReceber':				
		
		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor'=> 'receberNota', 'colunaTitulo'=>'Receber', 'colunaTipo'=>'livre', 'callback'=>'receberNota','style'=>'padding-left:15px', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número' ),			
			array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback'=>'validaTipo'),			
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Origem'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Destino'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
		);

		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
				    LEFT join D024 on D024_Id=T007_D024_Id
				    LEFT join D006 on D006_Id=T007_D006_Id
				    LEFT join C004 on C004_Id=T007_C004_Id
				    WHERE T007_D024_Id='{$g['C004']['C004_D024_Id']}'
				    AND T007_Data_Emissao>=DATE(DATE_ADD(NOW(), INTERVAL -90 DAY))
				    AND T007_Flag_Emitida_Recebida!='R'
				    AND T007_C004_Id!='{$g['empresaAtual']}'
			    	AND T007_Flag_Cancelada!='S'
			    	AND D006_Flag_Entrada_Saida='S'
					AND T007_Numero_Nota_Fiscal NOT IN (SELECT T013_Numero_Nota_Fiscal FROM T013 WHERE T013_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T013_D024_Id=C004_D024_Id and T013_C004_Id='{$g['empresaAtual']}')
				    
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
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'C004_Nome_Abreviado' => array('titulo' => 'Origem', 'tipo' => 'texto'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),			
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
echo <<<EOT
   <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
         $('#{$g['divId']} .grid').css('width', '1%');
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
		
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Notas Pendentes<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/		
		
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
	
	
	case 'est004GridProdutosNota':
		global $g;

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		//Busca destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'C');

		// ENCONTRA A CFOP DO CABECALHO DA NOTA, SERA UTILIZADO MAIS PARA BAIXO
		$sql = <<<EOT
			SELECT D006_Flag_Venda_Compra_Outros,
			       T013_D024_Id, 
				   T013_Flag_Obrigatorio_OC
			  FROM T013
		 LEFT JOIN D006 ON D006_Id=T013_D006_Id
			 WHERE T013_Id = '{$r_acaoId}'
EOT;
		$res = mysql_query($sql);
		$row1 = mysql_fetch_assoc($res);
		$D006_Flag_Venda_Compra_Outros = $row1['D006_Flag_Venda_Compra_Outros'];
		$D024_Id = $row1['T013_D024_Id'];
		$T013_Flag_Obrigatorio_OC = $row1['T013_Flag_Obrigatorio_OC'];

		$campoVinculoExibir = ($D006_Flag_Venda_Compra_Outros != 'C') ? 'T007_Numero_Nota_Fiscal' : 'T225_T224_Id';
		$exibirNF = ($D006_Flag_Venda_Compra_Outros != 'C') ? true : false;
		$exibirOC = ($D006_Flag_Venda_Compra_Outros == 'C') ? true : false;

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';

		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExcluir'),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/?T014_Id=true', 'T014_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 650), 'style' => 'width: 1%;'),            
            array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibir' => false),            
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibir' => false),
            array('colunaValor' => 'IF(Informada.D006_Id > 0, Informada.D006_Flag_Venda_Compra_Outros, Original.D006_Flag_Venda_Compra_Outros)', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibir' => false),
            array('colunaValor' => 'D083_Id', 'colunaTitulo' => 'D083_Id', 'colunaExibir' => true, 'chavePrimaria' => true),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T014_Flag_Conferencia_Cega', 'colunaTitulo' => 'Conf', 'colunaConfCegaFlag' => 'S', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'colunaEditarNivel' => 4, 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'CONCAT(T014_Numero_Pedido_Compra," / ", T014_Item_Pedido_Compra)', 'colunaTitulo' => "OC/Item XML", 'colunaAlinhar' => 'center', 'colunaExibir' => $exibirOC),
            array('colunaValor' => 'CONCAT(T225_T224_Id," / ",T225_Item)', 'colunaTitulo' => "OC / Item", 'colunaAlinhar' => 'center', 'colunaExibir' => $exibirOC, 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '1000,600')),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => "NF", 'colunaAlinhar' => 'center', 'colunaExibir' => $exibirNF),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T014A_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T014_Codigo_Produto', 'colunaTitulo' => 'Cód Forn', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'validaCampoCadastroProduto', 'callbackParameter' => array('Cadastro Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,600'), 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center', 'colunaConfCegaCod' => 'S'),
            array('colunaValor' => 'D083_Codigo_Barras', 'colunaTitulo' => 'EAN - Forn', 'colunaConfCegaCod' => 'S', 'colunaEditarOculto' => true,'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaEditar' => true),
			array('colunaValor' => 'T014_Codigo_Barras', 'colunaTitulo' => 'EAN XML', 'colunaEditarOculto' => true),
			array('colunaValor' => 'T014A_Codigo_Barras_Tributavel', 'colunaTitulo' => 'EAN Tributavel XML', 'colunaEditarOculto' => true),
            array('colunaValor' => 'T014_T066_Id', 'colunaTitulo' => 'Locação', 'callback' => 'T014_T066_Id_Local', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição produto nota', 'callback' => 'coloreLinhaProdutos', 'colunaConfCegaDescricao' => 'S'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T014_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaEditarOculto' => true),
            array('colunaValor' => 'T014_Flag_Origem_Produto', 'colunaTitulo' => 'O', 'colunaEditarOculto' => true),
            array('colunaValor' => 'Original.D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T014_D006_Id', 'colunaTitulo' => 'CFOP', 'colunaEditarGeraSelect' => array('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}", 'WHERE D006_Flag_Inativo!="S" order by D006_Codigo_CFOP'), 'colunaEditar' => true, 'callback' => 'T014_D006_Id_CFOP', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true),
            array('colunaValor' => 'concat("CFOP")', 'colunaTitulo' => 'Demonstrativo', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T014A_D006_Id_Info', 'Demonstrativo CFOP', 'T014A_Id', 'left', '', '500')),
            array('colunaValor' => 'T014A_D014_Id', 'colunaTitulo' => 'Plano de Contas', 'colunaEditarGeraSelect' => array('D014', 'D014_Id', 'D014_SubConta', 'WHERE D014_Flag_Ativo="S" order by D014_SubConta', true), 'colunaEditar' => true, 'callback' => 'T014A_D014_Id_PlanoContas', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D037_Prod.D037_Unidade as D037_Id_Prod', 'colunaTitulo' => 'UN Prod', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_Unidade', 'colunaTitulo' => 'UN NF', 'colunaAlinhar' => 'center', 'callback' => 'campoUnidadeNotaFiscal'),
            array('colunaValor' => 'D037_Conv.D037_Id as D037_Id_Conv', 'colunaTitulo' => 'Conv.D037_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),			
			array('colunaValor' => 'D184_Conv.D184_D037_Id as D184_D037_Id_Conv', 'colunaTitulo' => 'Conv.D184_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),			
			array('colunaValor' => 'D001_D037_Id', 'colunaTitulo' => 'D001_D037_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),					            
            array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '3', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'style' => 'background: var(--celula-amarelo);', 'colunaAlinhar' => 'right', 'colunaConfCegaQtd' => 'S'),
            array('colunaValor' => 'T014_Valor_Custo_Unitario', 'colunaTitulo' => 'Preço unit.', 'colunaEditarOculto' => true, 'extra' => 'style="width:300px;"', 'colunaCasasDecimais' => '6', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'style' => 'background: var(--celula-amarelo);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Preço total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'T014_Valor_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => 'DIFAL R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'T014_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'T014_Valor_ICMS_Substituicao', 'colunaTitulo' => 'ST R$', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'T014_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T014_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T014_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'IFNULL(T014A_Valor_Frete_NFS,0)', 'colunaTitulo' => 'Frete NFS R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T014_Valor_FCP_ST ', 'colunaTitulo' => 'FCP ST R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-verde)'),			
			array('colunaValor' => 'T014_Valor_Despesas_Acessorias ', 'colunaTitulo' => 'Despesas Acess.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-verde)'),			
			array('colunaValor' => 'T014_Valor_Total_Desconto', 'colunaTitulo' => 'Desconto R$', 'colunaCasasDecimais' => '2', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T014A_Valor_Acrescimo_Custo', 'colunaTitulo' => 'Acréscimo Custo R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '6', 'style' => 'background:var(--celula-vermelho);', 'colunaEditarOculto' => true, 'colunaExibir' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014'),
			array('colunaValor' => 'T014_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo final', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '6', 'style' => 'background:var(--celula-azul);'),
			array('colunaValor' => 'T014A_Valor_Custo_Final_Liquido_Unitario', 'colunaTitulo' => 'Custo final Líq.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '6', 'style' => 'background:var(--celula-azul);', 'colunaExibir' => false),
            array('colunaValor' => 'T014_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red.ICMS', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICM IE', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_Aliquota_ICMS_Interno', 'colunaTitulo' => '%ICM Int', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_MVA_Ajustada', 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo)'),
			array('colunaValor' => 'T014_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo)'),
			array('colunaValor' => 'T014_Percentual_FCP_ST', 'colunaTitulo' => '%FCP ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-amarelo)'),
            array('colunaValor' => 'T014_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICMS R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditarOculto' => true, 'style' => 'background:#var(--celula-vermelho-claro)'),
            array('colunaValor' => 'T014_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditarOculto' => true, 'style' => 'background:#var(--celula-vermelho-claro)'),
            array('colunaValor' => 'T014_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditarOculto' => true, 'style' => 'background:#var(--celula-vermelho-claro)'),
            array('colunaValor' => 'T014_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:#var(--celula-vermelho-claro)'),                        
			array('colunaValor' => 'T014_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:#var(--celula-vermelho-claro)'), 
			array('colunaValor' => 'T014_Valor_Base_FCP_ST', 'colunaTitulo' => 'Base FCP ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:#var(--celula-vermelho-claro)'),
            array('colunaValor' => 'T014_Situacao_Tributaria_IPI', 'colunaTitulo' => 'ST IPI', 'colunaExibir' => false,),
            array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => 'OC', 'colunaExibir' => false),
            array('colunaValor' => 'T014_D009_Id', 'colunaTitulo' => 'IdProd', 'colunaExibir' => false),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'IdEmpr', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição produto cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'comparaDescricao', 'colunaTitulo' => '%', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'colunaTipo' => 'livre', 'callback' => 'comparaDescricao'),
            array('colunaValor' => 'T014_Situacao_Tributaria_PIS', 'colunaTitulo' => 'ST PIS', 'colunaExibir' => false, 'colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T014_Situacao_Tributaria_COFINS', 'colunaTitulo' => 'ST COFINS', 'colunaExibir' => false, 'colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T014_Modalidade_ICMS_Substituicao', 'colunaTitulo' => 'modBCST', 'colunaExibir' => false, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T014_Modalidade_ICMS', 'colunaTitulo' => 'modBC', 'colunaExibir' => false, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T014_Flag_Divergencia', 'colunaTitulo' => 'Divergência', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Flag_Autorizado', 'colunaTitulo' => 'Autorizado', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_Id_Duplicado', 'colunaTitulo' => 'Id Dupl', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T013_Flag_Estoque_Finalizado', 'colunaTitulo' => 'Travar estoque', 'colunaExibir' => false),
			array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'T001_Id', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4, 'callback' => 'insereRegistroT001'),			
			array('colunaValor' => "D037_Conv.D037_Unidade", 'colunaTitulo' => 'UN CONV', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => "D184_Conv.D184_Tipo_Operacao", 'colunaTitulo' => 'Tipo Operação', 'colunaAlinhar' => 'center', 'callback' => 'campoTipoOperacao', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'D184_Conv.D184_Quantidade_Conversao', 'colunaTitulo' => 'Quantidade Conversão', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Quantidade', 'colunaTitulo' => 'Quantidade NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '3', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Valor_Custo_Unitario', 'colunaTitulo' => 'Preço unit NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '6', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Aliquota_IPI', 'colunaTitulo' => '%IPI NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Aliquota_PIS', 'colunaTitulo' => '%PIS NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Aliquota_COFINS', 'colunaTitulo' => '%COFINS NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Aliquota_ICMS', 'colunaTitulo' => '%ICMS NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICMS IE NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Aliquota_ICMS_Interno', 'colunaTitulo' => '%ICMS Int NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Valor_ICMS_Substituicao', 'colunaTitulo' => 'ST R$ NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014A_Valor_FCP_ST', 'colunaTitulo' => 'FCP ST R$ NF Original', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);'),
			/* [REFORMA TRIBUTÁRIA]
			==============================================================*/
			array('colunaValor' => 'T014A_Valor_Total_Reforma', 'colunaTitulo' => 'Total RT c/ impostos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Situacao_Tributaria_CBS_IBS', 'colunaTitulo' => 'CST CBS/IBS', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Classificacao_Tributaria_CBS_IBS', 'colunaTitulo' => 'Classificação Trib. CBS/IBS', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Situacao_Tributaria_CBS_IBS_Regular', 'colunaTitulo' => 'CST CBS/IBS Reg.', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Classificacao_Tributaria_CBS_IBS_Regular', 'colunaTitulo' => 'Classificação Trib. CBS/IBS Reg.', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_CBS', 'colunaTitulo' => 'CBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Reducao_CBS', 'colunaTitulo' => '% Red. CBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_CBS_Efetiva', 'colunaTitulo' => 'CBS Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_CBS_Regular', 'colunaTitulo' => 'CBS Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Reducao_CBS_Regular', 'colunaTitulo' => '% Red. CBS Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_CBS_Efetiva_Regular', 'colunaTitulo' => 'CBS Reg. Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Diferimento_CBS', 'colunaTitulo' => '% Dif. CBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_UF', 'colunaTitulo' => 'IBS UF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Reducao_IBS_UF', 'colunaTitulo' => '% Red. IBS UF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_UF_Efetiva', 'colunaTitulo' => 'IBS UF Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_UF_Regular', 'colunaTitulo' => 'IBS UF Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Reducao_IBS_UF_Regular', 'colunaTitulo' => '% Red. IBS UF Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_UF_Efetiva_Regular', 'colunaTitulo' => 'IBS UF Reg. Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Diferimento_IBS_UF', 'colunaTitulo' => '% Dif. IBS UF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_Municipal', 'colunaTitulo' => 'IBS Mun.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Reducao_IBS_Municipal', 'colunaTitulo' => '% Red. IBS Mun.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_Municipal_Efetiva', 'colunaTitulo' => 'IBS Mun. Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_Municipal_Regular', 'colunaTitulo' => 'IBS Mun. Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Reducao_IBS_Municipal_Regular', 'colunaTitulo' => '% Red. IBS Mun. Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Aliquota_IBS_Municipal_Efetiva_Regular', 'colunaTitulo' => 'IBS Mun. Reg. Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Percentual_Diferimento_IBS_Municipal', 'colunaTitulo' => '% Dif. IBS Mun.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_CBS', 'colunaTitulo' => 'CBS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_CBS_Regular', 'colunaTitulo' => 'CBS Reg. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_Diferimento_CBS', 'colunaTitulo' => 'Dif. CBS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_IBS_UF', 'colunaTitulo' => 'IBS UF R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_IBS_UF_Regular', 'colunaTitulo' => 'IBS UF Reg. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_Diferimento_IBS_UF', 'colunaTitulo' => 'Dif. IBS UF R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_IBS_Municipal', 'colunaTitulo' => 'IBS Mun. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_IBS_Municipal_Regular', 'colunaTitulo' => 'IBS Mun. Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_Diferimento_IBS_Municipal', 'colunaTitulo' => 'Dif. IBS Mun. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_IBS', 'colunaTitulo' => 'IBS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T014A_Valor_Base_CBS_IBS', 'colunaTitulo' => 'Base CBS/IBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			/*==============================================================*/
			array('colunaValor' => 'T014A_Valor_Base_ICMS_Mono_Retido', 'colunaTitulo' => 'BC ICMS Mono Ret.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);', 'colunaExibir' => false),
			array('colunaValor' => 'T014A_Valor_ICMS_Mono_Retido', 'colunaTitulo' => 'R$ ICMS Mono Ret.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);', 'colunaExibir' => false),
			array('colunaValor' => 'T014A_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => $destinoProdutos, 'style' => 'background: var(--celula-vermelho);font-size:12px'),	  
			array('colunaValor' => 'T014A_Ficha_Conteudo_Importacao', 'colunaTitulo' => 'Numero FCI', 'colunaEditarOculto' => true),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T014";
		
		$extra = <<<EOT
		-- JOIN
			LEFT JOIN T013 ON T013_Id=T014_T013_Id
			LEFT JOIN T012 ON T012_Id=T014_T012_Id
			LEFT JOIN T225 ON T225_Id=T014_T225_Id
			LEFT JOIN T224 ON T224_Id=T225_T224_Id
			LEFT JOIN D009 ON D009_Id=T014_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D083 ON D001_Id=D083_D001_Id and D083_D024_Id=T013_D024_Id
			LEFT JOIN D037 as D037_Prod ON D037_Prod.D037_Id=D001_D037_Id
			LEFT JOIN D037 as D037_Conv ON D037_Conv.D037_Id=T014_D037_Id	
			LEFT JOIN D184 as D184_Prod ON D184_D001_Id = D001_Id			
			LEFT JOIN D184 as D184_Conv ON D184_Conv.D184_D001_Id = D001_Id and D184_Conv.D184_D037_Id = T014_D037_Id 
			LEFT JOIN T014A ON T014A_T014_Id = T014_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN T001 ON T001_T014_Id=T014_Id
			LEFT JOIN D006 as Original ON Original.D006_Id=T014_D006_Id_Original
			LEFT JOIN D006 as Informada ON Informada.D006_Id=T014_D006_Id
			LEFT JOIN T066 ON T066_Id=T014_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN T008 ON T008_Id=T014_T008_Id
			LEFT JOIN T007 ON T007_Id=T008_T007_Id									
			LEFT JOIN D005 ON  D005_Classificacao_Fiscal = T014_Classificacao_Fiscal 
			LEFT JOIN D149 ON  D149_D005_Id = D005_Id and D149_C004_Id = '{$g['empresaAtual']}'
			LEFT JOIN C004 ON  C004_Id = '{$g['empresaAtual']}'				
			LEFT JOIN D024 ON D024_Id = T013_D024_Id
			LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T013_C004_Id											
		-- WHERE	
			where T014_T013_Id='{$r_acaoId}'
		-- GROUP BY	
			group by T014_Id
		-- ORDER BY
			order by T014_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T014_Codigo_Produto' => array('titulo' => 'Cod. Forn.', 'tipo' => 'text'),
            'D001_Codigo_Produto' => array('titulo' => 'Cod. Prod.', 'tipo' => 'text'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'text'),
            'T014_Flag_Conferencia_Cega' => array('titulo' => 'Conferido (S/N)?', 'tipo' => 'text'),
        );
		
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);


		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);	

		/**
         * Botões Almir
         */
		$botoes = array(
			array('titulo' => 'Aplicar Conversão Unidade', 'id' => 'est004AplicarConversao'),
			array('titulo' => 'Atualizar Alíquotas', 'id' => 'est004AtualizarAliquotas'),
			array('titulo' => 'Aplicar Regra de CFOP', 'id' => 'est004AtualizarCFOPRegras'),
		);        
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totalizadores (Versão Acima do GRID)
		*/
		
		/*if (!personalizacaoTotais()) {
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);		
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}*/	

		$sqlSelecao   = gComprimeSql("SELECT T014_Id, T014_Unidade, D001_Id, T013_Flag_Travar_Nota, D037_Prod.D037_Id as D037_Id_Prod, T014A_Quantidade, T014A_Valor_Custo_Unitario  FROM $from $extra");
		echo gProcessaEtapas('Aplicar conversão','EST004', "EST004AplicarConversao()", "$sqlSelecao",'est004AplicarConversao');	

		$sqlSelecao = gComprimeSql("SELECT
		 						T013_Flag_Travar_Nota,
								D009_Id,
								T014_Id,
								T013_Id,								
								T014_Valor_Frete,
								T014_Valor_Despesas_Acessorias,
								T014_Valor_Seguro,
								T014_Valor_Total_Custo,								
								T014_Quantidade,	
								T014_Valor_Total_Custo_Final,
								T013_Valor_Frete,
								T013_Valor_Seguro,
								T013_Valor_Despesas_Acessorias,
								T014A_Flag_Destino_Produto,
								Informada.D006_Flag_Somar_Despesas_Base_IPI,
								Informada.D006_Flag_Ajustar_MVA,
								Informada.D006_Flag_Somar_IPI_Base_ICMS,
								Informada.D006_Flag_Somar_Fator_ICMS_Dif_Base,
								Informada.D006_Modalidade_ICMS_Substituicao,
								Informada.D006_Flag_Calcula_FCP_ST,
								Informada.D006_Flag_Forma_Calculo_COFINS,								
								Informada.D006_Flag_Deduz_ICMS_Base_COFINS,
								Informada.D006_Flag_Forma_Calculo_PIS,
								Informada.D006_Flag_Deduz_ICMS_Base_PIS,
								D009_Preco_Pauta_ST,
								D053_Calculo_ST_Por_Dentro_Compra,
								T014_Valor_Total_Desconto,
								Informada.D006_Flag_Somar_Despesas_Base_PIS,
								Informada.D006_Flag_Somar_Despesas_Base_COFINS,
								T013_Flag_Calcular_ICMS_Despesas_Acessorias
					FROM $from $extra");									
		echo gProcessaEtapas('Atualizar alíquotas','EST004', "EST004ValidarVinculoProduto('{$r_acaoId}')|false;EST004AtualizarAliquotas();EST004AtualizarCabecalhoNota('{$r_acaoId}')|false", "$sqlSelecao",'est004AtualizarAliquotas');		

		echo gProcessaEtapas('Aplicar Regra CFOP','EST004', "EST004AtualizarCFOP('{$r_acaoId}')|false;EST004AtualizarAliquotas();EST004AtualizarCabecalhoNota('{$r_acaoId}')|false", "$sqlSelecao",'est004AtualizarCFOPRegras');	

        /**
        * Ação ao clicar em uma linha do grid
        */
    	$novoIdTelaForm = uniqid();
		
		if(isset($g['c029Ids'][189])){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var linhaId = $(this).closest('tr').attr('id') || '';
						var t014Match = linhaId.match(/(?:^|\|)T014_Id-([^|]*)/);
						var flagTipoMatch = linhaId.match(/(?:^|\|)IF\(Informada\.D006_Id > 0, Informada\.D006_Flag_Venda_Compra_Outros, Original\.D006_Flag_Venda_Compra_Outros\)-([^|]*)/);
						var T014_Id = (t014Match && t014Match[1]) ? t014Match[1] : '';
						var Flag_Tipo = (flagTipoMatch && flagTipoMatch[1]) ? flagTipoMatch[1] : '';
						if(Flag_Tipo == 'C' && '{$T013_Flag_Obrigatorio_OC}' == 'S'){
							//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItemNovo/', '&acaoId=' + encodeURIComponent(T014_Id) + '&tabela=T014', [950,500]);
						} else if(Flag_Tipo == 'DV'){
							if('{$g['C031']['vincularProdutoNFDevolucaoSemVinculoNFSaida']}' == 'N'){
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItem/', '&acaoId=' + encodeURIComponent(T014_Id) + '&D024_Id={$D024_Id}&tabela=T014', [500,150]);
							}else{
								var \$buttons = {
									"Notas Emitidas": function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItem/', '&acaoId=' + encodeURIComponent(T014_Id) + '&D024_Id={$D024_Id}&tabela=T014', [500,150]);
										$(this).dialog("destroy").remove();
									},
									"Cadastro Produto": function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItemProduto/', '&acaoId=' + encodeURIComponent(T014_Id) + '&tabela=T014&divRefresh=' + encodeURIComponent('{$g['divId']}'), [500,150]);
										$(this).dialog("destroy").remove();
									},
									"Fechar": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Vincular Item', 'CFOP deste item é de devolucao, selecione a opção abaixo', \$buttons);
							}

						} else {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItemProduto/', '&acaoId=' + encodeURIComponent(T014_Id) + '&tabela=T014&divRefresh=' + encodeURIComponent('{$g['divId']}'), [500,150]);
						}
					});
					$('#{$g['divId']} .grid td[onclick*="D083_Codigo_Barras"]')
						.addClass('est004AbrirCadCodForn')
						.each(function() {
							$(this).attr('data-onclick-original', $(this).attr('onclick') || '');
						})
						.removeAttr('onclick')
						.unbind('click')
						.bind('click', function(event) {
							var \$tr = $(this).closest('tr');
							var linhaId = \$tr.attr('id') || '';
							var d001Match = linhaId.match(/(?:^|\|)D001_Id-([^|]*)/);
							var d024Match = linhaId.match(/(?:^|\|)D024_Id-([^|]*)/);
							var D001_Id = (d001Match && d001Match[1]) ? d001Match[1] : '';
							var D024_Id = (d024Match && d024Match[1]) ? d024Match[1] : '';
							var D083_Id = '';
							var todosCamposRaw = \$tr.attr('todoscampos') || '';
							if (todosCamposRaw) {
								try {
									var todosCampos = JSON.parse(todosCamposRaw);
									D083_Id = todosCampos.D083_Id || '';
								} catch (e) {
									D083_Id = '';
								}
							}
							event.stopPropagation();
							if (D083_Id && D083_Id != '0') {
								var onClickOriginal = $(this).attr('data-onclick-original') || '';
								if (onClickOriginal) {
									(new Function(onClickOriginal)).call(this);
								}
							} else {
								abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Código Barras Fornecedor', '/cad/cad002/content/cad002ContentCadastroEmpresas/', '&tab=geral&D001_Id=' + encodeURIComponent(D001_Id) + '&D083_Id=' + encodeURIComponent(D083_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [650, 250]);
							}
							return false;
						});
					$('#{$g['divId']} .grid .campoEditavel').not('.est004AbrirCadCodForn').unbind('click').bind('click', function(event) { return false; });
				} </script></div>
EOT;
		} else {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$g['divId']} .grid tbody tr').css('cursor', 'no-drop');
				} </script></div>
EOT;
		}
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );	
		
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			// $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est004/content/est004ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
			// });
			
			// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_tabela_T014(T014_Id, campoEditado, valorAntigo, novoValor) {
					T014_Id = T014_Id.replace(/T014_Id-/, '');
					if (campoEditado == 'T014_Quantidade') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T014_Id, 'T014_Id'); // grid
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T014_Valor_Custo_Unitario') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T014_Id, 'T014_Id'); // grid
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T014_Aliquota_IPI') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T014_Id, 'T014_Id'); // grid
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'D083_Codigo_Barras') {
						$.getJSON('/est/est004/grid_func-ajax/atualizaCodigoFornecedor/?ajax=true&T014_Id='      + encodeURIComponent(T014_Id) + 
																					      		'&campoEditado=' + encodeURIComponent(campoEditado) +
																					      		'&valorAntigo='	 + encodeURIComponent(valorAntigo) +
																					      		'&novoValor='	 + encodeURIComponent(novoValor) +  
																					      		'&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T014_Id, 'T014_Id'); // grid
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T014A_Valor_Acrescimo_Custo') {
						divRefresh('{$g['divId']}', true, false, false, T014_Id, 'T014_Id'); // grid			
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
	
	case 'est004GridPagamentoNota':
			$acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T154_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T154_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T154_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T154_Prazos_Dias"),
				array('colunaValor' => 'T154_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData', 'colunaEditar'=>true,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T154_Prazos_Dias"),
				array('colunaValor' => 'est004_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "est004_Dia_Semana"),
				//array('colunaValor' => 'T154_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T154_Prazos_Dias"),
				array('colunaValor' => 'T154_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2'),
				array('colunaValor' => 'T154_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', ' order by D031_Portador', true), 'callback' => 'T154_D031_Id_Portador'),				
				array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T154';

			$extra = <<<EOT
				left join D031 on D031_Id=T154_D031_Id
				WHERE T154_T013_Id = {$acao_Id}
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
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T154_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});				
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T154_Prazos_Dias(T154_Id, campoEditado, valorAntigo, novoValor) {
					T154_Id = T154_Id.replace(/T154_Id-/, '');
					if (campoEditado == 'T154_Prazos_Dias') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarParcelas/?ajax=true&T154_Id='      		+ encodeURIComponent(T154_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T154_Data_Vencimento') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarParcelas/?ajax=true&T154_Id='      		+ encodeURIComponent(T154_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T154_Valor_Parcela') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarParcelas/?ajax=true&T154_Id='      		+ encodeURIComponent(T154_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
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

			$novoIdTelaForm = uniqid();
		break;

		case 'est004GridImportarOC':
			$T013_Id = (isset($r_T013_Id)) ? $r_T013_Id : false;
			
			$cClausula  = " select ";
			$cClausula .= " * ";
			$cClausula .= " from T013  ";
			$cClausula .= " left join D024 on D024_Id=T013_D024_Id ";
			$cClausula .= " where T013_Id='$T013_Id' ";
			$cSql_T013=mysql_query($cClausula);
			$mT013=mysql_fetch_array($cSql_T013);
			$T013_D024_Id=$mT013['T013_D024_Id'];
			$T013_Id=$mT013['T013_Id'];
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'O.C.', 'chavePrimaria' => true),
				array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id.', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'T225A_Id', 'colunaTitulo' => 'T225A_Id.', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-verde);'),
				array('colunaValor' => 'T225_Quantidade_Pendente_3(T225_Id)', 'colunaTitulo' => 'Quant Pend', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-azul);'),
				//array('colunaValor' => 'ifnull(T086_Quantidade,0) as T086_Quantidade', 'colunaTitulo' => 'Recebido', 'callback'=>'editarT086_Quantidade', 'callbackParameter'=>$T013_Id), //ifnull(T086_Quantidade,0) as 
				array('colunaValor' => 'T225A_Quantidade_Recebida', 'colunaTitulo' => 'Recebido', 'colunaEditar' => true, 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-claro);'), //ifnull(T086_Quantidade,0) as 
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód. Fornec.'),
				array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
				array('colunaValor' => 'T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Valor', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
				array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T225';

			$extra = <<<EOT
				left join T224 on T224_Id=T225_T224_Id
				left join D009 on T225_D009_Id=D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D049_D001_Id=D001_Id
				left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='[[T013_D024_Id]]'
				left join D005 on D009_D005_Id(T225_D009_Id)=D005_Id
				left join T014 on T014_T225_Id=T225_Id
				left join T225A on T225_Id = T225A_T225_Id
				where T224_C004_Id='{$g['empresaAtual']}'
				and T225_Flag_Cancelada!='S'
				and T224_D024_Id='{$T013_D024_Id}'
				and ifnull(T225_Quantidade_Pendente_3(T225_Id),0) >0 
				and T224_Data_Emissao>=date_sub(current_date(), interval 6 month)
				AND (T014_T225_Id IS NULL OR T014_T225_Id='')
				order by T224_Id,T225_Descricao_Produto
EOT;
			// left join T086 on T086_T012_Id=T012_Id and T086_T013_Id='[[T013_Id]]'
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T224_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
			);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totalizadores (Versão Acima do GRID)
		*/
		
/* 		if (!personalizacaoTotais()) {

			$extraNovo = str_replace('GROUP BY T012_Id', '', $extra);
			
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraNovo);
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		} */

/*         echo "<div class='buttonsBottomTop'>";
            echo '<button type="button" id="est004DefinirQuantidadeRecebidaOC">Definir quantidade recebida</button>';
            echo '<button type="button" id="est004LimparQuantidadeRecebidaOC">Limpar quantidade</button>';
			$botoesEsq .= gGeraBotao("Preencher OC","EST004PopulaOC");
			gProcessaEtapas('Preencher OC','EST004', "popularOCNotaFiscal($r_acaoId,{$mT013['T013_D024_Id']})|false", "",'EST004PopulaOC');
        echo "</div>"; */
 
		$botoes = array(
			array('titulo' => "Definir quantidade recebida", 'id' => 'est004DefinirQuantidadeRecebidaOC'),
			array('titulo' => "Limpar quantidade", 'id' => 'est004LimparQuantidadeRecebidaOC', 'desabilitar' => $disabledFinanceiro),

		); 

		echo Botoes::processar($botoes);

			
			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

			$extra_ = base64_encode($extra);
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

			$novoIdTelaForm = uniqid();

			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est004ConcluirInclusaoItens">Concluir inclusão dos itens</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est004ConcluirInclusaoItens').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004ConcluirInclusaoItens/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
											divSetarAcaoId('{$g['divIdParent']}', '', 'cacheDados');
										 	divRefresh('{$g['divIdParent']}', false);
											fecharJanela("{$g['divId']}");
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Concluir inclusão dos itens', 'Você tem certeza? ', \$buttons);
				});

				$('#est004DefinirQuantidadeRecebidaOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004DefinirQuantidadeRecebidaOC/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Inserir quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});

				$('#est004LimparQuantidadeRecebidaOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004LimparQuantidadeRecebidaOC/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Limpar quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});
			} </script></div>
EOT;
		break;
		
		case 'xxxest004GridImportarOC':
			$T013_Id = (isset($r_T013_Id)) ? $r_T013_Id : false;
			
			$cClausula  = " select ";
			$cClausula .= " * ";
			$cClausula .= " from T013  ";
			$cClausula .= " left join D024 on D024_Id=T013_D024_Id ";
			$cClausula .= " where T013_Id='$T013_Id' ";
			$cSql_T013=mysql_query($cClausula);
			$mT013=mysql_fetch_array($cSql_T013);
			$T013_D024_Id=$mT013['T013_D024_Id'];
			$T013_Id=$mT013['T013_Id'];
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'O.C.', 'chavePrimaria'=>true),
				array('colunaValor' => 'T012_Quantidade_Pendente_3(T012_Id) as T012_Quantidade', 'colunaTitulo' => 'Quant Pend'),
				//array('colunaValor' => 'ifnull(T086_Quantidade,0) as T086_Quantidade', 'colunaTitulo' => 'Recebido', 'callback'=>'editarT086_Quantidade', 'callbackParameter'=>$T013_Id), //ifnull(T086_Quantidade,0) as 
				array('colunaValor' => 'T012_Quantidade_Recebida', 'colunaTitulo' => 'Recebido', 'colunaEditar'=>true), //ifnull(T086_Quantidade,0) as 
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód. Fornec.'),
				array('colunaValor' => 'T012_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
				array('colunaValor' => 'T012_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor', 'callback'=>'gCorrigeNumero'),
				array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
				array('colunaValor' => 'T012_Id', 'colunaTitulo' => 'T012_Id', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T012';

			$extra = <<<EOT
				left join T011 on T011_Id=T012_T011_Id
				left join D009 on T012_D009_Id=D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D049_D001_Id=D001_Id
				left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='[[T013_D024_Id]]'
				left join D005 on D009_D005_Id(T012_D009_Id)=D005_Id
				left join T014 on T014_T012_Id=T012_Id
				where T011_C004_Id='[[g-empresaAtual]]'
				and T012_Flag_Cancelada!='S'
				and T011_D024_Id='[[T013_D024_Id]]'
				and ifnull(T012_Quantidade_Pendente_3(T012_Id),0) >0 
				and T011_Data_Emissao>=date_sub(current_date(), interval 6 month)
				and T011_Status_Aprovacao = '2'
				AND (T014_T012_Id IS NULL OR T014_T012_Id='')
				order by T011_Id,T012_Descricao_Produto
EOT;
			// left join T086 on T086_T012_Id=T012_Id and T086_T013_Id='[[T013_Id]]'
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T011_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
			);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totalizadores (Versão Acima do GRID)
		*/
		
		if (!personalizacaoTotais()) {

			$extraNovo = str_replace('GROUP BY T012_Id', '', $extra);
			
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraNovo);
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

        echo "<div class='buttonsBottomTop'>";
            echo '<button type="button" id="est004DefinirQuantidadeRecebidaOC">Definir quantidade recebida</button>';
            echo '<button type="button" id="est004LimparQuantidadeRecebidaOC">Limpar quantidade</button>';
			$botoesEsq .= gGeraBotao("Preencher OC","EST004PopulaOC");
			gProcessaEtapas('Preencher OC','EST004', "popularOCNotaFiscal($r_acaoId,{$mT013['T013_D024_Id']})|false", "",'EST004PopulaOC');
        echo "</div>";

			
			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

			$extra_ = base64_encode($extra);
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

			$novoIdTelaForm = uniqid();

			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est004ConcluirInclusaoItens">Concluir inclusão dos itens</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est004ConcluirInclusaoItens').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004ConcluirInclusaoItens/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divIdParent']}', true);
											fecharJanela("{$g['divId']}");
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Concluir inclusão dos itens', 'Você tem certeza? ', \$buttons);
				});

				$('#est004DefinirQuantidadeRecebidaOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004DefinirQuantidadeRecebidaOC/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Inserir quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});

				$('#est004LimparQuantidadeRecebidaOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004LimparQuantidadeRecebidaOC/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Limpar quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});
			} </script></div>
EOT;
		break;
        
	case 'est004GridNotasFiscaisRecebidasProdutos':

		$filtroObrigatorio = true;

		$grid = array(
            array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T014_Codigo_Produto', 'colunaTitulo' => 'Cód Forn'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso Cod'),
            array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'callback' => 'coloreLinhaProdutos', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição fornecedor'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_D006_Id', 'colunaTitulo' => 'CFOP', 'colunaEditarGeraSelect' => array('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', '', true), 'callback' => 'T014_D006_Id_CFOP'),
            array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Qte', 'colunaCasasDecimais' => '3', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Custo_Unitario', 'colunaTitulo' => 'Preço', 'extra' => 'style="width:300px;"', 'colunaCasasDecimais' => '6', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Total_Desconto', 'colunaTitulo' => 'ICMS ST', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_ICMS_Substituicao', 'colunaTitulo' => 'Total IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Aliquota_ICMS', 'colunaTitulo' => 'Aliq. ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Percentual_Reducao_ICMS', 'colunaTitulo' => 'Red. ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Aliquota_IPI', 'colunaTitulo' => 'Aliq. IPI', 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => 'OC', 'colunaExibir' => false),
            array('colunaValor' => 'T014_D009_Id', 'colunaTitulo' => 'IdProd', 'colunaExibir' => false),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'IdEmpr', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T014";
		
		$extra = <<<EOT
			left join T013 on T013_Id=T014_T013_Id
            left join D024 on D024_Id=T013_D024_Id
			left join T012 on T012_Id=T014_T012_Id
			left join D009 on D009_Id=T014_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D037 on D037_Id=D001_D037_Id
			left join D082 on D082_Id=D049_D082_Id
			left join T001 on T001_T014_Id=T014_Id
			left join D006 on D006_Id=T014_D006_Id
			left join T225 on T225_Id=T014_T225_Id
            left join T224 on T224_Id=T225_T224_Id
			left join C007 as Comprador on Comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
			WHERE T013_C004_Id='{$g['empresaAtual']}'
			order by T014_Id
EOT;
        /**
        * Ação ao clicar em uma linha do grid
        */
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T013_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal: ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + id + '&tabela=T013', 'auto', false, false);
			});
		} </script></div>
EOT;
		/**
		* Filtro do modo correto, porém está dando um looping, verificar com o tiago
		* Problema resolvido, o primeiro parametro enviado para o gridFiltroExtra deve ser o mesmo dado ao array do filtro
		*/
		$gridFiltro = array(
            'T013_Numero_Controle' => array('titulo' => 'Controle', 'tipo' => 'texto'),
            'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'texto'),
            'T013_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T013_Data_Entrada' => array('titulo' => 'Entrada', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'T014_Codigo_Produto, D001_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'texto'),
            'T014_Descricao_Produto, D001_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'T013_Flag_Cancelada' => array('titulo' => 'NF Cancelada', 'tipo' => 'select', 'select' => array(
                	array('title' => 'TODAS', 'value' => ''),
                	array('title' => 'SIM', 'value' => 'S'),
                	array('title' => 'NÃO', 'value' => 'N'),
              	),
            ),
            'T014_Valor_ICMS_Substituicao' => array('titulo' => 'ICMS ST', 'tipo' => 'numero'),
			'T014_Valor_Frete' => array('titulo' => 'Frete', 'tipo' => 'numero'),
			'T014_Valor_IPI' => array('titulo' => 'IPI', 'tipo' => 'numero'),
			'T014_Valor_ICMS' => array('titulo' => 'ICMS', 'tipo' => 'numero'),
			'T014_Aliquota_ICMS' => array('titulo' => 'Aliq. ICMS', 'tipo' => 'numero'),
			'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
        );

		/**
         * Botões Almir
         */
        $botoes = array(
			);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}


		/*if (!personalizacaoTotais()) {
			$valorTotal  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T014_Valor_Total_Custo,T014_Id))", $from, $extra)));
			$itens = $g['sqlAuto']->pegarSqlCampo("count(distinct T014_Id)", $from, $extra);
			$quantidade  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T014_Quantidade,T014_Id))", $from, $extra)));
			$totalICMSST = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_ICMS_Substituicao)", $from, $extra)));
			$totalFrete  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_Frete)", $from, $extra)));
			$totalIPI    = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_IPI)", $from, $extra)));
			$totalICMS   = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_ICMS)", $from, $extra)));
			
			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$itens}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Quantidade<br><input type="text" value="{$quantidade}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS ST<br><input type="text" value="{$totalICMSST}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total Frete<br><input type="text" value="{$totalFrete}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total IPI<br><input type="text" value="{$totalIPI}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS<br><input type="text" value="{$totalICMS}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
		}*/

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Quantidade', 'valor' => 'T014_Quantidade', 'titulo' => 'Quantidade'),
			array('nome' => 'Valor_Total', 'valor' => 'T014_Valor_Total_Custo', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_ST', 'valor' => 'T014_Valor_ICMS_Substituicao', 'titulo' => 'Total ICMS ST'),
			array('nome' => 'Valor_Frete', 'valor' => 'T014_Valor_Frete', 'titulo' => 'Total Frete'),
			array('nome' => 'Valor_IPI', 'valor' => 'T014_Valor_IPI', 'titulo' => 'Total IPI'),
			array('nome' => 'Valor_ICMS_', 'valor' => 'T014_Valor_ICMS', 'titulo' => 'Total ICMS'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			// $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est004/content/est004ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
			// });
			
			// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_tabela_T014(T014_Id, campoEditado, valorAntigo, novoValor) {
					T014_Id = T014_Id.replace(/T014_Id-/, '');
					if (campoEditado == 'T014_Quantidade') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								//divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T014_Valor_Custo_Unitario') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								//divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T014_Aliquota_IPI') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								//divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
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
	
	case 'est004GridPagamentoNota':
			$acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T154_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T154_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T154_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T154_Prazos_Dias"),
				array('colunaValor' => 'T154_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData', 'colunaEditar'=>true,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T154_Prazos_Dias"),
				array('colunaValor' => 'est004_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "est004_Dia_Semana"),
				array('colunaValor' => 'T154_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T154_Prazos_Dias"),
				array('colunaValor' => 'T154_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', ' order by D031_Portador', true), 'callback' => 'T154_D031_Id_Portador'),				
				array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T154';

			$extra = <<<EOT
				left join D031 on D031_Id=T154_D031_Id
				WHERE T154_T013_Id = {$acao_Id}
EOT;

			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T154_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});				
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T154_Prazos_Dias(T154_Id, campoEditado, valorAntigo, novoValor) {
					T154_Id = T154_Id.replace(/T154_Id-/, '');
					if (campoEditado == 'T154_Prazos_Dias') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarParcelas/?ajax=true&T154_Id='      		+ encodeURIComponent(T154_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T154_Data_Vencimento') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarParcelas/?ajax=true&T154_Id='      		+ encodeURIComponent(T154_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if (campoEditado == 'T154_Valor_Parcela') {
						$.getJSON('/est/est004/grid_func-ajax/atualizarParcelas/?ajax=true&T154_Id='      		+ encodeURIComponent(T154_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
				}
								
			} </script></div>
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

			$novoIdTelaForm = uniqid();
		break;
		
    case 'est004GridItensOrdemCompra':  
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
         * Definição do Grid (Tabela)
         */
		$grid = array(
			array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'O.C.', 'chavePrimaria' => false),
            array('colunaValor' => 'T086_Id', 'colunaTitulo' => 'T086_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T086_T225_Id', 'colunaTitulo' => 'T086_T225_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T086_T013_Id', 'colunaTitulo' => 'T086_T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false),
            //array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Req.'),
            //array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cot.'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Cód Produto'),
            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-verde);'),
            array('colunaValor' => 'T225_Quantidade_Pendente_3(T225_Id) as T225_Quantidade_Pendente', 'colunaTitulo' => 'Qtd. Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-azul);'),
            //array('colunaValor' => 'ifnull(T086_Quantidade,0) as T086_Quantidade', 'colunaTitulo' => 'Recebido', 'callback' => 'editarT086_Quantidade', 'callbackParameter' => ''),
			array('colunaValor' => 'ifnull(T086_Quantidade,0) as T086_Quantidade', 'colunaTitulo' => 'Recebido',  'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T086_Quantidade', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Total_Custo', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
        );

        /**
         *
         * Definição da SQL
         */
        $from = 'T225';
        
        //Verificar se é assim que verifica se os itens estão pendendtes e incluir na linha em branco da variável $extra: and (T086_T013_Id is null or T086_T013_Id=0 or T086_T013_Id='')
        $extra = <<<EOT
            left join D009 on D009_Id=T225_D009_Id 
            left join D049 on D049_Id=D009_D049_Id 
            left join D082 on D082_Id=D049_D082_Id 
            left join D001 on D049_D001_Id=D001_Id 
            left join T224 on T224_Id=T225_T224_Id  
            left join D024 on D024_Id=T224_D024_Id
            left join T086 on T086_T225_Id=T225_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id
			left join C004 on C004_Id=T224_C004_Id
            where T224_C004_Id='{$g['empresaAtual']}' 
			  and T225_Quantidade_Pendente_3(T225_Id) >0 
              and T224_Flag_Cancelada!='S'
EOT;

//            order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))
    
        /**
        * Filtro
        */
        $gridFiltro = array(
            'T224_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T225_Codigo_Produto' => array('titulo' => 'Cód Produto', 'tipo' => 'numero'),
            'T225_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        
        $sql_ = urlencode(base64_encode($extra));  
		
		/* 		echo <<<EOT
		<div class="buttonsBottomTop">
		   <button type="button" id="est004GerarNF">Gerar NF Recebida</button>
		</div>
EOT; */
		// Botoes
		$botoes = array(
			array('titulo' => 'Gerar NF Recebida', 'id' => 'est004GerarNF'),
		);
		echo Botoes::processar($botoes);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Totais
		$totais = array(
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

			function colunaEditarCallbackJS_T086_Quantidade(T225_Id, campoEditado, valorAntigo, novoValor) {
				T225_Id = T225_Id.replace(/T225_Id-/, '');
				console.log("qual o ID : " + T225_Id);
				$.getJSON('/est/est004/grid_func-ajax/est004AlterarQuantidadeRecebidaOC/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
																					'&campoEditado='		+ encodeURIComponent(campoEditado) +
																					'&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					'&novoValor='			+ encodeURIComponent(novoValor) +  
																					'&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}', true, false, false, T225_Id, 'T225_Id'); // grid
					} else {
						divRefresh('{$g['divId']}', true, false, false, T225_Id, 'T225_Id'); // grid
						dialogConfirm('Erros', request.data);
					}
				});
			}		

            $('#est004GerarNF').unbind('click').bind('click', function(data) {
                $('#{$g['divId']}').showLoading();
                $.getJSON('/est/est004/grid_func-ajax/est004VerificaItens/?ajax=true&extra={$sql_}&callback=?', function(request) {
                    $('#{$g['divId']}').hideLoading();
                    if (request.code) {
                        var \$buttons = {
                            "Sim": function() {
                                $('#{$g['divId']}').showLoading();
                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar NF', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&GerarNFOC=true&extra={$sql_}', [800,500]);
                                $('#{$g['divId']}').hideLoading();
                                $(this).dialog("destroy").remove();
                            },
                            "Não": function() { $(this).dialog("destroy").remove(); } 
                        };
                        dialogConfirm('Confirmação', request.data, \$buttons);
                    }else{
                        var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
                        dialogConfirm("Erro!", request.data, buttons)
                    }
                });
            });
        } </script></div>
EOT;
        }

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Ordem de Compra: - ID: ' + array['todosCampos']['T224_Id'], '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T224_Id']) + '&tabela={$from}', [990,605]);
            });
		} </script></div>
EOT;

        /*
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         *
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T225_Id-/, '');
            });
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
        } </script></div>
EOT;*/

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

	  case 'xxxest004GridItensOrdemCompra':  
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
         * Definição do Grid (Tabela)
         */
		$grid = array(
            array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'O.C.', 'chavePrimaria' => true),
            array('colunaValor' => 'T012_Id', 'colunaTitulo' => 'T012_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T086_Id', 'colunaTitulo' => 'T086_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T086_T012_Id', 'colunaTitulo' => 'T086_T012_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T086_T013_Id', 'colunaTitulo' => 'T086_T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Req.'),
            array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cot.'),
            array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Cód Produto'),
            array('colunaValor' => 'T012_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T012_Quantidade_Falta_Lancar(T012_Id,T086_Id) as T012_Quantidade_Pendente', 'colunaTitulo' => 'Qtd. Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
            array('colunaValor' => 'ifnull(T086_Quantidade,0) as T086_Quantidade', 'colunaTitulo' => 'Recebido', 'callback' => 'editarT086_Quantidade', 'callbackParameter' => ''),
            array('colunaValor' => 'T012_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T012_Valor_Total_Custo', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T012_Quantidade', 'colunaTitulo' => 'Qtd. OC', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
        );

        /**
         *
         * Definição da SQL
         */
        $from = 'T012';
        
        //Verificar se é assim que verifica se os itens estão pendendtes e incluir na linha em branco da variável $extra: and (T086_T013_Id is null or T086_T013_Id=0 or T086_T013_Id='')
        $extra = <<<EOT
            left join D009 on D009_Id=T012_D009_Id 
            left join D049 on D049_Id=D009_D049_Id 
            left join D082 on D082_Id=D049_D082_Id 
            left join D001 on D049_D001_Id=D001_Id 
            left join T011 on T011_Id=T012_T011_Id  
            left join D024 on D024_Id=T011_D024_Id
            left join T086 on T086_T012_Id=T012_Id
            left join T059 on T059_Id=T012_T059_Id
            left join T189 on T189_Id=T059_T189_Id or T189_Id=T012_T189_Id
			left join T010 on T010_Id=T059_T010_Id
			left join T014 on T014_T012_Id=T012_Id
			left join T013 on T013_Id=T014_T013_Id
			left join C007 on C007_Id=T189_C007_Id
			left join C004 on C004_Id=T011_C004_Id
            where ifnull(T012_Quantidade_Falta_Lancar(T012_Id,T086_Id),0) >0 
            and T011_Flag_Cancelada!='S'
            AND (T012_C004_Id='{$g['empresaAtual']}')
            AND T011_Status_Aprovacao=2
EOT;

//            order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))
    
        /**
        * Filtro
        */
        $gridFiltro = array(
            'T011_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
            'T011_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T012_Codigo_Produto' => array('titulo' => 'Cód Produto', 'tipo' => 'numero'),
            'T012_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        
        $sql_ = urlencode(base64_encode($extra));        
        echo <<<EOT
         <div class="buttonsBottomTop">
            <button type="button" id="est004GerarNF">Gerar NF Recebida</button>
         </div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        
    
        /**
        * Botões (Versão Acima do GRID)
        */
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
         <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
         </div>
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#est004GerarNF').unbind('click').bind('click', function(data) {
                $('#{$g['divId']}').showLoading();
                $.getJSON('/est/est004/grid_func-ajax/est004VerificaItens/?ajax=true&extra={$sql_}&callback=?', function(request) {
                    $('#{$g['divId']}').hideLoading();
                    if (request.code) {
                        var \$buttons = {
                            "Sim": function() {
                                $('#{$g['divId']}').showLoading();
                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar NF', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&GerarNFOC=true&extra={$sql_}', [650,450]);
                                $('#{$g['divId']}').hideLoading();
                                $(this).dialog("destroy").remove();
                            },
                            "Não": function() { $(this).dialog("destroy").remove(); } 
                        };
                        dialogConfirm('Confirmação', request.data, \$buttons);
                    }else{
                        var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
                        dialogConfirm("Erro!", request.data, buttons)
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
				var id = $(this).closest('tr').attr('id').replace(/T011_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Ordem de Compra: ' + id, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,600]);
			});
		} </script></div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         *
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T012_Id-/, '');
            });
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
        } </script></div>
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

      case 'est004GridImportarItensNota':
			global $g;
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T008_Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
				array('colunaValor' => 'T086_Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
				array('colunaValor' => 'LPAD(T007_Numero_Nota_Fiscal,7,0)', 'colunaTitulo' => 'NF'),
				array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão NF', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-verde);'),
				array('colunaValor' => 'T008_Quantidade_Falta_Lancar(T008_Id) as saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-azul);'),
				array('colunaValor' => 'T086_Quantidade', 'colunaTitulo' => 'Recebido', 'colunaEditar' => true, 'colunaEditarJSCallback' => "editarQuantidadeRecebido", 'colunaEditarInsert' => true, 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);'),
				array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D049_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód. Fornec.'),
				array('colunaValor' => 'T008_Codigo_Substituto', 'colunaTitulo' => 'Substituto'),
				array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'T008_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
				array('colunaValor' => 'T008_Valor_Total_Preco', 'colunaTitulo' => 'Valor Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T008';

			$extra = <<<EOT
				LEFT JOIN T007 ON T007_Id=T008_T007_Id 
				LEFT JOIN D009 ON T008_D009_Id=D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN T086 ON T086_T008_Id=T008_Id AND T086_T013_Id = '{$r_T013_Id}'
				WHERE T008_C004_Id='{$g['empresaAtual']}' 
				AND T007_D024_Id='{$r_D024_Id}' 
				AND T008_Quantidade_Falta_Lancar(T008_Id) > 0
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
            'T008_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'T008_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'numero'),
        );
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

			
		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"> <script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function editarQuantidadeRecebido(T008_Id, campoEditado, valorAntigo, novoValor) {
				T008_Id = T008_Id.replace(/T008_Id-/, '');
				$.getJSON('/est/est004/grid_func-ajax/est004AlterarQuantidadeRecebidaNF/?ajax=true&T008_Id='    + encodeURIComponent(T008_Id) + 
																					'&T013_Id='			+ encodeURIComponent('{$r_T013_Id}') +
																					'&campoEditado='		+ encodeURIComponent(campoEditado) +
																					'&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					'&novoValor='			+ encodeURIComponent(novoValor) +  
																					'&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}');
					} else {
						divRefresh('{$g['divId']}');
						dialogConfirm('Erros', request.data);
					}
				});
			}		
		} </script> </div>
EOT;
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$extra_ = base64_encode($extra);
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
		$novoIdTelaForm = uniqid();
	break;
        
	case 'est004GridTitulosNota':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
        array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
        array('colunaValor' => 'T015_Flag_Reparcelar', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
		array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T015_Id', $g['divId'])),
        array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
        array('colunaValor' => 'T015_Flag_Boleto', 'colunaTitulo' => 'Ok', 'callback' => 'validaCampoFlagBoleto', 'colunaExibir' => false),
		array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'validaCampoExtratoCliente', 'colunaExibir'=>false),
		array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
		array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
		array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli'),
		array('colunaValor' => "concat(T015_Numero_Parcela,'/',T015_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
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
		array('colunaValor' => "T015_Observacao", 'colunaTitulo' => 'Observação'),
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
				(T015_T013_Id={$r_acaoId})
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
			'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'numero'),
			'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
			'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda'),
			'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
			'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
		);
		echo '<br><br>';
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);	
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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

	case 'est004GridImportarOS':
			$T013_Id = (isset($r_T013_Id)) ? $r_T013_Id : false;
			
			$cClausula  = " select ";
			$cClausula .= " * ";
			$cClausula .= " from T013  ";
			$cClausula .= " left join D024 on D024_Id=T013_D024_Id ";
			$cClausula .= " where T013_Id='$T013_Id' ";
			$cSql_T013=mysql_query($cClausula);
			$mT013=mysql_fetch_array($cSql_T013);
			$T013_D024_Id=$mT013['T013_D024_Id'];
			$T013_Id=$mT013['T013_Id'];
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T128_Id', 'colunaTitulo' => 'O.S.', 'chavePrimaria'=>true),
				array('colunaValor' => 'T129_Quantidade_Pendente_3(T129_Id) as T129_Quantidade', 'colunaTitulo' => 'Quant Pend'),
				array('colunaValor' => 'T129_Quantidade_Recebida', 'colunaTitulo' => 'Recebido', 'colunaEditar'=>true), //ifnull(T086_Quantidade,0) as 
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód. Fornec.'),
				array('colunaValor' => 'T129_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
				array('colunaValor' => 'T129_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor', 'callback'=>'gCorrigeNumero'),
				array('colunaValor' => 'T128_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
				array('colunaValor' => 'T129_Id', 'colunaTitulo' => 'T129_Id', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T129';

			$extra = <<<EOT
				left join T128 on T128_Id=T129_T128_Id
				left join D009 on T129_D009_Id=D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D049_D001_Id=D001_Id
				left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='[[T013_D024_Id]]'
				left join D005 on D009_D005_Id(T129_D009_Id)=D005_Id
				left join T014 on T014_T129_Id=T129_Id
				where T128_C004_Id='[[g-empresaAtual]]'
				and T129_Flag_Cancelada!='S'
				and ifnull(T129_Quantidade_Pendente_3(T129_Id),0) >0 
				and T128_D024_Id='[[T013_D024_Id]]'
				and T128_Data_Emissao>=date_sub(current_date(), interval 6 month)
				and T128_Flag_Estoque_Processado = 'S'
				
				order by T128_Id,T129_Descricao_Produto
EOT;
			// left join T086 on T086_T012_Id=T012_Id and T086_T013_Id='[[T013_Id]]'
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T128_Id' => array('titulo' => 'N° OS', 'tipo' => 'numero'),
			);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totalizadores (Versão Acima do GRID)
		*/
		
		if (!personalizacaoTotais()) {

			$extraNovo = str_replace('GROUP BY T012_Id', '', $extra);
			
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraNovo);
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

		echo <<<EOT
        <div class="buttonsBottomTop">
            <button type="button" id="est004DefinirQuantidadeRecebidaOS">Definir quantidade recebida</button>
            <button type="button" id="est004LimparQuantidadeRecebidaOS">Limpar quantidade</button>
        </div>
EOT;

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden">
				<script type="text/javascript"> 
					if (typeof jQuery != 'undefined') {
					}
				</script>
			</div>
EOT;
			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

			$extra_ = base64_encode($extra);
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

			$novoIdTelaForm = uniqid();

			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est004ConcluirInclusaoItensOS">Concluir inclusão dos itens</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est004ConcluirInclusaoItensOS').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004ConcluirInclusaoItensOS/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divIdParent']}', true);
											fecharJanela("{$g['divId']}");
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Concluir inclusão dos itens', 'Você tem certeza? ', \$buttons);
				});

				$('#est004DefinirQuantidadeRecebidaOS').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004DefinirQuantidadeRecebidaOS/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Inserir quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});

				$('#est004LimparQuantidadeRecebidaOS').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004LimparQuantidadeRecebidaOS/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
					    			dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Limpar quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});
			} </script></div>
EOT;
		break;

	case 'est004GridImportarOCNovo':
		$T013_Id = (isset($r_T013_Id)) ? $r_T013_Id : false;
		
		$cClausula  = " select ";
		$cClausula .= " * ";
		$cClausula .= " from T013  ";
		$cClausula .= " left join D024 on D024_Id=T013_D024_Id ";
		$cClausula .= " where T013_Id='$T013_Id' ";
		$cSql_T013=mysql_query($cClausula);
		$mT013=mysql_fetch_array($cSql_T013);
		$T013_D024_Id=$mT013['T013_D024_Id'];
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
	        array('colunaValor' => 'T225_T014_Id', 'colunaTitulo' => 'T225_T014_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'T014_T225_Id', 'colunaTitulo' => 'T014_T225_Id', 'colunaExibir' => false),
	        //array('colunaValor' => 'horizontal', 'colunaTipo' => 'livre', 'callback' => 'horizontal'),
	        array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T014_Numero_Pedido_Compra', 'colunaTitulo' => "OC XML", 'colunaAlinhar' => 'center', 'colunaExibir' => $exibirOC, 'colunaEditar' => 'S', 'colunaEditarTodos' => 'S'),
	        array('colunaValor' => 'CONCAT(T224_Id," / ",T225_Item)', 'colunaTitulo' => 'OC / Item', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '1000,600')),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cadastro Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,600'), 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
	        array('colunaValor' => 'T014_Codigo_Produto', 'colunaTitulo' => 'Cód. Fornec.', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição produto nota', 'colunaExibir' => true),
	        array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
	        array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),

			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM OC', 'style' => 'background:var(--celula-azul)', 'callback' => 'T014_Classificacao_Fiscal', 'colunaAlinhar' => 'center'),
	        array('colunaValor' => 'T014_Classificacao_Fiscal', 'colunaTitulo' => 'NCM NF', 'style' => 'background:var(--celula-azul)', 'colunaAlinhar' => 'center'),

	        array('colunaValor' => 'D6OC.D006_Codigo_CFOP as D6OC_Codigo', 'colunaTitulo' => 'CFOP OC', 'callback' => 'D006_Codigo_CFOP', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'D6NF.D006_Codigo_CFOP as D6NF_Codigo', 'colunaTitulo' => 'CFOP NF', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-verde)'),

	        array('colunaValor' => 'IFNULL(T225_Quantidade,0)', 'colunaTitulo' => 'Qte OC', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Qte NF', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-azul)'),

			array('colunaValor' => 'T225_Quantidade_Recebida', 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarJSCallback' => 'T225_Gravar_Totalizacao', 'colunaExibir' => false),
	        
			array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Unitário OC', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'T014_Valor_Desconto_Unitario*-1', 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T014_Valor_Custo_Unitario-T014_Valor_Desconto_Unitario', 'colunaTitulo' => 'Unitário NF', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),

			array('colunaValor' => 'T014_Valor_Total_Preco_Recebida', 'colunaTitulo' => 'Total OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_Total_Preco_Recebida', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T014_Valor_Total_Custo-T014_Valor_Total_Desconto', 'colunaTitulo' => 'Total NF', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
	        
			//array('colunaValor' => '(SELECT SUM(T014_Valor_ICMS_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) as T014_Valor_ICMS_Recebida', 'colunaTitulo' => 'ICMS OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Recebida', 'style' => 'background:#DCD6F9'),

			array('colunaValor' => 'T014_Valor_ICMS_Recebida', 'colunaTitulo' => 'ICMS OC R$', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Recebida', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'T014_Aliquota_ICMS', 'colunaTitulo' => 'Aliq %', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS NF R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'T225_Aliquota_ICMS', 'colunaTitulo' => 'Aliq %', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),

			//array('colunaValor' => '(SELECT SUM(T014_Valor_IPI_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) as T014_Valor_IPI_Recebida', 'colunaTitulo' => 'IPI OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_IPI_Recebida', 'style' => 'background:#CDF7F5'),

			array('colunaValor' => 'T014_Valor_IPI_Recebida', 'colunaTitulo' => 'IPI OC R$', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_IPI_Recebida', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T225_Aliquota_IPI', 'colunaTitulo' => 'Aliq %', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T014_Valor_IPI', 'colunaTitulo' => 'IPI NF R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T014_Aliquota_IPI', 'colunaTitulo' => 'Aliq %', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),

			//array('colunaValor' => '(SELECT SUM(T014_Valor_ICMS_Substituicao_Retencao_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) as T014_Valor_ICMS_Substituicao_Retencao_Recebida', 'colunaTitulo' => 'ST OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Substituicao_Recebida', 'style' => 'background:#CDF7D8'),

			array('colunaValor' => 'T014_Valor_ICMS_Substituicao_Retencao_Recebida', 'colunaTitulo' => 'ST OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Substituicao_Recebida', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'T014_Valor_ICMS_Substituicao', 'colunaTitulo' => 'ST NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),

			array('colunaValor' => 'T014_Valor_Total_Frete_Recebida', 'colunaTitulo' => 'Frete OC', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T014_Valor_Frete', 'colunaTitulo' => 'Frete NF', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),

			array('colunaValor' => 'T014_Valor_Custo_Unitario_Recebida', 'colunaTitulo' => 'Custo OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_Custo_Unitario_Recebida', 'style' => 'background:var(--celula-verde)'),
	        array('colunaValor' => 'T014_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo NF', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '6', 'style' => 'background:var(--celula-verde)'),

	        array('colunaValor' => 'T225_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T225_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T225_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T225_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T225_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
	        array('colunaValor' => 'T014_Flag_Divergencia', 'colunaTitulo' => 'Divergência', 'colunaAlinhar' => 'center'),
	        array('colunaValor' => 'T014_Observacao_Divergencia', 'colunaTitulo' => 'Observação divergência', 'colunaExibir' => false),	        
	        array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'center'),
	        array('colunaValor' => 'T014_Valor_Total_Desconto', 'colunaTitulo' => 'Total desconto', 'colunaExibir' => false),
	        array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Total Produto', 'colunaExibir' => false),
            array('colunaValor' => 'Duplicar', 'colunaTitulo' => 'Duplicar', 'colunaTipo' => 'livre', 'callback' => 'duplicarItemNFEntrada'),
            array('colunaValor' => 'T014_Id_Duplicado', 'colunaTitulo' => 'Id Dupl', 'colunaAlinhar' => 'center'),
	        array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição produto'),
            array('colunaValor' => 'comparaDescricao', 'colunaTitulo' => '%', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'colunaTipo' => 'livre', 'callback' => 'comparaDescricao'),
		);
		if($g['nivelAlcada'] == '4' || isset($g['c029Ids'][265])){
			$grid[] = array('colunaValor' => 'T225_Flag_Autorizado', 'colunaTitulo' => 'Ok', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array('T225_Flag_Autorizado', 'N|S'));
		} else {
			$grid[] = array('colunaValor' => 'T225_Flag_Autorizado', 'colunaTitulo' => 'Ok', 'colunaAlinhar' => 'center');
		}

		/**
		* Definição da SQL
		*/
		$from = 'T014';

		$extra = <<<EOT
			left join T225 on T225_Id=T014_T225_Id
			left join T224 on T224_Id=T225_T224_Id
			left join D009 on D009_Id=T014_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='{$T013_D024_Id}'
			left join T013 on T013_Id=T014_T013_Id
			left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
			left join D005 on D005_Id=T225_D005_Id
			left join D006 D6OC on T225_D006_Id=D6OC.D006_Id
			left join D006 D6NF on T014_D006_Id_Original=D6NF.D006_Id
			where T013_Id='{$T013_Id}'
			group by T014_Id
			order by T224_Id,T225_Descricao_Produto
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T224_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);


        echo "<div class='buttonsBottomTop'>";
			echo gGeraBotao("Vincular OC","EST004PopulaOC",NULL,$mT013['T013_Flag_Travar_Nota'],'N');
			echo gGeraBotao("Concluir inclusão itens","est004ConcluirInclusaoItensNovo",NULL,$mT013['T013_Flag_Travar_Nota'],'N');
		echo "</div>";

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['T225_Quantidade_Recebida']>0){

		    }
		}

		/**
		* Totalizadores (Versão Acima do GRID)
		*/
		
		if (!personalizacaoTotais()) {
			$extraNovo = str_replace('GROUP BY T225_Id', '', $extra);
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraNovo);
			/*echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;*/
		}

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
					var T014_Id = id[1].replace(/T014_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItemNovo/', '&acaoId=' + encodeURIComponent(T014_Id) + '&tabela=T014', [950,500]);
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$extra_ = base64_encode($extra);
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

		$novoIdTelaForm = uniqid();

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');

		if (empty($r_linhaGridId)) {
			
			echo gProcessaEtapas('Vincular OC','EST004', "popularOCNotaFiscal($T013_Id,$T013_D024_Id)|false", "",'EST004PopulaOC');

			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est004ConcluirInclusaoItensNovo').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/est004ConcluirInclusaoItensNovo/?ajax=true&T013_Id=' + encodeURIComponent('{$T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
											divRefresh('{$g['divIdParent']}', true);
											fecharJanela("{$g['divId']}");
										} 
									}
									dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Concluir inclusão dos itens', 'Você tem certeza? ', \$buttons);
				});

				$('#est004DefinirQuantidadeRecebidaOCNovo').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_funcajax/est004DefinirQuantidadeRecebidaOCNovo/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
									dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Inserir quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});

				$('#est004LimparQuantidadeRecebidaOCNovo').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T013_Id = $('#{$g['divId']} #T013_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_funcajax/est004LimparQuantidadeRecebidaOCNovo/?ajax=true&T013_Id=' + encodeURIComponent('{$r_T013_Id}') + '&extraSql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										} 
									}
									dialogConfirm('Ok', 'Processo concluído com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Limpar quantidade', 'Atenção, este processo será aplicado para todos os ítens filtrados. Você tem certeza?', \$buttons);
				});
			} </script></div>
EOT;
		}
	
	break;

	// Cópia do grid 'Importar OC', mais listando somente os produtos
	case 'est004GridAssociarItemNovo':
			// T014
			$T014 = mysql_query("SELECT T013_D024_Id,
			                            T013_Id,
										T014_Descricao_Produto,
										T014_Valor_Custo_Unitario,
										T014_D009_Id,
										T014_Classificacao_Fiscal
								   FROM T014
							  LEFT JOIN T013 ON T013_Id = T014_T013_Id
							  LEFT JOIN D024 on D024_Id=T013_D024_Id
								  WHERE T014_Id = '{$r_acaoId}'");
			$mT014=mysql_fetch_array($T014);

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
	            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	            array('colunaValor' => 'CONCAT(T224_Id," / ", T225_Item)', 'colunaTitulo' => 'OC / Item', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Editar Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605')),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cadastro Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,600'), 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição produto'),
				array('colunaValor' => "'{$mT014['T014_Descricao_Produto']}' as T014_Descricao_Produto", 'colunaTitulo' => 'Descrição NF', 'colunaExibir' => false),
				array('colunaValor' => 'comparaDescricao', 'colunaTitulo' => '%', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'colunaTipo' => 'livre', 'callback' => 'comparaDescricao', 'callbackParameter' => array("{$mT014['T014_Valor_Custo_Unitario']}","{$mT014['T014_D009_Id']}")),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
	            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'callback' => 'comparaNCM', 'callbackParameter' => array("{$mT014['T014_Valor_Custo_Unitario']}", "{$mT014['T014_Classificacao_Fiscal']}")),
	            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
	            array('colunaValor' => 'T225_Quantidade_Pendente_3(T225_Id)', 'colunaTitulo' => 'Qte Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço unit', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T225_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T225_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T225_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left'),
	            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'Empresa', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        );

			/**
			* Definição da SQL
			*/
			$from = 'T225';

			$extra = <<<EOT
				left join T224 on T224_Id=T225_T224_Id
				left join D009 on T225_D009_Id=D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D049_D001_Id=D001_Id
				left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='{$mT014['T013_D024_Id']}'
				left join T014 on T014_T225_Id=T225_Id
				left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
				left join D005 on D005_Id=T225_D005_Id
				where T224_C004_Id='{$g['empresaAtual']}'
				and D009_C004_Id='{$g['empresaAtual']}'
				and (T224_Flag_Status_Liberacao = '2')
				and T224_Flag_Status != '10'
				and T225_Flag_Cancelada!='S'
				and T224_D024_Id='{$mT014['T013_D024_Id']}'
				and (ifnull(T225_Quantidade_Pendente_3(T225_Id),0) >0 OR ifnull(T014_Flag_Divergencia,'N') = 'S')
				group by T225_Id
				order by T224_Id,T225_Descricao_Produto
EOT;

			/**
			* Função que define classes CSS para cada linha do grid
			*/
			function gridLinha($array) {
				global $mT014;
				if ($array['todosCampos']['D009_Id']==$mT014['T014_D009_Id'])  {
				    return 'linhaAzul linhaNegrito';
				}
				if ($array['todosCampos']['T225_Valor_Preco_Sem_Desconto_Unitario']==$mT014['T014_Valor_Custo_Unitario'])  {
				    return 'linhaVerde linhaNegrito';
				}
			}

			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T224_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
				'D001_Codigo_Produto,D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
				'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			);
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

			/**
			* Totalizadores (Versão Acima do GRID)
			*/
			if (!personalizacaoTotais()) {
				//
			}

			/**
	        * Ação ao clicar em uma linha do grid
	        */	    
        	echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
						if('{$mT013['T013_Flag_Travar_Nota']}' == 'S'){
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
		    				dialogConfirm('Erro', "Não foi possível realizar a operação, a nota fiscal já está finalizada!", buttons);
						} else {								
							var id = $(this).closest('tr').attr('id').split('|');
							var D009_Id = id[0].replace(/D009_Id-/, '');
							var T225_Id = id[1].replace(/T225_Id-/, '');
							
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/grid_func-ajax/associarItemNovo/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T225_Id=' + encodeURIComponent(T225_Id) + '&T014_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									// fechar janela e atualizar parent
									//divRefresh('{$g['divIdParent']}');
									divRefresh('{$r_divRefresh}');
									fecharJanela('{$g['divId']}');
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
		    						dialogConfirm('Erro', request.data, buttons);
								}
							});
						}
					});
					$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
				} </script></div>
EOT;

			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

			$extra_ = base64_encode($extra);
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

			$novoIdTelaForm = uniqid();
		break;

	case 'est004gridOrdemCompra':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Numero_Pedido_Venda', 'colunaTitulo' => 'Pedido Ecommerce', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
            array('colunaValor' => 'concat(substr(D024_Nome_Empresa,1,30),\'<br><font style="font-size:10px"><i>\',ifnull(T224_Observacao_Pedido_Perdido,\'\'))', 'colunaTitulo' => 'Razão Social', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\'),\' \',ifnull(D013_DDD_Celular,\'\'),\'-\',ifnull(D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;min-width:190px;width:190px;white-space:normal;'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T224_Prazos(T224_Id)', 'colunaTitulo' => 'Prazo pagamento', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id)) as valorPendente', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%', 'colunaExibir' => false),
            array('colunaValor' => '(sum(distinct concat(ifnull(T014_Quantidade,\'0.00000\'),T014_Id))/sum(distinct concat(T225_Quantidade,T225_Id)))*100', 'colunaTitulo' => '%Receb', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'concat(T224_Nome_Status,\'<font style="font-size:10px;font-weight:normal"> \',ifnull(group_concat(DISTINCT \'Mail:\',T139_Data_Hora),\'\'),\'<br><font style="font-size:11px;font-weight:normal">\',ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\'))', 'colunaTitulo' => 'Status', 'colunaExibir' => false, 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'group_concat(distinct T013_Id SEPARATOR \'|\')', 'colunaTitulo' => 'NF', 'colunaExibir' => false, 'callback' => 'danfeNFsDaOrdemDeCompra', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Nome_Status as Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat("<span style=\\"\\">", T224_Nome_Status, "</span> ", ifnull((SELECT date_format(T228_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T228 WHERE T228_T224_Id=T224_Id order by T228_Id desc LIMIT 1), ""), "<br />", if(T224_Flag_Status=9, T224_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T225_T075_Id>0,"MP","PE"))) FROM T225 WHERE T225_T224_Id=T224_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        									FROM T225 
        									LEFT JOIN D009 ON D009_Id = T225_D009_Id
        									LEFT JOIN D049 ON D049_Id = D009_D049_Id
        									LEFT JOIN D082 ON D082_Id = D049_D082_Id
        									WHERE T225_T224_Id=T224_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'downloadXMLPedido', 'colunaTipo' => 'livre', 'colunaExibir' => false),
            array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
            array('colunaValor' => 'T224_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(T013_Numero_Nota_Fiscal)', 'colunaTitulo' => 'T013_Numero_Nota_Fiscal', 'colunaExibir' => false),
        );

		/**
		*
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
			left join D022 on D022_Id = T224_D022_Id
			left join D013 on D013_Id=T224_D013_Id
			left join T139 on T139_T224_Id=T224_Id
			left join T225 on T225_T224_Id=T224_Id AND T225_Flag_Cancelada != 'S'
			left join D006 on D006_Id = T225_D006_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			where (T224_C004_Id={$g['empresaAtual']} AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			and T013_Id = '{$r_acaoId}'
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
    			where (T224_C004_Id={$g['empresaAtual']} AND T224_Flag_Status!=8 AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T224_Flag_Status']=='5'){
				return 'linhaCinza';
			}
			else if($array['todosCampos']['T224_Flag_Status']<=0){
				return 'linhaAzul';
			}
		}
		
		/*
		* Botao Receber Pedidos do ecommerce
		*
		echo <<<EOT
		<div class="buttonsBottomTop">
			<!--<button type="button" id="est016RecebePedidos">Receber Pedidos</button>-->
			<button type="button" id="est016ProcessarOCVencida">Processar Vencidos</button>
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

			$('#est016RecebePedidos').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Receber Pedidos', '/est/est016/content/est016ContentReceberPedidos/', '', [400,200]);
			});

			$('#est016ProcessarOCVencida').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/grid_func-ajax/est016ProcessarOCVencida/?ajax=true&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Operação Ok', request.data, \$buttons);
								divRefresh('{$g['divId']}', true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Processar ordens de compra vencidas', 'Você tem certeza?', \$buttons);
			});
		} </script></div>
EOT;
		*/
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T224_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'T224_Flag_Status_Financeiro' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Pendentes', 'value' => '1'),
				array('title' => 'Concluidos', 'value' => '2'),
				array('title' => 'Cancelados', 'value' => '3'),
			), 'naoGerarExtra' => false),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
									FROM T225 
									LEFT JOIN D009 ON D009_Id = T225_D009_Id
									LEFT JOIN D049 ON D049_Id = D009_D049_Id
									LEFT JOIN D082 ON D082_Id = D049_D082_Id
									WHERE T225_T224_Id=T224_Id)' => array('titulo' => 'Marca', 'tipo'=>'texto'),
			
			/*'D082_Id' => array('titulo' => 'Marca', 'tipo' => 'select', 
				'select' => gGeraSelect("D082", "D082_Id", "D082_Marca", "GROUP BY D082_Marca"), 'naoGerarExtra' => false),*/
			'T224_Nome_Status' => array('titulo' => 'Status Pedido', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(IF(T225_T075_Id>0[[virgula]]"MP"[[virgula]]"PE"))) FROM T225 WHERE T225_T224_Id=T224_Id)' => array('titulo' => 'Estoque', 'tipo' => 'texto', 'exibir'=>false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/*
		 * Totalizadores
		 *
		if (!personalizacaoTotais()) { 
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
		/*
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela={$from}', [990,605]);  //950 615
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

	case 'est004GridObservacoes':

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T297_Id', $g['divId'])),
			array('colunaValor' => 'T297_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),						
			array('colunaValor' => 'D188_Codigo_Observacao', 'colunaTitulo' => 'Código'),			
			array('colunaValor' => 'D188_Descricao_Observacao', 'colunaTitulo' => 'Descrição'),		
			array('colunaValor' => 'T297_Descricao_Complementar_Observacao', 'colunaTitulo' => 'Descrição Complementar da Observação'),											
		);

		/**
		 * Definição da SQL
		 */
		$from = "T297";
		$extra = <<<EOT
		LEFT JOIN D188 ON D188_Id = T297_D188_Id
		WHERE T297_T013_Id = '{$r_acaoId}'
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est004FormObservacaoBtnIncluir'),
			array('titulo' => 'Atualizar Observações', 'id' => 'est004AtualizarObservacoes'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est004FormObservacaoBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Observação', '/est/est004/content/est004ContentCadastroObservacao/', '&tab=geral&T013_Id={$r_acaoId}', [700,450]);
			});
			
			$('#est004AtualizarObservacoes').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est004/grid_func-ajax/est004AtualizarObservacoes/?ajax=true&T013_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', 
							function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Atualizar Observações', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
						});						
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Atualizar Observações', 'Você tem certeza?', \$buttons);
			});
		} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D188_Codigo_Observacao' => array('titulo' => 'Código', 'tipo' => 'text'),			
		);
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
					var chavePrimaria = 'T297_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');					
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Observação - ID: ' + acaoId, '/est/est004/content/est004ContentCadastroObservacao/', '&acaoId=' + encodeURIComponent(acaoId) + '&T013_Id={$r_acaoId}', [1000,450]);
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
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);



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

	case 'est004GridObrigacoes':

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T298_Id', $g['divId'])),
			array('colunaValor' => 'T298_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),									
			array('colunaValor' => 'D188_Codigo_Observacao', 'colunaTitulo' => 'Código Obs', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),												
			array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto'),		
			array('colunaValor' => 'D189_Codigo_Ajuste', 'colunaTitulo' => 'Código Ajuste'),						
			array('colunaValor' => 'T298_Descricao_Complementar_Ajuste', 'colunaTitulo' => 'Descrição Complementar Ajuste'),			
			array('colunaValor' => 'T298_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),								
			array('colunaValor' => 'T298_Aliquota_ICMS', 'colunaTitulo' => '% ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),								
			array('colunaValor' => 'T298_Valor_ICMS', 'colunaTitulo' => 'Valor de ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),								
			array('colunaValor' => 'T298_Outros_Valores', 'colunaTitulo' => 'Valor Outros', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),								
		);

		/**
		 * Definição da SQL
		 */
		$from = "T298";
		$extra = <<<EOT
		LEFT JOIN T297 ON T297_Id = T298_T297_Id
		LEFT JOIN D189 ON D189_Id = T298_D189_Id
		LEFT JOIN D188 ON D188_Id = T297_D188_Id
		LEFT JOIN T014 ON T014_Id = T298_T014_Id
		LEFT JOIN D009 ON D009_Id = T014_D009_Id 
		LEFT JOIN D049 ON D049_Id = D009_D049_Id 
		LEFT JOIN D001 ON D001_Id = D049_D001_Id 
		WHERE T298_T297_Id = '{$r_acaoId}'
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est004FormObrigacoesBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est004FormObrigacoesBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de Obrigações Tributárias', '/est/est004/content/est004ContentCadastroObrigacaoTributaria/', '&tab=geral&T013_Id={$r_T013_Id}&T297_Id={$r_acaoId}', [700,450]);
			});
		} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D189_Codigo_Ajuste' => array('titulo' => 'Código Ajuste', 'tipo' => 'text'),				
		);
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
					var chavePrimaria = 'T298_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');					
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Obrigação Tributária - ID: ' + acaoId, '/est/est004/content/est004ContentCadastroObrigacaoTributaria/', '&acaoId=' + encodeURIComponent(acaoId) + '&T013_Id={$r_T013_Id}&T297_Id={$r_acaoId}', [700,450]);
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
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);



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

	case 'est004GridCiap':

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T316_Id', $g['divId'])),
			array('colunaValor' => 'T316_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=> true, 'style' => 'width: 1px;'),		
			array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir'=> false, 'style' => 'width: 1px;'),							
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cadastro Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,600'), 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo); width: 1px;'),				
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T316_Codigo_Produto_CIAP', 'colunaTitulo' => 'Código Escrituração', 'style' => 'background: var(--celula-verde);  width: 1px;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T316_Descricao_Utilizacao_Bem', 'colunaTitulo' => 'Descrição Utilização Bem', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-vermelho); width: 100px;'),
			array('colunaValor' => 'T316_Data_Inicio_Apropriacao_Credito', 'colunaTitulo' => 'Início Apropriação', 'callback' => 'gCorrigeData', 'style' => 'width: 1px;', 'colunaAlinhar' => 'center'),						
			array('colunaValor' => 'T316_Numero_Parcelas_Apropriacao', 'colunaTitulo' => 'Nº Parcelas', 'style' => ' width: 1px;', 'colunaAlinhar' => 'center'),	
			array('colunaValor' => 'validaParcelaDataAtualCiap', 'colunaTipo' => 'livre' ,'colunaTitulo' => 'Nº Parcela Aproprição<br>Sobre Data Atual','callback' => 'validaParcelaDataAtualCiap', 'style' => ' width: 1px;', 'colunaAlinhar' => 'center'),								
			array('colunaValor' => 'T316_Flag_Ativo_Baixado', 'colunaTitulo' => 'Baixado CIAP', 'style' => ' width: 1px;', 'colunaAlinhar' => 'center'),						
			array('colunaValor' => 'T316_Data_Fim_Apropriacao_Credito', 'colunaTitulo' => 'Fim Apropriação', 'colunaExibir' => true, 'callback' => 'gCorrigeData','style' => 'width: 1px;', 'colunaAlinhar' => 'center'),								
		);

		/**
		 * Definição da SQL
		 */
		$from = "T316";
		$extra = <<<EOT
		LEFT JOIN T014 ON T014_Id = T316_T014_Id
		LEFT JOIN T013 ON T013_id = T014_T013_Id
		LEFT JOIN D009 ON D009_Id = T014_D009_Id 
		LEFT JOIN D049 ON D049_Id = D009_D049_Id 
		LEFT JOIN D001 ON D001_Id = D049_D001_Id 
		WHERE T014_T013_Id = '{$r_acaoId}'
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est004FormObrigacoesBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est004FormObrigacoesBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro CIAP', '/est/est004/content/est004ContentCadastroCiap/', '&tab=geral&T013_Id={$r_T013_Id}', [655,250]);
			});
		} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF Entrada', 'tipo' => 'text'),			
			'T316_Data_Inicio_Apropriacao_Credito' => array('titulo' => 'Início Apropriação', 'tipo' => 'data'),			
			'T316_Flag_Ativo_Baixado' => array('titulo' => 'Baixado (S/N)', 'tipo' => 'text'),	
			'T316_Data_Fim_Apropriacao_Credito' => array('titulo' => 'Fim Apropriação', 'tipo' => 'data'),				
		);
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
					var chavePrimaria = 'T316_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');					
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Obrigação Tributária - ID: ' + acaoId, '/est/est004/content/est004ContentCadastroCiap/', '&T316_Id=' + encodeURIComponent(acaoId) + '&T013_Id={$r_T013_Id}', [655,250]);
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
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);



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
}
?>
