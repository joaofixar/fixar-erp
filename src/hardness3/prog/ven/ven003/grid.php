<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/CAD001.php');
//$CAD001 = new CAD001();
// Carrega funções callback
include_once('prog/ven/ven003/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven003/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
	*/
	case 'ven003GridPrincipal':
        $filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
        if($g['c029Ids'][242]){
            $habilitaCampo = true;
        }else{
            $habilitaCampo = false;
        }

		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirNFe'),
            array('colunaValor' => 'imprimirMinutaDeDespacho', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Minuta', 'callback' => 'imprimirMinutaDeDespacho', 'style' => 'width:30px; padding: 3px 10px 3px 20px;', 'colunaAlinhar' => 'center',),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/notafiscal/', 'T007_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 330), 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'style' => 'width:1%;font-size:10px', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => '', 'callback' => 'campoMarcarNFImpressao', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'M|S'),
            array('colunaValor' => 'if(T007_Flag_ACP!="3",LPAD(T007_Numero_Nota_Fiscal,7,"0"),T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'T007_Numero_Nota_Fiscal', 'colunaExibir' => false),
            array('colunaValor' => 'T276_Flag_Cancelada', 'colunaTitulo' => 'T276_Flag_Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'T276_C004_Id', 'colunaTitulo' => 'T276_C004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T276_Numero_Protocolo_MDFe', 'colunaTitulo' => 'T276_Numero_Protocolo_MDFe', 'colunaExibir' => false),
            array('colunaValor' => 'T276_Numero_MDFe', 'colunaTitulo' => 'MDF-e', 'colunaAlinhar' => 'left','colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo'),
            array('colunaValor' => 'T007_Serie', 'colunaTitulo' => 'Série', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Numero_Pedido_Venda', 'colunaTitulo' => 'N Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T007_Flag_Marcado', 'colunaTitulo' => 'MDF-e', 'callback' => 'validaFlagNotaMarcada', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'N|S','colunaExibir' => $habilitaCampo,'style' => '','colunaAlinhar' => 'center',),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'T007_D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razao Social'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Cpf', 'colunaTitulo' => 'CPF', 'colunaAlinhar' => 'left', 'style' => 'width: 1%'),
            array('colunaValor' => 'concat(CONVERT(substr(Pedido.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(Pedido.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(Pedido.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:170px;white-space:normal;min-width:170px;width:170px'),
            array('colunaValor' => 'UFCliente.D018_UF', 'colunaTitulo' => 'UF', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'UFClienteEntrega.D018_UF', 'colunaTitulo' => 'UF Entrega', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T007_Prazos(T007_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'max-width:300px;white-space:normal;min-width:200px;width:200px;word-wrap:break-word;', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T007_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'Mail', 'callback' => 'validaCampoEmail'),
            array('colunaValor' => 'T007_Data_Envio_Email', 'colunaTitulo' => 'Data Envio Email', 'colunaExibir' => false),
            array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDanfe'),
            array('colunaValor' => 'NF', 'colunaTitulo' => 'NF', 'colunaTipo' => 'livre', 'callback' => 'validaCampoNF', 'colunaExibir' => false),
            array('colunaValor' => 'ACP', 'colunaTitulo' => 'ACP', 'colunaTipo' => 'livre', 'callback' => 'validaCampoACP', 'colunaExibir' => true),
            array('colunaValor' => 'CCe', 'colunaTitulo' => 'CC-e', 'colunaTipo' => 'livre', 'callback' => 'enviarCCe'),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar', 'style'=>'max-width:350px;min-width:350px;width:350px;white-space:normal;'),
            array('colunaValor' => 'Damdfe', 'colunaTitulo' => 'Damdfe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDamdfe','colunaExibir' => $habilitaCampo),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaDimensao' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInternoNF', 'colunaEditarNivel' => 4, 'style' => 'width:1%;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C007_Id_Vendedor_Externo', 'colunaTitulo' => 'Vendedor Ext', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorExterno', 'colunaEditarNivel' => 4, 'style' => 'width:1%;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'Externo.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor Ext', 'colunaDimensao' => 'S', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Percentual_Comissao', 'colunaTitulo' => '%Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'colunaEditarOculto' => (isset($g['c029Ids'][201])) ? true : false, 'style' => 'background:var(--celula-amarelo-claro)', 'colunaEditarJSCallback' => 'colunaEditarPercentualComissao'),
            array('colunaValor' => 'T007_Valor_Total_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T007_D021_Id', 'colunaTitulo' => 'Representante', 'colunaEditarGeraSelect' => array('D021', 'D021_Id', 'D021_Nome_Empresa','',true), 'colunaEditarOculto' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D021_Nome_Empresa', 'colunaEditarJSCallback' => "colunaEditarRepresentante", 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'T007_Percentual_Comissao_Representante', 'colunaTitulo' => '%Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'colunaEditarOculto' => (isset($g['c029Ids'][202])) ? true : false, 'style' => 'background:var(--celula-verde)', 'colunaEditarJSCallback' => 'colunaEditarPercentualComissao'),
            array('colunaValor' => 'T007_Valor_Comissao_Representante', 'colunaTitulo' => 'Comissão R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right','colunaExibir' => true, 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'T007_Valor_Total_ICMS', 'colunaTitulo' => 'ICM', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Valor_Total_PIS', 'colunaTitulo' => 'PIS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Valor_Total_COFINS', 'colunaTitulo' => 'COFINS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007A_Valor_Total_PIS_Retido', 'colunaTitulo' => 'PIS Ret.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007A_Valor_Total_COFINS_Retido', 'colunaTitulo' => 'COFINS Ret.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4),
            //array('colunaValor' => 'Retorno Sefaz', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Retorno SEFAZ', 'callback' => 'chamaCampo'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
            array('colunaValor' => 'T007_Numero_Protocolo_Cancelamento_Nfe', 'colunaTitulo' => 'Prot. Cancel', 'callback' => 'validaCampoXMLCancelado'),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'callback' => 'ValidaChaveAcesso'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Desconto', 'colunaTitulo' => 'Desc.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Nome_Status as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir' => false),
            //array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'XML', 'colunaExibir' => false),
            //array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            //array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Protocolo_Inutilizacao', 'colunaTitulo' => 'Prot.Inutilizacao', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Observacao_Cancelamento', 'colunaTitulo' => 'Motivo Cancelamento', 'colunaExibir' => false),
            array('colunaValor' => 'UFFaturamento.D018_UF', 'colunaTitulo' => 'UF Faturamento', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Data_Entrega', 'colunaTitulo' => 'Data Entrega', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Cliente_Endereco', 'colunaTitulo' => 'Rua', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T007_Cliente_Endereco_Numero', 'colunaTitulo' => 'Número', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T007_Cliente_Bairro', 'colunaTitulo' => 'Bairro', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T007_Cliente_CEP', 'colunaTitulo' => 'CEP', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T007A_Id', 'colunaTitulo' => '',  'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007A_Flag_Contingencia_Offline', 'colunaExibir' => false),
            /*            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
            FROM T008 
            LEFT JOIN D009 ON D009_Id=T008_D009_Id 
            LEFT JOIN D049 ON D049_Id=D009_D049_Id 
            LEFT JOIN D001 ON D001_Id=D049_D001_Id 
            LEFT JOIN C008 ON C008_Id=D001_C008_Id 
            WHERE T008_T007_Id=T007_Id) as Tipos', 'colunaTitulo' => 'Tipos'),
            array('colunaValor' => '(SELECT SUM(T006_Valor_Desconto_Unitario_Bruto)
            FROM T006 
            LEFT JOIN T005 ON T006_T005_Id = T005_Id 
            WHERE T005_Id = T007_T005_Id) as desconto', 'colunaTitulo' => 'Desc.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaTipo' => 'banco', 'colunaExibir' => false),
            */
            /* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 19/09/2025
            ==============================================================*/
            array('colunaValor' => 'T007A_Valor_Total_Reforma', 'colunaTitulo' => 'Total RT', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_CBS', 'colunaTitulo' => 'CBS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_Diferimento_CBS', 'colunaTitulo' => 'Dif. CBS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_IBS_UF', 'colunaTitulo' => 'IBS UF', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_Diferimento_IBS_UF', 'colunaTitulo' => 'Dif. IBS UF', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_IBS_Municipal', 'colunaTitulo' => 'IBS Mun.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_Diferimento_IBS_Municipal', 'colunaTitulo' => 'Dif. IBS Mun.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_IBS', 'colunaTitulo' => 'IBS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T007A_Valor_Total_Base_CBS_IBS', 'colunaTitulo' => 'Base CBS/IBS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibirNivel' => 4, 'style' => 'background:var(--celula-azul)'),
            /*==============================================================*/
        );

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}

            if($array['todosCampos']['T276_Flag_Cancelada']=='S'){
                return 'linhaRed';
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
		    left join D018 as UFCliente on UFCliente.D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T007_D006_Id
            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
            left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
            left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
            left join C007 as Externo on Externo.C007_Id=T007_C007_Id_Vendedor_Externo
            left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
		    left join D021 on D021_Id=T007_D021_Id
		    left join D022 on D022_Id=T007_D022_Id
            left join D018 as UFFaturamento on UFFaturamento.D018_Id=D022_D018_Id
            left join D148 on D148_Id=T007_D148_Id_Entrega
            left join D018 as UFClienteEntrega on UFClienteEntrega.D018_Id=D148_D018_Id
            left join T284 on T007_Id=T284_T007_Id
            left join T276 on T276_Id=T284_T276_Id
            left join T007A on T007_Id = T007A_T007_Id
        -- WHERE
		    where T007_C004_Id='{$g['empresaAtual']}'
EOT;

        // and T007_Flag_Emitida_Recebida!='R'

		/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven003IncluirNotaFiscal">Incluir</button>
                <button type="button" id="ven003IncluirNotaFiscalConsumidor">PDV</button>
                <button type="button" id="ven003ImprimirNFeMarcada">Imprimir Marcadas</button>
                <button type="button" id="ven003BaixarXMLNFEMarcada">Baixar XML Marcadas</button>
EOT;
        if($habilitaCampo){
        echo <<<EOT
                <button type="button" id="ven003GerarMdfe">Gerar MDF-e</button>
EOT;
        }  
        echo <<<EOT
                <!--<button type="button" id="ven003MarcarTodos">Marcar Todos</button>-->
                <!--<button type="button" id="ven003DesmarcarTodos">Desmarcar Todos</button>-->
                <!--<button type="button" id="ven003Inutilizar">Inutilizar Numeração</button>-->
                <!--<button type="button" id="ven003ImportarXML">Importar XML</button>-->
                <!--<button type="button" id="ven003GerarLoteRps">Gerar Lote Rps</button>-->
            </div>
EOT;
        */


		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'Pedido.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
            'T276_Numero_MDFe' => array('titulo' => 'Número MDF-e', 'tipo' => 'texto'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'UFCliente.D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D024_Cnpj' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Origem', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ),
              'naoGerarExtra' => ''),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),
            'T007_Flag_Marcado' => array('titulo' => 'Gerar MDFe', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Tipo produto', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Marca produto', 'tipo' => 'texto'),
            'T007_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo', 'tipo' => 'text', 'exibir' => false),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'NF-e', 'value' => '6'),
                array('title' => 'NFC-e', 'value' => '8'),
                array('title' => 'CF', 'value' => '4'),
                array('title' => 'ACP', 'value' => '3')
              ),
            ),
            'T007_Prazos(T007_Id)' => array('titulo' => 'Prazo', 'tipo' => 'texto'),
            'T007_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda'),
            'T007_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda'),
            'T005_Ordem_Compra' => array('titulo' => 'OC. Cliente', 'tipo' => 'texto'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'T007_Data_Entrega' => array('titulo' => 'Data Entrega', 'tipo' => 'data', 'exibir' => false),
        );

        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extra_ = base64_encode($extra);

		// Monta fórmula total produtos sem impostos como base do cálculo da Margem
		$baseMargem  = "({Valor_Produtos}";
		$baseMargem .= ($g['C031']['somarICMSPrecoVenda']   == 'S') ? "-{Valor_ICMS_}"   : "" ;
		$baseMargem .= ($g['C031']['somarPISPrecoVenda']    == 'S') ? "-{Valor_PIS_}"    : "" ;
		$baseMargem .= ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? "-{Valor_COFINS_}" : "" ;
		$baseMargem .= ($g['C031']['somarIRPJPrecoVenda']   == 'S') ? "-{Valor_IRPJ_}"   : "" ;
		$baseMargem .= ($g['C031']['somarCSLLPrecoVenda']   == 'S') ? "-{Valor_CSLL_}"   : "" ;
		$baseMargem .= ")";

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
            array('nome' => 'Valor_Produtos', 'valor' => 'T007_Valor_Total_Produtos', 'titulo' => 'Produtos R$'),
            array('nome' => 'Valor_IPI', 'valor' => 'T007_Valor_Total_IPI', 'titulo' => 'IPI R$'),
            array('nome' => 'Valor_ST', 'valor' => 'T007_Valor_ICMS_Substituicao_Retencao', 'titulo' => 'ST R$'),
            array('nome' => 'Valor_Desconto', 'valor' => 'T007_Valor_Desconto', 'titulo' => 'Desconto R$'),
            array('nome' => 'Valor_Despesas', 'valor' => 'T007_Valor_Despesas_Acessorias', 'titulo' => 'Desp.Assec. R$'),
            array('nome' => 'Valor_Frete', 'valor' => 'T007_Valor_Frete', 'titulo' => 'Frete R$'),
            array('nome' => 'Valor_Total', 'valor' => 'T007_Valor_Total', 'titulo' => 'Total R$'),
            array('nome' => 'Valor_Custo', 'valor' => 'T007_Valor_Total_Custo', 'titulo' => 'Custo R$'),
            array('nome' => 'Percentual_Margem', 'tipo' => 'totais', 'valor' => '(('.$baseMargem.' - {Valor_Custo}) / '.$baseMargem.') * 100', 'titulo' => '% Margem'),
            array('nome' => 'IPV', 'tipo' => 'totais', 'valor' => '('.$baseMargem.'/{Valor_Custo})', '1,' => 4, 'titulo' => 'IPV', 'exibir' => true),
            array('nome' => 'Valor_Comissao', 'valor' => 'T007_Valor_Total_Comissao', 'titulo' => 'Comissão R$'),
            array('nome' => 'Percentual_Comissao', 'tipo' => 'totais', 'valor' => '({Valor_Comissao}/{Valor_Produtos})*100', 'titulo' => '% Comissão'),
        );
        
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sqlGridFiltro = base64_encode($geradoSql);
        
        //Botões
        $botoes = array(
            array('titulo' => 'Incluir', 'id' => 'ven003IncluirNotaFiscal', 'janelaUrl' => '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'janelaTamanho' => '[900,550]'),
            array('titulo' => 'PDV', 'id' => 'ven003IncluirNotaFiscalConsumidor'),
            array('titulo' => 'Imprimir Marcadas', 'id' => 'ven003ImprimirNFeMarcada'),
            array('titulo' => 'Baixar XML Marcadas', 'id' => 'ven003BaixarXMLNFEMarcada'),
            array('titulo' => 'Gerar MDF-e', 'id' => 'ven003GerarMdfe', 'exibirCallback' => 'return $g["c029Ids"][242];'),
        );
        echo Botoes::processar($botoes);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
                $('#ven003GerarMdfe').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/fis/fis012/grid_func-ajax/gerarMDFeCabecalho/?ajax=true&callback=?', function(request) {
                        if (request.code) {
                            $('#{$g['divId']}').hideLoading();
                            var T276_Id=request.T276_Id;
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar MDF-e - ID: ' + T276_Id, '/fis/fis012/content/fis012MdfeCadastro/', '&tab=geral&acaoId=' + encodeURIComponent(T276_Id), [1000,550]);
                            divRefresh('{$g['divIdParent']}', true);
                            divRefresh('{$g['divId']}', true);
                        } else {
                            $('#{$g['divId']}').hideLoading();
                            dialogConfirm('Erro', request.data);
                        }                                           
                    });
                });

                $('#ven003BaixarXMLNFEMarcada').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/grid_func-ajax/BaixarXMLNFEMarcada/?ajax=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
								if (request.code) {
                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									window.location = request.data;
								    divRefresh('{$g['divId']}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Baixar', 'Baixar XMLs das NF-e marcadas em formato zip? ', \$buttons);
                });

				$('#ven003DesmarcarTodos').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven003/grid_func-ajax/ven003checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					});
				});
				$('#ven003MarcarTodos').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven003/grid_func-ajax/ven003checkMarcarTodos/?ajax=true&sqlFiltro='+encodeURIComponent('{$sqlGridFiltro}')+'&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}											
					});
				});
                $('#xven003IncluirNotaFiscal').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','', [900,550]);
                });

                $('#ven003ImprimirNFeMarcada').unbind('click').bind('click', function(data) {
                    nova_janela('/ven/ven003/grid_func-ajax/ven003ImprimirMarcadas/?ajax=true&extra=' + encodeURIComponent('{$extra_}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao Danfe');
                });
                $('#ven003Inutilizar').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Inutilizar Numeração', '/ven/ven003/content/ven003ContentInutilizarNumeracao/','', [500,450]);
                });
                $('#ven003ImportarXML').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Inutilizar Numeração', '/ven/ven003/content/ven003ContentImportarXML/','', [500,120]);
                });
                $('#ven003GerarLoteRps').unbind('click').bind('click', function(data) {
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003gerarLoteRps/?ajax=true&callback=?', function(request) {
                        if (request.code) {
                            dialogConfirm('Operação Ok', request.data);
                            divRefresh('{$g['divId']}', true);
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                });
                
                $('#ven003IncluirNotaFiscalConsumidor').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003incluirNotaFiscalConsumidor/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code) { 
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('PDV'), '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','&acaoId=' + encodeURIComponent(request.data), [1000,650]);
                        } else {
                            dialogConfirm('Erro', request.data, false);
                        }
                    });
                });

                function colunaEditarRepresentante(T007_Id, campoEditado, valorAntigo, novoValor) {
                  T007_Id = T007_Id.replace(/T007_Id-/, '');
                  $('#{$g['divId']}').showLoading();
                  $.getJSON('/ven/ven001/grid_func-ajax/alterarRepresentante/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + 
                                              '&campoEditado='    + encodeURIComponent(campoEditado) +
                                              '&valorAntigo='     + encodeURIComponent(valorAntigo) +
                                              '&D021_Id='       + encodeURIComponent(novoValor) +  
                                              '&callback=?', function(request) {
                    $('#{$g['divId']}').hideLoading();
                    if (request.code) {
                      divRefresh('{$g['divId']}', true);
                    } else {
                      dialogConfirm('Erro', request.data);
                    }
                  });
                }
                function colunaEditarPercentualComissao(T007_Id, campoEditado, valorAntigo, novoValor) {
                    T007_Id = T007_Id.replace(/T007_Id-/, '');
                    divRefresh('{$g['divId']}', true, false, false, T007_Id, 'T007_Id'); // grid
                }
			} </script></div>
EOT;
        }

		/**
		* Botões (Versão Acima do GRID)
		*/
/*          $IPVTotal = $g['sqlAuto']->pegarSqlCampo("(SUM(T007_Valor_Total_Produtos-
                        IF('{$g['C031']['somarICMSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
                        IF('{$g['C031']['somarPISPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_PIS,0), 0)-
                        IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS,0), 0)-
                        IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_IRPJ,0), 0)-
                        IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_CSLL,0), 0)) / SUM(T007_Valor_Total_Custo))", $from, $extra);
                        $IPVTotal = gCorrigeNumero($IPVTotal,4);
  */

		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

                acaoClickLinha('{$g['divId']}', function(data, array) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,menuId('PDV'), '', 'Editar Nota Fiscal - ID: ' + array['todosCampos']['T007_Id'], '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T007_Id']) + '&tabela={$from}', [990,605]);
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
	
	case 'ven003GridAnalitico':

        $filtroObrigatorio = true;

		$acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$grid = array(
            // array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha'),
            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => '', 'callback' => 'campoMarcarNFImpressao', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'M|S'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Data Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'width:1%;', 'colunaDimensao'=>'S'),
            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Cód. NF', 'colunaDimensao' => 'S','colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 
            'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width:1%;', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaDimensao'=>'S'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
            array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica'=>'S'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao'=>'S'),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante'),
            array('colunaValor' => 'T008_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'colunaDimensao'=>'S'),
            array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Aliquota_ICMS', 'colunaTitulo' => '% ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'T007_Flag_Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Peso_Unitario', 'colunaTitulo' => 'Peso unit', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3),
            array('colunaValor' => 'T008_Peso_Total', 'colunaTitulo' => 'Peso total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3),
            array('colunaValor' => "D004_Local", 'colunaTitulo' => 'Locação', 'colunaExibir' => true),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T008";
		
		$extra = <<<EOT
            left join T066 on T066_Id=T008_T066_Id
            left join D004 on D004_Id=T066_D004_Id
			left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D003 on D003_Id=D001_D003_Id
            left join D006 on D006_Id=T008_D006_Id
            left join D037 on D037_Id=T008_D037_Id
            left join T007 on T007_Id=T008_T007_Id
            left join D024 on D024_Id=T007_D024_Id
            left join C008 on C008_Id=D001_C008_Id
            left join D021 on D021_Id=T007_D021_Id
            left join D018 on D018_Id=D024_D018_Id
            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
            WHERE T007_C004_Id='{$g['empresaAtual']}'
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*echo <<<EOT
			<div class="buttonsBottomTop">
                <button type="button" id="ven003ImprimirNFeMarcadaProdutos">Imprimir Marcadas</button>
            </div>
EOT;
*/

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Codigo_CFOP, D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Origem', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ),
              'naoGerarExtra' => ''),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T008_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'T008_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),
            'D006_Flag_Venda_Compra_Outros' => array('titulo' => 'CFOP Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => '...', 'value' => ''),
                array('title' => 'Venda', 'value' => 'V'),
                array('title' => 'Compra', 'value' => 'C'),
                array('title' => 'Transferencia', 'value' => 'T'),
                array('title' => 'Outros', 'value' => 'O'),
                array('title' => 'Devolução Vendas', 'value' => 'DV'),
                array('title' => 'Devolução Compras', 'value' => 'DC'),
                array('title' => 'Transporte', 'value' => 'TR'),
              ),
              'naoGerarExtra' => ''),
            'D006_Flag_Devolucao' => array('titulo' => 'Devolução', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),


              ),
              'naoGerarExtra' => ''),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'NF-e', 'value' => '6'),
                array('title' => 'NFC-e', 'value' => '8'),
                array('title' => 'CF', 'value' => '4'),
                array('title' => 'ACP', 'value' => '3')
              ),
            ),
            'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'C008_Tipo' => array('titulo' => 'Depto', 'tipo' => 'texto'),
        );

		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extra_ = base64_encode($extra);
		
        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Quantidade', 'valor' => 'T008_Quantidade', 'titulo' => 'Quantidade Total'),
            array('nome' => 'Valor_Total', 'valor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'titulo' => 'Valor produtos total'),
            array('nome' => 'Peso_Total', 'valor' => 'T008_Peso_Total', 'titulo' => 'Peso Total'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);	

        /**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Imprimir Marcadas', 'id' => 'ven003ImprimirNFeMarcadaProdutos'),
		);
		echo Botoes::processar($botoes);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
		/*if (!personalizacaoTotais()) {
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Quantidade) Total", $from, $extra));
			$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Valor_Total_Preco_Sem_Desconto)", $from, gInsertExtraWhere($extra, " T007_Flag_Cancelada != 'S'")));
            $valorTotal3 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Peso_Total) Total", $from, $extra));

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Quantidade total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor produtos total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Peso total<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
        }*/
        
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var T007_Id = id[1].replace(/T007_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('PDV'), '', 'Editar Nota Fiscal - ID: ' + T007_Id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(T007_Id) + '&tabela=T007', [990,605]);
				});
			} </script></div>
EOT;

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
                $('#ven003ImprimirNFeMarcadaProdutos').unbind('click').bind('click', function(data) {
                    nova_janela('/ven/ven003/grid_func-ajax/ven003ImprimirMarcadas/?ajax=true&extra=' + encodeURIComponent('{$extra_}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao Danfe');
                });
			} </script></div>
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

	case 'ven003GridInutilizacoesPendentes':

        $filtroObrigatorio = true;
		/*
		 * Histórico criado antes do envio da inutilização. O status 000, combinado
		 * com o prefixo, é reservado para solicitações que ainda não receberam
		 * confirmação local da SEFAZ.
		 */
		$grid = array(
			array('colunaValor' => 'recuperarInutilizacao', 'colunaTitulo' => 'Ação', 'colunaTipo' => 'livre', 'callback' => 'ven003RecuperarInutilizacao', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T127_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T127_Faixa', 'colunaTitulo' => 'Faixa'),
			array('colunaValor' => 'T127_Quantidade_NFs', 'colunaTitulo' => 'NFs'),
			array('colunaValor' => 'T127_Modelo', 'colunaTitulo' => 'Modelo'),
			array('colunaValor' => 'T127_Data_Transmicao', 'colunaTitulo' => 'Iniciado em', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T127_Retorno_Sefaz', 'colunaTitulo' => 'Situação'),
			array('colunaValor' => 'T127_Numero_Recibo_Nfe', 'colunaTitulo' => 'Protocolo/recibo'),
		);

		/*
		 * A T127 continua sendo gravada por número, pois é o histórico fiscal da
		 * NF. A grade, porém, representa a solicitação: uma linha por série/faixa.
		 */
		$from = <<<EOT
			(
				SELECT
					MIN(T127_Id) AS T127_Id,
					CONCAT(MIN(CAST(T127_Numero_Nota_Fiscal AS UNSIGNED)), '-', MAX(CAST(T127_Numero_Nota_Fiscal AS UNSIGNED))) AS T127_Faixa,
					COUNT(*) AS T127_Quantidade_NFs,
					T127_Modelo,
					MAX(T127_Data_Transmicao) AS T127_Data_Transmicao,
					MAX(T127_Retorno_Sefaz) AS T127_Retorno_Sefaz,
					MAX(T127_Numero_Recibo_Nfe) AS T127_Numero_Recibo_Nfe
				FROM T127
				WHERE T127_C004_Id = '{$g['empresaAtual']}'
				  AND T127_Codigo_Retorno = '000'
				  AND T127_Retorno_Sefaz LIKE 'INUTILIZACAO_PENDENTE%'
				GROUP BY
					T127_C004_Id,
					T127_Modelo,
					SUBSTRING_INDEX(SUBSTRING_INDEX(T127_Retorno_Sefaz, 'Série: ', -1), ' |', 1),
					SUBSTRING_INDEX(SUBSTRING_INDEX(T127_Retorno_Sefaz, 'Faixa: ', -1), ' |', 1)
			) AS T127
EOT;
		$extra = <<<EOT
			WHERE 1 = 1
			ORDER BY T127_Data_Transmicao DESC, T127_Id DESC
EOT;

		$gridFiltro = array(
			'T127_Data_Transmicao' => array('titulo' => 'Data de início', 'tipo' => 'data'),
			'T127_Faixa' => array('titulo' => 'Faixa inicial', 'tipo' => 'numero'),
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'ven003GridInutilizadas':
        $filtroObrigatorio = true;
		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'exibir' => false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF'),
			array('colunaValor' => 'T007_Serie', 'colunaTitulo' => 'Série'),
			array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Inutilizada em', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T007_Protocolo_Inutilizacao', 'colunaTitulo' => 'Protocolo'),
			array('colunaValor' => 'T007_Justificativa_Inutilizacao', 'colunaTitulo' => 'Justificativa'),
		);

		$from = 'T007';
		$extra = <<<EOT
			WHERE T007_C004_Id = '{$g['empresaAtual']}'
			  AND T007_Flag_Inutilizado = 'S'
			  AND IFNULL(T007_Protocolo_Inutilizacao, '') <> ''
			ORDER BY T007_Data_Hora_Inutilizacao DESC, T007_Id DESC
EOT;

		$gridFiltro = array(
			'T007_Data_Hora_Inutilizacao' => array('titulo' => 'Data de inutilização', 'tipo' => 'data'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número NF', 'tipo' => 'numero'),
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'ven003GridTecnico':
	
//	 GRID
	case 'lista':
		$acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T127_Id', $g['divId']), 'colunaExibirNivel' => 4),
			array('colunaValor' => 'T127_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'exibir' => false),
			array('colunaValor' => 'T127_Data_Transmicao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T127_Retorno_Sefaz', 'colunaTitulo' => 'Log'),
			array('colunaValor' => 'T127_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave acesso'),
			array('colunaValor' => 'T127_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo'),
            array('colunaValor' => 'T127_T007_Id', 'colunaTitulo' => 'ID NF', 'colunaExibir' => true),
            array('colunaValor' => 'T127_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número NF', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id NF', 'colunaExibir' => false),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T127";
		
		$sT007 = mysql_query("select T007_Numero_Nota_Fiscal,T007_C004_Id, T007_Flag_ACP from T007 where T007_Id=$acao_Id");
		$mT007=mysql_fetch_array($sT007);
		
		$extra = <<<EOT

                    left join T007 on T007_Numero_Nota_Fiscal=T127_Numero_Nota_Fiscal AND T007_C004_Id='{$mT007[T007_C004_Id]}'
					where T127_Numero_Nota_Fiscal='$mT007[T007_Numero_Nota_Fiscal]'
                    and T127_Modelo = if('$mT007[T007_Flag_ACP]'='6','55','65')
                    and T127_C004_Id='$mT007[T007_C004_Id]' 
                    order by T127_Id desc,T127_Data_Transmicao desc
		            	
EOT;

        /**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T127_T007_Id'] != $array['todosCampos']['T007_Id']){
				return 'linhaVermelho';
			}
        }
        
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
	
	
	case 'ven003GridProdutos':

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
        //Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');

        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
        $sqlT007 = mysql_query("SELECT T007_Numero_Protocolo_Nfe, 
                                       T007_Casas_Decimais_Preco,
                                       T007_Casas_Decimais_Quantidade,
                                       T007A_Flag_Conversao_Unidade 
                                  FROM T007 
                             LEFT JOIN T007A ON T007_Id = T007A_T007_Id 
                                 WHERE T007_Id = '{$acao_Id}'");
        $mT007 = mysql_fetch_array($sqlT007);
        if(empty($mT007['T007_Numero_Protocolo_Nfe'])) {
            $inclusaoMultiProdutos=array('T008_T007_Id'=>$r_T007_Id);
        }

        // Valida colunas exibir para conversão de unidade
		$colunaPrecoComercial                = false;
		$colunaUnidadeComercial              = false;
		$colunaQuantidadeComercial 	         = false;	
		$colunaQuantidade                    = true;
        $colunaPrecoLiquido                  = true;
		// Só valida conversão se for diferente de preço bruto, primeira versão da conversão de unidade trata apenas para liquido (Felipe 26/06/2023)
		if($mT007['T007A_Flag_Conversao_Unidade'] == 'S'){
			// Colunas T004A que são utilizadas na conversão
			$colunaPrecoComercial                = true ;
			$colunaUnidadeComercial              = true ;
			$colunaQuantidadeComercial 	         = true;
			//Colunas T004 que são usado no padrão para calculos
			$colunaQuantidade                    = false;
            $colunaPrecoLiquido                  = false;
		}

        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T008A_Id', 'colunaTitulo' => 'T008A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'callbackParameter' => array(
                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => "(IF(T006_Quantidade_Estoque< T006_Quantidade, CONCAT('<span style=\"color: var(--BGVermelho);\">',REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ','),'</span>'), REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ',')))", 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_Estoque as Pedido', 'colunaTitulo' => 'Estoq', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T006_Quantidade'), 'colunaExibir' => false),
            array('colunaValor' => 'T008_D009_Id', 'colunaTitulo' => 'T008_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
            array('colunaValor' => 'D006_Flag_Estoque', 'colunaTitulo' => 'D006_Flag_Estoque', 'colunaExibir' => false),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Local', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;', 'colunaExibir' => false),
			array('colunaValor' => 'unPadrao.D037_Id', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'unPadrao.D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
			array('colunaValor' => 'unConversao.D037_Unidade', 'colunaTitulo' => 'Un Conversão', 'colunaAlinhar' => 'center', 'style' => 'font-size:12px', 'colunaExibir' => false),
			array('colunaValor' => 'T008A_D184_Id', 'colunaTitulo' => 'Un Conversão', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'callback' => 'T008A_D184_Id_Unidade', 'callbackParameter' => array('divReload' => $r_divReload), 'colunaExibir' => $colunaUnidadeComercial),
            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Quantidade'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);','colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto', 'colunaExibir' => $colunaQuantidade),
            array('colunaValor' => 'T008A_Quantidade_Comercial', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Quantidade'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto','colunaExibir' => $colunaQuantidadeComercial),
            //array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T008_D006_Id', 'colunaTitulo' => 'CFOP', 'colunaEditarGeraSelect' => array('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', ' ORDER BY D006_Codigo_CFOP ', true), 'colunaEditar' => true, 'callback' => 'T008_D006_Id_CFOP'),
            array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'T008_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4, 'colunaExibir' => false),          
            array('colunaValor' => 'T008_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Preco'], 'callback' => 'campoPrecoTabelaNotaFiscal'),
            array('colunaValor' => 'T008_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto'),
            array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto', 'colunaExibir' => $colunaPrecoLiquido),
            array('colunaValor' => 'T008A_Preco_Unitario_Comercial', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $mT007['T007_Casas_Decimais_Preco'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde); ;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto', 'colunaExibir' => $colunaPrecoComercial),  
            array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'T208_Rota', 'colunaTitulo' => 'Rota', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Hora_Entrega', 'colunaTitulo' => 'Entrega', 'colunaTempo' => 'S', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_T208_Id', 'colunaTitulo' => 'Rota', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'T208_Rota', 'colunaEditarTodos' => true, 'colunaEditar' => true, 'colunaExibir' => false,
                'colunaEditarGeraSelect' => array('T208', 'T208_Id', 'T208_Rota', "ORDER BY T208_Rota")
            ),
            array('colunaValor' => 'T006_D022_Id', 'colunaTitulo' => 'Transportador', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D022_Nome_Empresa', 'colunaEditarTodos' => true, 'colunaEditar' => true, 'colunaExibir' => false,
                'colunaEditarGeraSelect' => array('D022', 'D022_Id', 'D022_Nome_Empresa', "ORDER BY D022_Nome_Empresa")
            ),
            array('colunaValor' => 'T008_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T008_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T008_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV SL', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Valor_Lucro', 'colunaTitulo' => 'R$ Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => '(T008_Valor_Lucro/T008_Valor_Total_Preco)*100', 'colunaTitulo' => '% Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T008_Informacoes_Adicionais', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D006_Codigo_CFOP," (",D006_Id,")") as D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T008_D006_Id_Info', 'Demonstrativo CFOP', 'T008_Id', 'bottom', '', '700')),
            array('colunaValor' => 'T008_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'IF(IFNULL(D149_Id,0) > 0, concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao), "????????") as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T008_D005_Id_Info', 'Demonstrativo NCM', 'T008_Id', 'bottom', '', '500')),
            array('colunaValor' => 'substring(T008_Flag_Situacao_Tributaria,1,1)', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'T008_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarGeraSelect' => $destinoProdutos, 'style' => 'background: var(--celula-vermelho); ; font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T008_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_ICMS_ST', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Percentual_FCP', 'colunaTitulo' => '% FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => 'PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008A_Percentual_Reducao_PIS', 'colunaTitulo' => '%Red PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => 'COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008A_Percentual_Reducao_COFINS', 'colunaTitulo' => '%Red COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T008_Percentual_Partilha_ICMS_Destino', 'colunaTitulo' => 'Difal Dest', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Percentual_Partilha_ICMS_Origem', 'colunaTitulo' => 'Difal Orig', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_ICMS_Substituicao_Retido', 'colunaTitulo' => 'ST Ret R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Partilha_ICMS_Destino', 'colunaTitulo' => 'Difal Dest R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Partilha_ICMS_Origem', 'colunaTitulo' => 'Difal Orig R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_ICMS_Desonerado', 'colunaTitulo' => 'ICMS Deson. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_FCP_ST', 'colunaTitulo' => 'FCP ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_FCP', 'colunaTitulo' => 'FCP R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_FCP_Retido', 'colunaTitulo' => 'FCP Ret. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D149_Valor_VA', 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_ST_VA', 'colunaTitulo' => 'MVA Ajust', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_Preco_Pauta_ST', 'colunaTitulo' => 'Pauta', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_Partilha_ICMS', 'colunaTitulo' => 'Base Difal', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_Base_PIS_Retido', 'colunaTitulo' => 'Base PIS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_PIS_Retido', 'colunaTitulo' => 'Valor PIS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_Base_COFINS_Retido', 'colunaTitulo' => 'Base COFINS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_COFINS_Retido', 'colunaTitulo' => 'Valor COFINS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_ICMS_Substituicao_Retido', 'colunaTitulo' => 'Base ST Ret', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_FCP_ST', 'colunaTitulo' => 'Base FCP ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_FCP', 'colunaTitulo' => 'Base FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_FCP_Ret', 'colunaTitulo' => 'Base FCP Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T008_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            //array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Percentual_Comissao', 'colunaTitulo' => '% Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Comissao', 'colunaTitulo' => 'Valor comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605'), 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T008_Valor_Total_Preco', 'colunaTitulo' => 'T008_Valor_Total_Preco', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '(((T008_Valor_Total_Preco/(T008_Valor_Custo_Unitario*T008_Quantidade))-1)*100)', 'colunaTitulo' => 'Margem', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'colunaTotal' => 'S', 'colunaTotalFormula' => 'return gCorrigeNumero((($t0/$t1)-1)*100);', 'colunaExibir' => false),
            array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'T001_Id', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4, 'callback' => 'insereRegistroT001'),
            array('colunaValor' => 'T008_T066_Id', 'colunaTitulo' => 'Locação', 'callback' => 'T008_T066_Id_Local', 'colunaAlinhar' => 'center', 'colunaExibirCodigo' => '$row = mysql_fetch_assoc(mysql_query("SELECT T007_T005_Id FROM T007 WHERE T007_Id = \'$r_acaoId\'")); return ($row{\'T007_T005_Id\'} > 0) ? false : true;'),
            array('colunaValor' => 'T008_Codigo_CEST', 'colunaTitulo' => 'CEST', 'style' => 'width: 1%'),
            array('colunaValor' => 'T008_Codigo_Beneficio_Fiscal', 'colunaTitulo' => 'Benefício Fiscal'),
            array('colunaValor' => 'T008_T007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Aliquota_ICMS_Credito', 'colunaTitulo' => '%ICM Créd', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_ICMS_Credito', 'colunaTitulo' => 'Valor ICM Créd', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST ICMS',  'colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T008_Situacao_Tributaria_IPI', 'colunaTitulo' => 'ST IPI', 'colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T008_Situacao_Tributaria_PIS', 'colunaTitulo' => 'ST PIS', 'colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T008_Situacao_Tributaria_COFINS', 'colunaTitulo' => 'ST COFINS', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D184_Quantidade_Conversao', 'colunaTitulo' => 'D184_Quantidade_Conversao', 'colunaExibir' => false),
			array('colunaValor' => 'D184_Tipo_Operacao', 'colunaTitulo' => 'D184_Tipo_Operacao', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Travar_Nota', 'colunaTitulo' => 'T007_Flag_Travar_Nota', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'T007_Numero_Protocolo_Nfe', 'colunaExibir' => false),
            array('colunaValor' => 'T008A_D014_Id', 'colunaTitulo' => 'Plano de Contas', 'colunaEditarGeraSelect' => array('D014', 'D014_Id', 'D014_SubConta', 'WHERE D014_Flag_Ativo="S" order by D014_SubConta', true), 'colunaEditar' => true, 'callback' => 'T008A_D014_Id_PlanoContas', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaExibir' => false),
            /* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 19/09/2025
            ==============================================================*/
            array('colunaValor' => 'T008A_Valor_Total_Reforma', 'colunaTitulo' => 'Total RT c/ impostos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Situacao_Tributaria_CBS_IBS', 'colunaTitulo' => 'CST CBS/IBS', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Classificacao_Tributaria_CBS_IBS', 'colunaTitulo' => 'Classificação Trib. CBS/IBS', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Situacao_Tributaria_CBS_IBS_Regular', 'colunaTitulo' => 'CST CBS/IBS Reg.', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Classificacao_Tributaria_CBS_IBS_Regular', 'colunaTitulo' => 'Classificação Trib. CBS/IBS Reg.', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Aliquota_CBS', 'colunaTitulo' => 'CBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Reducao_CBS', 'colunaTitulo' => '% Red. CBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Aliquota_CBS_Regular', 'colunaTitulo' => 'CBS Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Reducao_CBS_Regular', 'colunaTitulo' => '% Red. CBS Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Diferimento_CBS', 'colunaTitulo' => '% Dif. CBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Aliquota_IBS_UF', 'colunaTitulo' => 'IBS UF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Reducao_IBS_UF', 'colunaTitulo' => '% Red. IBS UF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Aliquota_IBS_UF_Regular', 'colunaTitulo' => 'IBS UF Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Reducao_IBS_UF_Regular', 'colunaTitulo' => '% Red. IBS UF Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Diferimento_IBS_UF', 'colunaTitulo' => '% Dif. IBS UF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Aliquota_IBS_Municipal', 'colunaTitulo' => 'IBS Mun.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Reducao_IBS_Municipal', 'colunaTitulo' => '% Red. IBS Mun.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Aliquota_IBS_Municipal_Regular', 'colunaTitulo' => 'IBS Mun. Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Reducao_IBS_Municipal_Regular', 'colunaTitulo' => '% Red. IBS Mun. Reg.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Percentual_Diferimento_IBS_Municipal', 'colunaTitulo' => '% Dif. IBS Mun.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_CBS', 'colunaTitulo' => 'CBS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_CBS_Regular', 'colunaTitulo' => 'CBS Reg. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_Diferimento_CBS', 'colunaTitulo' => 'Dif. CBS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_IBS_UF', 'colunaTitulo' => 'IBS UF R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_IBS_UF_Regular', 'colunaTitulo' => 'IBS UF Reg. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_Diferimento_IBS_UF', 'colunaTitulo' => 'Dif. IBS UF R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_IBS_Municipal', 'colunaTitulo' => 'IBS Mun. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_IBS_Municipal_Regular', 'colunaTitulo' => 'IBS Mun. Reg. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_Diferimento_IBS_Municipal', 'colunaTitulo' => 'Dif. IBS Mun. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            array('colunaValor' => 'T008A_Valor_Base_CBS_IBS', 'colunaTitulo' => 'Base CBS/IBS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
            /*==============================================================*/
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T008";
		
		$extra = <<<EOT
        -- JOIN
            left join T007 on T007_Id=T008_T007_Id
            left join D024 on T007_D024_Id = D024_Id
            left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D006 on D006_Id=T008_D006_Id
            left join T006 on T008_T006_Id=T006_Id
            left join T005 on T005_Id=T006_T005_Id
            left join D005 on D005_Id=T008_D005_Id
            left join D149 on D149_D005_Id=D005_Id AND D149_C004_Id=T007_C004_Id
            left join T066 on T066_Id=T008_T066_Id
            left join D004 on D004_Id=T066_D004_Id
            left join T219 ON T219_Id=T006_T219_Id
            left join T218 ON T218_Id=T219_T218_Id
            LEFT JOIN T001 ON T001_T008_Id=T008_Id
            LEFT JOIN C004 ON C004_Id=IFNULL(T006_C004_Id_Estoque,T005_C004_Id)
            left join D022 on D022_Id=T006_D022_Id
            left join T208 on T208_Id=T006_T208_Id
            left join T008A ON T008_Id = T008A_T008_Id
            left join D184 on D184_Id=T008A_D184_Id
            left join D037 as unPadrao on unPadrao.D037_Id=T008_D037_Id
            left join D037 as unConversao on unConversao.D037_Id=D184_D037_Id
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
                    }else if(campoEditado == 'T008A_Preco_Unitario_Comercial'){
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
                    }else if(campoEditado == 'T008A_Quantidade_Comercial'){
                        divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id'); // grid
                        divRefresh('{$r_divReload}');                           
                        
                    }else if(campoEditado == 'T008_Quantidade'){
                        divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id'); // grid
                        divRefresh('{$r_divReload}');                           
                        
                    }
                    divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
                }

                function colunaEditarCallbackJSReload() {
                    divRefresh('{$r_divReload}');
                }

                acaoClickLinha('{$g['divId']}', function(data, array) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + array['todosCampos']['T008_Id'], '/ven/ven003/content/ven003ContentProdutoEditar/', '&tabela=T008&acaoId=' + encodeURIComponent(array['todosCampos']['T008_Id']) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [800,550]);
                });

				//var dbclickPrevent = false;
				//$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				//	 Previne duplo click (abrir duas janelas)
				//	if (dbclickPrevent) { return false; }
				//	dbclickPrevent = true;
				//	setTimeout(function() { dbclickPrevent = false; }, 500);
				//  (substituir pelo mesmo ID da chave primária do grid)
				//	var chavePrimaria = 'T008_Id';	
				//	if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				//	var id = $(this).closest('tr').attr('id').split('|');
				//	var acaoId = id[0].replace(chavePrimaria + '-', '');
				// Ação: abrir nova janela
	        	//	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/ven/ven003/content/ven003ContentProdutoEditar/', '&tabela=T008&acaoId=' + encodeURIComponent(acaoId) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [800,500]);
		        //});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		* Botões (Versão Acima do GRID)
		*/
        /*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" style="float:left;" id="ven003IncluirProdutoNFe">Incluir</button>
				<button type="button" style="float:left;" id="ven003AplicarDesconto" {$disabled}>Aplicar Desconto</button>
                <span class="item" style="text-align:right; margin: 4px 0 0 10px;">Total Itens <input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#ven003IncluirProdutoNFe').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Produto', '/ven/ven003/content/ven003ContentProdutoEditar/','&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [600,450]);
				});
				$('#ven003AplicarDesconto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar desconto', '/ven/ven003/content/ven003ContentAplicarDesconto/','&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [400,150]);
				});
			} </script></div>
EOT;
*/

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
	
	case 'ven003GridPagamentos':
			$acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
			$sqlT007 = "SELECT T007_Pgto_Data, T007_Pgto_C007_Id, C007_Login FROM T007 LEFT JOIN C007 ON C007_Id=T007_Pgto_C007_Id WHERE T007_Id='$r_acaoId'";
			$resultT007 = mysql_query($sqlT007);
			$linhaT007 = mysql_fetch_array($resultT007);
			$dataGerada = gCorrigeData($linhaT007['T007_Pgto_Data']);
			$user = strtoupper($linhaT007['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por <b>".$user."</b> em <b>".$dataGerada."</b>";
			}
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T118_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T118_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T118_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T118_Prazos_Dias"),
				array('colunaValor' => 'T118_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T118_Prazos_Dias"),
				array('colunaValor' => 'ven003_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "ven003_Dia_Semana"),
				array('colunaValor' => 'T118_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T118_Prazos_Dias"),				
				array('colunaValor' => 'T118_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'order by D027_Portador', true), 'callback' => 'T118_D027_Id_Portador'),
				array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven003_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T118';

			$extra = <<<EOT
				left join D027 on D027_Id=T118_D027_Id
				WHERE T118_T007_Id = {$acao_Id}
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
					var id = $(this).closest('tr').attr('id').replace(/T118_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T118_Prazos_Dias(T118_Id, campoEditado, valorAntigo, novoValor) {
					T118_Id = T118_Id.replace(/T118_Id-/, '');
					$.getJSON('/ven/ven003/grid_func-ajax/atualizarParcelas/?ajax=true&T118_Id='      		+ encodeURIComponent(T118_Id) + 
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

		case 'ven003GridReferenciada':
		// Recompõe referências de pagamento antecipado para vendas geradas por
		// qualquer fluxo (direto, expedição ou agrupado), inclusive notas criadas
		// antes da centralização desta regra.
		// Desativado temporariamente: não sincronizar pagamento antecipado ao abrir/recarregar o grid.
	    /*require_once('bibliotecas/classes/VEN012.php');
		$VEN012PagamentoAntecipado = new VEN012();
		$VEN012PagamentoAntecipado->sincronizarPagamentoAntecipadoVenda((int) $r_T007_Id, array()); */
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T132_Id', $g['divId'])),
				array('colunaValor'=>'T132_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true),
				array('colunaValor'=>'T132_Tipo', 'colunaTitulo'=>'Tipo'),
				array('colunaValor'=>'T132_Chave_Acesso', 'colunaTitulo'=>'Chave Acesso'),
				array('colunaValor'=>'T132_Cupom_Numero_EFC', 'colunaTitulo' => 'EFC', 'colunaExibir' => true),
				array('colunaValor'=>'T132_Cupom_Numero_COO', 'colunaTitulo' => 'COO', 'colunaExibir' => true),
				array('colunaValor'=>'T132_Cupom_Modelo', 'colunaTitulo' => 'Modelo de Cupom', 'colunaExibir' => true),
				//array('colunaValor'=>'campoCupom','colunaTipo' => 'livre','colunaTitulo'=>'Cupom','callback' => 'campoCupom'),
				array('colunaValor'=>'D018_UF', 'colunaTitulo'=>'UF'),
				array('colunaValor'=>'T132_Ano_Mes', 'colunaTitulo'=>'Emissão'),
				array('colunaValor'=>'T132_Pessoa_Fisica_Juridica', 'colunaExibir' => false),
				array('colunaValor'=>'T132_Cnpj', 'colunaExibir' => false),
				            
				array('colunaValor'=>'campoNumeroDocumento','colunaTipo' => 'livre', 'colunaTitulo'=>'CNPJ/CPF', 'callback' => 'campoNumeroDocumento'),
				array('colunaValor'=>'T132_Modelo', 'colunaTitulo'=>'Modelo'),
				array('colunaValor'=>'T132_Serie', 'colunaTitulo'=>'Serie'),
				array('colunaValor'=>'T132_Numero_Nota_Fiscal', 'colunaTitulo'=>'Número'),
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T132';			
                $extra = <<<EOT
                left join D018 on D018_Codigo_IBGE=T132_UF_IBGE
                WHERE T132_T007_Id='{$r_T007_Id}'
EOT;

		/* Define ação de consulta ao clicar em uma linha do grid. */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T132_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Consultar Referência Fiscal', '/ven/ven003/content/ven003ContentVisualizarNotaReferenciada/', '&T132_Id=' + encodeURIComponent(id) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [720,260]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
		} </script></div>
EOT;

        /**
        * Botões (Versão Acima do GRID)
        */
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <button type="button" id="ven003IncluirNotaReferenciada">Incluir</button>
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {      
                $('#ven003IncluirNotaReferenciada').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Nota Referenciada', '/ven/ven003/content/ven003ContentIncluirNotaReferenciada/','&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [500,300]);
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
        
        /**
        * Grid Serviços
        */
        case 'ven003GridServicos':

            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(
            array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T203_Id', $g['divId'])),
            array('colunaValor' => 'T203_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true),
            //array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => "concat(T203_Titulo, '<br />', T203_Descricao)", 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => 'T203_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'T203_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true),
            //array('colunaValor' => 'T203_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'T203_T007_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            array('colunaValor' => 'T203_T147_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            //array('colunaValor' => 'T203_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'ven002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
            array('colunaValor' => 'T203_D116_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T203_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado'),
            //array('colunaValor' => 'T203_Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T203_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
            );
    
            /**
                *
                * Definição da SQL
                */
            $from = 'T203';
    
            $extra = <<<EOT
                    LEFT JOIN T008 ON T008_Id=T203_T008_Id
                    LEFT JOIN D009 ON D009_Id=T008_D009_Id
                    LEFT JOIN D049 ON D009_D049_Id=D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    WHERE T203_T007_Id='{$r_acaoId}'
EOT;
    

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T203_Id-/, '');
                divSetarAcaoId('{$r_divReload}', id);
                divRefresh('{$r_divReload}');
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
        
        case 'ven003GridCCE':
        
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T199_Id', $g['divId'])),
            array('colunaValor' => 'T199_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true),
            array('colunaValor' => 'T199_Flag_Enviado', 'colunaTitulo' => 'T199_Flag_Enviado', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'T007_Flag_Cancelada', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF','callback' => 'ven003CampoNF'),
            array('colunaValor' => 'T199_Sequencial_Evento', 'colunaTitulo' => 'Sequencial'),
            array('colunaValor' => 'T199_Data_Hora_Inclusao', 'colunaTitulo' => 'Inclusão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T199_Correcao', 'colunaTitulo' => 'Correção'),
            array('colunaValor' => '(SELECT CONCAT(T200_Status," - ",T200_Motivo) FROM T200 WHERE T200_T199_Id=T199_Id ORDER BY T200_Id DESC LIMIT 1) as RetornoSefaz', 'colunaTitulo' => 'Retorno Sefaz', 'callback' => 'ven003LogCCe'),
            array('colunaValor' => 'DownloadXML', 'colunaTitulo' => 'Download XML','colunaTipo' => 'livre','callback' => 'ven003DownloadXmlCce', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T199_Data_Envio_Email_Cce', 'colunaTitulo' => 'Email','callback' => 'validaCampoEmailCce', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
        );
        
        /**
         * Definição da SQL
         */
        $from = "T199";
        
        $extra = <<<EOT
                    left join C007 on C007_Id=T199_C007_Id
                    left join T007 on T007_Id=T199_T007_Id
                    left join D024 on D024_Id=T007_D024_Id
                    WHERE T007_C004_Id='{$g['empresaAtual']}'
EOT;
        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        function gridLinha($array) {
            if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
                return 'linhaVermelho';
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
            'T199_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
            'T199_Sequencial_Evento' => array('titulo' => 'Nome Fantasia', 'tipo' => 'numero'),
            'T199_Data_Hora_Inclusao' => array('titulo' => 'Inclusão', 'tipo' => 'data'),
            'T199_Correcao' => array('titulo' => 'Correção', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
        );
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
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
        * Botões (Versão Acima do GRID)
        */
        /*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;
*/
        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        $ErroCCe = gGeraAlertMsg('Erro', 'Esta CCe ainda não foi emitida!', false);
        $ErroCancelada = gGeraAlertMsg('Erro', 'Esta nota foi cancelada!', false);
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                // pega o ID da linha (verifica as chave(s) primaria(s) do grid)
                var id = $(this).closest('tr').attr('id').split('|');
                var T199_Id = id[0].replace(/T199_Id-/, '');
                var T199_Flag_Enviado = id[1].replace(/T199_Flag_Enviado-/, '');
                var T007_Flag_Cancelada = id[2].replace(/T007_Flag_Cancelada-/, '');
                if(T199_Flag_Enviado == 'S' && T007_Flag_Cancelada=='N'){
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003ImprimirCce/?ajax=true&T199_Id=' + encodeURIComponent(T199_Id) + '&callback=?', function(request) {
                        window.open(request.data);
                    });
                }else{
                    if(T007_Flag_Cancelada=='S'){
                        {$ErroCancelada}
                    }else{
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Carta de Correção Eletrônica', '/ven/ven003/content/ven003contentIncluirCCe/','&acaoId=' + encodeURIComponent(T199_Id), [500,350]); 
                    }
                }
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

    case 'ven003GridPagamentosTitulos':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoT002'),
				array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir'=>false),
				array('colunaValor' => "T002_Numero_Documento", 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center'),
				array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'T002_T035_Id',  'colunaExibir'=>false),
				array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'validaCampoExtratoCliente', 'colunaExibir'=>false),
				array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
				array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa', 'colunaExibir'=>false),
				array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
				array('colunaValor' => "concat(T002_Numero_Parcela,'/',T002_Total_Parcelas) as Parcelas", 'colunaTitulo' => 'Parc'),
				array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => true),
				array('colunaValor' => "datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo", 'colunaTitulo' => 'Prz'),
				array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => true),
				array('colunaValor' => "datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso", 'colunaTitulo' => 'Ats'),
				array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
				array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'colunaMetrica' => true),
				array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Juros','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
				array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desconto','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
				array('colunaValor' => "T002_Valor_Recebido", 'colunaTitulo' => 'Recebido','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
				array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
				array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
				array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'Portador'),
				array('colunaValor' => "T117_Nosso_Numero", 'colunaTitulo' => 'Nosso Número'),
				array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'Observação'),
				array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'Vendedor'),
				array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta", 'colunaTitulo' => 'Conta'),
				array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'Grupo'),
				array('colunaValor' => "T002_T045_Id", 'colunaTitulo' => 'Lote'),
	            //array('colunaValor' => "T002_Flag_Status", 'colunaTitulo' => 'Status', 'callback' => 'fin001CampoStatus'),
				array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
				array('colunaValor' => "T002_Agrupamento", 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
				array('colunaValor' => "T002_Flag_Enviar_Banco", 'colunaTitulo' => '', 'colunaExibir' => false),
				array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
				array('colunaValor' => "T117_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
				array('colunaValor' => "T002_Flag_Reparcelar", 'colunaTitulo' => 'Reparcelar', 'colunaExibir' => false),
				array('colunaValor' => "T002_Flag_Cancelada", 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
				array('colunaValor' => "T035_Data_Processamento", 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
				array('colunaValor' => "T007_Id", 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false)
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'T002';
			
			$extra = <<<EOT
				left join D024 on D024_Id=T002_D024_Id
				left join D027 on D027_Id=T002_D027_Id
				left join T117 on T117_Id=T002_T117_Id
				left join D014 on D014_Id=T002_D014_Id
                LEFT JOIN D154 ON D154_Id=D014_D154_Id
				left join D046 on D046_Id=D024_D046_Id
				left join T007 on T007_Id=T002_T007_Id
				left join T035 on T035_Id=T002_T035_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				left join T103 on T002_Id=T103_T002_Id
				WHERE T002_T007_Id='{$r_acaoId}'
				AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		*/ 
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
			});
		} </script></div>
EOT;
 		echo '<br><br>';
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

    case 'ven003GridPagamentosTitulosPagar':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(       
            array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
            //array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Numero_Documento', 'colunaTitulo' => 'N.Doc.', 'style' => 'width:1%', 'colunaExibir' => true),
            array('colunaValor' => 'T015_Flag_Boleto', 'colunaTitulo' => 'Ok', 'callback' => 'validaCampoFlagBoleto', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D024_Cnpj,D024_Cpf)', 'colunaTitulo' => 'CNPJ/CPF', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor','colunaDimensao' => 'S'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
            array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta','colunaDimensao' => 'S','colunaExibir' => false),
            array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupl.', 'callback' => 'validaCampoDuplicata', 'callbackParameter' => array('15', ',', 'T015_Observacao_Reparcelamento'), 'style' => 'width:1%;'),
            array('colunaValor' => 'concat(T015_Numero_Parcela,\'/\',T015_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaDimensao' => 'S'),
            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaDimensao' => 'S'),
            array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaDimensao' => 'S'),
            array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%','colunaMetrica' => 'S'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador','colunaDimensao' => 'S'),
            array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo','colunaDimensao' => 'S'),
            array('colunaValor' => 'T015_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T015_Codigo_Barras', 'colunaTitulo' => 'Boleto'),
            array('colunaValor' => 'T015_T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Observacao_Reparcelamento', 'colunaTitulo' => 'T015_Observacao_Reparcelamento', 'colunaExibir' => false),
            array('colunaValor' => 'T015_T015_Id_Agrupado', 'colunaTitulo' => 'T015_T015_Id_Agrupado', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false)
        );

        /**
         *
         * Definição da SQL
         */
        $from = 'T015';

            $extra = <<<EOT
                LEFT JOIN D024 ON D024_Id=T015_D024_Id
                LEFT JOIN D031 ON D031_Id=T015_D031_Id
                LEFT JOIN D073 ON D073_Id=T015_D073_Id
                LEFT JOIN D014 ON D014_Id=T015_D014_Id
                LEFT JOIN D154 ON D154_Id=D014_D154_Id
                LEFT JOIN D046 ON D046_Id=D024_D046_Id
                LEFT JOIN T007 ON T007_Id=T015_T007_Id
                LEFT JOIN C004 ON C004_Id=T015_C004_Id
                LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                LEFT JOIN T103 ON T015_Id=T103_T015_Id
                WHERE T015_T007_Id='{$r_acaoId}'
                AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
EOT;

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
        echo '<br><br>';
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

	case 'ven003GridPagamentosBoletos':
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				//array('colunaValor' => 'T117_T035_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false), //se necessário usar o T002_T035_Id
				array('colunaValor' => 'T117_Flag_Layout', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
				array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
				array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
				array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
				array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
				array('colunaValor' => 'T117_D024_Id', 'colunaTitulo' => 'IdCliente','colunaExibir' => false),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
				array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
				array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaExibir' => false),
				array('colunaValor' => 'campoCancelarT117', 'colunaTipo' => 'livre', 'callback' => 'campoCancelarT117'),
				array('colunaValor' => 'D024_E_Mail', 'colunaTitulo' => 'E-mail'),
				array('colunaValor' => 'T117_T002_Id', 'colunaExibir' => false),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T117';
			$extra = <<<EOT
				left join D024 on D024_Id=T117_D024_Id
		 		left join T002 on T002_T117_Id=T117_Id OR T117_T002_Id = T002_Id
		 		left join D007 on D007_Id=T117_D007_Id
		 		where T002_T007_Id = '{$r_acaoId}'
		 		GROUP BY T117_Id
EOT;
			function gridLinha($array) {
				global $g;
				$dataRecebimento = $array['todosCampos']['T002_Data_Recebimento'];
				$cancelado = $array['todosCampos']['T117_Flag_Cancelado'];
				if($cancelado == 'S'){
					return 'linhaVermelho';
				} else if($dataRecebimento != '0000-00-00'){
					return 'linhaCinza';
				} 
			}
			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
				<div class="buttonsBottomTop">
                    <button type="button" id="fin005GridPrincipalBtnIncluir">Incluir</button>
					<button type="button" id="fin005GridPrincipalBtnImprimir">Imprimir boletos</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
                    $('#fin005GridPrincipalBtnIncluir').unbind('click').bind('click', function(data) {
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de boleto', '/fin/fin005/content/fin005ContentAbreCadastroBoleto/', '&tab=geral', [900,550]);
                    });
					$('#fin005GridPrincipalBtnImprimir').unbind('click').bind('click', function(data) {
                        $.getJSON('/ven/ven003/grid_func-ajax/gerarPDFboleto/?ajax=true&T007_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {    
                            if(request.code){
                                $('#{$g['divId']}').hideLoading();
                                nova_janela('{$confUsuario['urlRaiz']}' + request.data,0,0,800,500,'no','no','no','no','yes','yes',0);
                                $.getJSON('/ven/ven003/grid_func-ajax/ven003DeletarPDFBoleto/?ajax=true&T007_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {});
                            } else {
                                dialogConfirm('Operação cancelada', request.data);
                            }
                        });
					});
				} </script></div>
EOT;

			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);
			
			/**
			 * Geração: Monta o SQL e retorna o dados
			 */
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

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
					var chavePrimaria = 'T117_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela do browser 
					// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
                    $.getJSON('/fin/fin005/grid_func-ajax/gerarPDFboleto/?ajax=true&T117_Id=' + encodeURIComponent(acaoId) + '&callback=?', function(request) {
                        if(request.code){
                            $('#{$g['divId']}').hideLoading();
                            var w = window.open('{$confUsuario['urlRaiz']}' + request.data,'_blank');
                            w.addEventListener("load", function(event) {
                                $.getJSON('/fin/fin005/grid_func-ajax/fin005DeletarPDFBoleto/?ajax=true&T117_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {});
                            });
                        } else {
                            dialogConfirm('Operação cancelada', request.data);
                        }
                    });
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

	case 'ven003GridDevolucao':
		$filtroObrigatorio = "Selecione uma ou mais notas fiscais.";

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_fiscal', 'colunaTitulo' => 'NF', 'style' => 'background-color: var(--celula-azul); width: 80px; max-width: 80px; min-width: 80px', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D006R.D006_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'D006D.D006_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D006R.D006_Id," - ",D006R.D006_Codigo_CFOP)', 'colunaTitulo' => 'CFOP Entrada', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('CFOP', '/fis/fis001/content/fis001content02', 'D006R.D006_Id', '1000,583')),
            array('colunaValor' => 'concat(D006D.D006_Id," - ",D006D.D006_Codigo_CFOP)', 'colunaTitulo' => 'CFOP Devolução', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('CFOP', '/fis/fis001/content/fis001content02', 'D006R.D006_Id', '1000,583')),
			array('colunaValor' => 'T008_Quantidade_Devolucao', 'colunaTitulo' => 'Qtd Devolução', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-verde); width: 100px; max-width: 100px; min-width: 100px', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Quantidade_Falta_Lancar(T008_Id)', 'colunaTitulo' => 'Saldo','callback' => 'gCorrigeNumero', 'style' => 'background-color: var(--celula-amarelo-claro); width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtd', 'callback' => 'gCorrigeNumero', 'style' => 'background-color: var(--celula-amarelo-claro); width: 1%;'),
			array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Preço Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
			array('colunaValor' => "CONCAT(T007_Numero_Nota_Fiscal, ' - ', D024_Nome_Empresa) as tituloAgrupar", 'colunaTitulo' => '', 'colunaExibir' => false, 'style' => 'width: 1%;'),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T008';
		$extra = <<<EOT
			left join D009 on D009_Id=T008_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
            LEFT JOIN D006 as D006R ON D006R.D006_Id=T008_D006_Id
            LEFT JOIN D006 as D006D ON D006D.D006_Id=D006R.D006_D006_Id
			LEFT JOIN T007 ON T007_Id = T008_T007_Id
			LEFT JOIN D024 ON D024_Id = T007_D024_Id
                WHERE T007_C004_Id = '{$g['empresaAtual']}'
                  AND T007_Flag_Cancelada !='S'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número Nota Fiscal', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Data Emissão', 'tipo' => 'data'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'NF', 'campoAgrupar' => 'tituloAgrupar', 'campoMostrar' => 'tituloAgrupar', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			)),
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$T007_Numero_Nota_Fiscal = $g['ui']->getGridFiltroCampoBusca('T007_Numero_Nota_Fiscal');
        /**
         * Botões Almir
         */
        $seNotaFiscalMostra = (!empty($T007_Numero_Nota_Fiscal)) ? true : false;
        $botoes = array(
			array('titulo' => 'Gerar Nota de Devolução', 'id' => 'ven003DevolucaoGerar',  'exibirCallback' => "return $seNotaFiscalMostra;"),
		);
		echo Botoes::processar($botoes);

		/*if (!empty($T007_Numero_Nota_Fiscal)) {
			/**
			* Botões (Versão Acima do GRID)
			*/
			
				/*<div class="buttonsBottomTop">
					<button type="button" id="ven003DevolucaoGerar">Gerar Nota de Devolução</button>
				</div>*/
                echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#ven003DevolucaoGerar').unbind('click').bind('click', function(data) {
						$.ajax({
							url: '/ven/ven003/grid_func-ajax/verificarGerarNotaDevolucao/',
							type: 'post',
							dataType: 'text',
							data: {
								ajax: true,
								sql: $('#{$g['divId']} .divToScroll').attr('gridsql'),
								callback: 'retorno'
							},
							success: function(response) {
								var request = JSON.parse(response.replace(/^[^(]*\(/, '').replace(/\);?\s*$/, ''));
								if (!request.code) {
									dialogConfirm('Erro', request.data);
								} else {
									// Procurar todos os T007 envolvidos na consulta
									var listaT007s = [];
									$('#{$g['divId']} tr.linha').each(function() {
										var id = $(this).attr('id').split('|');
										var T007_Id = id[0].replace('T007_Id-', '');
										if (jQuery.inArray(T007_Id, listaT007s) < 0) {
											listaT007s.push(T007_Id);
										}
									});
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Nota Devolução', '/ven/ven003/content/ven003ContentNotaTransporte/', '&tab=geral&listaT007s=' + encodeURIComponent(JSON.stringify(listaT007s)) + '&divIdAberto=' + encodeURIComponent('{$g['divId']}') + '&acao=devolucao', [580,330]);
								}
							}
						});
					});
				} </script></div>
EOT;
		//}

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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T007_Id = id[0].replace('T007_Id-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Nota Fiscal: ' + T007_Id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(T007_Id) + '&tabela={$from}', [900,550]);
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

    case 'ven003GridRecuperacaoICMSST':
        $filtroObrigatorio = true;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'T008_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Cod. Produto', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'style' => 'width: 1%;', 'callbackParameter' => array(
                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'Venda.T001_Quantidade', 'colunaTitulo' => 'Quantidade', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'callbackParameter' => 2, 'style' => 'width: 1%;'),
            //array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF Saída', 'style' => 'width: 1%; background-color: lightblue;'),
            array('colunaValor' => 'Venda.T001_Descricao_Operacao', 'colunaTitulo' => 'NF Saída', 'style' => 'width: 1%; background-color: lightblue;'),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Ch Saída', 'colunaExibir'=>false),
            array('colunaValor' => 'SaidaCFOP.D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP Saída', 'colunaExibir'=>false),
            array('colunaValor' => 'Venda.T001_Data_Lancamento', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'VendaUF.D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'Venda.T001_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'callbackParameter' =>4),
            array('colunaValor' => 'Venda.T001_Valor_Preco_Unitario*Venda.T001_Quantidade', 'colunaTitulo' => 'Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'callbackParameter' =>4),
            array('colunaValor' => 'Compra.T001_Descricao_Operacao', 'colunaTitulo' => 'NF Entrada', 'style' => 'width: 1%; background-color: var(--celula-verde);'),
            array('colunaValor' => 'CompraNF.T013_Chave_Acesso_Nfe', 'colunaTitulo' => 'Ch Entrada', 'colunaExibir'=>false),
            array('colunaValor' => 'EntradaCFOP.D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP Entrada', 'colunaExibir'=>false),
            array('colunaValor' => 'Compra.T001_Data_Lancamento', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'CompraUF.D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'Compra.T001_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'callbackParameter' =>4),
            array('colunaValor' => 'Compra.T001_Valor_Preco_Unitario*Venda.T001_Quantidade', 'colunaTitulo' => 'Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'callbackParameter' =>4),
            array('colunaValor' => "Compra.T001_Valor_ICMS*Venda.T001_Quantidade as ICMS", 'colunaTitulo' => 'ICMS', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'center', 'callbackParameter' => 2, 'style' => 'width: 1%; background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => "Compra.T001_Valor_ST*Venda.T001_Quantidade as ICMS_ST", 'colunaTitulo' => 'ICMS/ST', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'center', 'callbackParameter' => 2, 'style' => 'width: 1%; background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => "(Compra.T001_Valor_ICMS*Venda.T001_Quantidade)+(Compra.T001_Valor_ST*Venda.T001_Quantidade) as Recuperacao", 'colunaTitulo' => 'Recuperação', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'center', 'callbackParameter' => 2, 'style' => 'width: 1%; background-color: var(--celula-amarelo-claro);'),
        );

        /**
        * Definição da SQL
        */
        $from = 'T008';
        
        $extra = <<<EOT
            LEFT JOIN T007 ON T007_Id=T008_T007_Id
            LEFT JOIN T001 as Venda ON T008_Id=Venda.T001_T008_Id
            LEFT JOIN T001 as Compra ON T008_Compra_T001_Id=Compra.T001_Id
            left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D024 VendaEmp on VendaEmp.D024_Id=T007_D024_Id
            left join D018 VendaUF on VendaUF.D018_Id=VendaEmp.D024_D018_Id
            left join D024 CompraEmp on CompraEmp.D024_Id=Compra.T001_D024_Id
            left join D018 CompraUF on CompraUF.D018_Id=CompraEmp.D024_D018_Id
            left join T013 CompraNF on CompraNF.T013_Id=Compra.T001_T013_Id
            left join D006 EntradaCFOP on EntradaCFOP.D006_Id=Compra.T001_D006_Id
            left join D006 SaidaCFOP on SaidaCFOP.D006_Id=Venda.T001_D006_Id
            WHERE
                Venda.T001_Id IS NOT NULL
                AND Venda.T001_Flag_Operacao = 'S'
                AND Compra.T001_Id IS NOT NULL
                AND Compra.T001_Valor_ST > 0
                AND VendaUF.D018_Id != '{$g['C004']['C004_D018_Id']}'
                AND T007_C004_Id = '{$g['empresaAtual']}'
            ORDER BY Venda.T001_Data_Lancamento ASC, T007_Numero_Nota_Fiscal ASC
EOT;

        /**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Associar Venda Compra', 'id' => 'ven003AssociacaoVendaCompra'),
		);
		echo Botoes::processar($botoes);


        /**
        * Filtro
        */
        $gridFiltro = array(
            'Venda.T001_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
        * Botões (Versão Acima do GRID)
        */
        /*if (!personalizacaoTotais()) { 
            $registros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
            $ICMS = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(Compra.T001_Valor_ICMS*Venda.T001_Quantidade)", $from, $extra));
            $ST = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(Compra.T001_Valor_ST*Venda.T001_Quantidade)", $from, $extra));
            $ambos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum((Compra.T001_Valor_ICMS*Venda.T001_Quantidade)+(Compra.T001_Valor_ST*Venda.T001_Quantidade))", $from, $extra));
            
            echo $totais = <<<EOT
                <div class="buttonsBottomTop">
                    <span class="item" style="text-align:right">Registros<br><input type="text" value="{$registros}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">ICMS<br><input type="text" value="{$ICMS}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">ICMS/ST<br><input type="text" value="{$ST}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Recuperação<br><input type="text" value="{$ambos}" class="whiteBg" id="" size="11" readonly></span>
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
            array('nome' => 'ICMS', 'tipo'=>'sql','valor' =>'ICMS', 'titulo' => 'ICMS'),
            array('nome' => 'ICMS_ST', 'tipo'=>'sql', 'valor' => 'ICMS_ST', 'titulo' => 'ICMS/ST'),
            array('nome' => 'Recuperacao', 'tipo'=>'sql', 'valor' => 'Recuperacao', 'titulo' => 'Recuperação'),
        );

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T007', [900,550]);
                });
            } </script></div>
EOT;
        
        
        $data = $g['ui']->getGridFiltroCampoBusca('Venda.T001_Data_Lancamento');
        $dataInicial = gCorrigeDataInverte($data[0]);
        $dataFinal   = gCorrigeDataInverte($data[1]);

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
        * Botões (Versão Acima do GRID)
        */
        echo <<<EOT
        <!--<div class="buttonsBottomTop">
            <button type="button" id="ven003AssociacaoVendaCompra">Associar Venda Compra</button>
        </div>-->
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#ven003AssociacaoVendaCompra').unbind('click').bind('click', function(data) {
                var \$buttons = {
                    "Sim": function() {
                        $('#{$g['divId']}').showLoading();
                        $.getJSON('/ven/ven003/grid_func-ajax/ven003AssociacaoVendaCompra/?ajax=true&dataInicial={$dataInicial}&dataFinal={$dataFinal}'  + '&callback=?', function(request) {
                            $('#{$g['divId']}').hideLoading();
                            if (!request.code) {
                                dialogConfirm('Atenção', request.data);
                            } else {
                                dialogConfirm('Associação Venda Compra', request.data);
                                divRefresh('{$g['divId']}');
                            }
                        });
                        $(this).dialog("destroy").remove();
                    },
                    "Não": function() { $(this).dialog("destroy").remove(); } 
                };
                    dialogConfirm('Associar Venda Compra?', 'Você tem certeza que deseja associar os produtos da venda com a compra no período de {$data[0]} à {$data[1]}?', \$buttons);
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

    case 'ven003GridDevolucaoRecebidas':
        $filtroObrigatorio = "Selecione uma ou mais notas fiscais.";

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => '', 'colunaExibir' => false),
            //array('colunaValor' => 'T014_Flag_Excluir', 'colunaTitulo' => '', 'colunaEditar' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D006R.D006_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'D006D.D006_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D006R.D006_Id," - ",D006R.D006_Codigo_CFOP)', 'colunaTitulo' => 'CFOP Entrada', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('CFOP', '/fis/fis001/content/fis001content02', 'D006R.D006_Id', '1000,583')),
            array('colunaValor' => 'concat(D006D.D006_Id," - ",D006D.D006_Codigo_CFOP)', 'colunaTitulo' => 'CFOP Devolução', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('CFOP', '/fis/fis001/content/fis001content02', 'D006R.D006_Id', '1000,583')),
            array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '3', 'style' => 'background-color: var(--celula-amarelo-claro); width: 1%;','colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T014_Quantidade_Falta_Lancar(T014_Id)', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '3', 'style' => 'background-color: var(--celula-amarelo-claro); width: 1%;', 'colunaAlinhar' => 'right'),            
            array('colunaValor' => 'T014_Quantidade_Devolucao', 'colunaTitulo' => 'Qtd Devolução', 'colunaEditar' => true, 'colunaCasasDecimais' => '3', 'style' => 'background-color: var(--celula-verde); width: 100px; max-width:100px; min-width:100px;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Custo_Unitario', 'colunaTitulo' => 'Preço Unitário', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
            array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Preço Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;'),
            array('colunaValor' => "CONCAT(T013_Numero_Nota_Fiscal, ' - ', D024_Nome_Empresa) as tituloAgrupar", 'colunaTitulo' => '', 'colunaExibir' => false, 'style' => 'width: 1%;'),
            array('colunaValor' => "(SELECT GROUP_CONCAT(DISTINCT IF(T007_Flag_Cancelada = 'S' OR T007_Flag_Inutilizado = 'S', CONCAT(T007_Numero_Nota_Fiscal, ' (canc) '), T007_Numero_Nota_Fiscal)  SEPARATOR ' | ')
                                       FROM T008
                                  LEFT JOIN T007 ON T007_Id = T008_T007_Id
                                      WHERE T008_T014_Id = T014_Id) as nfGeradas", 'colunaTitulo' => 'Notas geradas'),
            //array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre'),
        );
        
        /**
        * Definição da SQL
        */
        $from = 'T014';
        $extra = <<<EOT
            left join D009 on D009_Id=T014_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            LEFT JOIN D006 as D006R ON D006R.D006_Id=T014_D006_Id
            LEFT JOIN D006 as D006D ON D006D.D006_Id=D006R.D006_D006_Id
            LEFT JOIN T013 ON T013_Id=T014_T013_Id
            LEFT JOIN D024 ON D024_Id=T013_D024_Id
            WHERE T013_C004_Id = '{$g['empresaAtual']}'
            AND T013_Flag_Cancelada != 'S'
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
            'T013_Numero_Nota_Fiscal' => array('titulo' => 'Número Nota Fiscal', 'tipo' => 'numero'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'NF', 'campoAgrupar' => 'tituloAgrupar', 'campoMostrar' => 'tituloAgrupar', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
            )),
        );
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $T013_Numero_Nota_Fiscal = $g['ui']->getGridFiltroCampoBusca('T013_Numero_Nota_Fiscal');

        /**
         * Botões Almir
         */
        $seNotaFiscalMostra = (!empty($T013_Numero_Nota_Fiscal)) ? true : false;
        $botoes = array(
			array('titulo' => 'Gerar Nota de Devolução', 'id' => 'ven003DevolucaoRecebidasGerar',  'exibirCallback' => "return $seNotaFiscalMostra;"),
		);
		echo Botoes::processar($botoes);

        /*if (!empty($T013_Numero_Nota_Fiscal)) {
            /**
            * Botões (Versão Acima do GRID)
            */
            /*echo '<div class="buttonsBottomTop">';

                echo '<button type="button" id="ven003DevolucaoRecebidasGerar">Gerar Nota de Devolução</button>';

            echo '</div>';*/
            
            echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $('#ven003DevolucaoRecebidasGerar').unbind('click').bind('click', function(data) {
                        $.ajax({
                            url: '/ven/ven003/grid_func-ajax/verificarGerarNotaDevolucaoRecebidas/',
                            type: 'post',
                            dataType: 'text',
                            data: {
                                ajax: true,
                                sql: $('#{$g['divId']} .divToScroll').attr('gridsql'),
                                callback: 'retorno'
                            },
                            success: function(response) {
                                var request = JSON.parse(response.replace(/^[^(]*\(/, '').replace(/\);?\s*$/, ''));
                                if (!request.code) {
                                    dialogConfirm('Erro', request.data);
                                } else {
                                    // Procurar todos os T013 envolvidos na consulta
                                    var listaT013s = [];
                                    $('#{$g['divId']} tr.linha').each(function() {
                                        var id = $(this).attr('id').split('|');
                                        var T013_Id = id[0].replace('T013_Id-', '');
                                        if (jQuery.inArray(T013_Id, listaT013s) < 0) {
                                            listaT013s.push(T013_Id);
                                        }
                                    });
                                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Nota Devolução', '/ven/ven003/content/ven003ContentDevolucaoNotaRecebida/', '&tab=geral&listaT013s=' + encodeURIComponent(JSON.stringify(listaT013s)) + '&divIdAberto=' + encodeURIComponent('{$g['divId']}'), [600,215]);
                                }
                            }
                        });
                    });
                } </script></div>
EOT;
        //}

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
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').split('|');
                var T013_Id = id[0].replace('T013_Id-', '');
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal', '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + T013_Id + '&tabela=T013', 'auto', false, false);
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

    case 'ven003GridEventosNFe':
        /**
        * Eventos posteriores da NF-e. O cabeçalho é comum (T321); cada
        * modalidade de evento terá seus dados próprios em tabela específica.
        */
        $grid = array(
			array('colunaValor' => 'T321_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T321_T007_Id', 'colunaTitulo' => 'T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'EXISTS (SELECT 1 FROM T321 AS Cancelamento WHERE Cancelamento.T321_Tipo_Evento = \'110001\' AND Cancelamento.T321_T321_Evento_Original_Id = T321.T321_Id) AS Possui_Cancelamento', 'colunaTitulo' => 'Possui cancelamento', 'colunaExibir' => false),
			array('colunaValor' => 'T321_T321_Evento_Original_Id', 'colunaTitulo' => 'Evento original', 'colunaExibir' => false),
            // Mantém o tipo no identificador técnico da linha para que o clique
            // saiba se existe uma tela de detalhes específica para o evento.
            array('colunaValor' => 'T321_Tipo_Evento', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'descricaoEventoNFe', 'colunaTitulo' => 'Evento', 'colunaTipo' => 'livre', 'callback' => 'ven003DescricaoEventoNFe'),
			array('colunaValor' => 'T321_Status', 'colunaTitulo' => 'Status', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T321_Sequencial_Evento', 'colunaTitulo' => 'Seq.', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T321_Protocolo', 'colunaTitulo' => 'Protocolo', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T321_Data_Hora_Inclusao', 'colunaTitulo' => 'Inclusão', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width: 1%'),
           	array('colunaValor' => 'acoesEventoNFe', 'colunaTitulo' => 'Ações', 'colunaTipo' => 'livre', 'callback' => 'ven003AcoesEventoNFe', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'downloadXmlEventoNFe', 'colunaTitulo' => 'XML', 'colunaTipo' => 'livre', 'callback' => 'ven003BaixarXmlEventoNFe', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'CONCAT(IFNULL(T321_Codigo_Retorno, \'\'), IF(T321_Motivo_Retorno IS NULL OR T321_Motivo_Retorno = \'\', \'\', CONCAT(\' - \', T321_Motivo_Retorno))) AS RetornoSefaz', 'colunaTitulo' => 'Resposta SEFAZ'),
			array('colunaValor' => 'eventoOriginalNFe', 'colunaTitulo' => 'Referente ao evento', 'colunaTipo' => 'livre', 'callback' => 'ven003EventoOriginalNFe', 'style' => 'width: 1%'),

        );

        $from = 'T321';
        $extra = <<<EOT
			LEFT JOIN C007 ON C007_Id = T321_C007_Id
            WHERE T321_T007_Id = '{$r_acaoId}'
            ORDER BY T321_Data_Hora_Inclusao DESC, T321_Id DESC
EOT;

        function gridLinha($array) {
            if ($array['todosCampos']['T321_Status'] == 'AUTORIZADO') {
                return 'linhaSuccess';
			}

			if ($array['todosCampos']['T321_Status'] == 'CANCELADO') {
				return 'linhaVermelho';
            }
        }

        echo <<<EOT
            <div class="buttonsBottomTop">
                <button type="button" id="ven003IncluirEventoNFe">Incluir evento</button>
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#ven003IncluirEventoNFe').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir evento NF-e', '/ven/ven003/content/ven003contentIncluirEventoNFe/', '&T007_Id={$r_acaoId}', [640, 150]);
                });
            } </script></div>
EOT;

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                function ven003CriarCancelamentoEventoNFe(event, T321_Id) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    if (!confirm('Deseja criar o evento de cancelamento desta ocorrência?')) {
                        return false;
                    }

                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003CriarCancelamentoEventoNFe/?ajax=true&T321_Id=' + encodeURIComponent(T321_Id) + '&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        dialogConfirm(request.code ? 'Cancelamento de evento' : 'Não foi possível cancelar o evento', request.data);
                        if (request.code) {
                            divRefresh('{$g['divId']}', true);
                        }
                    });

                    return false;
                }

                function ven003BaixarXmlEventoNFe(event, T321_Id) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    window.location.href = '/ven/ven003/grid_func-ajax/ven003BaixarXmlEventoNFe/?ajax=true&T321_Id=' + encodeURIComponent(T321_Id);
                    return false;
                }

                function ven003EnviarEventoNFe(event, T321_Id) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003EnviarEventoNFe/?ajax=true&T321_Id=' + encodeURIComponent(T321_Id) + '&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        dialogConfirm(request.code ? 'Evento transmitido' : 'Transmissão do evento', request.data);
                        divRefresh('{$g['divId']}', true);
                    });

                    return false;
                }

                $('#{$g['divId']} .grid tbody tr').unbind('click.eventoNFe').bind('click.eventoNFe', function(data) {
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }

                    var identificadorLinha = $(this).closest('tr').attr('id');
                    var id = identificadorLinha.split('|')[0].replace(/T321_Id-/, '');
                    var tipoEvento = (identificadorLinha.match(/(?:^|\|)T321_Tipo_Evento-([^|]+)/) || [])[1];

                    // O 110001 não possui itens próprios. Somente o 112140 abre
                    // a tela de vínculo/listagem dos itens não fornecidos.
                    if (tipoEvento != '112140') { return true; }

                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Evento NF-e', '/ven/ven003/content/ven003contentEvento112140/', '&T321_Id=' + encodeURIComponent(id), [950, 600]);
                });
            } </script></div>
EOT;

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'ven003GridEvento112140Itens':
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T322_Id', $g['divId'])),
            array('colunaValor' => 'T322_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T322_NItem', 'colunaTitulo' => 'Item', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008Origem.T008_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%'),
			array('colunaValor' => 'T008Origem.T008_Descricao_Produto', 'colunaTitulo' => 'Descrição do produto'),
			array('colunaValor' => 'T008Origem.T008_Quantidade', 'colunaTitulo' => 'Qtd. original', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T322_Quantidade_Nao_Fornecida', 'colunaTitulo' => 'Qtd. não fornecida', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T322_Unidade_Nao_Fornecida', 'colunaTitulo' => 'Unidade', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008AOrigem.T008A_Valor_IBS', 'colunaTitulo' => 'IBS origem', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T322_Valor_IBS', 'colunaTitulo' => 'IBS informado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
			array('colunaValor' => 'T008AOrigem.T008A_Valor_CBS', 'colunaTitulo' => 'CBS origem', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
			array('colunaValor' => 'T322_Valor_CBS', 'colunaTitulo' => 'CBS informado', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)'),
        );
        $from = 'T322';
		$extra = "LEFT JOIN T008 AS T008Origem ON T008Origem.T008_Id = T322_T008_Id_Origem LEFT JOIN T008A AS T008AOrigem ON T008AOrigem.T008A_T008_Id = T008Origem.T008_Id WHERE T322_T321_Id = '{$r_T321_Id}' ORDER BY T322_NItem";
		$evento = mysql_fetch_assoc(mysql_query("SELECT T321_Status
			FROM T321
			WHERE T321_Id = '{$r_T321_Id}'"));
		$podeAlterarItens = !empty($evento) && $evento['T321_Status'] == 'RASCUNHO';

		$botoes = array(
			array(
				'titulo' => 'Incluir produto',
				'id' => 'ven003IncluirItemEvento112140',
				'janelaUrl' => '/ven/ven003/content/ven003contentIncluirItemEvento112140/',
				'janelaParametros' => '&T321_Id=' . urlencode($r_T321_Id),
				'janelaTamanho' => '[500,210]',
				'exibir' => $podeAlterarItens,
			),
			array('titulo' => 'Incluir todos', 'id' => 'ven003IncluirTodosItensEvento112140', 'exibir' => $podeAlterarItens),
			array('titulo' => 'Incluir por percentual', 'id' => 'ven003IncluirPercentualItensEvento112140', 'exibir' => $podeAlterarItens),
		);
		echo Botoes::processar($botoes);

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                function ven003IncluirItensEvento112140PorPercentual(percentual) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003IncluirItensEvento112140PorPercentual/?ajax=true&T321_Id={$r_T321_Id}&percentual=' + encodeURIComponent(percentual) + '&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        dialogConfirm(request.code ? 'Itens incluídos' : 'Não foi possível incluir os itens', request.data);

                        if (request.code) {
                            divRefresh('{$g['divId']}', true);
                        }
                    });
                }

                $('#ven003IncluirTodosItensEvento112140').unbind('click').bind('click', function() {
                    if (confirm('Incluir todos os itens disponíveis com 100% do saldo?')) {
                        ven003IncluirItensEvento112140PorPercentual(100);
                    }
                });

                $('#ven003IncluirPercentualItensEvento112140').unbind('click').bind('click', function() {
                    var percentual = prompt('Informe o percentual a incluir para cada item disponível:', '100');
                    if (percentual !== null && $.trim(percentual) !== '') {
                        ven003IncluirItensEvento112140PorPercentual(percentual);
                    }
                });
            } </script></div>
EOT;
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']); $g['smarty']->assign('tbody', $gridTabela['tbody']); $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'ven003GridCCe':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T199_Id', $g['divId'])),
            array('colunaValor' => 'T199_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true),
            array('colunaValor' => 'T199_Flag_Enviado', 'colunaTitulo' => 'T199_Flag_Enviado', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'T007_Flag_Cancelada', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T199_T007_Id', 'colunaTitulo' => 'T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T199_Sequencial_Evento', 'colunaTitulo' => 'Evento', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T199_Data_Hora_Inclusao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width: 1%'),
            array('colunaValor' => 'T199_Correcao', 'colunaTitulo' => 'Correção'),
            array('colunaValor' => 'CONCAT(T200_Status,"-",T200_Motivo) as RetornoSefaz', 'colunaTitulo' => 'Resposta SEFAZ', 'style' => 'width: 1%', 'callback' => 'ven003LogCCe'),
            array('colunaValor' => 'DownloadXML', 'colunaTitulo' => 'Download XML','colunaTipo' => 'livre','callback' => 'ven003DownloadXmlCce', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'acaoCCe', 'colunaTitulo' => '', 'callback' => 'acaoCCe', 'style' => 'width: 1%', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'T199_Data_Envio_Email_Cce', 'colunaTitulo' => 'T199_Data_Envio_Email_Cce', 'colunaExibir' => false),
        );


        /**
        * Definição da SQL
        */
        $from = 'T199';
        
        $extra = <<<EOT
            LEFT JOIN C007 ON C007_Id=T199_C007_Id
            left join T007 on T007_Id=T199_T007_Id
            LEFT JOIN T200 ON T200_Id = (SELECT T200_Id FROM T200 WHERE T199_Id = T200_T199_Id ORDER BY T200_Id DESC LIMIT 1)
            WHERE T199_T007_Id='{$r_acaoId}'
            GROUP BY T199_Id
            ORDER BY T199_Sequencial_Evento
EOT;
        function gridLinha($array) {
            if($array['todosCampos']['T199_Flag_Enviado']=='S'){
                return 'linhaSuccess';
            }
        }
        /**
        * Botões (Versão Acima do GRID)
        */

        echo <<<EOT
            <div class="buttonsBottomTop">
                <button type="button" id="ven003IncluirCCe">Incluir CC-e</button>
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {      
                $('#ven003IncluirCCe').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Carta de Correção Eletrônica', '/ven/ven003/content/ven003contentIncluirCCe/','&T007_Id={$r_acaoId}', [500,350]);
                });
            } </script></div>
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
            'T199_Id' => array('titulo' => 'Data', 'tipo' => 'texto'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        $ErroCCe = gGeraAlertMsg('Erro', 'Esta CCe ainda não foi emitida!', false);
        $ErroCancelada = gGeraAlertMsg('Erro', 'Esta nota foi cancelada!', false);
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                // pega o ID da linha (verifica as chave(s) primaria(s) do grid)
                var id = $(this).closest('tr').attr('id').split('|');
                var T199_Id = id[0].replace(/T199_Id-/, '');
                var T199_Flag_Enviado = id[1].replace(/T199_Flag_Enviado-/, '');
                var T007_Flag_Cancelada = id[2].replace(/T007_Flag_Cancelada-/, '');
                if(T199_Flag_Enviado == 'S' && T007_Flag_Cancelada=='N'){
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003ImprimirCce/?ajax=true&T199_Id=' + encodeURIComponent(T199_Id) + '&callback=?', function(request) {
                        window.open(request.data);
                    });
                }else{
                    if(T007_Flag_Cancelada=='S'){
                        {$ErroCancelada}
                    }else{
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Carta de Correção Eletrônica', '/ven/ven003/content/ven003contentIncluirCCe/','&acaoId=' + encodeURIComponent(T199_Id), [500,350]); 
                    }
                }
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

    case 'ven003GridProdutosNotaConsumidor':
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
        $sqlT007 = mysql_query("SELECT T007_Numero_Protocolo_Nfe FROM T007 WHERE T007_Id = '{$acao_Id}'");
        $mT007 = mysql_fetch_array($sqlT007);
        if(empty($mT007['T007_Numero_Protocolo_Nfe'])) {
            $inclusaoMultiProdutos=array('T008_T007_Id'=>$r_T007_Id);
        }

        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId'])),
            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'callbackParameter' => array(
                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => "(IF(T006_Quantidade_Estoque< T006_Quantidade, CONCAT('<span style=\"color: var(--BGVermelho);\">',REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ','),'</span>'), REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ',')))", 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_Estoque as Pedido', 'colunaTitulo' => 'Estoq', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T006_Quantidade'), 'colunaExibir' => false),
            array('colunaValor' => 'T008_T066_Id', 'colunaTitulo' => 'Locação', 'callback' => 'T008_T066_Id_Local', 'colunaAlinhar' => 'center', 'colunaExibirCodigo' => '$row = mysql_fetch_assoc(mysql_query("SELECT T007_T005_Id FROM T007 WHERE T007_Id = \'$r_acaoId\'")); return ($row{\'T007_T005_Id\'} > 0) ? false : true;'),
            array('colunaValor' => 'T008_D009_Id', 'colunaTitulo' => 'T008_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
            array('colunaValor' => 'D006_Flag_Estoque', 'colunaTitulo' => 'D006_Flag_Estoque', 'colunaExibir' => false),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Local', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_alteracaoGrid'),
            //array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T008_D006_Id', 'colunaTitulo' => 'CFOP', 'colunaEditarGeraSelect' => array('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', ' ORDER BY D006_Codigo_CFOP ', true), 'colunaEditar' => true, 'callback' => 'T008_D006_Id_CFOP'),
            array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'T008_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outros/demonstrativoPrecoVenda/', 'T006_Id', 'bottom', 4, 'T006', '', '560'), 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'T008_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T008_Percentual_Desconto', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_alteracaoGrid'),
            array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            /*array('colunaValor' => 'T008_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T008_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T008_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV SL', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T008_Valor_Lucro', 'colunaTitulo' => 'R$ Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => '(T008_Valor_Lucro/T008_Valor_Total_Preco)*100', 'colunaTitulo' => '% Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T008_D006_Id_Info', 'Demonstrativo CFOP', 'T008_Id', 'bottom', '', '500')),
            array('colunaValor' => 'T008_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'IF(IFNULL(D149_Id,0) > 0, concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao), "????????") as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T008_D005_Id_Info', 'Demonstrativo NCM', 'T008_Id', 'bottom', '', '500')),
            array('colunaValor' => 'substring(T008_Flag_Situacao_Tributaria,1,1)', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'T008_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'T008_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_ICMS_ST', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => 'PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => 'COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D149_Valor_VA', 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_ST_VA', 'colunaTitulo' => 'MVA Ajust', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T008_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_Base_PIS_Retido', 'colunaTitulo' => 'Base PIS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_PIS_Retido', 'colunaTitulo' => 'Valor PIS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_Base_COFINS_Retido', 'colunaTitulo' => 'Base COFINS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008A_Valor_COFINS_Retido', 'colunaTitulo' => 'Valor COFINS Ret.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T008_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true),                
            //array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Percentual_Comissao', 'colunaTitulo' => '% Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T008_Valor_Comissao', 'colunaTitulo' => 'Valor comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605'), 'colunaExibirNivel' => 4),

            array('colunaValor' => 'T008_Valor_Total_Preco', 'colunaTitulo' => 'T008_Valor_Total_Preco', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '(((T008_Valor_Total_Preco/(T008_Valor_Custo_Unitario*T008_Quantidade))-1)*100)', 'colunaTitulo' => 'Margem', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'colunaTotal' => 'S', 'colunaTotalFormula' => 'return gCorrigeNumero((($t0/$t1)-1)*100);', 'colunaExibir' => false),
            array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'T001_Id', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),*/
        );
        
        /**
         * Definição da SQL
         */
        $from = "T008";
        
        $extra = <<<EOT
            left join T007 on T007_Id=T008_T007_Id
            left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D006 on D006_Id=T008_D006_Id
            left join D037 on D037_Id=T008_D037_Id
            left join T006 on T008_T006_Id=T006_Id
            left join T005 on T005_Id=T006_T005_Id
            left join D005 on D005_Id=T008_D005_Id
            left join D149 on D149_D005_Id=D005_Id AND D149_C004_Id=T007_C004_Id
            left join T066 on T066_Id=T008_T066_Id
            left join D004 on D004_Id=T066_D004_Id
            left join T219 ON T219_Id=T006_T219_Id
            left join T218 ON T218_Id=T219_T218_Id
            LEFT JOIN T001 ON T001_T008_Id=T008_Id
            LEFT JOIN C004 ON C004_Id=IFNULL(T006_C004_Id_Estoque,T005_C004_Id)
            where T008_T007_Id = '{$acao_Id}'
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
                function colunaEditarCallbackJS_alteracaoGrid(T008_Id, campoEditado, valorAntigo, novoValor) {
                    T008_Id = T008_Id.replace(/T008_Id-/, '');                                    
                    divRefresh('{$g['divId']}', true, false, false, T008_Id, 'T008_Id'); // grid
                    divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
                    divRefresh('{$r_divReload}');
                }

                var dbclickPrevent = false;
                $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Previne duplo click (abrir duas janelas)
                    if (dbclickPrevent) { return false; }
                    dbclickPrevent = true;
                    setTimeout(function() { dbclickPrevent = false; }, 500);
                    // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'T008_Id';  
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/ven/ven003/content/ven003ContentProdutoEditar/', '&tabela=T008&acaoId=' + encodeURIComponent(acaoId) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [650,500]);
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
        
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

    case 'ven003GridTransmitirNotaFiscal':
        $T007_Id = $r_acaoId;
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'campoInutilizar', 'colunaTipo' => 'livre', 'callback' => 'validaCampoInutilizar', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Enviar', 'callback' => 'campoMarcarEnviar', 'style' => 'width: 1%;', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Saida', 'colunaTitulo' => 'Saída', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão<br/>Saída', 'callback' => 'filaEnvioData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T007_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaExibir' => false),
            array('colunaValor' => 'PedNfe', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Pedido<br/>NF-e', 'callback' => 'filaEnvioPedNfe', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'IF(T007_Flag_ACP=6,\'<b>NF-e</b>\', \'<b>NFC-e</b>\')', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Observacao_Nota_Fiscal_1', 'colunaTitulo' => 'Obs. Fiscal', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Cliente_Razao', 'colunaTitulo' => 'Cliente<br/>Observação Fiscal', 'callback' => 'filaEnvioCliente', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-size:12px;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(T007_Chave_Acesso_Nfe,\'<br /> \',\'Rec: \',T007_Numero_Recibo_Nfe,\'  \',\'Prot: \',\'<b>\',T007_Numero_Protocolo_Nfe,\'</b>\')', 'colunaTitulo' => 'Chave de Acesso / Recibo / Protocolo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'callback' => 'quebra10', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Resposta_Codigo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Resposta_Extra', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Retorno_Sefaz', 'colunaExibir' => false),
            array('colunaValor' => 'T007_D024_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007_TX_Flag_Finalizada', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaExibir' => false),
            array('colunaValor' => 'txSefaz', 'colunaTitulo' => 'Transmissão NF-e Sefaz', 'callback' => 'txSefaz', 'colunaTipo' => 'livre', 'style' => 'width: 50%;', 'callbackParameter' => array('imprimirAutomatico' => true)),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Numero_Protocolo_Nfe'),
            array('colunaValor' => 'T007_Flag_Almoxarifado', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_Almoxarifado'),
            array('colunaValor' => 'T007_Flag_Almoxarifado', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_Almoxarifado'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaExibir' => false, 'colunaTitulo' => 'T007_Flag_ACP'),
        );

        if($r_naoFechaJanelaPDV){
            // apenas se vier atraves do PDV (F8) para não fechar a janela do PDV ao transmitr no callback txSefaz
            $grid[] =  array('colunaValor' => 'naoFechaJanelaPDV', 'colunaTipo' => 'livre' ,'colunaExibir' => false, 'colunaTitulo' => 'naoFechaJanelaPDV');
        }

        function gridLinha($array) {
            global $g;
            // ...
            if ($array['todosCampos']['T007_TX_Resposta_Codigo'] < 100) {
                if ($array['todosCampos']['T007_TX_Resposta_Codigo'] == 9) {
                    return 'linhaSuccess';
                } else {
                    return '';
                }
            } else {
                return 'linhaErrorNoColor';
            }
        }
        /**
        * Definição da SQL
        */
        $from = 'T007';

        $extra = <<<EOT
            WHERE T007_Id = '{$T007_Id}'
            ORDER BY T007_Numero_Nota_Fiscal
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
        $g['smarty']->assign('class', 'naoProcessarThead');
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

        $novoIdTelaForm = uniqid();
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
                });
            } </script></div>
EOT;
        $row = mysql_fetch_assoc(mysql_query("SELECT T007_TX_Resposta_Codigo FROM T007 WHERE T007_Id = '{$T007_Id}'"));
        $mC020 = mysql_fetch_assoc(mysql_query("SELECT C020_NFe_SVC_Ativado FROM C020 WHERE C020_C004_Id='{$g['empresaAtual']}'"));

        if($row['T007_TX_Resposta_Codigo'] == '0'){
            if($mC020['C020_NFe_SVC_Ativado'] == 'N') {
                echo <<<EOT
                    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $('.btnEnviarNFE').trigger('click');
                    } </script></div>
                EOT;
            } else {
                echo <<<EOT
                    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $('.btnEnviarNFeSVC').trigger('click');
                    } </script></div>
                EOT;
            }
        }
    break;

    case 'ven003GridPagamentosPDV':
        $acao_Id = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'T118_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            //array('colunaValor' => 'T118_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
            //array('colunaValor' => 'T118_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T118_Prazos_Dias"),
            //array('colunaValor' => 'T118_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T118_Prazos_Dias"),
            //array('colunaValor' => 'ven003_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "ven003_Dia_Semana"),
            array('colunaValor' => 'T118_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => false, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'style' => 'width:65%;font-size:16px', 'order by D027_Portador', true), 'callback' => 'T118_D027_Id_Portador', 'colunaEditarJSCallback'=>'colunaEditarCallbackJS_T118_Prazos_Dias'),
            array('colunaValor' => 'T118_Valor_Parcela', 'colunaTitulo' => 'Valor Recebido', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2', 'colunaAlinhar' => 'right', 'style' => 'width:35%;font-size:16px', 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T118_Prazos_Dias"),
            array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
            array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
            //array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven003_Detalhes"),
        );

        /**
        *
        * Definição da SQL
        */
        $from = 'T118';

        $extra = <<<EOT
            left join D027 on D027_Id=T118_D027_Id
            WHERE T118_T007_Id = {$acao_Id}
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
                var id = $(this).closest('tr').attr('id').replace(/T118_Id-/, '');
                divSetarAcaoId('{$r_divReload}', id);
                divRefresh('{$r_divReload}');
            });

            // Função JS executada como callback do editar campo (colunaEditarJSCallback)
            function colunaEditarCallbackJS_T118_Prazos_Dias(T118_Id, campoEditado, valorAntigo, novoValor) {
                divRefresh('{$r_divReload}');
                if (campoEditado == 'T118_Valor_Parcela') {
                    divRefresh('{$r_divReload}');
                }
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

    case 'ven003GridConhecimentosNF':
        global $g;

        $filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
        
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirNFe', 'colunaExibir'=>false),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/notafiscal/', 'T007_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 330), 'style' => 'width: 1%;', 'colunaExibir'=>false),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'style' => 'width:1%;font-size:10px', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => '', 'callback' => 'campoMarcarNFImpressao', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'M|S', 'colunaExibir'=>false),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo'),
            array('colunaValor' => 'T005_Numero_Pedido_Venda', 'colunaTitulo' => 'N Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razao Social'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaDimensao' => 'S', 'colunaExibir'=>false),
            array('colunaValor' => 'concat(CONVERT(substr(Pedido.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(Pedido.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(Pedido.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:170px;white-space:normal;min-width:170px;width:170px', 'colunaExibir'=>false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T007_Prazos(T007_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%', 'colunaExibir'=>false),
            array('colunaValor' => 'T007_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T007_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'Mail', 'callback' => 'validaCampoEmail', 'colunaExibir' => false),
            array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDanfe'),
            array('colunaValor' => 'NF', 'colunaTitulo' => 'NF', 'colunaTipo' => 'livre', 'callback' => 'validaCampoNF', 'colunaExibir' => false),
            array('colunaValor' => 'ACP', 'colunaTitulo' => 'ACP', 'colunaTipo' => 'livre', 'callback' => 'validaCampoACP', 'colunaExibir' => false),
            array('colunaValor' => 'CCe', 'colunaTitulo' => 'CC-e', 'colunaTipo' => 'livre', 'callback' => 'enviarCCe', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar', 'style' => 'width:20px', 'colunaExibir' => false),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre', 'colunaExibir' => false),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaDimensao' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInternoNF', 'colunaEditarNivel' => 4, 'style' => 'width:1%;background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Percentual_Comissao', 'colunaTitulo' => '%Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => (isset($g['c029Ids'][201])) ? true : false, 'style' => 'background:var(--celula-amarelo-claro)', 'colunaEditarJSCallback' => 'colunaEditarPercentualComissao', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Total_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T007_D021_Id', 'colunaTitulo' => 'Representante', 'colunaEditarGeraSelect' => array('D021', 'D021_Id', 'D021_Nome_Empresa','',true), 'colunaEditarOculto' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D021_Nome_Empresa', 'colunaEditarJSCallback' => "colunaEditarRepresentante", 'style' => 'background:var(--celula-verde)', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Percentual_Comissao_Representante', 'colunaTitulo' => '%Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => (isset($g['c029Ids'][202])) ? true : false, 'style' => 'background:var(--celula-verde)', 'colunaEditarJSCallback' => 'colunaEditarPercentualComissao', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Comissao_Representante', 'colunaTitulo' => 'Comissão R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)', 'colunaExibir' => false),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
            // array('colunaValor' => 'D022_Taxa_Frete','colunaTitulo' => '% Negociado','colunaAlinhar' => 'right','callback' => 'TaxaFreteDeTransportadora', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Frete', 'colunaTitulo' => 'Tipo Frete', 'style'=>'width:1%;'),
            array('colunaValor' => 'T007_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'T007_Valor_Total_ICMS', 'colunaTitulo' => 'ICM', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Total_PIS', 'colunaTitulo' => 'PIS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Total_COFINS', 'colunaTitulo' => 'COFINS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'Retorno Sefaz', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Retorno SEFAZ', 'callback' => 'chamaCampo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Protocolo_Cancelamento_Nfe', 'colunaTitulo' => 'Prot. Cancel', 'callback' => 'validaCampoXMLCancelado', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'callback' => 'ValidaChaveAcesso', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Desconto', 'colunaTitulo' => 'Desc.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir' => false),
            //array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'XML', 'colunaExibir' => false),
            array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Protocolo_Inutilizacao', 'colunaTitulo' => 'Prot.Inutilizacao', 'colunaExibir' => false),
            //array('colunaValor' => 'T007_Flag_Gerar_Conhecimento', 'colunaTitulo' => 'Conhecimento', 'callback'=>'campoMarcarConhecimento', 'style'=>'width:1%;'),
            array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Conhecimento', 'colunaExibir' => true),
            array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData', 'style'=>'width:1%;'),
            array('colunaValor' => 'T084_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData', 'style'=>'width:1%;'),
            // array('colunaValor' => '(T007_Valor_Total*(D022_Taxa_Frete/100))','colunaTitulo' => 'Projetado R$','colunaCasasDecimais' => '2','colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Valor_Frete_Calculado','colunaTitulo' => 'Calculado R$','colunaCasasDecimais' => '2','colunaAlinhar' => 'right'),
            array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor R$', 'colunaCasasDecimais'=>'2', 'style'=>'width:1%;'),
            array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'T084_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T007A_Flag_Contingencia_Offline', 'colunaExibir' => false),
/*            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Tipos', 'colunaTitulo' => 'Tipos'),
            array('colunaValor' => '(SELECT SUM(T006_Valor_Desconto_Unitario_Bruto)
                                                FROM T006 
                                                LEFT JOIN T005 ON T006_T005_Id = T005_Id 
                                                WHERE T005_Id = T007_T005_Id) as desconto', 'colunaTitulo' => 'Desc.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaTipo' => 'banco', 'colunaExibir' => false),
*/
        );

      

        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        function gridLinha($array) {
            if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
                return 'linhaVermelho';
            }
            //Caso o valor do conhecimento não for igual ao valor calculado a linha ficará vermelha
            if($array['todosCampos']['T007_Valor_Frete_Calculado']>0){
                if(round($array['todosCampos']['T084_Valor_Conhecimento'],2) > round($array['todosCampos']['T007_Valor_Frete_Calculado'],2)){
                    return 'linhaVermelho';
                }
            }  
        }

        /**
         * Definição da SQL
         */
        $from = "T007";
        
        $extra = <<<EOT
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
            left join D021 on D021_Id=T007_D021_Id
            left join D022 on D022_Id=T007_D022_Id
            left join T085 on T085_T007_Id=T007_Id
            left join T084 on T084_Id=T085_T084_Id
            left join T007A on T007_Id = T007A_T007_Id
            where (T007_C004_Id='{$g['empresaAtual']}'
            and T007_Flag_Emitida_Recebida!='R')
            and T007_Numero_Recibo_Nfe > 0
            and (T007_Flag_Cancelada!='S')
            and (T007_Protocolo_Inutilizacao ='' OR T007_Protocolo_Inutilizacao IS NULL) 
            and T007_Flag_Denegado != 'S'
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'Pedido.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Origem', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ),
              'naoGerarExtra' => ''),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'exibir'=>false, 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Tipo produto', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Marca produto', 'tipo' => 'texto'),
            'T007_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo', 'tipo' => 'text', 'exibir' => false),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'NF-e', 'value' => '6'),
                array('title' => 'NFC-e', 'value' => '8'),
                array('title' => 'CF', 'value' => '4'),
              ),
            ),
            'T007_Prazos(T007_Id)' => array('titulo' => 'Prazo', 'tipo' => 'texto'),
            'T007_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda'),
            'T007_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda'),
            'T005_Ordem_Compra' => array('titulo' => 'OC. Cliente', 'tipo' => 'texto'),
            'T084_Numero_Conhecimento' => array('titulo' => 'Conhecimento', 'tipo' => 'texto'),
        );

        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

        /**
        * Botões (Versão Acima do GRID)
        */
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
            <div class="buttonsBottomTop">
                <!--<button type="button" id="ven003GerarConhecimento">Gerar Conhecimento</button>-->
                <!--<button type="button" id="ven003IncluirNotaFiscal">Incluir</button>-->
                <!--<button type="button" id="ven003IncluirNotaFiscalConsumidor">PDV</button>-->
                <!--<button type="button" id="ven003MarcarTodos">Marcar Todos</button>-->
                <!--<button type="button" id="ven003DesmarcarTodos">Desmarcar Todos</button>-->
                <!--<button type="button" id="ven003Inutilizar">Inutilizar Numeração</button>-->
                <!--<button type="button" id="ven003ImportarXML">Importar XML</button>-->
                <!--<button type="button" id="ven003GerarLoteRps">Gerar Lote Rps</button>-->
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {      
                $('#ven003GerarConhecimento').unbind('click').bind('click', function(data) {
                    var \$buttons = {
                        "Sim": function() {
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/ven/ven003/grid_func-ajax/ven003GerarConhecimento/?ajax=true&extra={$extra_}&T003_Id=' + encodeURIComponent() + '&callback=?', function(request) {
                                $('#{$g['divId']}').hideLoading();
                                if (request.code) {
                                    divRefresh('{$g['divId']}');
                                    dialogConfirm('Erro', request.data);
                                } else {
                                    dialogConfirm('Erro', request.data);
                                }
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); } 
                    };
                    dialogConfirm('Gerar Conhecimento', 'Você tem certeza que deseja gerar conhecimento das notas seleiconadas?', \$buttons);
                }); 

                $('#ven003IncluirNotaFiscal').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','', [900,550]);
                });

                $('#ven003ImprimirNFeMarcada').unbind('click').bind('click', function(data) {
                    nova_janela('/ven/ven003/grid_func-ajax/ven003ImprimirMarcadas/?ajax=true',0,0,750,500,'yes','no','no','no','yes','no','Impressao Danfe');
                });
                $('#ven003Inutilizar').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Inutilizar Numeração', '/ven/ven003/content/ven003ContentInutilizarNumeracao/','', [500,450]);
                });
                $('#ven003ImportarXML').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Inutilizar Numeração', '/ven/ven003/content/ven003ContentImportarXML/','', [500,120]);
                });
                $('#ven003GerarLoteRps').unbind('click').bind('click', function(data) {
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003gerarLoteRps/?ajax=true&callback=?', function(request) {
                        if (request.code) {
                            dialogConfirm('Operação Ok', request.data);
                            divRefresh('{$g['divId']}', true);
                        } else {
                            dialogConfirm('Erro', request.data);
                        }
                    });
                });
                
                $('#ven003IncluirNotaFiscalConsumidor').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003incluirNotaFiscalConsumidor/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code) { 
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('PDV'), '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','&acaoId=' + encodeURIComponent(request.data), [1000,650]);
                        } else {
                            dialogConfirm('Erro', request.data, false);
                        }
                    });
                });
            } </script></div>
EOT;
        }

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Produtos', 'tipo'=>'totais', 'valor' => 'T007_Valor_Total_Produtos', 'titulo' => 'Valor Produtos'),
            array('nome' => 'Valor_IPI', 'tipo'=>'totais', 'valor' => 'T007_Valor_Total_IPI', 'titulo' => 'Valor IPI'),
            array('nome' => 'Valor_ICM', 'tipo'=>'totais', 'valor' => 'T007_Valor_Total_ICMS', 'titulo' => 'Valor ICM'),
            array('nome' => 'Valor_ST', 'tipo'=>'totais', 'valor' => 'T007_Valor_ICMS_Substituicao_Retencao', 'titulo' => 'Valor ST'),
            array('nome' => 'Valor_Desconto', 'tipo'=>'totais', 'valor' => 'T007_Valor_Desconto', 'titulo' => 'Valor Desconto'),
            array('nome' => 'Valor_Total', 'tipo'=>'totais', 'valor' => 'T007_Valor_Total', 'titulo' => 'Valor Total'),
            array('nome' => 'Valor_Comissao', 'tipo'=>'totais', 'valor' => 'T007_Valor_Total_Comissao', 'titulo' => 'Valor Comissão'),
        );

         /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        $sqlGridFiltro = base64_encode($geradoSql);
        
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {      
                $('#ven003DesmarcarTodos').unbind('click').bind('click', function(data) {
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}', true);
                        }
                    });
                });
                $('#ven003MarcarTodos').unbind('click').bind('click', function(data) {
                    $.getJSON('/ven/ven003/grid_func-ajax/ven003checkMarcarTodos/?ajax=true&sqlFiltro='+encodeURIComponent('{$sqlGridFiltro}')+'&callback=?', function(request) {
                        if (request.code) {
                            divRefresh('{$g['divId']}', true);
                        } else {
                            dialogConfirm('Erro', request.data);
                        }                                           
                    });
                });
        function colunaEditarRepresentante(T007_Id, campoEditado, valorAntigo, novoValor) {
          T007_Id = T007_Id.replace(/T007_Id-/, '');
          $('#{$g['divId']}').showLoading();
          $.getJSON('/ven/ven001/grid_func-ajax/alterarRepresentante/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + 
                                      '&campoEditado='    + encodeURIComponent(campoEditado) +
                                      '&valorAntigo='     + encodeURIComponent(valorAntigo) +
                                      '&D021_Id='       + encodeURIComponent(novoValor) +  
                                      '&callback=?', function(request) {
            $('#{$g['divId']}').hideLoading();
            if (request.code) {
              divRefresh('{$g['divId']}', true);
            } else {
              dialogConfirm('Erro', request.data);
            }
          });
        }
        function colunaEditarPercentualComissao(T007_Id, campoEditado, valorAntigo, novoValor) {
            T007_Id = T007_Id.replace(/T007_Id-/, '');
            divRefresh('{$g['divId']}', true, false, false, T007_Id, 'T007_Id'); // grid
        }
            } </script></div>
EOT;

        /**
        * Botões (Versão Acima do GRID)
        */
        /* if (!personalizacaoTotais()) { 
            $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(*),
                                                      sum(T007_Valor_Total_Produtos),
                                                      sum(T007_Valor_Total),
                                                      sum(T007_Valor_Total_Comissao),
                                                      sum(T007_Valor_Total_IPI),
                                                      sum(T007_Valor_Total_ICMS),
                                                      sum(T007_Valor_ICMS_Substituicao_Retencao),
                                                      sum(T007_Valor_Desconto)", 
                                                      $from, 
                                                      $extra);

            list($valorTotal,$valorTotal1,$valorTotal2,$valorTotal3,$valorIPI,$valorICM,$valorST,$valorDesc) = $sqlTotal;


            $valorTotal1 = gCorrigeNumero($valorTotal1);
            $valorTotal2 = gCorrigeNumero($valorTotal2);
            $valorTotal3 = gCorrigeNumero($valorTotal3);
            $valorIPI = gCorrigeNumero($valorIPI);
            $valorICM = gCorrigeNumero($valorICM);
            $valorST = gCorrigeNumero($valorST);
            $valorDesc = gCorrigeNumero($valorDesc);
            
            echo $totais = <<<EOT
                <div class="buttonsBottomTop">
                    <span class="item" style="text-align:right">Notas Emitidas..<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor IPI<br><input type="text" value="{$valorIPI}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor ICMS<br><input type="text" value="{$valorICM}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor ST<br><input type="text" value="{$valorST}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor Desconto<br><input type="text" value="{$valorDesc}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
                    <div style="clear:both;"></div>
                </div>
EOT;
      $g['smarty']->assign('totais', $totais);
        }   */   
       
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
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

    case 'ven003GridLogCCe':
        
        $T199_Id = (isset($r_T199_Id)) ? $r_T199_Id : false;
        
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T200_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'T200_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'exibir' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T200_T199_Id', 'colunaTitulo' => 'CC-e', 'chavePrimaria' => true, 'colunaExibir' => false, 'style' => 'width:1%'),
			array('colunaValor' => 'T200_Data_Hora_Inclusao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T200_Status', 'colunaTitulo' => 'Status', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T200_Motivo', 'colunaTitulo' => 'Motivo'),

		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T200";
				
		$extra = <<<EOT
            WHERE T200_T199_Id = '{$T199_Id}'
                   
		            	
EOT;
        
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		//$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
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

    case 'ven003GridCiap':

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T316_Id', $g['divId'])),
			array('colunaValor' => 'T316_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=> true, 'style' => 'width: 1px;'),		
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir'=> false, 'style' => 'width: 1px;'),							
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cadastro Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,600'), 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo); width: 1px;'),				
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T316_Codigo_Produto_CIAP', 'colunaTitulo' => 'Código Escrituração', 'style' => 'background: var(--celula-verde);  width: 1px;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T316_Descricao_Utilizacao_Bem', 'colunaTitulo' => 'Descrição Utilização Bem', 'style' => 'background: var(--celula-verde);'),
			array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-vermelho); width: 100px;'),
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
		LEFT JOIN T008 ON T008_Id = T316_T008_Id
		LEFT JOIN T007 ON T007_Id = T008_T007_Id
		LEFT JOIN D009 ON D009_Id = T008_D009_Id 
		LEFT JOIN D049 ON D049_Id = D009_D049_Id 
		LEFT JOIN D001 ON D001_Id = D049_D001_Id 
		WHERE T008_T007_Id = '{$r_acaoId}'
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'ven003FormObrigacoesBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven003FormObrigacoesBtnIncluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro CIAP', '/ven/ven003/content/ven003ContentCadastroCiap/', '&tab=geral&T007_Id={$r_T007_Id}', [655,250]);
			});
		} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'text'),			
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
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Obrigação Tributária - ID: ' + acaoId, '/ven/ven003/content/ven003ContentCadastroCiap/', '&T316_Id=' + encodeURIComponent(acaoId) + '&T007_Id={$r_T007_Id}', [655,250]);
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

    case 'ven003GridObservacoes':
        $T007_Id = isset($r_T007_Id) ? $r_T007_Id : $r_acaoId;
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T297_Id', $g['divId'])),
            array('colunaValor' => 'T297_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D188_Codigo_Observacao', 'colunaTitulo' => 'Código'),
            array('colunaValor' => 'D188_Descricao_Observacao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T297_Descricao_Complementar_Observacao', 'colunaTitulo' => 'Descrição Complementar da Observação'),
        );
        $from = 'T297';
        $extra = "LEFT JOIN D188 ON D188_Id = T297_D188_Id WHERE T297_T007_Id = '{$T007_Id}'";
        $botoes = array(
            array('titulo' => 'Incluir', 'id' => 'ven003FormObservacaoBtnIncluir'),
            array('titulo' => 'Atualizar Observações', 'id' => 'ven003AtualizarObservacoes'),
        );
        echo Botoes::processar($botoes);
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') {
            $('#ven003FormObservacaoBtnIncluir').unbind('click').bind('click', function(data) {
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Observação', '/ven/ven003/content/ven003ContentCadastroObservacao/', '&tab=geral&T007_Id={$T007_Id}', [700,450]);
            });
            $('#ven003AtualizarObservacoes').unbind('click').bind('click', function() {
                var buttons = {
                    'Sim': function() {
                        $.getJSON('/ven/ven003/grid_func-ajax/ven003AtualizarObservacoes/?ajax=true&T007_Id=' + encodeURIComponent('{$T007_Id}') + '&callback=?', function(request) {
                            dialogConfirm(request.code ? 'Atualizar Observações' : 'Erro', request.data, {'Fechar': function() { $(this).dialog('destroy').remove(); }});
                            if (request.code) { divRefresh('{$g['divId']}', true); }
                        });
                        $(this).dialog('destroy').remove();
                    },
                    'Não': function() { $(this).dialog('destroy').remove(); }
                };
                dialogConfirm('Atualizar Observações', 'Você tem certeza?', buttons);
            });
            $('#{$g['divId']} .grid tr').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').split('|')[0].replace('T297_Id-', '');
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Observação - ID: ' + id, '/ven/ven003/content/ven003ContentCadastroObservacao/', '&acaoId=' + encodeURIComponent(id) + '&T007_Id={$T007_Id}', [700,450]);
            });
        }</script></div>
EOT;
        $gridFiltro = array('D188_Codigo_Observacao' => array('titulo' => 'Código', 'tipo' => 'text'));
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'ven003GridObrigacoes':
        $T297_Id = isset($r_acaoId) ? $r_acaoId : false;
        $T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T298_Id', $g['divId'])),
            array('colunaValor' => 'T298_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D188_Codigo_Observacao', 'colunaTitulo' => 'Código Observação'),
            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto'),
            array('colunaValor' => 'D189_Codigo_Ajuste', 'colunaTitulo' => 'Código Ajuste'),
            array('colunaValor' => 'T298_Descricao_Complementar_Ajuste', 'colunaTitulo' => 'Descrição Complementar Ajuste'),
            array('colunaValor' => 'T298_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICMS'),
            array('colunaValor' => 'T298_Aliquota_ICMS', 'colunaTitulo' => '% ICMS'),
            array('colunaValor' => 'T298_Valor_ICMS', 'colunaTitulo' => 'Valor ICMS'),
            array('colunaValor' => 'T298_Outros_Valores', 'colunaTitulo' => 'Valor Outros'),
        );
        $from = 'T298';
        $extra = "LEFT JOIN T297 ON T297_Id = T298_T297_Id LEFT JOIN D189 ON D189_Id = T298_D189_Id LEFT JOIN D188 ON D188_Id = T297_D188_Id LEFT JOIN T008 ON T008_Id = T298_T008_Id WHERE T298_T297_Id = '{$T297_Id}'";
        $botoes = array(array('titulo' => 'Incluir', 'id' => 'ven003FormObrigacoesBtnIncluir'));
        echo Botoes::processar($botoes);
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') {
            $('#ven003FormObrigacoesBtnIncluir').unbind('click').bind('click', function(data) {
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de Obrigações Tributárias', '/ven/ven003/content/ven003ContentCadastroObrigacaoTributaria/', '&T007_Id={$T007_Id}&T297_Id={$T297_Id}', [700,450]);
            });
            $('#{$g['divId']} .grid tr').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').split('|')[0].replace('T298_Id-', '');
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Obrigação Tributária - ID: ' + id, '/ven/ven003/content/ven003ContentCadastroObrigacaoTributaria/', '&acaoId=' + encodeURIComponent(id) + '&T007_Id={$T007_Id}&T297_Id={$T297_Id}', [700,450]);
            });
        }</script></div>
EOT;
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'ven003GridRelatorioPuloNumeracao':
		$grid = array(
			array('colunaValor' => 'tipoDocumento', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'serie', 'colunaTitulo' => 'Série', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'faltantes', 'colunaTitulo' => 'Faltantes', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'faixa', 'colunaTitulo' => 'Faixa faltante'),
			array('colunaValor' => 'numeroSuperior', 'colunaTitulo' => 'Número superior', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'numeroInferior', 'colunaTitulo' => 'Número inferior', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
		);

		$gridFiltro = array(
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data', 'naoGerarExtra' => true),
			'Tipo_Documento' => array(
				'titulo' => 'Tipo',
				'tipo' => 'select',
				'naoGerarExtra' => true,
				'select' => array(
					array('title' => 'Selecione', 'value' => ''),
					array('title' => 'NF-e', 'value' => '6'),
					array('title' => 'NFC-e', 'value' => '8'),
				),
			),
			'T007_Serie' => array(
				'titulo' => 'Série',
				'tipo' => 'texto',
				'naoGerarExtra' => true,
			),
		);
		$g['ui']->gridFiltroExtra(false, $gridFiltro, false);
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$periodoEmissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
		$tipoDocumentoFiltro = $g['ui']->getGridFiltroCampoBusca('Tipo_Documento');
		$serieFiltro = $g['ui']->getGridFiltroCampoBusca('T007_Serie');

		$geradoDados = array();
		$totalFaixas = 0;
		$totalNumerosFaltantes = 0;

		if (empty($tipoDocumentoFiltro) || empty($serieFiltro)) {
			echo "<div style='margin:auto;text-align:center;padding:4px;border:1px solid #1976d2;background:#e3f2fd;color:#0d47a1;width:99%;border-radius:3px;font-size:13px'>Selecione obrigatoriamente o Tipo e a Série para gerar o relatório.</div>";
		} elseif (!empty($periodoEmissao[0]) && !empty($periodoEmissao[1])) {
			$dataInicial      = gCorrigeDataInverte($periodoEmissao[0]) . " 00:00:00";
			$dataFinal        = gCorrigeDataInverte($periodoEmissao[1]) . " 23:59:59";
			$tipoDocumentoSql = in_array($tipoDocumentoFiltro, array('6', '8')) ? " AND T007_Flag_ACP = '{$tipoDocumentoFiltro}'" : " AND T007_Flag_ACP IN (6,8)";
			$serieFiltroSql   = mysql_real_escape_string($serieFiltro);

			$sqlNotas = "SELECT T007_Flag_ACP,
								T007_Serie,
								CAST(T007_Numero_Nota_Fiscal AS UNSIGNED) AS numeroNota
						   FROM T007
						  WHERE T007_C004_Id = '{$g['empresaAtual']}'
							AND T007_Data_Emissao >= '{$dataInicial}'
							AND T007_Data_Emissao <= '{$dataFinal}'
							AND CAST(T007_Numero_Nota_Fiscal AS UNSIGNED) > 0
							{$tipoDocumentoSql}
							AND T007_Serie = '{$serieFiltroSql}'
					   ORDER BY T007_Flag_ACP,
					   			T007_Serie,
								CAST(T007_Numero_Nota_Fiscal AS UNSIGNED) DESC";
			$queryNotas = mysql_query($sqlNotas);

			$ultimoTipo = '';
			$ultimaSerie = '';
			$numeroAnterior = 0;
			while ($linhaNota = mysql_fetch_assoc($queryNotas)) {
				$tipoAtual   = $linhaNota['T007_Flag_ACP'];
				$serieAtual  = $linhaNota['T007_Serie'];
				$numeroAtual = intval($linhaNota['numeroNota']);

				if ($ultimoTipo !== $tipoAtual || $ultimaSerie !== $serieAtual) {
					$numeroAnterior = 0;
				}

				if ($numeroAnterior > 0) {
					$diferenca = $numeroAnterior - $numeroAtual;
					if ($diferenca > 1) {
						$faltantes = $diferenca - 1;
						$inicioFaixa = $numeroAtual + 1;
						$fimFaixa = $numeroAnterior - 1;
						$faixa = ($inicioFaixa == $fimFaixa) ? $inicioFaixa : $inicioFaixa . ' a ' . $fimFaixa;

						$geradoDados[] = array(
							($tipoAtual == '6' ? 'NF-e' : 'NFC-e'),
							$serieAtual,
							$faltantes,
							$faixa,
							$numeroAnterior,
							$numeroAtual,
						);

						$totalFaixas++;
						$totalNumerosFaltantes += $faltantes;
					}
				}

				$numeroAnterior = $numeroAtual;
				$ultimoTipo = $tipoAtual;
				$ultimaSerie = $serieAtual;
			}

			if ($totalFaixas > 0) {
				echo "<div style='margin:auto;text-align:center;padding:4px;border:1px solid #d6a100;background:#fff4ce;color:#7a5a00;width:99%;border-radius:3px;font-size:13px'>Foram encontradas {$totalFaixas} faixa(s) com pulo de numeração no período informado, totalizando {$totalNumerosFaltantes} número(s) faltante(s).</div>";
			} else {
				echo "<div style='margin:auto;text-align:center;padding:4px;border:1px solid #4caf50;background:#e8f5e9;color:#2e7d32;width:99%;border-radius:3px;font-size:13px'>Não foram encontrados pulos de numeração no período informado.</div>";
			}
		} else {
			echo "<div style='margin:auto;text-align:center;padding:4px;border:1px solid #1976d2;background:#e3f2fd;color:#0d47a1;width:99%;border-radius:3px;font-size:13px'>Informe o período de emissão no filtro para gerar o relatório de pulo de numeração.</div>";
		}

		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

    case 'ven003GridListaNFDebitoCredito':
        $filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] ?? 'N') === 'S';
        $empresaNFDC = (int) $g['empresaAtual'];
        $campoVinculoNFDC = 'T007A_T007_Id_Origem_NF_Debito_Credito';
        $consultaCampoVinculoNFDC = mysql_query("SHOW COLUMNS FROM T007A LIKE '{$campoVinculoNFDC}'");
        $possuiCampoVinculoNFDC = $consultaCampoVinculoNFDC && mysql_num_rows($consultaCampoVinculoNFDC) > 0;
        $joinOrigemNFDC = '';
        $idOrigemNFDC = 'NULL AS NFOrigemId';
        $numeroOrigemNFDC = "'Atualização de base pendente' AS NFOrigemNumero";
        if ($possuiCampoVinculoNFDC) {
            // Aliases próprios evitam ambiguidade com os campos da NF listada.
            // LEFT JOIN mantém notas manuais e de pedido, sem inventar uma origem.
            $joinOrigemNFDC = "LEFT JOIN (
                SELECT T007_Id AS NFOrigemId, T007_C004_Id AS NFOrigemEmpresa,
                    T007_Numero_Nota_Fiscal AS NFOrigemNumeroBruto,
                    T007_Serie AS NFOrigemSerie, T007_Flag_Cancelada AS NFOrigemCancelada
                FROM T007
            ) AS OrigemNFDC ON NFOrigemId = T007A.{$campoVinculoNFDC}
                AND NFOrigemEmpresa = T007.T007_C004_Id";
            $idOrigemNFDC = 'NFOrigemId';
            $numeroOrigemNFDC = "IF(NFOrigemId IS NULL, '-',
                CONCAT('NF ', LPAD(NFOrigemNumeroBruto,7,'0'),
                    IF(NFOrigemCancelada='S',' (c)',''), ' - Série ', IFNULL(NFOrigemSerie,''))) AS NFOrigemNumero";
        }
        $tipoFiscalNFDC = "IF(T007_Flag_Finalidade_NFe='6', T007A_Tipo_NF_Debito, T007A_Tipo_NF_Credito)";
        $grid = array(
            array('colunaValor' => 'T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => "CONCAT('NF ', LPAD(T007_Numero_Nota_Fiscal,7,'0'), IF(T007_Flag_Cancelada='S',' (c)','')) AS NFDCNumero", 'colunaTitulo' => 'NF', 'callback' => 'ven003ExibirVinculoNFDC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaExibir' => false),
            array('colunaValor' => "CASE T007_Flag_ACP WHEN '6' THEN '55 - NF-e' WHEN '8' THEN '65 - NFC-e' WHEN '1' THEN 'NF' WHEN '2' THEN 'ROM' WHEN '3' THEN 'ACP' WHEN '4' THEN 'CF' WHEN '5' THEN 'D1' WHEN '7' THEN 'NFS-e' ELSE T007_Flag_ACP END AS NFDCModelo", 'colunaTitulo' => 'Modelo'),
            array('colunaValor' => 'T007_Serie', 'colunaTitulo' => 'Série', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "IF(T007_Flag_Finalidade_NFe='6','Débito','Crédito') AS NFDCOperacao", 'colunaTitulo' => 'Operação'),
            array('colunaValor' => 'T007_Flag_Finalidade_NFe', 'colunaExibir' => false),
            array('colunaValor' => $tipoFiscalNFDC . ' AS NFDCTipoFiscal', 'colunaTitulo' => 'Tipo Débito/Crédito', 'callback' => 'ven003ExibirTipoFiscalNFDC', 'style' => 'min-width:220px;white-space:normal;'),
            array('colunaValor' => $numeroOrigemNFDC, 'colunaTitulo' => 'NF ORIGEM', 'callback' => 'ven003ExibirOrigemNFDC'),
            array('colunaValor' => $idOrigemNFDC, 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Situação', 'callback' => 'ven003ExibirSituacaoNFDC'),
            array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave de acesso', 'colunaExibir' => false),
        );
        $from = 'T007';
        $extra = "LEFT JOIN T007A ON T007A_T007_Id = T007_Id
            LEFT JOIN D024 ON D024_Id = T007_D024_Id
            LEFT JOIN D006 ON D006_Id = T007_D006_Id
            {$joinOrigemNFDC}
            WHERE T007_C004_Id = '{$empresaNFDC}'
                AND T007_Flag_Finalidade_NFe IN ('5','6')
            ORDER BY T007_Numero_Nota_Fiscal DESC, T007_Id DESC";
        $gridFiltro = array(
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
            'T007_Serie' => array('titulo' => 'Série', 'tipo' => 'texto'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T007_Flag_Finalidade_NFe' => array('titulo' => 'Operação', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Débito', 'value' => '6'),
                array('title' => 'Crédito', 'value' => '5'),
            )),
            $tipoFiscalNFDC => array('titulo' => 'Tipo Débito/Crédito (código)', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            )),
        );
        if ($possuiCampoVinculoNFDC) {
            $gridFiltro['NFOrigemNumeroBruto'] = array('titulo' => 'NF origem', 'tipo' => 'numero');
        }
        $totais = array(
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Total', 'valor' => 'T007_Valor_Total', 'titulo' => 'Total R$'),
        );
        // Ciclo nativo: filtros, SQL Extra original, paginação e personalização
        // ficam associados à rota desta grade, sem reutilizar o layout principal.
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') {
            acaoClickLinha('{$g['divId']}', function(data, array) {
                if ($(data.target).closest('.nfDCOrigem').length) { return; }
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('PDV'), '', 'Editar Nota Fiscal - ID: ' + array['todosCampos']['T007_Id'], '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T007_Id']), [990,605]);
            });
            // O framework usa jQuery 1.6.2: on/off interrompem o carregamento,
            // inclusive a inicialização visual dos botões de filtro.
            $('#{$g['divId']}').undelegate('.nfDCOrigem', 'click.nfDCOrigem').delegate('.nfDCOrigem', 'click.nfDCOrigem', function(data) {
                data.preventDefault();
                data.stopPropagation();
                var id = $(this).attr('data-nf-origem');
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'NF de origem - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id), [990,605]);
            });
        }</script></div>
EOT;
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;
	case 'ven003GridNFDebitoCreditoAutorizada':
		/*
		 * Grade usada dentro da janela de geração de NF de Débito/Crédito a
		 * partir de NF-e autorizada. É uma grade nativa do framework para que
		 * sua personalização seja isolada por cliente e acessível pelo atalho P.
		 */
		$T007_IdOrigemNFDC = (int) $r_T007_Id;
		if ($T007_IdOrigemNFDC <= 0) {
			echo gGeraAlertMsg('NF de Débito/Crédito', 'NF-e de origem não informada para carregar os itens.');
			break;
		}

		$casasQuantidadeNFDC = (int) $g['c020']['C020_Casas_Decimais_Quantidade'];
		if ($casasQuantidadeNFDC < 0 || $casasQuantidadeNFDC > 6) $casasQuantidadeNFDC = 3;
		$grid = array(
			array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'T008_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T008_Id AS NFDc_Item', 'colunaTitulo' => 'Item', 'colunaAlinhar' => 'right', 'callback' => 'ven003NFDcNumeroItem', 'style' => 'width:1%'),
			array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%'),
			array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtd. origem', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $casasQuantidadeNFDC, 'style' => 'width:1%'),
			array('colunaValor' => 'NFDc_Nova_Quantidade', 'colunaTitulo' => 'Nova quantidade', 'colunaTipo' => 'livre', 'callback' => 'ven003NFDcCampoQuantidade', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
			array('colunaValor' => 'NFDc_Novo_Preco_Unitario', 'colunaTitulo' => 'Preço unit. novo', 'colunaTipo' => 'livre', 'callback' => 'ven003NFDcCampoPreco', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
		);
		$from = 'T008';
		$extra = " WHERE T008_T007_Id='{$T007_IdOrigemNFDC}' ORDER BY T008_Id";
		// Registra o extra original no ciclo de personalização da grade. Além de
		// manter o recorte obrigatório pela NF-e de origem, permite ao cliente
		// consultar/estender o SQL com JOINs no atalho Ctrl+Alt+Shift+P.
		$extra = $g['ui']->gridFiltroExtra(true, array(), $extra);
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('gridOptNoShow', true);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

}
