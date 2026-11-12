CREATE PROCEDURE `T084_Gravar_Totalizacao_Reforma`(xT084_Id int(11))
BEGIN
    -- Calcula todas as fórmulas da reforma tributária para o CTe
    UPDATE T084A
    LEFT JOIN T084 ON T084A_T084_Id = T084_Id
    SET 
    T084A_Valor_Base_CBS_IBS                    = Calcular_T084A_Valor_Base_CBS_IBS(T084_Id, T084A_Id, T084_Valor_Conhecimento, T084_Valor_ICMS),

    T084A_Aliquota_CBS_Efetiva                  = Calcular_T084A_Aliquota_CBS_Efetiva(T084_Id, T084A_Id, T084A_Aliquota_CBS, T084A_Percentual_Reducao_CBS),
    T084A_Aliquota_IBS_UF_Efetiva               = Calcular_T084A_Aliquota_IBS_UF_Efetiva(T084_Id, T084A_Id, T084A_Aliquota_IBS_UF, T084A_Percentual_Reducao_IBS_UF),
    T084A_Aliquota_IBS_Municipal_Efetiva        = Calcular_T084A_Aliquota_IBS_Municipal_Efetiva(T084_Id, T084A_Id, T084A_Aliquota_IBS_Municipal, T084A_Percentual_Reducao_IBS_Municipal),

    T084A_Valor_Diferimento_CBS                 = Calcular_T084A_Valor_Diferimento_CBS(T084_Id, T084A_Id, T084A_Valor_Base_CBS_IBS, T084A_Aliquota_CBS_Efetiva, T084A_Percentual_Diferimento_CBS),
    T084A_Valor_Diferimento_IBS_UF              = Calcular_T084A_Valor_Diferimento_IBS_UF(T084_Id, T084A_Id, T084A_Valor_Base_CBS_IBS, T084A_Aliquota_IBS_UF_Efetiva, T084A_Percentual_Diferimento_IBS_UF),
    T084A_Valor_Diferimento_IBS_Municipal       = Calcular_T084A_Valor_Diferimento_IBS_Municipal(T084_Id, T084A_Id, T084A_Valor_Base_CBS_IBS, T084A_Aliquota_IBS_Municipal_Efetiva, T084A_Percentual_Diferimento_IBS_Municipal),

    T084A_Valor_CBS                             = Calcular_T084A_Valor_CBS(T084_Id, T084A_Id, T084A_Valor_Base_CBS_IBS, T084A_Aliquota_CBS_Efetiva, T084A_Valor_Diferimento_CBS),
    T084A_Valor_IBS_UF                          = Calcular_T084A_Valor_IBS_UF(T084_Id, T084A_Id, T084A_Valor_Base_CBS_IBS, T084A_Aliquota_IBS_UF_Efetiva, T084A_Valor_Diferimento_IBS_UF),
    T084A_Valor_IBS_Municipal                   = Calcular_T084A_Valor_IBS_Municipal(T084_Id, T084A_Id, T084A_Valor_Base_CBS_IBS, T084A_Aliquota_IBS_Municipal_Efetiva, T084A_Valor_Diferimento_IBS_Municipal),

    T084A_Valor_IBS                             = Calcular_T084A_Valor_IBS(T084_Id, T084A_Id, T084A_Valor_IBS_Municipal, T084A_Valor_IBS_UF),

    T084A_Valor_Total_Reforma                   = Calcular_T084A_Valor_Total_Reforma(T084_Id, T084A_Id, T084_Valor_Conhecimento, T084A_Valor_CBS, T084A_Valor_IBS)
    WHERE T084A_T084_Id=xT084_Id;
END;
