<?php
namespace hardness;

// Carrega funções callback
require_once('prog/fis/fis001/grid_func-callback.php');
/**
* Definição do Grid (Tabela)
*/

$exibirDescricaoComercial  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? false : true;

$grid = array(
	array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D006_Id', $g['divId'])),
	array('colunaValor' => "D006_Id", 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Codigo_CFOP", 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center' ),
	array('colunaValor' => "D006_Descricao", 'colunaTitulo' => 'Descrição'),
	array('colunaValor' => "D006_Descricao_Comercial", 'colunaTitulo' => 'Descrição comercial', 'colunaExibir' => $exibirDescricaoComercial, 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
	array('colunaValor' => "D006_Flag_Estoque", 'colunaTitulo' => 'Estoque', 'callback' => 'campoEstoque', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_MMF", 'colunaTitulo' => 'MMF', 'callback' => 'campoMMF', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Atualiza_Custo", 'colunaTitulo' => 'Custo', 'callback' => 'campoAtualizaCusto', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Gera_Contas", 'colunaTitulo' => 'Financeiro', 'callback' => 'campoFinanceiro', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Substituicao_Tributaria", 'colunaTitulo' => 'ST', 'callback' => 'campoST', 'colunaAlinhar' => 'center'),
	array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta', 'colunaAlinhar' => 'left'),
	array('colunaValor' => "D073_Nome", 'colunaTitulo' => 'Centro custo', 'colunaAlinhar' => 'left'),
	array('colunaValor' => "D006_Flag_Inativo", 'colunaTitulo' => 'Inativo', 'colunaAlinhar' => 'Center', 'colunaExibir' => false),
	array('colunaValor' => "D006_Flag_Devolucao", 'colunaTitulo' => 'Devolução', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Departamento", 'colunaTitulo' => 'Departamento', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Venda_Compra_Outros", 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Estoque", 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_MMF", 'colunaTitulo' => 'MMF', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Gera_Contas", 'colunaTitulo' => 'Contas', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Flag_Entrada_Saida", 'colunaTitulo' => 'Entrada/Saída', 'colunaAlinhar' => 'center'),
	array('colunaValor' => "D006_Impressao_Dados_Produtos_DANFE", 'colunaTitulo' => 'Impressão dados produtos Danfe', 'colunaAlinhar' => 'left', 'colunaEditarTodos' => true, 'colunaExibir' => false),
	// ICMS
	array('colunaValor' => "D006_Flag_Isento_ICMS", 'colunaTitulo' => 'ICMS', 'callback' => 'campoICMS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_IPI_Base_ICMS", 'colunaTitulo' => 'Soma IPI<br>Base ICMS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_Despesas_Base_ST", 'colunaTitulo' => 'Soma desp.<br>Base ICMS/ST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deb_Crd_ICMS", 'colunaTitulo' => 'Credita<br>ICMS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D059_Codigo_ST", 'colunaTitulo' => 'CST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);'),
	array('colunaValor' => "D006_Aliquota_ICMS", 'colunaTitulo' => '% ICMS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);','colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2'),
	array('colunaValor' => "D006_Percentual_FCP", 'colunaTitulo' => '% FCP', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);','colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2'),
	array('colunaValor' => "D006_Percentual_Reducao_ICMS", 'colunaTitulo' => '% Red.<br>ICMS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Reduz_Base_DIFAL", 'colunaTitulo' => 'Reduz BC.<br>DIFAL', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Substituicao_Tributaria", 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	// Task 60: elegibilidade manual para GNRE. Estes flags não alteram os
	// cálculos fiscais desta CFOP nem preenchem dados da guia.
	array('colunaValor' => "D006_Flag_Gera_GNRE_ST", 'colunaTitulo' => 'Gera GNRE<br>ST', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaEditarGeraSelect' => array(
		array('title' => 'Não', 'value' => 'N', 'selected' => true),
		array('title' => 'Sim', 'value' => 'S'),
	)),
	array('colunaValor' => "D006_Flag_Gera_GNRE_DIFAL", 'colunaTitulo' => 'Gera GNRE<br>DIFAL', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true,'colunaEditarOculto' => true, 'colunaEditarGeraSelect' => array(
		array('title' => 'Não', 'value' => 'N', 'selected' => true),
		array('title' => 'Sim', 'value' => 'S'),
	)),
	array('colunaValor' => "D006_Flag_Gera_GNRE_FCP", 'colunaTitulo' => 'Gera GNRE<br>FCP', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true,'colunaEditarOculto' => true, 'colunaEditarGeraSelect' => array(
		array('title' => 'Não', 'value' => 'N', 'selected' => true),
		array('title' => 'Sim', 'value' => 'S'),
	)),
	array('colunaValor' => "D006_Flag_Deb_Crd_ST", 'colunaTitulo' => 'Credita<br>ST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_ST_MVA", 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_ST_VA", 'colunaTitulo' => 'Agrega<br> MVA', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Ajustar_MVA", 'colunaTitulo' => 'Ajusta<br> MVA', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Reducao_MVA", 'colunaTitulo' => '% Red<br> MVA', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),	
	array('colunaValor' => "D006_Flag_Deduz_FCP_ST_Valor_ST", 'colunaTitulo' => 'Deduz FPC ST<br>Valor ST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_ST_ICMS_Inter_Estadual", 'colunaTitulo' => '% ICMS IE', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_ST_ICMS_Interno", 'colunaTitulo' => '% ICMS Int.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_ST_ICMS_Interno_Reducao", 'colunaTitulo' => '% Red.BC<br>ICMS ST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Somar_FCP_ICMS_Interno", 'colunaTitulo' => 'Soma FCP<br>alíq.ICMS Int.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Percentual_Partilha_ICMS_Destino", 'colunaTitulo' => '% Partilha<br>ICMS Dest.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Percentual_Partilha_ICMS_Origem", 'colunaTitulo' => '% Partilha<br>ICMS Ori.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Endereco_Aliquota_Icms_Interno_Difal", 'colunaTitulo' => 'Usar % ICMS Interno<br>Difal Enderço', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'callback' => 'campoPercentualIcmsIntDifal' , 'colunaEditarGeraSelect' => array(
		array('title' => 'PADRÃO', 'value' => 'P'),
		array('title' => 'ENTREGA', 'value' => 'E'))),
	array('colunaValor' => "D006_Flag_Calcula_FCP", 'colunaTitulo' => 'FCP', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Calcula_FCP_ST", 'colunaTitulo' => 'FCP<br>ST', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Calcula_FCP_Retido", 'colunaTitulo' => 'FCP<br>Ret.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Percentual_Base_Operacao_Propria", 'colunaTitulo' => '% BC oper.<br>própria', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_DIFAL_Custo", 'colunaTitulo' => 'Soma DIFAL<br>Custo', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Deduz_ICMS_Desonerado_Valor_Total", 'colunaTitulo' => 'Deduz ICMS<br>Deson. V.total', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Codigo_Beneficio_Fiscal", 'colunaTitulo' => 'Benefício<br>Fiscal', 'colunaAlinhar' => 'Center', 'colunaExibir' => true,'style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => true),
	//IPI
	array('colunaValor' => "D006_Flag_Isento_IPI", 'colunaTitulo' => 'IPI', 'callback' => 'campoIPI', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_Despesas_Base_IPI", 'colunaTitulo' => 'Soma Desp.<br>Base IPI', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Credita_IPI", 'colunaTitulo' => 'Credita<br>IPI', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Reducao_Aliquota_IPI", 'colunaTitulo' => '% Red.<br>IPI', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Codigo_Enq_Legal_IPI", 'colunaTitulo' => 'Código<br> Enqua.IPI', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditarOculto' => true),
	//PIS
	array('colunaValor' => "D006_Flag_Isento_PIS", 'colunaTitulo' => 'PIS', 'callback' => 'campoPIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Forma_Calculo_PIS", 'colunaTitulo' => 'Base Cálculo<br>PIS', 'callback' => 'baseCalculoPIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deb_Crd_PIS", 'colunaTitulo' => 'Credita<br>PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deduz_ICMS_Base_PIS", 'colunaTitulo' => 'Deduz ICMS<br>Base PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deduz_FCP_Base_PIS", 'colunaTitulo' => 'Deduz FCP<br>Base PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_Despesas_Base_PIS", 'colunaTitulo' => 'Soma desp.<br>Base PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Envia_PIS_XML", 'colunaTitulo' => 'Retém PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Aliquota_PIS", 'colunaTitulo' => '% PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaCasasDecimais' => '2','colunaEditarOculto' => true),
	array('colunaValor' => "D006_Percentual_Reducao_PIS", 'colunaTitulo' => '% Red.PIS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-vermelho);', 'colunaCasasDecimais' => '2', 'colunaEditarOculto' => true),
	//COFINS
	array('colunaValor' => "D006_Flag_Isento_COFINS", 'colunaTitulo' => 'COFINS', 'callback' => 'campoCOFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Forma_Calculo_COFINS", 'colunaTitulo' => 'Base Cálculo<br>COFINS', 'callback' => 'baseCalculoCOFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deb_Crd_COFINS", 'colunaTitulo' => 'Credita<br>COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deduz_ICMS_Base_COFINS", 'colunaTitulo' => 'Deduz ICMS<br>Base COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Deduz_FCP_Base_COFINS", 'colunaTitulo' => 'Deduz FCP<br>Base COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_Despesas_Base_COFINS", 'colunaTitulo' => 'Soma desp.<br>Base COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Envia_COFINS_XML", 'colunaTitulo' => 'Retém COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Aliquota_COFINS", 'colunaTitulo' => '% COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaCasasDecimais' => '2','colunaEditarOculto' => true),
	array('colunaValor' => "D006_Percentual_Reducao_COFINS", 'colunaTitulo' => '% Red.<br>COFINS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-azul);', 'colunaCasasDecimais' => '2','colunaEditarOculto' => true),
	
	/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 03/12/2025 
	=============================================================================*/
	array('colunaValor' => "D006_Situacao_Tributaria_CBS_IBS", 'colunaTitulo' => 'CST CBS/IBS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	array('colunaValor' => "D006_Classificacao_Tributaria_CBS_IBS", 'colunaTitulo' => 'Classificação Trib. <br>CBS/IBS', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	array('colunaValor' => "D006_Situacao_Tributaria_CBS_IBS_Regular", 'colunaTitulo' => 'CST CBS/IBS Reg.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	array('colunaValor' => "D006_Classificacao_Tributaria_CBS_IBS_Regular", 'colunaTitulo' => 'Classificação Trib. <br>CBS/IBS Reg.', 'colunaAlinhar' => 'center','style' => 'background: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	/*=============================================================================*/

	// OUTROS
	array('colunaValor' => "D006_Flag_Isento_IRPJ", 'colunaTitulo' => 'Isento IRPJ', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_Despesas_Base_IRPJ", 'colunaTitulo' => 'Soma desp.<br>Base IRPJ', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Isento_CSLL", 'colunaTitulo' => 'Isento CSLL', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Somar_Despesas_Base_CSLL", 'colunaTitulo' => 'Soma desp.<br>Base CSLL', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Envia_IRPJ_XML", 'colunaTitulo' => 'Envia IRPJ<br>XML', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Envia_CSLL_XML", 'colunaTitulo' => 'Envia CSLL<br>XML', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Tipo_Desconto", 'colunaTitulo' => 'Tipo Desconto Aplicado Total<br>Na base ICMS', 'colunaAlinhar' => 'left', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'tipoDescontoAplicadoValorTotal', 'colunaEditarGeraSelect' => array(
		array('title' => 'CONDICIONAL', 'value' => 'C', 'selected' => true),
		array('title' => 'INCONDICIONAL', 'value' => 'I'),
	)),
	array('colunaValor' => "D006_Flag_Tipo_Desconto_PIS_COFINS", 'colunaTitulo' => 'Tipo Desconto Aplicado Total<br>Na base PIS/COFINS', 'colunaAlinhar' => 'left', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'tipoDescontoAplicadoValorTotal', 'colunaEditarGeraSelect' => array(
		array('title' => 'CONDICIONAL', 'value' => 'C', 'selected' => true),
		array('title' => 'INCONDICIONAL', 'value' => 'I'),
	)),

	array('colunaValor' => "D006_Flag_Tipo_Desconto_IPI", 'colunaTitulo' => 'Tipo Desconto Aplicado Total<br>Na base IPI', 'colunaAlinhar' => 'left', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'tipoDescontoAplicadoValorTotal', 'colunaEditarGeraSelect' => array(
		array('title' => 'CONDICIONAL', 'value' => 'C', 'selected' => true),
		array('title' => 'INCONDICIONAL', 'value' => 'I'),
	)),
	array('colunaValor' => "D006_Percentual_Desconto_CFOP", 'colunaTitulo' => '% Desconto', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2','colunaEditarOculto' => true),
	array('colunaValor' => "D006_Flag_Destaca_Desconto_NF", 'colunaTitulo' => 'Destaca<br>Desconto NF', 'colunaAlinhar' => 'center','colunaEditarOculto' => true),


);


/**
* Definição da SQL
*/
$from = 'D006';

/**
 * Ações
 */
switch($g['r'][3]){
	// GRID
	
	/**
	* CFOP Vendas
	* Chamado em: fis001tab01
	*/
	case 'fis001grid01':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='V' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;
	
	/**
	* CFOP Compras
	* Chamado em: fis001tab01
	*/
	case 'fis001grid02':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='C' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;

	/**
	* CFOP Transferencias
	* Chamado em: fis001tab01
	*/
	case 'fis001grid03':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='T' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;

	/**
	* CFOP Outras Saidas
	* Chamado em: fis001tab01
	*/
	case 'fis001grid04':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='OS' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;

	/**
	* CFOP Outras Entradas
	* Chamado em: fis001tab01
	*/
	case 'fis001grid05':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='OE' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;

	/**
	* CFOP Devolução Compras
	* Chamado em: fis001tab01
	*/
	case 'fis001grid06':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='DC' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;

	/**
	* CFOP Devolução Vendas
	* Chamado em: fis001tab01
	*/
	case 'fis001grid07':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Venda_Compra_Outros='DV' AND IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;
	
	/**
	* CFOP Devolução Vendas
	* Chamado em: fis001tab01
	*/
	case 'fis001GridGeral':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			WHERE IFNULL(D006_Flag_Inativo,'N') != 'S'
EOT;
	break;
	
	/**
	* CFOP Inativas
	* Chamado em: fis001tab01
	*/
	case 'fis001gridInativas':
		$extra = <<<EOT
			left join D059 on D059_Id=D006_D059_Id
			left join D014 on D014_Id=D006_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D073 on D073_Id=D006_D073_Id
			where D006_Flag_Inativo='S'
EOT;
	break;	
} 

/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
$novoIdJanela = uniqid();
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
		var id = $(this).closest('tr').attr('id').replace('D006_Id-', '');
		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdJanela}', '', 'CFOP ' + id, '/fis/fis001/content/fis001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [850,620]);
	});			
	$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
} </script></div>
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis001Grid01Incluir-{$g['r'][3]}">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
 			$('#fis001Grid01Incluir-{$g['r'][3]}').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir CFOP', '/fis/fis001/content/fis001content02/', '', [700,580]);
				});	 
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D006_Codigo_CFOP' => array('titulo' => 'Código CFOP', 'tipo' => 'numero'),
			'D006_Descricao' => array('titulo' => 'Descrição CFOP', 'tipo' => 'texto'),
			'D006_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			//'D006_Flag_Inativo' => array('titulo' => 'Inativo (S/N)', 'tipo' => 'texto'),
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
		$g['smarty']->display('grid.tpl');
?>
