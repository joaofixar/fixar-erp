CREATE PROCEDURE `T224_Gravar_Totalizacao_3_Reforma`(xT224_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xT224A_Valor_Total_Base_CBS_IBS decimal(15, 2);
    DECLARE xT224A_Valor_Total_CBS decimal(15, 2);
    DECLARE xT224A_Valor_Total_IBS decimal(15, 2);
    DECLARE xT224A_Valor_Total_IBS_Municipal decimal(15, 2);
    DECLARE xT224A_Valor_Total_IBS_UF decimal(15, 2);
    DECLARE xT224A_Valor_Total_Reforma decimal(15, 2);
    DECLARE xT224A_Valor_Total_Diferimento_CBS decimal(15, 2);
    DECLARE xT224A_Valor_Total_Diferimento_IBS_UF decimal(15, 2);
    DECLARE xT224A_Valor_Total_Diferimento_IBS_Municipal decimal(15, 2);

    /* SELECTS
    ================================================== */
    -- Soma os itens ativos da OC (T225A) e grava na tabela estendida do cabeçalho (T224A)
    SELECT IFNULL(SUM(T225A_Valor_Base_CBS_IBS), 0),
           IFNULL(SUM(T225A_Valor_CBS), 0),
           IFNULL(SUM(T225A_Valor_IBS), 0),
           IFNULL(SUM(T225A_Valor_IBS_Municipal), 0),
           IFNULL(SUM(T225A_Valor_IBS_UF), 0),
           IFNULL(SUM(T225A_Valor_Total_Reforma), 0),
           IFNULL(SUM(T225A_Valor_Diferimento_CBS), 0),
           IFNULL(SUM(T225A_Valor_Diferimento_IBS_UF), 0),
           IFNULL(SUM(T225A_Valor_Diferimento_IBS_Municipal), 0)
      INTO xT224A_Valor_Total_Base_CBS_IBS,
           xT224A_Valor_Total_CBS,
           xT224A_Valor_Total_IBS,
           xT224A_Valor_Total_IBS_Municipal,
           xT224A_Valor_Total_IBS_UF,
           xT224A_Valor_Total_Reforma,
           xT224A_Valor_Total_Diferimento_CBS,
           xT224A_Valor_Total_Diferimento_IBS_UF,
           xT224A_Valor_Total_Diferimento_IBS_Municipal
      FROM T225
 LEFT JOIN T225A ON T225A_T225_Id = T225_Id
     WHERE T225_T224_Id = xT224_Id
       AND IFNULL(T225_Flag_Cancelada, '') != 'S';

    /* UPDATE dos campos da reforma
    ================================================== */
    UPDATE T224A
    SET T224A_Valor_Total_Base_CBS_IBS               = xT224A_Valor_Total_Base_CBS_IBS,
        T224A_Valor_Total_CBS                        = xT224A_Valor_Total_CBS,
        T224A_Valor_Total_IBS                        = xT224A_Valor_Total_IBS,
        T224A_Valor_Total_IBS_Municipal              = xT224A_Valor_Total_IBS_Municipal,
        T224A_Valor_Total_IBS_UF                     = xT224A_Valor_Total_IBS_UF,
        T224A_Valor_Total_Reforma                    = xT224A_Valor_Total_Reforma,
        T224A_Valor_Total_Diferimento_CBS            = xT224A_Valor_Total_Diferimento_CBS,
        T224A_Valor_Total_Diferimento_IBS_UF         = xT224A_Valor_Total_Diferimento_IBS_UF,
        T224A_Valor_Total_Diferimento_IBS_Municipal  = xT224A_Valor_Total_Diferimento_IBS_Municipal
    WHERE T224A_T224_Id=xT224_Id;
END;
