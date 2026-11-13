CREATE FUNCTION `Calcular_T004A_Valor_Base_CBS_IBS`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T004_Gravar_Totalizacao_4 )
    xT004_Id bigint,
    xT004A_Id bigint,

    -- Parâmetros para cálculo
    xT004_Valor_Total_Preco_Sem_Desconto decimal(20,2),
    xT004_Valor_Frete_Total decimal(20,10),
    xT004_Valor_Total_Despesas_Acessorias decimal(20,10),
    xT004_Valor_Total_Desconto decimal(20,10),
    xT004_Valor_PIS decimal(10,2),
    xT004_Valor_COFINS decimal(10,2),
    xT004_Valor_ICMS decimal(15,2),
    xT004_Valor_Partilha_ICMS_Destino decimal(15,2),
    xT004_Valor_FCP decimal(15,2),
    xT004_Valor_FCP_Partilha decimal(15,2),

    -- Parâmetros para verificação se a base vai ser caculada
    xT004A_Aliquota_CBS decimal(7, 4),
    xT004A_Aliquota_IBS_Municipal decimal(7, 4),
    xT004A_Aliquota_IBS_UF decimal(7, 4),
    xT004A_Aliquota_CBS_Regular decimal(7, 4),
    xT004A_Aliquota_IBS_Municipal_Regular decimal(7, 4),
    xT004A_Aliquota_IBS_UF_Regular decimal(7, 4)

) RETURNS decimal(15, 2)
BEGIN
    -- Caso nenhuma alíquota foi setada, retornamos a base como zero para não influenciar em alguns valores
    IF (
        IFNULL(xT004A_Aliquota_CBS, 0) <= 0
        AND IFNULL(xT004A_Aliquota_IBS_Municipal, 0) <= 0
        AND IFNULL(xT004A_Aliquota_IBS_UF, 0) <= 0
        AND IFNULL(xT004A_Aliquota_CBS_Regular, 0) <= 0
        AND IFNULL(xT004A_Aliquota_IBS_Municipal_Regular, 0) <= 0
        AND IFNULL(xT004A_Aliquota_IBS_UF_Regular, 0) <= 0
    ) THEN
        RETURN 0;
    END IF;

    -- Cálculo da base
    RETURN
    IFNULL(xT004_Valor_Total_Preco_Sem_Desconto, 0)
    + IFNULL(xT004_Valor_Frete_Total, 0)
    + IFNULL(xT004_Valor_Total_Despesas_Acessorias, 0)
    - IFNULL(xT004_Valor_Total_Desconto,0)
    - IFNULL(xT004_Valor_PIS, 0)
    - IFNULL(xT004_Valor_COFINS, 0)
    - IFNULL(xT004_Valor_ICMS, 0)
    - IFNULL(xT004_Valor_Partilha_ICMS_Destino, 0)
    - IFNULL(xT004_Valor_FCP, 0)
    - IFNULL(xT004_Valor_FCP_Partilha, 0);
END
