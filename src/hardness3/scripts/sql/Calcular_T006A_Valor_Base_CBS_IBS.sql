CREATE FUNCTION `Calcular_T006A_Valor_Base_CBS_IBS`(
    xT006_Id bigint, xT006A_Id bigint,
    xT006_Valor_Total_Preco_Sem_Desconto decimal(20,2),
    xT006_Valor_Total_Frete decimal(20,10),
    xT006_Valor_Total_Despesas_Acessorias decimal(20,10),
    xT006_Valor_Total_Desconto decimal(20,10),
    xT006_Valor_PIS decimal(10,2), xT006_Valor_COFINS decimal(10,2),
    xT006_Valor_ICMS decimal(15,2), xT006_Valor_Partilha_ICMS_Destino decimal(15,2),
    xT006_Valor_FCP decimal(15,2), xT006_Valor_FCP_Partilha decimal(15,2),
    xT006A_Aliquota_CBS decimal(7,4), xT006A_Aliquota_IBS_Municipal decimal(7,4),
    xT006A_Aliquota_IBS_UF decimal(7,4), xT006A_Aliquota_CBS_Regular decimal(7,4),
    xT006A_Aliquota_IBS_Municipal_Regular decimal(7,4), xT006A_Aliquota_IBS_UF_Regular decimal(7,4)
) RETURNS decimal(15,2)
BEGIN
    IF IFNULL(xT006A_Aliquota_CBS,0) <= 0 AND IFNULL(xT006A_Aliquota_IBS_Municipal,0) <= 0 AND IFNULL(xT006A_Aliquota_IBS_UF,0) <= 0 AND IFNULL(xT006A_Aliquota_CBS_Regular,0) <= 0 AND IFNULL(xT006A_Aliquota_IBS_Municipal_Regular,0) <= 0 AND IFNULL(xT006A_Aliquota_IBS_UF_Regular,0) <= 0 THEN
        RETURN 0;
    END IF;
    RETURN IFNULL(xT006_Valor_Total_Preco_Sem_Desconto,0) + IFNULL(xT006_Valor_Total_Frete,0) + IFNULL(xT006_Valor_Total_Despesas_Acessorias,0) - IFNULL(xT006_Valor_Total_Desconto,0) - IFNULL(xT006_Valor_PIS,0) - IFNULL(xT006_Valor_COFINS,0) - IFNULL(xT006_Valor_ICMS,0) - IFNULL(xT006_Valor_Partilha_ICMS_Destino,0) - IFNULL(xT006_Valor_FCP,0) - IFNULL(xT006_Valor_FCP_Partilha,0);
END
