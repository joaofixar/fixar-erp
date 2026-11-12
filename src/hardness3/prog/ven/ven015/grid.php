<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/CAD001.php');
//$CAD001 = new CAD001();
// Carrega funções callback
include_once('prog/ven/ven003/grid_func-callback.php');
include_once('prog/ven/ven015/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven003/grid_func-js.php');


switch($g['r'][3]){
    	/**
		* Definição do Grid (Tabela)
	    */

      case 'ven015GridProdutosPDV':
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
        $sqlT007 = mysql_query("SELECT T007_Numero_Protocolo_Nfe, T007_Casas_Decimais_Preco,T007_Casas_Decimais_Quantidade FROM T007 WHERE T007_Id = '{$acao_Id}'");
        $mT007 = mysql_fetch_array($sqlT007);
        if(empty($mT007['T007_Numero_Protocolo_Nfe'])) {
            $inclusaoMultiProdutos=array('T008_T007_Id'=>$r_T007_Id);
        }

        $grid = array(
            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId'], false, false, "{$r_divReload}")),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProdutoPDV', 'style' => 'width:1px'),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'style' => 'width:0px', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T008_D009_Id', 'colunaTitulo' => 'T008_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Quantidade'], 'colunaEditar' => true, 'style' => 'font-weight:bold;background:#b1cbe0', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T008_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto'),
            array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => false, 'style' => 'font-weight:bold;background:#b1cbe0', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto'),
            array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'DAV', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_T007_Id', 'colunaTitulo' => 'T008_T007_Id', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T008";
		
		$extra = <<<EOT
        -- JOIN
            left join T007 on T008_T007_Id = T007_Id
            left join D024 on T007_D024_Id = D024_Id
            left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D049_D082_Id = D082_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D037 on D037_Id=T008_D037_Id
            left join T006 on T006_Id=T008_T006_Id
            left join D006 on D006_Id=T008_D006_Id
        -- WHERE
            where T008_T007_Id = '{$acao_Id}'
        -- GROUP BY
            group by T008_Id
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
                    function colunaEditarCallbackJSReload(T008_Id, campoEditado, valorAntigo, novoValor) {
                        T008_Id = T008_Id.replace(/T008_Id-/, '');
                        $.getJSON('/ven/ven015/grid_func-ajax/ven015VerificaAlterarItem/?ajax=true&T008_Id='      + encodeURIComponent(T008_Id) + 
                                                                                                 '&campoEditado=' + encodeURIComponent(campoEditado) +
                                                                                                 '&valorAntigo='  + encodeURIComponent(valorAntigo) +
                                                                                                 '&novoValor='    + encodeURIComponent(novoValor) +  
                                                                                                 '&callback=?', function(request) {
                            if(request.code){
                               divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id');
                               divRefresh('{$r_divReload}');

                            } else{
                                divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id'); // grid
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    }
                    function colunaEditarCallbackJS_T008_Percentual_Desconto(T008_Id, campoEditado, valorAntigo, novoValor) {
                    T008_Id = T008_Id.replace(/T008_Id-/, '');
                    if(campoEditado == 'T008_Percentual_Desconto'){
                        $.getJSON('/ven/ven003/grid_func-ajax/ven003aplicarPercentualDesconto/?ajax=true&T008_Id='    + encodeURIComponent(T008_Id) + 
                                                                                          '&campoEditado='      + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='       + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='         + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id'); // grid
                                divRefresh('{$r_divReload}');
                                //alert("NFe Excluída");
                            } else {
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    } else if(campoEditado == 'T008_Valor_Preco_Sem_Desconto_Unitario'){
                        $.getJSON('/ven/ven003/grid_func-ajax/ven003aplicarPercentualDesconto/?ajax=true&T008_Id='    + encodeURIComponent(T008_Id) + 
                                                                                          '&campoEditado='      + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='       + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='         + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id'); // grid
                                divRefresh('{$r_divReload}');
                            } else {
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    }
                    divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
                }

			} </script></div>
EOT;

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Copiado do sistema.js pesquisaGridTeclado para editar a primeira coluna editável ao teclar ENTER
                    // mesma função da tecla E 
                    linhaSelecionada = $('#{$g['divId']} .grid').find('.linhaSelecionada');
                    if (linhaSelecionada.size() > 0) {
                        linhaSelecionada.find('.campoEditavel:first').trigger('click');
                        event.stopPropagation();
                        event.preventDefault();
                    }
            });
            } </script></div>
EOT;

		$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe FROM T007 WHERE T007_Id='{$r_T007_Id}'";
		$resultT007 = mysql_query($sqlT007);
		$linhaT007 = mysql_fetch_array($resultT007);
		if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
			$disabled = "disabled='disabled'";
		}else{
			$disabled = "";
		}
        $extraCount = gRetirarGroupBy($extra);
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(DISTINCT T008_Id)", $from, $extraCount);

		/**
         * Botões 
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
			
		
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

    case 'ven015GridPagamentosPDV':
        $acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T292_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            array('colunaValor' => 'T292_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => false, 'style' => 'width:60%', 'callback' => 'T292_D027_Id_Portador'),
            array('colunaValor' => 'T292_Valor_Recebido', 'colunaTitulo' => 'Valor Recebido', 'colunaEditar' => true, 'style' => 'width:25%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T292_Prazos_Dias"),
            array('colunaValor' => 'T292_Quantidade_Parcelas', 'colunaTitulo' => 'Parcelas', 'colunaEditar' => true, 'style' => 'width:5%', 'colunaAlinhar' => 'center', 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T292_Prazos_Dias"),
            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
        );

        /**
        *
        * Definição da SQL
        */
        $from = 'T292';

        $extra = <<<EOT
            left join D027 on D027_Id=T292_D027_Id
            WHERE T292_T007_Id = {$acao_Id}
            ORDER BY D027_Portador ASC
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
            // Função JS executada como callback do editar campo (colunaEditarJSCallback)
            function colunaEditarCallbackJS_T292_Prazos_Dias(T292_Id, campoEditado, valorAntigo, novoValor) {
                  divRefresh('{$r_divReload}');
//                  T292_Id = T292_Id.replace(/T292_Id-/, '');
//                  setTimeout(function() {
//                      divRefresh('{$g['divId']}', true, false, false, T292_Id, 'T292_Id'); // grid
//                  }, 150 );
            }
        } </script></div>
EOT;

    echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            // Primeira coluna editável ao teclar ENTER
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                eventosTeclado('Alt_E', data);
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

    case 'ven015GridDiversos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirNFe', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30) as D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(CONVERT(substr(Pedido.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(Pedido.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(Pedido.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:170px;white-space:normal;min-width:170px;width:170px'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Prazos(T007_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'max-width:300px;white-space:normal;min-width:200px;width:200px;word-wrap:break-word;', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Total_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Percentual_Margem', 'colunaTitulo' => 'Margem', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4', 'callback' => 'gPorcentagem'),
            array('colunaValor' => 'T007_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => ',width:1%', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInternoNF', 'colunaEditarNivel' => 4, 'style' => 'width:1%'),
            array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDanfe', 'style' => 'width:1%'),
            //array('colunaValor' => 'NF', 'colunaTitulo' => 'NF', 'colunaTipo' => 'livre', 'callback' => 'validaCampoNF', 'style' => 'width:1%'),
            array('colunaValor' => 'ACP', 'colunaTitulo' => 'ACP', 'colunaTipo' => 'livre', 'callback' => 'validaCampoACP', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar'),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'eMail', 'colunaTipo' => 'Livre', 'callback' => 'validaCampoEmail', 'style' => 'width:1%'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Marcas', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'Retorno Sefaz', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Retorno SEFAZ', 'callback' => 'chamaCampo'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
            array('colunaValor' => 'T007_Numero_Protocolo_Cancelamento_Nfe', 'colunaTitulo' => 'Prot. Cancel'),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'callback' => 'ValidaChaveAcesso'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Observacao_Cancelamento', 'colunaTitulo' => 'Observação Cancelamento', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => 'Impressão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML as Data_Envio_XML', 'colunaTitulo' => 'XML', 'colunaExibir' => false),
            array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir' => false),
        );
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}
		
		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
        -- JOIN
			left join T005 on T005_Id=T007_T005_Id
			left join D013 as Pedido on Pedido.D013_Id=T005_D013_Id
		    left join D024 on D024_Id=T007_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T007_D006_Id
			left join C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
		    left join D021 on D021_Id=D024_D021_Id
        -- WHERE
		    where (T007_C004_Id='{$g['empresaAtual']}'
            and T007_Flag_PDV = 'S'
		    and T007_Flag_Emitida_Recebida!='R')
			{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
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
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Nota Fiscal', 'tipo' => 'texto'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            //'Pedido.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            //'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 1', 'tipo' => 'texto'),
            //'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ),
            'naoGerarExtra' => ''),
            'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T007_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo', 'tipo' => 'text', 'exibir' => false),
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
            array('nome' => 'Valor_ICMS_', 'valor' => 'T007_Valor_Total_ICMS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_PIS_', 'valor' => 'T007_Valor_Total_PIS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_COFINS_', 'valor' => 'T007_Valor_Total_COFINS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_IRPJ_', 'valor' => 'T007_Valor_Total_IRPJ', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_CSLL_', 'valor' => 'T007_Valor_Total_CSLL', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_Produtos', 'valor' => 'T007_Valor_Total_Produtos', 'titulo' => 'Produtos R$','exibir' => false),
            array('nome' => 'Valor_IPI', 'valor' => 'T007_Valor_Total_IPI', 'titulo' => 'IPI R$','exibir' => false),
            array('nome' => 'Valor_ST', 'valor' => 'T007_Valor_ICMS_Substituicao_Retencao', 'titulo' => 'ST R$','exibir' => false),
            array('nome' => 'Valor_Desconto', 'valor' => 'T007_Valor_Desconto', 'titulo' => 'Desconto R$','exibir' => false),
            array('nome' => 'Valor_Despesas', 'valor' => 'T007_Valor_Despesas_Acessorias', 'titulo' => 'Desp.Assec. R$','exibir' => false),
            array('nome' => 'Valor_Frete', 'valor' => 'T007_Valor_Frete', 'titulo' => 'Frete R$','exibir' => false),
            array('nome' => 'Valor_Total', 'valor' => 'T007_Valor_Total', 'titulo' => 'Total R$'),
            array('nome' => 'Valor_Custo', 'valor' => 'T007_Valor_Total_Custo', 'titulo' => 'Custo R$','exibir' => false),
            array('nome' => 'Valor_Comissao', 'valor' => 'T007_Valor_Total_Comissao', 'titulo' => 'Comissão R$','exibir' => false),
            array('nome' => 'Percentual_Comissao', 'tipo' => 'totais', 'valor' => '({Valor_Comissao}/{Valor_Produtos})*100', 'titulo' => '% Comissão','exibir' => false),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('PDV'), '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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
