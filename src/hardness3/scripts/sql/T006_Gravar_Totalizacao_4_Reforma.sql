CREATE PROCEDURE `T006_Gravar_Totalizacao_4_Reforma`(xT006_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xD006_Deduz_ICMS_Desonerado_Valor_Total VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_PIS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_COFINS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_ICMS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_DIFAL_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_FCP_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';

    /* SELECTS
    ================================================== */
    -- Informações gerais do T006 (item do pedido) e D006 (CFOP do item)
    SELECT D006_Deduz_ICMS_Desonerado_Valor_Total,
           D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
           D006_Flag_Deduz_PIS_Base_IBS_CBS,
           D006_Flag_Deduz_COFINS_Base_IBS_CBS,
           D006_Flag_Deduz_ICMS_Base_IBS_CBS,
           D006_Flag_Deduz_DIFAL_Base_IBS_CBS,
           D006_Flag_Deduz_FCP_Base_IBS_CBS,
           D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS
      INTO xD006_Deduz_ICMS_Desonerado_Valor_Total,
           xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
           xD006_Flag_Deduz_PIS_Base_IBS_CBS,
           xD006_Flag_Deduz_COFINS_Base_IBS_CBS,
           xD006_Flag_Deduz_ICMS_Base_IBS_CBS,
           xD006_Flag_Deduz_DIFAL_Base_IBS_CBS,
           xD006_Flag_Deduz_FCP_Base_IBS_CBS,
           xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS
      FROM T006
 LEFT JOIN D006 ON D006_Id=T006_D006_Id
     WHERE T006_Id=xT006_Id
     LIMIT 1;

    /* Alíquotas efetivas da Reforma
    =================================================== */
    UPDATE T006A
    LEFT JOIN T006 ON T006A_T006_Id = T006_Id
    SET
    T006A_Aliquota_CBS_Efetiva              = Calcular_T006A_Aliquota_CBS_Efetiva(T006_Id, T006A_Id, T006A_Aliquota_CBS, T006A_Percentual_Reducao_CBS),
    T006A_Aliquota_IBS_UF_Efetiva           = Calcular_T006A_Aliquota_IBS_UF_Efetiva(T006_Id, T006A_Id, T006A_Aliquota_IBS_UF, T006A_Percentual_Reducao_IBS_UF),
    T006A_Aliquota_IBS_Municipal_Efetiva    = Calcular_T006A_Aliquota_IBS_Municipal_Efetiva(T006_Id, T006A_Id, T006A_Aliquota_IBS_Municipal, T006A_Percentual_Reducao_IBS_Municipal),
    T006A_Aliquota_CBS_Efetiva_Regular              = Calcular_T006A_Aliquota_CBS_Efetiva(T006_Id, T006A_Id, T006A_Aliquota_CBS_Regular, T006A_Percentual_Reducao_CBS_Regular),
    T006A_Aliquota_IBS_UF_Efetiva_Regular           = Calcular_T006A_Aliquota_IBS_UF_Efetiva(T006_Id, T006A_Id, T006A_Aliquota_IBS_UF_Regular, T006A_Percentual_Reducao_IBS_UF_Regular),
    T006A_Aliquota_IBS_Municipal_Efetiva_Regular    = Calcular_T006A_Aliquota_IBS_Municipal_Efetiva(T006_Id, T006A_Id, T006A_Aliquota_IBS_Municipal_Regular, T006A_Percentual_Reducao_IBS_Municipal_Regular)
    WHERE T006A_T006_Id=xT006_Id;

    /* Fórmulas dos campos da Reforma
    ================================================== */
    UPDATE T006A
    LEFT JOIN T006 ON T006A_T006_Id = T006_Id
    SET
    T006A_Valor_Base_CBS_IBS                = Calcular_T006A_Valor_Base_CBS_IBS(
        T006_Id,
        T006A_Id,
        T006_Valor_Total_Preco_Sem_Desconto,
        T006_Valor_Total_Frete,
        T006_Valor_Total_Despesas_Acessorias,
        T006_Valor_Total_Desconto,
        IF(IFNULL(xD006_Flag_Deduz_PIS_Base_IBS_CBS, 'S') = 'N', 0, T006_Valor_PIS),
        IF(IFNULL(xD006_Flag_Deduz_COFINS_Base_IBS_CBS, 'S') = 'N', 0, T006_Valor_COFINS),
        IF(IFNULL(xD006_Flag_Deduz_ICMS_Base_IBS_CBS, 'S') = 'N', 0, T006_Valor_ICMS),
        IF(IFNULL(xD006_Flag_Deduz_DIFAL_Base_IBS_CBS, 'S') = 'N', 0, T006_Valor_Partilha_ICMS_Destino),
        IF(IFNULL(xD006_Flag_Deduz_FCP_Base_IBS_CBS, 'S') = 'N', 0, T006A_Valor_FCP),
        IF(IFNULL(xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS, 'S') = 'N', 0, T006A_Valor_FCP_Partilha),
        T006A_Aliquota_CBS,
        T006A_Aliquota_IBS_Municipal,
        T006A_Aliquota_IBS_UF,
        T006A_Aliquota_CBS_Regular,
        T006A_Aliquota_IBS_Municipal_Regular,
        T006A_Aliquota_IBS_UF_Regular
    ),
    T006A_Valor_Diferimento_CBS             = Calcular_T006A_Valor_Diferimento_CBS(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_CBS_Efetiva, T006A_Percentual_Diferimento_CBS),
    T006A_Valor_Diferimento_IBS_UF          = Calcular_T006A_Valor_Diferimento_IBS_UF(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_IBS_UF_Efetiva, T006A_Percentual_Diferimento_IBS_UF),
    T006A_Valor_Diferimento_IBS_Municipal   = Calcular_T006A_Valor_Diferimento_IBS_Municipal(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_IBS_Municipal_Efetiva, T006A_Percentual_Diferimento_IBS_Municipal),
    T006A_Valor_CBS                         = Calcular_T006A_Valor_CBS(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_CBS_Efetiva, T006A_Valor_Diferimento_CBS),
    T006A_Valor_IBS_UF                      = Calcular_T006A_Valor_IBS_UF(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_IBS_UF_Efetiva, T006A_Valor_Diferimento_IBS_UF),
    T006A_Valor_IBS_Municipal               = Calcular_T006A_Valor_IBS_Municipal(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_IBS_Municipal_Efetiva, T006A_Valor_Diferimento_IBS_Municipal),
    T006A_Valor_CBS_Regular                 = Calcular_T006A_Valor_CBS(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_CBS_Efetiva_Regular, 0),
    T006A_Valor_IBS_UF_Regular              = Calcular_T006A_Valor_IBS_UF(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_IBS_UF_Efetiva_Regular, 0),
    T006A_Valor_IBS_Municipal_Regular       = Calcular_T006A_Valor_IBS_Municipal(T006_Id, T006A_Id, T006A_Valor_Base_CBS_IBS, T006A_Aliquota_IBS_Municipal_Efetiva_Regular, 0)
    WHERE T006A_T006_Id=xT006_Id;

    /* Totais da Reforma
    =================================================== */
    UPDATE T006A
    LEFT JOIN T006 ON T006A_T006_Id = T006_Id
    SET
    T006A_Valor_IBS                         = Calcular_T006A_Valor_IBS(T006_Id, T006A_Id, T006A_Valor_IBS_Municipal, T006A_Valor_IBS_UF),
    T006A_Valor_Total_Reforma               = Calcular_T006A_Valor_Total_Reforma(
        T006_Id,
        T006A_Id,
        T006_Valor_Total_Preco_Sem_Desconto,
        T006_Valor_Total_Desconto,
        xD006_Deduz_ICMS_Desonerado_Valor_Total,
        T006_Valor_ICMS_Desonerado,
        T006_Valor_ICMS_Substituicao_Retencao,
        T006A_Valor_FCP_ST,
        T006_Valor_Total_Frete,
        T006_Valor_Total_Despesas_Acessorias,
        T006_Valor_IPI,
        T006_Valor_PIS,
        T006_Valor_COFINS,
        xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
        T006A_Valor_IBS,
        T006A_Valor_CBS
    )
    WHERE T006A_T006_Id=xT006_Id;
END;
