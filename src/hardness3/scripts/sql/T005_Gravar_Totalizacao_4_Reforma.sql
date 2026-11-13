CREATE PROCEDURE `T005_Gravar_Totalizacao_4_Reforma`(xT005_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xT005A_Valor_Total_Base_CBS_IBS decimal(15, 2);
    DECLARE xT005A_Valor_Total_CBS decimal(15, 2);
    DECLARE xT005A_Valor_Total_IBS decimal(15, 2);
    DECLARE xT005A_Valor_Total_IBS_Municipal decimal(15, 2);
    DECLARE xT005A_Valor_Total_IBS_UF decimal(15, 2);
    DECLARE xT005A_Valor_Total_Reforma decimal(15, 2);
    DECLARE xT005A_Valor_Total_Diferimento_CBS decimal(15, 2);
    DECLARE xT005A_Valor_Total_Diferimento_IBS_UF decimal(15, 2);
    DECLARE xT005A_Valor_Total_Diferimento_IBS_Municipal decimal(15, 2);

    /* SELECTS
    ================================================== */
    SELECT IFNULL(SUM(T006A_Valor_Base_CBS_IBS), 0),
           IFNULL(SUM(T006A_Valor_CBS), 0),
           IFNULL(SUM(T006A_Valor_IBS), 0),
           IFNULL(SUM(T006A_Valor_IBS_Municipal), 0),
           IFNULL(SUM(T006A_Valor_IBS_UF), 0),
           IFNULL(SUM(T006A_Valor_Total_Reforma), 0),
           IFNULL(SUM(T006A_Valor_Diferimento_CBS), 0),
           IFNULL(SUM(T006A_Valor_Diferimento_IBS_UF), 0),
           IFNULL(SUM(T006A_Valor_Diferimento_IBS_Municipal), 0)
      INTO xT005A_Valor_Total_Base_CBS_IBS,
           xT005A_Valor_Total_CBS,
           xT005A_Valor_Total_IBS,
           xT005A_Valor_Total_IBS_Municipal,
           xT005A_Valor_Total_IBS_UF,
           xT005A_Valor_Total_Reforma,
           xT005A_Valor_Total_Diferimento_CBS,
           xT005A_Valor_Total_Diferimento_IBS_UF,
           xT005A_Valor_Total_Diferimento_IBS_Municipal
      FROM T006
 LEFT JOIN T006A ON T006A_T006_Id = T006_Id
     WHERE T006_T005_Id = xT005_Id;

    /* UPDATE dos campos da Reforma
    ================================================== */
    UPDATE T005A
    SET T005A_Valor_Total_Base_CBS_IBS                 = xT005A_Valor_Total_Base_CBS_IBS,
        T005A_Valor_Total_CBS                          = xT005A_Valor_Total_CBS,
        T005A_Valor_Total_IBS                          = xT005A_Valor_Total_IBS,
        T005A_Valor_Total_IBS_Municipal                = xT005A_Valor_Total_IBS_Municipal,
        T005A_Valor_Total_IBS_UF                       = xT005A_Valor_Total_IBS_UF,
        T005A_Valor_Total_Reforma                      = xT005A_Valor_Total_Reforma,
        T005A_Valor_Total_Diferimento_CBS              = xT005A_Valor_Total_Diferimento_CBS,
        T005A_Valor_Total_Diferimento_IBS_UF           = xT005A_Valor_Total_Diferimento_IBS_UF,
        T005A_Valor_Total_Diferimento_IBS_Municipal    = xT005A_Valor_Total_Diferimento_IBS_Municipal
    WHERE T005A_T005_Id=xT005_Id;
END;
