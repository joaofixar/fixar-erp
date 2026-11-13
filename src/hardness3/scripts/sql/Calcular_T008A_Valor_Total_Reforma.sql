CREATE FUNCTION `Calcular_T008A_Valor_Total_Reforma`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T008_Gravar_Totalizacao_4 )
    xT008_Id bigint, 
    xT008A_Id bigint,

    -- Parâmetros para cálculo
    xT008_Valor_Total_Preco_Sem_Desconto decimal(20,2),
    xT008_Valor_Total_Desconto decimal(20,10),
    xD006_Deduz_ICMS_Desonerado_Valor_Total varchar(1),
    xT008_Valor_ICMS_Desonerado decimal(15,2),
    xT008_Valor_ICMS_Substituicao_Retencao decimal(20,2),
    xT008_Valor_FCP_ST decimal(15,2),
    xT008_Valor_Total_Frete decimal(20,10),
    xT008_Valor_Total_Seguro decimal(19,10),
    xT008_Valor_Total_Despesas_Acessorias decimal(20,10),
    xT008_Valor_Total_Imposto_Importacao decimal(19,2),
    xT008_Valor_IPI decimal(14,2),
    xT008_Valor_IPI_Devolucao decimal(14,2),
    xT008_Valor_PIS decimal(10,2),
    xT008_Valor_COFINS decimal(10,2),
    xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma varchar(1),
    xT008A_Valor_IBS decimal(15, 2),
    xT008A_Valor_CBS decimal(15, 2)
) RETURNS decimal(15, 2)
BEGIN
        RETURN
        IFNULL(xT008_Valor_Total_Preco_Sem_Desconto, 0)
        - IFNULL(xT008_Valor_Total_Desconto, 0)
        - IFNULL(IF(xD006_Deduz_ICMS_Desonerado_Valor_Total, xT008_Valor_ICMS_Desonerado,0), 0)
        + IFNULL(xT008_Valor_ICMS_Substituicao_Retencao, 0)
        + IFNULL(xT008_Valor_FCP_ST, 0)
        + IFNULL(xT008_Valor_Total_Frete, 0)
        + IFNULL(xT008_Valor_Total_Seguro, 0)
        + IFNULL(xT008_Valor_Total_Despesas_Acessorias, 0)
        + IFNULL(xT008_Valor_Total_Imposto_Importacao, 0)
        + IFNULL(xT008_Valor_IPI, 0)
        + IFNULL(xT008_Valor_IPI_Devolucao, 0)
        + IF(
            IFNULL(xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma, 'S') = 'N',
            0,
            IFNULL(xT008A_Valor_IBS, 0) + IFNULL(xT008A_Valor_CBS, 0)
        );
END
