<?php
namespace hardness;

/**
 * @author Ricardo Norio Miyata <ricardo@hardness.com.br>
 */
definirClasse('gravarTotalizacaoT004');

/**
 * \arg A classe <em>gravarTotalizacaoT004</em>, possui um método principal e submétodos para realização de cálculos de imposto, desconto, dentre outros.
 * \arg Existem 3 arrays principais de dados, cabeçalho (T003), produto (T004) e configurações (C031). Estes arrays são populados em métodos diferentes de consultas no banco, retornando um dicionário indexado com o nome dos campos.
 * \arg Todos os submétodos, por padrão, recebem 4 arrays, 3 de dados ($tabCabecalho, $tabProduto e $tabConfiguracao) e 1 para os resultados ($resultArray) das operações já realizadas.
 */
class gravarTotalizacaoT004_
{
    /**
     * Método principal para fazer a chamada de submétodos para oprerações no banco e cálculos.
     *
     * @param string $tabelaCabecalho String da tabela de cabeçalho (T003).
     * @param int $tabelaCabecalhoId Número identificador para a tabela de cabeçalho (T003).
     * @param string $tabelaProduto String da tabela de produto (T004).
     * @param int $tabelaProdutoId Número identificador para a tabela de produto (T004).
     */
    public function gravarTotalizacao($tabelaCabecalho, $tabelaCabecalhoId, $tabelaProduto, $tabelaProdutoId)
    {
        // Variável global para chamar os métodos SQL do framework.
        global $g;

        // ++++++++++ C O N S U L T A S  D E  B A N C O ++++++++++
        // Resultado da consulta SQL na tabela de produtos (T004).
        $tabProduto = gravarTotalizacaoT004_::dadosTabProduto($tabelaCabecalho, $tabelaProduto, $tabelaProdutoId);
        // Resultado da consulta SQL na tabela de configurações (C031).
        $tabConfiguracao = gravarTotalizacaoT004_::dadosTabConfiguracao($tabProduto);
        // Resultado da consulta SQL na tabela de cabeçalho (T003).
        $tabCabecalho = gravarTotalizacaoT004_::dadosTabCabecalho($tabelaCabecalho, $tabelaCabecalhoId, $tabelaProduto, $tabProduto, $tabConfiguracao);

        // ++++++++++ O P E R A Ç Õ E S ++++++++++
        // Dicionário para o resultado dos cálculos.
        $resultArray = array();

        // Módulo desconto.
        $resultArray[$tabelaProduto . '_Valor_Desconto_Unitario_Bruto'] = gravarTotalizacaoT004_::valorDescontoUnitarioBruto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Desconto_Unitario'] = gravarTotalizacaoT004_::valorDescontoUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Desconto'] = gravarTotalizacaoT004_::valorTotalDesconto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo preço.
        $resultArray[$tabelaProduto . '_Valor_Preco_Bruto'] = gravarTotalizacaoT004_::valorPrecoBruto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Preco_Unitario'] = gravarTotalizacaoT004_::valorPrecoUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Preco'] = gravarTotalizacaoT004_::valorTotalPreco($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Preco_Sem_Desconto'] = gravarTotalizacaoT004_::valorTotalPrecoSemDesconto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Custo'] = gravarTotalizacaoT004_::valorTotalCusto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Agregados'] = $tabProduto['Valor_Total_Agregados'];

        // Módulo peso.
        $resultArray[$tabelaProduto . '_Peso_Bruto_Total'] = gravarTotalizacaoT004_::pesoBrutoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Peso_Total'] = gravarTotalizacaoT004_::pesoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo frete.
        $resultArray[$tabelaProduto . '_Valor_Frete_Total'] = gravarTotalizacaoT004_::valorTotalFrete($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004 != T006 == T008
        $resultArray[$tabelaProduto . '_Valor_Frete_Unitario'] = gravarTotalizacaoT004_::valorFreteUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo despesas.
        $resultArray[$tabelaProduto . '_Valor_Total_Despesas_Acessorias'] = gravarTotalizacaoT004_::valorTotalDespesasAcessorias($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Despesas_Acessorias_Unitario'] = gravarTotalizacaoT004_::valorDespesasAcessoriasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Despesas'] = gravarTotalizacaoT004_::valorTotalDespesas($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Despesas_Unitario'] = gravarTotalizacaoT004_::valorDespesasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo IPI.
        $resultArray[$tabelaProduto . '_Valor_Base_IPI'] = gravarTotalizacaoT004_::valorBaseIPI($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_IPI'] = gravarTotalizacaoT004_::valorIPI($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo custo final.
        $resultArray[$tabelaProduto . '_Valor_Total_Custo_Final'] = gravarTotalizacaoT004_::valorTotalCustoFinal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Custo_Final_Unitario'] = gravarTotalizacaoT004_::valorCustoFinalUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo ICMS.
        $resultArray[$tabelaProduto . '_Valor_Base_ICMS'] = gravarTotalizacaoT004_::valorBaseICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_ICMS'] = gravarTotalizacaoT004_::valorICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Desonerado'] = gravarTotalizacaoT004_::valorBaseICMS_Desonerado($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_ICMS_Desonerado'] = gravarTotalizacaoT004_::valorICMS_Desonerado($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_ICMS_Credito'] = gravarTotalizacaoT004_::valorICMS_Credito($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo ST.
        $resultArray[$tabelaProduto . '_Valor_Base_ST'] = gravarTotalizacaoT004_::valorBaseST($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004_Valor_Base_ST == T006_Valor_Base_ICMS_Substituicao
        $resultArray[$tabelaProduto . '_Valor_ST'] = gravarTotalizacaoT004_::valorST($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004_Valor_ST == T006_Valor_ICMS_Substituicao_Retencao

        // Módulo PIS.
        $resultArray[$tabelaProduto . '_Aliquota_PIS'] = gravarTotalizacaoT004_::aliquotaPIS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Base_PIS'] = gravarTotalizacaoT004_::valorBasePIS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_PIS'] = gravarTotalizacaoT004_::valorPIS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo COFINS.
        $resultArray[$tabelaProduto . '_Aliquota_COFINS'] = gravarTotalizacaoT004_::aliquotaCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Base_COFINS'] = gravarTotalizacaoT004_::valorBaseCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_COFINS'] = gravarTotalizacaoT004_::valorCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo IRPJ.
        $resultArray[$tabelaProduto . '_Aliquota_IRPJ'] = gravarTotalizacaoT004_::aliquotaIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Base_IRPJ'] = gravarTotalizacaoT004_::valorBaseIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_IRPJ'] = gravarTotalizacaoT004_::valorIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo CSLL.
        $resultArray[$tabelaProduto . '_Aliquota_CSLL'] = gravarTotalizacaoT004_::aliquotaCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Base_CSLL'] = gravarTotalizacaoT004_::valorBaseCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_CSLL'] = gravarTotalizacaoT004_::valorCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Módulo cálculos finais.
        $resultArray[$tabelaProduto . '_IPV'] = gravarTotalizacaoT004_::IPV($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Lucro'] = gravarTotalizacaoT004_::valorLucro($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Percentual_Margem'] = gravarTotalizacaoT004_::percentualMargem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Trib_Transparencia'] = gravarTotalizacaoT004_::valorTribTransparencia($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Total_Cubagem'] = gravarTotalizacaoT004_::totalCubagem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

        // Método do framework para executar o UPDATE.
        $g['sqlAuto']->gerarSQLUpdate($tabelaProduto, $tabelaProduto . '_Id', $tabelaProdutoId, null, $resultArray, true, false);
    }

    /**
     * Método para zerar campos nulos de um dicionário.
     *
     * @param array $array Dicionário com valores indexados .
     */
    public function verificaCamposNulos(&$array)
    {
        foreach ($array as $key => $value) {
            if (!$array[$key]) {
                $array[$key] = 0;
            }
        }
    }

    /**
     * Método para retornar um dicionário da consulta SQL da tabela de produto (T004).
     *
     * @param string $tabelaCabecalho String da tabela de cabeçalho (T003).
     * @param string $tabelaProduto String da tabela de produto (T004).
     * @param int $tabelaProdutoId Número identificador para a tabela de produto (T004).
     */
    public function dadosTabProduto($tabelaCabecalho, $tabelaProduto, $tabelaProdutoId)
    {
        // Variáveis globais. A variável "$grid" é tratada como global para ser usado na execução do comando SQL pelo método do framework.
        global $g, $grid;

        // Campos da consulta SQL.
        $grid = array(
            array('colunaValor' => $tabelaCabecalho . '_Flag_ACP', 'colunaTitulo' => 'Flag_ACP'),
            array('colunaValor' => 'D006_Flag_Forma_Calculo_COFINS', 'colunaTitulo' => 'Flag_Forma_Calculo_COFINS'),
            array('colunaValor' => 'D006_Flag_Forma_Calculo_PIS', 'colunaTitulo' => 'Flag_Forma_Calculo_PIS'),
            array('colunaValor' => 'D006_Flag_Somar_IPI_Base_ICMS', 'colunaTitulo' => 'Flag_Somar_IPI_Base_ICMS'),
            array('colunaValor' => 'D006_Flag_Somar_Despesas_Base_ST', 'colunaTitulo' => 'Flag_Somar_Despesas_Base_ST'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Desconto / 100', 'colunaTitulo' => 'Percentual_Desconto'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Desconto_Bruto / 100', 'colunaTitulo' => 'Percentual_Desconto_Bruto'),
            array('colunaValor' => $tabelaProduto . '_C004_Id', 'colunaTitulo' => 'C004_Id'),
            array('colunaValor' => 'C004_Casas_Decimais_Preco', 'colunaTitulo' => 'Casas_Decimais_Preco'),
            array('colunaValor' => 'D006_Flag_Isento_ICMS', 'colunaTitulo' => 'Flag_Isento_ICMS'),
            array('colunaValor' => '(100 - ' . $tabelaProduto . '_ST_ICMS_Interno) / 100', 'colunaTitulo' => 'Fator_ICMS'),
            array('colunaValor' => 'D006_Flag_Somar_Fator_ICMS_Dif_Base', 'colunaTitulo' => 'Flag_Somar_Fator_ICMS_Dif_Base'),
            array('colunaValor' => 'D006_Flag_Somar_Despesas_Base_IPI', 'colunaTitulo' => 'Flag_Somar_Despesas_Base_IPI'),
            array('colunaValor' => 'T134_Id', 'colunaTitulo' => 'T134_Id'),
            array('colunaValor' => 'T134_Reducao_Base_ICMS', 'colunaTitulo' => 'Reducao_Base_ICMS'),
            array('colunaValor' => 'C004_Flag_Rateio_Frete', 'colunaTitulo' => 'Flag_Rateio_Frete'),
            array('colunaValor' => 'SUM(T202_Valor_Unitario)', 'colunaTitulo' => 'Valor_Total_Agregados'),
            array('colunaValor' => 'COUNT(*)', 'colunaTitulo' => 'Total_Itens'),
            array('colunaValor' => $tabelaProduto . '_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Valor_Preco_Original_Bruto'),
            array('colunaValor' => $tabelaProduto . '_Valor_Preco_Original', 'colunaTitulo' => 'Valor_Preco_Original'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'Flag_Tipo'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Quantidade_OC'),
            array('colunaValor' => $tabelaProduto . '_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaTitulo' => 'Valor_Custo_Tabela'),
            array('colunaValor' => $tabelaProduto . '_Valor_Preco_Bruto', 'colunaTitulo' => 'Valor_Preco_Bruto'),
            array('colunaValor' => $tabelaProduto . '_Valor_Preco_Unitario', 'colunaTitulo' => 'Valor_Preco_Unitario'),
            array('colunaValor' => $tabelaProduto . '_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor_Custo_Unitario'),
            array('colunaValor' => $tabelaProduto . '_Peso_Bruto', 'colunaTitulo' => 'Peso_Bruto'),
            array('colunaValor' => $tabelaProduto . '_Peso_Unitario', 'colunaTitulo' => 'Peso_Unitario'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Reducao_IPI', 'colunaTitulo' => 'Percentual_Reducao_IPI'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_IPI', 'colunaTitulo' => 'Aliquota_IPI'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Reducao_ICMS', 'colunaTitulo' => 'Percentual_Reducao_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_ICMS', 'colunaTitulo' => 'Aliquota_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_ICMS_Oculto', 'colunaTitulo' => 'Aliquota_ICMS_Oculto'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_ICMS_Credito', 'colunaTitulo' => 'Aliquota_ICMS_Credito'),
            array('colunaValor' => $tabelaProduto . '_Flag_ST', 'colunaTitulo' => 'Flag_ST'),
            array('colunaValor' => $tabelaProduto . '_ST_VA', 'colunaTitulo' => 'ST_VA'),
            array('colunaValor' => $tabelaProduto . '_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS_Inter_Estadual'),
            array('colunaValor' => $tabelaProduto . '_ST_ICMS_Interno', 'colunaTitulo' => 'ST_ICMS_Interno'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_PIS', 'colunaTitulo' => 'Flag_Isento_PIS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_PIS', 'colunaTitulo' => 'Aliquota_PIS'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_COFINS', 'colunaTitulo' => 'Flag_Isento_COFINS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_COFINS', 'colunaTitulo' => 'Aliquota_COFINS'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_IRPJ', 'colunaTitulo' => 'Flag_Isento_IRPJ'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_IRPJ', 'colunaTitulo' => 'Aliquota_IRPJ'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_CSLL', 'colunaTitulo' => 'Flag_Isento_CSLL'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_CSLL', 'colunaTitulo' => 'Aliquota_CSLL'),
            array('colunaValor' => $tabelaProduto . '_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV_Sem_Lucro'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Trib_Transparencia', 'colunaTitulo' => 'Percentual_Trib_Transparencia'),
            array('colunaValor' => $tabelaProduto . '_Cubagem_Unitaria', 'colunaTitulo' => 'Cubagem_Unitaria'),
        );

        // Tabela de consulta SQL.
        $from = $tabelaProduto;

        // Cláusula WHERE.
        $extra = <<<EOT
            LEFT JOIN $tabelaCabecalho ON {$tabelaCabecalho}_Id = {$tabelaProduto}_{$tabelaCabecalho}_Id
            LEFT JOIN D006 ON D006_Id = {$tabelaProduto}_D006_Id
            LEFT JOIN C004 ON C004_Id = {$tabelaCabecalho}_C004_Id
            LEFT JOIN T134 ON T134_Id = {$tabelaCabecalho}_T134_Id
            LEFT JOIN T202 ON T202_{$tabelaProduto}_Id = {$tabelaProduto}_Id
            LEFT JOIN D009 ON D009_Id = {$tabelaProduto}_D009_Id
            LEFT JOIN D049 ON D049_Id = D009_D049_Id
            WHERE {$tabelaProduto}_Id = $tabelaProdutoId
EOT;

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1, false, false, false, 1500, true);

        // Índice com a string da tabela.
        $geradoDados[0]['Str_Tab_Produto'] = $tabelaProduto;

        gravarTotalizacaoT004_::verificaCamposNulos($geradoDados[0]);

        return $geradoDados[0];
    }

    /**
     * Método para retornar um dicionário da consulta SQL da tabela de configuração (C031).
     *
     * @param array $dadosTabProduto Dicionário de dados da tabela de produto (T004).
     */
    public function dadosTabConfiguracao($dadosTabProduto)
    {
        // Variáveis globais. "$grid" é tratada como global para ser usado na execução do comando SQL pelo método do framework.
        global $g, $grid;

        // Campos da consulta SQL.
        $grid = array(
            array('colunaValor' => 'C031_Valor', 'colunaTitulo' => 'Valor'),
        );

        // Tabela de consulta SQL.
        $from = 'C031';

        // Array com a string dos valores a serem buscados na tabela C031.
        $valuesArrayC031 = array('somarICMSPrecoVenda', 'somarPISPrecoVenda', 'somarCOFINSPrecoVenda', 'somarIRPJPrecoVenda', 'somarCSLLPrecoVenda', 'desabilitarCotacaoEmergencia');

        // Array para armazenar os resultado das consultas.
        $resultArray = array();

        // Loop para realizar a consulta no banco para os valores do vetor "$valuesArrayC031".
        foreach ($valuesArrayC031 as $valor) {
            $extra = <<<EOT
                WHERE C031_Campo = '$valor' AND C031_C004_Id = $dadosTabProduto[C004_Id]
EOT;

            list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 0, false, false, false, 1500, true);

            // Indexar o resultado das consultas em um dicionário a ser retornado.
            $resultArray[$valor] = $geradoDados[0]['Valor'];
        }

        return $resultArray;
    }

    /**
     * Método para retornar um dicionário da consulta SQL da tabela de cabeçalho (T003).
     *
     * @param string $tabelaCabecalho String da tabela de cabeçalho (T003).
     * @param int $tabelaCabecalhoId Número identificador para a tabela de cabeçalho (T003).
     * @param string $tabelaProduto String da tabela de produto (T004).
     * @param array $dadosTabProduto Dicionário de dados da tabela de produto (T004).
     * @param array $dadosTabC031 Dicionário de dados da tabela de configuração (C031).
     */
    public function dadosTabCabecalho($tabelaCabecalho, $tabelaCabecalhoId, $tabelaProduto, $dadosTabProduto, $dadosTabC031)
    {
        // Variáveis globais. A variável "$grid" é tratada como global para ser usado na execução do comando SQL pelo método do framework.
        global $g, $grid;

        // Campos da consulta SQL.
        $grid = array(
            array('colunaValor' => '(' . $tabelaCabecalho . '_Valor_Frete + ' . $tabelaCabecalho . '_Valor_Despesas_Acessorias) / SUM(' . $tabelaProduto . '_Valor_Preco_Unitario * ' . $tabelaProduto . '_Quantidade)', 'colunaTitulo' => 'Multiplicador_Despesa'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Frete / SUM(' . $tabelaProduto . '_Valor_Preco_Unitario * ' . $tabelaProduto . '_Quantidade)', 'colunaTitulo' => 'Multiplicador_Frete'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Frete', 'colunaTitulo' => 'Valor_Frete'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Despesas_Acessorias / SUM(' . $tabelaProduto . '_Valor_Preco_Unitario * ' . $tabelaProduto . '_Quantidade)', 'colunaTitulo' => 'Multiplicador_Despesas_Acessorias'),
            array('colunaValor' => $tabelaCabecalho . '_Flag_Destino_Produto', 'colunaTitulo' => 'Flag_Destino_Produto'),
            array('colunaValor' => 'D053_Calculo_ST_Por_Dentro', 'colunaTitulo' => 'Calculo_ST_Por_Dentro'),
        );

        // Tabela de consulta SQL.
        $from = $tabelaCabecalho;

        // Cláusula WHERE.
        $extra = <<<EOT
            LEFT JOIN $tabelaProduto ON {$tabelaProduto}_{$tabelaCabecalho}_Id = {$tabelaCabecalho}_Id
            LEFT JOIN C004 ON C004_Id = {$tabelaCabecalho}_C004_Id
            LEFT JOIN D024 ON D024_Id = {$tabelaCabecalho}_D024_Id
            LEFT JOIN D018 AS UFOrigem ON UFOrigem.D018_Id = C004_D018_Id
            LEFT JOIN D018 AS UFDestino ON UFDestino.D018_Id = D024_D018_Id
            LEFT JOIN D053 ON D053_D018_Id = D024_D018_Id AND D053_C004_Id = {$tabelaCabecalho}_C004_Id
            WHERE {$tabelaCabecalho}_Id = $tabelaCabecalhoId
EOT;

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1, false, false, false, 1500, true);

        // Índice com a string da tabela.
        $geradoDados[0]['Str_Tab_Cabecalho'] = $tabelaCabecalho;

        gravarTotalizacaoT004_::verificaCamposNulos($geradoDados[0]);

        return $geradoDados[0];
    }

    /**
     * Método para cálculo do desconto unitário bruto.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorDescontoUnitarioBruto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Percentual_Desconto_Bruto'] > 0 or $tabProduto['Percentual_Desconto_Bruto'] < 0) {
            return $tabProduto['Valor_Preco_Original_Bruto'] * $tabProduto['Percentual_Desconto_Bruto'];
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do valor do desconto unitário.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorDescontoUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (($tabProduto['Percentual_Desconto'] > 0 or $tabProduto['Percentual_Desconto'] < 0) and $tabProduto['Valor_Preco_Original'] > 0) {
            return $tabProduto['Valor_Preco_Original'] * $tabProduto['Percentual_Desconto'];
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do desconto unitário.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalDesconto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Tipo'] != 'CP' or $tabConfiguracao['desabilitarCotacaoEmergencia'] == 'S' or ($tabProduto['Quantidade_Estoque'] + $tabProduto['Quantidade_OC']) >= $tabProduto['Quantidade']) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Desconto_Unitario'] * $tabProduto['Quantidade'];
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do preço original bruto.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorPrecoBruto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Valor_Preco_Original_Bruto'] > 0) {
            return round($tabProduto['Valor_Preco_Original_Bruto'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Desconto_Unitario_Bruto'], $tabProduto['Casas_Decimais_Preco']);
        } else {
            return $tabProduto['Valor_Preco_Bruto'];
        }
    }

    /**
     * Método para cálculo do preço unitário.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorPrecoUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Valor_Preco_Original'] > 0) {
            return round($tabProduto['Valor_Preco_Original'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Desconto_Unitario'], $tabProduto['Casas_Decimais_Preco']);
        } else {
            return $tabProduto['Valor_Preco_Unitario'];
        }
    }

    /**
     * Método para cálculo do preço total.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalPreco($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_ACP'] == 4) {
            return round($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] * $tabProduto['Quantidade'], 2);
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] * $tabProduto['Quantidade'];
        }
    }

    /**
     * Método para cálculo do preço total sem desconto.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalPrecoSemDesconto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_ACP'] == 4) {
            return round($tabProduto['Valor_Preco_Original'] * $tabProduto['Quantidade'], 2);
        } else {
            return $tabProduto['Valor_Preco_Original'] * $tabProduto['Quantidade'];
        }
    }

    /**
     * Método para cálculo do custo total.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalCusto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['Valor_Custo_Unitario'] * $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do peso total bruto.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function pesoBrutoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['Peso_Bruto'] * $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do peso total.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function pesoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['Peso_Unitario'] * $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do valor total do frete.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalFrete($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Rateio_Frete'] == 1) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Frete'];
        } else {
            return $tabCabecalho['Valor_Frete'] / $tabProduto['Total_Itens'];
        }
    }

    /**
     * Método para cálculo do valor unitário do frete.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorFreteUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Frete_Total'] / $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do valor total em despesas de acessoria.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalDespesasAcessorias($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Despesas_Acessorias'];
    }

    /**
     * Método para cálculo do valor em despesas de acessoria unitário.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorDespesasAcessoriasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas_Acessorias'] / $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do valor total em despesas.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalDespesas($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Despesa']) + $tabProduto['Valor_Total_Agregados'];
    }

    /**
     * Método para cálculo do valor unitário em despesas.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorDespesasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'] / $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do valor base do IPI.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseIPI($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Percentual_Reducao_IPI'] > 0) {
            $tmpA = $tabProduto['Percentual_Reducao_IPI'] / 100;
        } else {
            $tmpA = 1;
        }

        if ($tabProduto['Flag_Somar_Despesas_Base_IPI'] == 'S') {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Frete_Total'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas_Acessorias'];
        } else {
            $tmpB = 0;
        }

        if ($tabProduto['Aliquota_IPI'] > 0) {
            return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $tmpB) * $tmpA;
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do IPI.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorIPI($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_IPI'] * ($tabProduto['Aliquota_IPI'] / 100);
    }

    /**
     * Método para cálculo do custo total final.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalCustoFinal($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Somar_IPI_Base_ICMS'] == 'S') {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI'];
        } else {
            $tmpA = 0;
        }

        if ($tabProduto['Flag_Somar_Despesas_Base_ST'] == 'S') {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'];
        } else {
            $tmpB = 0;
        }

        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $tmpB + $tmpA;
    }

    /**
     * Método para cálculo do custo final unitário.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorCustoFinalUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] / $tabProduto['Quantidade'];
    }

    /**
     * Método para o cálculo do valor base do ICMS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
            $tmpA = (100 - $tabProduto['Percentual_Reducao_ICMS']) / 100;
        } else {
            $tmpA = 1;
        }

        if ($tabProduto['Aliquota_ICMS'] > 0) {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] * $tmpA;
        } else {
            $tmpB = 0;
        }

        if ($tabProduto['Flag_Somar_Fator_ICMS_Dif_Base'] == 'S') {
            return $tmpB / $tabProduto['Fator_ICMS'];
        } else {
            return $tmpB;
        }
    }

    /**
     * Método para cálculo do valor ICMS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS'] * ($tabProduto['Aliquota_ICMS'] / 100);
    }

    /**
     * Método para cálculo do valor base do ICMS desonerado.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_Desonerado($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Somar_IPI_Base_ICMS'] == 'S') {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI'];
        } else {
            $tmpA = 0;
        }

        if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
            $tmpB = (100 - $tabProduto['Percentual_Reducao_ICMS']) / 100;
        } else {
            $tmpB = 1;
        }

        if ($tabProduto['Flag_Somar_Despesas_Base_ST'] == 'S') {
            $tmpC = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'];
        } else {
            $tmpC = 0;
        }

        if ($tabProduto['Aliquota_ICMS_Oculto'] > 0 and $tabProduto['Flag_Isento_ICMS'] == 'D') {
            return (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $tmpC) * $tmpB) + $tmpA;
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do calor ICMS desonerado.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_Desonerado($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Desonerado'] * ($tabProduto['Aliquota_ICMS_Oculto'] / 100);
    }

    /**
     * Método para cálculo do valor ICMS crédito.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_Credito($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['T134_Id'] <= 0) {
            return 0;
        } else {

            if ($tabProduto['Reducao_Base_ICMS'] > 0) {
                $tmpA = $tabProduto['Reducao_Base_ICMS'] / 100;
            } else {
                $tmpA = 1;
            }

            if ($tabProduto['Flag_Somar_IPI_Base_ICMS'] == 'S') {
                $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI'];
            } else {
                $tmpB = 0;
            }

            if ($tabProduto['Aliquota_ICMS_Credito'] > 0) {
                $tmpC = ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] + $tmpB) - (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] + $tmpB) * $tmpA);
            } else {
                $tmpC = 0;
            }

            return $tmpC * ($tabProduto['Aliquota_ICMS_Credito'] / 100);
        }
    }

    /**
     * Método para cálculo do valor base ST.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseST($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (!$result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS']) {
            $result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS'] = 0;
        }

        if ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS'] > 0) {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS'];

        } else {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * ($tabProduto['ICMS_Inter_Estadual'] / 100);
        }

        if ($tabProduto['Flag_Somar_Despesas_Base_ST'] == 'S') {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'];
        } else {
            $tmpB = 0;
        }

        if ($tabProduto['Flag_ST'] == 'S') {
            if ($tabCabecalho['Flag_Destino_Produto'] == 3 and $tabCabecalho['Calculo_ST_Por_Dentro'] == 'S') {
                return (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $tmpB + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI']) * (($tabProduto['ST_VA'] / 100) + 1) - $tmpA) / (($tabProduto['ST_ICMS_Interno'] / 100 - 1) * -1);
            } else {
                return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $tmpB + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI']) * (($tabProduto['ST_VA'] / 100) + 1);
            }
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do valor ST.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorST($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        // Origem
        if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
            $tmpOrigem = (100 - $tabProduto['Percentual_Reducao_ICMS']) / 100;
        } else {
            $tmpOrigem = 1;
        }

        $valorOrigem = ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ST'] * $tmpOrigem) * ($tabProduto['ST_ICMS_Interno'] / 100);

        // Destino
        if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
            $tmpDestino = (100 - $tabProduto['Percentual_Reducao_ICMS']) / 100;
        } else {
            $tmpDestino = 1;
        }

        $valorDestino = ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] * $tmpDestino) * $tabProduto['ICMS_Inter_Estadual'] / 100;

        // Valor
        $valorST = $valorOrigem - $valorDestino;

        if ($valorST < 0) {
            return 0;
        } else {
            return $valorST;
        }
    }

    /**
     * Método para verificação do valor alíquota do PIS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function aliquotaPIS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Isento_PIS'] == 'S') {
            return 0;
        } else {
            return $tabProduto['Aliquota_PIS'];
        }
    }

    /**
     * Método para cálculo do valor base do PIS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBasePIS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo'] < $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco']) {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo'];
        } else {
            $tmpA = 0;
        }

        if ($tabProduto['Flag_Isento_PIS'] == 'S') {
            return 0;
        } else {
            if ($tabProduto['Flag_Forma_Calculo_PIS'] == 'L') {
                return $tmpA;
            } else {
                return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
            }
        }
    }

    /**
     * Método para cálculo do PIS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorPIS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_PIS'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_PIS'] * ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_PIS'] / 100);
        } else {
            return 0;
        }
    }

    /**
     * Método para verificação do valor alíquota do COFINS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function aliquotaCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Isento_COFINS'] == 'S') {
            return 0;
        } else {
            return $tabProduto['Aliquota_COFINS'];
        }
    }

    /**
     * Método para cálculo do valor base COFINS
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo'] < $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco']) {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo'];
        } else {
            $tmpA = 0;
        }

        if ($tabProduto['Flag_Isento_COFINS'] == 'S') {
            return 0;
        } else {
            if ($tabProduto['Flag_Forma_Calculo_COFINS'] == 'L') {
                return $tmpA;
            } else {
                return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
            }
        }
    }

    /**
     * Método para cálculo do valor COFINS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_COFINS'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_COFINS'] * ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_COFINS'] / 100);
        } else {
            return 0;
        }
    }

    /**
     * Método para verificação do valor alíquota do IRPJ.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function aliquotaIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Isento_IRPJ'] == 'S') {
            return 0;
        } else {
            return $tabProduto['Aliquota_IRPJ'];
        }
    }

    /**
     * Método para cálculo do valor base do IRPJ.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_IRPJ'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do valor do IRPJ.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_IRPJ'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_IRPJ'] * ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_IRPJ'] / 100);
        } else {
            return 0;
        }
    }

    /**
     * Método para verificação do valor alíquota do CSLL.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function aliquotaCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Isento_CSLL'] == 'S') {
            return 0;
        } else {
            return $tabProduto['Aliquota_CSLL'];
        }
    }

    /**
     * Método para cálculo do valor base do CSLL.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_CSLL'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do valor do CSLL.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_CSLL'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_CSLL'] * ($result[$tabProduto['Str_Tab_Produto'] . '_Aliquota_CSLL'] / 100);
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do IPV.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function IPV($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        // ICMS
        if ($tabConfiguracao['somarICMSPrecoVenda'] == 'S') {
            $ICMS = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS'] / $tabProduto['Quantidade'];
            if (!$ICMS) {
                $ICMS = 0;
            }
        } else {
            $ICMS = 0;
        }

        // PIS
        if ($tabConfiguracao['somarPISPrecoVenda'] == 'S') {
            $PIS = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_PIS'] / $tabProduto['Quantidade'];
            if (!$PIS) {
                $PIS = 0;
            }
        } else {
            $PIS = 0;
        }

        // COFINS
        if ($tabConfiguracao['somarCOFINSPrecoVenda'] == 'S') {
            $COFINS = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_COFINS'] / $tabProduto['Quantidade'];
            if (!$COFINS) {
                $COFINS = 0;
            }
        } else {
            $COFINS = 0;
        }

        // IRPJ
        if ($tabConfiguracao['somarIRPJPrecoVenda'] == 'S') {
            $IRPJ = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IRPJ'] / $tabProduto['Quantidade'];
            if (!$IRPJ) {
                $IRPJ = 0;
            }
        } else {
            $IRPJ = 0;
        }

        // CSLL
        if ($tabConfiguracao['somarCSLLPrecoVenda'] == 'S') {
            $CSLL = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_CSLL'] / $tabProduto['Quantidade'];
            if (!$CSLL) {
                $CSLL = 0;
            }
        } else {
            $CSLL = 0;
        }

        return round(($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] - $ICMS - $PIS - $COFINS - $IRPJ - $CSLL) / $tabProduto['Valor_Custo_Unitario'], 4);
    }

    /**
     * Método para cálculo do lucro.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorLucro($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return round(($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo'] * $result[$tabProduto['Str_Tab_Produto'] . '_IPV']) - ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo'] * $tabProduto['IPV_Sem_Lucro']), 2);
    }

    /**
     * Método para cálculo do percentual margem.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function percentualMargem($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        // ICMS
        if ($tabConfiguracao['somarICMSPrecoVenda'] == 'S') {
            $ICMS = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS'] / $tabProduto['Quantidade'];
            if (!$ICMS) {
                $ICMS = 0;
            }
        } else {
            $ICMS = 0;
        }

        // PIS
        if ($tabConfiguracao['somarPISPrecoVenda'] == 'S') {
            $PIS = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_PIS'] / $tabProduto['Quantidade'];
            if (!$PIS) {
                $PIS = 0;
            }
        } else {
            $PIS = 0;
        }

        // COFINS
        if ($tabConfiguracao['somarCOFINSPrecoVenda'] == 'S') {
            $COFINS = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_COFINS'] / $tabProduto['Quantidade'];
            if (!$COFINS) {
                $COFINS = 0;
            }
        } else {
            $COFINS = 0;
        }

        // IRPJ
        if ($tabConfiguracao['somarIRPJPrecoVenda'] == 'S') {
            $IRPJ = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IRPJ'] / $tabProduto['Quantidade'];
            if (!$IRPJ) {
                $IRPJ = 0;
            }
        } else {
            $IRPJ = 0;
        }

        // CSLL
        if ($tabConfiguracao['somarCSLLPrecoVenda'] == 'S') {
            $CSLL = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_CSLL'] / $tabProduto['Quantidade'];
            if (!$CSLL) {
                $CSLL = 0;
            }
        } else {
            $CSLL = 0;
        }

        return round(((($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] - $ICMS - $PIS - $COFINS - $IRPJ - $CSLL) - $tabProduto['Valor_Custo_Unitario']) / ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] - $ICMS - $PIS - $COFINS - $IRPJ - $CSLL)) * 100, 2);
    }

    /**
     * Método para cálculo o valor de tributos transparência.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTribTransparencia($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (!$result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco']) {
            $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] = 0;
        }

        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * ($tabProduto['Percentual_Trib_Transparencia'] / 100);
    }

    /**
     * Método para cálculo da cubagem total.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function totalCubagem($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['Cubagem_Unitaria'] * $tabProduto['Quantidade'];
    }
}
