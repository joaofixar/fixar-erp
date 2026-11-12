<?php
namespace hardness;
/**
 * VEN - VEN001- Orçamentos
 */
// Classes
//require_once('bibliotecas/classes/VEN001.php');
//$VEN001 = new VEN001();
// Carrega funções callback
require_once('prog/ven/ven001/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven001/grid_func-js.php');
// Imagem para exclusão da linha
/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: ven001TabPrincipal
	 */
	case 'ven001GridPrincipalOrcamentos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus'),
            array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", ") as Pedido', 'colunaTitulo' => 'Pedidos', 'callback' => 'validaCampoPedido', 'style' => 'width:1%'),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'CONCAT_WS(" ",Cliente.D013_Nome_Contato, Cliente.D013_Telefone_1) as D013_Nome_Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo' => 'livre', 'callback' => 'validaCampoStatusGridPrincipal'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaTitulo' => 'T003_Flag_Status_Orcamento', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T003';
		$extra = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 as Orcamento on Orcamento.D013_Id=T003_D013_Id
			left join D013 as Cliente on Cliente.D013_D024_Id=T003_D024_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id AND T005_Flag_Status!='8'
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}')
		    group by T003_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id AND T005_Flag_Status!='8'
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}')
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalAberto = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id AND T005_Flag_Status!='8'
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		      and T003_Flag_Perdido!='S'
		      and (select T005_Id from T005 where T005_T003_Id=T003_Id limit 1) is null)
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalFechado = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		      and T006_Id > 0)
		      GROUP BY T003_Id
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalPerdido = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id AND T005_Flag_Status!='8'
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		      and T003_Flag_Perdido = 'S')
		      GROUP BY T003_Id
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T005_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T003_Flag_Perdido']=='S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T005_Flag_Status']<=0){
				if ($array['todosCampos']['T003_Flag_Status_Orcamento']=='V') {
					return 'linhaFundoAmareloClaro linhaAzul';
				} else {
					return '';
				}
			}
		}

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven001GridPrincipalOrcamentos">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#ven001GridPrincipalOrcamentos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '', [750,600]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
            'T003_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
            'T003_Flag_Status_Orcamento' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Finalizado', 'value' => 'F'),
                array('title' => 'Pendente', 'value' => 'P'),
                array('title' => 'Cancelado', 'value' => 'C'),
                array('title' => 'Vencido', 'value' => 'V'),
              ),
              'naoGerarExtra' => ''),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		$extraTotalFechado = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalFechado, false, false, true);
		$extraTotalPerdido = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalPerdido, false, false, true);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Totais(Versão Acima do GRID)
		*/
		/**
		 * Totalizadores
		 */
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotal);
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotal));
		$registrosAberto = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalAberto);
		$valorTotalAberto = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotalAberto));
		$registrosFechado = $g['sqlAuto']->pegarSqlCampo("count(distinct T005_Id)", $from, $extraTotalFechado, $extraTotalFechado);
		$valorTotalFechado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T006_Valor_Total_Preco + T006_Valor_IPI)", $from, str_replace('GROUP BY T003_Id', '', $extraTotalFechado)));
		$registrosPerdido = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalPerdido);
		$valorTotalPerdido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, str_replace('GROUP BY T003_Id', '', $extraTotalPerdido)));
		//T003_Flag_Perdido='S'
		//echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T003_Valor_Total)" . $from . $extra;
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Orçamentos<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosAberto}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalAberto}" readonly></span>
				<span class="item" style="text-align:right">Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosFechado}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalFechado}" readonly></span>
				<span class="item" style="text-align:right">Perdidos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosPerdido}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Perdidos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalPerdido}" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T003_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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
	
	/**
	 * Grid Principal
	 * Chamado em: ven001tab01
	 */
	case 'ven001GridProdutos':

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');

		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		$importacaoDados = array('T004_T003_Id' => $r_T003_Id);

		
		/**
		* Definição das Tabelas
		*/
		$from = 'T004';
		$fromParent = 'T003_Id';
		$fromParentValor = $r_T003_Id;

		$sql = "SELECT T003_Casas_Decimais_Preco,
					   T003A_Flag_Conversao_Unidade 
				  FROM T003 
			 LEFT JOIN T003A ON T003_Id = T003A_T003_Id
				 WHERE T003_Id = '{$r_T003_Id}'";
		$resT003 = mysql_query($sql);
		$rowT003 = mysql_fetch_assoc($resT003);

		// Valida colunas exibir ao usar preço bruto
		$colunaPrecoLiquido       = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoTabela        = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPercentualDesconto = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoTotal         = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoBruto         = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? true : false;
		// Fim preço Bruto

		// Valida colunas exibir para conversão de unidade
		$colunaPrecoComercial                = false;
		$colunaUnidadeComercial              = false;
		$colunaQuantidadeComercial           = false;
		$colunaQuantidadeConfirmadaComercial = false;
		$colunaQuantidadeConfirmada 	     = true;	
		$colunaQuantidade                    = true;
		// Só valida conversão se for diferente de preço bruto, primeira versão da conversão de unidade trata apenas para liquido (Felipe 26/06/2023)
		if($g['C031']['precopadraoVendaProduto'] != 'bruto' && $rowT003['T003A_Flag_Conversao_Unidade'] == 'S'){
			// Colunas T004A que são utilizadas na conversão
			$colunaPrecoComercial                = true ;
			$colunaUnidadeComercial              = true ;
			$colunaQuantidadeComercial           = true ;
			$colunaQuantidadeConfirmadaComercial = true ;
			//Colunas T004 que são usado no padrão para calculos
			$colunaQuantidadeConfirmada          = false;
			$colunaQuantidade                    = false;
			$colunaPrecoLiquido                  = false;
		}
		// Fim conversão unidade

		//Utilizado para fazer a busca da data de previsao da PO e DI
		$diasPrazoOC = (empty($g['C031']['diasMedioPrazo'])) ? 0 : $g['C031']['diasMedioPrazo'];

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T004_Flag_Excluir', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gCheckbox', 'callbackParameter' => array('T004_Flag_Excluir', '2|1'), 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => '1|2', 'style' => 'width:1%;'),
            array('colunaValor' => 'horizontal', 'colunaTipo' => 'livre', 'callback' => 'horizontal', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004A_Id', 'colunaTitulo' => 'T004A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T219_Id', 'colunaTitulo' => 'T004_T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Item'),
            array('colunaValor' => 'T004_Codigo_Local', 'colunaTitulo' => 'Ambiente', 'colunaAlinhar' => 'left', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
            array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'style' => 'font-size:12px'),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referência', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré Cadastro', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'font-size:12px'),
            array('colunaValor' => 'T004_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-size:12px'),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
			array('colunaValor' => 'unPadrao.D037_Id', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'unPadrao.D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:12px'),
			array('colunaValor' => 'unConversao.D037_Unidade', 'colunaTitulo' => 'Un Conversão', 'colunaAlinhar' => 'center', 'style' => 'font-size:12px', 'colunaExibir' => false),
			array('colunaValor' => 'T004A_D184_Id', 'colunaTitulo' => 'Un Conversão', 'colunaAlinhar' => 'center', 'colunaExibir' => $colunaUnidadeComercial, 'callback' => 'T004A_D184_Id_Unidade', 'callbackParameter' => array('divReload' => $r_divReload)),
			//array('colunaValor' => '(IF(D009_Quantidade_Estoque_Liquido < T004_Quantidade, CONCAT(\'<span style="color: var(--BGVermelho);">\',REPLACE( REPLACE( REPLACE( FORMAT( D009_Quantidade_Estoque_Liquido, 2), \'.\', \'|\'), \',\', \'.\'), \'|\', \',\'),\'</span>\'), D009_Quantidade_Estoque_Liquido))', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
 			array('colunaValor' => "IFNULL((SELECT T075_Data_Previsao 
												   FROM T055 
											  LEFT JOIN T075 ON T075_Id = T055_T075_Id 
											  LEFT JOIN D024 ON D024_Id = T075_D024_Id 
											  LEFT JOIN C007 ON C007_Id = T075_C007_Id 
											      WHERE T055_D009_Id = D009_Id 
												    AND IFNULL(T075_Data_Estoque,'0000-00-00')='0000-00-00' 
													AND IFNULL(T075_Data_Previsao,'0000-00-00')>'0000-00-00' AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasPrazoOC} DAY)) 
											   ORDER BY T075_Data_Previsao ASC LIMIT 1), 
		    							   (SELECT T295_Data_Previsao 
										   	  FROM T296 
										 LEFT JOIN T295 ON T295_Id = T296_T295_Id 
										 LEFT JOIN D024 ON D024_Id = T295_D024_Id
										 LEFT JOIN C007 ON C007_Id = T295_C007_Id 
										     WHERE T296_D009_Id = D009_Id
											   AND (T295_Flag_Status = '3' OR T295_Flag_Status = '4') 
											   AND T296_Quantidade_Saldo>0 AND T296_Flag_Finalizar_Compra != 'S' 
											   AND IFNULL(T295_Data_Previsao,'0000-00-00') > '0000-00-00' 
											   AND T295_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasPrazoOC} DAY)) ORDER BY T295_Data_Previsao ASC LIMIT 1)) as previsao", 'colunaTitulo' => 'Previsão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData','colunaExibir' => false),  
			array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Est.Tab', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>false),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'callbackParameter' => array($r_acaoId)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares - ' . $T003_Id, '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true, "", array('T003_Id' => 'T003_Id')), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoque', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            array('colunaValor' => 'T004_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaQuantidade),
			array('colunaValor' => 'T004A_Quantidade_Comercial', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaQuantidadeComercial),
			array('colunaValor' => 'T004_Quantidade_Confirmada', 'colunaTitulo' => 'Conf', 'colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload', 'colunaExibir' => $colunaQuantidadeConfirmada),
			array('colunaValor' => 'T004A_Quantidade_Comercial_Confirmada', 'colunaTitulo' => 'Conf', 'colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Quantidade_Comercial_Confirmada', 'colunaExibir' => $colunaQuantidadeConfirmadaComercial),
			array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'T004_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'width:1%', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T004_Valor_Frete_Unitario', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'T004_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaOrcamento', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'T004_Id', 'right', $rowT003['T003_Casas_Decimais_Preco'], 'T004'), 'colunaExibir' => $colunaPrecoTabela),
			array('colunaValor' => 'T004_Valor_Preco_Tabela', 'colunaTitulo' => 'Forn', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoFornecedorOrcamento', 'callbackParameter' => array('Demonstrativo Preço Fornecedor', '/ven/ven001/outro/demonstrativoPrecoFornecedor/', 'T004_Id', 'right', 4, 'T004'), 'colunaExibir' => true),
			array('colunaValor' => "(SELECT D009_Id FROM D009 WHERE D009_D049_Id=D049_Id AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}') as D009_Id_Transferencia", 'colunaTitulo' => 'D009_Id_Transferencia Id', 'colunaExibir' => false),
			array('colunaValor' => "'{$g['C031']['C004PrecoTransferenciaGridProdutos']}' as C004_Id_Transferencia", 'colunaTitulo' => 'C004_Id_Transferencia Id', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Demonstrativo_Preco_Transferencia', 'colunaTitulo' => 'T004_Demonstrativo_Preco_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Valor_Custo_Transferencia', 'colunaTitulo' => 'T004_Valor_Custo_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Flag_Origem_Produto_Transferencia', 'colunaTitulo' => 'T004_Flag_Origem_Produto_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Valor_Preco_Transferencia', 'colunaTitulo' => 'Transf', 'colunaAlinhar' => 'right', 'callback' => 'retornaPrecoTransferencia', 'callbackParameter' => array($r_acaoId, false, false, 'T004_Id', 'D009_Id_Transferencia', 'C004_Id_Transferencia'), 'colunaExibir' => false),
            array('colunaValor' => 'T004_Percentual_Desconto', 'colunaTitulo' => '% Desc', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaPercentualDesconto),
			array('colunaValor' => 'T004_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $rowT003['T003_Casas_Decimais_Preco'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde); ;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaPrecoLiquido),
			array('colunaValor' => 'T004A_Preco_Unitario_Comercial', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $rowT003['T003_Casas_Decimais_Preco'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde); ;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaPrecoComercial),
			array('colunaValor' => 'T004_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoTotal),
			array('colunaValor' => 'T004_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaOrcamento', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'T004_Id', 'right', 4, 'T004'), 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'T004_Percentual_Desconto_Bruto', 'colunaTitulo' => '% Desc', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaPrecoBruto),
			array('colunaValor' => 'T004_Valor_Preco_Bruto', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowT003['T003_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'T004_Calculo_Valor_Unitario', 'colunaExibir' => $colunaPrecoBruto),
			array('colunaValor' => 'T004_Valor_Preco_Bruto*T004_Quantidade as T004_Valor_Total_Bruto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
			array('colunaValor' => 'T004_Valor_Total_Desconto', 'colunaTitulo' => 'R$ Desc', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Percentual_Margem', 'colunaTitulo' => '%Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T004_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibir' => false, 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T004_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV SL', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Valor_Lucro', 'colunaTitulo' => 'R$ Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => '(T004_Valor_Lucro/((T004_Valor_Custo_Unitario*T004_IPV)*T004_Quantidade))*100', 'colunaTitulo' => '% Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'validaCampoComprar', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Observacao_Entrega', 'colunaTitulo' => 'Entg.dias', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro); ;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload', 'colunaEditarTodos' => true),
            array('colunaValor' => 'T004_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro); ;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'concat(D006_Codigo_CFOP," (",D006_Id,")") as D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho); ; font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T004_D006_Id_Info', 'Demonstrativo CFOP', 'T004_Id', 'left', '', '700')),
            array('colunaValor' => 'T004_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaExibir' => false),
            array('colunaValor' => 'IF(IFNULL(D149_Id,0) > 0, concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao), "????????") as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho); ; font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T004_D005_Id_Info', 'Demonstrativo NCM', 'T004_Id', 'left', '', '500')),
            array('colunaValor' => 'T004_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho); ; font-size:12px'),
            array('colunaValor' => 'T004_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarGeraSelect' => $destinoProdutos, 'style' => 'background: var(--celula-vermelho); ; font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T004_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T004_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T004_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => '(round(T004_Valor_ST,2)/(round(T004_Valor_Total_Preco,2)+round(T004_Valor_IPI,2)))*100', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004A_Percentual_FCP', 'colunaTitulo' => '%FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004A_Percentual_Reducao_PIS', 'colunaTitulo' => '%Red PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004A_Percentual_Reducao_COFINS', 'colunaTitulo' => '%Red COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004_Aliquota_IRPJ', 'colunaTitulo' => '%IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T004_Aliquota_CSLL', 'colunaTitulo' => '%CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T004_Percentual_Partilha_ICMS_Destino', 'colunaTitulo' => '%Difal Dest', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T004_Percentual_Partilha_ICMS_Origem', 'colunaTitulo' => '%Difal Orig', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T004_Valor_Frete_Total', 'colunaTitulo' => 'Frete R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T004_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_IRPJ', 'colunaTitulo' => 'IRPJ R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_CSLL', 'colunaTitulo' => 'CSLL R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T004_Valor_Partilha_ICMS_Destino', 'colunaTitulo' => 'Difal Dest R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T004_Valor_Partilha_ICMS_Origem', 'colunaTitulo' => 'Difal Orig R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T004_Valor_ICMS_Desonerado', 'colunaTitulo' => 'ICMS Deson. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),            
			array('colunaValor' => 'T004A_Valor_FCP_ST', 'colunaTitulo' => 'FCP ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T004A_Valor_FCP', 'colunaTitulo' => 'FCP R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),	
			array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Médio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;'),
            array('colunaValor' => 'T004_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;'),
            array('colunaValor' => 'T004_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;'),
            array('colunaValor' => 'T004_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;'),
			array('colunaValor' => 'T004_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '%Red Base', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;'),
			array('colunaValor' => 'T004_Preco_Pauta_ST', 'colunaTitulo' => 'Pauta', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;'),
            array('colunaValor' => 'T004_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);;', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Valor_Base_ST', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T004_Valor_Base_Partilha_ICMS', 'colunaTitulo' => 'Base Difal', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Base_IRPJ', 'colunaTitulo' => 'Base IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Base_CSLL', 'colunaTitulo' => 'Base CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'T004A_Valor_Base_FCP_ST', 'colunaTitulo' => 'Base FCP ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T004A_Valor_Base_FCP', 'colunaTitulo' => 'Base FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T004_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
			array('colunaValor' => 'T004A_T066_Id', 'colunaTitulo' => 'Locação', 'callback' => 'T004A_T066_Id_Local'),
            array('colunaValor' => 'LinkProduto', 'colunaTitulo' => 'Link', 'colunaAlinhar' => 'center', 'colunaTipo' => 'livre', 'callback' => 'campoLinkProduto', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaTitulo' => 'D009_Valor_Custo_Tabela', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Data_Vencimento', 'colunaTitulo' => 'Validade', 'colunaExibir' => false, 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'T003_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Flag_Transferencia', 'colunaTitulo' => 'T004_Flag_Transferencia', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Demonstrativo_Preco_Venda', 'colunaTitulo' => 'T004_Demonstrativo_Preco_Venda', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Percentual_Comissao', 'colunaTitulo' => '%Comiss', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T004_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'IF(T004_T219_Id > 0 AND T004_Valor_Preco_Cotacao > 0, 675, 450) as width', 'colunaTitulo' => 'width', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Aliquota_ICMS_Credito', 'colunaTitulo' => '%ICM Créd', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_ICMS_Credito', 'colunaTitulo' => 'Valor ICM Créd', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D184_Quantidade_Conversao', 'colunaTitulo' => 'D184_Quantidade_Conversao', 'colunaExibir' => false),
			array('colunaValor' => 'D184_Tipo_Operacao', 'colunaTitulo' => 'D184_Tipo_Operacao', 'colunaExibir' => false),
			array('colunaValor' => 'D001A_Flag_Validade', 'colunaTitulo' => 'D001A_Flag_Validade', 'colunaExibir' => false),
			array('colunaValor' => 'T066_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'T066_Quantidade_Estoque_Liquido', 'colunaExibir' => false),
			array('colunaValor' => 'T066A_Data_Validade', 'colunaTitulo' => 'T066A_Data_Validade', 'colunaExibir' => false)
        );

		$extraConversao = '';
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			array_push($grid, array('colunaValor' => 'T004A_Quantidade_Conversao', 'colunaTitulo' => 'Qtde Conv.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto'));
			array_push($grid, array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso Unitário KG', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'));
			array_push($grid, array('colunaValor' => 'D002A_Utiliza_Conversao', 'colunaTitulo' => 'D002A_Utiliza_Conversao', 'colunaExibir' => false));

			$extraConversao  = ' left join D002 on D002_Id = D001_D002_Id ';
			$extraConversao .= ' left join D002A on D002_Id = D002A_D002_Id ';
		}

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
		-- JOIN
			LEFT JOIN T003 on T003_Id=T004_T003_Id
			LEFT JOIN D024 ON T003_D024_Id = D024_Id
			LEFT JOIN D009 on D009_Id=T004_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D001A on D001_Id=D001A_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D005 on D005_Id=T004_D005_Id
			LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=T003_C004_Id
			LEFT JOIN D006 on D006_Id=T004_D006_Id
			LEFT JOIN T006 on T006_T004_Id=T004_Id
			LEFT JOIN T004A ON T004_Id = T004A_T004_Id
			LEFT JOIN T066 on T004A_T066_Id=T066_Id
			LEFT JOIN D004 on D004_Id=T066_D004_Id
			LEFT JOIN T066A ON T066A_T066_Id = T066_Id
			LEFT JOIN T219 on T004_Id=T219_T004_Id
			LEFT JOIN D069 on D069_Id=D009_D069_Id AND D069_Flag_Tabela_Principal='S'
			LEFT JOIN D070 on D070_Id=D069_D070_Id
			LEFT JOIN D184 on D184_Id=T004A_D184_Id
			LEFT JOIN D037 as unPadrao on unPadrao.D037_Id=T004_D037_Id
			LEFT JOIN D037 as unConversao on unConversao.D037_Id=D184_D037_Id
			{$extraConversao}
		-- WHERE
			WHERE T004_T003_Id = '{$r_acaoId}'
		-- GROUP BY
			group by T004_Id

EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S' or $array['todosCampos']['T004_Valor_Preco_Unitario']<=0) {
			    return 'linhaVermelho';
			}
			if($array['todosCampos']['T006_T005_Id']>0){
				return 'linhaCinza';
			}
		}

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			if('{$g['C031']['permiteBuscarCadastroItem']}' == 'S'){
				acaoClickLinha('{$g['divId']}', function(data, array) {
    	            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/ven/ven001/content/ven001ContentAssociarItemProduto/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T004_Id']) + '&tabela=T004', [500,150]);
	            });
			}else{
				acaoClickLinha('{$g['divId']}', function(data, array) {
    	            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Produto - ID: ' + array['todosCampos']['T004_Id'], '/ven/ven001/content/ven001ContentEditarProduto/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T004_Id']) + '&T003_Id=' + encodeURIComponent(array['todosCampos']['T003_Id']) + '&tabela={$from}', [650,500]);
	            });
			}
			

			// $('#{$g['divId']} .grid .campoEditavel').bind('click', function(event) {
			// 	return false;
			// });
			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function T004_Calculo_Valor_Unitario(T004_Id, campoEditado, valorAntigo, novoValor) {
				T004_Id = T004_Id.replace(/T004_Id-/, '');
					if(campoEditado == 'T004_Valor_Preco_Bruto'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
			}
			
			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function colunaEditarCallbackJS_T004_Quantidade_Confirmada() {
				divRefresh('{$r_divReload}');
			}

			function colunaEditarCallbackJS_T004_Quantidade_Comercial_Confirmada(T004_Id, campoEditado, valorAntigo, novoValor) {
				T004_Id = T004_Id.replace(/T004_Id-/, '');

				$.getJSON('/ven/ven001/grid_func-ajax/confirmarQuantidadeComercial/?ajax=true&T004_Id='       + encodeURIComponent(T004_Id) + 
																					         '&campoEditado=' + encodeURIComponent(campoEditado) +
																						     '&valorAntigo='  + encodeURIComponent(valorAntigo) +
																						     '&novoValor='	   + encodeURIComponent(novoValor) +  
																						     '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			}

			function colunaEditarCallbackJSReload() {
				divRefresh('{$r_divReload}');
			}
			
			function colunaEditarCallbackJS_T004_Percentual_Desconto(T004_Id, campoEditado, valorAntigo, novoValor) {
					T004_Id = T004_Id.replace(/T004_Id-/, '');
					if(campoEditado == 'T004_Percentual_Desconto'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004_Quantidade'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarQuantidade/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + 
																				'&campoEditado='     + encodeURIComponent(campoEditado) +
																				'&valorAntigo='      + encodeURIComponent(valorAntigo) +
																				'&novoValor='        + encodeURIComponent(novoValor) +  
																				'&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
								divRefresh('{$r_divReload}');
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004A_Quantidade_Conversao'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarQuantidade/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + 
																				'&campoEditado='     + encodeURIComponent(campoEditado) +
																				'&valorAntigo='      + encodeURIComponent(valorAntigo) +
																				'&novoValor='        + encodeURIComponent(novoValor) +  
																				'&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
								divRefresh('{$r_divReload}');
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004A_Quantidade_Comercial'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarQuantidade/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + 
																				'&campoEditado='     + encodeURIComponent(campoEditado) +
																				'&valorAntigo='      + encodeURIComponent(valorAntigo) +
																				'&novoValor='        + encodeURIComponent(novoValor) +  
																				'&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
								divRefresh('{$r_divReload}');
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004_Quantidade_Confirmada'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
								
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004_Valor_Preco_Unitario'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004A_Preco_Unitario_Comercial'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T004_Percentual_Desconto_Bruto'){
						$.getJSON('/ven/ven001/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
					// divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id'); // grid
					//divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
					divRefresh('{$r_divReload}');
				}

				function colunaEditarCallbackJS_T004_Item(T004_Id, campoEditado, valorAntigo, novoValor) {
					T004_Id = T004_Id.replace(/T004_Id-/, '');
					$.getJSON('/ven/ven001/grid_func-ajax/processarNumeroItemOrcamento/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				}
		} </script></div>
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
	
	case 'ven001GridTrocas':
		
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T138_Id', $g['divId'])),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
			//array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback'=>'retornaOperacaoTroca'),						
			array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'colunaEditar' => true,'colunaEditarGeraSelect' => array(array('title' => 'ENTRADAS', 'value' => 'P'),array('title' => 'DEVOLUÇÕES', 'value' => 'I')), 'callback' => 'retornaOperacaoTroca'),
			array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);
		
		
								

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T138';

		$extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
			WHERE T138_T003_Id = '{$r_acaoId}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		/*
		function gridLinha($array) {
			if($array['todosCampos']['T006_T005_Id']>0){
				return 'linhaCinza';
			}
		}
		*/

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
			//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
			function colunaEditarCallbackJS_Grid_Refresh() {
				divRefresh('{$g['divId']}');
			}
		} </script></div>
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
	
	case 'ven001GridServicos':			
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'excluirServico'),
            array('colunaValor' => 'geraImagem', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => 'T145_Id', 'colunaTitulo' => 'OS', 'chavePrimaria' => true),
            array('colunaValor' => 'T145_Valor_Unitario', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaEditar' => true),
            array('colunaValor' => 'concat(T145_Titulo, \'<br />\', T145_Descricao) as Servico', 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'ven001_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo', 'colunaExibir' => false),
            array('colunaValor' => 'T145_Tempo_Estimado', 'colunaTitulo' => 'Tempo'),
            array('colunaValor' => 'T145_D110_Id', 'colunaTitulo' => 'Competência', 'colunaExibir' => false),
            array('colunaValor' => 'T145_D112_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T145_D116_Id', 'colunaExibir' => false),
            array('colunaValor' => 'statusServicos', 'colunaTitulo' => 'Status Serviços', 'callback' => 'validaCampoStatusServicos', 'colunaTipo' => 'livre'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T145';

		$extra = <<<EOT
			LEFT JOIN T004 ON T004_Id=T145_T004_Id
			LEFT JOIN D009 ON D009_Id=T004_D009_Id
			LEFT JOIN D049 ON D009_D049_Id=D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			WHERE T145_T003_Id='{$acaoId}'
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T145_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		
		case 'ven001grid05Processos':
			
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T146_Id', $g['divId'])),
			array('colunaValor' => 'T146_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor' => 'T146_T145_Id', 'colunaTitulo' => 'IdServico', 'colunaExibir' => false),
			array('colunaValor' => 'T146_D110_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
			array('colunaValor' => 'T146_Titulo', 'colunaTitulo' => 'Título', 'colunaEditar'=>true),
			array('colunaValor' => 'T146_Descricao', 'colunaTitulo' => 'Descrição', 'colunaEditar'=>true),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T146';

			$extra = <<<EOT
				where 
				T146_T145_Id = {$r_T145_Id}
EOT;

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			/* retirada a ação q faz o registro do grid ser carregado no form acima
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T146_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
			} </script></div>
EOT;
			*/
			
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

			$novoIdTelaForm = uniqid();
		break;	
		
		case 'ven001GridMateriais':
			
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T152_Id', $g['divId'])),
				array('colunaValor' => 'T152_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T152_D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),				
				array('colunaValor' => 'T152_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar'=>true),
				array('colunaValor' => 'T152_D037_Id', 'colunaTitulo' => 'Unidade', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true), 'callback' => 'buscaUnidade'),
				array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade','colunaExibir'=>false),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo'=>'Marca'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T152';

			$extra = <<<EOT
				left join D037 on D037_Id=T152_D037_Id
				left join D009 on D009_Id=T152_D009_Id
				LEFT JOIN D049 on D049_Id=D009_D049_Id
				LEFT JOIN D001 on D001_Id=D049_D001_Id
				LEFT JOIN D082 on D082_Id=D049_D082_Id				
				where 
				T152_T145_Id = {$r_T145_Id}
EOT;

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			/* retirada ação que carrega o registro selecionado no form acima
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T152_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
			} </script></div>
EOT;
			*/
			
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

			$novoIdTelaForm = uniqid();
		break;
		
		case 'ven001GridPagamentos':
			global $g;
			$sqlT003 = "SELECT T003_Pgto_Data, T003_Pgto_C007_Id, C007_Login FROM T003 LEFT JOIN C007 ON C007_Id=T003_Pgto_C007_Id WHERE T003_Id='$r_acaoId'";
			$resultT003 = mysql_query($sqlT003);
			$linhaT003 = mysql_fetch_array($resultT003);
			$dataGerada = gCorrigeData($linhaT003['T003_Pgto_Data']);
			$user = strtoupper($linhaT003['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por ".$user." em ".$dataGerada."";
			}

			$CNPJ_Empresa_Corrente = substr($g['C004']['C004_Cnpj'], 0, -8);
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T089_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T089_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T089_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T089_Prazos_Dias"),
				array('colunaValor' => 'T089_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaAlinhar' => 'center', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T089_Prazos_Dias"),
				array('colunaValor' => 'ven001_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "ven001_Dia_Semana"),
                array('colunaValor' => 'T089_Taxa_Juros', 'colunaTitulo' => 'Taxa Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2','colunaExibir' => false),
                array('colunaValor' => 'T089_Valor_Juros', 'colunaTitulo' => 'Valor Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2','colunaExibir' => false),
                array('colunaValor' => 'T089_Valor_Parcela_Original', 'colunaTitulo' => 'Valor Original', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2','colunaExibir' => false),
                array('colunaValor' => 'T089_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T089_Prazos_Dias"),
				array('colunaValor' => 'T089_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE D027_Flag_Ativo = "S" AND D027_Flag_Exibe_CRM = "S" AND ((D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0) order by D027_Portador', true), 'callback' => 'T089_D027_Id_Portador'),
				array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				//array('colunaValor' => 'ven001_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven001_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T089';

			$extra = <<<EOT
				left join D027 on D027_Id=T089_D027_Id
				WHERE T089_T003_Id = {$r_acaoId}
EOT;

			/**
			* Filtro
			
			// Gera o filtro e acrescenta ao $extra
			$gridFiltro = array(
				
			);
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
            */
			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T089_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T089_Prazos_Dias(T089_Id, campoEditado, valorAntigo, novoValor) {
					T089_Id = T089_Id.replace(/T089_Id-/, '');
					$.getJSON('/ven/ven001/grid_func-ajax/atualizarParcelas/?ajax=true&T089_Id='      		+ encodeURIComponent(T089_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (!request.code) {
							dialogConfirm('Erro', request.data);
						}
						divRefresh('{$g['divId']}');
					});
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
		
		case 'ven001GridEmail':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Chave Primária
			array('colunaValor' => 'T139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'Data/Hora', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T139_Pasta', 'colunaTitulo' => 'Pasta'),
			array('colunaValor' => 'T139_Titulo', 'colunaTitulo' => 'Assunto'),
			array('colunaValor' => 'T139_De', 'colunaTitulo' => 'De'),
			array('colunaValor' => 'T139_Para', 'colunaTitulo' => 'Para'),
			array('colunaValor' => 'T139_Numero_Registros', 'colunaTitulo' => 'Ítens'),
			array('colunaValor' => 'T139_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			where T139_T003_Id='{$r_acaoId}'
EOT;

/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006EnviarEmail2">Enviar Email</button>
				<button type="button" id="etc006VerificarEmailsEnviadosVen001">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T003_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosVen001').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/atualizarEmails/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove(); 
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
				});
			} </script></div>
EOT;
	
			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($grid);
	
			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			echo <<<EOT
			<script type="text/javascript">
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace('T139_Id-', '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=S&acaoId=' + encodeURIComponent(id), [900,550]);
				});
			</script>
			
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
		
		case 'ven001GridPrincipalOrcamentosCancelados':
			global $g;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'count(distinct T003_Id) as Total', 'colunaTitulo' => 'Orçamentos'),
			array('colunaValor' => 'D047_Descricao_Motivo', 'colunaTitulo' => 'Motivo'),
			array('colunaValor' => 'sum(T003_Valor_Total) as Valor_Total', 'colunaTitulo' => 'Valor Total', 'callback'=>'gCorrigeNumero'),
		);
		/**
		* Definição da SQL
		*/
		$from = 'T003';
		
		$extra = <<<EOT
		left join D024 on D024_Id=T003_D024_Id
		left join D013 on D013_Id=T003_D013_Id
	    left join D047 on D047_Id=T003_D047_Id
	    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
	    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
	    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where T003_C004_Id='{$g['empresaAtual']}'
	    and T003_Flag_Perdido='S'
	    group by T003_D047_Id
		
EOT;
		
		// /**
		// * Botões (Versão Acima do GRID)
		// */
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad046FormCadastroMotivosCanecelamento">Incluir</button>
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad046FormCadastroMotivosCanecelamento').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Motivos Cancelamento do Orçamento', '/cad/cad046/content/cad046ContentMotivosCancelamentoIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			//'D047_Descricao_Motivo' => array('titulo' => 'Motivo', 'tipo' => 'texto'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		// echo <<<EOT
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// var dbclickPrevent = false;
				//$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					//if (dbclickPrevent) { return false; }
					//dbclickPrevent = true;
					//setTimeout(function() { dbclickPrevent = false; }, 500);
					// // (substituir pelo mesmo ID da chave primária do grid)
					// var chavePrimaria = 'D047_Id';	
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// var id = $(this).closest('tr').attr('id').split('|');
					// var acaoId = id[0].replace(chavePrimaria + '-', '');
					// // Ação: abrir nova janela
	        		// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Motivos Cancelamento do Orçamento - ID: ' + acaoId, '/cad/cad046/content/cad046ContentMotivosCancelamentoIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
		        // });
				// $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			// } </script></div>
// EOT;

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
	
	case 'ven001GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T188_Id', $g['divId'])),
			array('colunaValor' => 'T188_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T188_T003_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
			array('colunaValor' => 'T188_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'T188_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T188_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			array('colunaValor' => 'visualizarArquivo', 'colunaTitulo' =>'Visualizar', 'colunaTipo'=>'livre', 'callback'=>'visualizarArquivo', 'callbackParameter' => array('T188_Id', 'documentoOrcamento'), 'style' => 'width: 1%'),

			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T188';

			$extra = <<<EOT
				left join C007 on C007_Id=T188_C007_Id
				where 
				(T188_T003_Id = '{$r_acaoId}')
EOT;

			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T188_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
        
        case 'ven001GridAgregados':
            
            
            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(
            array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T202_Id', $g['divId'])),
            array('colunaValor' => 'T202_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
            array('colunaValor' => 'T202_Descricao', 'colunaTitulo' => 'Descrição','colunaEditar' => true),
            array('colunaValor' => 'T202_Observacao', 'colunaTitulo' => 'Observação','colunaEditar' => true),
            array('colunaValor' => 'T202_Valor_Unitario', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2','colunaEditar' => true),
            );

            /**
            *
            * Definição da SQL
            */
            $from = 'T202';

            $extra = <<<EOT
                where 
                T202_T004_Id = {$r_acaoId}
EOT;

            
            //Define ação do click de abertura (ao clicar em uma linha do grid)

            $novoIdTelaForm = uniqid();
            echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
               // $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                   // var id = $(this).closest('tr').attr('id').replace(/T202_Id-/, '');
                    //divSetarAcaoId('{$r_divReload}', id);
                   // divRefresh('{$r_divReload}');
                //});
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
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

            $novoIdTelaForm = uniqid();
        break;
        
        case 'ven001GridPrincipalProdutos':
        
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
        $grid = array(
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'T004_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T004_Descricao_Produto', 'colunaTitulo' => 'Descrição produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Veiculo', 'colunaTitulo' => 'Veículo', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'T004_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
        );
        
        /**
         * Definição da SQL
         */
        $from = "T004";
        
        $extra = <<<EOT
                    left join D009 on D009_Id=T004_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join D006 on D006_Id=T004_D006_Id
                    left join D037 on D037_Id=T004_D037_Id
                    left join T003 on T003_Id=T004_T003_Id
                    left join T006 on T006_T004_Id=T004_Id
                    left join D024 on D024_Id=T003_D024_Id
                    left join D018 on D018_Id=D024_D018_Id
                    left join C007 on C007_Id=T003_C007_Id_Vendedor_Interno
                    left join D021 on D021_Id=D024_D021_Id
                    left join C004 on C004_Id=T003_C004_Id
                    left join D116 on D116_Id=T003_D116_Id
EOT;
        /**
        * Pode ser definido uma classe para cada linha do grid
        */        
        function gridLinha($array) {
            if(!empty($array['todosCampos']['T006_Id'])){
                return 'linhaCinza';
            }
        }
        
        /**
        * Filtro
        */
        $gridFiltro = array(
            'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
            'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T004_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'T004_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
                        'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
        );
        //Gera o filtro e acrescenta ao $extra
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
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T004_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T004_Valor_Total_Preco_Sem_Desconto)", $from, $extra));
        $valorTotalVendido = $g['sqlAuto']->pegarSqlCampo("count(T006_Id)", $from, $extra . " AND T006_Id IS NOT NULL");
        $valorTotal1Vendido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T006_Quantidade) Total", $from, $extra));
        $valorTotal2Vendido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T006_Valor_Total_Preco_Sem_Desconto) Total", $from, $extra));
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Qtd total cotados<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor total cotados<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Itens Vendidos<br><input type="text" value="{$valorTotalVendido}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Qtd vendidos<br><input type="text" value="{$valorTotal1Vendido}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor total vendidos<br><input type="text" value="{$valorTotal2Vendido}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;

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

    case 'ven001GridProdutosDividir':
        /**
            * Definição do Grid (Tabela)
            */
            
        //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Produtos</div>';
        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'excluirProduto'),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            //array('colunaValor' => 'T004_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T004', 'style' => 'width:1%'),
            array('colunaValor' => 'T004_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven001CampoDividir','callbackParameter' => 'T004', 'style' => 'width:1%'),
            //array('colunaValor' => 'T004_Item', 'colunaTitulo' => 'Item', 'colunaAlinhar' => 'center' ),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            //array('colunaValor' => 'T004_Codigo_Substituto', 'colunaTitulo' => 'Código Cli.', 'colunaAlinhar' => 'left'),
            //array('colunaValor' => 'T004_Numero_Pedido_Compra', 'colunaTitulo' => 'Num. OC', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T004_Item_Pedido_Compra', 'colunaTitulo' => 'Item OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T004_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3' ),
            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Qtd', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2','colunaEditar' => true),
            //array('colunaValor' => 'T004_Valor_Preco_Original', 'colunaTitulo' => 'Original', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T004_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Total_Preco', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            //array('colunaValor' => 'Duplicar', 'colunaTitulo' => '','colunaTipo' => 'livre', 'style'=>'width:1%;', 'callback'=>'ven012DuplicarProduto'),            
            //array('colunaValor' => 'T004_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T004_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T004_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir'=>false),
            // array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T004_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T004_Valor_ICMS_Substituicao_Retencao/T004_Quantidade as T004_Valor_ICMS_Substituicao', 'colunaTitulo' => 'Valor ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
        );

        /**
            *
            * Definição da SQL
            */
        $from = 'T004';

        $extra = <<<EOT
                left join T003 on T003_Id=T004_T003_Id
                left join D024 on D024_Id=T004_D024_Id
                left join D037 on D037_Id=T004_D037_Id
                left join D009 on D009_Id=T004_D009_Id
                left join D049 on D049_Id=D009_D049_Id
                left join D001 on D001_Id=D049_D001_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D006 on D006_Id=T004_D006_Id
                left join D005 on D005_Id=T004_D005_Id
                left join C008 on C008_Id=D001_C008_Id
                where 
                T004_T003_Id = '{$r_acaoId}'
                
EOT;
        if (!empty($xConsulta_Clausula_Filtro)) {
            $extra .= <<<EOT
                      and $xConsulta_Clausula_Filtro
EOT;
        }
        
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
            $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'T004_Id';    
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
            });
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
            
            // Função JS executada como callback do editar campo (colunaEditarCallbackJS)
            function colunaEditarCallbackJS_T004_Percentual_Desconto(T004_Id, campoEditado, valorAntigo, novoValor) {
                    T004_Id = T004_Id.replace(/T004_Id-/, '');
                    if(campoEditado == 'T004_Percentual_Desconto'){
                        $.getJSON('/ven/ven001/grid_func-ajax/ven001aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
                                                                                          '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}');
                                //alert("NFe Excluída");
                            } else {
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    }else if(campoEditado == 'T004_Valor_Preco_Sem_Desconto_Unitario'){
                        $.getJSON('/ven/ven001/grid_func-ajax/ven001aplicarPercentualDesconto/?ajax=true&T004_Id='    + encodeURIComponent(T004_Id) + 
                                                                                          '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='            + encodeURIComponent(novoValor) +  
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


	break;

	case 'ven001GridTrocasDividir':
        
        $T003_Id = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
         * Definição do Grid (Tabela)
         */
         //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Trocas</div>';
        $grid = array(
            //array('colunaValor' => 'T138_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao','callbackParameter' => 'T138', 'style' => 'width:1%'),
            array('colunaValor' => 'T138_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven001CampoDividir','callbackParameter' => 'T138', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Troca'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            //array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
            array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
            array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
            //array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação'),
        );
        
        
                                

        /**
         *
         * Definição da SQL
         */
        $from = 'T138';

        $extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            left join T008 on T008_T138_Id=T138_Id
            left join T007 on T007_Id=T008_T007_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
            WHERE T138_T003_Id = '{$T003_Id}'
EOT;

        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        /*
        function gridLinha($array) {
            if($array['todosCampos']['T006_T005_Id']>0){
                return 'linhaCinza';
            }
        }
        */

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
            //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        	$('#{$g['divId']} .gridOptTop');
        } </script></div>
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

    case 'ven001GridServicosDividir':

            /**
            * Definição do Grid (Tabela)
            */
            //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Serviços</div>';
            $grid = array(
            array('colunaValor' => 'T145_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven001CampoDividir','callbackParameter' => 'T145', 'style' => 'width:1%'),
            array('colunaValor' => 'T145_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            //array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => "concat(T145_Titulo, ' ', T145_Descricao) as Descricao", 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => 'T145_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'T145_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
            //array('colunaValor' => 'T145_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
            //array('colunaValor' => 'T145_T005_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            //array('colunaValor' => 'T145_T145_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            //array('colunaValor' => 'T145_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'ven002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
            array('colunaValor' => 'T145_D116_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T145_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'colunaExibir'=>false),
            //array('colunaValor' => 'if(T007_Id>0 and (T007_Numero_Nota_Fiscal<0 or T007_Numero_Nota_Fiscal is null or T007_Numero_Nota_Fiscal=0),"FINALIZAR",T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'ven012CampoNF','style' => 'width:1%;'),
            //array('colunaValor' => 'T145_Status', 'colunaTitulo' => 'Status'),
            //array('colunaValor' => 'T145_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
            //array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir'=>false),
            );
    
            /**
                *
                * Definição da SQL
                */
            $from = 'T145';
    
            $extra = <<<EOT
                    LEFT JOIN T004 ON T004_Id=T145_T004_Id
                    LEFT JOIN D009 ON D009_Id=T004_D009_Id
                    LEFT JOIN D049 ON D009_D049_Id=D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    WHERE T145_T003_Id='{$r_acaoId}'
EOT;
    

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 		 $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
                divSetarAcaoId('{$g['divId']}', id);
                divRefresh('{$g['divId']}');
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
        $novoIdTelaForm = uniqid();
    break;

    case 'ven001GridCompras':

    		$exibirColunaCot = false;
    		if(isset($g['c029Ids'][221])){
    			$exibirColunaCot = true;
    		}

        	/**
            * Definição do Grid (Tabela)
            */
			$grid = array(
	            array('colunaValor' => 'T004_Id', 'chavePrimaria' => true, 'colunaTitulo' => 'Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T004_T003_Id', 'colunaTitulo' => 'T004_T003_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T004_D009_Id', 'colunaTitulo' => 'T004_D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'T003_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
	            array('colunaValor' => 'T218_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'T218_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
				array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),	            array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T004_Descricao_Produto', 'colunaExibir' => false),
	            array('colunaValor' => 'descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left', 'callback' => 'validaCampoCotacaoOrcamento', 'colunaTipo' => 'livre'),
	            array('colunaValor' => 'substr(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%;font-size:11px'),
	            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
	            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
	            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
			    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
			    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
	            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
	            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Fis', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaCasasDecimais' => 2, 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estq.Liq', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaCasasDecimais' => 2),
	            array('colunaValor' => 'IF((T004_Quantidade - IF(D009_Quantidade_Estoque_Liquido<0,0,D009_Quantidade_Estoque_Liquido)) <= 0, 0.00, T004_Quantidade-IF(D009_Quantidade_Estoque_Liquido<0,0,D009_Quantidade_Estoque_Liquido))', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'width:1%;color: var(--BGVermelho)'),
	            array('colunaValor' => 'campoCotar', 'colunaTitulo' => 'Cot', 'callback' => 'validaCampoCotarOrcamento', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'width:1%'),
	            array('colunaValor' => 'T004_Flag_Comprar', 'colunaTitulo' => 'Req.', 'callback' => 'checkboxCotarOrcamento', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'style' => 'width:1%'),
				array('colunaValor' => 'T004A_Flag_Solicitar_Prazo_Compras', 'colunaTitulo' => 'Prz.', 'callback' => 'checkboxPrazoCompra', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'style' => 'width:1%', 'colunaExibir' => false),
	            array('colunaValor' => 'T004_Flag_Gerar_Cotacao', 'colunaTitulo' => 'Cot.', 'callback' => 'checkboxCotarEmergencia', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'T004_C007_Id_Comprador', 'colunaExibir' => $exibirColunaCot),
	            array('colunaValor' => 'T004_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço<br>venda', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;width:1%', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto'),
	            array('colunaValor' => 'T004_Observacao_Entrega', 'colunaTitulo' => 'Entreg<br>dias', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;width:1%'),
	            array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-azul);width:1%', 'callback' => 'ven001AbrirCotacaoCompra'),
	            array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Preço<br>cotação', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'background-color: var(--celula-azul);width:1%'),
	            array('colunaValor' => 'T219_Valor_Preco_Venda_Negociado-T219_Valor_Preco_Venda', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	            array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrg<br>dias', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-azul);width:1%'),
				array('colunaValor' => 'T004A_Data_Hora_Solicitacao_Compra', 'colunaTitulo' => 'Solicitou compra', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaExibir' => false),
				array('colunaValor' => 'T219A_Data_Hora_Responta_Venda', 'colunaTitulo' => 'Resposta Venda', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaExibir' => false),
				array('colunaValor' => 'T004_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'colunaEditar' => true),
	            array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'width:1%;font-size:11px'),
	            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré-cadastro', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
	        );
    
            /**
            * Definição da SQL
            */
            $from = 'T004';
            $extra = <<<EOT
            	LEFT JOIN T006 ON T004_Id = T006_T004_Id
            	LEFT JOIN T003 ON T003_Id = T004_T003_Id
				LEFT JOIN T005 ON T005_Id = T006_T005_Id
				LEFT JOIN T219 ON T219_Id = T004_T219_Id
				LEFT JOIN T218 ON T218_Id = T219_T218_Id
				LEFT JOIN D009 ON D009_Id = T004_D009_Id
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D001 ON D001_Id = D049_D001_Id
				LEFT JOIN D082 ON D082_Id = D049_D082_Id  
				LEFT JOIN C007 ON C007_Id = D009_C007_Id_Comprador
				LEFT JOIN T004A ON T004_Id = T004A_T004_Id
				LEFT JOIN T219A ON T219_Id = T219A_T219_Id
				WHERE T004_T003_Id='{$r_T003_Id}' GROUP BY T004_Id
EOT;
		//LEFT JOIN T219 ON IF(IFNULL(T004_T219_Id,0)>0, T004_T219_Id=T219_Id, T219_T004_Id=T004_Id)
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
    	$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));
		$sql_ = base64_encode($geradoSql);
		/**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

    if(isset($g['c029Ids'][221])){
		if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est009GerarCotacaoEmergenciaEmOrc">Gerar Cotação Manual</button>
				</div>
EOT;
		}
	}

    // Não executar quando houver o refresh de linha
    if (empty($r_linhaGridId)) {
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est009GerarCotacaoEmergenciaEmOrc').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009GerarCotacaoEmergencia/?ajax=true&T003_Id='+ encodeURIComponent('{$r_T003_Id}') +'&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Adicionar": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/adicionarProdutosCotacao/?ajax=true&T003_Id='+ encodeURIComponent('{$r_T003_Id}') + '&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
											abrirJanela(false, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra', '/est/est015/content/est015ContentEditarOrcamento/', '&T003_Id=' + encodeURIComponent("{$r_T003_Id}") + '&acaoId=' + numeroCotacao, [1000,610]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Nova Cotação": function() {
									$('#{$g['divId']}').showLoading();
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est015/content/est015ContentOrcamento/', '&tab=geral&T003_Id=' + encodeURIComponent("{$r_T003_Id}") + '&GerarCotacaoEmergencia=true&extra=' + encodeURIComponent("{$sql_}"), [650,500]);
									$('#{$g['divId']}').hideLoading();
									$(this).dialog("destroy").remove();
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							var novoId = dialogConfirm('Gerar Cotação', request.data + '<br /><br />Digite o número de uma cotação para adicionar os produtos selecionados.<br /><br />Cotação: ', \$buttons);
							$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				function abrirCotacao(T218_Id){					
					$('#{$g['divId']}').showLoading();
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra', '/est/est015/content/est015ContentEditarOrcamento/', '&T003_Id=' + encodeURIComponent("{$r_T003_Id}") + '&acaoId=' + T218_Id, [1000,610]);
					$('#{$g['divId']}').hideLoading();
				}

			} </script></div>
EOT;
    }

    	/**
		* Botões (Versão Acima do GRID)
		*/
/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven001CotarProdutos">Cotar Produtos Selecionados</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven001CotarProdutos').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven001/grid_func-ajax/cotarProdutosSelecionados/?ajax=true&T003_Id='+ encodeURIComponent('{$r_T003_Id}') +'&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if(request.code){
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				});

				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven001/grid_func-ajax/ven001MarcarTodosCotar/?ajax=true&T003_Id=' + encodeURIComponent('{$r_T003_Id}') + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven001/grid_func-ajax/ven001DesmarcarTodosCotar/?ajax=true&T003_Id=' + encodeURIComponent('{$r_T003_Id}') + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
				}
			} </script></div>
EOT;
*/

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T219_Valor_Preco_Venda']>0){
				return 'color:blue;font-size:13px';
			}else if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
				return 'linhaVermelho';
			}
		}

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 		 $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T004_Id-/, '');                
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
        $novoIdTelaForm = uniqid();
    break;

    case 'ven001GridProdutoOC':
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Observacao_Entrega', 'colunaTitulo' => 'Entreg<br>dias', 'style' => 'background: var(--celula-amarelo-claro);;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(T224_Data_Emissao + INTERVAL T225_Observacao_Entrega DAY) as dataPrevisao', 'colunaTitulo' => 'Previsão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605')),
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30)', 'colunaTitulo' => 'Razão Social'),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qtde', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo unit', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'sum(T014_Quantidade)', 'colunaTitulo' => 'Qtde NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'SUM(T225_Quantidade_Pendente_3(T225_Id))', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => 'group_concat(distinct T013_Id SEPARATOR \'|\')', 'colunaTitulo' => 'NF', 'callback' => 'danfeNFsDaOrdemDeCompra'),
            array('colunaValor' => 'T224_Nome_Status', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center'),
        );

		/*
		* Definição da SQL
		*/
		$from = 'T225';
		
		$extra = <<<EOT
			LEFT JOIN T224 ON T224_Id = T225_T224_Id
			LEFT JOIN D024 ON D024_Id = T224_D024_Id
			LEFT JOIN C007 ON C007_Id = T224_C007_Id_Vendedor_Interno
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id
			WHERE (T224_C004_Id='{$g['empresaAtual']}' AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			AND T225_D009_Id = '{$r_acaoId}'
			AND T224_Flag_Status!='11'
			AND T225_Quantidade_Pendente_Estoque(T225_Id)>0
			GROUP BY T225_Id
EOT;
		
		/**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
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
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T224_Id = id[0].replace(/T224_Id-/, '');
EOT;
	   if(isset($g['c029Ids'][171])){
			echo <<<EOT
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela={$from}', [990,605]);
EOT;
		}
		echo <<<EOT
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

	case 'ven001GridProdutoOP':
        /*
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T256_T006_Id', 'colunaTitulo' => 'T256_T006_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade-T256_Quantidade_Produzida', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'usuario.C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            //array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado","Aberto")', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag Status', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T256_Data_Previsao_Entrega', 'colunaTitulo' => 'Previsão entrega', 'style' => 'width:1%;', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Observacao_Producao', 'colunaTitulo' => 'Observação produção', 'style' => 'width:1%;'),

            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Prazo', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'Date_add(T005_Data_Emissao, interval T006_Observacao_Entrega day)', 'colunaTitulo' => 'Previsão pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação Pedido', 'colunaAlinhar' => 'left', 'colunaEditar' => false, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observação Compra', 'colunaAlinhar' => 'left', 'colunaEditar' => false, 'style' => 'background:var(--celula-amarelo-claro)'),
        );

		$from = 'T256';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
		    LEFT JOIN T255 on T255_Id=T256_T255_Id
		    LEFT JOIN C007 as usuario on usuario.C007_Id=T255_C007_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 as vendedor on vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			WHERE D009_Id='{$r_acaoId}'
			AND T255_Flag_Status != '99'
			AND (T256_Quantidade-IFNULL(T256_Quantidade_Entrada_Estoque,0)) > 0
EOT;
		function gridLinha($array) {
			global $r_T006_Id;
			if ($array['todosCampos']['T256_T006_Id'] == $r_T006_Id) {
			    return 'linhaAzul';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;
    
    case 'ven001PesquisaTabelaFornecedores':
    	$filtroObrigatorio = true;
    	$acaoId = unserialize($r_campo9);
    	$tabela = $r_tabela;
    	$acaoId = $acaoId[0];
		/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
            array('colunaValor' => 'D069_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Id', 'colunaTitulo' => 'Tab', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,15)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
            array('colunaValor' => 'D069_D006_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_ICMS', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_PIS', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_COFINS', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D005_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaExibir' => false),
            array('colunaValor' => $tabela.'_Id', 'colunaExibir' => false),
            array('colunaValor' => $tabela.'_D024_Id', 'colunaExibir' => false),
            array('colunaValor' => $tabela.'_C004_Id', 'colunaExibir' => false),
            array('colunaValor' => $tabela.'_Flag_Lista_Preco', 'colunaExibir' => false),
            array('colunaValor' => $tabela.'_Flag_Acp', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'preCadastro', 'colunaTitulo' => 'Nosso', 'callback' => 'preCadastroProdutoFornecedor', 'colunaTipo' => 'livre', 'style' => 'width: 1%', 'callbackParameter' => $tabela),
            array('colunaValor' => 'D069_Descricao_Produto', 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'left', 'style' => ''),
            array('colunaValor' => 'if(D069_D037_Id > 0, D037_Unidade, D069_Unidade) as Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'if(D069_D009_Id > 0, D082_Marca, D069_Marca) as Marca', 'colunaTitulo' => 'Marca', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            //array('colunaValor' => "IF(D005_Classificacao_Fiscal != '',concat(D005_Classificacao_Fiscal,'-',D005_Flag_Aplicacao),CONCAT('<font color=\"red\">',D069_NCM,'</font>'))", 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'callback' => 'campoInfo', 'callbackParameter' => array('D069_D005_Id_Info', 'Demonstrativo NCM', 'D069_Id', 'left', '', '500')),
            array('colunaValor' => 'D069_Quantidade_Embalagem', 'colunaTitulo' => 'D069_Quantidade_Embalagem', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Original', 'colunaTitulo' => 'Preço FOB', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D069_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo CIF', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'retornaPrecoVendaTabelaFornecedor', 'colunaTitulo' => 'Preço Sugerido', 'colunaAlinhar' => 'right', 'callback' => 'retornaPrecoVendaTabelaFornecedor', 'colunaTipo' => 'livre', 'style' => '', 'callbackParameter' => $tabela),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D069';

		$extra = <<<EOT
			LEFT JOIN D070 ON D070_Id=D069_D070_Id
			LEFT JOIN D024 ON D024_Id=D070_D024_Id
			LEFT JOIN D009 ON D009_Id=D069_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D037 ON D037_Id=D069_D037_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D006 ON D006_Id=D069_D006_Id
			LEFT JOIN D005 ON D005_Id=D069_D005_Id
			LEFT JOIN {$tabela} ON {$tabela}_Id={$acaoId}
			WHERE D070_C004_Id = '{$g['empresaAtual']}'
			GROUP BY D069_Id
EOT;

		$gridFiltro = array(
            'D069_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'ifnull(D001_Codigo_Produto[[virgula]]\'\')' => array('titulo' => 'Nosso', 'tipo' => 'texto'),
            'D069_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D069_NCM,D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'D069_Codigo_Barras' => array('titulo' => 'EAN', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);	
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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

	case 'ven001GridProdutoDI':
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T075_Id', 'colunaTitulo' => 'T075_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T075_Data_Emissao_DI', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T075_Data_Previsao', 'colunaTitulo' => 'Previsão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T075_Numero_DI', 'colunaTitulo' => 'DI', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('DI', '/imp/imp003/content/imp003ContentDiIncluir/', 'T075_Id', '990,605')),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30)', 'colunaTitulo' => 'Razão Social'),
            array('colunaValor' => 'T055_Quantidade', 'colunaTitulo' => 'Qtde', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo unit', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
        );

		/*
		* Definição da SQL
		*/
		$from = 'T055';
		
		$extra = <<<EOT
			LEFT JOIN T075 ON T075_Id = T055_T075_Id
			LEFT JOIN D024 ON D024_Id = T075_D024_Id
			LEFT JOIN C007 ON C007_Id = T075_C007_Id 
			WHERE T075_C004_Id='{$g['empresaAtual']}'
			  AND T055_D009_Id = '{$r_acaoId}'
		   	  AND IFNULL(T075_Data_Estoque,'0000-00-00')='0000-00-00' 
              AND T075_Data_Previsao>0 
              AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$g['C031']['diasMedioPrazo']} DAY))			
			GROUP BY T055_Id
EOT;
		
		/**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
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

	case 'ven001GridProdutoPO':
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T295_Id', 'colunaTitulo' => 'T295_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T295_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T295_Data_Previsao', 'colunaTitulo' => 'Previsão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T295_Ordem_Compra', 'colunaTitulo' => 'PO', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('PO', '/imp/imp005/content/imp005ContentDiIncluir/', 'T295_Id', '990,605')),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30)', 'colunaTitulo' => 'Razão Social'),
            array('colunaValor' => 'T296_Quantidade_Saldo', 'colunaTitulo' => 'Qtde', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo unit', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
        );

		/*
		* Definição da SQL
		*/
		$from = 'T296';
		
		$extra = <<<EOT
			LEFT JOIN T295 ON T295_Id = T296_T295_Id
			LEFT JOIN D024 ON D024_Id = T295_D024_Id
			LEFT JOIN C007 ON C007_Id = T295_C007_Id 
			WHERE T295_C004_Id='{$g['empresaAtual']}'
			  AND T296_D009_Id = '{$r_acaoId}'
			  AND (T295_Flag_Status = '3' OR T295_Flag_Status = '4')
			  AND T296_Quantidade_Saldo>0
			  AND T296_Flag_Finalizar_Compra != 'S'
              AND T295_Data_Previsao>0 
              AND T295_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$g['C031']['diasMedioPrazo']} DAY))			
			GROUP BY T296_Id
EOT;
		
		/**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
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

	case 'ven001GridProdutoNF':
    	/**
		* T008 das notas que foram emitidas de outros C004 para a empresa atual, mas ainda não foram dado entrada.
		*/
		$grid = array(
            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'T008_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'origem.C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtde', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Emissor'),
        );

		/*
		* Definição da SQL
		*/
		$from = 'T008';
		
		$extra = <<<EOT
			LEFT JOIN T007 ON T007_Id = T008_T007_Id
			LEFT JOIN D024 ON D024_Id = T007_D024_Id
			LEFT JOIN C007 ON C007_Id = T007_C007_Id 
		   	LEFT JOIN D009 ON D009_Id = '{$r_acaoId}' 
   			LEFT JOIN D049 ON D049_Id = D009_D049_Id 
   			LEFT JOIN D001 ON D001_Id = D049_D001_Id 
   			LEFT JOIN C004 as destino ON destino.C004_Id = D009_C004_Id 
   			LEFT JOIN C004 as origem ON origem.C004_Id = T007_C004_Id 
          	  WHERE T007_D024_Id=destino.C004_D024_Id 
         		AND T007_C004_Id!=destino.C004_Id 
         		AND T007_Flag_Emitida_Recebida!='R' 
         		AND T007_Flag_Cancelada!='S' 
				AND T007_Numero_Protocolo_NFe!=''
				AND T007_Flag_Entrada_Saida='S'  
         		AND T007_Data_Emissao>=DATE(DATE_ADD(NOW(), INTERVAL -90 DAY))
         		AND T008_Codigo_Produto=D001_Codigo_Produto 
         		AND T007_Numero_Nota_Fiscal NOT IN (SELECT T013_Numero_Nota_Fiscal FROM T013 WHERE T013_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T013_C004_Id=destino.C004_Id)
EOT;
		
		/**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
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

    case 'ven001GridMontagemIPV':

		$sqlT003 = mysql_query("SELECT T003_Id,T003_Valor_Total_Produtos, T003_Flag_Lista_Preco, T003_Valor_Total_ICMS, T003_Valor_Total_IRPJ, T003_Valor_Total_CSLL, T003_Valor_Total_PIS, T003_Valor_Total_COFINS, T003_Valor_Total_Produtos FROM T003 WHERE T003_Id = '{$r_acaoId}'");
		$mT003 = mysql_fetch_array($sqlT003);

		$impostos = 0;
		$impostos += ($g['C031']['somarICMSPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_ICMS'] : 0;
        $impostos += ($g['C031']['somarPISPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_PIS'] : 0;
        $impostos += ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_COFINS'] : 0;
        $impostos += ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_IRPJ'] : 0;
        $impostos += ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_CSLL'] : 0;

        $IPV = mysql_query("select sum(T250_Aliquota) from T250 where T250_T003_Id='{$r_acaoId}' and T250_Grupo='IPV'");
        $mIPV = mysql_fetch_array($IPV);
        $IPV = $mIPV[0]/100;

        $T003_Valor_Total_Produtos = $mT003['T003_Valor_Total_Produtos'] - $impostos;

		$sqlT004 = mysql_query("SELECT SUM(T004_Valor_Custo_Unitario*T004_Quantidade) custo from T004 left join D009 on D009_Id=T004_D009_Id where T004_T003_Id='{$r_acaoId}'");
		$mT004 = mysql_fetch_array($sqlT004);
		$custo = $mT004['custo'];
		if($custo<=0){
			$custo=0;
		}

	
		mysql_query("UPDATE T250 SET T250_Aliquota_Editavel = T250_Aliquota WHERE T250_T003_Id = '{$mT003['T003_Id']}'");
		

    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T250_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
 			array('colunaValor' => 'concat(concat(T250_Descricao," ",if(T250_Flag_Altera_Preco="S","(p)",""))," ",if(T250_Flag_Campo_Calculado="S","(c)",""))', 'colunaTitulo' => 'Descrição'),
 			array('colunaValor' => 'T250_Aliquota', 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'atualizarTotaisIPV', 'colunaTotal' => true, 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);;width: 15%', 'colunaExibir' => false),
 			array('colunaValor' => 'T250_Aliquota_Editavel', 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaTotal' => true, 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);;width: 15%', 'colunaExibir' => true),
			array('colunaValor' => "(if(T250_Grupo='IPV',$T003_Valor_Total_Produtos,$custo)*(T250_Aliquota/100))", 'colunaTitulo' => 'Valor R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width: 15%', 'colunaTotal' => true),
 			array('colunaValor' => 'T250_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
        );		

		//Só aparece as opções do IPV que estão autorizadas para o nível dentro do Comercial->váriaveis IPV de acordo com o nível da pessoa logada(DIRETOR,SUPERVIDOR,VENDEDOR)
		$filtro = "";
		if($g['nivelAlcada']==2){
			$filtro="AND (T250_Nivel_Acesso=2 OR T250_Nivel_Acesso=1)";
		}else if($g['nivelAlcada']==1){
			$filtro="AND T250_Nivel_Acesso=1";
		}

		/*
		* Definição da SQL
		*/

		$from = 'T250';
		
		$extra = <<<EOT
			LEFT JOIN T003 ON T003_Id=T250_T003_Id
			WHERE T250_T003_Id = '{$r_acaoId}'
			{$filtro}
EOT;
		$extraSemLucro = <<<EOT
			LEFT JOIN T003 ON T003_Id=T250_T003_Id
			WHERE T250_T003_Id = '{$r_acaoId}' and ifnull(T250_Tipo,0)!='3' and IFNULL(T250_Tipo,0)!='4'
			AND T250_Grupo='IPV'
			{$filtro}
EOT;
		$extraLucro = <<<EOT
			LEFT JOIN T003 ON T003_Id=T250_T003_Id
			WHERE T250_T003_Id = '{$r_acaoId}' and ifnull(T250_Tipo,0)='3'
			AND T250_Grupo='IPV'
			{$filtro}
EOT;
		$extraICF = <<<EOT
			LEFT JOIN T003 ON T003_Id=T250_T003_Id
			WHERE T250_T003_Id = '{$r_acaoId}' and T250_Grupo='ICF'
			
EOT;
		$extraIPV = <<<EOT
			LEFT JOIN T003 ON T003_Id=T250_T003_Id
			WHERE T250_T003_Id = '{$r_acaoId}' and T250_Grupo='IPV' and ifnull(T250_Tipo,'')!=4
			
EOT;
		$extraTributos = <<<EOT
			LEFT JOIN T003 ON T003_Id=T250_T003_Id
			WHERE T250_T003_Id = '{$r_acaoId}' and T250_Grupo='IPV' and T250_Tipo='4'
			{$filtro}
EOT;


		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => 
				array(
	            	array('title' => 'Grupo', 'campoAgrupar' => 'T250_Grupo', 'campoMostrar' => 'T250_Grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
	            ),
	        ),
        );

		// Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
        * Totais
        */
        $tributos    = $g['sqlAuto']->pegarSqlCampo("{$impostos}", $from, $extra);
        // $ipvSemLucro = $g['sqlAuto']->pegarSqlCampo("SUM((if(T250_Grupo='IPV',$T003_Valor_Total_Produtos,$custo)*(T250_Aliquota/100)))", $from, $extraSemLucro);
        $ipvSemLucro = $g['sqlAuto']->pegarSqlCampo("SUM((if(T250_Grupo='IPV',$T003_Valor_Total_Produtos,$custo)*(T250_Aliquota/100)))", $from, $extraSemLucro);
        $ipvLucro    = $g['sqlAuto']->pegarSqlCampo("SUM((if(T250_Grupo='IPV',$T003_Valor_Total_Produtos,$custo)*(T250_Aliquota/100)))", $from, $extraLucro);
        $aliquotasIPV= $g['sqlAuto']->pegarSqlCampo("sum(T250_Aliquota)", $from, $extraIPV );
        $aliquotasIPVSemLucro= $g['sqlAuto']->pegarSqlCampo("sum(T250_Aliquota)", $from, $extraSemLucro );
        $IPVTributos = $g['sqlAuto']->pegarSqlCampo("sum(T250_Aliquota)", $from, $extraTributos );
        $ICF 		 = $g['sqlAuto']->pegarSqlCampo("(100/(100-SUM(T250_Aliquota))) AS IPV", $from, $extraICF);

		if ($IPVTributos<=0){
			$IPVTributos = ($tributos / $mT003['T003_Valor_Total_Produtos']) * 100;
		}

		
		$IPV         = round((100 / ( 100 - $aliquotasIPV )) , 4 );
		$IPVSL       = round((100 / ( 100 - $aliquotasIPVSemLucro )) , 4 );
        $total       = $T003_Valor_Total_Produtos + $tributos;
        $valorLucro  = $T003_Valor_Total_Produtos - $custo - $ipvSemLucro;
		$valorMinimo = ( $custo * $IPV );
		$valorMinimoSL = ( $custo * $IPVSL );


		$IPV         = gCorrigeNumero( $IPV, 4 );
		$IPVTributos = gCorrigeNumero( (100 / ( 100 - $IPVTributos )) , 4 );
		$ICF 		 = gCorrigeNumero($ICF, 4);

		$tributos        = gCorrigeNumero($tributos);
        $ipvSemLucro     = gCorrigeNumero($ipvSemLucro);
		$percentualLucro = gCorrigeNumero(($valorLucro/$T003_Valor_Total_Produtos)*100);
        $custo           = gCorrigeNumero($custo);
        $valorLucro 	 = gCorrigeNumero($valorLucro);
		$total           = gCorrigeNumero($total);
		$valorMinimo     = gCorrigeNumero($valorMinimo);
		$valorMinimoSL   = gCorrigeNumero($valorMinimoSL);
		$T003_Valor_Total_Produtos = gCorrigeNumero($T003_Valor_Total_Produtos);

		echo <<<EOT
	            <div class="buttonsBottomTop">
	            	
	            	
EOT;

		echo <<<EOT
				<span class="item" style="text-align:right; float: left;">ICF<br><input type="text" value="{$ICF}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right; float: left;">IPV<br><input type="text" value="{$IPV}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizarTotaisIPV(T250_Id, campoEditado, valorAntigo, novoValor) {
					divRefresh('{$g['divId']}');
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

    case 'ven001GridProdutosSimples':
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		
		/**
		* Definição das Tabelas
		*/
		$from = 'T004';
		$fromParent = 'T003_Id';
		$fromParentValor = $r_T003_Id;

		$sql = "SELECT T003_Casas_Decimais_Preco FROM T003 WHERE T003_Id = '{$r_T003_Id}'";
		$resT003 = mysql_query($sql);
		$rowT003 = mysql_fetch_assoc($resT003);
		
		$colunaPrecoLiquido = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoBruto = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? true : false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T219_Id', 'colunaTitulo' => 'T004_T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Item'),
            array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T004_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-size:12px'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto'),
            array('colunaValor' => 'T004_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => false),
			array('colunaValor' => 'T004_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $rowT003['T003_Casas_Decimais_Preco'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T004_Percentual_Desconto', 'colunaExibir' => $colunaPrecoLiquido),
            array('colunaValor' => 'T004_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoLiquido),
			array('colunaValor' => 'T004_Valor_Preco_Bruto', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowT003['T003_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'T004_Calculo_Valor_Unitario', 'colunaExibir' => $colunaPrecoBruto),
			array('colunaValor' => 'T004_Valor_Preco_Bruto*T004_Quantidade as T004_Valor_Total_Bruto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoBruto),
        );
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T003 on T003_Id=T004_T003_Id
			LEFT JOIN D009 on D009_Id=T004_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T004_D037_Id
			LEFT JOIN D005 on D005_Id=T004_D005_Id
			LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=T003_C004_Id
			LEFT JOIN D006 on D006_Id=T004_D006_Id
			LEFT JOIN T006 on T006_T004_Id=T004_Id
			LEFT JOIN T066 on T066_D009_Id=D009_Id
			LEFT JOIN D004 on D004_Id=T066_D004_Id
			LEFT JOIN T219 on T004_Id=T219_T004_Id
			LEFT JOIN D069 on D069_Id=D009_D069_Id AND D069_Flag_Tabela_Principal='S'
			LEFT JOIN D070 on D070_Id=D069_D070_Id
			WHERE T004_T003_Id = '{$r_acaoId}'
			group by T004_Id

EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S' or $array['todosCampos']['T004_Valor_Preco_Unitario']<=0) {
			    return 'linhaVermelho';
			}
			if($array['todosCampos']['T006_T005_Id']>0){
				return 'linhaCinza';
			}
		}

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T004_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/ven/ven001/content/ven001ContentEditarProduto/', '&acaoId=' + encodeURIComponent(acaoId) + '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&tabela=T004', [600,400]);
			});
		} </script></div>
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

	case 'ven001GridIncluirItemMontado':
		$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		$sqlD009 = mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id = '{$D009_Id}'");
		$rowD009 = mysql_fetch_array($sqlD009);
		$sqlT003 = mysql_query("SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$T003_Id}'");
		$rowT003 = mysql_fetch_array($sqlT003);

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'C008_Id', 'colunaTitulo' => 'Dep', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'D060_Id', 'colunaTitulo' => 'Setor', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'D042_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
			array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
			array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'white-space:normal;'),
			array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referência', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('colunaValor' => 'substr(D082_Marca,1,15) as D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'style' => 'width:1%'),
			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'style' => 'width:1%'),
			array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Preco_2', 'colunaTitulo' => 'Preço 2', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Preco_3', 'colunaTitulo' => 'Preço 3', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
			array('colunaValor' => '(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque', 'colunaTitulo' => 'Loja', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
			array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Locação', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => 2, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'callbackParameter' => array($r_T003_Id)),
			array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => '"" as retornaPrecoVenda', 'colunaTitulo' => 'Preço', 'colunaTipo' => 'livre', 'callback' => 'retornaPrecoVenda', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
			array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
			array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação', 'style' => 'width:1%'),
			array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'style' => 'width:1%'),
			array('colunaValor' => 'D001_Especificacoes', 'colunaTitulo' => 'Observações', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Descricao_Ingles', 'colunaTitulo' => 'Descrição Inglês', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'D001_Flag_Pre_Cadastro', 'colunaExibir' => false),
			array('colunaValor' => 'D144_Id', 'colunaTitulo' => 'D144_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Setor', 'colunaExibir' => false),
			array('colunaValor' => '"' . $T003_Id . '" as T003_Id', 'colunaTitulo' => 'T003_Id', 'colunaTitulo' => 'T003_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaExibir' => false)
        );

		$from = "D001";
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D041 ON D041_D049_Id=D049_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN D042 ON D042_D049_Id_Itens=D049_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D037 ON D037_Id=D001_D037_Id
			LEFT JOIN D060 ON D060_Id=D001_D060_Id
			LEFT JOIN D003 ON D003_Id=D001_D003_Id
			LEFT JOIN D002 ON D002_Id=D001_D002_Id
			LEFT JOIN C008 ON C008_Id=D001_C008_Id
			LEFT JOIN D005 ON D005_Id=D001_D005_Id
			LEFT JOIN T066 ON T066_D009_Id=D009_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id='{$rowT003['T003_D024_Id']}'
			LEFT JOIN D144 ON D144_D049_Id=D049_Id
			LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprador
			where D042_D049_Id = '{$rowD009['D009_D049_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'
			GROUP BY D009_Id
			ORDER BY D001_Codigo_Produto ASC

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
}
