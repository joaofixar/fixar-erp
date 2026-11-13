CREATE PROCEDURE `T225_Gravar_Totalizacao_Reforma`(xT225_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xDeduzDesonerado varchar(1) DEFAULT 'S';
    DECLARE xNaoSomarReforma varchar(1) DEFAULT 'S';
    DECLARE xDeduzPIS varchar(1) DEFAULT 'S';
    DECLARE xDeduzCOFINS varchar(1) DEFAULT 'S';
    DECLARE xDeduzICMS varchar(1) DEFAULT 'S';

    /* SELECTS
    ================================================== */
    SELECT D006_Deduz_ICMS_Desonerado_Valor_Total,
           D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
           D006_Flag_Deduz_PIS_Base_IBS_CBS,
           D006_Flag_Deduz_COFINS_Base_IBS_CBS,
           D006_Flag_Deduz_ICMS_Base_IBS_CBS
      INTO xDeduzDesonerado,
           xNaoSomarReforma,
           xDeduzPIS,
           xDeduzCOFINS,
           xDeduzICMS
      FROM T225
 LEFT JOIN D006 ON D006_Id=T225_D006_Id
     WHERE T225_Id=xT225_Id;

    /* Alíquotas efetivas da Reforma
    =================================================== */
    UPDATE T225A
 LEFT JOIN T225 ON T225A_T225_Id=T225_Id
       SET T225A_Aliquota_CBS_Efetiva=Calcular_T225A_Aliquota_CBS_Efetiva(T225_Id,T225A_Id,T225A_Aliquota_CBS,T225A_Percentual_Reducao_CBS),
           T225A_Aliquota_IBS_UF_Efetiva=Calcular_T225A_Aliquota_IBS_UF_Efetiva(T225_Id,T225A_Id,T225A_Aliquota_IBS_UF,T225A_Percentual_Reducao_IBS_UF),
           T225A_Aliquota_IBS_Municipal_Efetiva=Calcular_T225A_Aliquota_IBS_Municipal_Efetiva(T225_Id,T225A_Id,T225A_Aliquota_IBS_Municipal,T225A_Percentual_Reducao_IBS_Municipal),
           T225A_Aliquota_CBS_Efetiva_Regular=Calcular_T225A_Aliquota_CBS_Efetiva(T225_Id,T225A_Id,T225A_Aliquota_CBS_Regular,T225A_Percentual_Reducao_CBS_Regular),
           T225A_Aliquota_IBS_UF_Efetiva_Regular=Calcular_T225A_Aliquota_IBS_UF_Efetiva(T225_Id,T225A_Id,T225A_Aliquota_IBS_UF_Regular,T225A_Percentual_Reducao_IBS_UF_Regular),
           T225A_Aliquota_IBS_Municipal_Efetiva_Regular=Calcular_T225A_Aliquota_IBS_Municipal_Efetiva(T225_Id,T225A_Id,T225A_Aliquota_IBS_Municipal_Regular,T225A_Percentual_Reducao_IBS_Municipal_Regular)
    WHERE T225A_T225_Id=xT225_Id;

    /* Fórmulas dos campos da Reforma
    ================================================== */
    UPDATE T225A
 LEFT JOIN T225 ON T225A_T225_Id=T225_Id
       SET T225A_Valor_Base_CBS_IBS=Calcular_T225A_Valor_Base_CBS_IBS(
                T225_Id, T225A_Id, T225_Valor_Total_Preco_Sem_Desconto,
                T225_Valor_Total_Frete, T225_Valor_Total_Despesas_Acessorias,
                T225_Valor_Total_Desconto, IF(IFNULL(xDeduzPIS,'S')='N',0,T225_Valor_PIS),
                IF(IFNULL(xDeduzCOFINS,'S')='N',0,T225_Valor_COFINS),
                IF(IFNULL(xDeduzICMS,'S')='N',0,T225_Valor_ICMS),
                T225_Valor_ICMS_Diferenca_Aliquotas, T225A_Valor_FCP, 0,
                T225A_Aliquota_CBS, T225A_Aliquota_IBS_Municipal,
                T225A_Aliquota_IBS_UF, T225A_Aliquota_CBS_Regular,
                T225A_Aliquota_IBS_Municipal_Regular, T225A_Aliquota_IBS_UF_Regular
           ),
        T225A_Valor_Diferimento_CBS=Calcular_T225A_Valor_Diferimento_CBS(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_CBS_Efetiva,T225A_Percentual_Diferimento_CBS),
        T225A_Valor_Diferimento_IBS_UF=Calcular_T225A_Valor_Diferimento_IBS_UF(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_IBS_UF_Efetiva,T225A_Percentual_Diferimento_IBS_UF),
        T225A_Valor_Diferimento_IBS_Municipal=Calcular_T225A_Valor_Diferimento_IBS_Municipal(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_IBS_Municipal_Efetiva,T225A_Percentual_Diferimento_IBS_Municipal)
    WHERE T225A_T225_Id=xT225_Id;

    /* Totais da Reforma
    =================================================== */
    UPDATE T225A
 LEFT JOIN T225 ON T225A_T225_Id=T225_Id
       SET
        T225A_Valor_CBS=Calcular_T225A_Valor_CBS(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_CBS_Efetiva,T225A_Valor_Diferimento_CBS),
        T225A_Valor_IBS_UF=Calcular_T225A_Valor_IBS_UF(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_IBS_UF_Efetiva,T225A_Valor_Diferimento_IBS_UF),
        T225A_Valor_IBS_Municipal=Calcular_T225A_Valor_IBS_Municipal(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_IBS_Municipal_Efetiva,T225A_Valor_Diferimento_IBS_Municipal),
        T225A_Valor_CBS_Regular=Calcular_T225A_Valor_CBS(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_CBS_Efetiva_Regular,0),
        T225A_Valor_IBS_UF_Regular=Calcular_T225A_Valor_IBS_UF(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_IBS_UF_Efetiva_Regular,0),
        T225A_Valor_IBS_Municipal_Regular=Calcular_T225A_Valor_IBS_Municipal(T225_Id,T225A_Id,T225A_Valor_Base_CBS_IBS,T225A_Aliquota_IBS_Municipal_Efetiva_Regular,0)
    WHERE T225A_T225_Id=xT225_Id;
    UPDATE T225A
 LEFT JOIN T225 ON T225A_T225_Id=T225_Id
       SET T225A_Valor_IBS=Calcular_T225A_Valor_IBS(T225_Id,T225A_Id,T225A_Valor_IBS_Municipal,T225A_Valor_IBS_UF),
           T225A_Valor_Total_Reforma=Calcular_T225A_Valor_Total_Reforma(T225_Id,T225A_Id,T225_Valor_Total_Preco_Sem_Desconto,T225_Valor_Total_Desconto,xDeduzDesonerado,0,T225_Valor_ICMS_Substituicao_Retencao,T225A_Valor_FCP_ST,T225_Valor_Total_Frete,T225_Valor_Total_Despesas_Acessorias,T225_Valor_IPI,T225_Valor_PIS,T225_Valor_COFINS,xNaoSomarReforma,T225A_Valor_IBS,T225A_Valor_CBS)
     WHERE T225A_T225_Id=xT225_Id;

END;
