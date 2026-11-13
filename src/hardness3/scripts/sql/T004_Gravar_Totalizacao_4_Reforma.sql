CREATE PROCEDURE `T004_Gravar_Totalizacao_4_Reforma`(xT004_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xD006_Deduz_ICMS_Desonerado_Valor_Total VARCHAR (1) DEFAULT 'S';
    DECLARE xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma VARCHAR (1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_PIS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_COFINS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_ICMS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_DIFAL_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_FCP_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';

    /* SELECTS
    ================================================== */
    -- Informações gerais do T004 (item do orçamento), T003 (cabeçalho) e D006 (CFOP do item)
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
      FROM T004
 LEFT JOIN D006 ON D006_Id=T004_D006_Id
     WHERE T004_Id=xT004_Id
     LIMIT 1;

    /* Alíquotas efetivas da Reforma
    =================================================== */
    UPDATE T004A
    LEFT JOIN T004 ON T004A_T004_Id = T004_Id
    SET
    -- Cálculo das aliquotas efetivas (alíquota + redução aplicada)
    T004A_Aliquota_CBS_Efetiva              = Calcular_T004A_Aliquota_CBS_Efetiva(T004_Id, T004A_Id, T004A_Aliquota_CBS, T004A_Percentual_Reducao_CBS),
    T004A_Aliquota_IBS_UF_Efetiva           = Calcular_T004A_Aliquota_IBS_UF_Efetiva(T004_Id, T004A_Id, T004A_Aliquota_IBS_UF, T004A_Percentual_Reducao_IBS_UF),
    T004A_Aliquota_IBS_Municipal_Efetiva    = Calcular_T004A_Aliquota_IBS_Municipal_Efetiva(T004_Id, T004A_Id, T004A_Aliquota_IBS_Municipal, T004A_Percentual_Reducao_IBS_Municipal),

    -- Cálculo das aliquotas efetivas regular (alíquota + redução aplicada) usamos a mesma função que a alíquota efetiva normal porque o cálculo é o mesmo
    T004A_Aliquota_CBS_Efetiva_Regular              = Calcular_T004A_Aliquota_CBS_Efetiva(T004_Id, T004A_Id, T004A_Aliquota_CBS_Regular, T004A_Percentual_Reducao_CBS_Regular),
    T004A_Aliquota_IBS_UF_Efetiva_Regular           = Calcular_T004A_Aliquota_IBS_UF_Efetiva(T004_Id, T004A_Id, T004A_Aliquota_IBS_UF_Regular, T004A_Percentual_Reducao_IBS_UF_Regular),
    T004A_Aliquota_IBS_Municipal_Efetiva_Regular    = Calcular_T004A_Aliquota_IBS_Municipal_Efetiva(T004_Id, T004A_Id, T004A_Aliquota_IBS_Municipal_Regular, T004A_Percentual_Reducao_IBS_Municipal_Regular)
    WHERE T004A_T004_Id=xT004_Id;

    /* Fórmulas dos campos da Reforma
    ================================================== */
    UPDATE T004A
        LEFT JOIN T004 ON T004A_T004_Id = T004_Id
        SET
        -- As opções da CFOP afetam somente as deduções desta base; apenas N desativa.
        -- Calculo da base de CBS e IBS
        T004A_Valor_Base_CBS_IBS                = Calcular_T004A_Valor_Base_CBS_IBS(
            T004_Id,
            T004A_Id,
            T004_Valor_Total_Preco_Sem_Desconto,
            T004_Valor_Frete_Total,
            T004_Valor_Total_Despesas_Acessorias,
            T004_Valor_Total_Desconto,
            IF(IFNULL(xD006_Flag_Deduz_PIS_Base_IBS_CBS, 'S') = 'N', 0, T004_Valor_PIS),
            IF(IFNULL(xD006_Flag_Deduz_COFINS_Base_IBS_CBS, 'S') = 'N', 0, T004_Valor_COFINS),
            IF(IFNULL(xD006_Flag_Deduz_ICMS_Base_IBS_CBS, 'S') = 'N', 0, T004_Valor_ICMS),
            IF(IFNULL(xD006_Flag_Deduz_DIFAL_Base_IBS_CBS, 'S') = 'N', 0, T004_Valor_Partilha_ICMS_Destino),
            IF(IFNULL(xD006_Flag_Deduz_FCP_Base_IBS_CBS, 'S') = 'N', 0, T004A_Valor_FCP),
            IF(IFNULL(xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS, 'S') = 'N', 0, T004A_Valor_FCP_Partilha),
            T004A_Aliquota_CBS,
            T004A_Aliquota_IBS_Municipal,
            T004A_Aliquota_IBS_UF,
            T004A_Aliquota_CBS_Regular,
            T004A_Aliquota_IBS_Municipal_Regular,
            T004A_Aliquota_IBS_UF_Regular
        ),

        -- Cálculo dos valores dos diferimentos de CBS e IBS (usa alíquota efetiva porque tem redução aplicada)
        T004A_Valor_Diferimento_CBS             = Calcular_T004A_Valor_Diferimento_CBS(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_CBS_Efetiva, T004A_Percentual_Diferimento_CBS),
        T004A_Valor_Diferimento_IBS_UF          = Calcular_T004A_Valor_Diferimento_IBS_UF(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_IBS_UF_Efetiva, T004A_Percentual_Diferimento_IBS_UF),
        T004A_Valor_Diferimento_IBS_Municipal   = Calcular_T004A_Valor_Diferimento_IBS_Municipal(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_IBS_Municipal_Efetiva, T004A_Percentual_Diferimento_IBS_Municipal),

        -- Aqui mandamos a aliquota efetiva porque ela é a que tem a redução aplicada (ou seja, se redução for zero, a aliquota normal será usada)
        T004A_Valor_CBS                         = Calcular_T004A_Valor_CBS(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_CBS_Efetiva, T004A_Valor_Diferimento_CBS),
        T004A_Valor_IBS_UF                      = Calcular_T004A_Valor_IBS_UF(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_IBS_UF_Efetiva, T004A_Valor_Diferimento_IBS_UF),
        T004A_Valor_IBS_Municipal               = Calcular_T004A_Valor_IBS_Municipal(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_IBS_Municipal_Efetiva, T004A_Valor_Diferimento_IBS_Municipal),

        -- Aqui cálculamos os valores da tributação regular, usamos o cacular dos impostos normais porque o cálculo é o mesmo, exceto que mandamos 0 para o diferimento porque tributação regular não possui diferimento
        T004A_Valor_CBS_Regular                 = Calcular_T004A_Valor_CBS(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_CBS_Efetiva_Regular, 0),
        T004A_Valor_IBS_UF_Regular              = Calcular_T004A_Valor_IBS_UF(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_IBS_UF_Efetiva_Regular, 0),
        T004A_Valor_IBS_Municipal_Regular       = Calcular_T004A_Valor_IBS_Municipal(T004_Id, T004A_Id, T004A_Valor_Base_CBS_IBS, T004A_Aliquota_IBS_Municipal_Efetiva_Regular, 0)

        WHERE T004A_T004_Id=xT004_Id;

    /* Totais da Reforma
    =================================================== */
    UPDATE T004A
    LEFT JOIN T004 ON T004A_T004_Id = T004_Id
    SET
    -- Cálculo IBS UF + IBS Mun
    T004A_Valor_IBS                         = Calcular_T004A_Valor_IBS(T004_Id, T004A_Id, T004A_Valor_IBS_Municipal, T004A_Valor_IBS_UF),

    -- Cálcula o valor total da reforma
    T004A_Valor_Total_Reforma               = Calcular_T004A_Valor_Total_Reforma(
        T004_Id,
        T004A_Id,
        T004_Valor_Total_Preco_Sem_Desconto,
        T004_Valor_Total_Desconto,
        xD006_Deduz_ICMS_Desonerado_Valor_Total,
        T004_Valor_ICMS_Desonerado,
        T004_Valor_ST,
        T004A_Valor_FCP_ST,
        T004_Valor_Frete_Total,
        T004_Valor_Total_Despesas_Acessorias,
        T004_Valor_IPI,
        T004_Valor_PIS,
        T004_Valor_COFINS,
        xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
        T004A_Valor_IBS,
        T004A_Valor_CBS
    )
    WHERE T004A_T004_Id=xT004_Id;
END;
