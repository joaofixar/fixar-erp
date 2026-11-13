CREATE PROCEDURE `T218_Gravar_Totalizacao_3_Reforma`(xT218_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xT218A_Valor_Total_Base_CBS_IBS decimal(15, 2);
    DECLARE xT218A_Valor_Total_CBS decimal(15, 2);
    DECLARE xT218A_Valor_Total_IBS decimal(15, 2);
    DECLARE xT218A_Valor_Total_IBS_Municipal decimal(15, 2);
    DECLARE xT218A_Valor_Total_IBS_UF decimal(15, 2);
    DECLARE xT218A_Valor_Total_Reforma decimal(15, 2);
    DECLARE xT218A_Valor_Total_Diferimento_CBS decimal(15, 2);
    DECLARE xT218A_Valor_Total_Diferimento_IBS_UF decimal(15, 2);
    DECLARE xT218A_Valor_Total_Diferimento_IBS_Municipal decimal(15, 2);

    /* SELECTS
    ================================================== */
    -- Soma os itens ativos da cotação (T219A) e grava na tabela estendida do cabeçalho (T218A)
    SELECT IFNULL(SUM(T219A_Valor_Base_CBS_IBS), 0),
           IFNULL(SUM(T219A_Valor_CBS), 0),
           IFNULL(SUM(T219A_Valor_IBS), 0),
           IFNULL(SUM(T219A_Valor_IBS_Municipal), 0),
           IFNULL(SUM(T219A_Valor_IBS_UF), 0),
           IFNULL(SUM(T219A_Valor_Total_Reforma), 0),
           IFNULL(SUM(T219A_Valor_Diferimento_CBS), 0),
           IFNULL(SUM(T219A_Valor_Diferimento_IBS_UF), 0),
           IFNULL(SUM(T219A_Valor_Diferimento_IBS_Municipal), 0)
      INTO xT218A_Valor_Total_Base_CBS_IBS,
           xT218A_Valor_Total_CBS,
           xT218A_Valor_Total_IBS,
           xT218A_Valor_Total_IBS_Municipal,
           xT218A_Valor_Total_IBS_UF,
           xT218A_Valor_Total_Reforma,
           xT218A_Valor_Total_Diferimento_CBS,
           xT218A_Valor_Total_Diferimento_IBS_UF,
           xT218A_Valor_Total_Diferimento_IBS_Municipal
      FROM T219
 LEFT JOIN T219A ON T219A_T219_Id = T219_Id
     WHERE T219_T218_Id = xT218_Id
       AND IFNULL(T219_Flag_Cancelado, '') != 'S';

    /* UPDATE dos campos da reforma
    ================================================== */
    UPDATE T218A
    SET T218A_Valor_Total_Base_CBS_IBS                  = xT218A_Valor_Total_Base_CBS_IBS,
        T218A_Valor_Total_CBS                           = xT218A_Valor_Total_CBS,
        T218A_Valor_Total_IBS                           = xT218A_Valor_Total_IBS,
        T218A_Valor_Total_IBS_Municipal                 = xT218A_Valor_Total_IBS_Municipal,
        T218A_Valor_Total_IBS_UF                        = xT218A_Valor_Total_IBS_UF,
        T218A_Valor_Total_Reforma                       = xT218A_Valor_Total_Reforma,
        T218A_Valor_Total_Diferimento_CBS               = xT218A_Valor_Total_Diferimento_CBS,
        T218A_Valor_Total_Diferimento_IBS_UF            = xT218A_Valor_Total_Diferimento_IBS_UF,
        T218A_Valor_Total_Diferimento_IBS_Municipal     = xT218A_Valor_Total_Diferimento_IBS_Municipal
    WHERE T218A_T218_Id=xT218_Id;
END;
