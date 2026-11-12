<?php
namespace hardness;

/**
 * @author Ricardo Norio Miyata <ricardo@hardness.com.br>
 */
definirClasse('gravarTotalizacaoT008');

/**
 * \arg A classe <em>gravarTotalizacaoT008</em>, possui um método principal e submétodos para realização de cálculos de imposto, desconto, dentre outros.
 * \arg Existem 3 arrays principais de dados, cabeçalho (T007), produto (T008) e configurações (C031). Estes arrays são populados em métodos diferentes de consultas no banco, retornando um dicionário indexado com o nome dos campos.
 * \arg Todos os submétodos, por padrão, recebem 4 arrays, 3 de dados ($tabCabecalho, $tabProduto e $tabConfiguracao) e 1 para os resultados ($resultArray) das operações já realizadas.
 */
class gravarTotalizacaoT008_
{
    /**
     * Método principal para fazer a chamada de submétodos para oprerações no banco e cálculos.
     *
     * @param string $tabelaCabecalho String da tabela de cabeçalho (T007).
     * @param int $tabelaCabecalhoId Número identificador para a tabela de cabeçalho (T007).
     * @param string $tabelaProduto String da tabela de produto (T008).
     * @param int $tabelaProdutoId Número identificador para a tabela de produto (T008).
     */
    public function gravarTotalizacao($tabelaCabecalho, $tabelaCabecalhoId, $tabelaProduto, $tabelaProdutoId)
    {
        // Variável global para chamar os métodos SQL do framework.
        global $g;

        // ++++++++++ C O N S U L T A S  D E  B A N C O ++++++++++
        // Resultado da consulta SQL na tabela de produtos (T008).
        $tabProduto = gravarTotalizacaoT008_::dadosTabProduto($tabelaCabecalho, $tabelaProduto, $tabelaProdutoId);
        // Resultado da consulta SQL na tabela de configurações (C031).
        $tabConfiguracao = gravarTotalizacaoT008_::dadosTabConfiguracao($tabProduto);
        // Resultado da consulta SQL na tabela de cabeçalho (T007).
        $tabCabecalho = gravarTotalizacaoT008_::dadosTabCabecalho($tabelaCabecalho, $tabelaCabecalhoId, $tabelaProduto, $tabProduto, $tabConfiguracao);

        // ++++++++++ O P E R A Ç Õ E S ++++++++++
        // Dicionário para o resultado dos cálculos.
        $resultArray = array();

        // Módulo independente da forma de cálculo.
        $resultArray[$tabelaProduto . '_Valor_Desconto_Unitario'] = gravarTotalizacaoT008_::valorDescontoUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Preco_Unitario'] = gravarTotalizacaoT008_::valorPrecoUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Preco'] = gravarTotalizacaoT008_::valorTotalPreco($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Preco_Sem_Desconto'] = gravarTotalizacaoT008_::valorTotalPrecoSemDesconto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        $resultArray[$tabelaProduto . '_Valor_Total_Desconto'] = gravarTotalizacaoT008_::valorTotalDesconto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // Possui divergencias

        if ($tabProduto['Flag_Calculo_Manual'] === 'S') {
            // Carregamento prévio dos valores de impostos.
            gravarTotalizacaoT008_::dadosPreTabProduto($tabelaProduto, $tabelaProdutoId, $resultArray);

            // Módulo preço.
            $resultArray[$tabelaProduto . '_Valor_Total_Custo'] = gravarTotalizacaoT008_::valorTotalCusto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo peso.
            $resultArray[$tabelaProduto . '_Peso_Total'] = gravarTotalizacaoT008_::pesoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Peso_Bruto_Total'] = gravarTotalizacaoT008_::pesoBrutoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo frete.
            $resultArray[$tabelaProduto . '_Valor_Frete_Unitario'] = gravarTotalizacaoT008_::valorFreteUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo seguro.
            $resultArray[$tabelaProduto . '_Valor_Total_Seguro'] = gravarTotalizacaoT008_::valorTotalSeguro($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Seguro_Unitario'] = gravarTotalizacaoT008_::valorSeguroUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo despesas.
            $resultArray[$tabelaProduto . '_Valor_Despesas_Acessorias_Unitario'] = gravarTotalizacaoT008_::valorDespesasAcessoriasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Total_Despesas'] = gravarTotalizacaoT008_::valorTotalDespesas($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004 == T008  =! T008
            $resultArray[$tabelaProduto . '_Valor_Despesas_Unitario'] = gravarTotalizacaoT008_::valorDespesasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo custo final.
            $resultArray[$tabelaProduto . '_Valor_Total_Custo_Final'] = gravarTotalizacaoT008_::valorTotalCustoFinal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // Tem algumas mudanças "radicais"
            $resultArray[$tabelaProduto . '_Valor_Custo_Final_Unitario'] = gravarTotalizacaoT008_::valorCustoFinalUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo Cálculos Finais.
            $resultArray[$tabelaProduto . '_IPV'] = gravarTotalizacaoT008_::IPV($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Lucro'] = gravarTotalizacaoT008_::valorLucro($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Percentual_Margem'] = gravarTotalizacaoT008_::percentualMargem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Total_Cubagem'] = gravarTotalizacaoT008_::totalCubagem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Quantidade_Tributavel'] = gravarTotalizacaoT008_::quantidadeTributavel($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Preco_Sem_Desconto_Unitario_Tributavel'] = gravarTotalizacaoT008_::valorPrecoSemDescontoUnitarioTributavel($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        } else {
            // Módulo custo.
            $resultArray[$tabelaProduto . '_Valor_Total_Custo'] = gravarTotalizacaoT008_::valorTotalCusto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo peso.
            $resultArray[$tabelaProduto . '_Peso_Total'] = gravarTotalizacaoT008_::pesoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Peso_Bruto_Total'] = gravarTotalizacaoT008_::pesoBrutoTotal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo frete.
            $resultArray[$tabelaProduto . '_Valor_Total_Frete'] = gravarTotalizacaoT008_::valorTotalFrete($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004 != T008  == T008
            $resultArray[$tabelaProduto . '_Valor_Frete_Unitario'] = gravarTotalizacaoT008_::valorFreteUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo seguro.
            $resultArray[$tabelaProduto . '_Valor_Total_Seguro'] = gravarTotalizacaoT008_::valorTotalSeguro($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Seguro_Unitario'] = gravarTotalizacaoT008_::valorSeguroUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo despesas.
            $resultArray[$tabelaProduto . '_Valor_Total_Despesas_Acessorias'] = gravarTotalizacaoT008_::valorTotalDespesasAcessorias($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Despesas_Acessorias_Unitario'] = gravarTotalizacaoT008_::valorDespesasAcessoriasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Total_Despesas'] = gravarTotalizacaoT008_::valorTotalDespesas($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004 == T008  =! T008
            $resultArray[$tabelaProduto . '_Valor_Despesas_Unitario'] = gravarTotalizacaoT008_::valorDespesasUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo IPI.
            $resultArray[$tabelaProduto . '_Valor_Base_IPI'] = gravarTotalizacaoT008_::valorBaseIPI($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_IPI'] = gravarTotalizacaoT008_::valorIPI($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo custo final.
            $resultArray[$tabelaProduto . '_Valor_Total_Custo_Final'] = gravarTotalizacaoT008_::valorTotalCustoFinal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Custo_Final_Unitario'] = gravarTotalizacaoT008_::valorCustoFinalUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo ICMS.
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Sem_Reducao'] = gravarTotalizacaoT008_::valorBaseICMS_SemReducao($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS'] = gravarTotalizacaoT008_::valorBaseICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Diferido'] = gravarTotalizacaoT008_::valorBaseICMS_Diferido($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Desonerado'] = gravarTotalizacaoT008_::valorBaseICMS_Desonerado($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS'] = gravarTotalizacaoT008_::valorICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Oculto'] = gravarTotalizacaoT008_::valorBaseICMS_Oculto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS_Oculto'] = gravarTotalizacaoT008_::valorICMS_Oculto($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS_Desonerado'] = gravarTotalizacaoT008_::valorICMS_Desonerado($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Substituicao'] = gravarTotalizacaoT008_::valorBaseICMS_Substituicao($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004_Valor_Base_ST == T006_Valor_Base_ICMS_Substituicao == T008_Valor_Base_ICMS_Substituicao
            $resultArray[$tabelaProduto . '_Valor_ICMS_Substituicao'] = gravarTotalizacaoT008_::valorICMS_Substituicao($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray); // T004_Valor_ST == T006_Valor_ICMS_Substituicao_Retencao == T008_Valor_ICMS_Substituicao_Retencao
            $resultArray[$tabelaProduto . '_Valor_ICMS_Substituicao_Retencao'] = gravarTotalizacaoT008_::valorICMS_SubstituicaoRetencao($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Interno'] = gravarTotalizacaoT008_::valorBaseICMS_Interno($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS_Interno'] = gravarTotalizacaoT008_::valorICMS_Interno($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Inter_Estadual'] = gravarTotalizacaoT008_::valorBaseICMS_InterEstadual($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS_Inter_Estadual'] = gravarTotalizacaoT008_::valorICMS_InterEstadual($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Impresso'] = gravarTotalizacaoT008_::valorBaseICMS_Impresso($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS_Credito'] = gravarTotalizacaoT008_::valorICMS_Credito($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_Partilha_ICMS'] = gravarTotalizacaoT008_::valorBasePartilhaICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Percentual_Partilha_ICMS'] = gravarTotalizacaoT008_::percentualPartilhaICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Partilha_ICMS_Destino'] = gravarTotalizacaoT008_::valorPartilhaICMS_Destino($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Partilha_ICMS_Origem'] = gravarTotalizacaoT008_::valorPartilhaICMS_Origem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_ICMS_Substituicao_Retido'] = gravarTotalizacaoT008_::valorBaseICMS_SubstituicaoRetido($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_ICMS_Substituicao_Retido'] = gravarTotalizacaoT008_::valorICMS_SubstituicaoRetido($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo FCP.
            $resultArray[$tabelaProduto . '_Valor_FCP_Partilha'] = gravarTotalizacaoT008_::valorFCP_Partilha($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_FCP'] = gravarTotalizacaoT008_::valorBase_FCP($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_FCP'] = gravarTotalizacaoT008_::valorFCP($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_FCP_Ret'] = gravarTotalizacaoT008_::valorBaseFCP_Ret($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_FCP_Retido'] = gravarTotalizacaoT008_::valorFCP_Retido($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_FCP_ST'] = gravarTotalizacaoT008_::valorBaseFCP_ST($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_FCP_ST'] = gravarTotalizacaoT008_::valorFCP_ST($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_FCP_Dest'] = gravarTotalizacaoT008_::valorBaseFCP_Dest($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Aliquota_ST_FCP'] = gravarTotalizacaoT008_::aliquotaST_FCP($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo PIS.
            $resultArray[$tabelaProduto . '_Aliquota_PIS'] = gravarTotalizacaoT008_::aliquotaPIS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_PIS'] = gravarTotalizacaoT008_::valorBasePIS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_PIS'] = gravarTotalizacaoT008_::valorPIS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo COFINS.
            $resultArray[$tabelaProduto . '_Aliquota_COFINS'] = gravarTotalizacaoT008_::aliquotaCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_COFINS'] = gravarTotalizacaoT008_::valorBaseCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_COFINS'] = gravarTotalizacaoT008_::valorCOFINS($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo IRPJ.
            $resultArray[$tabelaProduto . '_Aliquota_IRPJ'] = gravarTotalizacaoT008_::aliquotaIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_IRPJ'] = gravarTotalizacaoT008_::valorBaseIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_IRPJ'] = gravarTotalizacaoT008_::valorIRPJ($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo CSLL.
            $resultArray[$tabelaProduto . '_Aliquota_CSLL'] = gravarTotalizacaoT008_::aliquotaCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Base_CSLL'] = gravarTotalizacaoT008_::valorBaseCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_CSLL'] = gravarTotalizacaoT008_::valorCSLL($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // // Módulo tributos.
            $resultArray[$tabelaProduto . '_Valor_Trib_Transparencia'] = gravarTotalizacaoT008_::valorTribTransparencia($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Trib_Federal'] = gravarTotalizacaoT008_::valorTribFederal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Trib_Estadual'] = gravarTotalizacaoT008_::valorTribEstadual($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Trib_Municipal'] = gravarTotalizacaoT008_::valorTribMunicipal($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);

            // Módulo Cálculos Finais.
            $resultArray[$tabelaProduto . '_IPV'] = gravarTotalizacaoT008_::IPV($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Lucro'] = gravarTotalizacaoT008_::valorLucro($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Percentual_Margem'] = gravarTotalizacaoT008_::percentualMargem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Total_Cubagem'] = gravarTotalizacaoT008_::totalCubagem($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Quantidade_Tributavel'] = gravarTotalizacaoT008_::quantidadeTributavel($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
            $resultArray[$tabelaProduto . '_Valor_Preco_Sem_Desconto_Unitario_Tributavel'] = gravarTotalizacaoT008_::valorPrecoSemDescontoUnitarioTributavel($tabCabecalho, $tabProduto, $tabConfiguracao, $resultArray);
        }

        // Método do framework para executar o UPDATE.
        $g['sqlAuto']->gerarSQLUpdate($tabelaProduto, $tabelaProduto . '_Id', $tabelaProdutoId, null, $resultArray, true, false);
    }

    /**
     * Método para carregar previamente alguns valores.
     *
     * @param string $tabelaProduto String da tabela produto.
     * @param int $tabelaProdutoId Número identificador da tabela produto.
     * @param array $result Dicionário com os valores indexados em campos da tabela.
     */
    public function dadosPreTabProduto($tabelaProduto, $tabelaProdutoId, &$result)
    {
        // Variáveis globais. A variável "$grid" é tratada como global para ser usado na execução do comando SQL pelo método do framework.
        global $g, $grid;

        // Campos da consulta SQL.
        $grid = array(
            array('colunaValor' => $tabelaProduto . '_Valor_IPI', 'colunaTitulo' => 'Valor_IPI'),
            array('colunaValor' => $tabelaProduto . '_Valor_ICMS', 'colunaTitulo' => 'Valor_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Valor_PIS', 'colunaTitulo' => 'Valor_PIS'),
            array('colunaValor' => $tabelaProduto . '_Valor_COFINS', 'colunaTitulo' => 'Valor_COFINS'),
            array('colunaValor' => $tabelaProduto . '_Valor_IRPJ', 'colunaTitulo' => 'Valor_IRPJ'),
            array('colunaValor' => $tabelaProduto . '_Valor_CSLL', 'colunaTitulo' => 'Valor_CSLL'),
            array('colunaValor' => $tabelaProduto . '_Valor_Total_Despesas_Acessorias', 'colunaTitulo' => 'Valor_Total_Despesas_Acessorias'),
            array('colunaValor' => $tabelaProduto . '_Valor_Total_Frete', 'colunaTitulo' => 'Valor_Total_Frete'),
        );

        // Tabela de consulta SQL.
        $from = $tabelaProduto;

        // Cláusula WHERE.
        $extra = <<<EOT
            WHERE {$tabelaProduto}_Id = $tabelaProdutoId
EOT;

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1, false, false, false, 1500, true);

        $geradoDados[0] = gravarTotalizacaoT008_::verificaCamposNulos($geradoDados[0]);

        // Insere os valores obtidos do SELECT no dicionário de resultados, passado por referência.
        foreach ($geradoDados[0] as $key => $value) {
            $result[$tabelaProduto . '_' . $key] = $value;
        }
    }

    /**
     * Método para zerar campos nulos de um dicionário.
     *
     * @param array $array Dicionário com valores indexados .
     */
    public function verificaCamposNulos($array)
    {
        foreach ($array as $key => $value) {
            if (!$array[$key]) {
                $array[$key] = 0;
            }
        }

        return $array;
    }

    /**
     * Método para retornar um dicionário da consulta SQL da tabela de produto (T008).
     *
     * @param string $tabelaCabecalho String da tabela de cabeçalho (T007).
     * @param string $tabelaProduto String da tabela de produto (T008).
     * @param int $tabelaProdutoId Número identificador para a tabela de produto (T008).
     */
    public function dadosTabProduto($tabelaCabecalho, $tabelaProduto, $tabelaProdutoId)
    {
        // Variáveis globais. A variável "$grid" é tratada como global para ser usado na execução do comando SQL pelo método do framework.
        global $g, $grid;

        // Campos da consulta SQL.
        $grid = array(
            array('colunaValor' => $tabelaProduto . '_C004_Id', 'colunaTitulo' => 'C004_Id'),
            array('colunaValor' => $tabelaProduto . '_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Valor_Preco_Sem_Desconto_Unitario'),
            array('colunaValor' => $tabelaCabecalho . '_Flag_ACP', 'colunaTitulo' => 'Flag_ACP'),
            array('colunaValor' => $tabelaProduto . '_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'SUM(ROUND(' . $tabelaProduto . '_Valor_Total_Desconto, 2))', 'colunaTitulo' => 'Soma_Desconto'),
            array('colunaValor' => 'ROUND(' . $tabelaCabecalho . '_Valor_Desconto, 2)', 'colunaTitulo' => 'Total_Desconto'),
            array('colunaValor' => $tabelaProduto . '_Flag_Calculo_Manual', 'colunaTitulo' => 'Flag_Calculo_Manual'),
            array('colunaValor' => $tabelaProduto . '_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor_Custo_Unitario'),
            array('colunaValor' => $tabelaProduto . '_Peso_Unitario', 'colunaTitulo' => 'Peso_Unitario'),
            array('colunaValor' => $tabelaProduto . '_Peso_Bruto', 'colunaTitulo' => 'Peso_Bruto'),
            array('colunaValor' => 'D006_Flag_Somar_IPI_Base_ICMS', 'colunaTitulo' => 'Flag_Somar_IPI_Base_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Reducao_IPI', 'colunaTitulo' => 'Percentual_Reducao_IPI'),
            array('colunaValor' => 'D006_Flag_Somar_Despesas_Base_IPI', 'colunaTitulo' => 'Flag_Somar_Despesas_Base_IPI'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_IPI', 'colunaTitulo' => 'Aliquota_IPI'),
            array('colunaValor' => $tabelaProduto . '_Valor_Total_Imposto_Importacao', 'colunaTitulo' => 'Valor_Total_Imposto_Importacao'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_ICMS', 'colunaTitulo' => 'Aliquota_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Reducao_ICMS', 'colunaTitulo' => 'Percentual_Reducao_ICMS'),
            array('colunaValor' => 'D006_Flag_Somar_Fator_ICMS_Dif_Base', 'colunaTitulo' => 'Flag_Somar_Fator_ICMS_Dif_Base'),
            array('colunaValor' => '(100 - ' . $tabelaProduto . '_ST_ICMS_Interno) / 100', 'colunaTitulo' => 'Fator_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_ICMS_Oculto', 'colunaTitulo' => 'Aliquota_ICMS_Oculto'),
            array('colunaValor' => 'D006_Flag_Isento_ICMS', 'colunaTitulo' => 'Flag_Isento_ICMS'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Reducao_ICMS_Oculto', 'colunaTitulo' => 'Percentual_Reducao_ICMS_Oculto'),
            array('colunaValor' => $tabelaProduto . '_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS_Inter_Estadual'),
            array('colunaValor' => 'D006_Flag_Somar_Despesas_Base_ST', 'colunaTitulo' => 'Flag_Somar_Despesas_Base_ST'),
            array('colunaValor' => $tabelaProduto . '_Flag_ST', 'colunaTitulo' => 'Flag_ST'),
            array('colunaValor' => 'D053_Calculo_ST_Por_Dentro', 'colunaTitulo' => 'Calculo_ST_Por_Dentro'),
            array('colunaValor' => $tabelaProduto . '_ST_VA', 'colunaTitulo' => 'ST_VA'),
            array('colunaValor' => $tabelaProduto . '_ST_ICMS_Interno', 'colunaTitulo' => 'ST_ICMS_Interno'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => 'Percentual_Reducao_ICMS_ST'),
            array('colunaValor' => $tabelaProduto . '_Flag_Situacao_Tributaria', 'colunaTitulo' => 'Flag_Situacao_Tributaria'),
            array('colunaValor' => 'T134_Reducao_Base_ICMS', 'colunaTitulo' => 'Reducao_Base_ICMS'),
            array('colunaValor' => 'T134_Id', 'colunaTitulo' => 'T134_Id'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_ICMS_Credito', 'colunaTitulo' => 'Aliquota_ICMS_Credito'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Partilha_ICMS_Destino', 'colunaTitulo' => 'Percentual_Partilha_ICMS_Destino'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Partilha_ICMS_Origem', 'colunaTitulo' => 'Percentual_Partilha_ICMS_Origem'),
            array('colunaValor' => 'SUM(T290_Valor_Base_ICMS_Substituicao)', 'colunaTitulo' => 'BC_ICMS_ST_Compra'),
            array('colunaValor' => 'SUM(T290_Valor_ICMS_Substituicao)', 'colunaTitulo' => 'Valor_ICMS_ST_Compra'),
            array('colunaValor' => $tabelaProduto . '_Percentual_FCP', 'colunaTitulo' => 'Percentual_FCP'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_PIS', 'colunaTitulo' => 'Flag_Isento_PIS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_PIS', 'colunaTitulo' => 'Aliquota_PIS'),
            array('colunaValor' => 'D006_Flag_Forma_Calculo_PIS', 'colunaTitulo' => 'Flag_Forma_Calculo_PIS'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_COFINS', 'colunaTitulo' => 'Flag_Isento_COFINS'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_COFINS', 'colunaTitulo' => 'Aliquota_COFINS'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_IRPJ', 'colunaTitulo' => 'Flag_Isento_IRPJ'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_IRPJ', 'colunaTitulo' => 'Aliquota_IRPJ'),
            array('colunaValor' => $tabelaProduto . '_Flag_Isento_CSLL', 'colunaTitulo' => 'Flag_Isento_CSLL'),
            array('colunaValor' => $tabelaProduto . '_Aliquota_CSLL', 'colunaTitulo' => 'Aliquota_CSLL'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Trib_Transparencia', 'colunaTitulo' => 'Percentual_Trib_Transparencia'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Trib_Federal', 'colunaTitulo' => 'Percentual_Trib_Federal'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Trib_Estadual', 'colunaTitulo' => 'Percentual_Trib_Estadual'),
            array('colunaValor' => $tabelaProduto . '_Percentual_Trib_Municipal', 'colunaTitulo' => 'Percentual_Trib_Municipal'),
            array('colunaValor' => $tabelaProduto . '_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV_Sem_Lucro'),
            array('colunaValor' => $tabelaProduto . '_Cubagem_Unitaria', 'colunaTitulo' => 'Cubagem_Unitaria'),
            array('colunaValor' => 'D006_Flag_Unidade_Tributavel', 'colunaTitulo' => 'Flag_Unidade_Tributavel'),
            array('colunaValor' => 'D001_Quantidade_Tributavel', 'colunaTitulo' => 'Quantidade_Tributavel'),
        );

        // Tabela de consulta SQL.
        $from = $tabelaProduto;

        // Cláusula WHERE.
        $extra = <<<EOT
            LEFT JOIN $tabelaCabecalho ON {$tabelaCabecalho}_Id = {$tabelaProduto}_{$tabelaCabecalho}_Id
            LEFT JOIN D006 ON D006_Id = {$tabelaProduto}_D006_Id
            LEFT JOIN C004 ON C004_Id = {$tabelaCabecalho}_C004_Id
            LEFT JOIN D024 ON D024_Id = {$tabelaCabecalho}_D024_Id
            LEFT JOIN D018 AS UFOrigem ON UFOrigem.D018_Id = C004_D018_Id
            LEFT JOIN D018 AS UFDestino ON UFDestino.D018_Id = D024_D018_Id
            LEFT JOIN D053 ON D053_D018_Id = D024_D018_Id AND D053_C004_Id = {$tabelaCabecalho}_C004_Id
            LEFT JOIN T290 ON T290_{$tabelaProduto}_Id = {$tabelaProduto}_Id
            LEFT JOIN T134 ON T134_Id = {$tabelaCabecalho}_T134_Id
            LEFT JOIN D009 ON D009_Id = {$tabelaProduto}_D009_Id
            LEFT JOIN D001 ON D001_Id = D009_D001_Id
            WHERE {$tabelaProduto}_Id = $tabelaProdutoId
EOT;

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1, false, false, false, 1500, true);

        // Índice com a string da tabela.
        $geradoDados[0]['Str_Tab_Produto'] = $tabelaProduto;

        $geradoDados[0] = gravarTotalizacaoT008_::verificaCamposNulos($geradoDados[0]);

        return $geradoDados[0];
    }

    /**
     * Método para retornar um dicionário da consulta SQL da tabela de configuração (C031).
     *
     * @param array $dadosTabProduto Dicionário de dados da tabela de produto (T008).
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
        $valuesArrayC031 = array('somarICMSPrecoVenda', 'somarPISPrecoVenda', 'somarCOFINSPrecoVenda', 'somarIRPJPrecoVenda', 'somarCSLLPrecoVenda', 'ratearFreteVendaNoCusto');

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
     * Método para retornar um dicionário da consulta SQL da tabela de cabeçalho (T007).
     *
     * @param string $tabelaCabecalho String da tabela de cabeçalho (T007).
     * @param int $tabelaCabecalhoId Número identificador para a tabela de cabeçalho (T007).
     * @param string $tabelaProduto String da tabela de produto (T008).
     * @param array $dadosTabProduto Dicionário de dados da tabela de produto (T008).
     * @param array $dadosTabC031 Dicionário de dados da tabela de configuração (C031).
     */
    public function dadosTabCabecalho($tabelaCabecalho, $tabelaCabecalhoId, $tabelaProduto, $dadosTabProduto, $dadosTabC031)
    {
        // Variáveis globais. A variável "$grid" é tratada como global para ser usado na execução do comando SQL pelo método do framework.
        global $g, $grid;

        // Campos da consulta SQL.
        $grid = array(
            array('colunaValor' => 'C004_Casas_Decimais_Preco', 'colunaTitulo' => 'Casas_Decimais_Preco'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Desconto / ' . $tabelaCabecalho . '_Valor_Total_Produtos', 'colunaTitulo' => 'Percentual_Desconto'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Frete / SUM(' . $tabelaProduto . '_Valor_Total_Preco)', 'colunaTitulo' => 'Multiplicador_Frete'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Seguro / SUM(' . $tabelaProduto . '_Valor_Total_Preco)', 'colunaTitulo' => 'Multiplicador_Seguro'),
            array('colunaValor' => $tabelaCabecalho . '_Valor_Despesas_Acessorias / SUM(' . $tabelaProduto . '_Valor_Total_Preco)', 'colunaTitulo' => 'Multiplicador_Despesas_Acessorias'),
            array('colunaValor' => '(' . $tabelaCabecalho . '_Valor_Frete + ' . $tabelaCabecalho . '_Valor_Despesas_Acessorias + ' . $tabelaCabecalho . '_Valor_Seguro) / SUM(' . $tabelaProduto . '_Valor_Total_Preco)', 'colunaTitulo' => 'Multiplicador_Despesa'),
            array('colunaValor' => $tabelaCabecalho . '_Flag_Revenda_Consumidor', 'colunaTitulo' => 'Flag_Revenda_Consumidor'),
            array('colunaValor' => $tabelaCabecalho . '_Flag_Destino_Produto', 'colunaTitulo' => 'Flag_Destino_Produto'),
        );

        // Tabela de consulta SQL.
        $from = $tabelaCabecalho;

        // Cláusula WHERE.
        $extra = <<<EOT
            LEFT JOIN $tabelaProduto ON {$tabelaProduto}_{$tabelaCabecalho}_Id = {$tabelaCabecalho}_Id
            LEFT JOIN C004 ON C004_Id = {$tabelaCabecalho}_C004_Id
            WHERE {$tabelaCabecalho}_Id = $tabelaCabecalhoId
EOT;

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1, false, false, false, 1500, true);

        // Índice com a string da tabela.
        $geradoDados[0]['Str_Tab_Cabecalho'] = $tabelaCabecalho;

        $geradoDados[0] = gravarTotalizacaoT008_::verificaCamposNulos($geradoDados[0]);

        return $geradoDados[0];
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
        return 0;
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
        $tmpA = round($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Percentual_Desconto'], 2);

        if ($tabProduto['Soma_Desconto'] != $tabProduto['Total_Desconto']) {
            $diferencaDesconto = $tabProduto['Soma_Desconto'] - $tabProduto['Total_Desconto'];
        }
        $tmpA -= $diferencaDesconto;

        return $tmpA;
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
        return round($tabProduto['Valor_Preco_Sem_Desconto_Unitario'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Desconto_Unitario'], $tabCabecalho['Casas_Decimais_Preco']);
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
            return round($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] * $tabProduto['Quantidade'], 2);
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Preco_Unitario'] * $tabProduto['Quantidade'];
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
        if ($tabCabecalho['Multiplicador_Frete'] > 0 and $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Frete'] <= 0.01) {
            return 0.01;
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Frete'];
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
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Frete'] / $tabProduto['Quantidade'];
    }

    /**
     * Método para cálculo do valor total do seguro.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTotalSeguro($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Seguro'];
    }

    /**
     * Método para cálculo do seguro unitário.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorSeguroUnitario($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Seguro'] / $tabProduto['Quantidade'];
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
        if (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Despesas_Acessorias']) < 0.01 and $tabCabecalho['Multiplicador_Despesas_Acessorias'] > 0) {
            return 0.01;
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Despesas_Acessorias'];
        }
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
        return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] * $tabCabecalho['Multiplicador_Despesa']);
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
            $tmpA = $tabProduto['Percentual_Reducao_IPI'] /= 100;
        } else {
            $tmpA = 1;
        }

        if ($tabProduto['Flag_Somar_Despesas_Base_IPI'] == 'S') {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Frete'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Seguro'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas_Acessorias'];
        } else {
            $tmpB = 0;
        }

        if ($tabProduto['Aliquota_IPI'] > 0) {
            return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $tabProduto['Valor_Total_Imposto_Importacao'] + $tmpB) * $tmpA;
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
        return round($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_IPI'] * ($tabProduto['Aliquota_IPI'] / 100), 2);
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
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI'];
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'];
        }

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
     * Método para cálculo do valor base do ICMS diferido.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_Diferido($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Sem_Reducao'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS'];
    }

    /**
     * Método para cálculo do valor base do ICMS interno.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_Interno($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Percentual_Reducao_ICMS_ST'] > 0) {
            return (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI']) * (($tabProduto['ST_VA'] / 100) + 1)) * ((100 - $tabProduto['Percentual_Reducao_ICMS_ST']) / 100);
        } else {
            return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Despesas'] + $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI']) * (($tabProduto['ST_VA'] / 100) + 1);
        }
    }

    /**
     * Método para cálculo do valor ICMS interno.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_Interno($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Interno'] * ($tabProduto['ST_ICMS_Interno'] / 100);
    }

    /**
     * Método para cálculo do valor base do ICMS interestadual.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_InterEstadual($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] * ((100 - $tabProduto['Percentual_Reducao_ICMS']) / 100);
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'];
        }
    }

    /**
     * Método para cálculo do valor ICMS interestadual.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_InterEstadual($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Inter_Estadual'] * $tabProduto['ICMS_Inter_Estadual']) / 100;
    }

    /**
     * Método para cálculo do valor base do ICMS impresso.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_Impresso($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (substr($tabProduto['Flag_Situacao_Tributaria'], 1, 2) == 51 or substr($tabProduto['Flag_Situacao_Tributaria'], 1, 1) == 90 or substr($tabProduto['Flag_Situacao_Tributaria'], 1, 1) == 10) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Sem_Reducao'];
        } else {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS'];
        }
    }

    /**
     * Método para cálculo do valor base do ICMS substituição retido.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_SubstituicaoRetido($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['BC_ICMS_ST_Compra'];
    }

    /**
     * Método para cálculo do valor ICMS retido.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_SubstituicaoRetido($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['Valor_ICMS_ST_Compra'];
    }

    /**
     * Método para cálculo do percentual partilha ICMS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function percentualPartilhaICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['ST_ICMS_Interno'] - $tabProduto['ICMS_Inter_Estadual'];
    }

    /**
     * Método para cálculo do valor base da partilha ICMS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBasePartilhaICMS($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Aliquota_ICMS'] > 0) {
            if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
                $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Sem_Reducao'] * ((100 - $tabProduto['Percentual_Reducao_ICMS']) / 100);
            } else {
                $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Sem_Reducao'];
            }
        } else {
            $tmpA = 0;
        }

        if ($tabProduto['Flag_Somar_Fator_ICMS_Dif_Base'] == 'S') {
            return $tmpA / $tabProduto['Fator_ICMS'];
        } else {
            return $tmpA;
        }
    }

    /**
     * Método para cálculo do valor partilha ICMS destino.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorPartilhaICMS_Destino($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_Partilha_ICMS'] * ($result[$tabProduto['Str_Tab_Produto'] . '_Percentual_Partilha_ICMS'] / 100)) * ($tabProduto['Percentual_Partilha_ICMS_Destino'] / 100);
    }

    /**
     * Método para cálculo do valor partilha ICMS origem.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorPartilhaICMS_Origem($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_Partilha_ICMS'] * ($result[$tabProduto['Str_Tab_Produto'] . '_Percentual_Partilha_ICMS'] / 100)) * ($tabProduto['Percentual_Partilha_ICMS_Origem'] / 100);
    }

    /**
     * Método para cálculo do valor FCP partilha.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorFCP_Partilha($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_Partilha_ICMS'] * ($tabProduto['Percentual_FCP'] / 100);
    }

    /**
     * Método para cálculo do valor base FCP.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBase_FCP($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Impresso'];
    }

    /**
     * Método para cálculo do valor base FCP.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseFCP($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $tabProduto['valorBaseICMS_Impresso'];
    }

    /**
     * Método para cálculo do valor FCP.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorFCP($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_FCP'] * ($tabProduto['Percentual_FCP'] / 100);
    }

    /**
     * Método para cálculo do valor base FCP Retenção.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseFCP_Ret($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Substituicao_Retido'];
    }

    /**
     * Método para cálculo do valor FCP retido.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorFCP_Retido($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Substituicao_Retido'] * ($tabProduto['Percentual_FCP'] / 100);
    }

    /**
     * Método para cálculo do valor base FCP ST.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseFCP_ST($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Substituicao'];
    }

    /**
     * Método para cálculo do valor FCP ST.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorFCP_ST($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_ICMS_Substituicao_Retencao'] > 0) {
            $tmpA = ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_FCP_ST'] - $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_FCP']) * ($tabProduto['Percentual_FCP'] / 100);
        } else {
            $tmpA = 0;
        }

        if ($tmpA < 0) {
            return $tmpA * (-1);
        } else {
            return $tmpA;
        }
    }

    /**
     * Método para cálculo do valor base FCP destino.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseFCP_Dest($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_Partilha_ICMS'];
    }

    /**
     * Método para cálculo do valor alíquota ST FCP.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function aliquotaST_FCP($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Percentual_Partilha_ICMS'] + $tabProduto['Percentual_FCP'];
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
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Sem_Reducao'] * $tmpA;
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
     * Método para o cálculo do valor base do ICMS sem redução.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_SemReducao($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Aliquota_ICMS'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'];
        } else {
            return 0;
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
     * Método para cálculo do valor base do ICMS oculto.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_Oculto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Percentual_Reducao_ICMS_Oculto'] > 0) {
            $tmpA = (100 - $tabProduto['Percentual_Reducao_ICMS_Oculto']) / 100;
        } else {
            $tmpA = 1;
        }

        if ($tabProduto['Aliquota_ICMS_Oculto'] > 0) {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] * $tmpA;
        } else {
            return 0;
        }
    }

    /**
     * Método para cálculo do valor ICMS oculto.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_Oculto($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Oculto'] * ($tabProduto['Aliquota_ICMS_Oculto'] / 100);
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
        if ($tabProduto['Percentual_Reducao_ICMS'] > 0) {
            $tmpA = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS'];
        } else {
            $tmpA = 0;
        }

        if ($tabProduto['Flag_Somar_IPI_Base_ICMS'] == 'S') {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI'];
        }

        if ($tabProduto['Aliquota_ICMS_Oculto'] > 0 and $tabProduto['Flag_Isento_ICMS'] == 'D') {
            return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] + $tmpB - $tmpA;
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
     * Método para cálculo do valor base do ICMS de substituição.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorBaseICMS_Substituicao($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
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
     * Método para cálculo do valor do ICMS.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_Substituicao($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Substituicao'] * ($tabProduto['ST_ICMS_Interno'] / 100);
    }

    /**
     * Método para cálculo do valor do ICMS retenção de susbtituição.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_SubstituicaoRetencao($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        // Origem
        if ($tabProduto['Percentual_Reducao_ICMS_ST'] > 0) {
            $tmpOrigem = (100 - $tabProduto['Percentual_Reducao_ICMS_ST']) / 100;
        } else {
            $tmpOrigem = 1;
        }

        $valorOrigem = ($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Base_ICMS_Substituicao'] * $tmpOrigem) * ($tabProduto['ST_ICMS_Interno'] / 100);

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
     * Método para cálculo do valor ICMS crédito.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorICMS_Credito($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
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

        if ($tabProduto['Flag_Somar_IPI_Base_ICMS'] == 'S') {
            $tmpC = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_IPI'];
        } else {
            $tmpC = 0;
        }

        if ($tabProduto['T134_Id'] <= 0) {
            return 0.00;
        } else {
            if ($tabProduto['Aliquota_ICMS_Credito'] > 0) {
                return (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] + $tmpC) - (($result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Custo_Final'] + $tmpB) * $tmpA)) * ($tabProduto['Aliquota_ICMS_Credito'] / 100);
            } else {
                return 0;
            }
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

        if ($tabProduto['Flag_Forma_Calculo_PIS'] == 'L') {
            $tmpB = $tmpA;
        } else {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
        }

        if ($tabProduto['Flag_Isento_PIS'] == 'S') {
            return 0;
        } else {
            return $tmpB;
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

        if ($tabProduto['Flag_Forma_Calculo_PIS'] == 'L') {
            $tmpB = $tmpA;
        } else {
            $tmpB = $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
        }

        if ($tabProduto['Flag_Isento_COFINS'] == 'S') {
            return 0;
        } else {
            return $tmpB;
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
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
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
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco'];
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
        if (!$result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto']) {
            $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] = 0;
        }

        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] * ($tabProduto['Percentual_Trib_Transparencia'] / 100);
    }

    /**
     * Método para cálculo o valor de tributos federal.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTribFederal($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (!$result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto']) {
            $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] = 0;
        }

        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] * ($tabProduto['Percentual_Trib_Federal'] / 100);
    }

    /**
     * Método para cálculo o valor de tributos estadual.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTribEstadual($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (!$result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto']) {
            $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] = 0;
        }

        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] * ($tabProduto['Percentual_Trib_Estadual'] / 100);
    }

    /**
     * Método para cálculo o valor de tributos municipal.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorTribMunicipal($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if (!$result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto']) {
            $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] = 0;
        }

        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] * ($tabProduto['Percentual_Trib_Municipal'] / 100);
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

    /**
     * Método para cálculo da quantidade tributável.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function quantidadeTributavel($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        if ($tabProduto['Flag_Unidade_Tributavel'] == 'S' and $tabProduto['Quantidade_Tributavel'] > 0) {
            return $tabProduto['Quantidade_Tributavel'] * $tabProduto['Quantidade'];
        } else {
            return $tabProduto['Quantidade'];
        }
    }

    /**
     * Método para cálculo do preço sem desconto unitário trabutável.
     *
     * @param array $tabCabecalho Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela cabeçalho.
     * @param array $tabProduto Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela produto.
     * @param array $tabConfiguracao Vetor (dicionário) indexado em chaves para os resultados da consulta SQL da tabela de configurações C031.
     * @param array $result Vetor (dicionário) indexado em chaves para os resultados das operações já realizadas previamente.
     */
    public function valorPrecoSemDescontoUnitarioTributavel($tabCabecalho, $tabProduto, $tabConfiguracao, $result)
    {
        return $result[$tabProduto['Str_Tab_Produto'] . '_Valor_Total_Preco_Sem_Desconto'] / $result[$tabProduto['Str_Tab_Produto'] . '_Quantidade_Tributavel'];
    }
}
