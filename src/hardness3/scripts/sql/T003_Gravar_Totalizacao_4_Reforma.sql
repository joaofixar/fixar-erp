CREATE PROCEDURE `T003_Gravar_Totalizacao_4_Reforma`(xT003_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xT003A_Valor_Total_Base_CBS_IBS decimal(15, 2);
    DECLARE xT003A_Valor_Total_CBS decimal(15, 2);
    DECLARE xT003A_Valor_Total_IBS decimal(15, 2);
    DECLARE xT003A_Valor_Total_IBS_Municipal decimal(15, 2);
    DECLARE xT003A_Valor_Total_IBS_UF decimal(15, 2);
    DECLARE xT003A_Valor_Total_Reforma decimal(15, 2);
    DECLARE xT003A_Valor_Total_Diferimento_CBS decimal(15, 2);
    DECLARE xT003A_Valor_Total_Diferimento_IBS_UF decimal(15, 2);
    DECLARE xT003A_Valor_Total_Diferimento_IBS_Municipal decimal(15, 2);


    /* SELECTS
    ================================================== */
    -- Soma os itens do orçamento (T004A) e grava na tabela estendida do cabeçalho (T003A)
    SELECT IFNULL(SUM(T004A_Valor_Base_CBS_IBS), 0),
           IFNULL(SUM(T004A_Valor_CBS), 0),
           IFNULL(SUM(T004A_Valor_IBS), 0),
           IFNULL(SUM(T004A_Valor_IBS_Municipal), 0),
           IFNULL(SUM(T004A_Valor_IBS_UF), 0),
           IFNULL(SUM(T004A_Valor_Total_Reforma), 0),
           IFNULL(SUM(T004A_Valor_Diferimento_CBS), 0),
           IFNULL(SUM(T004A_Valor_Diferimento_IBS_UF), 0),
           IFNULL(SUM(T004A_Valor_Diferimento_IBS_Municipal), 0)
      INTO xT003A_Valor_Total_Base_CBS_IBS,
           xT003A_Valor_Total_CBS,
           xT003A_Valor_Total_IBS,
           xT003A_Valor_Total_IBS_Municipal,
           xT003A_Valor_Total_IBS_UF,
           xT003A_Valor_Total_Reforma,
           xT003A_Valor_Total_Diferimento_CBS,
           xT003A_Valor_Total_Diferimento_IBS_UF,
           xT003A_Valor_Total_Diferimento_IBS_Municipal
      FROM T004
 LEFT JOIN T004A ON T004A_T004_Id = T004_Id
     WHERE T004_T003_Id = xT003_Id;

    /* UPDATE dos campos da reforma
    ================================================== */
    UPDATE T003A
    SET T003A_Valor_Total_Base_CBS_IBS                  = xT003A_Valor_Total_Base_CBS_IBS,
        T003A_Valor_Total_CBS                           = xT003A_Valor_Total_CBS,
        T003A_Valor_Total_IBS                           = xT003A_Valor_Total_IBS,
        T003A_Valor_Total_IBS_Municipal                 = xT003A_Valor_Total_IBS_Municipal,
        T003A_Valor_Total_IBS_UF                        = xT003A_Valor_Total_IBS_UF,
        T003A_Valor_Total_Reforma                       = xT003A_Valor_Total_Reforma,
        T003A_Valor_Total_Diferimento_CBS              = xT003A_Valor_Total_Diferimento_CBS,
        T003A_Valor_Total_Diferimento_IBS_UF           = xT003A_Valor_Total_Diferimento_IBS_UF,
        T003A_Valor_Total_Diferimento_IBS_Municipal    = xT003A_Valor_Total_Diferimento_IBS_Municipal
    WHERE T003A_T003_Id=xT003_Id;
END;
