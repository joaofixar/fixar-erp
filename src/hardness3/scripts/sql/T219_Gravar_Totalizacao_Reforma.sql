CREATE PROCEDURE `T219_Gravar_Totalizacao_Reforma`(xT219_Id int(11))
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
      FROM T219
 LEFT JOIN D006 ON D006_Id=T219_D006_Id
     WHERE T219_Id=xT219_Id;

    /* Alíquotas efetivas da Reforma
    =================================================== */
    UPDATE T219A
 LEFT JOIN T219 ON T219A_T219_Id=T219_Id
       SET T219A_Aliquota_CBS_Efetiva=Calcular_T219A_Aliquota_CBS_Efetiva(T219_Id,T219A_Id,T219A_Aliquota_CBS,T219A_Percentual_Reducao_CBS),
           T219A_Aliquota_IBS_UF_Efetiva=Calcular_T219A_Aliquota_IBS_UF_Efetiva(T219_Id,T219A_Id,T219A_Aliquota_IBS_UF,T219A_Percentual_Reducao_IBS_UF),
           T219A_Aliquota_IBS_Municipal_Efetiva=Calcular_T219A_Aliquota_IBS_Municipal_Efetiva(T219_Id,T219A_Id,T219A_Aliquota_IBS_Municipal,T219A_Percentual_Reducao_IBS_Municipal),
           T219A_Aliquota_CBS_Efetiva_Regular=Calcular_T219A_Aliquota_CBS_Efetiva(T219_Id,T219A_Id,T219A_Aliquota_CBS_Regular,T219A_Percentual_Reducao_CBS_Regular),
           T219A_Aliquota_IBS_UF_Efetiva_Regular=Calcular_T219A_Aliquota_IBS_UF_Efetiva(T219_Id,T219A_Id,T219A_Aliquota_IBS_UF_Regular,T219A_Percentual_Reducao_IBS_UF_Regular),
           T219A_Aliquota_IBS_Municipal_Efetiva_Regular=Calcular_T219A_Aliquota_IBS_Municipal_Efetiva(T219_Id,T219A_Id,T219A_Aliquota_IBS_Municipal_Regular,T219A_Percentual_Reducao_IBS_Municipal_Regular)
    WHERE T219A_T219_Id=xT219_Id;

    /* Fórmulas dos campos da Reforma
    ================================================== */
    UPDATE T219A
 LEFT JOIN T219 ON T219A_T219_Id=T219_Id
       SET T219A_Valor_Base_CBS_IBS=Calcular_T219A_Valor_Base_CBS_IBS(
                T219_Id, T219A_Id, T219_Valor_Total_Preco_Sem_Desconto,
                T219_Valor_Frete_Total, T219_Valor_Total_Despesas_Acessorias,
                T219_Valor_Total_Desconto, IF(IFNULL(xDeduzPIS,'S')='N',0,T219_Valor_PIS),
                IF(IFNULL(xDeduzCOFINS,'S')='N',0,T219_Valor_COFINS),
                IF(IFNULL(xDeduzICMS,'S')='N',0,T219_Valor_ICMS),
                T219_Valor_ICMS_Diferenca_Aliquotas, T219A_Valor_FCP, 0,
                T219A_Aliquota_CBS, T219A_Aliquota_IBS_Municipal,
                T219A_Aliquota_IBS_UF, T219A_Aliquota_CBS_Regular,
                T219A_Aliquota_IBS_Municipal_Regular, T219A_Aliquota_IBS_UF_Regular
           ),
        T219A_Valor_Diferimento_CBS=Calcular_T219A_Valor_Diferimento_CBS(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_CBS_Efetiva,T219A_Percentual_Diferimento_CBS),
        T219A_Valor_Diferimento_IBS_UF=Calcular_T219A_Valor_Diferimento_IBS_UF(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_IBS_UF_Efetiva,T219A_Percentual_Diferimento_IBS_UF),
           T219A_Valor_Diferimento_IBS_Municipal=Calcular_T219A_Valor_Diferimento_IBS_Municipal(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_IBS_Municipal_Efetiva,T219A_Percentual_Diferimento_IBS_Municipal)
    WHERE T219A_T219_Id=xT219_Id;

    /* Totais da Reforma
    =================================================== */
    UPDATE T219A
 LEFT JOIN T219 ON T219A_T219_Id=T219_Id
       SET
        T219A_Valor_CBS=Calcular_T219A_Valor_CBS(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_CBS_Efetiva,T219A_Valor_Diferimento_CBS),
        T219A_Valor_IBS_UF=Calcular_T219A_Valor_IBS_UF(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_IBS_UF_Efetiva,T219A_Valor_Diferimento_IBS_UF),
        T219A_Valor_IBS_Municipal=Calcular_T219A_Valor_IBS_Municipal(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_IBS_Municipal_Efetiva,T219A_Valor_Diferimento_IBS_Municipal),
        T219A_Valor_CBS_Regular=Calcular_T219A_Valor_CBS(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_CBS_Efetiva_Regular,0),
        T219A_Valor_IBS_UF_Regular=Calcular_T219A_Valor_IBS_UF(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_IBS_UF_Efetiva_Regular,0),
        T219A_Valor_IBS_Municipal_Regular=Calcular_T219A_Valor_IBS_Municipal(T219_Id,T219A_Id,T219A_Valor_Base_CBS_IBS,T219A_Aliquota_IBS_Municipal_Efetiva_Regular,0)
    WHERE T219A_T219_Id=xT219_Id;
    UPDATE T219A
 LEFT JOIN T219 ON T219A_T219_Id=T219_Id
       SET T219A_Valor_IBS=Calcular_T219A_Valor_IBS(T219_Id,T219A_Id,T219A_Valor_IBS_Municipal,T219A_Valor_IBS_UF),
           T219A_Valor_Total_Reforma=Calcular_T219A_Valor_Total_Reforma(T219_Id,T219A_Id,T219_Valor_Total_Preco_Sem_Desconto,T219_Valor_Total_Desconto,xDeduzDesonerado,T219_Valor_ICMS_Desonerado,T219_Valor_ST,T219A_Valor_FCP_ST,T219_Valor_Frete_Total,T219_Valor_Total_Despesas_Acessorias,T219_Valor_IPI,T219_Valor_PIS,T219_Valor_COFINS,xNaoSomarReforma,T219A_Valor_IBS,T219A_Valor_CBS)
     WHERE T219A_T219_Id=xT219_Id;

END;
